@extends('layouts.admin.master')

@section('page_title', 'Thêm tiện ích mới')

@section('content')
<div class="amf-page">

    {{-- Breadcrumb --}}
    <div class="amf-breadcrumb">
        <a href="{{ portal_route('amenities.index') }}">Quản lý tiện ích</a>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ portal_route('amenities.index') }}">Danh sách tiện ích</a>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        <span>Thêm tiện ích mới</span>
    </div>

    <form method="POST" action="{{ portal_route('amenities.store') }}" enctype="multipart/form-data" id="facilityForm">
        @csrf
        
        <div class="amf-header">
            <div>
                <h1 class="amf-title">Thêm tiện ích mới</h1>
                <p class="amf-subtitle">Điền thông tin chi tiết để thiết lập tiện ích mới cho cư dân.</p>
            </div>
            <div class="amf-actions-top">
                <a href="{{ portal_route('amenities.index') }}" class="amf-btn amf-btn-outline">Hủy</a>
                <button type="submit" class="amf-btn amf-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Lưu tiện ích
                </button>
            </div>
        </div>

        {{-- Error summary --}}
        @if($errors->any())
        <div class="amf-alert amf-alert-danger">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                <strong>Vui lòng kiểm tra lại:</strong>
                <ul style="margin:4px 0 0; padding-left:16px; font-size:13px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        </div>
        @endif

        <div class="amf-layout">
            <div class="amf-col-left">
                {{-- 1. Thông tin cơ bản --}}
                <div class="amf-card">
                    <div class="amf-card-header">
                        <span class="amf-card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg></span>
                        <h2 class="amf-card-title">1. Thông tin cơ bản</h2>
                    </div>
                    <div class="amf-card-body">
                        <div class="amf-form-group">
                            <label>Tên tiện ích <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="amf-control" value="{{ old('name') }}" placeholder="VD: Hồ bơi, Phòng Gym, Sân cầu lông..." required>
                        </div>
                        <div class="amf-row">
                            <div class="amf-col">
                                <div class="amf-form-group">
                                    <label>Loại tiện ích</label>
                                    <select name="facility_type" class="amf-control">
                                        <option value="">Chọn loại tiện ích</option>
                                        <option value="Thể thao" {{ old('facility_type') == 'Thể thao' ? 'selected' : '' }}>Thể thao</option>
                                        <option value="Giải trí" {{ old('facility_type') == 'Giải trí' ? 'selected' : '' }}>Giải trí</option>
                                        <option value="Sinh hoạt chung" {{ old('facility_type') == 'Sinh hoạt chung' ? 'selected' : '' }}>Sinh hoạt chung</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="amf-row">
                            <div class="amf-col">
                                <div class="amf-form-group">
                                    <label>Tòa nhà</label>
                                    <select name="block_id" id="blockSelect" class="amf-control">
                                        <option value="">Chọn tòa nhà (Tùy chọn)</option>
                                        @foreach($blocks as $block)
                                            <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="amf-col">
                                <div class="amf-form-group">
                                    <label>Tầng</label>
                                    <select name="floor_id" id="floorSelect" class="amf-control" {{ old('block_id') ? '' : 'disabled' }}>
                                        <option value="">Chọn tầng (Tùy chọn)</option>
                                    </select>
                                    <input type="hidden" id="oldFloorId" value="{{ old('floor_id') }}">
                                </div>
                            </div>
                        </div>
                        <div class="amf-form-group">
                            <label>Mô tả ngắn</label>
                            <textarea name="description" class="amf-control" rows="4" placeholder="Mô tả tóm tắt về tiện ích, công dụng, đối tượng sử dụng...">{{ old('description') }}</textarea>
                            <div class="amf-char-count">0/500</div>
                        </div>
                    </div>
                </div>

                {{-- 3. Quy định sử dụng --}}
                <div class="amf-card">
                    <div class="amf-card-header">
                        <span class="amf-card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                        <h2 class="amf-card-title">3. Quy định sử dụng</h2>
                    </div>
                    <div class="amf-card-body">
                        <div class="amf-row">
                            <div class="amf-col">
                                <div class="amf-form-group">
                                    <label>Giờ mở cửa <span class="text-danger">*</span></label>
                                    <div class="amf-time-range">
                                        <input type="time" name="open_time" class="amf-control" value="{{ old('open_time', '06:00') }}" required>
                                        <span>-</span>
                                        <input type="time" name="close_time" class="amf-control" value="{{ old('close_time', '22:00') }}" required>
                                    </div>
                                </div>
                                <div class="amf-form-group mt-3">
                                    <label>Ngày hoạt động</label>
                                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                                        @php
                                            $days = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
                                            $selectedDays = old('operating_days', $days);
                                        @endphp
                                        @foreach($days as $day)
                                        <label style="display:flex; align-items:center; gap:4px; font-weight:normal; font-size:13px">
                                            <input type="checkbox" name="operating_days[]" value="{{ $day }}" {{ in_array($day, $selectedDays) ? 'checked' : '' }}> {{ $day }}
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="amf-col">
                                <div class="amf-form-group">
                                    <label>Sức chứa tối đa (người) <span class="text-danger">*</span></label>
                                    <div class="amf-input-group">
                                        <input type="number" name="capacity" class="amf-control" value="{{ old('capacity', 50) }}" placeholder="VD: 50" min="1" required>
                                        <span class="amf-input-group-text">người</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="amf-form-group">
                            <label>Nội quy sử dụng</label>
                            <div class="amf-editor-toolbar">
                                <button type="button"><b>B</b></button>
                                <button type="button"><i>I</i></button>
                                <button type="button"><u>U</u></button>
                                <button type="button">⋮¯</button>
                                <button type="button">1.</button>
                            </div>
                            <textarea name="rules" class="amf-control" style="border-top-left-radius:0; border-top-right-radius:0; border-top:0" rows="5" placeholder="Nhập nội quy, quy định khi sử dụng tiện ích...">{{ old('rules') }}</textarea>
                            <div class="amf-char-count">0/1000</div>
                        </div>
                    </div>
                </div>

                {{-- 4. Hình ảnh --}}
                <div class="amf-card">
                    <div class="amf-card-header">
                        <span class="amf-card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></span>
                        <h2 class="amf-card-title">4. Hình ảnh</h2>
                    </div>
                    <div class="amf-card-body">
                        <div class="amf-form-group">
                            <label>Ảnh đại diện</label>
                            <div class="amf-upload-zone" id="uploadZone">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <p class="mt-2 mb-1"><b>Kéo thả ảnh vào đây</b></p>
                                <p class="text-muted text-sm mb-2">hoặc click để chọn file</p>
                                <p class="text-muted text-xs">Định dạng: PNG, JPG, JPEG, GIF<br>Kích thước tối đa: 5MB</p>
                                <input type="file" name="images[]" multiple accept="image/*" class="amf-file-hidden">
                            </div>
                            <div id="imagePreviewContainer" class="amf-image-previews"></div>
                        </div>
                    </div>
                </div>
                
                <div class="amf-alert amf-alert-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    Lưu ý: Sau khi tạo, bạn có thể chỉnh sửa thông tin, thêm ca cố định (nếu có) và quản lý chi tiết tiện ích.
                </div>
            </div>

            <div class="amf-col-right">
                {{-- 2. Biểu giá & Đặt chỗ --}}
                <div class="amf-card">
                    <div class="amf-card-header">
                        <span class="amf-card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg></span>
                        <h2 class="amf-card-title">2. Biểu giá & Đặt chỗ</h2>
                    </div>
                    <div class="amf-card-body">
                        <div class="amf-form-group">
                            <label>Loại phí <span class="text-danger">*</span></label>
                            <div class="amf-radio-list">
                                <label class="amf-radio">
                                    <input type="radio" name="fee_type" value="free" {{ old('fee_type', 'free') == 'free' ? 'checked' : '' }}>
                                    <span>Miễn phí</span>
                                </label>
                                <label class="amf-radio">
                                    <input type="radio" name="fee_type" value="per_hour" {{ old('fee_type') == 'per_hour' ? 'checked' : '' }}>
                                    <span>Theo giờ</span>
                                </label>
                                <label class="amf-radio">
                                    <input type="radio" name="fee_type" value="per_use" {{ old('fee_type') == 'per_use' ? 'checked' : '' }}>
                                    <span>Theo lượt</span>
                                </label>
                                <label class="amf-radio">
                                    <input type="radio" name="fee_type" value="per_person" {{ old('fee_type') == 'per_person' ? 'checked' : '' }}>
                                    <span>Theo người</span>
                                </label>
                            </div>
                        </div>

                        <div class="amf-form-group" id="priceGroup">
                            <label>Đơn giá</label>
                            <div class="amf-input-group">
                                <input type="number" name="price" id="priceInput" class="amf-control" value="{{ old('price', 0) }}" {{ old('fee_type', 'free') == 'free' ? 'disabled' : '' }}>
                                <span class="amf-input-group-text">VND</span>
                            </div>
                            <p class="amf-hint">Đơn giá sẽ chỉ áp dụng khi loại phí khác "Miễn phí".</p>
                        </div>

                        <div class="amf-form-group" style="margin-top:24px">
                            <label>Hình thức đặt chỗ <span class="text-danger">*</span></label>
                            <div class="amf-radio-list amf-radio-list-large">
                                <label class="amf-radio-card">
                                    <input type="radio" name="booking_type" value="none" {{ old('booking_type', 'none') == 'none' ? 'checked' : '' }}>
                                    <div class="amf-radio-content">
                                        <div class="amf-radio-title">Không cần đặt trước</div>
                                        <div class="amf-radio-desc">Cư dân đến sử dụng trực tiếp</div>
                                    </div>
                                </label>
                                <label class="amf-radio-card">
                                    <input type="radio" name="booking_type" value="time_slot" {{ old('booking_type') == 'time_slot' ? 'checked' : '' }}>
                                    <div class="amf-radio-content">
                                        <div class="amf-radio-title">Theo khung giờ</div>
                                        <div class="amf-radio-desc">Cư dân chọn thời gian bắt đầu và thời lượng sử dụng</div>
                                    </div>
                                </label>
                                <label class="amf-radio-card">
                                    <input type="radio" name="booking_type" value="slot" {{ old('booking_type') == 'slot' ? 'checked' : '' }}>
                                    <div class="amf-radio-content">
                                        <div class="amf-radio-title">Theo thời gian</div>
                                        <div class="amf-radio-desc">Cư dân đặt lịch theo thời gian cụ thể</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div id="timeSlotSettings" class="amf-sub-settings" style="display: {{ in_array(old('booking_type'), ['time_slot', 'slot']) ? 'block' : 'none' }}">
                            <div class="amf-form-group">
                                <label>Thời lượng 1 lượt (phút) <span class="text-danger">*</span></label>
                                <div class="amf-input-group">
                                    <input type="number" name="slot_duration" class="amf-control" value="{{ old('slot_duration', 60) }}">
                                    <span class="amf-input-group-text">phút</span>
                                </div>
                            </div>
                            
                            <div class="amf-row">
                                <div class="amf-col">
                                    <div class="amf-form-group">
                                        <label>Đặt trước tối thiểu</label>
                                        <div class="amf-input-group">
                                            <input type="number" name="min_advance_booking_hours" class="amf-control" value="{{ old('min_advance_booking_hours', 2) }}">
                                            <span class="amf-input-group-text">giờ</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="amf-col">
                                    <div class="amf-form-group">
                                        <label>Đặt trước tối đa</label>
                                        <div class="amf-input-group">
                                            <input type="number" name="max_advance_booking_days" class="amf-control" value="{{ old('max_advance_booking_days', 7) }}">
                                            <span class="amf-input-group-text">ngày</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="amf-alert amf-alert-info mt-3" style="background:#f0f5ff; border:none; color:#1e40af">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                            Thời gian đặt trước giúp cư dân chủ động sắp xếp khi sử dụng tiện ích.
                        </div>
                    </div>
                </div>

                {{-- 5. Trạng thái --}}
                <div class="amf-card">
                    <div class="amf-card-header">
                        <span class="amf-card-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
                        <h2 class="amf-card-title">5. Trạng thái</h2>
                    </div>
                    <div class="amf-card-body">
                        <div class="amf-form-group amf-status-group" style="flex-direction:column; align-items:flex-start">
                            <label>Trạng thái hiển thị</label>
                            <div class="amf-radio-list">
                                <label class="amf-radio">
                                    <input type="radio" name="status" value="available" {{ old('status', 'available') == 'available' ? 'checked' : '' }}>
                                    <span>Hoạt động</span>
                                </label>
                                <label class="amf-radio">
                                    <input type="radio" name="status" value="maintenance" {{ old('status') == 'maintenance' ? 'checked' : '' }}>
                                    <span>Bảo trì</span>
                                </label>
                                <label class="amf-radio">
                                    <input type="radio" name="status" value="closed" {{ old('status') == 'closed' ? 'checked' : '' }}>
                                    <span>Ngừng sử dụng</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="amf-alert amf-alert-success mt-3" style="background:#f0fdf4; border:none; color:#166534">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Tiện ích sẽ được hiển thị trên ứng dụng của cư dân sau khi lưu.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
/* CSS Variables */
:root {
    --primary: #2563eb;
    --primary-hover: #1d4ed8;
    --bg-page: #f8fafc;
    --card-bg: #ffffff;
    --text-main: #0f172a;
    --text-muted: #64748b;
    --border: #e2e8f0;
    --border-focus: #93c5fd;
    --ring: rgba(59,130,246,0.15);
    --danger: #ef4444;
}

/* Base Layout */
.amf-page {
    max-width: 1100px;
    margin: 0 auto;
    padding: 24px;
    background: var(--bg-page);
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
}

.amf-breadcrumb {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--text-muted);
    margin-bottom: 20px;
}
.amf-breadcrumb a { color: var(--text-muted); text-decoration: none; }
.amf-breadcrumb a:hover { color: var(--primary); }

.amf-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}
.amf-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0 0 6px 0;
}
.amf-subtitle {
    font-size: 14px;
    color: var(--text-muted);
    margin: 0;
}
.amf-actions-top {
    display: flex;
    gap: 12px;
}

/* Layout Grid */
.amf-layout {
    display: grid;
    grid-template-columns: 1.4fr 1fr;
    gap: 24px;
    align-items: start;
}
@media (max-width: 900px) {
    .amf-layout { grid-template-columns: 1fr; }
}

.amf-col-left { display: flex; flex-direction: column; gap: 24px; }
.amf-col-right { display: flex; flex-direction: column; gap: 24px; }
.amf-row { display: flex; gap: 16px; margin-bottom: 16px; }
.amf-col { flex: 1; }

/* Cards */
.amf-card {
    background: var(--card-bg);
    border-radius: 12px;
    border: 1px solid var(--border);
    overflow: hidden;
}
.amf-card-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
}
.amf-card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    background: #eff6ff;
    width: 28px;
    height: 28px;
    border-radius: 50%;
}
.amf-card-title {
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    color: var(--text-main);
}
.amf-card-body {
    padding: 20px;
}

/* Form Controls */
.amf-form-group {
    margin-bottom: 16px;
}
.amf-form-group:last-child {
    margin-bottom: 0;
}
.amf-form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-main);
    margin-bottom: 8px;
}
.amf-control {
    width: 100%;
    padding: 10px 12px;
    font-size: 14px;
    line-height: 1.5;
    color: var(--text-main);
    background-color: #fff;
    border: 1px solid var(--border);
    border-radius: 6px;
    transition: border-color .15s, box-shadow .15s;
    box-sizing: border-box;
}
.amf-control:focus {
    border-color: var(--border-focus);
    outline: 0;
    box-shadow: 0 0 0 3px var(--ring);
}
.amf-control:disabled {
    background-color: #f1f5f9;
    color: #94a3b8;
}
.text-danger { color: var(--danger); }
.text-muted { color: var(--text-muted); }
.mt-2 { margin-top: 8px; }
.mt-3 { margin-top: 16px; }
.mb-1 { margin-bottom: 4px; }
.mb-2 { margin-bottom: 8px; }
.text-sm { font-size: 13px; }
.text-xs { font-size: 12px; }

/* Input Groups */
.amf-input-group {
    display: flex;
    align-items: center;
}
.amf-input-group .amf-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: 0;
}
.amf-input-group-text {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    font-size: 14px;
    color: var(--text-muted);
    background-color: #f8fafc;
    border: 1px solid var(--border);
    border-top-right-radius: 6px;
    border-bottom-right-radius: 6px;
}

/* Time Range */
.amf-time-range {
    display: flex;
    align-items: center;
    gap: 12px;
}
.amf-time-range .amf-control {
    flex: 1;
}

/* Textarea & Editor */
.amf-editor-toolbar {
    display: flex;
    border: 1px solid var(--border);
    border-bottom: none;
    border-top-left-radius: 6px;
    border-top-right-radius: 6px;
    background: #f8fafc;
    padding: 4px 8px;
    gap: 4px;
}
.amf-editor-toolbar button {
    background: transparent;
    border: none;
    padding: 6px 10px;
    cursor: pointer;
    color: var(--text-muted);
    border-radius: 4px;
}
.amf-editor-toolbar button:hover {
    background: #e2e8f0;
}
.amf-char-count {
    text-align: right;
    font-size: 12px;
    color: var(--text-muted);
    margin-top: 4px;
}

/* Upload Zone */
.amf-upload-zone {
    border: 2px dashed #bfdbfe;
    border-radius: 8px;
    background: #f8fafc;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}
.amf-upload-zone:hover {
    background: #eff6ff;
    border-color: #93c5fd;
}
.amf-file-hidden {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
}
.amf-image-previews {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 12px;
}

/* Radio List */
.amf-radio-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.amf-radio {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 14px;
}
.amf-radio input[type="radio"] {
    width: 18px;
    height: 18px;
    accent-color: var(--primary);
}
.amf-hint {
    font-size: 12px;
    color: var(--text-muted);
    margin: 4px 0 0;
}

/* Radio Cards (Booking Type) */
.amf-radio-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.amf-radio-card:has(input:checked) {
    border-color: var(--primary);
    background: #eff6ff;
}
.amf-radio-card input[type="radio"] {
    margin-top: 4px;
    width: 18px;
    height: 18px;
    accent-color: var(--primary);
}
.amf-radio-title {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-main);
    margin-bottom: 2px;
}
.amf-radio-desc {
    font-size: 12px;
    color: var(--text-muted);
}

.amf-sub-settings {
    background: #f8fafc;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 16px;
    margin-top: 12px;
}

/* Toggle Switch */
.amf-status-group {
    display: flex;
    align-items: center;
    gap: 16px;
}
.amf-toggle-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
}
.amf-toggle {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
}
.amf-toggle input[type="checkbox"] {
    opacity: 0;
    width: 0;
    height: 0;
}
.amf-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: .3s;
    border-radius: 24px;
}
.amf-slider:before {
    position: absolute;
    content: "";
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}
.amf-toggle input:checked + .amf-slider {
    background-color: #16a34a;
}
.amf-toggle input:checked + .amf-slider:before {
    transform: translateX(20px);
}
.amf-toggle-label {
    font-size: 14px;
    font-weight: 500;
}

/* Alerts */
.amf-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 13px;
    line-height: 1.5;
}
.amf-alert svg { flex-shrink: 0; margin-top: 1px; }
.amf-alert-info { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
.amf-alert-danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; margin-bottom: 24px; }
.amf-alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

/* Buttons */
.amf-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}
.amf-btn-primary {
    background: var(--primary);
    color: white;
    border: 1px solid var(--primary);
}
.amf-btn-primary:hover {
    background: var(--primary-hover);
    border-color: var(--primary-hover);
}
.amf-btn-outline {
    background: white;
    color: var(--text-main);
    border: 1px solid var(--border);
}
.amf-btn-outline:hover {
    background: var(--bg-page);
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handling fee type and price input logic
    const feeRadios = document.querySelectorAll('input[name="fee_type"]');
    const priceGroup = document.getElementById('priceGroup');
    const priceInput = document.getElementById('priceInput');

    function updatePriceState() {
        const selectedFee = document.querySelector('input[name="fee_type"]:checked').value;
        if (selectedFee === 'free') {
            priceInput.value = 0;
            priceInput.disabled = true;
            priceGroup.style.display = 'none';
        } else {
            priceInput.disabled = false;
            priceGroup.style.display = 'block';
        }
    }

    feeRadios.forEach(radio => {
        radio.addEventListener('change', updatePriceState);
    });

    // Handling booking type logic
    const bookingRadios = document.querySelectorAll('input[name="booking_type"]');
    const timeSlotSettings = document.getElementById('timeSlotSettings');

    function updateBookingState() {
        const selectedBooking = document.querySelector('input[name="booking_type"]:checked').value;
        if (selectedBooking === 'time_slot' || selectedBooking === 'slot') {
            timeSlotSettings.style.display = 'block';
        } else {
            timeSlotSettings.style.display = 'none';
        }
    }

    bookingRadios.forEach(radio => {
        radio.addEventListener('change', updateBookingState);
    });

    // AJAX load floors
    const blockSelect = document.getElementById('blockSelect');
    const floorSelect = document.getElementById('floorSelect');
    const oldFloorId = document.getElementById('oldFloorId').value;

    function loadFloors(blockId, selectedFloorId = null) {
        if (!blockId) {
            floorSelect.innerHTML = '<option value="">Chọn tầng (Tùy chọn)</option>';
            floorSelect.disabled = true;
            return;
        }

        floorSelect.disabled = true;
        floorSelect.innerHTML = '<option value="">Đang tải...</option>';

        fetch(`/amenities/floors/${blockId}`)
            .then(response => response.json())
            .then(data => {
                floorSelect.innerHTML = '<option value="">Chọn tầng (Tùy chọn)</option>';
                data.forEach(floor => {
                    const option = document.createElement('option');
                    option.value = floor.id;
                    option.textContent = floor.name;
                    if (selectedFloorId && selectedFloorId == floor.id) {
                        option.selected = true;
                    }
                    floorSelect.appendChild(option);
                });
                floorSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error loading floors:', error);
                floorSelect.innerHTML = '<option value="">Lỗi tải dữ liệu</option>';
            });
    }

    blockSelect.addEventListener('change', function() {
        loadFloors(this.value);
    });

    if (blockSelect.value) {
        loadFloors(blockSelect.value, oldFloorId);
    }

    bookingRadios.forEach(radio => {
        radio.addEventListener('change', updateBookingState);
    });

    // Initialize states
    updatePriceState();
    updateBookingState();
});
</script>
@endpush

@endsection
