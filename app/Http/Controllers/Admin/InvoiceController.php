<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SystemLogger;
use App\Models\Invoice;
use App\Models\Apartment;
use App\Models\InvoiceDetail;
use App\Models\ServicePrice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Thống kê hóa đơn.
     */
    public function stats(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        // KPI tổng quát
        $totalRevenue     = Invoice::where('status', 'paid')->sum('total_amount');
        $thisMonthRevenue = Invoice::where('status', 'paid')
                               ->whereMonth('updated_at', now()->month)
                               ->whereYear('updated_at', now()->year)
                               ->sum('total_amount');
        $totalInvoices    = Invoice::count();
        $paidCount        = Invoice::where('status', 'paid')->count();
        $unpaidCount      = Invoice::where('status', 'unpaid')->count();
        $overdueCount     = Invoice::where('status', 'unpaid')->where('due_date', '<', now())->count();
        $totalUnpaid      = Invoice::where('status', 'unpaid')->sum('total_amount');
        $totalOverdue     = Invoice::where('status', 'unpaid')->where('due_date', '<', now())->sum('total_amount');

        // Doanh thu 12 tháng của năm được chọn
        $revenueByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $base = Invoice::where('billing_year', $year)->where('billing_month', $m);
            $revenueByMonth[] = [
                'label'   => 'T' . $m,
                'paid'    => (clone $base)->where('status', 'paid')->sum('total_amount'),
                'unpaid'  => (clone $base)->where('status', 'unpaid')->where('due_date', '>=', now())->sum('total_amount'),
                'overdue' => (clone $base)->where('status', 'unpaid')->where('due_date', '<', now())->sum('total_amount'),
            ];
        }

        // Phân tích theo loại phí (join qua bill_details -> service_prices)
        $typeLabels = [
            'electricity'    => 'Tiền điện',
            'water'          => 'Tiền nước',
            'management_fee' => 'Phí quản lý',
            'parking'        => 'Phí đỗ xe',
            'other'          => 'Khác',
        ];
        $byType = DB::table('bills')
            ->join('bill_details', 'bills.id', '=', 'bill_details.bill_id')
            ->join('service_prices', 'bill_details.service_price_id', '=', 'service_prices.id')
            ->whereNull('bills.deleted_at')
            ->select('service_prices.type', DB::raw('SUM(bills.total_amount) as total'))
            ->groupBy('service_prices.type')
            ->get()
            ->map(fn($r) => [
                'type'  => $r->type,
                'label' => $typeLabels[$r->type] ?? $r->type,
                'total' => (float) $r->total,
            ]);

        // Hóa đơn gần đây
        $recentInvoices = Invoice::with(['apartment.floor.block'])
            ->orderByDesc('created_at')->limit(8)->get();

        // Top nợ đọng theo căn hộ
        $topDebt = Invoice::with(['apartment.floor.block'])
            ->where('status', 'unpaid')
            ->select('apartment_id', DB::raw('SUM(total_amount) as debt'), DB::raw('COUNT(*) as count'))
            ->groupBy('apartment_id')
            ->orderByDesc('debt')
            ->limit(7)
            ->get();

        // Tỉ lệ thu tiền theo tháng
        $collectionRate = collect(array_map(function ($m) use ($year) {
            $total = Invoice::where('billing_year', $year)->where('billing_month', $m)->count();
            $paid  = Invoice::where('billing_year', $year)->where('billing_month', $m)->where('status', 'paid')->count();
            return ['month' => $m, 'rate' => $total > 0 ? round($paid / $total * 100) : 0];
        }, range(1, 12)));

        return view('admin.invoices.stats', compact(
            'year', 'totalRevenue', 'thisMonthRevenue',
            'totalInvoices', 'paidCount', 'unpaidCount',
            'overdueCount', 'totalUnpaid', 'totalOverdue',
            'revenueByMonth', 'byType', 'recentInvoices',
            'topDebt', 'collectionRate'
        ));
    }

    /**
     * Danh sách hóa đơn có filter.
     */

    public function index(Request $request)
    {
        $query = Invoice::with(['apartment.floor.block', 'details.servicePrice'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->whereHas('details.servicePrice', function ($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->where('billing_month', (int) $month)
                  ->where('billing_year', (int) $year);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $cleanSearch = ltrim(str_ireplace('BILL-', '', $search), '0');

            $query->where(function ($q) use ($search, $cleanSearch) {
                // Tìm theo tiêu đề hoặc ID hóa đơn
                $q->where('title', 'like', '%' . $search . '%');
                if (is_numeric($cleanSearch)) {
                    $q->orWhere('id', $cleanSearch);
                }

                // Tìm theo thông tin căn hộ hoặc cư dân
                $q->orWhereHas('apartment', function ($a) use ($search) {
                    $a->where(function ($sq) use ($search) {
                        $sq->where('apartment_number', 'like', '%' . $search . '%')
                           ->orWhereHas('floor.block', function ($b) use ($search) {
                               $b->where('name', 'like', '%' . $search . '%')
                                 ->orWhere('code', 'like', '%' . $search . '%')
                                 ->orWhere(DB::raw("CONCAT(blocks.code, apartments.apartment_number)"), 'like', '%' . str_replace(['-', ' '], '', $search) . '%')
                                 ->orWhere(DB::raw("CONCAT(blocks.name, ' ', apartments.apartment_number)"), 'like', '%' . $search . '%');
                           });
                    })
                    ->orWhereHas('residents.user', function ($u) use ($search) {
                        $u->where('name', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%')
                          ->orWhere('phone', 'like', '%' . $search . '%');
                    });
                });
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Form tạo hóa đơn mới.
     */
    public function create()
    {
        $apartments = Apartment::with('floor.block')->get();
        return view('admin.invoices.create', compact('apartments'));
    }

    /**
     * Lưu hóa đơn mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id'   => 'required|exists:apartments,id',
            'title'          => 'required|string|max:150',
            'type'           => 'required|in:electricity,water,management_fee,parking,other',
            'amount'         => 'required|numeric|min:0',
            'billing_month'  => 'required|string',
            'due_date'       => 'required|date',
            'note'           => 'nullable|string|max:500',
        ]);

        // Parse month and year from YYYY-MM
        $monthYear = explode('-', $validated['billing_month']);
        $billingMonth = (int) $monthYear[1];
        $billingYear = (int) $monthYear[0];

        // Ensure ServicePrice exists
        $servicePrice = \App\Models\ServicePrice::firstOrCreate(
            ['type' => $validated['type']],
            [
                'name' => 'Phí ' . Invoice::typeLabel($validated['type']),
                'unit_price' => $validated['amount'],
                'status' => 'active',
                'description' => 'Tự động tạo từ màn hình phát hành hóa đơn'
            ]
        );

        // Find if an invoice already exists for the apartment in this month/year
        $invoice = Invoice::where('apartment_id', $validated['apartment_id'])
            ->where('billing_month', $billingMonth)
            ->where('billing_year', $billingYear)
            ->first();

        if ($invoice) {
            // Check if this service type already exists
            $exists = \App\Models\InvoiceDetail::where('bill_id', $invoice->id)
                ->where('service_price_id', $servicePrice->id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Hóa đơn tháng này của căn hộ đã có loại phí này.');
            }

            $invoice->increment('total_amount', $validated['amount']);
        } else {
            $invoice = Invoice::create([
                'apartment_id'  => $validated['apartment_id'],
                'title'         => 'Hóa đơn tháng ' . $billingMonth . '/' . $billingYear,
                'billing_month' => $billingMonth,
                'billing_year'  => $billingYear,
                'due_date'      => $validated['due_date'],
                'total_amount'  => $validated['amount'],
                'status'        => 'unpaid',
            ]);
        }

        // Create InvoiceDetail
        \App\Models\InvoiceDetail::create([
            'bill_id'          => $invoice->id,
            'service_price_id' => $servicePrice->id,
            'quantity'         => 1,
            'amount'           => $validated['amount'],
        ]);



        return redirect()->route('admin.invoices.index')
                         ->with('success', 'Hóa đơn đã được tạo thành công.');
    }

    /**
     * Chi tiết hóa đơn.
     */
    public function show(Invoice $invoice)
    {
        $invoice->recalculateDetailsStatus();
        $invoice->load(['apartment.floor.block', 'details.servicePrice', 'payments']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Form xuất hóa đơn hàng loạt.
     */
    public function batchCreate()
    {
        $apartments   = Apartment::with('floor.block')->orderBy('id')->get();
        $activePrices = ServicePrice::where('status', 'active')->get();

        return view('admin.invoices.batch', compact('apartments', 'activePrices'));
    }

    /**
     * Xử lý xuất hóa đơn hàng loạt.
     */
    public function batchStore(Request $request)
    {
        $request->validate([
            'billing_month' => 'required|string',
            'due_date'      => 'required|date',
            'types'         => 'required|array|min:1',
            'types.*'       => 'in:electricity,water,management_fee,parking,internet,service,other',
        ], [
            'types.required' => 'Vui lòng chọn ít nhất một loại phí.',
        ]);

        [$year, $month] = explode('-', $request->billing_month);
        $skipExisting  = $request->boolean('skip_existing', true);
        $onlyOccupied  = $request->boolean('only_occupied', true);

        $aptQuery = Apartment::query();
        if ($onlyOccupied) {
            $aptQuery->where('status', 'occupied');
        }
        $apartments = $aptQuery->get();

        $activePrices = ServicePrice::where('status', 'active')
            ->whereIn('type', $request->types)
            ->get()
            ->keyBy('type');

        $created = 0;
        $skipped = 0;

        foreach ($apartments as $apartment) {
            // Find or create the single invoice for this apartment for the billing month and year
            $invoice = Invoice::where('apartment_id', $apartment->id)
                ->where('billing_month', (int) $month)
                ->where('billing_year', (int) $year)
                ->first();

            $invoiceCreated = false;
            $invoiceAmountAdded = 0;

            foreach ($request->types as $type) {
                $servicePrice = $activePrices->get($type);
                if (!$servicePrice) { $skipped++; continue; }

                if ($invoice && $skipExisting) {
                    $exists = InvoiceDetail::where('bill_id', $invoice->id)
                        ->where('service_price_id', $servicePrice->id)
                        ->exists();

                    if ($exists) { $skipped++; continue; }
                }

                if (!$invoice) {
                    $invoice = Invoice::create([
                        'apartment_id'  => $apartment->id,
                        'title'         => 'Hóa đơn tháng ' . (int)$month . '/' . (int)$year,
                        'billing_month' => (int) $month,
                        'billing_year'  => (int) $year,
                        'due_date'      => $request->due_date,
                        'total_amount'  => 0,
                        'status'        => 'unpaid',
                    ]);
                    $invoiceCreated = true;
                }

                InvoiceDetail::create([
                    'bill_id'          => $invoice->id,
                    'service_price_id' => $servicePrice->id,
                    'quantity'         => 1,
                    'amount'           => $servicePrice->unit_price,
                ]);

                $invoiceAmountAdded += $servicePrice->unit_price;
            }

            if ($invoiceAmountAdded > 0) {
                $invoice->increment('total_amount', $invoiceAmountAdded);
                if ($invoiceCreated) {
                    $created++;
                }
            }
        }



        return redirect()->route('admin.invoices.index')
            ->with('success', "Đã tạo {$created} hóa đơn" . ($skipped ? ", bỏ qua {$skipped} (đã tồn tại hoặc thiếu đơn giá)." : '.'));
    }

    /**
     * Đánh dấu đã thanh toán (hỗ trợ thanh toán một phần, ghi chú, lưu người ghi nhận).
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $maxAmount = $invoice->remaining_amount > 0 ? $invoice->remaining_amount : $invoice->total_amount;

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,transfer,other',
            'amount'         => 'required|numeric|min:1|max:' . $maxAmount,
            'note'           => 'nullable|string|max:500',
            'proof_image'    => 'nullable|image|max:4096', // Max 4MB
            'payer_name'     => 'nullable|string|max:255',
            'transaction_code' => 'nullable|string|max:100',
        ]);

        $paymentMethodMap = [
            'cash'     => 'cash',
            'transfer' => 'bank_transfer',
            'other'    => 'other',
        ];

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('proofs', 'public');
        }

        DB::transaction(function () use ($invoice, $validated, $paymentMethodMap, $proofPath) {
            // Tạo bản ghi payment
            Payment::create([
                'bill_id'        => $invoice->id,
                'amount'         => $validated['amount'],
                'payment_method' => $paymentMethodMap[$validated['payment_method']] ?? 'other',
                'note'           => $validated['note'] ?? null,
                'proof_image'    => $proofPath,
                'payer_name'     => $validated['payer_name'] ?? null,
                'transaction_code'=> $validated['transaction_code'] ?? null,
                'recorded_by'    => auth()->id(),
                'status'         => 'success',
                'paid_at'        => now(),
            ]);

            // Cộng vào paid_amount của bill
            $newPaidAmount = (float) $invoice->paid_amount + (float) $validated['amount'];

            // Xác định status mới
            $newStatus = $newPaidAmount >= (float) $invoice->total_amount ? 'paid' : 'partial';

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'status'      => $newStatus,
            ]);

            $invoice->recalculateDetailsStatus();
        });

        // Sync trạng thái thanh toán cho lịch đặt tiện ích liên kết (nếu có)
        $freshInvoice = $invoice->fresh();
        if ($freshInvoice->status === 'paid') {
            $facilityBooking = $freshInvoice->facilityBooking;
            if ($facilityBooking && $facilityBooking->payment_status !== 'paid') {
                $facilityBooking->update([
                    'payment_status' => 'paid',
                    'payment_method' => $paymentMethodMap[$validated['payment_method']] ?? 'cash',
                ]);
            }
        }

        $message = (float)($invoice->fresh()->paid_amount) >= (float)$invoice->total_amount
            ? 'Hóa đơn đã được thanh toán đầy đủ.'
            : 'Ghi nhận thanh toán ' . number_format($validated['amount']) . 'đ thành công. Còn lại: ' . number_format($invoice->fresh()->remaining_amount) . 'đ.';



        return back()->with('success', $message);
    }

    /**
     * Hủy (refund) một lần thanh toán.
     */
    public function refundPayment(Request $request, Payment $payment)
    {
        if ($payment->is_refunded) {
            return back()->with('error', 'Thanh toán này đã được hủy trước đó.');
        }

        $validated = $request->validate([
            'refund_note' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($payment, $validated) {
            $invoice = $payment->invoice;

            // Cập nhật payment thành refunded
            $payment->update([
                'status'      => 'refunded',
                'refunded_at' => now(),
                'refund_note' => $validated['refund_note'] ?? null,
                'refunded_by' => auth()->id(),
            ]);

            // Trừ paid_amount của bill
            $newPaidAmount = max(0, (float) $invoice->paid_amount - (float) $payment->amount);

            // Xác định lại status
            if ($newPaidAmount <= 0) {
                $newStatus = 'unpaid';
            } elseif ($newPaidAmount < (float) $invoice->total_amount) {
                $newStatus = 'partial';
            } else {
                $newStatus = 'paid';
            }

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'status'      => $newStatus,
            ]);

            $invoice->recalculateDetailsStatus();
        });

        // Sync trạng thái booking tiện ích nếu hóa đơn bị hoàn tiền
        $freshInvoice = $payment->invoice->fresh();
        if ($freshInvoice->status !== 'paid') {
            $facilityBooking = $freshInvoice->facilityBooking;
            if ($facilityBooking && $facilityBooking->payment_status === 'paid') {
                $facilityBooking->update(['payment_status' => 'unpaid']);
            }
        }

        SystemLogger::log('system', 'Hủy thanh toán #' . $payment->id . ' trị giá ' . number_format($payment->amount) . 'đ của hóa đơn #' . $payment->invoice->id, $payment);

        return back()->with('success', 'Hủy thanh toán thành công. Trạng thái hóa đơn đã được cập nhật.');
    }

    /**
     * Ghi nhận thanh toán cho một chi tiết dịch vụ đơn lẻ.
     */
    public function markDetailAsPaid(Request $request, $id)
    {
        $detail = \App\Models\InvoiceDetail::with('invoice')->findOrFail($id);

        if ($detail->status === 'paid') {
            return back()->with('error', 'Khoản phí này đã được thanh toán trước đó.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,transfer,other',
            'note'           => 'nullable|string|max:500',
            'proof_image'    => 'nullable|image|max:4096', // Max 4MB
            'payer_name'     => 'nullable|string|max:255',
            'transaction_code' => 'nullable|string|max:100',
        ]);

        $paymentMethodMap = [
            'cash'     => 'cash',
            'transfer' => 'bank_transfer',
            'other'    => 'other',
        ];

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('proofs', 'public');
        }

        DB::transaction(function () use ($detail, $validated, $paymentMethodMap, $proofPath) {
            $invoice = $detail->invoice;

            // Cập nhật trạng thái chi tiết
            $detail->update(['status' => 'paid']);

            // Cập nhật hóa đơn chính
            $newPaidAmount = (float)$invoice->paid_amount + (float)$detail->amount;
            $newStatus = $newPaidAmount >= (float)$invoice->total_amount ? 'paid' : 'partial';

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'status'      => $newStatus,
            ]);

            $invoice->recalculateDetailsStatus();

            // Tạo bản ghi giao dịch thanh toán
            Payment::create([
                'bill_id'        => $invoice->id,
                'amount'         => $detail->amount,
                'payment_method' => $paymentMethodMap[$validated['payment_method']] ?? 'cash',
                'note'           => $validated['note'] ?? ('Thanh toán riêng lẻ: ' . ($detail->servicePrice->name ?? 'Phí dịch vụ')),
                'proof_image'    => $proofPath,
                'payer_name'     => $validated['payer_name'] ?? null,
                'transaction_code'=> $validated['transaction_code'] ?? null,
                'recorded_by'    => auth()->id(),
                'status'         => 'success',
                'paid_at'        => now(),
            ]);
        });

        return back()->with('success', 'Ghi nhận thanh toán thành công cho dịch vụ: ' . ($detail->servicePrice->name ?? 'Phí dịch vụ'));
    }

    /**
     * In biên lai thu tiền cho một giao dịch cụ thể.
     */
    public function printReceipt(Payment $payment)
    {
        $payment->load(['invoice.apartment.floor.block', 'recorder']);
        return view('admin.invoices.receipt', compact('payment'));
    }
}
