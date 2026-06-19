@extends('layouts.resident.master')

@section('title', 'Tạo QR mời khách – DomusHub')

@push('styles')
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

<script>
function setExpiry(hours) {
    const d = new Date();
    d.setTime(d.getTime() + hours * 3600000);
    const pad = n => String(n).padStart(2, '0');
    const val = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    document.querySelector('input[name="expired_at"]').value = val;
}

document.getElementById('visitor-form').addEventListener('submit', function() {
    const btn = document.getElementById('submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="spin-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg> Đang tạo QR...';
});
</script>

<style>
.spin-icon { animation: spin .7s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endsection
