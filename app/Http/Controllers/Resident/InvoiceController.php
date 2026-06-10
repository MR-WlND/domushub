<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentGateway\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function history(Request $request): View
    {
        $user = Auth::user();
        $apartmentIds = $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        $payments = Payment::with(['invoice'])
            ->whereHas('invoice', fn($q) => $q->whereIn('apartment_id', $apartmentIds))
            ->where('status', 'success')
            ->orderByDesc('paid_at')
            ->paginate(20)
            ->withQueryString();

        return view('resident.invoices.history', compact('payments'));
    }

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
     * Hiển thị trang chọn cổng thanh toán
     */
    public function showPaymentGateways($id): View
    {
        $user = Auth::user();

        $apartmentIds = $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        $invoice = Invoice::whereIn('apartment_id', $apartmentIds)->findOrFail($id);

        if ($invoice->status === 'paid') {
            return back()->with('error', 'Hóa đơn này đã được thanh toán.');
        }

        $gateways = $this->paymentService->getAvailableGateways();

        return view('resident.invoices.payment-gateways', compact('invoice', 'gateways'));
    }

    /**
     * Tạo QR thanh toán qua web session
     */
    public function checkPayment(Request $request, $id)
    {
        $user = Auth::user();
        $apartmentIds = $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        $invoice = Invoice::whereIn('apartment_id', $apartmentIds)->findOrFail($id);
        $request->validate(['transaction_code' => 'required|string']);

        $payment = Payment::where('transaction_code', $request->transaction_code)
            ->where('bill_id', $invoice->id)
            ->first();

        if (!$payment) {
            return response()->json(['status' => 'pending']);
        }

        // Nếu payment đã success (do Sepay webhook xác nhận), cập nhật hóa đơn
        if ($payment->status === 'success' && $invoice->status !== 'paid') {
            $invoice->update(['status' => 'paid']);
        }

        return response()->json(['status' => $payment->status]);
    }

    public function generateQR(Request $request, $id)
    {
        $user = Auth::user();
        $apartmentIds = $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        $invoice = Invoice::whereIn('apartment_id', $apartmentIds)->findOrFail($id);

        $request->validate(['gateway' => 'required|in:mbbank,momo']);

        $result = $this->paymentService->generatePaymentQR($invoice, $request->gateway);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Thanh toán hoá đơn (cách cũ - bank transfer)
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
