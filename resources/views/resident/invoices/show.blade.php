@extends('layouts.resident.master')

@section('title', 'Thanh toán hóa đơn – DomusHub')

@section('content')
<div class="pay-page">
    {{-- Header Row --}}
    <div class="pay-header-row">
        <div>
            <h1 class="pay-page__title">Thanh toán hóa đơn</h1>
            <p class="pay-page__subtitle">Vui lòng kiểm tra và hoàn tất các khoản thanh toán định kỳ cho căn hộ của bạn.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="pay-alert pay-alert--error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    @if(session('success'))
        <div class="pay-alert pay-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($invoices->isEmpty())
        <div class="pay-empty">
            <div class="pay-empty__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <h3 class="pay-empty__title">Tuyệt vời!</h3>
            <p class="pay-empty__desc">Căn hộ của bạn không có khoản phí nào cần thanh toán lúc này.</p>
        </div>
    @else
        <form method="POST" action="{{ route('resident.invoices.pay') }}" id="payment-form">
            @csrf

            <div class="pay-card-container">
                {{-- Select All Header --}}
                <div class="pay-select-header">
                    <label class="pay-checkbox">
                        <input type="checkbox" id="select-all" checked>
                        <span class="pay-checkbox__box">
                            <svg viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 5L4.5 8.5L11 1.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="pay-checkbox__label">Danh sách dịch vụ</span>
                    </label>
                    <span class="pay-header-month">
                        Tháng {{ str_pad($invoices->first()->billing_month->month, 2, '0', STR_PAD_LEFT) }}/{{ $invoices->first()->billing_year }}
                    </span>
                </div>

                {{-- Invoice List --}}
                <div class="pay-list">
                    @foreach($invoices as $invoice)
                        @php
                            $title = mb_strtolower($invoice->title ?? '');
                            $iconClass = 'icon--default';
                            $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>';
                            $monthStr = str_pad($invoice->billing_month->month, 2, '0', STR_PAD_LEFT);
                            $yearStr = $invoice->billing_year;
                            $firstDetail = $invoice->details->first();
                            $quantity = $firstDetail ? (int)$firstDetail->quantity : null;
                            $subtitle = 'Kỳ thanh toán: Tháng ' . $monthStr . '/' . $yearStr;
                            
                            if(str_contains($title, 'điện')) {
                                $iconClass = 'icon--electric';
                                $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>';
                                $qVal = $quantity ?? 450;
                                $subtitle = "Sử dụng: {$qVal} kWh | Kỳ: 01/{$monthStr} - " . $invoice->billing_month->endOfMonth()->format('d/m');
                            } elseif(str_contains($title, 'nước')) {
                                $iconClass = 'icon--water';
                                $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M12 2c-5.33 7.23-8 11.23-8 14a8 8 0 1 0 16 0c0-2.77-2.67-6.77-8-14zm0 15c-1.66 0-3-1.34-3-3 0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5c0 .55.45 1 1 1s1-.45 1-1c0-2.21-1.79-4-4-4-.55 0-1-.45-1-1s.45-1 1-1c3.31 0 6 2.69 6 6 0 1.66-1.34 3-3 3z"/></svg>';
                                $qVal = $quantity ?? 24;
                                $subtitle = "Sử dụng: {$qVal} m³ | Kỳ: 01/{$monthStr} - " . $invoice->billing_month->endOfMonth()->format('d/m');
                            } elseif(str_contains($title, 'quản lý') || str_contains($title, 'phí quản')) {
                                $iconClass = 'icon--management';
                                $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M19 2H5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-9 18H5v-4h5v4zm0-6H5v-4h5v4zm0-6H5V4h5v4zm9 12h-5v-4h5v4zm0-6h-5v-4h5v4zm0-6h-5V4h5v4z"/></svg>';
                                $area = $invoice->apartment->area ?? 95;
                                $subtitle = "Diện tích: {$area} m² | Tháng {$monthStr}/{$yearStr}";
                            } elseif(str_contains($title, 'xe') || str_contains($title, 'parking')) {
                                $iconClass = 'icon--parking';
                                $iconSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1-1.5 1zm11 0c-.83 0-1.5-.67-1.5-1s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1-1.5 1zM5 11l1.27-3.82c.14-.4.52-.68.96-.68h9.54c.44 0 .82.28.96.68L19 11H5z"/></svg>';
                                $subtitle = "1 Ô tô, 2 Xe máy | Tháng {$monthStr}/{$yearStr}";
                            }
                        @endphp
                        <div class="pay-item" data-amount="{{ $invoice->total_amount }}">
                            <label class="pay-checkbox pay-checkbox--item">
                                <input type="checkbox" name="invoice_ids[]" value="{{ $invoice->id }}" checked class="invoice-checkbox">
                                <span class="pay-checkbox__box">
                                    <svg viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 5L4.5 8.5L11 1.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </label>
                            
                            <div class="pay-item__icon">
                                <span class="{{ $iconClass }}">{!! $iconSvg !!}</span>
                            </div>

                            <div class="pay-item__info">
                                <h4 class="pay-item__name">{{ $invoice->title }}</h4>
                                <p class="pay-item__meta">{{ $subtitle }}</p>
                            </div>

                            <div class="pay-item__right-side">
                                <span class="pay-item__amount">{{ number_format($invoice->total_amount, 0, ',', '.') }} đ</span>
                                @if($invoice->status === 'overdue')
                                    <span class="pay-badge pay-badge--overdue">Quá hạn</span>
                                @else
                                    <span class="pay-badge pay-badge--unpaid">Chờ thanh toán</span>
                                @endif
                            </div>

                            <div class="pay-item__arrow">
                                <a href="{{ route('resident.invoices.show', $invoice->id) }}" class="pay-item__detail-link" title="Xem chi tiết">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Bottom Summary Card --}}
                <div class="pay-footer-summary">
                    <div class="pay-footer-summary__count">
                        Đã chọn: <strong id="invoice-count">0 dịch vụ</strong>
                    </div>
                    
                    <div class="pay-footer-summary__total">
                        <span class="pay-total-label">TỔNG TIỀN THANH TOÁN</span>
                        <span class="pay-total-val" id="total-amount">0 đ</span>
                    </div>

                    <div class="pay-footer-summary__btn">
                        <button type="submit" class="pay-submit-btn" id="pay-btn">
                            Tiến hành thanh toán
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    @endif

    {{-- Bottom History Link Section --}}
    <div class="pay-history-link-wrapper">
        <a href="{{ route('resident.invoices.history') }}" class="pay-history-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><polyline points="3 3 3 8 8 8"/><line x1="12" y1="7" x2="12" y2="12"/><line x1="12" y1="12" x2="16" y2="14"/></svg>
            Xem lịch sử hóa đơn các tháng trước
            <span class="chevron-right">&gt;</span>
        </a>
    </div>
</div>

@include('resident.invoices.partials.style')

@include('resident.invoices.partials.script')
@endsection
