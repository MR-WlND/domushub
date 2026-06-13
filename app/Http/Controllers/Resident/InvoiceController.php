<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Hiển thị trang thanh toán hoá đơn (Tất cả hóa đơn chưa thanh toán/quá hạn)
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $apartmentIds = $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        $invoices = Invoice::with(['apartment.floor.block', 'details.servicePrice'])
            ->whereIn('apartment_id', $apartmentIds)
            ->whereIn('status', ['unpaid', 'overdue'])
            ->orderByDesc('billing_year')
            ->orderByDesc('billing_month')
            ->get();

        return view('resident.invoices.show', compact('invoices'));
    }

    /**
     * Lịch sử hoá đơn (đã thanh toán)
     */
    public function history(Request $request): View
    {
        $user = Auth::user();

        $apartmentIds = $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        $query = Invoice::with(['apartment.floor.block', 'details.servicePrice', 'payments'])
            ->whereIn('apartment_id', $apartmentIds)
            ->where('status', 'paid')
            ->orderByDesc('billing_year')
            ->orderByDesc('billing_month');

        $invoices = $query->paginate(15)->withQueryString();

        return view('resident.invoices.history', compact('invoices'));
    }

    /**
     * Xem chi tiết một hoá đơn
     */
    public function show($id)
    {
        $user = Auth::user();

        $apartmentIds = $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        $invoice = Invoice::with(['apartment.floor.block', 'details.servicePrice', 'payments'])
            ->whereIn('apartment_id', $apartmentIds)
            ->findOrFail($id);

        return view('resident.invoices.detail', compact('invoice'));
    }

    /**
     * Thanh toán nhiều hoá đơn cùng lúc – tạo URL chuyển hướng tới VNPay
     */
    public function pay(Request $request)
    {
        $user = Auth::user();

        $apartmentIds = $user->residents()
            ->whereNull('deleted_at')
            ->pluck('apartment_id')
            ->toArray();

        $invoiceIds = $request->input('invoice_ids', []);
        if (empty($invoiceIds)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một hoá đơn để thanh toán.');
        }

        $invoices = Invoice::whereIn('apartment_id', $apartmentIds)
            ->whereIn('id', $invoiceIds)
            ->whereIn('status', ['unpaid', 'overdue'])
            ->get();

        if ($invoices->isEmpty()) {
            return back()->with('error', 'Không tìm thấy hoá đơn hợp lệ để thanh toán.');
        }

        $totalAmount  = $invoices->sum('total_amount');
        $invoiceIdStr = $invoices->pluck('id')->implode('-');

        $vnp_TmnCode   = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url       = config('services.vnpay.url');
        $vnp_Returnurl = route('resident.invoices.vnpay-return');
        $vnp_IpnUrl    = route('vnpay.ipn'); // IPN – xử lý server-to-server
        $vnp_TxnRef    = $invoiceIdStr . 'T' . time(); // "1-2-3T1749780512"
        $invoiceCodes  = $invoices->map(fn($invoice) => $invoice->invoice_code)->implode(', ');
        $vnp_OrderInfo = 'Thanh toan hoa don ' . $invoiceCodes;
        $vnp_OrderType = 'other';
        $vnp_Amount    = $totalAmount * 100;
        $vnp_Locale    = 'vn';
        $vnp_IpAddr    = request()->ip();

        $inputData = [
            'vnp_Version'   => '2.1.0',
            'vnp_TmnCode'   => $vnp_TmnCode,
            'vnp_Amount'    => $vnp_Amount,
            'vnp_Command'   => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode'  => 'VND',
            'vnp_IpAddr'    => $vnp_IpAddr,
            'vnp_Locale'    => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_TxnRef'    => $vnp_TxnRef,
        ];

        ksort($inputData);
        $hashdata = '';
        $query    = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . '=' . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . '?' . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnp_Url);
    }

    // =========================================================================
    // IPN – Xử lý thông báo ngầm Server-to-Server từ VNPay
    // Không yêu cầu đăng nhập, VNPay gọi trực tiếp vào endpoint này.
    // =========================================================================
    public function vnpayIpn(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');

        // Tách tất cả tham số vnp_*
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'vnp_')) {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
        ksort($inputData);

        $i        = 0;
        $hashData = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // 1. Kiểm tra chữ ký bảo mật
        if ($secureHash !== $vnp_SecureHash) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid Checksum']);
        }

        // 2. Kiểm tra giao dịch thành công
        if ($request->vnp_ResponseCode !== '00') {
            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success (payment failed on gateway)']);
        }

        // 3. Parse invoice IDs và cập nhật DB (nếu chưa)
        $this->handlePaymentSuccess($request);

        // 7. Trả về JSON để VNPay biết IPN đã được xử lý thành công
        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }

    // =========================================================================
    // Return URL – Chỉ hiển thị thông báo kết quả giao dịch cho cư dân
    // Không cập nhật DB ở đây nữa (IPN đã làm rồi)
    // =========================================================================
    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');

        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'vnp_')) {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);
        ksort($inputData);

        $i        = 0;
        $hashData = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . '=' . urlencode($value);
            } else {
                $hashData .= urlencode($key) . '=' . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash !== $vnp_SecureHash) {
            return redirect()->route('resident.invoices.index')
                ->with('error', 'Chữ ký bảo mật không hợp lệ.');
        }

        if ($request->vnp_ResponseCode === '00') {
            // Thành công – IPN có thể chưa chạy (đặc biệt khi test localhost), 
            // nên ta gọi luôn hàm cập nhật ở đây như một fallback an toàn.
            $this->handlePaymentSuccess($request);

            $txnRef   = $request->vnp_TxnRef ?? '';
            $idsPart  = explode('T', $txnRef)[0];
            $count    = count(explode('-', $idsPart));

            return redirect()->route('resident.invoices.history')
                ->with('success', "Thanh toán {$count} hoá đơn thành công qua VNPay.");
        }

        return redirect()->route('resident.invoices.index')
            ->with('error', 'Giao dịch thanh toán không thành công. Vui lòng thử lại.');
    }

    /**
     * Xử lý cập nhật CSDL khi thanh toán thành công (Dùng chung cho IPN và Return)
     */
    private function handlePaymentSuccess(Request $request)
    {
        $txnRef  = $request->vnp_TxnRef ?? '';
        $idsPart = explode('T', $txnRef)[0];
        $invoiceIds = array_map('intval', explode('-', $idsPart));

        $invoices = Invoice::whereIn('id', $invoiceIds)
            ->where('status', '!=', 'paid')
            ->get();

        if ($invoices->isEmpty()) {
            return;
        }

        $cardType   = $request->vnp_CardType   ?? '';
        $bankCode   = $request->vnp_BankCode   ?? '';
        $txnNo      = $request->vnp_TransactionNo ?? null;
        $payDateStr = $request->vnp_PayDate;

        $paidAt = now();
        if ($payDateStr) {
            try {
                $paidAt = Carbon::createFromFormat('YmdHis', $payDateStr, 'Asia/Ho_Chi_Minh');
            } catch (\Exception $e) {}
        }

        $fullTxnCode = implode('|', [$txnNo ?? '', '', $cardType, $bankCode]);

        DB::transaction(function () use ($invoices, $txnNo, $fullTxnCode, $paidAt) {
            foreach ($invoices as $invoice) {
                $invoice->update(['status' => 'paid']);

                Payment::create([
                    'bill_id'          => $invoice->id,
                    'amount'           => $invoice->total_amount,
                    'payment_method'   => 'vnpay',
                    'transaction_code' => $fullTxnCode . '|' . $invoice->id,
                    'vnp_txn_ref'      => $txnNo,
                    'status'           => 'success',
                    'paid_at'          => $paidAt,
                ]);
            }
        });

        // Gửi thông báo cho quản trị viên/kế toán khi cư dân thanh toán thành công
        try {
            $admins = \App\Models\User::whereIn('role', ['staff', 'manager', 'admin'])->get();
            if ($invoices->count() === 1) {
                $invoice = $invoices->first();
                $notificationData = [
                    'title' => 'Hoá đơn đã được thanh toán',
                    'message' => "Cư dân đã thanh toán hoá đơn <strong>{$invoice->invoice_code}</strong> số tiền <strong>" . number_format($invoice->total_amount) . "đ</strong> qua VNPay.",
                    'url' => route('admin.invoices.show', $invoice->id),
                    'type' => 'payment',
                ];
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\InvoicePaidNotification($notificationData));
                }
            } else {
                $invoiceCodes = $invoices->map(fn($inv) => $inv->invoice_code)->implode(', ');
                $totalAmount = $invoices->sum('total_amount');
                $notificationData = [
                    'title' => 'Nhiều hoá đơn đã được thanh toán',
                    'message' => "Cư dân đã thanh toán <strong>{$invoices->count()}</strong> hoá đơn ({$invoiceCodes}) tổng số tiền <strong>" . number_format($totalAmount) . "đ</strong> qua VNPay.",
                    'url' => route('admin.invoices.index'),
                    'type' => 'payment_batch',
                ];
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\InvoicePaidNotification($notificationData));
                }
            }
        } catch (\Exception $e) {
            // Tránh làm gián đoạn luồng thanh toán nếu xảy ra lỗi gửi thông báo
            logger()->error('Lỗi khi gửi thông báo thanh toán cho admin: ' . $e->getMessage());
        }
    }
}
