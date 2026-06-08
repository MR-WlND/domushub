<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Danh sách hoá đơn của cư dân
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Lấy danh sách căn hộ mà cư dân đang ở
        $apartmentIds = $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        $query = Invoice::with(['apartment.floor.block', 'details.servicePrice'])
            ->whereIn('apartment_id', $apartmentIds)
            ->orderByDesc('billing_year')
            ->orderByDesc('billing_month');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(15)->withQueryString();

        return view('resident.invoices.index', compact('invoices'));
    }

    /**
     * Thanh toán hoá đơn
     */
    public function pay($id)
    {
        $user = Auth::user();

        $apartmentIds = $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        $invoice = Invoice::whereIn('apartment_id', $apartmentIds)->findOrFail($id);

        if ($invoice->status === 'paid') {
            return back()->with('error', 'Hoá đơn này đã được thanh toán.');
        }

        // Cập nhật trạng thái hóa đơn
        $invoice->update([
            'status' => 'paid',
        ]);

        // Tạo bản ghi thanh toán tương ứng
        Payment::create([
            'bill_id'          => $invoice->id,
            'amount'           => $invoice->total_amount,
            'payment_method'   => 'bank_transfer',
            'transaction_code' => 'TX-' . $invoice->id . '-' . time(),
            'status'           => 'success',
            'paid_at'          => now(),
        ]);

        return back()->with('success', 'Thanh toán hoá đơn thành công.');
    }
}
