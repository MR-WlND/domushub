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

        // Query các căn hộ có hóa đơn chưa thanh toán / thanh toán 1 phần
        $query = Apartment::with([
            'floor.block',
            'ownerResident.user',
            'invoices' => function ($invQ) {
                $invQ->whereIn('status', ['unpaid', 'partial_paid'])->orderBy('due_date', 'asc');
            },
            'invoices.details.servicePrice'
        ])
        ->whereHas('invoices', function ($invQ) {
            $invQ->whereIn('status', ['unpaid', 'partial_paid']);
        });

        // Nếu có từ khóa tìm kiếm, lọc theo mã căn hộ hoặc chủ hộ
        if (!empty($q)) {
            $query->where(function ($subQ) use ($q) {
                $subQ->where('apartment_number', 'like', "%{$q}%")
                    ->orWhereHas('ownerResident.user', function ($uQ) use ($q) {
                        $uQ->where('name', 'like', "%{$q}%")
                           ->orWhere('phone', 'like', "%{$q}%")
                           ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        $apartments = $query->orderBy('apartment_number', 'asc')->get();

        if (!empty($q) && $apartments->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy căn hộ hoặc chủ hộ nào có hóa đơn chưa thanh toán.']);
        }

        $result = $apartments->map(function ($apt) {
            $ownerUser = optional($apt->ownerResident)->user;
            $unpaidInvoices = $apt->invoices->map(function ($invoice) use ($apt, $ownerUser) {
                $isOverdue = $invoice->due_date && now()->isAfter($invoice->due_date);
                $totalDue = (float) ($invoice->total_due_at_issue > 0 ? $invoice->total_due_at_issue : $invoice->total_amount);
                $remaining = max(0, $totalDue - (float) $invoice->paid_amount);

                $serviceNames = $invoice->details
                    ->map(fn($d) => optional($d->servicePrice)->name ?? $d->note)
                    ->filter()->unique()->implode(', ');

                $description = !empty($serviceNames) ? $serviceNames : ($invoice->title ?? 'Hóa đơn dịch vụ');

                $statusLabel = match ($invoice->status) {
                    'partial_paid' => 'Thanh toán 1 phần',
                    default => ($isOverdue ? 'Quá hạn' : 'Chưa thanh toán'),
                };

                return [
                    'id'               => $invoice->id,
                    'apartment_code'   => $apt->apartment_code,
                    'owner_name'       => $ownerUser->name ?? $apt->owner_name ?? '---',
                    'invoice_code'     => $invoice->invoice_code,
                    'billing_month'    => $invoice->billing_month instanceof \Carbon\Carbon
                        ? $invoice->billing_month->format('m')
                        : $invoice->getRawOriginal('billing_month'),
                    'billing_year'     => $invoice->billing_year,
                    'due_date'         => $invoice->due_date ? $invoice->due_date->format('d/m/Y') : null,
                    'total_amount'     => $remaining,
                    'status'           => $invoice->status === 'partial_paid' ? 'partial_paid' : ($isOverdue ? 'overdue' : 'unpaid'),
                    'status_label'     => $statusLabel,
                    'description'      => $description,
                    'show_url'         => portal_route('invoices.show', $invoice->id),
                ];
            });

            $totalDebt = $unpaidInvoices->sum('total_amount');
            $blockName = optional(optional($apt->floor)->block)->name;
            $floorName = optional($apt->floor)->name;

            return [
                'apartment_id'     => $apt->id,
                'apartment_number' => $apt->apartment_number,
                'apartment_code'   => $apt->apartment_code,
                'block_name'       => $blockName ? (\Illuminate\Support\Str::startsWith($blockName, 'Tòa') ? $blockName : 'Tòa ' . $blockName) : '',
                'floor_name'       => $floorName ? (\Illuminate\Support\Str::startsWith($floorName, 'Tầng') ? $floorName : 'Tầng ' . $floorName) : '',
                'owner_name'       => $ownerUser->name ?? $apt->owner_name ?? '---',
                'owner_phone'      => $ownerUser->phone ?? '---',
                'owner_email'      => $ownerUser->email ?? '---',
                'owner_avatar'     => $ownerUser && $ownerUser->avatar ? asset('storage/' . $ownerUser->avatar) : null,
                'unpaid_count'     => $unpaidInvoices->count(),
                'total_debt'       => $totalDebt,
                'apartment_url'    => portal_route('invoices.apartment', $apt->id),
                'invoices'         => $unpaidInvoices->values()->toArray(),
            ];
        });

        return response()->json([
            'success'    => true,
            'is_grouped' => true,
            'apartments' => $result,
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
            'proof_image'      => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'invoice_ids.required'   => 'Vui lòng chọn ít nhất một hóa đơn.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'amount_received.required' => 'Vui lòng nhập số tiền thực thu.',
            'amount_received.min'    => 'Số tiền phải lớn hơn 0.',
            'proof_image.image'      => 'File tải lên phải là hình ảnh.',
            'proof_image.max'        => 'Dung lượng ảnh tối đa là 5MB.',
        ]);

        $lastPaymentId = null;

        // Lưu ảnh minh chứng nếu có
        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('payments/proofs', 'public');
        }

        try {
            DB::transaction(function () use ($request, &$lastPaymentId, $proofPath) {
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

                    $payment = Payment::create([
                        'bill_id'        => $invoice->id,
                        'amount'         => $payForThisBill,
                        'payment_method' => $paymentMethod,
                        'status'         => 'success',
                        'paid_at'        => $paymentDate,
                        'note'           => $note,
                        'proof_image'    => $proofPath,
                        'recorded_by'    => auth()->id(),
                    ]);

                    $lastPaymentId = $payment->id;

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

            $redirect = redirect()->to(portal_route('manual-payment.index'))
                ->with('success', 'Xác nhận thanh toán thành công!');

            if ($request->get('action') === 'print' && $lastPaymentId) {
                $redirect->with('print_receipt_url', portal_route('payments.receipt', $lastPaymentId));
            }

            return $redirect;
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
