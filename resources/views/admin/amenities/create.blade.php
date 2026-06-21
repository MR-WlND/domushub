@extends('layouts.admin.master')

@section('page_title', 'Thêm tiện ích mới')

@section('content')
<div class="amf-page">

    {{-- Breadcrumb --}}
    <div class="amf-breadcrumb">
        <a href="{{ route('admin.amenities.index') }}">Tiện ích chung cư</a>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        <span>Thêm mới</span>
    </div>

    <div class="amf-header">
        <h1 class="amf-title">Thêm tiện ích mới</h1>
        <p class="amf-subtitle">Điền thông tin để tạo tiện ích cho cư dân đặt lịch sử dụng.</p>
    </div>

    <div class="amf-card">
        @if($errors->any())
        <div class="amf-error-summary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                <strong>Vui lòng kiểm tra lại:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        <form method="POST" action="{{ route('admin.amenities.store') }}">
            @csrf

            <div class="amf-section-label">Thông tin cơ bản</div>

            <div class="amf-grid">
                {{-- Tên tiện ích --}}
                <div class="amf-field amf-field--wide">
                    <label for="name">Tên tiện ích <span class="amf-required">*</span></label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="VD: Hồ bơi, Phòng Gym, Sân BBQ..."
                        maxlength="100"
                        required
                        class="{{ $errors->has('name') ? 'amf-input--error' : '' }}"
                    >
                    @error('name')
                        <span class="amf-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Sức chứa --}}
                <div class="amf-field">
                    <label for="capacity">Sức chứa tối đa <span class="amf-required">*</span></label>
                    <input
                        type="number"
                        id="capacity"
                        name="capacity"
                        value="{{ old('capacity', 10) }}"
                        min="1"
                        max="9999"
                        required
                        class="{{ $errors->has('capacity') ? 'amf-input--error' : '' }}"
                    >
                    <span class="amf-hint">Số người tối đa có thể sử dụng đồng thời</span>
                    @error('capacity')
                        <span class="amf-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Trạng thái --}}
                <div class="amf-field">
                    <label for="status">Trạng thái <span class="amf-required">*</span></label>
                    <select id="status" name="status" required>
                        <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>✅ Hoạt động</option>
                        <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>🔧 Bảo trì</option>
                        <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>🚫 Đóng cửa</option>
                    </select>
                    @error('status')
                        <span class="amf-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Mô tả --}}
                <div class="amf-field amf-field--wide">
                    <label for="description">Mô tả</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Mô tả về tiện ích: giờ mở cửa, lưu ý khi sử dụng..."
                        maxlength="500"
                    >{{ old('description') }}</textarea>
                    <span class="amf-hint">Tối đa 500 ký tự</span>
                    @error('description')
                        <span class="amf-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="amf-actions">
                <button type="submit" class="amf-btn amf-btn--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Lưu tiện ích
                </button>
                <a href="{{ route('admin.amenities.index') }}" class="amf-btn amf-btn--ghost">Hủy bỏ</a>
            </div>
        </form>
    </div>
</div>

<style>
.amf-page { max-width: 760px; margin: 0 auto; padding: 24px 20px; }

.amf-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; color: #64748b; margin-bottom: 18px; }
.amf-breadcrumb a { color: #2563eb; text-decoration: none; font-weight: 500; }
.amf-breadcrumb a:hover { text-decoration: underline; }

.amf-header { margin-bottom: 24px; }
.amf-title { font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0 0 6px; }
.amf-subtitle { font-size: 0.875rem; color: #64748b; margin: 0; }

.amf-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 28px 32px; }

.amf-section-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9; }

.amf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 24px; }
.amf-field { display: flex; flex-direction: column; gap: 5px; }
.amf-field--wide { grid-column: 1 / -1; }
.amf-field label { font-size: 0.8rem; font-weight: 600; color: #374151; }
.amf-required { color: #ef4444; }

.amf-field input,
.amf-field select,
.amf-field textarea {
    padding: 9px 13px;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.875rem;
    color: #1e293b;
    background: #f8fafc;
    outline: none;
    transition: border-color 0.15s, background 0.15s;
    font-family: inherit;
    resize: vertical;
}
.amf-field input:focus,
.amf-field select:focus,
.amf-field textarea:focus { border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.amf-input--error { border-color: #ef4444 !important; background: #fff5f5 !important; }
.amf-error { font-size: 0.75rem; color: #dc2626; font-weight: 500; }
.amf-hint { font-size: 0.73rem; color: #94a3b8; }

.amf-error-summary { display: flex; gap: 10px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; margin-bottom: 20px; color: #b91c1c; font-size: 0.85rem; }
.amf-error-summary svg { flex-shrink: 0; margin-top: 2px; }
.amf-error-summary strong { display: block; margin-bottom: 4px; }
.amf-error-summary ul { margin: 0; padding-left: 16px; }
.amf-error-summary li { margin: 2px 0; }

.amf-actions { display: flex; gap: 10px; align-items: center; padding-top: 20px; border-top: 1px solid #f1f5f9; }

.amf-btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 20px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
.amf-btn--primary { background: #2563eb; color: #fff; }
.amf-btn--primary:hover { background: #1d4ed8; }
.amf-btn--ghost { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
.amf-btn--ghost:hover { background: #f1f5f9; color: #475569; }

@media (max-width: 600px) {
    .amf-grid { grid-template-columns: 1fr; }
    .amf-card { padding: 20px 16px; }
}
</style>
@endsection
