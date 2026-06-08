@extends('layouts.admin.master')

@section('page_title', 'Quản lý Hóa đơn')

@section('content')
<div class="inv-admin">

    {{-- Header --}}
    <div class="inv-admin__header">
        <div>
            <p class="inv-admin__eyebrow">Tài chính</p>
            <h1 class="inv-admin__title">Quản lý Hóa đơn</h1>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="inv-admin__alert inv-admin__alert--success">✅ {{ session('success') }}</div>
    @endif

    {{-- Bộ lọc --}}
    <div class="inv-admin__filter-bar">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="inv-admin__filter-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Tìm mã HD, tiêu đề, căn hộ..."
                   class="inv-admin__input">

            <select name="status" class="inv-admin__select" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="paid" {{ request('status')=='paid' ? 'selected' : '' }}>✅ Đã thanh toán</option>
                <option value="unpaid" {{ request('status')=='unpaid' ? 'selected' : '' }}>⏳ Chưa thanh toán</option>
                <option value="overdue" {{ request('status')=='overdue' ? 'selected' : '' }}>🚨 Quá hạn</option>
            </select>

            <button type="submit" class="inv-admin__btn inv-admin__btn--primary">Lọc</button>
            @if(request()->hasAny(['search','status','type','month']))
                <a href="{{ route('admin.invoices.index') }}" class="inv-admin__btn inv-admin__btn--ghost">Xóa lọc</a>
            @endif
        </form>
    </div>

    {{-- Bảng hóa đơn --}}
    <div class="inv-admin__card">
        <div class="inv-admin__card-header">
            <span>{{ $invoices->total() }} hóa đơn</span>
            <span class="inv-admin__muted">Trang {{ $invoices->currentPage() }}/{{ $invoices->lastPage() }}</span>
        </div>

        <table class="inv-admin__table">
            <thead>
                <tr>
                    <th>Mã HD</th>
                    <th>Căn hộ</th>
                    <th>Tiêu đề</th>
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
                    <td>
                        <span class="inv-admin__code">{{ $inv->invoice_code }}</span>
                    </td>
                    <td>
                        {{ optional($inv->apartment)->apartment_number ?? '—' }}
                        <div class="inv-admin__sub">{{ optional(optional(optional($inv->apartment)->floor)->block)->name ?? '' }}</div>
                    </td>
                    <td>{{ $inv->title }}</td>
                    <td class="inv-admin__amount">{{ number_format($inv->total_amount) }}đ</td>
                    <td class="inv-admin__muted">{{ $inv->billing_month->format('m/Y') }}</td>
                    <td class="inv-admin__muted">{{ $inv->due_date->format('d/m/Y') }}</td>
                    <td>
                        @if($inv->status === 'paid')
                            <span class="inv-admin__badge inv-admin__badge--paid">Đã TT</span>
                        @elseif($inv->status === 'overdue')
                            <span class="inv-admin__badge inv-admin__badge--overdue">Quá hạn</span>
                        @else
                            <span class="inv-admin__badge inv-admin__badge--unpaid">Chưa TT</span>
                        @endif
                    </td>
                    <td>
                        @if($inv->status !== 'paid')
                        <form method="POST" action="{{ route('admin.invoices.mark-paid', $inv) }}"
                              onsubmit="return confirm('Đánh dấu đã thu hóa đơn {{ $inv->invoice_code }}?')">
                            @csrf @method('PATCH')
                            <input type="hidden" name="payment_method" value="cash">
                            <button type="submit" class="inv-admin__btn inv-admin__btn--sm inv-admin__btn--success">✔ Thu</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="inv-admin__empty">📄 Không có hóa đơn nào</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Phân trang --}}
        @if($invoices->hasPages())
        <div class="inv-admin__pagination">
            @if(!$invoices->onFirstPage())
                <a href="{{ $invoices->previousPageUrl() }}" class="inv-admin__page-btn">‹ Trước</a>
            @endif
            @foreach($invoices->getUrlRange(max(1,$invoices->currentPage()-2), min($invoices->lastPage(),$invoices->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="inv-admin__page-btn {{ $page==$invoices->currentPage() ? 'inv-admin__page-btn--active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($invoices->hasMorePages())
                <a href="{{ $invoices->nextPageUrl() }}" class="inv-admin__page-btn">Sau ›</a>
            @endif
        </div>
        @endif
    </div>
</div>

<style>
.inv-admin { max-width: 1100px; margin: 0 auto; padding: 24px 20px; }

/* Header */
.inv-admin__header { margin-bottom: 20px; }
.inv-admin__eyebrow { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin: 0 0 4px; font-weight: 600; }
.inv-admin__title { font-size: 1.6rem; font-weight: 700; color: #0f172a; margin: 0; }

/* Alert */
.inv-admin__alert { padding: 12px 16px; border-radius: 8px; font-size: 0.9rem; font-weight: 500; margin-bottom: 16px; }
.inv-admin__alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }

/* Filter */
.inv-admin__filter-bar { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin-bottom: 16px; }
.inv-admin__filter-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
.inv-admin__input {
    flex: 1; min-width: 200px; padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px;
    font-size: 0.85rem; color: #1e293b; outline: none; background: #f8fafc;
}
.inv-admin__input:focus { border-color: #3b82f6; }
.inv-admin__select {
    padding: 7px 12px; border: 1px solid #cbd5e1; border-radius: 6px;
    font-size: 0.85rem; color: #1e293b; background: #f8fafc; cursor: pointer;
}

/* Buttons */
.inv-admin__btn {
    padding: 7px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;
    cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center;
}
.inv-admin__btn--primary { background: #2563eb; color: #fff; }
.inv-admin__btn--primary:hover { background: #1d4ed8; }
.inv-admin__btn--ghost { background: none; color: #ef4444; border: 1px solid #fecaca; }
.inv-admin__btn--ghost:hover { background: #fef2f2; }
.inv-admin__btn--success { background: #16a34a; color: #fff; }
.inv-admin__btn--success:hover { background: #15803d; }
.inv-admin__btn--sm { padding: 4px 10px; font-size: 0.78rem; }

/* Card / Table */
.inv-admin__card { background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; }
.inv-admin__card-header {
    display: flex; justify-content: space-between; align-items: center;
    padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; font-weight: 600; color: #334155;
}
.inv-admin__muted { color: #94a3b8; font-size: 0.8rem; }

.inv-admin__table { width: 100%; border-collapse: collapse; }
.inv-admin__table th {
    text-align: left; padding: 10px 14px; font-size: 0.75rem; font-weight: 600;
    color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #e2e8f0; background: #f8fafc;
}
.inv-admin__table td { padding: 12px 14px; font-size: 0.85rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.inv-admin__table tr:hover td { background: #f8fafc; }
.inv-admin__sub { font-size: 0.75rem; color: #94a3b8; }
.inv-admin__code { font-family: monospace; font-size: 0.8rem; color: #3b82f6; font-weight: 600; }
.inv-admin__amount { font-weight: 700; color: #0f172a; }

/* Badges */
.inv-admin__badge { font-size: 0.72rem; font-weight: 600; padding: 3px 8px; border-radius: 4px; }
.inv-admin__badge--paid { background: #dcfce7; color: #15803d; }
.inv-admin__badge--unpaid { background: #fef3c7; color: #b45309; }
.inv-admin__badge--overdue { background: #fee2e2; color: #b91c1c; }

/* Empty */
.inv-admin__empty { text-align: center; padding: 40px 20px; color: #94a3b8; font-size: 0.95rem; }

/* Pagination */
.inv-admin__pagination { display: flex; justify-content: center; gap: 5px; padding: 14px; border-top: 1px solid #e2e8f0; }
.inv-admin__page-btn {
    padding: 6px 12px; border-radius: 6px; font-size: 0.8rem; font-weight: 600;
    border: 1px solid #e2e8f0; color: #475569; background: #fff; text-decoration: none;
}
.inv-admin__page-btn:hover { background: #f1f5f9; }
.inv-admin__page-btn--active { background: #2563eb; color: #fff; border-color: #2563eb; }
</style>
@endsection
