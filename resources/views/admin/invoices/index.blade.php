@extends('admin.layout')

@section('title', 'Danh sách Hóa đơn')

@push('page_css')
@vite(['resources/css/admin/invoices.css'])
@endpush

@section('breadcrumb')
    <span class="sep">›</span>
    <a href="{{ route('admin.invoices.stats') }}">Thống kê</a>
    <span class="sep">›</span>
    <span class="current">Danh sách hóa đơn</span>
@endsection

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Danh sách Hóa đơn</h1>
        <p class="page-subtitle">Quản lý toàn bộ hóa đơn trong hệ thống</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.invoices.stats') }}" class="btn btn-outline">
            📊 Thống kê
        </a>
        <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tạo hóa đơn
        </a>
    </div>
</div>

{{-- Flash message --}}
@if(session('success'))
<div style="background:var(--green-50);border:1px solid #6ee7b7;color:var(--green-600);padding:12px 18px;border-radius:var(--radius-sm);margin-bottom:16px;font-size:13.5px;font-weight:500;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Filters --}}
<div class="table-card" style="margin-bottom:16px">
    <form method="GET" action="{{ route('admin.invoices.index') }}"
          style="display:flex;gap:10px;align-items:center;padding:14px 18px;flex-wrap:wrap">

        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="🔍  Tìm mã hóa đơn, tiêu đề..."
               style="flex:1;min-width:200px;padding:8px 14px;border:1px solid var(--border);border-radius:var(--radius-xs);font-family:inherit;font-size:13px;color:var(--text-primary);outline:none;transition:border-color .2s"
               onfocus="this.style.borderColor='var(--brand-400)'"
               onblur="this.style.borderColor='var(--border)'">

        <select name="type" class="period-select">
            <option value="">Tất cả loại</option>
            <option value="electricity" {{ request('type')=='electricity'?'selected':'' }}>⚡ Tiền điện</option>
            <option value="water"       {{ request('type')=='water'?'selected':'' }}>💧 Tiền nước</option>
            <option value="management_fee" {{ request('type')=='management_fee'?'selected':'' }}>🏢 Phí quản lý</option>
            <option value="parking"     {{ request('type')=='parking'?'selected':'' }}>🚗 Phí gửi xe</option>
            <option value="other"       {{ request('type')=='other'?'selected':'' }}>📦 Khác</option>
        </select>

        <select name="status" class="period-select">
            <option value="">Tất cả trạng thái</option>
            <option value="paid"    {{ request('status')=='paid'?'selected':'' }}>✅ Đã thanh toán</option>
            <option value="unpaid"  {{ request('status')=='unpaid'?'selected':'' }}>⏳ Chưa thanh toán</option>
            <option value="overdue" {{ request('status')=='overdue'?'selected':'' }}>🚨 Quá hạn</option>
        </select>

        <input type="month" name="month" value="{{ request('month') }}"
               class="period-select" style="cursor:pointer">

        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline btn-sm">Xóa lọc</a>
    </form>
</div>

{{-- Table --}}
<div class="table-card">
    <div class="table-card-header">
        <div class="table-card-title">
            {{ $invoices->total() }} hóa đơn
        </div>
        <div style="font-size:12px;color:var(--text-muted)">
            Trang {{ $invoices->currentPage() }} / {{ $invoices->lastPage() }}
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Mã hóa đơn</th>
                <th>Căn hộ</th>
                <th>Tiêu đề</th>
                <th>Loại phí</th>
                <th>Số tiền</th>
                <th>Tháng</th>
                <th>Hạn TT</th>
                <th>Trạng thái</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
            <tr>
                <td><span class="invoice-code">{{ $inv->invoice_code }}</span></td>
                <td>
                    <div class="apt-label">
                        <div class="apt-icon">🏠</div>
                        <div>
                            <div class="apt-name">{{ optional($inv->apartment)->apartment_number ?? '—' }}</div>
                            <div class="apt-block">
                                {{ optional(optional($inv->apartment->floor??null)->block??null)->name ?? '' }}
                            </div>
                        </div>
                    </div>
                </td>
                <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    {{ $inv->title }}
                </td>
                <td>
                    <span class="badge badge-{{ $inv->type }}">
                        {{ \App\Models\Invoice::typeLabel($inv->type) }}
                    </span>
                </td>
                <td class="amount">{{ number_format($inv->amount) }} đ</td>
                <td class="text-muted">{{ $inv->billing_month->format('m/Y') }}</td>
                <td class="text-muted {{ $inv->status==='overdue' ? 'font-semibold' : '' }}"
                    style="{{ $inv->status==='overdue' ? 'color:var(--red-500)' : '' }}">
                    {{ $inv->due_date->format('d/m/Y') }}
                </td>
                <td>
                    <span class="badge badge-{{ $inv->status }}">
                        {{ \App\Models\Invoice::statusLabel($inv->status) }}
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:6px">
                        <a href="{{ route('admin.invoices.show', $inv) }}"
                           class="btn btn-outline btn-sm" title="Chi tiết">👁</a>
                        @if($inv->status !== 'paid')
                        <button type="button" class="btn btn-success btn-sm" title="Ghi nhận thanh toán nhanh" 
                                onclick="openPaymentModal({{ $inv->id }}, '{{ $inv->invoice_code }}', '{{ addslashes($inv->title) }}', {{ $inv->amount }})">
                            ✔
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9">
                    <div class="empty-state">
                        <div class="empty-state-icon">📄</div>
                        <div class="empty-state-text">Không có hóa đơn nào phù hợp</div>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($invoices->hasPages())
    <div style="padding:14px 18px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:6px">
        @if($invoices->onFirstPage())
            <span class="btn btn-outline btn-sm" style="opacity:.4;cursor:not-allowed">‹ Trước</span>
        @else
            <a href="{{ $invoices->previousPageUrl() }}" class="btn btn-outline btn-sm">‹ Trước</a>
        @endif

        @foreach($invoices->getUrlRange(max(1,$invoices->currentPage()-2), min($invoices->lastPage(),$invoices->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="btn btn-sm {{ $page==$invoices->currentPage() ? 'btn-primary' : 'btn-outline' }}">
                {{ $page }}
            </a>
        @endforeach

        @if($invoices->hasMorePages())
            <a href="{{ $invoices->nextPageUrl() }}" class="btn btn-outline btn-sm">Sau ›</a>
        @else
            <span class="btn btn-outline btn-sm" style="opacity:.4;cursor:not-allowed">Sau ›</span>
        @endif
    </div>
    @endif
</div>

{{-- Quick Payment Modal --}}
<div class="modal-overlay" id="quickPaymentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Thanh toán nhanh</h3>
            <button class="modal-close" onclick="closePaymentModal()">&times;</button>
        </div>
        <form id="quickPaymentForm" method="POST" action="">
            @csrf
            <input type="hidden" name="amount" id="modal_amount">
            <div class="modal-body">
                <p id="modalInvoiceInfo" style="font-size: 13px; color: var(--text-secondary); margin-bottom: 16px; font-weight: 500;"></p>
                
                <div class="form-field">
                    <label for="modal_payment_method">Phương thức thanh toán</label>
                    <select name="payment_method" id="modal_payment_method" class="form-input">
                        <option value="transfer">🏦 Chuyển khoản ngân hàng</option>
                        <option value="cash">💵 Tiền mặt</option>
                        <option value="other">💳 Khác</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closePaymentModal()">Hủy</button>
                <button type="submit" class="btn btn-success">Xác nhận</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaymentModal(invoiceId, invoiceCode, title, amount) {
        const modal = document.getElementById('quickPaymentModal');
        const form = document.getElementById('quickPaymentForm');
        const info = document.getElementById('modalInvoiceInfo');
        
        form.action = `/admin/invoices/${invoiceId}/mark-paid`;
        document.getElementById('modal_amount').value = amount;
        info.innerHTML = `Hóa đơn: <strong>${invoiceCode}</strong><br>Nội dung: ${title}<br>Số tiền: <span style="color:var(--brand-600);font-weight:700">${Number(amount).toLocaleString('vi-VN')} đ</span>`;
        
        modal.classList.add('active');
    }
    
    function closePaymentModal() {
        document.getElementById('quickPaymentModal').classList.remove('active');
    }
    
    // Close modal on click overlay
    document.getElementById('quickPaymentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePaymentModal();
        }
    });
</script>

@endsection
