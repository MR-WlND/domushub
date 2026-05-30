<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceStatController extends Controller
{
    /**
     * Trang dashboard thống kê tổng quan.
     */
    public function index(Request $request)
    {
        $year  = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // ── KPI Cards ──────────────────────────────────────────────────
        $totalRevenue   = Invoice::paid()->sum('amount');
        $totalUnpaid    = Invoice::unpaid()->sum('amount');
        $totalOverdue   = Invoice::overdue()->sum('amount');
        $totalInvoices  = Invoice::count();

        $paidCount    = Invoice::paid()->count();
        $unpaidCount  = Invoice::unpaid()->count();
        $overdueCount = Invoice::overdue()->count();

        // ── Tháng hiện tại ────────────────────────────────────────────
        $thisMonthRevenue = Invoice::paid()
            ->whereYear('billing_month', $year)
            ->whereMonth('billing_month', $month)
            ->sum('amount');

        $thisMonthInvoices = Invoice::whereYear('billing_month', $year)
            ->whereMonth('billing_month', $month)
            ->count();

        // ── Doanh thu 12 tháng gần đây (biểu đồ đường) ───────────────
        $revenueByMonth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenueByMonth[] = [
                'label'    => $date->format('M Y'),
                'month'    => $date->format('Y-m'),
                'paid'     => Invoice::paid()
                    ->whereYear('billing_month', $date->year)
                    ->whereMonth('billing_month', $date->month)
                    ->sum('amount'),
                'unpaid'   => Invoice::unpaid()
                    ->whereYear('billing_month', $date->year)
                    ->whereMonth('billing_month', $date->month)
                    ->sum('amount'),
                'overdue'  => Invoice::overdue()
                    ->whereYear('billing_month', $date->year)
                    ->whereMonth('billing_month', $date->month)
                    ->sum('amount'),
            ];
        }

        // ── Theo loại hóa đơn (biểu đồ tròn) ────────────────────────
        $byType = Invoice::select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->get()
            ->map(fn($r) => [
                'type'  => $r->type,
                'label' => Invoice::typeLabel($r->type),
                'count' => $r->count,
                'total' => $r->total,
            ]);

        // ── Top 8 căn hộ nợ đọng ──────────────────────────────────────
        $topDebt = Invoice::select('apartment_id', DB::raw('SUM(amount) as debt'), DB::raw('COUNT(*) as count'))
            ->whereIn('status', ['unpaid', 'overdue'])
            ->groupBy('apartment_id')
            ->orderByDesc('debt')
            ->limit(8)
            ->with('apartment.floor.block')
            ->get();

        // ── Hóa đơn mới nhất ──────────────────────────────────────────
        $recentInvoices = Invoice::with('apartment.floor.block')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // ── Tỉ lệ thu theo tháng trong năm ────────────────────────────
        $collectionRate = Invoice::select(
                DB::raw('MONTH(billing_month) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid')
            )
            ->whereYear('billing_month', $year)
            ->groupBy(DB::raw('MONTH(billing_month)'))
            ->orderBy('month')
            ->get()
            ->map(fn($r) => [
                'month' => $r->month,
                'rate'  => $r->total > 0 ? round($r->paid / $r->total * 100, 1) : 0,
            ]);

        return view('admin.invoices.stats', compact(
            'totalRevenue', 'totalUnpaid', 'totalOverdue', 'totalInvoices',
            'paidCount', 'unpaidCount', 'overdueCount',
            'thisMonthRevenue', 'thisMonthInvoices',
            'revenueByMonth', 'byType', 'topDebt', 'recentInvoices',
            'collectionRate', 'year', 'month'
        ));
    }
}
