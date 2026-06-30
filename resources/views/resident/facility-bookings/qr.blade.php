@extends('layouts.resident.master')

@section('title', 'QR Check-in – ' . $booking->facility->name . ' – DomusHub')

@section('content')
<div class="qr-page">

    {{-- Breadcrumb --}}
    <div class="qr-breadcrumb">
        <a href="{{ route('resident.facility-bookings.index') }}">Lịch đặt của tôi</a>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        <span>QR Check-in</span>
    </div>

    <div class="qr-layout">

        {{-- QR Card --}}
        <div class="qr-card">
            <div class="qr-card-header">
                <div class="qr-card-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Đã duyệt
                </div>
                <h1 class="qr-facility-name">{{ $booking->facility->name }}</h1>
                <p class="qr-booking-id">Mã đặt lịch #{{ $booking->id }}</p>
            </div>

            {{-- QR Code --}}
            @php
                $needsPayment = $booking->amount > 0 && ($booking->payment_status !== 'paid' || !in_array($booking->payment_method, ['bank_transfer', 'vnpay']));
            @endphp
            @if($needsPayment)
            <div class="qr-payment-required" style="text-align:center; padding: 24px 16px; background: #fffbeb; border: 1.5px dashed #fde68a; border-radius: 12px; margin-bottom: 24px;">
                <div style="font-size: 2.2rem; margin-bottom: 8px;">💳</div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #92400e; margin: 0 0 6px;">Yêu cầu thanh toán</h3>
                <p style="font-size: 0.82rem; color: #78350f; line-height: 1.5; margin: 0 0 16px;">Vui lòng hoàn tất thanh toán phí sử dụng qua trang <strong>Hóa đơn</strong> để hiển thị mã QR check-in.</p>
                <a href="{{ route('resident.invoices.index') }}" class="qr-btn qr-btn--print" style="justify-content: center; box-shadow: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Đến trang Hóa đơn
                </a>
            </div>
            @else
            <div class="qr-code-wrap">
                <div class="qr-code-inner" id="qr-container">
                    {{-- QR image from Google Charts API (no dependency needed) --}}
                    <img
                        src="https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={{ urlencode($booking->qr_code) }}&bgcolor=ffffff&color=0f172a&qzone=1"
                        alt="QR Code Check-in"
                        id="qr-img"
                        class="qr-img"
                    >
                </div>
                <p class="qr-code-text">{{ $booking->qr_code }}</p>
            </div>
            @endif

            {{-- Booking details --}}
            <div class="qr-details">
                <div class="qr-detail-row">
                    <span class="qr-detail-label">Ngày sử dụng</span>
                    <span class="qr-detail-value">
                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                        ({{ \Carbon\Carbon::parse($booking->booking_date)->locale('vi')->dayName }})
                    </span>
                </div>
                <div class="qr-detail-row">
                    <span class="qr-detail-label">Khung giờ</span>
                    <span class="qr-detail-value qr-time">
                        {{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}
                    </span>
                </div>
                <div class="qr-detail-row">
                    <span class="qr-detail-label">Số người</span>
                    <span class="qr-detail-value">{{ $booking->number_of_people ?? 1 }} người</span>
                </div>
                @php $amount = $booking->amount; @endphp
                @if($amount > 0)
                <div class="qr-detail-row">
                    <span class="qr-detail-label">Phí sử dụng</span>
                    <span class="qr-detail-value qr-price">{{ number_format($amount) }}đ</span>
                </div>
                <div class="qr-detail-row">
                    <span class="qr-detail-label">Thanh toán</span>
                    <span class="qr-detail-value">
                        @if($booking->payment_status === 'paid')
                            <span class="qr-paid">✓ Đã thanh toán ({{ $booking->payment_method }})</span>
                        @else
                            <span class="qr-unpaid">Chưa thanh toán</span>
                        @endif
                    </span>
                </div>
                @endif
            </div>

            {{-- Hướng dẫn check-in --}}
            <div class="qr-guide">
                <div class="qr-guide-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Hướng dẫn check-in
                </div>
                <ul class="qr-guide-list">
                    <li>Đến đúng khung giờ đã đặt ({{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }})</li>
                    <li>Xuất trình mã QR này cho nhân viên quét tại tiện ích</li>
                    <li>Mã QR chỉ có hiệu lực vào đúng ngày đặt ({{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }})</li>
                </ul>
            </div>

            {{-- Actions --}}
            <div class="qr-actions">
                @if(!$needsPayment)
                <button onclick="window.print()" class="qr-btn qr-btn--print">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    In QR
                </button>
                @endif
                <a href="{{ route('resident.facility-bookings.index') }}" class="qr-btn qr-btn--back">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
                    Quay lại
                </a>
            </div>
        </div>

        {{-- Reminder sidebar --}}
        <div class="qr-reminder">
            <div class="qr-countdown" id="countdown-card">
                @php
                    $bookingDateTime = \Carbon\Carbon::parse($booking->booking_date->format('Y-m-d') . ' ' . $booking->start_time);
                    $isToday = $booking->booking_date->isToday();
                    $diffDays = now()->diffInDays($booking->booking_date, false);
                @endphp
                @if($isToday)
                <div class="qr-countdown-today">
                    <div class="qr-countdown-pulse"></div>
                    <p class="qr-countdown-label">Lịch sử dụng hôm nay!</p>
                    <p class="qr-countdown-time" id="live-time"></p>
                </div>
                @else
                <div class="qr-countdown-future">
                    <div class="qr-countdown-days">{{ max(0, $diffDays) }}</div>
                    <div class="qr-countdown-label">ngày nữa</div>
                </div>
                @endif
                <p class="qr-countdown-date">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }} – {{ substr($booking->start_time, 0, 5) }}</p>
            </div>

            <div class="qr-notice">
                <div class="qr-notice-item qr-notice--warn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Không chia sẻ QR cho người khác
                </div>
                <div class="qr-notice-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Có hiệu lực đúng ngày đặt
                </div>
                <div class="qr-notice-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    Đến đúng giờ để check-in
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.qr-page { max-width: 860px; margin: 0 auto; padding: 28px 20px; font-family: 'Inter', sans-serif; }

.qr-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; color: #64748b; margin-bottom: 24px; }
.qr-breadcrumb a { color: #2563eb; text-decoration: none; font-weight: 500; }
.qr-breadcrumb a:hover { text-decoration: underline; }

.qr-layout { display: grid; grid-template-columns: 1fr 240px; gap: 20px; align-items: start; }

/* QR Card */
.qr-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 32px; box-shadow: 0 4px 24px rgba(0,0,0,0.07); }

.qr-card-header { text-align: center; margin-bottom: 24px; }
.qr-card-badge { display: inline-flex; align-items: center; gap: 6px; background: #dcfce7; color: #15803d; font-size: 0.75rem; font-weight: 700; padding: 5px 12px; border-radius: 20px; margin-bottom: 14px; }
.qr-facility-name { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
.qr-booking-id { font-size: 0.82rem; color: #94a3b8; margin: 0; }

/* QR Code */
.qr-code-wrap { display: flex; flex-direction: column; align-items: center; margin-bottom: 24px; }
.qr-code-inner { padding: 20px; background: #fff; border: 2px solid #e2e8f0; border-radius: 16px; display: inline-block; box-shadow: 0 2px 16px rgba(0,0,0,0.08); }
.qr-img { width: 220px; height: 220px; display: block; }
.qr-code-text { font-size: 0.68rem; color: #94a3b8; font-family: monospace; margin-top: 12px; word-break: break-all; text-align: center; }

/* Details */
.qr-details { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-bottom: 20px; display: flex; flex-direction: column; gap: 10px; }
.qr-detail-row { display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; }
.qr-detail-label { color: #64748b; font-weight: 500; }
.qr-detail-value { font-weight: 700; color: #0f172a; }
.qr-time { font-family: monospace; font-size: 1rem; color: #2563eb; }
.qr-price { color: #d97706; }
.qr-paid { color: #15803d; font-size: 0.82rem; }
.qr-unpaid { color: #dc2626; font-size: 0.82rem; }

/* Guide */
.qr-guide { background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 14px 16px; margin-bottom: 24px; }
.qr-guide-title { display: flex; align-items: center; gap: 7px; font-size: 0.82rem; font-weight: 700; color: #92400e; margin-bottom: 10px; }
.qr-guide-list { margin: 0; padding-left: 18px; display: flex; flex-direction: column; gap: 6px; }
.qr-guide-list li { font-size: 0.82rem; color: #78350f; line-height: 1.5; }

/* Actions */
.qr-actions { display: flex; gap: 10px; }
.qr-btn { display: inline-flex; align-items: center; gap: 7px; padding: 11px 20px; border-radius: 10px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
.qr-btn--print { background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; box-shadow: 0 3px 10px rgba(59,130,246,0.3); }
.qr-btn--print:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(59,130,246,0.4); }
.qr-btn--back { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
.qr-btn--back:hover { background: #f1f5f9; }

/* Sidebar */
.qr-countdown { background: linear-gradient(135deg, #1e293b, #334155); border-radius: 16px; padding: 24px; text-align: center; color: #fff; margin-bottom: 14px; }
.qr-countdown-today { }
.qr-countdown-pulse { width: 14px; height: 14px; background: #22c55e; border-radius: 50%; margin: 0 auto 12px; animation: pulse 2s infinite; box-shadow: 0 0 0 6px rgba(34,197,94,0.2); }
@keyframes pulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.2);opacity:0.7} }
.qr-countdown-label { font-size: 0.82rem; font-weight: 600; color: #94a3b8; margin: 0 0 6px; }
.qr-countdown-time { font-size: 1.5rem; font-weight: 800; color: #fff; margin: 0; font-family: monospace; }
.qr-countdown-future { }
.qr-countdown-days { font-size: 3.5rem; font-weight: 900; color: #fff; line-height: 1; }
.qr-countdown-date { font-size: 0.78rem; color: #64748b; margin-top: 10px; }

.qr-notice { display: flex; flex-direction: column; gap: 8px; }
.qr-notice-item { display: flex; align-items: flex-start; gap: 8px; font-size: 0.8rem; color: #475569; padding: 10px 12px; background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; }
.qr-notice--warn { color: #d97706; background: #fffbeb; border-color: #fde68a; }

@media (max-width: 640px) {
    .qr-layout { grid-template-columns: 1fr; }
}

@media print {
    .qr-breadcrumb, .qr-actions, .qr-reminder, .qr-guide { display: none !important; }
    .qr-layout { grid-template-columns: 1fr; }
    .qr-page { padding: 0; }
    .qr-card { box-shadow: none; border: none; }
}
</style>

<script>
// Live clock nếu là hôm nay
const isToday = {{ $booking->booking_date->isToday() ? 'true' : 'false' }};
if (isToday) {
    const liveTime = document.getElementById('live-time');
    if (liveTime) {
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            liveTime.textContent = h + ':' + m + ':' + s;
        }
        updateClock();
        setInterval(updateClock, 1000);
    }
}
</script>
@endsection
