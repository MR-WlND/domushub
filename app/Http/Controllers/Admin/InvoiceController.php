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
        $query = Invoice::with(['apartment.floor.block', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('billing_month', $year)
                  ->whereMonth('billing_month', $month);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_code', 'like', '%' . $request->search . '%')
                  ->orWhere('title', 'like', '%' . $request->search . '%');
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
            'billing_month'  => 'required|date',
            'due_date'       => 'required|date|after_or_equal:billing_month',
            'note'           => 'nullable|string|max:500',
        ]);

        $validated['created_by']    = auth()->id();
        $validated['invoice_code']  = Invoice::generateCode();
        $validated['status']        = 'unpaid';

        Invoice::create($validated);

        return redirect()->route('admin.invoices.index')
                         ->with('success', 'Hóa đơn đã được tạo thành công.');
    }

    /**
     * Chi tiết hóa đơn.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['apartment.floor.block', 'creator', 'items']);
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
            'status'         => 'paid',
            'paid_at'        => now(),
            'payment_method' => $validated['payment_method'],
        ]);

        return back()->with('success', 'Hóa đơn đã được đánh dấu là đã thanh toán.');
    }
}
