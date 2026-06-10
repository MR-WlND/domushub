<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Invoice;
use App\Models\Apartment;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Danh sách hóa đơn có filter.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['apartment.floor.block', 'details.servicePrice', 'payments'])
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
            $query->where(function ($q) use ($request) {
                $cleanSearch = str_replace('BILL-', '', $request->search);
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('id', 'like', '%' . $cleanSearch . '%')
                  ->orWhereHas('apartment', function ($a) use ($request) {
                      $a->where('apartment_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        // Auto-sync: cập nhật status những hóa đơn có payment success nhưng chưa được đánh dấu paid
        $invoices->each(function ($inv) {
            if ($inv->status !== 'paid' && $inv->payments->where('status', 'success')->isNotEmpty()) {
                $inv->update(['status' => 'paid']);
                $inv->status = 'paid';
            }
        });

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
            ['type' => $validated['type'], 'status' => 'active'],
            [
                'name' => 'Phí ' . Invoice::typeLabel($validated['type']),
                'unit_price' => $validated['amount'],
                'description' => 'Tự động tạo từ màn hình phát hành hóa đơn'
            ]
        );

        // Create the Invoice (bill)
        $invoice = Invoice::create([
            'apartment_id'  => $validated['apartment_id'],
            'title'         => $validated['title'],
            'billing_month' => $billingMonth,
            'billing_year'  => $billingYear,
            'due_date'      => $validated['due_date'],
            'total_amount'  => $validated['amount'],
            'status'        => 'unpaid',
        ]);

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
        $invoice->load(['apartment.floor.block', 'details.servicePrice']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Đánh dấu đã thanh toán.
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,transfer,other',
        ]);

        $invoice->update([
            'status' => 'paid',
        ]);

        // Log payment in payments table
        $paymentMethodMap = [
            'cash'     => 'cash',
            'transfer' => 'bank_transfer',
            'other'    => 'other',
        ];

        \App\Models\Payment::create([
            'bill_id'        => $invoice->id,
            'amount'         => $invoice->total_amount,
            'payment_method' => $paymentMethodMap[$validated['payment_method']] ?? 'other',
            'status'         => 'success',
            'paid_at'        => now(),
        ]);

        return back()->with('success', 'Hóa đơn đã được đánh dấu là đã thanh toán.');
    }
}
