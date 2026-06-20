@extends('layouts.resident.master')

@section('title', 'Đăng ký xe – DomusHub')

@push('styles')
    @vite(['resources/css/resident/vehicles-index.css'])
@endpush

@section('content')
<div class="vp">

    {{-- Header --}}
    <div class="vp__header">
        <div>
            <p class="vp__eyebrow">Quản lý cư dân</p>
            <h1 class="vp__title">Đăng ký xe mới</h1>
        </div>

        <a href="{{ route('resident.vehicles.index') }}" class="vp-btn vp-btn--outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại
        </a>
    </div>

    {{-- Form --}}
    <div class="vp-panel" style="max-width: 680px; margin: 0 auto; width: 100%;">
        <div class="vp-panel__body" style="padding: 2rem;">

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="vp-alert vp-alert--error" style="margin-bottom: 1.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>
                        <strong style="display:block; margin-bottom:4px;">Vui lòng kiểm tra lại thông tin:</strong>
                        @foreach($errors->all() as $error)
                            <span style="display:block; font-size:0.8125rem; font-weight:500;">• {{ $error }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Info Notice --}}
            <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:14px 18px; margin-bottom:24px; border-radius:12px; font-size:0.8125rem; color:#475569; line-height:1.6;">
                <strong style="color:#1e293b;">Lưu ý:</strong>
                <ul style="margin:6px 0 0 18px; padding:0;">
                    <li>Mỗi căn hộ tối đa <strong>3 xe máy / xe điện</strong>.</li>
                    <li><strong>Ô tô</strong> cần Ban quản lý duyệt và cấp lốt đỗ.</li>
                </ul>
            </div>

            <form method="POST"
                  action="{{ route('resident.vehicles.store') }}"
                  enctype="multipart/form-data"
                  style="display:flex; flex-direction:column; gap:1.25rem;">
                @csrf

                {{-- Row: Biển số & Loại xe --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                    <div class="vp-field">
                        <label class="vp-label">Biển số xe <span style="color:#dc2626;">*</span></label>
                        <input type="text"
                               name="license_plate"
                               class="vp-input @error('license_plate') vp-input--err @enderror"
                               value="{{ old('license_plate') }}"
                               placeholder="VD: 29A-123.45"
                               style="text-transform:uppercase;"
                               required>
                    </div>

                    <div class="vp-field">
                        <label class="vp-label">Loại phương tiện <span style="color:#dc2626;">*</span></label>
                        <select name="vehicle_type"
                                class="vp-input @error('vehicle_type') vp-input--err @enderror"
                                required>
                            <option value="" disabled {{ old('vehicle_type') ? '' : 'selected' }}>-- Chọn loại xe --</option>
                            <option value="motorbike" {{ old('vehicle_type') === 'motorbike' ? 'selected' : '' }}>Xe máy</option>
                            <option value="electric_bike" {{ old('vehicle_type') === 'electric_bike' ? 'selected' : '' }}>Xe điện</option>
                            <option value="car" {{ old('vehicle_type') === 'car' ? 'selected' : '' }}>Ô tô</option>
                        </select>
                    </div>
                </div>

                {{-- Hãng xe --}}
                <div class="vp-field">
                    <label class="vp-label">Hãng xe / Nhãn hiệu</label>
                    <input type="text"
                           name="brand"
                           class="vp-input @error('brand') vp-input--err @enderror"
                           value="{{ old('brand') }}"
                           placeholder="VD: Honda SH, VinFast VF3, Yamaha Exciter...">
                </div>

                {{-- Image Upload --}}
                <div class="vp-field">
                    <label class="vp-label">Ảnh phương tiện</label>
                    <div class="vp-upload" id="upload-box" onclick="document.getElementById('vehicle-image').click()">
                        <input type="file"
                               name="image"
                               id="vehicle-image"
                               class="vp-upload__input"
                               accept="image/*"
                               onchange="previewVehicleImage(this)">

                        <div id="upload-placeholder" class="vp-upload__placeholder">
                            <div style="background:#f1f5f9; color:#64748b; padding:12px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            <p>Bấm để tải ảnh xe lên</p>
                            <span>JPG, PNG, WEBP — tối đa 2MB</span>
                        </div>

                        <div id="upload-preview" class="vp-upload__preview" style="display:none;">
                            <img id="preview-img" src="" alt="Preview">
                            <div style="position:absolute; top:10px; right:10px; background:rgba(0,0,0,.6); color:#fff; padding:5px 12px; border-radius:6px; font-size:0.75rem; font-weight:600; cursor:pointer;" onclick="event.stopPropagation(); removePreview();">
                                Đổi ảnh
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="vp-panel__actions" style="margin-top:0.5rem;">
                    <a href="{{ route('resident.vehicles.index') }}" class="vp-btn vp-btn--outline">Hủy</a>
                    <button type="submit" class="vp-btn vp-btn--primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Gửi đăng ký
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewVehicleImage(input) {
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('upload-placeholder').style.display = 'none';
            document.getElementById('upload-preview').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
}
function removePreview() {
    document.getElementById('vehicle-image').value = '';
    document.getElementById('upload-preview').style.display = 'none';
    document.getElementById('upload-placeholder').style.display = 'flex';
}
</script>
@endsection
