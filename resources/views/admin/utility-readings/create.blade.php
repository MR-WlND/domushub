@extends('layouts.admin.master')

@section('page_title', 'Ghi chỉ số đơn lẻ – DomusHub')

@push('styles')
@vite(['resources/css/pages/admin/utility-readings/index.css'])
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="util-page-header">
    <div>
        <h1>📝 Ghi chỉ số đơn lẻ</h1>
        <p>Nhập chỉ số mới cho một căn hộ</p>
    </div>
    <a href="{{ route('admin.utility-readings.index') }}" class="util-btn util-btn--outline">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Quay lại danh sách
    </a>
</div>

{{-- ── Error Alert ─────────────────────────────────── --}}
@if (isset($errors) && $errors->any())
    <div class="util-alert--danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ── Form Card ───────────────────────────────────── --}}
<div class="util-form-card" style="max-width: 760px;">

    <div class="form-section-header">
        <div class="section-number">1</div>
        <h4>Thông tin căn hộ</h4>
    </div>

    <form action="{{ route('admin.utility-readings.store') }}" method="POST" id="createForm">
        @csrf

        <div class="util-form-grid-2">
            {{-- Căn hộ --}}
            <div class="util-form-group" style="grid-column: span 2;">
                <label class="util-form-label">Căn hộ <span class="util-form-label .required" style="color:#ef4444">*</span></label>
                <select name="apartment_id" id="apartment_id"
                    class="util-form-input {{ $errors->has('apartment_id') ? 'util-form-input--error' : '' }}" required>
                    <option value="">— Chọn căn hộ —</option>
                    @foreach ($apartments as $apartment)
                        <option value="{{ $apartment->id }}" {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                            {{ $apartment->apartment_number }}
                            – {{ $apartment->floor->block->name ?? '' }}
                            / {{ $apartment->floor->name ?? 'Tầng ' . $apartment->floor->floor_number }}
                        </option>
                    @endforeach
                </select>
                @error('apartment_id')
                    <p class="util-form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-section-header" style="margin-top: 20px;">
            <div class="section-number">2</div>
            <h4>Thông tin chỉ số</h4>
        </div>

        <div class="util-form-grid-2">
            {{-- Loại --}}
            <div class="util-form-group">
                <label class="util-form-label">Loại <span style="color:#ef4444">*</span></label>
                <select name="type" id="type"
                    class="util-form-input {{ $errors->has('type') ? 'util-form-input--error' : '' }}" required>
                    <option value="">— Chọn loại —</option>
                    <option value="electricity" {{ old('type') == 'electricity' ? 'selected' : '' }}>⚡ Điện</option>
                    <option value="water" {{ old('type') == 'water' ? 'selected' : '' }}>💧 Nước</option>
                </select>
                @error('type')
                    <p class="util-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tháng/Năm --}}
            <div class="util-form-group">
                <label class="util-form-label">Kỳ ghi <span style="color:#ef4444">*</span></label>
                <div style="display: flex; gap: 8px;">
                    <input type="number" name="record_month" id="record_month"
                        value="{{ old('record_month', now()->month) }}" min="1" max="12"
                        class="util-form-input {{ $errors->has('record_month') ? 'util-form-input--error' : '' }}"
                        placeholder="Tháng" required style="flex: 1;">
                    <input type="number" name="record_year" id="record_year"
                        value="{{ old('record_year', now()->year) }}" min="2020" max="2100"
                        class="util-form-input {{ $errors->has('record_year') ? 'util-form-input--error' : '' }}"
                        placeholder="Năm" required style="flex: 1.5;">
                </div>
            </div>
        </div>

        {{-- Chỉ số --}}
        <div class="util-form-grid-3" style="margin-top: 0;">
            <div class="util-form-group">
                <label class="util-form-label">Chỉ số cũ (tự động)</label>
                <input type="number" id="old_value_display" class="util-form-input util-form-input--readonly"
                    value="0" readonly>
                <p class="util-form-hint">Lấy tự động từ kỳ trước</p>
            </div>

            <div class="util-form-group">
                <label class="util-form-label">Chỉ số mới <span style="color:#ef4444">*</span></label>
                <input type="number" name="new_value" id="new_value"
                    value="{{ old('new_value') }}" min="0"
                    class="util-form-input {{ $errors->has('new_value') ? 'util-form-input--error' : '' }}"
                    placeholder="Nhập chỉ số mới" required>
                @error('new_value')
                    <p class="util-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="util-form-group">
                <label class="util-form-label">Tiêu thụ (tự tính)</label>
                <input type="text" id="usage_display" class="util-form-input util-form-input--readonly"
                    value="0" readonly style="font-weight: 700; color: #00236f;">
                <p class="util-form-hint">= Chỉ số mới – Chỉ số cũ</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="util-form-actions">
            <button type="submit" class="util-btn util-btn--primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                Lưu chỉ số
            </button>
            <a href="{{ route('admin.utility-readings.index') }}" class="util-btn util-btn--outline">Hủy</a>
        </div>

    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const apartmentId  = document.getElementById('apartment_id');
    const type         = document.getElementById('type');
    const recordMonth  = document.getElementById('record_month');
    const recordYear   = document.getElementById('record_year');
    const oldValueDisp = document.getElementById('old_value_display');
    const newValueInp  = document.getElementById('new_value');
    const usageDisp    = document.getElementById('usage_display');

    function fetchOldValue() {
        const aptId = apartmentId.value;
        const t     = type.value;
        const m     = recordMonth.value;
        const y     = recordYear.value;

        if (!aptId || !t || !m || !y) return;

        fetch(`{{ route('admin.utility-readings.get-old-value') }}?apartment_id=${aptId}&type=${t}&month=${m}&year=${y}`)
            .then(res => res.json())
            .then(data => {
                oldValueDisp.value = data.old_value ?? 0;
                calcUsage();
            })
            .catch(() => {
                oldValueDisp.value = 0;
                calcUsage();
            });
    }

    function calcUsage() {
        const oldVal = parseInt(oldValueDisp.value) || 0;
        const newVal = parseInt(newValueInp.value) || 0;
        const usage  = Math.max(0, newVal - oldVal);
        usageDisp.value = usage.toLocaleString('vi-VN');
        usageDisp.style.color = usage > 0 ? '#10b981' : '#94a3b8';
    }

    apartmentId.addEventListener('change', fetchOldValue);
    type.addEventListener('change', fetchOldValue);
    recordMonth.addEventListener('change', fetchOldValue);
    recordYear.addEventListener('change', fetchOldValue);
    newValueInp.addEventListener('input', calcUsage);

    if (apartmentId.value && type.value) fetchOldValue();
});
</script>
@endpush
