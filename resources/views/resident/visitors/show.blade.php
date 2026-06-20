@extends('layouts.resident.master')

@section('title', 'QR khách: {{ $visitor->guest_name }} – DomusHub')

@push('styles')
    @vite(['resources/css/resident/visitors.css'])
@endpush

@section('content')
<div class="vq">

    {{-- HEADER --}}
    <div class="vq__header">
        <div>
            <p class="vq__eyebrow">QR mời khách</p>
            <h1 class="vq__title">Chi tiết mã QR</h1>
        </div>
        <a href="{{ route('resident.visitors.index') }}" class="vq-btn vq-btn--outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại
        </a>
    </div>

    {{-- SUCCESS FLASH --}}
    @if(session('success'))
        <div class="vq-alert vq-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- MAIN CARD --}}
    <div class="vq-show">
        <div class="vq-qr-card">

            {{-- Status stripe --}}
            <div style="height:5px;background:{{ match($visitor->status) {
                'pending'     => 'linear-gradient(90deg,#3b82f6,#6366f1)',
                'checked_in'  => 'linear-gradient(90deg,#059669,#10b981)',
                'checked_out' => 'linear-gradient(90deg,#94a3b8,#cbd5e1)',
                'expired'     => 'linear-gradient(90deg,#f59e0b,#fbbf24)',
                'cancelled'   => 'linear-gradient(90deg,#ef4444,#f87171)',
                default       => '#e2e8f0'
            } }};"></div>

            {{-- Top section --}}
            <div class="vq-qr-card__top">

                {{-- Info --}}
                <div class="vq-qr-card__info">
                    <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.5rem;">
                        <h2 class="vq-guest-name">{{ $visitor->guest_name }}</h2>
                        <span class="vq-chip vq-chip--{{ $visitor->status }}">{{ $visitor->statusLabel() }}</span>
                    </div>

                    <ul class="vq-detail-list">
                        @if($visitor->guest_phone)
                        <li>
                            <span class="vq-detail-list__label">📞 Điện thoại</span>
                            <span class="vq-detail-list__val">{{ $visitor->guest_phone }}</span>
                        </li>
                        @endif
                        <li>
                            <span class="vq-detail-list__label">🏠 Căn hộ</span>
                            <span class="vq-detail-list__val">{{ $visitor->apartment?->unit_number ?? '—' }}</span>
                        </li>
                        <li>
                            <span class="vq-detail-list__label">⏰ Hết hạn lúc</span>
                            <span class="vq-detail-list__val {{ $visitor->isExpired() ? 'text-danger' : '' }}">
                                {{ $visitor->expired_at->format('H:i — d/m/Y') }}
                                @if($visitor->isExpired())
                                    <span style="color:#dc2626;font-size:.8rem;"> (Đã hết hạn)</span>
                                @endif
                            </span>
                        </li>
                        @if($visitor->check_in_at)
                        <li>
                            <span class="vq-detail-list__label">✅ Vào lúc</span>
                            <span class="vq-detail-list__val">{{ $visitor->check_in_at->format('H:i d/m/Y') }}</span>
                        </li>
                        @endif
                        @if($visitor->check_out_at)
                        <li>
                            <span class="vq-detail-list__label">🚪 Ra lúc</span>
                            <span class="vq-detail-list__val">{{ $visitor->check_out_at->format('H:i d/m/Y') }}</span>
                        </li>
                        @endif
                        @if($visitor->note)
                        <li>
                            <span class="vq-detail-list__label">📝 Ghi chú</span>
                            <span class="vq-detail-list__val">{{ $visitor->note }}</span>
                        </li>
                        @endif
                        @if($visitor->hasVehicle())
                        <li>
                            <span class="vq-detail-list__label">🚗 Xe khách</span>
                            <span class="vq-detail-list__val">{{ $visitor->vehicle_plate }} ({{ $visitor->vehicleTypeLabel() }})</span>
                        </li>
                        @endif
                    </ul>
                </div>

                {{-- QR Code --}}
                <div class="vq-qr-wrap">
                    @php $qrUrl = $visitor->qrImageUrl(); @endphp
                    @if($qrUrl)
                        <img src="{{ $qrUrl }}" alt="QR Code" class="vq-qr-img" id="qr-svg-img">
                    @else
                        {{-- JS fallback QR --}}
                        <div id="qr-js-container" style="width:180px;height:180px;border:3px solid #4f46e5;border-radius:12px;padding:8px;background:#fff;display:flex;align-items:center;justify-content:center;"></div>
                        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
                        <script>
                            new QRCode(document.getElementById('qr-js-container'), {
                                text: "{{ $visitor->qr_token }}",
                                width: 160,
                                height: 160,
                                colorDark: "#0f172a",
                                colorLight: "#ffffff",
                                correctLevel: QRCode.CorrectLevel.H
                            });
                        </script>
                    @endif
                    <p style="font-size:.75rem;color:#64748b;text-align:center;margin:0;max-width:160px;">Chụp màn hình hoặc bấm tải để gửi cho khách</p>

                    {{-- Download button --}}
                    @if($qrUrl)
                        <a href="{{ $qrUrl }}" download="QR_{{ $visitor->guest_name }}.svg" class="vq-btn vq-btn--sm vq-btn--outline" style="margin-top:.25rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Tải QR
                        </a>
                    @endif
                </div>
            </div>

            {{-- Tip box --}}
            @if($visitor->isValid())
            <div class="vq-tip" style="margin: 0 2rem 1.5rem;">
                <strong>Cách chia sẻ:</strong> Chụp màn hình hoặc tải file QR gửi qua Zalo/Messenger cho khách.
                Khi đến cổng, bảo vệ sẽ quét mã QR để xác thực.
            </div>
            @endif

            {{-- Actions --}}
            <div class="vq-qr-card__actions">
                <span style="font-size:.82rem;color:#64748b;">Token: <code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:.78rem;">{{ Str::limit($visitor->qr_token, 20) }}...</code></span>
                <div style="display:flex;gap:.5rem;">
                    @if($visitor->status === 'pending')
                        <form method="POST" action="{{ route('resident.visitors.destroy', $visitor->id) }}"
                              onsubmit="return confirm('Hủy QR mời khách này?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="vq-btn vq-btn--sm vq-btn--danger">
                                Hủy QR
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('resident.visitors.create') }}" class="vq-btn vq-btn--sm vq-btn--primary">
                        + Tạo QR mới
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
