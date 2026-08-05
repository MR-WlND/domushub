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
        $apartment = Apartment::with(['ownerResident.user'])
            ->where(function ($query) use ($q) {
                $query->where('apartment_number', 'like', "%{$q}%")
                    ->orWhereHas('ownerResident.user', function ($sub) use ($q) {
                        $sub->where('name', 'like', "%{$q}%")
                            ->orWhere('phone', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            })
            ->first();


        if (!$apartment) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy căn hộ hoặc chủ hộ phù hợp.']);
        }

        // Lấy danh sách hóa đơn chưa thanh toán hoặc thanh toán một phần của căn hộ
        $invoices = Invoice::with(['details.servicePrice'])
            ->where('apartment_id', $apartment->id)
            ->whereIn('status', ['unpaid', 'partial_paid'])
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($invoice) {
                $isOverdue = $invoice->due_date && now()->isAfter($invoice->due_date);

                $serviceNames = $invoice->details
                    ->map(function ($detail) {
                        return optional($detail->servicePrice)->name ?? $detail->note;
                    })
                    ->filter()
                    ->unique()
                    ->implode(', ');

                $description = !empty($serviceNames) ? $serviceNames : ($invoice->title ?? 'Hóa đơn dịch vụ');

                $totalDue = (float) ($invoice->total_due_at_issue > 0 ? $invoice->total_due_at_issue : $invoice->total_amount);
                $remainingAmount = max(0, $totalDue - (float) $invoice->paid_amount);

                $statusLabel = match ($invoice->status) {
                    'partial_paid' => 'Thanh toán 1 phần',
                    default => ($isOverdue ? 'Quá hạn' : 'Chưa thanh toán'),
                };
                if ($isOverdue && $invoice->status !== 'paid') {
                    $statusLabel = 'Quá hạn';
                }

                return [
                    'id'            => $invoice->id,
                    'invoice_code'  => $invoice->invoice_code,
                    'billing_month' => $invoice->billing_month instanceof \Carbon\Carbon
                        ? $invoice->billing_month->format('m')
                        : $invoice->getRawOriginal('billing_month'),
                    'billing_year'  => $invoice->billing_year,
                    'due_date'      => $invoice->due_date ? $invoice->due_date->format('d/m/Y') : null,
                    'total_amount'  => $remainingAmount,
                    'status'        => $invoice->status === 'partial_paid' ? 'partial_paid' : ($isOverdue ? 'overdue' : 'unpaid'),
                    'status_label'  => $statusLabel,
                    'description'   => $description,
                    'show_url'      => portal_route('invoices.show', $invoice->id),
                ];
            });

        $ownerUser = optional($apartment->ownerResident)->user;

        return response()->json([
            'success'   => true,
            'apartment' => [
                'id'             => $apartment->id,
                'apartment_code' => $apartment->apartment_code,
            ],
            'owner' => $ownerUser ? [
                'name'   => $ownerUser->name,
                'phone'  => $ownerUser->phone,
                'email'  => $ownerUser->email,
                'avatar' => $ownerUser->avatar ? asset('storage/' . $ownerUser->avatar) : null,
            ] : null,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Xử lý thanh toán thủ công hàng loạt (Hỗ trợ nộp một phần tiền).
     */
    public function process(Request $request)
    {
        $request->validate([
            'invoice_ids'      => 'required|array|min:1',
            'invoice_ids.*'    => 'integer|exists:bills,id',
            'payment_method'   => 'required|in:cash,bank_transfer',
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
                $invoiceIds     = $request->invoice_ids;
                $paymentDate    = $request->payment_date ? now()->parse($request->payment_date) : now();
                $paymentMethod   = $request->payment_method;
                $note            = $request->note;
                $remainingToPay = (float) $request->amount_received;

                foreach ($invoiceIds as $invoiceId) {
                    if ($remainingToPay <= 0) {
                        break;
                    }

                    $invoice = Invoice::lockForUpdate()->findOrFail($invoiceId);

                    if (in_array($invoice->status, ['paid', 'cancelled'])) {
                        continue;
                    }

                    $totalDue    = (float) ($invoice->total_due_at_issue > 0 ? $invoice->total_due_at_issue : $invoice->total_amount);
                    $currentPaid = (float) $invoice->paid_amount;
                    $dueAmount   = max(0, $totalDue - $currentPaid);

                    if ($dueAmount <= 0) {
                        continue;
                    }

                    $payForThisBill = min($remainingToPay, $dueAmount);

                    Payment::create([
                        'invoice_id'     => $invoice->id,
                        'amount'         => $payForThisBill,
                        'payment_method' => $paymentMethod,
                        'paid_at'        => $paymentDate,
                        'note'           => $note,
                        'paid_by'        => auth()->id(),
                    ]);

                    $newPaidAmount = $currentPaid + $payForThisBill;
                    $newStatus     = ($newPaidAmount >= $totalDue - 0.01) ? 'paid' : 'partial_paid';

                    $invoice->update([
                        'paid_amount' => $newPaidAmount,
                        'status'      => $newStatus,
                        'paid_at'     => $newStatus === 'paid' ? $paymentDate : $invoice->paid_at,
                    ]);

                    $invoice->recalculateDetailsStatus();

                    if ($newStatus === 'paid') {
                        $this->activateVehicleIfParkingFee($invoice);
                    }

                    $remainingToPay -= $payForThisBill;

                    SystemLogger::log(
                        'payment',
                        'Ghi nhận thanh toán thủ công hóa đơn #' . $invoice->invoice_code . ' (Gạch nợ: ' . number_format($payForThisBill) . ' đ)',
                        ['invoice_id' => $invoice->id, 'amount' => $payForThisBill, 'method' => $paymentMethod],
                        auth()->id()
                    );
                }
            });

            return redirect()->to(portal_route('manual-payment.index'))
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
