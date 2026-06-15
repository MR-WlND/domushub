@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Hóa đơn ' . $invoice->invoice_code)

@push('page_css')
@vite(['resources/css/admin/invoices.css'])
<style>
    .invoice-detail-card {
        background: #fff;
        border: 1px solid var(--border, #e2e8f0);
        border-radius: var(--radius-md, 8px);
        box-shadow: var(--shadow-sm, 0 1px 2px 0 rgba(0, 0, 0, 0.05));
        padding: 24px;
        margin-bottom: 24px;
    }
    .invoice-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 24px;
    }
    .info-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted, #64748b);
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    .info-value {
        font-size: 14.5px;
        font-weight: 600;
        color: var(--text-primary, #0f172a);
    }
    .payment-form-card {
        background: #f8fafc;
        border: 1px solid var(--border, #e2e8f0);
        border-radius: var(--radius-md, 8px);
        padding: 20px;
        margin-top: 20px;
    }

    /* Progress bar */
    .progress-wrap {
        background: #e5e7eb;
        border-radius: 99px;
        height: 10px;
        overflow: hidden;
        margin: 8px 0 4px;
    }
    .progress-bar {
        height: 100%;
        border-radius: 99px;
        background: linear-gradient(90deg, #16a34a, #4ade80);
        transition: width .4s ease;
    }
    .progress-bar.partial { background: linear-gradient(90deg, #d97706, #fbbf24); }

    /* Badge partial */
    .badge-partial {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    /* Refund modal */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal-box {
        background: #fff;
        border-radius: 12px;
        padding: 28px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        animation: slideUp .2s ease;
    }
    @keyframes slideUp {
        from { transform: translateY(16px); opacity: 0; }
        to   { transform: translateY(0);    opacity: 1; }
    }
    .modal-title {
        font-size: 16px;
        font-weight: 700;
        color: #dc2626;
        margin-bottom: 8px;
    }
    .modal-body { font-size: 13.5px; color: var(--text-secondary, #475569); margin-bottom: 16px; }
    .form-label-sm { font-size: 12px; font-weight: 600; color: var(--text-muted, #64748b); text-transform: uppercase; letter-spacing: .04em; display: block; margin-bottom: 4px; }

    /* Alert */
    .alert-success {
        background: #f0fdf4;
        border: 1px solid #86efac;
        color: #15803d;
        padding: 12px 18px;
        border-radius: var(--radius-sm, 4px);
        margin-bottom: 16px;
        font-size: 13.5px;
        font-weight: 500;
    }
    .alert-error {
        background: #fef2f2;
        border: 1px solid #fca5a5;
        color: #dc2626;
        padding: 12px 18px;
        border-radius: var(--radius-sm, 4px);
        margin-bottom: 16px;
        font-size: 13.5px;
        font-weight: 500;
    }

    .recorder-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 500;
        color: #1d4ed8;
        background: #eff6ff;
        border-radius: 99px;
        padding: 2px 8px;
    }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { text-align: left; padding: 10px 14px; font-size: 0.85rem; color: #64748b; border-bottom: 1px solid #e2e8f0; }
    .data-table td { padding: 12px 14px; font-size: 0.9rem; border-bottom: 1px solid #f1f5f9; }
    .btn { padding: 7px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; }
    .btn-outline { background: none; color: #475569; border: 1px solid #cbd5e1; }
    .btn-outline:hover { background: #f1f5f9; }
    .btn-success { background: #16a34a; color: #fff; }
    .btn-success:hover { background: #15803d; }
    .badge { font-size: 0.72rem; font-weight: 600; padding: 3px 8px; border-radius: 4px; }
    .badge-paid { background: #dcfce7; color: #15803d; }
    .badge-unpaid { background: #fef3c7; color: #b45309; }
    .badge-overdue { background: #fee2e2; color: #b91c1c; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 1.6rem; font-weight: 700; color: #0f172a; margin: 0; }
    .page-subtitle { font-size: 0.9rem; color: #64748b; margin: 4px 0 0; }
</style>
@endpush

@section('breadcrumb')
    <span class="sep">›</span>
    <a href="{{ route('admin.invoices.index') }}">Hóa đơn</a>
    <span class="sep">›</span>
    <span class="current">Chi tiết</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Chi tiết Hóa đơn #{{ $invoice->invoice_code }}</h1>
        <p class="page-subtitle">{{ $invoice->title }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline">
            ← Quay lại
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert-success">✅ {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error">⚠️ {{ session('error') }}</div>
@endif

<div class="invoice-detail-card">
    <div class="invoice-grid">
        <div class="info-group">
            <span class="info-label">Căn hộ</span>
            <span class="info-value">
                🏠 {{ $invoice->apartment->apartment_number ?? '—' }}
                ({{ optional(optional($invoice->apartment)->floor)->name ?? 'Tầng ' . optional(optional($invoice->apartment)->floor)->floor_number }}
                - {{ optional(optional(optional($invoice->apartment)->floor)->block)->name ?? '—' }})
            </span>
        </div>
        <div class="info-group">
            <span class="info-label">Trạng thái</span>
            <div>
                <span class="badge badge-{{ $invoice->status }}">
                    {{ \App\Models\Invoice::statusLabel($invoice->status) }}
                </span>
            </div>
        </div>
        <div class="info-group">
            <span class="info-label">Kỳ hóa đơn</span>
            <span class="info-value">{{ $invoice->billing_month->format('m/Y') }}</span>
        </div>
        <div class="info-group">
            <span class="info-label">Hạn thanh toán</span>
            <span class="info-value">{{ $invoice->due_date->format('d/m/Y') }}</span>
        </div>
        <div class="info-group">
            <span class="info-label">Tổng số tiền</span>
            <span class="info-value" style="color: var(--brand-600, #2563eb); font-size: 18px;">{{ number_format($invoice->total_amount) }} đ</span>
        </div>
        <div class="info-group">
            <span class="info-label">Người tạo</span>
            <span class="info-value">{{ optional($invoice->creator)->name ?? 'Hệ thống' }}</span>
        </div>
    </div>

    {{-- Tiến trình thanh toán --}}
    @if($invoice->status === 'partial' || $invoice->status === 'paid')
    <div style="background: #f8fafc; border: 1px solid var(--border, #e2e8f0); border-radius: var(--radius-sm, 4px); padding: 14px 18px; margin-bottom: 16px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
            <span style="font-size: 13px; font-weight: 600; color: var(--text-primary, #0f172a);">💰 Tiến trình thanh toán</span>
            <span style="font-size: 13px; font-weight: 700; color: {{ $invoice->status === 'paid' ? '#16a34a' : '#d97706' }}">
                {{ number_format($invoice->paid_amount) }}đ / {{ number_format($invoice->total_amount) }}đ
                ({{ $invoice->paid_percent }}%)
            </span>
        </div>
        <div class="progress-wrap">
            <div class="progress-bar {{ $invoice->status === 'partial' ? 'partial' : '' }}" style="width: {{ $invoice->paid_percent }}%"></div>
        </div>
        @if($invoice->status === 'partial')
        <div style="font-size: 12px; color: var(--text-muted, #64748b); margin-top: 4px;">
            Còn thiếu: <strong style="color: #dc2626;">{{ number_format($invoice->remaining_amount) }}đ</strong>
        </div>
        @endif
    </div>
    @endif

    <hr style="border: 0; border-top: 1px solid var(--border, #e2e8f0); margin: 24px 0;">

    <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 12px;">Chi tiết dịch vụ</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tên dịch vụ</th>
                <th>Loại phí</th>
                <th>Số lượng / Chỉ số</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->details as $detail)
            <tr>
                <td class="info-value">{{ $detail->servicePrice->name ?? 'Dịch vụ phát sinh' }}</td>
                <td>
                    <span class="badge badge-{{ $detail->servicePrice->type ?? 'other' }}">
                        {{ \App\Models\Invoice::typeLabel($detail->servicePrice->type ?? 'other') }}
                    </span>
                </td>
                <td>{{ $detail->quantity }}</td>
                <td class="amount">{{ number_format($detail->amount) }} đ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Lịch sử thanh toán --}}
    @if($invoice->payments->isNotEmpty())
    <hr style="border: 0; border-top: 1px solid var(--border, #e2e8f0); margin: 24px 0;">
    <h3 style="font-size: 15px; font-weight: 700; margin-bottom: 12px; color: var(--green-600, #16a34a)">Lịch sử Thanh toán</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Mã GD</th>
                <th>Số tiền</th>
                <th>Phương thức</th>
                <th>Ghi chú</th>
                <th>Người ghi nhận</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->payments as $pm)
            <tr style="{{ $pm->is_refunded ? 'opacity:.55;' : '' }}">
                <td><span class="invoice-code">{{ $pm->payment_code }}</span></td>
                <td class="amount" style="{{ $pm->is_refunded ? 'text-decoration:line-through;color:var(--text-muted, #64748b)' : '' }}">
                    {{ number_format($pm->amount) }} đ
                </td>
                <td>
                    @if($pm->payment_method === 'cash') 💵 Tiền mặt
                    @elseif($pm->payment_method === 'bank_transfer') 🏦 Chuyển khoản
                    @else 💳 Khác @endif
                </td>
                <td class="text-muted" style="font-size:13px; max-width: 180px;">
                    {{ $pm->note ?? '—' }}
                </td>
                <td>
                    <span class="recorder-badge">👤 {{ optional($pm->recorder)->name ?? 'Hệ thống' }}</span>
                </td>
                <td class="text-muted">{{ $pm->paid_at ? $pm->paid_at->format('d/m/Y H:i') : '—' }}</td>
                <td>
                    @if($pm->is_refunded)
                        <span class="badge" style="background:#fee2e2;color:#dc2626;border:1px solid #fca5a5;">🔄 Đã hủy</span>
                        @if($pm->refund_note)
                        <div style="font-size:11px;color:var(--text-muted, #64748b);margin-top:3px;">{{ $pm->refund_note }}</div>
                        @endif
                    @else
                        <span class="badge badge-paid">Thành công</span>
                    @endif
                </td>
                <td>
                    @if(!$pm->is_refunded && $invoice->status !== 'paid' || (!$pm->is_refunded && $invoice->status === 'paid' && $invoice->payments->where('is_refunded', false)->count() > 0))
                    <button type="button"
                        class="btn btn-sm"
                        style="background:#fef2f2;color:#dc2626;border:1px solid #fca5a5;font-size:12px;padding:4px 10px;"
                        onclick="openRefundModal({{ $pm->id }}, '{{ $pm->payment_code }}', '{{ number_format($pm->amount) }}đ')">
                        🔄 Hủy
                    </button>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Form ghi nhận thanh toán thủ công --}}
    @if($invoice->status !== 'paid')
    <div class="payment-form-card">
        <h3 style="font-size: 14.5px; font-weight: 700; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
            💵 Ghi nhận Thanh toán Thủ công
        </h3>
        <p style="font-size: 12.5px; color: var(--text-secondary, #475569); margin-bottom: 16px;">
            Ghi nhận khi cư dân nộp tiền mặt hoặc chuyển khoản.
            @if($invoice->status === 'partial')
            Còn thiếu <strong style="color:#dc2626;">{{ number_format($invoice->remaining_amount) }}đ</strong>.
            @endif
        </p>

        <form method="POST" action="{{ route('admin.invoices.mark-paid', $invoice) }}">
            @csrf
            @method('PATCH')
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                {{-- Số tiền --}}
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <label class="form-label-sm" for="amount">Số tiền thu</label>
                    <input
                        type="number"
                        name="amount"
                        id="amount"
                        class="period-select"
                        style="width:100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"
                        value="{{ old('amount', $invoice->remaining_amount ?: $invoice->total_amount) }}"
                        min="1"
                        max="{{ $invoice->remaining_amount ?: $invoice->total_amount }}"
                        step="1"
                        required>
                    @error('amount')
                    <span style="font-size:11.5px;color:#dc2626;">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Phương thức --}}
                <div style="display: flex; flex-direction: column; gap: 5px;">
                    <label class="form-label-sm" for="payment_method">Phương thức</label>
                    <select name="payment_method" id="payment_method" class="period-select" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                        <option value="transfer">🏦 Chuyển khoản ngân hàng</option>
                        <option value="cash">💵 Tiền mặt</option>
                        <option value="other">💳 Khác</option>
                    </select>
                </div>
            </div>

            {{-- Ghi chú --}}
            <div style="display: flex; flex-direction: column; gap: 5px; margin-bottom: 16px;">
                <label class="form-label-sm" for="note">Ghi chú / Mã tham chiếu <span style="font-weight:400;text-transform:none;">(tuỳ chọn)</span></label>
                <input
                    type="text"
                    name="note"
                    id="note"
                    class="period-select"
                    style="width:100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"
                    placeholder="VD: Chuyển khoản số GD #123456, hoặc ghi chú khác..."
                    value="{{ old('note') }}">
            </div>

            <button type="submit" class="btn btn-success" style="padding: 10px 28px;">
                ✔ Xác nhận Thanh toán
            </button>
        </form>
    </div>
    @endif
</div>

{{-- Modal Hủy thanh toán --}}
<div class="modal-overlay" id="refundModal">
    <div class="modal-box">
        <div class="modal-title">🔄 Hủy lần thanh toán</div>
        <div class="modal-body">
            Bạn đang hủy <strong id="refundPayCode"></strong> — số tiền <strong id="refundAmount" style="color:#dc2626"></strong>.
            Hành động này sẽ trừ số tiền tương ứng khỏi hóa đơn và cập nhật lại trạng thái.
        </div>

        <form method="POST" id="refundForm">
            @csrf
            @method('POST')
            <div style="margin-bottom: 16px;">
                <label class="form-label-sm" for="refund_note">Lý do hủy <span style="font-weight:400;text-transform:none;">(tuỳ chọn)</span></label>
                <input
                    type="text"
                    name="refund_note"
                    id="refund_note"
                    class="period-select"
                    style="width:100%; margin-top:4px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;"
                    placeholder="VD: Ghi nhầm, cư dân yêu cầu hoàn trả...">
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="closeRefundModal()">Hủy bỏ</button>
                <button type="submit" class="btn" style="background:#dc2626;color:#fff;border:none;">Xác nhận Hủy</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openRefundModal(paymentId, payCode, amount) {
    document.getElementById('refundPayCode').textContent = payCode;
    document.getElementById('refundAmount').textContent = amount;
    document.getElementById('refundForm').action = '/admin/payments/' + paymentId + '/refund';
    document.getElementById('refundModal').classList.add('open');
}
function closeRefundModal() {
    document.getElementById('refundModal').classList.remove('open');
    document.getElementById('refund_note').value = '';
}
// Đóng modal khi click ngoài
document.getElementById('refundModal').addEventListener('click', function(e) {
    if (e.target === this) closeRefundModal();
});
</script>
@endpush

@endsection
