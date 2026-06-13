@extends('layouts.admin.master')

@section('page_title', 'Chi tiết hóa đơn')

@section('content')
<div class="inv-admin">
    <div class="inv-admin__header" style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p class="inv-admin__eyebrow">Hóa đơn {{ $invoice->invoice_code }}</p>
            <h1 class="inv-admin__title">{{ $invoice->title }}</h1>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="inv-admin__btn inv-admin__btn--ghost">Quay lại danh sách</a>
    </div>

    <div class="inv-admin__card" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 24px; border-bottom: 1px solid #e2e8f0; padding-bottom: 20px;">
            <div>
                <h3 style="margin: 0 0 8px 0;">Thông tin căn hộ</h3>
                <p style="margin: 0; color: #475569;">Căn hộ: {{ $invoice->apartment->apartment_number ?? '—' }}</p>
                <p style="margin: 0; color: #475569;">Tòa: {{ optional(optional($invoice->apartment)->floor)->block->name ?? '' }}</p>
            </div>
            <div style="text-align: right;">
                <h3 style="margin: 0 0 8px 0;">Trạng thái thanh toán</h3>
                @if($invoice->status === 'paid')
                    <span class="inv-admin__badge inv-admin__badge--paid" style="font-size: 0.9rem; padding: 6px 12px;">Đã thanh toán</span>
                @elseif($invoice->status === 'overdue')
                    <span class="inv-admin__badge inv-admin__badge--overdue" style="font-size: 0.9rem; padding: 6px 12px;">Quá hạn</span>
                @else
                    <span class="inv-admin__badge inv-admin__badge--unpaid" style="font-size: 0.9rem; padding: 6px 12px;">Chưa thanh toán</span>
                @endif
            </div>
        </div>

        <div style="display: flex; gap: 40px; margin-bottom: 30px;">
            <div>
                <strong style="color: #64748b; font-size: 0.85rem; display: block; margin-bottom: 4px;">Kỳ thanh toán</strong>
                <span>Tháng {{ str_pad($invoice->billing_month, 2, '0', STR_PAD_LEFT) }}/{{ $invoice->billing_year }}</span>
            </div>
            <div>
                <strong style="color: #64748b; font-size: 0.85rem; display: block; margin-bottom: 4px;">Ngày phát hành</strong>
                <span>{{ $invoice->created_at->format('d/m/Y') }}</span>
            </div>
            <div>
                <strong style="color: #64748b; font-size: 0.85rem; display: block; margin-bottom: 4px;">Hạn thanh toán</strong>
                <span>{{ $invoice->due_date->format('d/m/Y') }}</span>
            </div>
        </div>

        <h3 style="margin-top: 0;">Chi tiết khoản phí</h3>
        <table class="inv-admin__table" style="margin-bottom: 30px; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
            <thead style="background: #f8fafc;">
                <tr>
                    <th>Tên khoản phí</th>
                    <th style="text-align: right;">Số lượng</th>
                    <th style="text-align: right;">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->details as $detail)
                    <tr>
                        <td>
                            <div style="font-weight: 500;">{{ $detail->servicePrice->name ?? 'Phí khác' }}</div>
                            <div style="font-size: 0.8rem; color: #64748b;">Đơn giá: {{ number_format($detail->servicePrice->price ?? $detail->amount) }}đ</div>
                        </td>
                        <td style="text-align: right;">{{ $detail->quantity }}</td>
                        <td style="text-align: right; font-weight: 500;">{{ number_format($detail->amount) }}đ</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; color: #94a3b8; padding: 20px;">Không có chi tiết.</td>
                    </tr>
                @endforelse
                <tr style="background: #f8fafc;">
                    <td colspan="2" style="text-align: right; font-weight: 600; font-size: 1.1rem; padding: 15px;">Tổng cộng</td>
                    <td style="text-align: right; font-weight: 700; font-size: 1.2rem; color: #2563eb; padding: 15px;">{{ number_format($invoice->total_amount) }}đ</td>
                </tr>
            </tbody>
        </table>

        @if($invoice->status === 'paid' && $invoice->payments->isNotEmpty())
            @php $payment = $invoice->payments->first(); @endphp
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                <h3 style="margin: 0 0 15px 0; font-size: 1.1rem;">Thông tin giao dịch</h3>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                    <div>
                        <strong style="color: #64748b; font-size: 0.85rem;">Thời gian thanh toán:</strong>
                        <div style="margin-top: 4px;">{{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i:s') : '—' }}</div>
                    </div>
                    <div>
                        <strong style="color: #64748b; font-size: 0.85rem;">Phương thức:</strong>
                        <div style="margin-top: 4px;">{{ $payment->payment_method === 'vnpay' ? 'VNPay' : ($payment->payment_method === 'cash' ? 'Tiền mặt' : 'Chuyển khoản') }}</div>
                    </div>
                    @if($payment->payment_method === 'vnpay')
                    <div>
                        <strong style="color: #64748b; font-size: 0.85rem;">Mã GD đối soát:</strong>
                        <div style="margin-top: 4px; font-family: monospace; font-weight: 600; color: #0f172a;">{{ $payment->vnp_txn_ref ?: explode('|', $payment->transaction_code)[0] }}</div>
                    </div>
                    @endif
                </div>
            </div>
        @elseif($invoice->status !== 'paid')
            <div style="margin-top: 20px; text-align: right;">
                <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}" onsubmit="return confirm('Xác nhận đã thu tiền mặt cho hóa đơn này?')">
                    @csrf @method('PATCH')
                    <input type="hidden" name="payment_method" value="cash">
                    <button type="submit" class="inv-admin__btn inv-admin__btn--success" style="font-size: 1rem; padding: 10px 24px;">Đánh dấu đã thu (Tiền mặt)</button>
                </form>
            </div>
        @endif
    </div>
</div>

<style>
.inv-admin { max-width: 1100px; margin: 0 auto; padding: 24px 20px; }
.inv-admin__eyebrow { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin: 0 0 4px; font-weight: 600; }
.inv-admin__title { font-size: 1.6rem; font-weight: 700; color: #0f172a; margin: 0; }
.inv-admin__btn { padding: 7px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; }
.inv-admin__btn--ghost { background: none; color: #475569; border: 1px solid #cbd5e1; }
.inv-admin__btn--ghost:hover { background: #f1f5f9; }
.inv-admin__btn--success { background: #16a34a; color: #fff; }
.inv-admin__btn--success:hover { background: #15803d; }
.inv-admin__card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; }
.inv-admin__table { width: 100%; border-collapse: collapse; }
.inv-admin__table th { text-align: left; padding: 10px 14px; font-size: 0.85rem; color: #64748b; border-bottom: 1px solid #e2e8f0; }
.inv-admin__table td { padding: 12px 14px; font-size: 0.9rem; border-bottom: 1px solid #f1f5f9; }
.inv-admin__badge { font-size: 0.72rem; font-weight: 600; padding: 3px 8px; border-radius: 4px; }
.inv-admin__badge--paid { background: #dcfce7; color: #15803d; }
.inv-admin__badge--unpaid { background: #fef3c7; color: #b45309; }
.inv-admin__badge--overdue { background: #fee2e2; color: #b91c1c; }
</style>
@endsection
