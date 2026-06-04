<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;

use App\Models\Invoice;
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

        $query = Invoice::with(['apartment.floor.block'])
            ->whereIn('apartment_id', $apartmentIds)
            ->orderByDesc('created_at');

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

        $invoice->update([
            'status'         => 'paid',
            'paid_at'        => now(),
            'payment_method' => 'transfer',
        ]);

        return back()->with('success', 'Thanh toán hoá đơn thành công.');
    }
}
