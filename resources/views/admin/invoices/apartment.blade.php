@extends('layouts.admin.master')

@section('page_title', 'Hóa đơn Căn hộ ' . $apartment->apartment_number)

@section('content')
<div class="apt-inv-page">

    {{-- Header --}}
    <div class="apt-inv-header">
        <a href="{{ portal_route('invoices.index') }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại danh sách
        </a>
        <div class="apt-inv-header__content" style="margin-top:8px;">
            <p class="apt-inv-eyebrow">Tài chính <span class="dot">·</span> Hóa đơn căn hộ</p>
            @php
                $blockName = optional(optional($apartment->floor)->block)->name;
                $floorName = optional($apartment->floor)->name;
            @endphp
            <div style="display:flex; justify-content:space-between; align-items:center; width:100%; gap:16px; flex-wrap:wrap;">
                <h1 class="apt-inv-title" style="margin:0;">
                    Căn {{ $apartment->apartment_number }}
                    @if($blockName)
                        <span class="apt-inv-block-tag">{{ \Illuminate\Support\Str::startsWith($blockName, 'Tòa') ? $blockName : 'Tòa ' . $blockName }}</span>
                    @endif
                </h1>
                <a href="{{ portal_route('manual-payment.index', ['q' => $apartment->apartment_number]) }}" 
                   class="apt-inv-btn" 
                   style="background:#16a34a; color:#fff; padding:10px 22px; font-size:14px; font-weight:700; border-radius:8px; display:inline-flex; align-items:center; gap:8px; text-decoration:none; box-shadow:0 2px 6px rgba(22,163,74,0.3); white-space:nowrap;">
                    <i class="fas fa-hand-holding-usd" style="font-size:16px;"></i> Thu tiền
                </a>
            </div>
            <p class="apt-inv-sub" style="margin-top:6px;">
                @if($floorName)
                    {{ \Illuminate\Support\Str::startsWith($floorName, 'Tầng') ? $floorName : 'Tầng ' . $floorName }} <span class="dot">·</span>
                @endif
                {{ $apartment->owner_name }}
            </p>
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
                    <th>Tổng tiền</th>
                    <th>Đã thu</th>
                    <th>Hạn TT</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                <tr onclick="window.location='{{ portal_route('invoices.show', $inv) }}'" style="cursor:pointer;">
                    <td>
                        <span class="apt-inv-code">{{ $inv->invoice_code }}</span>
                    </td>
                    <td>
                        <div class="apt-inv-title-cell">{{ $inv->title }}</div>
                        <div class="apt-inv-muted">Tháng {{ $inv->billing_month->format('m/Y') }}</div>
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
                        @elseif($inv->status === 'cancelled')
                            <span class="apt-inv-status" style="background: #e2e3e5; color: #383d41;">Đã hủy</span>
                        @else
                            <span class="apt-inv-status apt-inv-status--unpaid">Chưa TT</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                            @if (!in_array($inv->status, ['paid', 'cancelled']))
                                <form action="{{ portal_route('invoices.resend-notification', $inv) }}"
                                    method="POST" style="display:inline;" onclick="event.stopPropagation()">
                                    @csrf
                                    <button type="submit" class="apt-inv-btn apt-inv-btn--notify" title="Gửi lại thông báo">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                                    </button>
                                </form>
                                <button class="apt-inv-btn apt-inv-btn--cancel" onclick="event.stopPropagation(); confirmCancel({{ $inv->id }})" title="Hủy hóa đơn">✕ Hủy</button>

                                <form id="cancel-form-{{ $inv->id }}" action="{{ portal_route('invoices.cancel', $inv) }}" method="POST" style="display:none;">
                                    @csrf
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
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
.apt-inv-header { margin-bottom: 24px; display: flex; flex-direction: row; justify-content: space-between; align-items: flex-end; gap: 16px; flex-wrap: wrap; }
.btn-back { display: inline-flex; align-items: center; gap: 8px; color: #2563eb; font-weight: 500; font-size: 1rem; text-decoration: none; }
.btn-back:hover { color: #1d4ed8; text-decoration: underline; }
.apt-inv-header__content { display: flex; flex-direction: column; gap: 8px; }
.apt-inv-eyebrow { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin: 0; font-weight: 600; }
.apt-inv-eyebrow .dot { margin: 0 4px; font-weight: 400; color: #cbd5e1; }
.apt-inv-title { font-size: 2.6rem; font-weight: 900; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 16px; letter-spacing: -0.02em; line-height: 1; }
.apt-inv-block-tag { font-size: 1rem; font-weight: 600; padding: 6px 16px; background: #eff6ff; color: #2563eb; border-radius: 20px; letter-spacing: normal; line-height: 1.2; }
.apt-inv-sub { font-size: 1.1rem; color: #64748b; margin: 0; font-weight: 400; }
.apt-inv-sub .dot { margin: 0 4px; font-weight: 700; color: #cbd5e1; }

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
.apt-inv-badge--motorbike { background: #fff7ed; color: #c2410c; }
.apt-inv-badge--electric_bike { background: #f0fdf4; color: #15803d; }
.apt-inv-badge--car { background: #fef3c7; color: #92400e; }
.apt-inv-badge--bicycle { background: #ecfdf5; color: #065f46; }
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
.apt-inv-btn--print { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
.apt-inv-btn--print:hover { background: #e2e8f0; }
.apt-inv-btn--pay { background: #16a34a; color: #fff; }
.apt-inv-btn--pay:hover { background: #15803d; }
.apt-inv-btn--notify { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
.apt-inv-btn--notify:hover { background: #dbeafe; }
.apt-inv-btn--cancel { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.apt-inv-btn--cancel:hover { background: #fee2f2; }

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
.modal-content { background: #fff; border-radius: 12px; width: 100%; max-width: 420px; overflow: hidden; }
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

function confirmCancel(id) {
    if (confirm('Bạn có chắc muốn hủy hóa đơn này? Hành động này không thể hoàn tác.')) {
        document.getElementById('cancel-form-' + id).submit();
    }
}

document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) closePayModal();
});
</script>

@endsection
