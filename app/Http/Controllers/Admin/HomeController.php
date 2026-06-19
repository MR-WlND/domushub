<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Apartment;
use App\Models\Resident;
use App\Models\Ticket;

class HomeController extends Controller
{
    /**
     * Màn hình tổng quan Admin Dashboard.
     * Staff và Technician sẽ được redirect tới trang chức năng chính.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $user = auth()->user();

        // Staff → redirect tới trang Điện nước
        if ($user->role === 'staff') {
            return redirect()->route('admin.utility-readings.index');
        }

        // Technician → redirect tới trang Điện nước
        if ($user->role === 'technician') {
            return redirect()->route('admin.utility-readings.index');
        }

        $totalBlocks = Block::count();
        $totalFloors = Floor::count();
        $totalApartments = Apartment::count();
        $occupiedApartments = Apartment::where('status', 'occupied')->count();
        $vacantApartments = Apartment::where('status', 'vacant')->count();
        $maintenanceApartments = Apartment::where('status', 'maintenance')->count();
        $activeBlocks = $totalBlocks;

        return view('admin.dashboard.index', compact(
            'totalBlocks', 'totalFloors', 'totalApartments', 'occupiedApartments',
            'vacantApartments', 'maintenanceApartments', 'activeBlocks'
        ));
    }

    /**
     * Màn hình thống kê báo cáo (Chuyển hướng đến trang mặc định).
     */
    public function statistics(Request $request)
    {
        return redirect()->route('admin.statistics.finance', $request->query());
    }

    /**
     * Màn hình thống kê Tài chính & Tiêu thụ.
     */
    public function statisticsFinance(Request $request): View
    {
        // Lấy danh sách các năm có dữ liệu để hiện trong bộ lọc
        $availableYears = DB::table('bills')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DISTINCT billing_year')
            ->orderBy('billing_year', 'desc')
            ->pluck('billing_year')
            ->toArray();

        // Năm được chọn (mặc định là năm hiện tại hoặc năm đầu tiên có dữ liệu)
        $selectedYear = $request->get('year', date('Y'));
        if (!in_array($selectedYear, $availableYears) && count($availableYears) > 0) {
            $selectedYear = $availableYears[0];
        }

        // 1. KPI TỔNG QUAN (Toàn thời gian)
        $totalBilled = DB::table('bills')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $totalCollected = DB::table('payments')
            ->whereNull('deleted_at')
            ->where('status', 'success')
            ->sum('amount');

        $totalUnpaid = DB::table('bills')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'cancelled')
            ->selectRaw('SUM(total_amount - paid_amount) as unpaid')
            ->value('unpaid') ?? 0;

        $collectionRate = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0;

        // 2. DOANH THU THEO THÁNG (lọc theo năm đã chọn)
        $monthlyRevenue = DB::table('bills')
            ->select(
                'billing_year',
                'billing_month',
                DB::raw('SUM(total_amount) as total_billed'),
                DB::raw("SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) as total_collected")
            )
            ->whereNull('deleted_at')
            ->where('status', '!=', 'cancelled')
            ->where('billing_year', $selectedYear)
            ->groupBy('billing_year', 'billing_month')
            ->orderBy('billing_month')
            ->get();

        // 3. KPI RIÊNG CHO NĂM ĐƯỢC CHỌN
        $yearBilled = DB::table('bills')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'cancelled')
            ->where('billing_year', $selectedYear)
            ->sum('total_amount');

        $yearCollected = DB::table('payments')
            ->join('bills', 'payments.bill_id', '=', 'bills.id')
            ->whereNull('payments.deleted_at')
            ->where('payments.status', 'success')
            ->where('bills.billing_year', $selectedYear)
            ->sum('payments.amount');

        $yearUnpaid = DB::table('bills')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'cancelled')
            ->where('billing_year', $selectedYear)
            ->selectRaw('SUM(total_amount - paid_amount) as unpaid')
            ->value('unpaid') ?? 0;

        $yearCollectionRate = $yearBilled > 0 ? ($yearCollected / $yearBilled) * 100 : 0;

        // 4. Cấu trúc doanh thu theo tháng và loại dịch vụ (biểu đồ stacked bar)
        // Chỉ tính các hóa đơn đã thanh toán (paid) theo năm đã chọn
        $monthlyServiceRevenue = DB::table('bill_details')
            ->join('bills', 'bill_details.bill_id', '=', 'bills.id')
            ->join('service_prices', 'bill_details.service_price_id', '=', 'service_prices.id')
            ->select(
                'bills.billing_month',
                'service_prices.type',
                DB::raw('SUM(bill_details.amount) as total_amount')
            )
            ->whereNull('bills.deleted_at')
            ->where('bills.status', 'paid')
            ->where('bills.billing_year', $selectedYear)
            ->groupBy('bills.billing_month', 'service_prices.type')
            ->get();

        $categories = [
            'electricity' => 'Điện',
            'water' => 'Nước',
            'management_fee' => 'Phí quản lý',
            'other' => 'Khác',
        ];

        $stackedData = [];
        foreach ($categories as $catKey => $catName) {
            $stackedData[$catKey] = array_fill(1, 12, 0);
        }

        foreach ($monthlyServiceRevenue as $row) {
            $month = $row->billing_month;
            $type = $row->type;
            $amount = (float) $row->total_amount;

            if (array_key_exists($type, $stackedData)) {
                $stackedData[$type][$month] += $amount;
            } else {
                $stackedData['other'][$month] += $amount;
            }
        }

        // Format lại dữ liệu dạng mảng JSON liên tục từ T1-T12
        $monthlyStackedData = [];
        foreach ($stackedData as $catKey => $monthValues) {
            $monthlyStackedData[$catKey] = array_values($monthValues);
        }

        // 5. Tỷ lệ hoàn thành đóng phí của tháng hiện tại/gần nhất có dữ liệu của năm được chọn
        $latestMonthRow = DB::table('bills')
            ->whereNull('deleted_at')
            ->where('status', '!=', 'cancelled')
            ->where('billing_year', $selectedYear)
            ->select('billing_month')
            ->orderBy('billing_month', 'desc')
            ->first();

        $latestMonth = $latestMonthRow ? $latestMonthRow->billing_month : null;
        $paidAmount = 0;
        $unpaidAmount = 0;

        if ($latestMonth) {
            $monthStats = DB::table('bills')
                ->whereNull('deleted_at')
                ->where('billing_year', $selectedYear)
                ->where('billing_month', $latestMonth)
                ->select('status', DB::raw('SUM(total_amount) as total'))
                ->groupBy('status')
                ->get();

            foreach ($monthStats as $row) {
                if ($row->status === 'paid') {
                    $paidAmount += (float) $row->total;
                } elseif (in_array($row->status, ['unpaid', 'overdue'])) {
                    $unpaidAmount += (float) $row->total;
                }
            }
        }

        $monthlyLabels = ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'];

        return view('admin.statistics.finance', compact(
            'availableYears', 'selectedYear',
            'totalBilled', 'totalCollected', 'totalUnpaid', 'collectionRate',
            'yearBilled', 'yearCollected', 'yearUnpaid', 'yearCollectionRate',
            'monthlyRevenue', 'monthlyLabels',
            'monthlyStackedData', 'latestMonth', 'paidAmount', 'unpaidAmount'
        ));
    }

    /**
     * Màn hình thống kê Vận hành & SLA.
     */
    public function statisticsOperations(Request $request): View
    {
        // Lấy danh sách các năm có phản ánh để hiện trong bộ lọc
        $availableYears = DB::table('tickets')
            ->whereNull('deleted_at')
            ->selectRaw('DISTINCT YEAR(created_at) as ticket_year')
            ->orderBy('ticket_year', 'desc')
            ->pluck('ticket_year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        // Năm được chọn
        $selectedYear = $request->get('year', date('Y'));
        if (!in_array($selectedYear, $availableYears) && count($availableYears) > 0) {
            $selectedYear = $availableYears[0];
        }

        // 1. Số lượng phản ánh theo status lọc theo năm
        $ticketStats = DB::table('tickets')
            ->whereNull('deleted_at')
            ->whereYear('created_at', $selectedYear)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $pendingCount    = $ticketStats['pending'] ?? 0;
        $assignedCount   = $ticketStats['assigned'] ?? 0;
        $inProgressCount = $ticketStats['in_progress'] ?? 0;
        $completedCount  = $ticketStats['completed'] ?? 0;
        $cancelledCount  = $ticketStats['cancelled'] ?? 0;
        $totalTickets    = array_sum($ticketStats);

        // 2. Thống kê phân bố đánh giá (1-5 sao)
        $csatStats = DB::table('tickets')
            ->whereNull('deleted_at')
            ->where('status', 'completed')
            ->whereYear('created_at', $selectedYear)
            ->whereNotNull('rating')
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $csatData = [
            1 => $csatStats[1] ?? 0,
            2 => $csatStats[2] ?? 0,
            3 => $csatStats[3] ?? 0,
            4 => $csatStats[4] ?? 0,
            5 => $csatStats[5] ?? 0,
        ];

        $totalRated = array_sum($csatData);
        $averageRating = 0;
        if ($totalRated > 0) {
            $sumRating = 0;
            foreach ($csatData as $rating => $count) {
                $sumRating += $rating * $count;
            }
            $averageRating = round($sumRating / $totalRated, 1);
        }

        // 3. Danh sách 5 đánh giá phản ánh gần nhất để hiển thị trực quan
        $recentFeedbacks = Ticket::with(['sender', 'apartment'])
            ->where('status', 'completed')
            ->whereYear('created_at', $selectedYear)
            ->whereNotNull('rating')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.statistics.operations', compact(
            'availableYears', 'selectedYear',
            'totalTickets', 'pendingCount', 'assignedCount', 'inProgressCount', 'completedCount', 'cancelledCount',
            'csatData', 'totalRated', 'averageRating', 'recentFeedbacks'
        ));
    }

    /**
     * Màn hình thống kê Cư dân & Hạ tầng.
     */
    public function statisticsResidents(Request $request): View
    {
        // ── 1. TỔNG QUAN HẠ TẦNG ──────────────────────────────────────
        $totalBlocks     = Block::count();
        $totalFloors     = Floor::count();
        $totalApartments = Apartment::count();
        $occupied        = Apartment::where('status', 'occupied')->count();
        $vacant          = Apartment::where('status', 'vacant')->count();
        $maintenance     = Apartment::where('status', 'maintenance')->count();
        $occupancyRate   = $totalApartments > 0 ? round(($occupied / $totalApartments) * 100, 1) : 0;

        // ── 2. TỔNG QUAN CƯ DÂN ───────────────────────────────────────
        $totalResidents = Resident::count();
        $ownerCount     = Resident::where('relationship', 'owner')->count();
        $tenantCount    = Resident::where('relationship', 'tenant')->count();
        $familyCount    = Resident::where('relationship', 'family_member')->count();

        // ── 3. TỶ LỆ LOẠI CƯ DÂN (cho pie chart) ─────────────────────
        $residentTypeData = [
            'owner'         => $ownerCount,
            'tenant'        => $tenantCount,
            'family_member' => $familyCount,
        ];

        // ── 4. PHÂN BỐ CĂN HỘ THEO TÒNG NHÀ ─────────────────────────
        $blockStats = Block::withCount([
            'apartments',
            'apartments as occupied_count' => fn($q) => $q->where('status', 'occupied'),
            'apartments as vacant_count'   => fn($q) => $q->where('status', 'vacant'),
            'apartments as maintenance_count' => fn($q) => $q->where('status', 'maintenance'),
        ])->orderBy('name')->get();

        // ── 5. XU HƯỚNG CƯ DÂN ĐĂNG KÝ THEO THÁNG (12 tháng gần nhất) ─
        $residentTrend = DB::table('residents')
            ->whereNull('deleted_at')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Tạo mảng đủ 12 tháng (kể cả tháng = 0)
        $trendLabels = [];
        $trendValues = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $label = now()->subMonths($i)->format('T' . now()->subMonths($i)->month . '/' . now()->subMonths($i)->year);
            $trendLabels[] = 'T' . now()->subMonths($i)->month . '/' . now()->subMonths($i)->format('y');
            $trendValues[] = $residentTrend[$key] ?? 0;
        }

        // ── 6. TOP CĂN HỘ ĐÔNG NGƯỜI NHẤT ────────────────────────────
        $topApartments = DB::table('residents')
            ->join('apartments', 'residents.apartment_id', '=', 'apartments.id')
            ->join('floors', 'apartments.floor_id', '=', 'floors.id')
            ->join('blocks', 'floors.block_id', '=', 'blocks.id')
            ->whereNull('residents.deleted_at')
            ->select(
                'apartments.apartment_number',
                'blocks.name as block_name',
                DB::raw('COUNT(residents.id) as resident_count')
            )
            ->groupBy('apartments.id', 'apartments.apartment_number', 'blocks.name')
            ->orderByDesc('resident_count')
            ->limit(10)
            ->get();

        // ── 7. THỐNG KÊ THEO TẦNG (heatmap-style) ─────────────────────
        $floorStats = DB::table('floors')
            ->join('blocks', 'floors.block_id', '=', 'blocks.id')
            ->leftJoin('apartments', 'apartments.floor_id', '=', 'floors.id')
            ->select(
                'floors.id',
                'floors.floor_number',
                'blocks.name as block_name',
                DB::raw('COUNT(apartments.id) as total_apts'),
                DB::raw("SUM(CASE WHEN apartments.status = 'occupied' THEN 1 ELSE 0 END) as occupied_apts")
            )
            ->groupBy('floors.id', 'floors.floor_number', 'blocks.name')
            ->orderBy('blocks.name')
            ->orderBy('floors.floor_number')
            ->get();

        return view('admin.statistics.residents', compact(
            'totalBlocks', 'totalFloors', 'totalApartments',
            'occupied', 'vacant', 'maintenance', 'occupancyRate',
            'totalResidents', 'ownerCount', 'tenantCount', 'familyCount',
            'residentTypeData',
            'blockStats',
            'trendLabels', 'trendValues',
            'topApartments',
            'floorStats'
        ));
    }
}
