<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SystemLogger;
use App\Models\Invoice;
use App\Models\Apartment;
use App\Models\Payment;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManualPaymentController extends Controller
{
    /**
     * Hiển thị trang Thu tiền thủ công.
     */
    public function index()
    {
        return view('admin.manual-payment.index');
    }

    /**
     * AJAX: Tìm kiếm căn hộ/chủ hộ và trả về danh sách hóa đơn chưa thanh toán.
     */
    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (empty($q)) {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập từ khóa tìm kiếm.']);
        }

        // Tìm căn hộ theo mã hoặc chủ hộ theo tên/số điện thoại/email
        $apartment = Apartment::with(['owner'])
            ->where(function ($query) use ($q) {
                $query->where('apartment_code', 'like', "%{$q}%")
                    ->orWhereHas('owner', function ($sub) use ($q) {
                        $sub->where('full_name', 'like', "%{$q}%")
                            ->orWhere('phone', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            })
            ->first();

        if (!$apartment) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy căn hộ hoặc chủ hộ phù hợp.']);
        }

        // Lấy danh sách hóa đơn chưa thanh toán của căn hộ
        $invoices = Invoice::where('apartment_id', $apartment->id)
            ->where('status', 'unpaid')
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($invoice) {
                $isOverdue = $invoice->due_date && now()->isAfter($invoice->due_date);
                return [
                    'id'            => $invoice->id,
                    'invoice_code'  => $invoice->invoice_code,
                    'billing_month' => $invoice->billing_month,
                    'billing_year'  => $invoice->billing_year,
                    'due_date'      => $invoice->due_date ? $invoice->due_date->format('d/m/Y') : null,
                    'total_amount'  => $invoice->total_amount,
                    'status'        => $isOverdue ? 'overdue' : 'unpaid',
                    'status_label'  => $isOverdue ? 'Quá hạn' : 'Chưa thanh toán',
                    'description'   => optional($invoice->details->first())->description ?? 'Phí quản lý',
                ];
            });

        $owner = $apartment->owner;

        return response()->json([
            'success'   => true,
            'apartment' => [
                'id'             => $apartment->id,
                'apartment_code' => $apartment->apartment_code,
            ],
            'owner' => $owner ? [
                'name'   => $owner->full_name,
                'phone'  => $owner->phone,
                'email'  => $owner->email,
                'avatar' => $owner->avatar_url ?? null,
            ] : null,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Xử lý thanh toán thủ công hàng loạt.
     */
    public function process(Request $request)
    {
        $request->validate([
            'invoice_ids'      => 'required|array|min:1',
            'invoice_ids.*'    => 'integer|exists:invoices,id',
            'payment_method'   => 'required|in:cash,bank_transfer,card',
            'amount_received'  => 'required|numeric|min:1',
            'note'             => 'nullable|string|max:500',
            'payment_date'     => 'nullable|date',
        ], [
            'invoice_ids.required'   => 'Vui lòng chọn ít nhất một hóa đơn.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'amount_received.required' => 'Vui lòng nhập số tiền thực thu.',
            'amount_received.min'    => 'Số tiền phải lớn hơn 0.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $invoiceIds   = $request->invoice_ids;
                $paymentDate  = $request->payment_date ? now()->parse($request->payment_date) : now();
                $paymentMethod = $request->payment_method;
                $note          = $request->note;

                foreach ($invoiceIds as $invoiceId) {
                    $invoice = Invoice::lockForUpdate()->findOrFail($invoiceId);

                    if ($invoice->status !== 'unpaid') {
                        continue;
                    }

                    // Tạo bản ghi thanh toán
                    Payment::create([
                        'invoice_id'     => $invoice->id,
                        'amount'         => $invoice->total_amount,
                        'payment_method' => $paymentMethod,
                        'paid_at'        => $paymentDate,
                        'note'           => $note,
                        'paid_by'        => auth()->id(),
                    ]);

                    // Cập nhật trạng thái hóa đơn
                    $invoice->update([
                        'status'   => 'paid',
                        'paid_at'  => $paymentDate,
                    ]);

                    // Kích hoạt xe nếu là phí gửi xe
                    $this->activateVehicleIfParkingFee($invoice);

                    SystemLogger::log(
                        'payment',
                        'Ghi nhận thanh toán thủ công hóa đơn #' . $invoice->invoice_code,
                        ['invoice_id' => $invoice->id, 'method' => $paymentMethod],
                        auth()->id()
                    );
                }
            });

            return redirect()->route('manual-payment.index')
                ->with('success', 'Xác nhận thanh toán thành công!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Kích hoạt xe khi thanh toán phí gửi xe.
     */
    private function activateVehicleIfParkingFee(Invoice $invoice): void
    {
        $hasParkingFee = $invoice->details()
            ->whereHas('servicePrice', function ($q) {
                $q->where('name', 'like', '%xe%')
                    ->orWhere('name', 'like', '%parking%')
                    ->orWhere('name', 'like', '%gửi xe%');
            })
            ->exists();

        if ($hasParkingFee) {
            $vehicles = Vehicle::where('apartment_id', $invoice->apartment_id)
                ->where('status', 'awaiting_payment')
                ->get();

            foreach ($vehicles as $vehicle) {
                $vehicle->update(['status' => 'active']);
            }
        }
    }
}
