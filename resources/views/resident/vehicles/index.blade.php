@extends('layouts.resident.master')
@section('title', 'Phương tiện – DomusHub')
@push('styles')
    @vite(['resources/css/resident/vehicles.css'])
@endpush

@section('content')
<div class="rv">
    <div class="rv-header">
        <div>
            <h1 class="rv__title">Quản lý Phương tiện</h1>
            <p class="rv__subtitle">Quản lý phương tiện đã đăng ký, cập nhật trạng thái và tải mã QR truy cập bãi đỗ xe.</p>
        </div>
        <a href="{{ route('resident.vehicles.create') }}" class="rv-btn rv-btn--primary">+ Đăng ký xe mới</a>
    </div>

    @if(session('success'))
        <div class="rv-alert rv-alert--success">{{ session('success') }}</div>
    @endif

    @if($vehicles->isEmpty())
        <div style="text-align:center;padding:60px 20px;">
            <p style="color:#64748b;font-size:0.95rem;margin:0 0 16px;">Chưa có phương tiện nào được đăng ký.</p>
            <a href="{{ route('resident.vehicles.create') }}" class="rv-btn rv-btn--primary">Đăng ký xe ngay</a>
        </div>
    @else
        <div class="rv-stats">
            <div class="rv-stat">
                <div class="rv-stat__icon rv-stat__icon--blue">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                </div>
                <div>
                    <span class="rv-stat__label">Tổng số xe</span>
                    <div class="rv-stat__value">{{ str_pad($vehicles->count(), 2, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
            <div class="rv-stat">
                <div class="rv-stat__icon rv-stat__icon--green">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <span class="rv-stat__label">Đang hoạt động</span>
                    <div class="rv-stat__value">{{ str_pad($vehicles->where('status', 'active')->count(), 2, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </div>

        <div class="rv-content">
            <div>
                <p class="rv-content__title">Phương tiện hiện có</p>
                <div class="rv-list">
                    @foreach($vehicles as $i => $vehicle)
                    <div class="rv-item {{ $i === 0 ? 'rv-item--selected' : '' }}">
                        <div class="rv-item__thumb">
                            @if($vehicle->image)
                                <img src="{{ asset('storage/' . $vehicle->image) }}" alt="{{ $vehicle->license_plate }}" loading="lazy">
                            @else
                                <div class="rv-item__icon">
                                    @if($vehicle->vehicle_type === 'car')
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                                    @else
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M12 17h5M5 17h2M12 12l2.5-5.5H19l-3 7H9.7L7 11.5"/></svg>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="rv-item__info">
                            <span class="rv-item__name">{{ $vehicle->brand ?? ($vehicle->vehicle_type === 'car' ? 'Ô tô' : 'Xe máy') }}</span>
                            <div class="rv-item__meta">
                                <span class="rv-item__plate">{{ $vehicle->license_plate }}</span>
                                <span class="rv-item__status rv-item__status--{{ $vehicle->status }}">● @switch($vehicle->status)@case('active') Hoạt động @break @case('pending') Chờ duyệt @break @case('locked') Đã khóa @break @case('pending_renewal') Chờ gia hạn @break @default {{ $vehicle->status }} @endswitch</span>
                            </div>
                        </div>
                        @if($vehicle->qr_code && in_array($vehicle->status, ['active','pending_renewal']))
                        <a href="{{ route('resident.vehicles.qr', $vehicle) }}" class="rv-item__qr" title="Xem QR">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="5" height="5" x="3" y="3" rx="1"/><rect width="5" height="5" x="16" y="3" rx="1"/><rect width="5" height="5" x="3" y="16" rx="1"/><path d="M21 16h-3a2 2 0 0 0-2 2v3"/></svg>
                        </a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- QR Panel --}}
            @php $qrV = $vehicles->first(fn($v) => $v->qr_code && in_array($v->status, ['active','pending_renewal'])); @endphp
            <div>
                @if($qrV)
                <div class="rv-qr-panel">
                    <h3 class="rv-qr-panel__title">Mã QR Ra Vào</h3>
                    <p class="rv-qr-panel__desc">Sử dụng mã này tại các cổng an ninh để xác thực phương tiện tự động.</p>
                    <div class="rv-qr-panel__frame"><img id="rv-qr-img" src="{{ asset('storage/' . $qrV->qr_code) }}" alt="QR" loading="lazy"></div>
                    <div class="rv-qr-panel__info">
                        <div class="rv-qr-panel__row"><span>Phương tiện:</span><span id="rv-qr-brand">{{ $qrV->brand ?? 'N/A' }}</span></div>
                        <div class="rv-qr-panel__row"><span>Biển số:</span><span id="rv-qr-plate">{{ $qrV->license_plate }}</span></div>
                    </div>
                    <a id="rv-qr-link" href="{{ route('resident.vehicles.qr', $qrV) }}" class="rv-qr-panel__btn">Tải mã QR</a>
                </div>
                @else
                <div class="rv-qr-panel"><p class="rv-qr-panel__desc" style="padding:40px 0;">Chưa có mã QR. Xe cần được BQL duyệt.</p></div>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
document.querySelectorAll('.rv-item').forEach((item, index) => {
    item.style.cursor = 'pointer';
    item.addEventListener('click', function() {
        document.querySelectorAll('.rv-item').forEach(i => i.classList.remove('rv-item--selected'));
        this.classList.add('rv-item--selected');

        const vehicles = {!! $vehicles->map(fn($v) => ['brand' => $v->brand ?? 'N/A', 'plate' => $v->license_plate, 'qr' => $v->qr_code ? asset('storage/' . $v->qr_code) : null, 'qr_url' => $v->qr_code ? route('resident.vehicles.qr', $v->id) : '#'])->values()->toJson() !!};
        const v = vehicles[index];
        const img = document.getElementById('rv-qr-img');
        if (img && v.qr) {
            img.src = v.qr;
            document.getElementById('rv-qr-brand').textContent = v.brand;
            document.getElementById('rv-qr-plate').textContent = v.plate;
            document.getElementById('rv-qr-link').href = v.qr_url;
        }
    });
});
</script>
@endsection
