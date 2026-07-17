@extends('layouts.resident.master')

@section('title', 'Hóa đơn – DomusHub')

@section('content')
<div class="inv-container">

    {{-- HEADER --}}
    <div class="inv-header">
        <div>
            <p class="inv-eyebrow">Tài chính căn hộ</p>
            <h1 class="inv-title">Lịch sử hóa đơn</h1>
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
                <div class="inv-card inv-card--paid">
                    <div class="inv-card__accent"></div>



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
                            @if($invoice->status === 'paid' && $invoice->payments->isNotEmpty())
                                @php $payment = $invoice->payments->first(); @endphp
                                <span class="inv-meta-item">
                                    Thanh toán lúc: {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '—' }}
                                </span>
                                @if($payment->payment_method === 'vnpay')
                                <span class="inv-meta-item">
                                    Mã GD: <strong style="color: #0f172a;">{{ $payment->vnp_txn_ref ?: explode('|', $payment->transaction_code)[0] }}</strong>
                                </span>
                                @endif
                            @else
                                <span class="inv-meta-item">
                                    Hạn chót: {{ $invoice->due_date->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Amount & actions --}}
                    <div class="inv-card__right">
                        <div class="inv-card__price">
                            {{ number_format($invoice->total_amount) }}đ
                        </div>

                        <div class="inv-card__status-box">
                            <span class="inv-status inv-status--paid">Đã thanh toán</span>
                        </div>

                        <a href="{{ route('resident.invoices.show', $invoice->id) }}" class="inv-btn" style="background-color: #f1f5f9; color: #475569;">
                            Xem chi tiết
                        </a>
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

@include('resident.invoices.partials.style')
@endsection
