@extends('layouts.resident.master')

@section('title', 'Mã QR xe ' . $vehicle->license_plate . ' – DomusHub')

@push('styles')
    @vite(['resources/css/resident/vehicles-index.css'])
@endpush

@section('content')
<div class="vp">

    {{-- Header --}}
    <div class="vp__header">
        <div>
            <p class="vp__eyebrow">Phương tiện</p>
            <h1 class="vp__title">Mã QR ra vào</h1>
        </div>

        <a href="{{ route('resident.vehicles.index') }}" class="vp-btn vp-btn--outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại
        </a>
    </div>

    {{-- QR Card --}}
    <div class="vp-qr-page">
        <div class="vp-qr-card">
            {{-- Vehicle Info --}}
            <div class="vp-qr-card__info">
                <h2 class="vp-qr-card__plate">{{ $vehicle->license_plate }}</h2>
                <div class="vp-qr-card__meta">
                    <span class="vp-card__tag">{{ $vehicle->typeLabel() }}</span>
                    @if($vehicle->brand)
                        <span class="vp-card__tag text-brand">{{ $vehicle->brand }}</span>
                    @endif
                    @if($vehicle->parkingLot)
                        <span class="vp-card__tag">Lốt {{ $vehicle->parkingLot->lot_number }}</span>
                    @endif
                </div>

                @if($vehicle->status === 'pending_renewal')
                    <div class="vp-qr-card__warning">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                        <span>Xe đang chờ gia hạn phí — vẫn được ra vào nhưng cần thanh toán sớm.</span>
                    </div>
                @endif
            </div>

            {{-- QR Code --}}
            <div class="vp-qr-card__qr-wrap">
                <img src="{{ asset('storage/' . $vehicle->qr_code) }}" 
                     alt="QR Code {{ $vehicle->license_plate }}"
                     class="vp-qr-card__qr-img">
            </div>

            <p class="vp-qr-card__hint">
                Đưa mã QR này cho bảo vệ quét khi ra vào hầm xe.
            </p>
        </div>
    </div>
</div>

<style>
.vp-qr-page {
    display: flex;
    justify-content: center;
    padding: 1rem 0;
}

.vp-qr-card {
    background: var(--white, #fff);
    border: 1px solid var(--gray-200, #e5e7eb);
    border-radius: 16px;
    padding: 2rem;
    max-width: 420px;
    width: 100%;
    text-align: center;
    box-shadow: 0 4px 24px rgba(0,0,0,.06);
}

.vp-qr-card__info {
    margin-bottom: 1.5rem;
}

.vp-qr-card__plate {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900, #111827);
    margin: 0 0 0.5rem;
    letter-spacing: 1px;
}

.vp-qr-card__meta {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.vp-qr-card__warning {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
    padding: 0.625rem 1rem;
    background: #fff8e1;
    border: 1px solid #ffd54f;
    border-radius: 8px;
    font-size: 0.8125rem;
    color: #856404;
    text-align: left;
}

.vp-qr-card__qr-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 12px;
    margin-bottom: 1rem;
}

.vp-qr-card__qr-img {
    width: 260px;
    height: 260px;
    object-fit: contain;
}

.vp-qr-card__hint {
    font-size: 0.875rem;
    color: var(--gray-500, #6b7280);
    margin: 0;
}

@media (max-width: 480px) {
    .vp-qr-card {
        padding: 1.25rem;
    }
    .vp-qr-card__qr-img {
        width: 200px;
        height: 200px;
    }
}
</style>
@endsection
