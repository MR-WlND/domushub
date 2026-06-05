@extends('layouts.admin.master')

@section('page_title', 'Sửa chỉ số điện nước – DomusHub')

@push('styles')
@vite(['resources/css/pages/admin/utility-readings/index.css'])
@endpush

@section('content')

{{-- ── Page Header ─────────────────────────────────── --}}
<div class="util-page-header">
    <div>
        <h1>✏️ Sửa chỉ số điện nước</h1>
        <p>
            Căn hộ <strong>{{ $reading->apartment->apartment_number }}</strong>
            – {{ $reading->type === 'electricity' ? '⚡ Điện' : '💧 Nước' }}
            – Tháng {{ $reading->record_month }}/{{ $reading->record_year }}
        </p>
    </div>
    <a href="{{ route('admin.utility-readings.index', ['month' => $reading->record_month, 'year' => $reading->record_year]) }}"
        class="util-btn util-btn--outline">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Quay lại
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

{{-- ── Info Card ───────────────────────────────────── --}}
<div style="background: #f0f7ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; display: flex; gap: 20px; flex-wrap: wrap;">
    <div>
        <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Căn hộ</div>
        <div style="font-size: 16px; font-weight: 700; color: #00236f; margin-top: 2px;">{{ $reading->apartment->apartment_number }}</div>
    </div>
    <div>
        <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Tòa / Tầng</div>
        <div style="font-size: 14px; font-weight: 600; color: #334155; margin-top: 2px;">
            {{ $reading->apartment->floor->block->name ?? '—' }} / {{ $reading->apartment->floor->name ?? 'Tầng ' . $reading->apartment->floor->floor_number }}
        </div>
    </div>
    <div>
        <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Loại</div>
        <div style="margin-top: 4px;">
            @if ($reading->type === 'electricity')
                <span class="util-badge util-badge--electricity">⚡ Điện</span>
            @else
                <span class="util-badge util-badge--water">💧 Nước</span>
            @endif
        </div>
    </div>
    <div>
        <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Kỳ ghi</div>
        <div style="font-size: 14px; font-weight: 600; color: #334155; margin-top: 2px;">
            Tháng {{ $reading->record_month }}/{{ $reading->record_year }}
        </div>
    </div>
    <div>
        <div style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Người ghi</div>
        <div style="font-size: 14px; font-weight: 600; color: #334155; margin-top: 2px;">
            {{ $reading->recorder->name ?? '—' }}
        </div>
    </div>
</div>

{{-- ── Form Card ───────────────────────────────────── --}}
<div class="util-form-card" style="max-width: 640px;">

    <div class="form-section-header">
        <div class="section-number">✎</div>
        <h4>Chỉnh sửa chỉ số</h4>
    </div>

    <form action="{{ route('admin.utility-readings.update', $reading->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="util-form-grid-3">
            <div class="util-form-group">
                <label class="util-form-label">Chỉ số cũ <span style="color:#ef4444">*</span></label>
                <input type="number" name="old_value" id="old_value"
                    value="{{ old('old_value', $reading->old_value) }}" min="0"
                    class="util-form-input {{ $errors->has('old_value') ? 'util-form-input--error' : '' }}" required>
                @error('old_value')
                    <p class="util-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="util-form-group">
                <label class="util-form-label">Chỉ số mới <span style="color:#ef4444">*</span></label>
                <input type="number" name="new_value" id="new_value"
                    value="{{ old('new_value', $reading->new_value) }}" min="0"
                    class="util-form-input {{ $errors->has('new_value') ? 'util-form-input--error' : '' }}" required>
                @error('new_value')
                    <p class="util-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="util-form-group">
                <label class="util-form-label">Tiêu thụ (tự tính)</label>
                <input type="text" id="usage_display" class="util-form-input util-form-input--readonly"
                    value="{{ number_format($reading->usage_amount) }}"
                    readonly style="font-weight: 700; color: #10b981;">
                <p class="util-form-hint">Cập nhật tự động</p>
            </div>
        </div>

        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 13px; color: #92400e;">
            ⚠️ Lưu ý: Sửa chỉ số cũ sẽ ảnh hưởng đến lượng tiêu thụ được tính toán.
        </div>

        <div class="util-form-actions">
            <button type="submit" class="util-btn util-btn--primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                </svg>
                Cập nhật chỉ số
            </button>
            <a href="{{ route('admin.utility-readings.index', ['month' => $reading->record_month, 'year' => $reading->record_year]) }}"
                class="util-btn util-btn--outline">Hủy</a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const oldValueInp = document.getElementById('old_value');
    const newValueInp = document.getElementById('new_value');
    const usageDisp   = document.getElementById('usage_display');

    function calcUsage() {
        const oldVal = parseInt(oldValueInp.value) || 0;
        const newVal = parseInt(newValueInp.value) || 0;
        const usage  = Math.max(0, newVal - oldVal);
        usageDisp.value = usage.toLocaleString('vi-VN');
        usageDisp.style.color = usage > 0 ? '#10b981' : '#94a3b8';
    }

    oldValueInp.addEventListener('input', calcUsage);
    newValueInp.addEventListener('input', calcUsage);
});
</script>
@endpush
