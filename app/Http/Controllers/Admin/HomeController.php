<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Apartment;

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

        // Technician → redirect tới trang Phản ánh sự cố
        if ($user->role === 'technician') {
            return redirect()->route('admin.incidents.index');
        }

        $totalBlocks = Block::count();
        $totalFloors = Floor::count();
        $totalApartments = Apartment::count();
        $occupiedApartments = Apartment::where('status', 'occupied')->count();
        $vacantApartments = Apartment::where('status', 'vacant')->count();
        $maintenanceApartments = Apartment::where('status', 'maintenance')->count();
        $activeBlocks = Block::where('status', 'active')->count();

        return view('admin.dashboard.index', compact(
            'totalBlocks', 'totalFloors', 'totalApartments', 'occupiedApartments',
            'vacantApartments', 'maintenanceApartments', 'activeBlocks'
        ));
    }

    /**
     * Màn hình thống kê báo cáo (có bộ lọc theo năm).
     */
    public function statistics(Request $request): View
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

        // ----------------------------------------------------------------
        // 1. KPI TỔNG QUAN (Toàn thời gian)
        // ----------------------------------------------------------------
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
            ->whereIn('status', ['unpaid', 'overdue'])
            ->sum('total_amount');

        $collectionRate = $totalBilled > 0 ? ($totalCollected / $totalBilled) * 100 : 0;

        // ----------------------------------------------------------------
        // 2. DOANH THU THEO THÁNG (lọc theo năm đã chọn)
        // ----------------------------------------------------------------
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

        // Dữ liệu cho biểu đồ Line/Bar Chart (12 tháng, điền 0 nếu không có dữ liệu)
        $monthlyBilledData    = array_fill(1, 12, 0);
        $monthlyCollectedData = array_fill(1, 12, 0);

        foreach ($monthlyRevenue as $row) {
            $monthlyBilledData[$row->billing_month]    = (float) $row->total_billed;
            $monthlyCollectedData[$row->billing_month] = (float) $row->total_collected;
        }

        // Format lại thành mảng đánh số từ 0 cho JSON
        $monthlyLabels        = ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'];
        $monthlyBilledData    = array_values($monthlyBilledData);
        $monthlyCollectedData = array_values($monthlyCollectedData);

        // ----------------------------------------------------------------
        // 3. KPI RIÊNG CHO NĂM ĐƯỢC CHỌN
        // ----------------------------------------------------------------
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
            ->whereIn('status', ['unpaid', 'overdue'])
            ->where('billing_year', $selectedYear)
            ->sum('total_amount');

        $yearCollectionRate = $yearBilled > 0 ? ($yearCollected / $yearBilled) * 100 : 0;

        // ----------------------------------------------------------------
        // 4. CƠ CẤU DỊCH VỤ (theo năm đã chọn)
        // ----------------------------------------------------------------
        $serviceDistribution = DB::table('bill_details')
            ->join('bills', 'bill_details.bill_id', '=', 'bills.id')
            ->join('service_prices', 'bill_details.service_price_id', '=', 'service_prices.id')
            ->select(
                'service_prices.type',
                'service_prices.name',
                DB::raw('SUM(bill_details.amount) as total_amount')
            )
            ->whereNull('bills.deleted_at')
            ->where('bills.status', '!=', 'cancelled')
            ->where('bills.billing_year', $selectedYear)
            ->groupBy('service_prices.type', 'service_prices.name')
            ->orderByDesc('total_amount')
            ->get();

        $serviceLabels = [];
        $serviceData   = [];
        foreach ($serviceDistribution as $row) {
            $serviceLabels[] = $row->name;
            $serviceData[]   = (float) $row->total_amount;
        }

        return view('admin.statistics.index', compact(
            // KPI toàn thời gian
            'totalBilled', 'totalCollected', 'totalUnpaid', 'collectionRate',
            // KPI theo năm
            'yearBilled', 'yearCollected', 'yearUnpaid', 'yearCollectionRate',
            // Biểu đồ
            'monthlyLabels', 'monthlyBilledData', 'monthlyCollectedData',
            'serviceLabels', 'serviceData',
            // Bảng & bộ lọc
            'monthlyRevenue', 'availableYears', 'selectedYear'
        ));
    }
}
