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

        $apartmentIds = $user->getApartmentIds();

        // Fallback: nếu user có apartment_id nhưng không có record residents
        if (empty($apartmentIds) && $user->apartment_id) {
            $apartmentIds = [$user->apartment_id];
        }

        $invoices = Invoice::with(['apartment.floor.block', 'details.servicePrice'])
            ->whereIn('apartment_id', $apartmentIds)
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
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

        $apartmentIds = $user->getApartmentIds();

        if (empty($apartmentIds) && $user->apartment_id) {
            $apartmentIds = [$user->apartment_id];
        }

        $query = Invoice::with(['apartment.floor.block', 'details.servicePrice', 'payments'])
            ->whereIn('apartment_id', $apartmentIds)
            ->where('status', 'paid')
            ->orderByDesc('billing_year')
            ->orderByDesc('billing_month');

        $invoices = $query->paginate(15)->withQueryString();

        // Lấy dữ liệu lượng nước tiêu thụ thật từ database (tối đa 12 tháng gần nhất có dữ liệu chốt)
        $meters = \App\Models\UtilityMeter::whereIn('apartment_id', $apartmentIds)
            ->where('type', 'water')
            ->where('status', 'approved')
            ->orderBy('record_year', 'asc')
            ->orderBy('record_month', 'asc')
            ->take(12)
            ->get();

        $waterChartLabels = [];
        $waterChartData = [];
        
        foreach ($meters as $meter) {
            $waterChartLabels[] = 'Tháng ' . str_pad($meter->record_month, 2, '0', STR_PAD_LEFT) . '/' . $meter->record_year;
            $waterChartData[] = (float)$meter->usage_amount;
        }

        return view('resident.invoices.history', compact('invoices', 'waterChartLabels', 'waterChartData'));
    }

    /**
     * Xem chi tiết một hoá đơn
     */
    public function show($id)
    {
        $user = Auth::user();

        $apartmentIds = $user->getApartmentIds();

        if (empty($apartmentIds) && $user->apartment_id) {
            $apartmentIds = [$user->apartment_id];
        }

        $invoice = Invoice::with(['apartment.floor.block', 'details.servicePrice', 'payments'])
            ->whereIn('apartment_id', $apartmentIds)
            ->findOrFail($id);

        $invoice->recalculateDetailsStatus();

        return view('resident.invoices.detail', compact('invoice'));
    }

    /**
     * Thanh toán nhiều hoá đơn cùng lúc – tạo URL chuyển hướng tới VNPay
     */
    public function pay(Request $request)
    {
        $user = Auth::user();

        $apartmentIds = $user->getApartmentIds();

        if (empty($apartmentIds) && $user->apartment_id) {
            $apartmentIds = [$user->apartment_id];
        }

        $invoiceIds = $request->input('invoice_ids', []);
        if (empty($invoiceIds)) {
            return back()->with('error', 'Vui lòng chọn ít nhất một hoá đơn để thanh toán.');
        }

        $invoices = Invoice::whereIn('apartment_id', $apartmentIds)
            ->whereIn('id', $invoiceIds)
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
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

    public function payDetails(Request $request)
    {
        $user = Auth::user();
        $apartmentIds = $user->getApartmentIds();

        if (empty($apartmentIds) && $user->apartment_id) {
            $apartmentIds = [$user->apartment_id];
        }

        $invoiceId = $request->input('invoice_id');
        $detailIds = $request->input('detail_ids', []);

        if (empty($detailIds) || !$invoiceId) {
            return back()->with('error', 'Vui lòng chọn ít nhất một khoản phí để thanh toán.');
        }

        $invoice = Invoice::whereIn('apartment_id', $apartmentIds)
            ->where('id', $invoiceId)
            ->whereIn('status', ['unpaid', 'partial', 'overdue'])
            ->first();

        if (!$invoice) {
            return back()->with('error', 'Không tìm thấy hoá đơn hợp lệ.');
        }

        $details = $invoice->details()->whereIn('id', $detailIds)->whereNull('payment_id')->get();
        if ($details->isEmpty()) {
            return back()->with('error', 'Các khoản phí đã được thanh toán hoặc không tồn tại.');
        }

        $totalAmount = $details->sum('amount');
        if ($totalAmount <= 0) {
            return back()->with('error', 'Số tiền thanh toán phải lớn hơn 0.');
        }

        $vnp_TmnCode   = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $vnp_Url       = config('services.vnpay.url');
        $vnp_Returnurl = route('resident.invoices.vnpay-return');
        $vnp_IpnUrl    = route('vnpay.ipn');
        
        // Format: {invoice_id}D{detail_id_1}-{detail_id_2}T{time}
        $vnp_TxnRef    = $invoice->id . 'D' . $details->pluck('id')->implode('-') . 'T' . time();
        $vnp_OrderInfo = 'Thanh toan phi HD ' . $invoice->invoice_code;
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
        
        $isDetailPayment = false;
        $detailIds = [];
        if (str_contains($idsPart, 'D')) {
            $isDetailPayment = true;
            $parts = explode('D', $idsPart);
            $invoiceIds = [(int)$parts[0]];
            $detailIds = array_map('intval', explode('-', $parts[1]));
        } else {
            $invoiceIds = array_map('intval', explode('-', $idsPart));
        }

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

        DB::transaction(function () use ($invoices, $txnNo, $fullTxnCode, $paidAt, $isDetailPayment, $detailIds) {
            foreach ($invoices as $invoice) {
                if ($isDetailPayment) {
                    $details = $invoice->details()->whereIn('id', $detailIds)->whereNull('payment_id')->get();
                    if ($details->isEmpty()) continue;
                    
                    $amount = $details->sum('amount');
                    $newPaidAmount = $invoice->paid_amount + $amount;
                    
                    $status = ($newPaidAmount >= (float) $invoice->total_amount - 0.001) ? 'paid' : 'partial_paid';
                    
                    $invoice->update([
                        'paid_amount' => $newPaidAmount,
                        'status'      => $status
                    ]);
                    
                    $payment = Payment::create([
                        'bill_id'          => $invoice->id,
                        'amount'           => $amount,
                        'payment_method'   => 'vnpay',
                        'transaction_code' => $fullTxnCode . '|' . $invoice->id . '|D',
                        'vnp_txn_ref'      => $txnNo,
                        'status'           => 'success',
                        'paid_at'          => $paidAt,
                        'payer_name'       => auth()->user()?->name ?? ($invoice->apartment->owner_name ?? 'Cư dân'),
                        'note'             => 'Thanh toán các khoản phí: ' . implode(', ', $details->pluck('servicePrice.name')->toArray())
                    ]);
                    
                    foreach ($details as $d) {
                        $d->update(['payment_id' => $payment->id, 'status' => 'paid']);
                    }
                    
                    $invoice->recalculateDetailsStatus();
                } else {
                    // Cập nhật trạng thái và số tiền đã thanh toán (Toàn bộ)
                    $invoice->update([
                        'status'      => 'paid',
                        'paid_amount' => $invoice->total_amount,
                    ]);

                    $invoice->recalculateDetailsStatus();

                    Payment::create([
                        'bill_id'          => $invoice->id,
                        'amount'           => $invoice->total_amount,
                        'payment_method'   => 'vnpay',
                        'transaction_code' => $fullTxnCode . '|' . $invoice->id,
                        'vnp_txn_ref'      => $txnNo,
                        'status'           => 'success',
                        'paid_at'          => $paidAt,
                        'payer_name'       => auth()->user()?->name ?? ($invoice->apartment->owner_name ?? 'Cư dân'),
                    ]);
                }
            }
        });

        // ─── Khôi phục xe pending_renewal → active nếu đây là hóa đơn phí gửi xe ───
        $this->restoreVehiclesAfterParkingPayment($invoices);

        // ─── Cập nhật trạng thái thanh toán cho lịch đặt tiện ích liên kết ───
        foreach ($invoices as $invoice) {
            $booking = $invoice->facilityBooking;
            if ($booking && $booking->payment_status !== 'paid' && $invoice->status === 'paid') {
                $booking->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'vnpay',
                ]);
            }
        }

        // Gửi thông báo cho quản trị viên/kế toán khi cư dân thanh toán thành công
        try {
            $admins = \App\Models\User::whereIn('role', ['staff', 'manager', 'admin'])->get();
            if ($invoices->count() === 1) {
                $invoice = $invoices->first();
                $notificationData = [
                    'title' => 'Hoá đơn đã được thanh toán',
                    'message' => "Cư dân đã thanh toán " . ($isDetailPayment ? "một phần" : "") . " hoá đơn <strong>{$invoice->invoice_code}</strong> qua VNPay.",
                    'url' => route('admin.invoices.show', $invoice->id),
                    'type' => 'payment',
                ];
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\InvoicePaidNotification($notificationData));
                }
            } else {
                $invoiceCodes = $invoices->map(fn($inv) => $inv->invoice_code)->implode(', ');
                $notificationData = [
                    'title' => 'Nhiều hoá đơn đã được thanh toán',
                    'message' => "Cư dân đã thanh toán <strong>{$invoices->count()}</strong> hoá đơn ({$invoiceCodes}) qua VNPay.",
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

    /**
     * Khôi phục xe từ pending_renewal → active khi thanh toán hóa đơn phí gửi xe thành công.
     * Đồng thời sinh QR code nếu xe chưa có.
     */
    private function restoreVehiclesAfterParkingPayment($invoices)
    {
        try {
            // Lọc hóa đơn có chứa chi tiết phí gửi xe (service_price type = 'parking')
            $parkingApartmentIds = [];

            foreach ($invoices as $invoice) {
                $hasParkingDetail = $invoice->details()
                    ->whereHas('servicePrice', function ($q) {
                        $q->whereIn('type', ['motorbike', 'car', 'bicycle', 'electric_bike']);
                    })
                    ->exists();

                if ($hasParkingDetail) {
                    $parkingApartmentIds[] = $invoice->apartment_id;
                }
            }

            if (empty($parkingApartmentIds)) {
                return;
            }

            // Tìm xe pending_renewal thuộc các căn hộ đã thanh toán phí gửi xe
            $vehicles = \App\Models\Vehicle::whereIn('apartment_id', $parkingApartmentIds)
                ->where('status', 'pending_renewal')
                ->withoutTrashed()
                ->get();

            foreach ($vehicles as $vehicle) {
                $vehicle->update(['status' => 'active']);

                // Sinh QR code nếu chưa có
                if (empty($vehicle->qr_code)) {
                    $this->generateVehicleQr($vehicle);
                }
            }
        } catch (\Exception $e) {
            logger()->error('Lỗi khôi phục xe sau thanh toán phí gửi xe: ' . $e->getMessage());
        }
    }

    /**
     * Sinh QR image cho xe và lưu path vào cột qr_code.
     * Content: biển số xe viết hoa, không dấu cách (dùng để bảo vệ quét).
     */
    private function generateVehicleQr(\App\Models\Vehicle $vehicle): void
    {
        try {
            $dir = storage_path('app/public/qr/vehicles');
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }

            $content  = strtoupper(str_replace([' ', '-'], '', $vehicle->license_plate));
            $filename = $content . '.svg';
            $filePath = $dir . '/' . $filename;

            if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size(300)
                    ->errorCorrection('H')
                    ->generate($content, $filePath);
            }

            $vehicle->update(['qr_code' => 'qr/vehicles/' . $filename]);
        } catch (\Throwable $e) {
            // Fallback: lưu content biển số để scanner vẫn hoạt động
            $content = strtoupper(str_replace([' ', '-'], '', $vehicle->license_plate));
            $vehicle->update(['qr_code' => $content]);
        }
    }

    /**
     * In biên lai thu tiền cho cư dân.
     */
    public function printReceipt(\App\Models\Payment $payment)
    {
        $payment->load(['invoice.apartment.floor.block', 'recorder']);
        
        $isAuthorized = false;
        
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'staff' || auth()->user()->role === 'manager') {
            $isAuthorized = true;
        } else {
            $apartmentIds = \Illuminate\Support\Facades\DB::table('residents')
                ->where('user_id', auth()->id())
                ->pluck('apartment_id')
                ->toArray();
                
            if (in_array($payment->invoice->apartment_id, $apartmentIds)) {
                $isAuthorized = true;
            }
        }
        
        if (!$isAuthorized) {
            abort(403, 'Bạn không có quyền truy cập biên lai này.');
        }
        
        return view('admin.invoices.receipt', compact('payment'));
    }

    /**
     * In hóa đơn cho cư dân (PDF/Print layout)
     */
    public function printInvoice($id)
    {
        $user = Auth::user();
        $apartmentIds = $user->getApartmentIds();

        if (empty($apartmentIds) && $user->apartment_id) {
            $apartmentIds = [$user->apartment_id];
        }

        $invoice = Invoice::with(['apartment.floor.block', 'details.servicePrice', 'payments'])
            ->whereIn('apartment_id', $apartmentIds)
            ->findOrFail($id);

        return view('admin.invoices.print', compact('invoice'));
    }

    /**
     * Tự động tạo phản ánh khiếu nại chỉ số nước.
     */
    public function complaintWater(Request $request, $id)
    {
        $user = Auth::user();
        $apartmentIds = $user->getApartmentIds();

        if (empty($apartmentIds) && $user->apartment_id) {
            $apartmentIds = [$user->apartment_id];
        }

        $invoice = Invoice::whereIn('apartment_id', $apartmentIds)->findOrFail($id);

        // Tìm chỉ số nước tương ứng
        $meter = \App\Models\UtilityMeter::where('apartment_id', $invoice->apartment_id)
            ->where('type', 'water')
            ->where('record_month', $invoice->billing_month->month)
            ->where('record_year', $invoice->billing_year)
            ->first();

        if (!$meter) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy dữ liệu chỉ số nước cho kỳ hóa đơn này.'
            ], 404);
        }

        // Tạo Ticket khiếu nại tự động
        $title = "Khiếu nại chỉ số nước - HĐ " . $invoice->invoice_code;
        $description = "Cư dân " . $user->name . " gửi khiếu nại về chỉ số nước của kỳ hóa đơn " . $invoice->invoice_code . ".\n"
                     . "- Chỉ số cũ: " . $meter->old_value . " m³\n"
                     . "- Chỉ số mới: " . $meter->new_value . " m³\n"
                     . "- Tiêu thụ: " . $meter->usage_amount . " m³\n"
                     . "- Ngày chốt: " . ($meter->recorded_at ? $meter->recorded_at->format('d/m/Y') : '—') . "\n"
                     . "Cư dân phản ánh chỉ số nước không chính xác, vui lòng kiểm tra lại.";

        $ticket = \App\Models\Ticket::create([
            'apartment_id' => $invoice->apartment_id,
            'sender_id' => $user->id,
            'ticket_type' => 'complaint',
            'title' => $title,
            'description' => $description,
            'priority' => 'high',
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi khiếu nại thành công! Ban Quản Lý sẽ sớm liên hệ xử lý.'
        ]);
    }
}
