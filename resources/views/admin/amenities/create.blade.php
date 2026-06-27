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
        <p class="amf-subtitle">Điền thông tin và cấu hình để tạo tiện ích cho cư dân đặt lịch.</p>
    </div>

    <form method="POST" action="{{ route('admin.amenities.store') }}">
        @csrf

        {{-- Error summary --}}
        @if($errors->any())
        <div class="amf-error-summary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                <strong>Vui lòng kiểm tra lại:</strong>
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        </div>
        @endif

        {{-- ====== PHẦN 1: Thông tin cơ bản ====== --}}
        <div class="amf-card">
            <div class="amf-section-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Thông tin cơ bản
            </div>
            <div class="amf-grid">
                <div class="amf-field amf-field--wide">
                    <label for="name">Tên tiện ích <span class="amf-required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        placeholder="VD: Hồ bơi, Phòng Gym, Sân BBQ..." maxlength="100" required
                        class="{{ $errors->has('name') ? 'amf-input--error' : '' }}">
                    @error('name')<span class="amf-error">{{ $message }}</span>@enderror
                </div>

                <div class="amf-field">
                    <label for="capacity">Sức chứa tối đa <span class="amf-required">*</span></label>
                    <div class="amf-input-group">
                        <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 10) }}"
                            min="1" max="9999" required class="{{ $errors->has('capacity') ? 'amf-input--error' : '' }}">
                        <span class="amf-input-suffix">người</span>
                    </div>
                    @error('capacity')<span class="amf-error">{{ $message }}</span>@enderror
                </div>

                <div class="amf-field">
                    <label for="status">Trạng thái <span class="amf-required">*</span></label>
                    <select id="status" name="status" required>
                        <option value="available" {{ old('status','available') === 'available' ? 'selected' : '' }}>✅ Hoạt động</option>
                        <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>🔧 Bảo trì</option>
                        <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>🚫 Đóng cửa</option>
                    </select>
                    @error('status')<span class="amf-error">{{ $message }}</span>@enderror
                </div>

                <div class="amf-field amf-field--wide">
                    <label for="description">Mô tả</label>
                    <textarea id="description" name="description" rows="2"
                        placeholder="Mô tả ngắn về tiện ích..." maxlength="500">{{ old('description') }}</textarea>
                    @error('description')<span class="amf-error">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        {{-- ====== PHẦN 2: Cấu hình khung giờ ====== --}}
        <div class="amf-card">
            <div class="amf-section-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Khung giờ hoạt động
            </div>
            <div class="amf-grid">
                <div class="amf-field">
                    <label for="open_time">Giờ mở cửa</label>
                    <input type="time" id="open_time" name="open_time" value="{{ old('open_time', '06:00') }}"
                        class="{{ $errors->has('open_time') ? 'amf-input--error' : '' }}">
                    @error('open_time')<span class="amf-error">{{ $message }}</span>@enderror
                </div>

                <div class="amf-field">
                    <label for="close_time">Giờ đóng cửa</label>
                    <input type="time" id="close_time" name="close_time" value="{{ old('close_time', '22:00') }}"
                        class="{{ $errors->has('close_time') ? 'amf-input--error' : '' }}">
                    @error('close_time')<span class="amf-error">{{ $message }}</span>@enderror
                    <span class="amf-hint">Phải sau giờ mở cửa</span>
                </div>

                <div class="amf-field">
                    <label for="slot_duration">Thời lượng mỗi lần đặt <span class="amf-required">*</span></label>
                    <select id="slot_duration" name="slot_duration" required>
                        <option value="30"  {{ old('slot_duration','60') === '30'  ? 'selected' : '' }}>30 phút</option>
                        <option value="60"  {{ old('slot_duration','60') === '60'  ? 'selected' : '' }}>1 tiếng</option>
                        <option value="90"  {{ old('slot_duration','60') === '90'  ? 'selected' : '' }}>1.5 tiếng</option>
                        <option value="120" {{ old('slot_duration','60') === '120' ? 'selected' : '' }}>2 tiếng</option>
                    </select>
                    <span class="amf-hint">Mỗi lần đặt kéo dài bao lâu</span>
                    @error('slot_duration')<span class="amf-error">{{ $message }}</span>@enderror
                </div>

                {{-- Preview slots --}}
                <div class="amf-field amf-field--wide">
                    <label>Xem trước các khung giờ</label>
                    <div class="amf-slots-preview" id="slotsPreview">
                        <span class="amf-hint">Nhập giờ mở/đóng cửa và thời lượng để xem trước</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== PHẦN 3: Bảng giá ====== --}}
        <div class="amf-card">
            <div class="amf-section-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                Bảng giá
            </div>
            <div class="amf-grid">
                <div class="amf-field">
                    <label for="price_per_slot">Giá mỗi lần đặt <span class="amf-required">*</span></label>
                    <div class="amf-input-group">
                        <input type="number" id="price_per_slot" name="price_per_slot"
                            value="{{ old('price_per_slot', 0) }}" min="0" step="1000"
                            class="{{ $errors->has('price_per_slot') ? 'amf-input--error' : '' }}">
                        <span class="amf-input-suffix">đ</span>
                    </div>
                    <span class="amf-hint">Nhập 0 nếu miễn phí</span>
                    @error('price_per_slot')<span class="amf-error">{{ $message }}</span>@enderror
                </div>

                <div class="amf-field amf-price-preview-wrap">
                    <label>Xem trước hiển thị giá</label>
                    <div class="amf-price-preview" id="pricePreview">
                        <span class="amf-free-badge">🎉 Miễn phí</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== PHẦN 4: Nội quy sử dụng ====== --}}
        <div class="amf-card">
            <div class="amf-section-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Nội quy & Lưu ý
            </div>
            <div class="amf-field">
                <label for="rules">Quy định sử dụng</label>
                <textarea id="rules" name="rules" rows="4"
                    placeholder="VD: Mặc trang phục thể thao. Không mang đồ ăn vào. Trẻ em dưới 12 tuổi cần có người lớn..."
                    maxlength="1000">{{ old('rules') }}</textarea>
                <span class="amf-hint">Tối đa 1000 ký tự. Hiển thị cho cư dân khi đặt lịch.</span>
                @error('rules')<span class="amf-error">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- Actions --}}
        <div class="amf-actions">
            <button type="submit" class="amf-btn amf-btn--primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Lưu tiện ích
            </button>
            <a href="{{ route('admin.amenities.index') }}" class="amf-btn amf-btn--ghost">Hủy bỏ</a>
        </div>
    </form>
</div>

<style>
.amf-page { max-width: 820px; margin: 0 auto; padding: 24px 20px; display: flex; flex-direction: column; gap: 18px; }

.amf-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; color: #64748b; }
.amf-breadcrumb a { color: #2563eb; text-decoration: none; font-weight: 500; }
.amf-breadcrumb a:hover { text-decoration: underline; }

.amf-header { margin-bottom: 4px; }
.amf-title { font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0 0 6px; }
.amf-subtitle { font-size: 0.875rem; color: #64748b; margin: 0; }

/* Card */
.amf-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 22px 26px; display: flex; flex-direction: column; gap: 16px; }

.amf-section-label { display: flex; align-items: center; gap: 7px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; }

/* Grid */
.amf-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
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
    width: 100%;
    box-sizing: border-box;
}
.amf-field input:focus,
.amf-field select:focus,
.amf-field textarea:focus { border-color: #3b82f6; background: #fff; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
.amf-input--error { border-color: #ef4444 !important; background: #fff5f5 !important; }
.amf-error { font-size: 0.75rem; color: #dc2626; font-weight: 500; }
.amf-hint { font-size: 0.73rem; color: #94a3b8; }

/* Input group với suffix */
.amf-input-group { display: flex; align-items: stretch; }
.amf-input-group input { border-radius: 8px 0 0 8px; border-right: none; flex: 1; }
.amf-input-suffix { padding: 9px 12px; background: #f1f5f9; border: 1.5px solid #e2e8f0; border-left: none; border-radius: 0 8px 8px 0; font-size: 0.8rem; color: #64748b; font-weight: 600; white-space: nowrap; display: flex; align-items: center; }

/* Slots preview */
.amf-slots-preview { display: flex; flex-wrap: wrap; gap: 6px; padding: 10px; background: #f8fafc; border: 1px dashed #e2e8f0; border-radius: 8px; min-height: 42px; align-items: center; }
.amf-slot-chip { background: #eff6ff; color: #2563eb; font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 20px; border: 1px solid #bfdbfe; }

/* Price preview */
.amf-price-preview-wrap { justify-content: flex-start; }
.amf-price-preview { padding: 10px 14px; background: #f8fafc; border: 1px dashed #e2e8f0; border-radius: 8px; font-size: 0.9rem; font-weight: 700; color: #0f172a; min-height: 42px; display: flex; align-items: center; }
.amf-free-badge { color: #16a34a; }
.amf-paid-badge { color: #2563eb; }

/* Error summary */
.amf-error-summary { display: flex; gap: 10px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; color: #b91c1c; font-size: 0.85rem; }
.amf-error-summary svg { flex-shrink: 0; margin-top: 2px; }
.amf-error-summary strong { display: block; margin-bottom: 4px; }
.amf-error-summary ul { margin: 0; padding-left: 16px; }
.amf-error-summary li { margin: 2px 0; }

/* Actions */
.amf-actions { display: flex; gap: 10px; align-items: center; }
.amf-btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 20px; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
.amf-btn--primary { background: #2563eb; color: #fff; }
.amf-btn--primary:hover { background: #1d4ed8; }
.amf-btn--ghost { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
.amf-btn--ghost:hover { background: #f1f5f9; }

@media (max-width: 600px) {
    .amf-grid { grid-template-columns: 1fr; }
    .amf-card { padding: 16px; }
}
</style>

@push('scripts')
<script>
function generateSlots() {
    const open     = document.getElementById('open_time').value;
    const close    = document.getElementById('close_time').value;
    const duration = parseInt(document.getElementById('slot_duration').value) || 60;
    const preview  = document.getElementById('slotsPreview');

    if (!open || !close) {
        preview.innerHTML = '<span class="amf-hint">Nhập giờ mở/đóng cửa và thời lượng để xem trước</span>';
        return;
    }

    const [oh, om] = open.split(':').map(Number);
    const [ch, cm] = close.split(':').map(Number);
    let start = oh * 60 + om;
    const end = ch * 60 + cm;

    let chips = '';
    let count = 0;
    while (start + duration <= end) {
        const s = String(Math.floor(start/60)).padStart(2,'0') + ':' + String(start%60).padStart(2,'0');
        const e = String(Math.floor((start+duration)/60)).padStart(2,'0') + ':' + String((start+duration)%60).padStart(2,'0');
        chips += `<span class="amf-slot-chip">${s} – ${e}</span>`;
        start += duration;
        count++;
    }

    preview.innerHTML = count > 0
        ? chips + `<span class="amf-hint" style="margin-left:4px">${count} khung giờ</span>`
        : '<span class="amf-hint" style="color:#ef4444">Không tạo được khung giờ nào</span>';
}

function updatePricePreview() {
    const price    = parseInt(document.getElementById('price_per_slot').value) || 0;
    const duration = document.getElementById('slot_duration').value;
    const preview  = document.getElementById('pricePreview');
    const labels   = { '30':'30 phút', '60':'1 tiếng', '90':'1.5 tiếng', '120':'2 tiếng' };

    if (price === 0) {
        preview.innerHTML = '<span class="amf-free-badge">🎉 Miễn phí</span>';
    } else {
        preview.innerHTML = `<span class="amf-paid-badge">${price.toLocaleString('vi-VN')}đ / ${labels[duration] || duration+' phút'}</span>`;
    }
}

['open_time','close_time','slot_duration'].forEach(id =>
    document.getElementById(id).addEventListener('change', generateSlots)
);
document.getElementById('price_per_slot').addEventListener('input', updatePricePreview);
document.getElementById('slot_duration').addEventListener('change', updatePricePreview);

// Init
generateSlots();
updatePricePreview();
</script>
@endpush

@endsection
