@extends('layouts.resident.master')

@section('title', 'Tạo QR mời khách – DomusHub')

@push('styles')
<style>
.spin-icon { animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>

    @vite(['resources/css/resident/visitors.css'])
@endpush

@section('content')
<div class="vq">

    {{-- HEADER --}}
    <div class="vq__header">
        <div>
            <p class="vq__eyebrow">QR mời khách</p>
            <h1 class="vq__title">Tạo QR mới</h1>
        </div>
        <a href="{{ route('resident.visitors.index') }}" class="vq-btn vq-btn--outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại
        </a>
    </div>

    {{-- FORM CARD --}}
    <div class="vq-form-card">
        <div class="vq-form-card__header">
            <h2 class="vq-form-card__title">Thông tin khách</h2>
        </div>

        <div class="vq-form-card__body">

            {{-- VALIDATION ERRORS --}}
            @if($errors->any())
                <div class="vq-alert vq-alert--error">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <ul style="margin:0;padding-left:1rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- INFO BOX --}}
            <div class="vq-alert vq-alert--info" style="margin-bottom:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <div>Hệ thống sẽ sinh mã QR tự động. Bạn gửi QR cho khách — bảo vệ quét QR để xác thực khi vào cổng.</div>
            </div>

            <form method="POST" action="{{ route('resident.visitors.store') }}" id="visitor-form">
                @csrf

                {{-- Tên khách & SĐT --}}
                <div class="vq-grid-2">
                    <div>
                        <label class="vq-label">Tên khách <span>*</span></label>
                        <input type="text" name="guest_name" class="vq-input @error('guest_name') vq-input--err @enderror"
                               value="{{ old('guest_name') }}" placeholder="Ví dụ: Nguyễn Văn An" required>
                    </div>
                    <div>
                        <label class="vq-label">Số điện thoại</label>
                        <input type="text" name="guest_phone" class="vq-input @error('guest_phone') vq-input--err @enderror"
                               value="{{ old('guest_phone') }}" placeholder="Ví dụ: 0912 345 678">
                    </div>
                </div>

                {{-- Thời gian hợp lệ đến --}}
                <div>
                    <label class="vq-label">Thời gian QR còn hiệu lực đến <span>*</span></label>
                    <input type="datetime-local" name="expired_at"
                           class="vq-input @error('expired_at') vq-input--err @enderror"
                           value="{{ old('expired_at', now()->addHours(4)->format('Y-m-d\TH:i')) }}"
                           min="{{ now()->format('Y-m-d\TH:i') }}"
                           required>
                    <p style="font-size:.78rem;color:#64748b;margin-top:.35rem;">Mặc định: 4 giờ kể từ bây giờ.</p>
                </div>

                {{-- Quick time presets --}}
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                    <span style="font-size:.8rem;color:#64748b;align-self:center;">Nhanh:</span>
                    @foreach([
                        ['1 giờ', 1],
                        ['3 giờ', 3],
                        ['8 giờ', 8],
                        ['1 ngày', 24],
                        ['3 ngày', 72],
                    ] as [$label, $hours])
                        <button type="button" class="vq-btn vq-btn--sm vq-btn--outline preset-btn"
                                data-hours="{{ $hours }}" onclick="setExpiry({{ $hours }})">{{ $label }}</button>
                    @endforeach
                </div>

                {{-- Ghi chú --}}
                <div>
                    <label class="vq-label">Ghi chú thêm</label>
                    <textarea name="note" class="vq-input @error('note') vq-input--err @enderror"
                              rows="3" placeholder="Ví dụ: Khách đến sửa điện, gặp chủ hộ tầng 5...">{{ old('note') }}</textarea>
                </div>

                {{-- Phương tiện khách --}}
                <div style="border-top:1px solid #e2e8f0;padding-top:1.25rem;">
                    <label class="vq-label" style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
                        <input type="checkbox" id="has-vehicle-toggle" onchange="toggleVehicleFields()"
                               {{ old('vehicle_plate') ? 'checked' : '' }}
                               style="width:16px;height:16px;accent-color:#00236f;">
                        <span>Khách có mang phương tiện</span>
                    </label>
                    <p style="font-size:.78rem;color:#64748b;margin-top:.25rem;">Khai báo để bảo vệ cho phép xe khách vào bãi giữ.</p>
                </div>

                <div id="vehicle-fields" style="display:{{ old('vehicle_plate') ? 'block' : 'none' }};">
                    <div class="vq-grid-2">
                        <div>
                            <label class="vq-label">Biển số xe</label>
                            <input type="text" name="vehicle_plate" class="vq-input @error('vehicle_plate') vq-input--err @enderror"
                                   value="{{ old('vehicle_plate') }}" placeholder="VD: 29A-12345"
                                   style="text-transform:uppercase;">
                        </div>
                        <div>
                            <label class="vq-label">Loại xe</label>
                            <select name="vehicle_type" class="vq-input @error('vehicle_type') vq-input--err @enderror">
                                <option value="">— Chọn loại xe —</option>
                                <option value="motorbike" {{ old('vehicle_type') === 'motorbike' ? 'selected' : '' }}>Xe máy</option>
                                <option value="electric_bike" {{ old('vehicle_type') === 'electric_bike' ? 'selected' : '' }}>Xe điện</option>
                                <option value="car" {{ old('vehicle_type') === 'car' ? 'selected' : '' }}>Ô tô</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="vq-form-actions">
                    <a href="{{ route('resident.visitors.index') }}" class="vq-btn vq-btn--outline">Hủy bỏ</a>
                    <button type="submit" class="vq-btn vq-btn--primary" id="submit-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg>
                        Tạo QR mời khách
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@endsection
@push('scripts')
    @vite(['resources/js/pages/resident/visitors/create.js'])
@endpush
