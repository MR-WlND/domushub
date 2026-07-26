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
use App\Models\Vehicle;
use App\Models\FacilityBooking;

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

        // Technician -> redirect to assigned technical tasks.
        if ($user->role === 'technician') {
            return redirect()->route('admin.tickets.my-tasks');
        }

        $totalBlocks = Block::count();
        $totalFloors = Floor::count();
        $totalApartments = Apartment::count();
        $occupiedApartments = Apartment::where('status', 'occupied')->count();
        $vacantApartments = Apartment::where('status', 'vacant')->count();
        $maintenanceApartments = Apartment::where('status', 'maintenance')->count();
        $activeBlocks = $totalBlocks;

        // Bổ sung các chỉ số vận hành để hiển thị trên dashboard xịn
        $totalResidents = Resident::whereNull('deleted_at')->count();
        $pendingTicketsCount = Ticket::where('status', 'pending')->count();
        $pendingVehiclesCount = Vehicle::where('status', 'pending')->count();
        $pendingBookingsCount = FacilityBooking::where('status', 'pending')->count();

        // 5 phản ánh gần nhất
        $recentTickets = Ticket::with('apartment')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 5 lượt đặt tiện ích gần nhất
        $recentBookings = FacilityBooking::with('facility', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalBlocks', 'totalFloors', 'totalApartments', 'occupiedApartments',
            'vacantApartments', 'maintenanceApartments', 'activeBlocks',
            'totalResidents', 'pendingTicketsCount', 'pendingVehiclesCount',
            'pendingBookingsCount', 'recentTickets', 'recentBookings'
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
        // Lấy danh sách tòa nhà/block
        $blocks = Block::orderBy('name')->get();
        $selectedBlock = $request->get('block_id');

        // Lấy danh sách các năm có dữ liệu để hiện trong bộ lọc
        $availableYearsQuery = DB::table('bills')
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled');
        if ($selectedBlock) {
            $availableYearsQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $availableYears = $availableYearsQuery->selectRaw('DISTINCT billing_year')
            ->orderBy('billing_year', 'desc')
            ->pluck('billing_year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        // Năm được chọn (mặc định là năm hiện tại hoặc năm đầu tiên có dữ liệu)
        $selectedYear = $request->get('year', date('Y'));
        if (!in_array($selectedYear, $availableYears) && count($availableYears) > 0) {
            $selectedYear = $availableYears[0];
        }

        // 1. KPI TỔNG QUAN (Toàn thời gian)
        $totalBilledQuery = DB::table('bills')
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled');
        if ($selectedBlock) {
            $totalBilledQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $totalBilled = $totalBilledQuery->sum('total_amount');

        $totalCollectedQuery = DB::table('payments')
            ->whereNull('payments.deleted_at')
            ->where('payments.status', 'success');
        if ($selectedBlock) {
            $totalCollectedQuery->join('bills', 'payments.bill_id', '=', 'bills.id')
                ->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $totalCollected = $totalCollectedQuery->sum('amount');

        $totalUnpaidQuery = DB::table('bills')
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled');
        if ($selectedBlock) {
            $totalUnpaidQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $totalUnpaid = $totalUnpaidQuery->selectRaw('SUM(total_amount - paid_amount) as unpaid')->value('unpaid') ?? 0;

        $collectionRate = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0;

        // 2. DOANH THU THEO THÁNG (lọc theo năm đã chọn)
        $monthlyRevenueQuery = DB::table('bills')
            ->select(
                'billing_year',
                'billing_month',
                DB::raw('SUM(total_amount) as total_billed'),
                DB::raw("SUM(paid_amount) as total_collected")
            )
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled')
            ->where('bills.billing_year', $selectedYear);
        if ($selectedBlock) {
            $monthlyRevenueQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $monthlyRevenue = $monthlyRevenueQuery->groupBy('billing_year', 'billing_month')
            ->orderBy('billing_month')
            ->get();

        // 3. KPI RIÊNG CHO NĂM ĐƯỢC CHỌN
        $yearBilledQuery = DB::table('bills')
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled')
            ->where('bills.billing_year', $selectedYear);
        if ($selectedBlock) {
            $yearBilledQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $yearBilled = $yearBilledQuery->sum('total_amount');

        $yearCollectedQuery = DB::table('payments')
            ->join('bills', 'payments.bill_id', '=', 'bills.id')
            ->whereNull('payments.deleted_at')
            ->whereNull('bills.deleted_at')
            ->where('payments.status', 'success')
            ->where('bills.billing_year', $selectedYear);
        if ($selectedBlock) {
            $yearCollectedQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $yearCollected = $yearCollectedQuery->sum('payments.amount');

        $yearUnpaidQuery = DB::table('bills')
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled')
            ->where('bills.billing_year', $selectedYear);
        if ($selectedBlock) {
            $yearUnpaidQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $yearUnpaid = $yearUnpaidQuery->selectRaw('SUM(total_amount - paid_amount) as unpaid')->value('unpaid') ?? 0;

        $yearCollectionRate = $yearBilled > 0 ? ($yearCollected / $yearBilled) * 100 : 0;

        // 4. Cấu trúc doanh thu theo tháng và loại dịch vụ (biểu đồ stacked bar)
        $monthlyServiceRevenueQuery = DB::table('bill_details')
            ->join('bills', 'bill_details.bill_id', '=', 'bills.id')
            ->join('service_prices', 'bill_details.service_price_id', '=', 'service_prices.id')
            ->select(
                'bills.billing_month',
                'service_prices.type',
                DB::raw('SUM(bill_details.amount) as total_amount')
            )
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled')
            ->where('bills.billing_year', $selectedYear);
        if ($selectedBlock) {
            $monthlyServiceRevenueQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $monthlyServiceRevenue = $monthlyServiceRevenueQuery->groupBy('bills.billing_month', 'service_prices.type')->get();

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

        // 5. Tỷ lệ hoàn thành đóng phí của tháng được chọn
        $latestMonthQuery = DB::table('bills')
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled')
            ->where('bills.billing_year', $selectedYear);
        if ($selectedBlock) {
            $latestMonthQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $latestMonthRow = $latestMonthQuery->select('billing_month')
            ->orderBy('billing_month', 'desc')
            ->first();

        $latestMonth = $latestMonthRow ? $latestMonthRow->billing_month : null;
        $selectedMonth = (int) $request->get('month', $latestMonth ?? date('m'));
        $paidAmount = 0;
        $unpaidAmount = 0;

        if ($selectedMonth) {
            $monthStatsQuery = DB::table('bills')
                ->whereNull('bills.deleted_at')
                ->where('bills.status', '!=', 'cancelled')
                ->where('bills.billing_year', $selectedYear)
                ->where('bills.billing_month', $selectedMonth);
            if ($selectedBlock) {
                $monthStatsQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                    ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                    ->where('floors.block_id', $selectedBlock);
            }
            $monthStats = $monthStatsQuery->selectRaw('SUM(paid_amount) as paid, SUM(total_amount - paid_amount) as unpaid')
                ->first();

            if ($monthStats) {
                $paidAmount = (float) $monthStats->paid;
                $unpaidAmount = (float) $monthStats->unpaid;
            }
        }

        // 4.5. Xu hướng sản lượng tiêu thụ Nước theo tháng (Bỏ Điện công tơ)
        $utilityConsumptionQuery = DB::table('utility_meters')
            ->select(
                'utility_meters.record_month',
                'utility_meters.type',
                DB::raw('SUM(utility_meters.usage_amount) as total_usage')
            )
            ->where('utility_meters.type', 'water')
            ->where('utility_meters.status', 'approved')
            ->where('utility_meters.record_year', $selectedYear);
        if ($selectedBlock) {
            $utilityConsumptionQuery->join('apartments', 'utility_meters.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $utilityConsumption = $utilityConsumptionQuery->groupBy('utility_meters.record_month', 'utility_meters.type')->get();

        $electricityConsumption = array_fill(0, 12, 0);
        $waterConsumption = array_fill(0, 12, 0);

        foreach ($utilityConsumption as $row) {
            $monthIndex = (int)$row->record_month - 1; // 0-indexed for JS array
            if ($row->type === 'water') {
                $waterConsumption[$monthIndex] = (float)$row->total_usage;
            }
        }

        $monthlyLabels = ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'];

        return view('admin.statistics.finance', compact(
            'blocks', 'selectedBlock',
            'availableYears', 'selectedYear', 'selectedMonth',
            'totalBilled', 'totalCollected', 'totalUnpaid', 'collectionRate',
            'yearBilled', 'yearCollected', 'yearUnpaid', 'yearCollectionRate',
            'monthlyRevenue', 'monthlyLabels',
            'monthlyStackedData', 'latestMonth', 'paidAmount', 'unpaidAmount',
            'electricityConsumption', 'waterConsumption'
        ));
    }

    /**
     * Xuất báo cáo tài chính năm thành file Excel.
     */
    public function exportFinanceExcel(Request $request)
    {
        $selectedBlock = $request->get('block_id');

        $availableYearsQuery = DB::table('bills')
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled');
        if ($selectedBlock) {
            $availableYearsQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $availableYears = $availableYearsQuery->selectRaw('DISTINCT bills.billing_year')
            ->orderBy('bills.billing_year', 'desc')
            ->pluck('billing_year')
            ->toArray();

        $selectedYear = $request->get('year', date('Y'));
        if (!in_array($selectedYear, $availableYears) && count($availableYears) > 0) {
            $selectedYear = $availableYears[0];
        }

        $monthlyRevenueQuery = DB::table('bills')
            ->select(
                'bills.billing_year',
                'bills.billing_month',
                DB::raw('SUM(bills.total_amount) as total_billed'),
                DB::raw("SUM(bills.paid_amount) as total_collected")
            )
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled')
            ->where('bills.billing_year', $selectedYear);
        if ($selectedBlock) {
            $monthlyRevenueQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $monthlyRevenue = $monthlyRevenueQuery->groupBy('bills.billing_year', 'bills.billing_month')
            ->orderBy('bills.billing_month')
            ->get();

        $yearBilledQuery = DB::table('bills')
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled')
            ->where('bills.billing_year', $selectedYear);
        if ($selectedBlock) {
            $yearBilledQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $yearBilled = $yearBilledQuery->sum('bills.total_amount');

        $yearCollectedQuery = DB::table('payments')
            ->join('bills', 'payments.bill_id', '=', 'bills.id')
            ->whereNull('payments.deleted_at')
            ->whereNull('bills.deleted_at')
            ->where('payments.status', 'success')
            ->where('bills.billing_year', $selectedYear);
        if ($selectedBlock) {
            $yearCollectedQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $yearCollected = $yearCollectedQuery->sum('payments.amount');

        $yearUnpaid = $yearBilled - $yearCollected;
        $yearCollectionRate = $yearBilled > 0 ? ($yearCollected / $yearBilled) * 100 : 0;

        // Tính cơ cấu chi tiết từng loại dịch vụ
        $monthlyServiceQuery = DB::table('bill_details')
            ->join('bills', 'bill_details.bill_id', '=', 'bills.id')
            ->join('service_prices', 'bill_details.service_price_id', '=', 'service_prices.id')
            ->select(
                'bills.billing_month',
                'service_prices.type',
                DB::raw('SUM(bill_details.amount) as total_amount')
            )
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled')
            ->where('bills.billing_year', $selectedYear);
        if ($selectedBlock) {
            $monthlyServiceQuery->join('apartments', 'bills.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $monthlyService = $monthlyServiceQuery->groupBy('bills.billing_month', 'service_prices.type')->get();

        $serviceData = [];
        for ($m = 1; $m <= 12; $m++) {
            $serviceData[$m] = [
                'electricity' => 0.0,
                'water' => 0.0,
                'management_fee' => 0.0,
                'other' => 0.0
            ];
        }
        foreach ($monthlyService as $row) {
            $m = (int)$row->billing_month;
            $type = $row->type;
            if (in_array($type, ['electricity', 'water', 'management_fee'])) {
                $serviceData[$m][$type] = (float)$row->total_amount;
            } else {
                $serviceData[$m]['other'] += (float)$row->total_amount;
            }
        }

        try {
            $filePath = \App\Helpers\SimpleXlsx::exportFinanceReport(
                $selectedYear,
                $monthlyRevenue,
                $yearBilled,
                $yearCollected,
                $yearUnpaid,
                $yearCollectionRate,
                $serviceData
            );

            return response()->download($filePath, "Bao-cao-tai-chinh-nam-{$selectedYear}.xlsx")->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xuất file báo cáo: ' . $e->getMessage());
        }
    }

    /**
     * Xuất báo cáo thống kê Vận hành & SLA thành file Excel.
     */
    public function exportOperationsExcel(Request $request)
    {
        $selectedBlock = $request->get('block_id');

        $availableYearsQuery = DB::table('tickets')
            ->whereNull('tickets.deleted_at');
        if ($selectedBlock) {
            $availableYearsQuery->join('apartments', 'tickets.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $availableYears = $availableYearsQuery->selectRaw('DISTINCT YEAR(tickets.created_at) as ticket_year')
            ->orderBy('ticket_year', 'desc')
            ->pluck('ticket_year')
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        $selectedYear = $request->get('year', date('Y'));
        if (!in_array($selectedYear, $availableYears) && count($availableYears) > 0) {
            $selectedYear = $availableYears[0];
        }

        $selectedMonth = $request->get('month');

        // Truy vấn tất cả các phản ánh của năm và tháng được chọn
        $ticketsQuery = Ticket::with(['sender', 'apartment.floor.block'])
            ->whereYear('tickets.created_at', $selectedYear);
        if ($selectedMonth) {
            $ticketsQuery->whereMonth('tickets.created_at', $selectedMonth);
        }
        if ($selectedBlock) {
            $ticketsQuery->join('apartments', 'tickets.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock)
                ->select('tickets.*');
        }
        $tickets = $ticketsQuery->orderBy('tickets.created_at', 'desc')->get();

        try {
            $filePath = \App\Helpers\SimpleXlsx::exportOperationsReport($selectedYear, $tickets);
            $filename = "Bao-cao-van-hanh-nam-{$selectedYear}" . ($selectedMonth ? "-thang-{$selectedMonth}" : "") . ".xlsx";
            return response()->download($filePath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xuất file báo cáo vận hành: ' . $e->getMessage());
        }
    }

    /**
     * Xuất báo cáo thống kê Cư dân & Hạ tầng thành file Excel.
     */
    public function exportResidentsExcel(Request $request)
    {
        $selectedBlock = $request->get('block_id');
        $selectedYear = $request->get('year');
        $selectedMonth = $request->get('month');

        // Truy vấn tất cả các căn hộ kèm quan hệ tòa/tầng và số cư dân hiện tại
        $apartmentsQuery = Apartment::with(['floor.block'])
            ->withCount(['residents as resident_count' => function ($q) use ($selectedYear, $selectedMonth) {
                $q->whereNull('deleted_at');
                if ($selectedYear) {
                    $q->whereYear('created_at', $selectedYear);
                    if ($selectedMonth) {
                        $q->whereMonth('created_at', $selectedMonth);
                    }
                }
            }]);
        if ($selectedBlock) {
            $apartmentsQuery->whereIn('floor_id', function($q) use ($selectedBlock) {
                $q->select('id')->from('floors')->where('block_id', $selectedBlock);
            });
        }
        $apartments = $apartmentsQuery->get();

        try {
            $filePath = \App\Helpers\SimpleXlsx::exportResidentsReport($apartments);
            $filename = "Bao-cao-cu-dan-va-ha-tang" . ($selectedYear ? "-nam-{$selectedYear}" : "") . ($selectedMonth ? "-thang-{$selectedMonth}" : "") . ".xlsx";
            return response()->download($filePath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra khi xuất file báo cáo cư dân: ' . $e->getMessage());
        }
    }

    /**
     * Màn hình thống kê Vận hành & SLA.
     */
    public function statisticsOperations(Request $request): View
    {
        // Lấy danh sách tòa nhà/block
        $blocks = Block::orderBy('name')->get();
        $selectedBlock = $request->get('block_id');

        // Lấy danh sách các năm có phản ánh để hiện trong bộ lọc
        $availableYearsQuery = DB::table('tickets')
            ->whereNull('tickets.deleted_at');
        if ($selectedBlock) {
            $availableYearsQuery->join('apartments', 'tickets.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $availableYears = $availableYearsQuery->selectRaw('DISTINCT YEAR(tickets.created_at) as ticket_year')
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

        $selectedMonth = $request->get('month');

        // 1. Số lượng phản ánh theo status lọc theo năm/tháng
        $ticketStatsQuery = DB::table('tickets')
            ->whereNull('tickets.deleted_at')
            ->whereYear('tickets.created_at', $selectedYear);
        if ($selectedMonth) {
            $ticketStatsQuery->whereMonth('tickets.created_at', $selectedMonth);
        }
        if ($selectedBlock) {
            $ticketStatsQuery->join('apartments', 'tickets.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $ticketStats = $ticketStatsQuery->select('tickets.status', DB::raw('count(*) as count'))
            ->groupBy('tickets.status')
            ->pluck('count', 'status')
            ->toArray();

        $pendingCount    = $ticketStats['pending'] ?? 0;
        $assignedCount   = $ticketStats['assigned'] ?? 0;
        $inProgressCount = $ticketStats['in_progress'] ?? 0;
        $completedCount  = $ticketStats['completed'] ?? 0;
        $cancelledCount  = $ticketStats['cancelled'] ?? 0;
        $totalTickets    = array_sum($ticketStats);

        // 2. Thống kê phân bố đánh giá (1-5 sao)
        $csatStatsQuery = DB::table('tickets')
            ->whereNull('tickets.deleted_at')
            ->where('tickets.status', 'completed')
            ->whereYear('tickets.created_at', $selectedYear)
            ->whereMonth('tickets.created_at', $selectedMonth)
            ->whereNotNull('tickets.rating');
        if ($selectedMonth) {
            $csatStatsQuery->whereMonth('tickets.created_at', $selectedMonth);
        }
        if ($selectedBlock) {
            $csatStatsQuery->join('apartments', 'tickets.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $csatStats = $csatStatsQuery->select('tickets.rating', DB::raw('count(*) as count'))
            ->groupBy('tickets.rating')
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
        $recentFeedbacksQuery = Ticket::with(['sender', 'apartment'])
            ->where('tickets.status', 'completed')
            ->whereYear('tickets.created_at', $selectedYear)
            ->whereMonth('tickets.created_at', $selectedMonth)
            ->whereNotNull('tickets.rating');
        if ($selectedMonth) {
            $recentFeedbacksQuery->whereMonth('tickets.created_at', $selectedMonth);
        }
        if ($selectedBlock) {
            $recentFeedbacksQuery->join('apartments', 'tickets.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock)
                ->select('tickets.*');
        }
        $recentFeedbacks = $recentFeedbacksQuery->orderBy('tickets.updated_at', 'desc')
            ->limit(5)
            ->get();

        // 4. Tính thời gian xử lý sự cố trung bình (SLA) - Tương thích đa CSDL (SQLite/MySQL)
        $driver = DB::getDriverName();
        $slaDiffSelect = $driver === 'sqlite'
            ? "(strftime('%s', tickets.updated_at) - strftime('%s', tickets.created_at))"
            : "TIMESTAMPDIFF(SECOND, tickets.created_at, tickets.updated_at)";

        $avgSlaQuery = DB::table('tickets')
            ->whereNull('tickets.deleted_at')
            ->where('tickets.status', 'completed')
            ->whereYear('tickets.created_at', $selectedYear);
        if ($selectedMonth) {
            $avgSlaQuery->whereMonth('tickets.created_at', $selectedMonth);
        }
        if ($selectedBlock) {
            $avgSlaQuery->join('apartments', 'tickets.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $avgResolutionSeconds = $avgSlaQuery->selectRaw("AVG($slaDiffSelect) as avg_sla")->value('avg_sla') ?? 0;

        $formattedResolutionTime = 'N/A';
        if ($avgResolutionSeconds) {
            $hours = round($avgResolutionSeconds / 3600, 1);
            if ($hours >= 24) {
                $days = round($hours / 24, 1);
                $formattedResolutionTime = $days . ' ngày';
            } else {
                $formattedResolutionTime = $hours . ' giờ';
            }
        }

        // 5. Phân bố mức độ ưu tiên phản ánh
        $priorityStatsQuery = DB::table('tickets')
            ->whereNull('tickets.deleted_at')
            ->whereYear('tickets.created_at', $selectedYear);
        if ($selectedMonth) {
            $priorityStatsQuery->whereMonth('tickets.created_at', $selectedMonth);
        }
        if ($selectedBlock) {
            $priorityStatsQuery->join('apartments', 'tickets.apartment_id', '=', 'apartments.id')
                ->join('floors', 'apartments.floor_id', '=', 'floors.id')
                ->where('floors.block_id', $selectedBlock);
        }
        $priorityStats = $priorityStatsQuery->select('tickets.priority', DB::raw('count(*) as count'))
            ->groupBy('tickets.priority')
            ->pluck('count', 'priority')
            ->toArray();

        $priorityData = [
            'low'    => $priorityStats['low'] ?? 0,
            'medium' => $priorityStats['medium'] ?? 0,
            'high'   => $priorityStats['high'] ?? 0,
            'urgent' => $priorityStats['urgent'] ?? 0,
        ];

        // 6. Thống kê hiệu quả công việc của từng kỹ thuật viên (KPI) - Tương thích SQLite/MySQL
        if ($selectedBlock) {
            $technicianPerformanceQuery = DB::table('users')
                ->leftJoin('tickets', function ($join) use ($selectedYear, $selectedMonth, $selectedBlock) {
                    $join->on('users.id', '=', 'tickets.handler_id')
                        ->whereNull('tickets.deleted_at')
                        ->where('tickets.status', '=', 'completed')
                        ->whereYear('tickets.created_at', $selectedYear);
                    if ($selectedMonth) {
                        $join->whereMonth('tickets.created_at', $selectedMonth);
                    }
                    $join->whereIn('tickets.apartment_id', function ($query) use ($selectedBlock) {
                        $query->select('id')
                            ->from('apartments')
                            ->whereIn('floor_id', function ($q) use ($selectedBlock) {
                                $q->select('id')
                                    ->from('floors')
                                    ->where('block_id', $selectedBlock);
                            });
                    });
                })
                ->where('users.role', 'technician');
        } else {
            $technicianPerformanceQuery = DB::table('users')
                ->leftJoin('tickets', function ($join) use ($selectedYear, $selectedMonth) {
                    $join->on('users.id', '=', 'tickets.handler_id')
                        ->whereNull('tickets.deleted_at')
                        ->where('tickets.status', '=', 'completed')
                        ->whereYear('tickets.created_at', $selectedYear);
                    if ($selectedMonth) {
                        $join->whereMonth('tickets.created_at', $selectedMonth);
                    }
                })
                ->where('users.role', 'technician');
        }

        $technicians = $technicianPerformanceQuery->select(
                'users.name',
                DB::raw('COUNT(tickets.id) as resolved_count'),
                DB::raw('ROUND(AVG(tickets.rating), 1) as avg_rating'),
                DB::raw("AVG($slaDiffSelect) as avg_sla_seconds")
            )
            ->groupBy('users.id', 'users.name')
            ->get();

        $technicianPerformance = [];
        foreach ($technicians as $tech) {
            $formattedTime = 'N/A';
            if ($tech->avg_sla_seconds > 0) {
                $hours = round($tech->avg_sla_seconds / 3600, 1);
                if ($hours >= 24) {
                    $days = round($hours / 24, 1);
                    $formattedTime = $days . ' ngày';
                } else {
                    $formattedTime = $hours . ' giờ';
                }
            }

            $technicianPerformance[] = (object)[
                'name' => $tech->name,
                'resolved_count' => (int)$tech->resolved_count,
                'avg_rating' => $tech->avg_rating ? (float)$tech->avg_rating : 0.0,
                'avg_resolution_time' => $formattedTime
            ];
        }

        // Sắp xếp theo số lượng hoàn thành giảm dần
        usort($technicianPerformance, function($a, $b) {
            if ($a->resolved_count === $b->resolved_count) {
                return $b->avg_rating <=> $a->avg_rating;
            }
            return $b->resolved_count <=> $a->resolved_count;
        });

        return view('admin.statistics.operations', compact(
            'blocks', 'selectedBlock',
            'availableYears', 'selectedYear', 'selectedMonth',
            'totalTickets', 'pendingCount', 'assignedCount', 'inProgressCount', 'completedCount', 'cancelledCount',
            'csatData', 'totalRated', 'averageRating', 'recentFeedbacks',
            'formattedResolutionTime', 'priorityData', 'technicianPerformance'
        ));
    }

    /**
     * Màn hình thống kê Cư dân & Hạ tầng.
     */
    public function statisticsResidents(Request $request): View
    {
        // Lấy danh sách tòa nhà/block
        $blocks = Block::orderBy('name')->get();
        $selectedBlock = $request->get('block_id');

        // Lấy danh sách các năm cư dân đăng ký để hiện trong bộ lọc
        $driver = DB::getDriverName();
        $yearExpression = $driver === 'sqlite' ? "strftime('%Y', created_at)" : "YEAR(created_at)";

        $availableYearsQuery = DB::table('residents')
            ->whereNull('deleted_at');
        if ($selectedBlock) {
            $availableYearsQuery->whereIn('apartment_id', function($q) use ($selectedBlock) {
                $q->select('id')->from('apartments')->whereIn('floor_id', function($q2) use ($selectedBlock) {
                    $q2->select('id')->from('floors')->where('block_id', $selectedBlock);
                });
            });
        }
        $availableYears = $availableYearsQuery->selectRaw("DISTINCT $yearExpression as year")
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->map(fn($y) => (int)$y)
            ->filter()
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }


        $selectedYear = $request->get('year', date('Y'));
        if (!in_array($selectedYear, $availableYears) && count($availableYears) > 0) {
            $selectedYear = $availableYears[0];
        }

        $selectedMonth = $request->get('month');

        // ── 1. TỔNG QUAN HẠ TẦNG ──────────────────────────────────────
        $totalBlocks = Block::count();
        $totalFloorsQuery = Floor::query();
        $totalApartmentsQuery = Apartment::query();
        $occupiedQuery = Apartment::where('status', 'occupied');
        $vacantQuery = Apartment::where('status', 'vacant');
        $maintenanceQuery = Apartment::where('status', 'maintenance');

        if ($selectedBlock) {
            $totalFloorsQuery->where('block_id', $selectedBlock);
            $totalApartmentsQuery->whereIn('floor_id', function($q) use ($selectedBlock) {
                $q->select('id')->from('floors')->where('block_id', $selectedBlock);
            });
            $occupiedQuery->whereIn('floor_id', function($q) use ($selectedBlock) {
                $q->select('id')->from('floors')->where('block_id', $selectedBlock);
            });
            $vacantQuery->whereIn('floor_id', function($q) use ($selectedBlock) {
                $q->select('id')->from('floors')->where('block_id', $selectedBlock);
            });
            $maintenanceQuery->whereIn('floor_id', function($q) use ($selectedBlock) {
                $q->select('id')->from('floors')->where('block_id', $selectedBlock);
            });
        }

        $totalFloors = $totalFloorsQuery->count();
        $totalApartments = $totalApartmentsQuery->count();
        $occupied = $occupiedQuery->count();
        $vacant = $vacantQuery->count();
        $maintenance = $maintenanceQuery->count();
        $occupancyRate = $totalApartments > 0 ? round(($occupied / $totalApartments) * 100, 1) : 0;

        // ── 2. TỔNG QUAN CƯ DÂN (Lọc theo năm/tháng) ───────────────────
        $totalResidentsQuery = Resident::query();
        $ownerCountQuery = Resident::where('relationship', 'owner');
        $tenantCountQuery = Resident::where('relationship', 'tenant');
        $familyCountQuery = Resident::where('relationship', 'family_member');

        if ($selectedBlock) {
            $totalResidentsQuery->whereIn('apartment_id', function($q) use ($selectedBlock) {
                $q->select('id')->from('apartments')->whereIn('floor_id', function($q2) use ($selectedBlock) {
                    $q2->select('id')->from('floors')->where('block_id', $selectedBlock);
                });
            });
            $ownerCountQuery->whereIn('apartment_id', function($q) use ($selectedBlock) {
                $q->select('id')->from('apartments')->whereIn('floor_id', function($q2) use ($selectedBlock) {
                    $q2->select('id')->from('floors')->where('block_id', $selectedBlock);
                });
            });
            $tenantCountQuery->whereIn('apartment_id', function($q) use ($selectedBlock) {
                $q->select('id')->from('apartments')->whereIn('floor_id', function($q2) use ($selectedBlock) {
                    $q2->select('id')->from('floors')->where('block_id', $selectedBlock);
                });
            });
            $familyCountQuery->whereIn('apartment_id', function($q) use ($selectedBlock) {
                $q->select('id')->from('apartments')->whereIn('floor_id', function($q2) use ($selectedBlock) {
                    $q2->select('id')->from('floors')->where('block_id', $selectedBlock);
                });
            });
        }

        if ($selectedYear) {
            $totalResidentsQuery->whereYear('created_at', $selectedYear);
            $ownerCountQuery->whereYear('created_at', $selectedYear);
            $tenantCountQuery->whereYear('created_at', $selectedYear);
            $familyCountQuery->whereYear('created_at', $selectedYear);
            
            if ($selectedMonth) {
                $totalResidentsQuery->whereMonth('created_at', $selectedMonth);
                $ownerCountQuery->whereMonth('created_at', $selectedMonth);
                $tenantCountQuery->whereMonth('created_at', $selectedMonth);
                $familyCountQuery->whereMonth('created_at', $selectedMonth);
            }
        }

        $totalResidents = $totalResidentsQuery->count();
        $ownerCount     = $ownerCountQuery->count();
        $tenantCount    = $tenantCountQuery->count();
        $familyCount    = $familyCountQuery->count();

        // ── 3. TỶ LỆ LOẠI CƯ DÂN (cho pie chart) ─────────────────────
        $residentTypeData = [
            'owner'         => $ownerCount,
            'tenant'        => $tenantCount,
            'family_member' => $familyCount,
        ];

        // ── 4. PHÂN BỐ CĂN HỘ THEO TÒNG NHÀ ─────────────────────────
        $blockStatsQuery = Block::withCount([
            'apartments',
            'apartments as occupied_count' => fn($q) => $q->where('apartments.status', 'occupied'),
            'apartments as vacant_count'   => fn($q) => $q->where('apartments.status', 'vacant'),
            'apartments as maintenance_count' => fn($q) => $q->where('apartments.status', 'maintenance'),
        ]);
        if ($selectedBlock) {
            $blockStatsQuery->where('id', $selectedBlock);
        }
        $blockStats = $blockStatsQuery->orderBy('name')->get();

        // ── 5. XU HƯỚNG CƯ DÂN ĐĂNG KÝ THEO THÁNG (12 tháng gần nhất dựa trên năm/tháng được chọn) ─
        $selectedDate = now();
        if ($selectedYear) {
            $m = $selectedMonth ?: 12;
            $selectedDate = \Carbon\Carbon::create($selectedYear, $m, 1);
        }

        $residentTrendQuery = DB::table('residents')
            ->whereNull('deleted_at')
            ->where('created_at', '>=', $selectedDate->copy()->subMonths(11)->startOfMonth())
            ->where('created_at', '<=', $selectedDate->copy()->endOfMonth());
        if ($selectedBlock) {
            $residentTrendQuery->whereIn('apartment_id', function($q) use ($selectedBlock) {
                $q->select('id')->from('apartments')->whereIn('floor_id', function($q2) use ($selectedBlock) {
                    $q2->select('id')->from('floors')->where('block_id', $selectedBlock);
                });
            });
        }
        $dateFormatSelect = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $residentTrend = $residentTrendQuery->selectRaw("$dateFormatSelect as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Tạo mảng đủ 12 tháng của năm được chọn
        $trendLabels = [];
        $trendValues = [];
        for ($i = 11; $i >= 0; $i--) {
            $key = $selectedDate->copy()->subMonths($i)->format('Y-m');
            $trendLabels[] = 'T' . $selectedDate->copy()->subMonths($i)->month . '/' . $selectedDate->copy()->subMonths($i)->format('y');
            $trendValues[] = $residentTrend[$key] ?? 0;
        }

        // ── 6. TOP CĂN HỘ ĐÔNG NGƯỜI NHẤT ────────────────────────────
        $topApartmentsQuery = DB::table('residents')
            ->join('apartments', 'residents.apartment_id', '=', 'apartments.id')
            ->join('floors', 'apartments.floor_id', '=', 'floors.id')
            ->join('blocks', 'floors.block_id', '=', 'blocks.id')
            ->whereNull('residents.deleted_at')
            ->whereYear('residents.created_at', $selectedYear)
            ->whereMonth('residents.created_at', $selectedMonth);
        if ($selectedBlock) {
            $topApartmentsQuery->where('blocks.id', $selectedBlock);
        }
        $topApartments = $topApartmentsQuery->select(
                'apartments.apartment_number',
                'blocks.name as block_name',
                DB::raw('COUNT(residents.id) as resident_count')
            )
            ->groupBy('apartments.id', 'apartments.apartment_number', 'blocks.name')
            ->orderByDesc('resident_count')
            ->limit(10)
            ->get();

        // ── 7. THỐNG KÊ THEO TẦNG (heatmap-style) ─────────────────────
        $floorStatsQuery = DB::table('floors')
            ->join('blocks', 'floors.block_id', '=', 'blocks.id')
            ->leftJoin('apartments', 'apartments.floor_id', '=', 'floors.id');
        if ($selectedBlock) {
            $floorStatsQuery->where('blocks.id', $selectedBlock);
        }
        $floorStats = $floorStatsQuery->select(
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
            'blocks', 'selectedBlock',
            'availableYears', 'selectedYear', 'selectedMonth',
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
