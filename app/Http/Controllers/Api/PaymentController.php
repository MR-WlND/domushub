<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentGateway\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->middleware('auth:sanctum');
    }

    public function availableGateways(): JsonResponse
    {
        return response()->json([
            'success'  => true,
            'gateways' => $this->paymentService->getAvailableGateways(),
        ]);
    }

    public function generateQR(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'bill_id' => 'required|integer|exists:bills,id',
                'gateway' => 'required|in:mbbank,momo',
            ]);

            $user    = Auth::user();
            $invoice = Invoice::find($validated['bill_id']);

            $apartmentIds = $user->residents()->whereNull('deleted_at')->pluck('apartment_id')->toArray();

            if (!in_array($invoice->apartment_id, $apartmentIds)) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập hóa đơn này.'], 403);
            }

            $result = $this->paymentService->generatePaymentQR($invoice, $validated['gateway']);

            return response()->json($result, ($result['success'] ?? false) ? 200 : 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Payment QR Generation Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Lỗi khi tạo QR code.'], 500);
        }
    }

    public function checkStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'transaction_code' => 'required|string',
                'gateway'          => 'required|in:mbbank,momo',
            ]);

            $payment = Payment::where('transaction_code', $validated['transaction_code'])->first();

            if (!$payment) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy bản ghi thanh toán.'], 404);
            }

            $user         = Auth::user();
            $apartmentIds = $user->residents()->whereNull('deleted_at')->pluck('apartment_id')->toArray();

            if (!in_array($payment->invoice->apartment_id, $apartmentIds)) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.'], 403);
            }

            return response()->json(['success' => true, 'status' => $payment->status]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không hợp lệ.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Payment Status Check Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Lỗi khi kiểm tra trạng thái.'], 500);
        }
    }

    public function refund(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'transaction_code' => 'required|string',
                'gateway'          => 'required|in:mbbank,momo',
            ]);

            $payment = Payment::where('transaction_code', $validated['transaction_code'])->first();

            if (!$payment) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy bản ghi thanh toán.'], 404);
            }

            if (Auth::user()->role !== 'admin') {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền hoàn tiền.'], 403);
            }

            $result = $this->paymentService->refundPayment($validated['gateway'], $validated['transaction_code'], $payment->amount);

            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Payment Refund Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Lỗi khi hoàn tiền.'], 500);
        }
    }

    public function history(Request $request): JsonResponse
    {
        try {
            $user         = Auth::user();
            $apartmentIds = $user->residents()->whereNull('deleted_at')->pluck('apartment_id')->toArray();

            $payments = Payment::whereHas('invoice', fn($q) => $q->whereIn('apartment_id', $apartmentIds))
                ->with(['invoice'])
                ->orderByDesc('created_at')
                ->paginate(20);

            return response()->json(['success' => true, 'data' => $payments]);
        } catch (\Exception $e) {
            Log::error('Payment History Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Lỗi khi lấy lịch sử thanh toán.'], 500);
        }
    }
}
