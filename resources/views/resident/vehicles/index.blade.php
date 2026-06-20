@extends('layouts.resident.master')

@section('title', 'Phương tiện – DomusHub')

@push('styles')
    @vite(['resources/css/resident/vehicles-index.css'])
@endpush

@section('content')
<div class="vp">

    {{-- Header --}}
    <div class="vp__header">
        <div>
            <p class="vp__eyebrow">Quản lý cư dân</p>
            <h1 class="vp__title">Phương tiện của tôi</h1>
        </div>

        <a href="{{ route('resident.vehicles.create') }}" class="vp-btn vp-btn--primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
            Đăng ký xe
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="vp-alert vp-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Empty State --}}
    @if($vehicles->isEmpty())
        <div class="vp-empty">
            <div class="vp-empty__visual">
                <div class="vp-empty__circle vp-empty__circle--outer"></div>
                <div class="vp-empty__circle vp-empty__circle--inner"></div>
                <div class="vp-empty__icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                </div>
            </div>
            <h3 class="vp-empty__title">Chưa có phương tiện đăng ký</h3>
            <p class="vp-empty__desc">Đăng ký phương tiện để được cấp mã QR ra vào hầm xe tự động.</p>
            <a href="{{ route('resident.vehicles.create') }}" class="vp-btn vp-btn--primary">
                Đăng ký xe ngay
            </a>
        </div>

    @else
        {{-- Stats --}}
        <div class="vp-stats">
            <div class="vp-stat stat--all">
                <div class="vp-stat__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                </div>
                <div>
                    <span class="vp-stat__num">{{ $vehicles->count() }}</span>
                    <span class="vp-stat__label">Tổng số xe</span>
                </div>
            </div>

            <div class="vp-stat stat--pending">
                <div class="vp-stat__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <span class="vp-stat__num color-pending">{{ $vehicles->where('status', 'pending')->count() }}</span>
                    <span class="vp-stat__label">Chờ duyệt</span>
                </div>
            </div>

            <div class="vp-stat stat--active">
                <div class="vp-stat__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <span class="vp-stat__num color-active">{{ $vehicles->where('status', 'active')->count() }}</span>
                    <span class="vp-stat__label">Hoạt động</span>
                </div>
            </div>

            <div class="vp-stat stat--inactive">
                <div class="vp-stat__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                </div>
                <div>
                    <span class="vp-stat__num color-inactive">{{ $vehicles->where('status', 'locked')->count() }}</span>
                    <span class="vp-stat__label">Đã khóa</span>
                </div>
            </div>
        </div>

        {{-- Vehicle List --}}
        <div class="vp-list">
            @foreach($vehicles as $vehicle)
                <div class="vp-card card--{{ $vehicle->status }}">
                    <div class="vp-card__accent"></div>

                    {{-- Thumbnail --}}
                    <div class="vp-card__thumb">
                        @if($vehicle->image)
                            <img src="{{ asset('storage/' . $vehicle->image) }}"
                                 alt="{{ $vehicle->license_plate }}"
                                 class="vp-card__img"
                                 onclick="openImageModal(this.src)">
                        @else
                            <div class="vp-card__icon-fallback">
                                @if($vehicle->vehicle_type === 'car')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                                @elseif($vehicle->vehicle_type === 'electric_bike')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M12 17h5M5 17h2M12 12l2.5-5.5H19l-3 7H9.7L7 11.5"/></svg>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="vp-card__body">
                        <h3 class="vp-card__plate">{{ $vehicle->license_plate }}</h3>
                        <div class="vp-card__meta">
                            <span class="vp-card__tag text-capitalize">
                                @if($vehicle->vehicle_type === 'car') Ô tô
                                @elseif($vehicle->vehicle_type === 'electric_bike') Xe điện
                                @else Xe máy
                                @endif
                            </span>
                            @if($vehicle->brand)
                                <span class="vp-card__tag text-brand">{{ $vehicle->brand }}</span>
                            @endif
                            <span class="vp-card__date">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:3px;"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                {{ $vehicle->created_at->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>

                    {{-- Right: Status + QR + Actions --}}
                    <div class="vp-card__right">
                        @if($vehicle->status === 'pending')
                            <span class="vp-badge badge--pending">Chờ duyệt</span>
                        @elseif($vehicle->status === 'active')
                            <span class="vp-badge badge--active">Hoạt động</span>
                        @elseif($vehicle->status === 'pending_renewal')
                            <span class="vp-badge badge--warning">Chờ gia hạn</span>
                        @elseif($vehicle->status === 'locked')
                            <span class="vp-badge badge--danger">Đã khóa</span>
                        @else
                            <span class="vp-badge badge--inactive">Đã hủy</span>
                        @endif

                        @if($vehicle->parking_lot_id)
                            <span class="vp-lot-tag">Lốt {{ $vehicle->parkingLot->lot_number }}</span>
                        @endif

                        @if(in_array($vehicle->status, ['active', 'pending_renewal']) && $vehicle->qr_code)
                            <a href="{{ route('resident.vehicles.qr', $vehicle) }}" class="vp-card__qr" title="Xem mã QR ra vào">
                                <img src="{{ asset('storage/' . $vehicle->qr_code) }}" alt="QR">
                            </a>
                            <a href="{{ route('resident.vehicles.qr', $vehicle) }}" class="vp-btn vp-btn--outline vp-btn--sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/><path d="M21 21v.01"/><path d="M12 7v3a2 2 0 0 1-2 2H7"/><path d="M3 12h.01"/><path d="M12 3h.01"/><path d="M12 16v.01"/><path d="M16 12h1"/><path d="M21 12v.01"/><path d="M12 21v-1"/></svg>
                                Xem QR
                            </a>
                        @endif

                        @if(in_array($vehicle->status, ['pending', 'active']))
                            <form method="POST"
                                  action="{{ route('resident.vehicles.destroy', $vehicle) }}"
                                  onsubmit="return confirm('Bạn có chắc muốn hủy đăng ký xe này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="vp-btn vp-btn--ghost text-danger">
                                    Hủy xe
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Lightbox --}}
<div id="vpImageModal" class="vp-img-modal" onclick="closeImageModal()">
    <span class="vp-img-modal__close">&times;</span>
    <img class="vp-img-modal__content" id="vpModalImg">
</div>

<script>
function openImageModal(src) {
    document.getElementById('vpImageModal').style.display = 'flex';
    document.getElementById('vpModalImg').src = src;
}
function closeImageModal() {
    document.getElementById('vpImageModal').style.display = 'none';
}
</script>
@endsection
