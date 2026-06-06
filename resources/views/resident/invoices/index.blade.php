@extends('layouts.resident.master')

@section('title', 'Hóa đơn – DomusHub')

@section('content')
<div class="inv-container">

    {{-- HEADER --}}
    <div class="inv-header">
        <div>
            <p class="inv-eyebrow">Tài chính căn hộ</p>
            <h1 class="inv-title">Hóa đơn của tôi</h1>
        </div>
    </div>

    {{-- SYSTEM ALERTS --}}
    @if(session('success'))
        <div class="inv-alert inv-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="inv-alert inv-alert--error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- FILTER BAR --}}
    <div class="inv-filter-section">
        <form method="GET" action="{{ route('resident.invoices.index') }}" class="inv-filter-form">
            <label for="status" class="inv-filter-label">Trạng thái:</label>
            <select name="status" id="status" class="inv-filter-select" onchange="this.form.submit()">
                <option value="">Tất cả hóa đơn</option>
                <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>⏳ Chưa thanh toán</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>✅ Đã thanh toán</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>🚨 Quá hạn</option>
            </select>
        </form>
    </div>

    {{-- EMPTY STATE --}}
    @if($invoices->isEmpty())
        <div class="inv-empty">
            <div class="inv-empty__icon-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <h3 class="inv-empty__title">Không tìm thấy hóa đơn</h3>
            <p class="inv-empty__desc">Hiện tại căn hộ của bạn không có hóa đơn nào phù hợp với bộ lọc đã chọn.</p>
        </div>
    @else
        {{-- INVOICES LIST --}}
        <div class="inv-list">
            @foreach($invoices as $invoice)
                @php
                    $isPending = in_array($invoice->status, ['unpaid', 'overdue']);
                @endphp
                <div class="inv-card inv-card--{{ $invoice->status }}">
                    <div class="inv-card__accent"></div>

                    {{-- Icon type --}}
                    <div class="inv-card__icon-wrap">
                        @if($invoice->type === 'electricity')
                            ⚡
                        @elseif($invoice->type === 'water')
                            💧
                        @elseif($invoice->type === 'parking')
                            🚗
                        @elseif($invoice->type === 'internet')
                            🌐
                        @else
                            📄
                        @endif
                    </div>

                    {{-- Body info --}}
                    <div class="inv-card__body">
                        <h3 class="inv-card__title">{{ $invoice->title }}</h3>
                        <p class="inv-card__subtitle">
                            Căn hộ: {{ $invoice->apartment->apartment_number ?? '—' }} 
                            ({{ optional(optional($invoice->apartment)->floor)->block->name ?? '' }})
                        </p>
                        
                        {{-- Chi tiết hóa đơn --}}
                        @if($invoice->details->isNotEmpty())
                            <div class="inv-card__details">
                                @foreach($invoice->details as $detail)
                                    <span class="inv-card__detail-badge">
                                        {{ $detail->servicePrice->name ?? 'Phí khác' }}: {{ number_format($detail->amount) }}đ
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <div class="inv-card__meta">
                            <span class="inv-meta-item">
                                Kỳ thanh toán: {{ str_pad($invoice->billing_month->month, 2, '0', STR_PAD_LEFT) }}/{{ $invoice->billing_year }}
                            </span>
                            <span class="inv-meta-item">
                                Hạn chót: {{ $invoice->due_date->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Amount & actions --}}
                    <div class="inv-card__right">
                        <div class="inv-card__price {{ $isPending ? 'inv-card__price--pending' : '' }}">
                            {{ number_format($invoice->total_amount) }}đ
                        </div>

                        <div class="inv-card__status-box">
                            @if($invoice->status === 'paid')
                                <span class="inv-status inv-status--paid">Đã thanh toán</span>
                            @elseif($invoice->status === 'overdue')
                                <span class="inv-status inv-status--overdue">Quá hạn</span>
                            @else
                                <span class="inv-status inv-status--unpaid">Chưa thanh toán</span>
                            @endif
                        </div>

                        @if($isPending)
                            <form method="POST" action="{{ route('resident.invoices.pay', $invoice->id) }}" onsubmit="return confirm('Xác nhận thanh toán hóa đơn: {{ $invoice->title }}?')">
                                @csrf
                                <button type="submit" class="inv-btn inv-btn--primary">
                                    Thanh toán ngay
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        @if($invoices->hasPages())
            <div class="inv-pagination">
                {{ $invoices->links() }}
            </div>
        @endif
    @endif
</div>

<style>
.inv-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 30px 20px;
}

.inv-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.inv-eyebrow {
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin: 0 0 4px 0;
    font-weight: 600;
}

.inv-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}

/* ALERTS */
.inv-alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 0.95rem;
    font-weight: 500;
}

.inv-alert--success {
    background-color: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.inv-alert--error {
    background-color: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

/* FILTERS */
.inv-filter-section {
    background-color: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 18px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

.inv-filter-form {
    display: flex;
    align-items: center;
    gap: 10px;
}

.inv-filter-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: #475569;
}

.inv-filter-select {
    padding: 6px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.9rem;
    color: #1e293b;
    outline: none;
    cursor: pointer;
    background-color: #f8fafc;
}

.inv-filter-select:focus {
    border-color: #3b82f6;
}

/* EMPTY STATE */
.inv-empty {
    text-align: center;
    padding: 50px 20px;
    background-color: #fff;
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    margin-top: 10px;
}

.inv-empty__icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background-color: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px auto;
}

.inv-empty__title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin: 0 0 6px 0;
}

.inv-empty__desc {
    font-size: 0.9rem;
    color: #64748b;
    max-width: 400px;
    margin: 0 auto;
}

/* CARD LIST */
.inv-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.inv-card {
    background-color: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    padding: 16px 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.inv-card__accent {
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    width: 4px;
}

/* Accents by status */
.inv-card--paid .inv-card__accent { background-color: #10b981; }
.inv-card--unpaid .inv-card__accent { background-color: #f59e0b; }
.inv-card--overdue .inv-card__accent { background-color: #ef4444; }

.inv-card__icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background-color: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-right: 18px;
    flex-shrink: 0;
}

.inv-card__body {
    flex: 1;
    min-width: 0;
}

.inv-card__title {
    font-size: 1.05rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 4px 0;
}

.inv-card__subtitle {
    font-size: 0.85rem;
    color: #64748b;
    margin: 0 0 8px 0;
}

.inv-card__details {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 8px;
}

.inv-card__detail-badge {
    font-size: 0.75rem;
    background-color: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #475569;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 500;
}

.inv-card__meta {
    display: flex;
    gap: 15px;
}

.inv-meta-item {
    font-size: 0.8rem;
    color: #64748b;
}

/* RIGHT SECTION */
.inv-card__right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
    margin-left: 20px;
    flex-shrink: 0;
}

.inv-card__price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
}

.inv-card__price--pending {
    color: #dc2626;
}

.inv-card__status-box {
    margin-bottom: 2px;
}

.inv-status {
    font-size: 0.75rem;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 4px;
    display: inline-block;
}

.inv-status--paid {
    background-color: #dcfce7;
    color: #15803d;
}

.inv-status--unpaid {
    background-color: #fef3c7;
    color: #b45309;
}

.inv-status--overdue {
    background-color: #fee2e2;
    color: #b91c1c;
}

/* BUTTONS */
.inv-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    outline: none;
}

.inv-btn--primary {
    background-color: #2563eb;
    color: #fff;
}

.inv-btn--primary:hover {
    background-color: #1d4ed8;
}

/* PAGINATION */
.inv-pagination {
    margin-top: 25px;
    display: flex;
    justify-content: center;
}
</style>
@endsection
