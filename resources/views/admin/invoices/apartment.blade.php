@extends('layouts.admin.master')

@section('page_title', 'Hóa đơn Căn hộ ' . $apartment->apartment_number)

@section('content')
<div class="apt-inv-page">

    {{-- Header --}}
    <div class="apt-inv-header">
        <div class="apt-inv-header__left">
            <a href="{{ portal_route('invoices.index') }}" class="btn-back">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Quay lại danh sách
            </a>
            <div style="margin-top: 12px;">
                <p class="apt-inv-eyebrow">Tài chính · Hóa đơn căn hộ</p>
                <h1 class="apt-inv-title">
                    Căn {{ $apartment->apartment_number }}
                    <span class="apt-inv-block-tag">{{ optional(optional($apartment->floor)->block)->name ?? '' }}</span>
                </h1>
                <p class="apt-inv-sub">{{ optional($apartment->floor)->name ?? '' }} · {{ $apartment->owner_name }}</p>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="apt-inv-alert apt-inv-alert--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="apt-inv-alert apt-inv-alert--danger">{{ session('error') }}</div>
    @endif

    {{-- Stat Cards --}}
    <div class="apt-inv-stats">
        <div class="apt-inv-stat-card">
            <span class="apt-inv-stat-label">Tổng hóa đơn đã phát</span>
            <span class="apt-inv-stat-val">{{ number_format($totalAmount) }}đ</span>
        </div>
        <div class="apt-inv-stat-card apt-inv-stat-card--success">
            <span class="apt-inv-stat-label">Đã thu</span>
            <span class="apt-inv-stat-val">{{ number_format($totalPaid) }}đ</span>
        </div>
        <div class="apt-inv-stat-card {{ $totalDebt > 0 ? 'apt-inv-stat-card--danger' : 'apt-inv-stat-card--ok' }}">
            <span class="apt-inv-stat-label">Còn dư nợ</span>
            <span class="apt-inv-stat-val">{{ number_format($totalDebt) }}đ</span>
        </div>
        <div class="apt-inv-stat-card apt-inv-stat-card--neutral">
            <span class="apt-inv-stat-label">Số hóa đơn</span>
            <span class="apt-inv-stat-val">{{ $invoices->total() }}</span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="apt-inv-filter-bar">
        <form method="GET" class="apt-inv-filter-form">
            <select name="status" class="apt-inv-select" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Thanh toán một phần</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Quá hạn</option>
            </select>
            <input type="month" name="month" value="{{ request('month') }}" class="apt-inv-input" onchange="this.form.submit()">
            @if(request()->hasAny(['status','month']))
                <a href="{{ portal_route('invoices.apartment', $apartment) }}" class="apt-inv-btn apt-inv-btn--ghost">Xóa lọc</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="apt-inv-card">
        <div class="apt-inv-card-header">
            <span>{{ $invoices->total() }} hóa đơn</span>
            <span class="apt-inv-muted">Trang {{ $invoices->currentPage() }}/{{ $invoices->lastPage() }}</span>
        </div>

        <table class="apt-inv-table">
            <thead>
                <tr>
                    <th>Mã HD</th>
                    <th>Tiêu đề / Kỳ</th>
                    <th>Chi tiết phí</th>
                    <th>Tổng tiền</th>
                    <th>Đã thu</th>
                    <th>Hạn TT</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td>
                        <span class="apt-inv-code">{{ $inv->invoice_code }}</span>
                    </td>
                    <td>
                        <div class="apt-inv-title-cell">{{ $inv->title }}</div>
                        <div class="apt-inv-muted">Tháng {{ $inv->billing_month->format('m/Y') }}</div>
                    </td>
                    <td>
                        @foreach($inv->details as $detail)
                            <div class="apt-inv-detail-item">
                                <span class="apt-inv-badge apt-inv-badge--{{ $detail->servicePrice->type ?? 'other' }}">
                                    {{ \App\Models\Invoice::typeLabel($detail->servicePrice->type ?? 'other') }}
                                </span>
                                <span style="font-size:0.78rem; color:#475569; margin-left:4px;">
                                    {{ number_format($detail->amount) }}đ
                                </span>
                            </div>
                        @endforeach
                    </td>
                    <td class="apt-inv-amount">{{ number_format($inv->total_amount) }}đ</td>
                    <td>
                        @if($inv->paid_amount > 0)
                            <span style="color:#16a34a; font-weight:600; font-size:0.85rem;">{{ number_format($inv->paid_amount) }}đ</span>
                        @else
                            <span class="apt-inv-muted">—</span>
                        @endif
                    </td>
                    <td class="apt-inv-muted">{{ $inv->due_date->format('d/m/Y') }}</td>
                    <td>
                        @if($inv->status === 'paid')
                            <span class="apt-inv-status apt-inv-status--paid">Đã TT</span>
                        @elseif($inv->status === 'partial')
                            <span class="apt-inv-status apt-inv-status--partial">Một phần</span>
                        @elseif($inv->status === 'overdue')
                            <span class="apt-inv-status apt-inv-status--overdue">Quá hạn</span>
                        @else
                            <span class="apt-inv-status apt-inv-status--unpaid">Chưa TT</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap:6px; align-items:center;">
                            <a href="{{ portal_route('invoices.show', $inv) }}" class="apt-inv-btn apt-inv-btn--detail">Chi tiết</a>
                            @if($inv->status !== 'paid')
                            <button type="button" class="apt-inv-btn apt-inv-btn--pay"
                                onclick="openPayModal({{ $inv->id }}, '{{ $inv->invoice_code }}', '{{ addslashes($inv->title) }}', {{ $inv->remaining_amount ?: $inv->total_amount }}, '{{ addslashes($apartment->owner_name ?? '') }}', 'Tháng {{ $inv->billing_month->format('m/Y') }}')">
                                Thu tiền
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="apt-inv-empty">Không có hóa đơn nào</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($invoices->hasPages())
        <div class="apt-inv-pagination">
            @if(!$invoices->onFirstPage())
                <a href="{{ $invoices->previousPageUrl() }}" class="apt-inv-page-btn">‹ Trước</a>
            @endif
            @foreach($invoices->getUrlRange(max(1,$invoices->currentPage()-2), min($invoices->lastPage(),$invoices->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="apt-inv-page-btn {{ $page == $invoices->currentPage() ? 'apt-inv-page-btn--active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($invoices->hasMorePages())
                <a href="{{ $invoices->nextPageUrl() }}" class="apt-inv-page-btn">Sau ›</a>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- Quick Payment Modal --}}
<div class="modal-overlay" id="payModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Thu tiền hóa đơn</h3>
            <button class="modal-close" onclick="closePayModal()">&times;</button>
        </div>
        <form id="payForm" method="POST" action="" enctype="multipart/form-data" onsubmit="return confirmPay(event);">
            @csrf
            @method('PATCH')
            <input type="hidden" name="amount" id="pay_amount">
            <div class="modal-body">
                <p id="payInfo" style="font-size:13px; color:#64748b; margin-bottom:16px; font-weight:500;"></p>
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:0.8rem; font-weight:600; color:#475569; margin-bottom:6px;">Phương thức thanh toán</label>
                    <select name="payment_method" id="pay_method" class="apt-inv-select" style="width:100%">
                        <option value="transfer">🏦 Chuyển khoản ngân hàng</option>
                        <option value="cash">💵 Tiền mặt</option>
                        <option value="other">💳 Khác</option>
                    </select>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="display:block; font-size:0.8rem; font-weight:600; color:#475569; margin-bottom:6px;">Người nộp tiền <span style="font-weight:400; color:#94a3b8;">(tuỳ chọn)</span></label>
                    <input type="text" name="payer_name" id="pay_payer" class="apt-inv-input" style="width:100%" placeholder="Tên người nộp tiền...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closePayModal()" style="padding:8px 16px; border-radius:6px; background:#f1f5f9; color:#475569; border:1px solid #e2e8f0; font-weight:600; cursor:pointer;">Hủy</button>
                <button type="submit" style="padding:8px 16px; border-radius:6px; background:#16a34a; color:#fff; border:none; font-weight:600; cursor:pointer;">Xác nhận Thu</button>
            </div>
        </form>
    </div>
</div>

<style>
.apt-inv-page { max-width: 1100px; margin: 0 auto; padding: 24px 20px; }

/* Header */
.apt-inv-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.apt-inv-header__left { flex: 1; }
.btn-back { display: inline-flex; align-items: center; gap: 6px; color: #2563eb; text-decoration: none; font-weight: 600; font-size: 0.88rem; }
.btn-back:hover { color: #1d4ed8; }
.apt-inv-eyebrow { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: #94a3b8; margin: 0 0 4px; font-weight: 600; }
.apt-inv-title { font-size: 1.7rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; display: flex; align-items: center; gap: 10px; }
.apt-inv-block-tag { font-size: 0.9rem; font-weight: 600; padding: 3px 10px; background: #eff6ff; color: #2563eb; border-radius: 20px; }
.apt-inv-sub { font-size: 0.9rem; color: #64748b; margin: 0; }

/* Alert */
.apt-inv-alert { padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; font-weight: 500; margin-bottom: 16px; }
.apt-inv-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
.apt-inv-alert--danger { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }

/* Stats */
.apt-inv-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px; }
.apt-inv-stat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; }
.apt-inv-stat-card--success { border-color: #bbf7d0; background: #f0fdf4; }
.apt-inv-stat-card--danger { border-color: #fca5a5; background: #fef2f2; }
.apt-inv-stat-card--ok { border-color: #bbf7d0; background: #f0fdf4; }
.apt-inv-stat-card--neutral { border-color: #e2e8f0; }
.apt-inv-stat-label { display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 6px; }
.apt-inv-stat-val { display: block; font-size: 1.5rem; font-weight: 800; color: #0f172a; }
.apt-inv-stat-card--success .apt-inv-stat-val { color: #166534; }
.apt-inv-stat-card--danger .apt-inv-stat-val { color: #991b1b; }

/* Filter */
.apt-inv-filter-bar { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; }
.apt-inv-filter-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.apt-inv-input { padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; color: #1e293b; background: #f8fafc; }
.apt-inv-select { padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; color: #1e293b; background: #f8fafc; cursor: pointer; }

/* Card/Table */
.apt-inv-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
.apt-inv-card-header { display: flex; justify-content: space-between; align-items: center; padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; font-weight: 600; color: #334155; }
.apt-inv-muted { color: #94a3b8; font-size: 0.8rem; }
.apt-inv-table { width: 100%; border-collapse: collapse; }
.apt-inv-table th { text-align: left; padding: 10px 14px; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
.apt-inv-table td { padding: 12px 14px; font-size: 0.85rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.apt-inv-table tr:hover td { background: #f8fafc; }
.apt-inv-code { font-family: monospace; font-size: 0.8rem; color: #3b82f6; font-weight: 600; }
.apt-inv-amount { font-weight: 700; color: #0f172a; }
.apt-inv-title-cell { font-weight: 600; color: #0f172a; font-size: 0.88rem; margin-bottom: 2px; }
.apt-inv-detail-item { display: flex; align-items: center; margin-bottom: 3px; }

/* Badges */
.apt-inv-badge { font-size: 0.68rem; font-weight: 600; padding: 2px 7px; border-radius: 4px; }
.apt-inv-badge--electricity { background: #fef9c3; color: #854d0e; }
.apt-inv-badge--water { background: #dbeafe; color: #1e40af; }
.apt-inv-badge--management_fee { background: #f3e8ff; color: #6b21a8; }
.apt-inv-badge--parking { background: #dcfce7; color: #166534; }
.apt-inv-badge--other, .apt-inv-badge--internet, .apt-inv-badge--service { background: #f1f5f9; color: #475569; }

/* Status Pills */
.apt-inv-status { font-size: 0.72rem; font-weight: 600; padding: 3px 8px; border-radius: 4px; }
.apt-inv-status--paid { background: #dcfce7; color: #15803d; }
.apt-inv-status--partial { background: #fef3c7; color: #b45309; }
.apt-inv-status--unpaid { background: #eff6ff; color: #1d4ed8; }
.apt-inv-status--overdue { background: #fee2e2; color: #b91c1c; }

/* Buttons */
.apt-inv-btn { padding: 5px 12px; border-radius: 6px; font-size: 0.78rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; }
.apt-inv-btn--ghost { background: none; color: #ef4444; border: 1px solid #fecaca; }
.apt-inv-btn--detail { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
.apt-inv-btn--detail:hover { background: #dbeafe; }
.apt-inv-btn--pay { background: #16a34a; color: #fff; }
.apt-inv-btn--pay:hover { background: #15803d; }

/* Empty */
.apt-inv-empty { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.95rem; }

/* Pagination */
.apt-inv-pagination { display: flex; justify-content: center; gap: 5px; padding: 14px; border-top: 1px solid #e2e8f0; }
.apt-inv-page-btn { padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; border: 1px solid #e2e8f0; color: #475569; background: #fff; text-decoration: none; }
.apt-inv-page-btn:hover { background: #f1f5f9; }
.apt-inv-page-btn--active { background: #2563eb; color: #fff; border-color: #2563eb; }

/* Modal */
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.45); z-index: 1000; align-items: center; justify-content: center; }
.modal-overlay.active { display: flex; }
.modal-content { background: #fff; border-radius: 12px; width: 100%; max-width: 420px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); overflow: hidden; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #e2e8f0; }
.modal-title { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; }
.modal-close { background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #94a3b8; }
.modal-body { padding: 20px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc; }

@media (max-width: 768px) {
    .apt-inv-stats { grid-template-columns: 1fr 1fr; }
}
</style>

<script>
let currentAmt = 0, currentCode = '';

function openPayModal(id, code, title, amount, payerName, month) {
    currentAmt = amount;
    currentCode = code;
    document.getElementById('payForm').action = `/admin/invoices/${id}/mark-paid`;
    document.getElementById('pay_amount').value = amount;
    document.getElementById('pay_payer').value = payerName;
    document.getElementById('payInfo').innerHTML =
        `Hóa đơn: <strong>${code}</strong><br>Nội dung: ${title}<br>Kỳ: ${month}<br>Số tiền: <span style="color:#2563eb;font-weight:700">${Number(amount).toLocaleString('vi-VN')} đ</span>`;
    document.getElementById('payModal').classList.add('active');
}

function closePayModal() {
    document.getElementById('payModal').classList.remove('active');
}

function confirmPay(event) {
    const method = document.getElementById('pay_method');
    const methodLabel = method.options[method.selectedIndex].text;
    const payer = document.getElementById('pay_payer').value || 'Cư dân';
    const msg = `Xác nhận thu tiền:\n• Hóa đơn: ${currentCode}\n• Số tiền: ${Number(currentAmt).toLocaleString('vi-VN')} đ\n• Phương thức: ${methodLabel}\n• Người nộp: ${payer}\n\nBạn có chắc không?`;
    return confirm(msg);
}

document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) closePayModal();
});
</script>

@endsection
