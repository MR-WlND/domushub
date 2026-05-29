@extends('layouts.resident.master')

@section('title', 'Đăng ký xe – DomusHub')

@push('styles')
    @vite(['resources/css/resident/vehicles-index.css'])
@endpush

@section('content')

<div class="vp">

    {{-- HEADER --}}
    <div class="vp__header">
        <div>
            <p class="vp__eyebrow">Phương tiện</p>
            <h1 class="vp__title">Đăng ký xe mới</h1>
        </div>

        <a href="{{ route('resident.vehicles.index') }}"
           class="vp-btn vp-btn--outline" style="display: inline-flex; align-items: center; gap: 6px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Quay lại
        </a>
    </div>

    {{-- FORM PANEL CARD --}}
    <div class="vp-panel" style="position:static;transform:none;width:100%;max-width:700px;margin:auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 22px; box-shadow: 0 12px 34px rgba(15, 23, 42, .05);">

        <div class="vp-panel__body" style="padding: 2rem;">

            {{-- ALERT VALIDATION ERRORS --}}
            @if($errors->any())
                <div class="vp-alert vp-alert--error" style="margin-bottom:1.5rem; display: flex; flex-direction: column; align-items: flex-start; gap: 4px;">
                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 700;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        <span>Vui lòng hoàn thiện đúng các thông tin sau:</span>
                    </div>
                    <ul style="margin: 4px 0 0 24px; padding: 0; font-size: 0.9rem; line-height: 1.5;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST"
                  action="{{ route('resident.vehicles.store') }}"
                  enctype="multipart/form-data"
                  style="display: flex; flex-direction: column; gap: 1.25rem;">

                @csrf

                {{-- GRID DUAL COLUMNS: BIỂN SỐ & LOẠI XE --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem;">
                    <div class="vp-field">
                        <label class="vp-label" style="display: block; font-weight: 600; font-size: 0.9rem; color: #1e293b; margin-bottom: 0.5rem;">
                            Biển số xe <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="text"
                               name="license_plate"
                               class="vp-input @error('license_plate') vp-input--err @enderror"
                               value="{{ old('license_plate') }}"
                               placeholder="Ví dụ: 29A-123.45"
                               style="text-transform: uppercase;"
                               required>
                    </div>

                    <div class="vp-field">
                        <label class="vp-label" style="display: block; font-weight: 600; font-size: 0.9rem; color: #1e293b; margin-bottom: 0.5rem;">
                            Loại phương tiện <span style="color: #ef4444;">*</span>
                        </label>
                        <div style="position: relative; display: flex; align-items: center;">
                            <select name="vehicle_type"
                                    class="vp-input @error('vehicle_type') vp-input--err @enderror"
                                    style="width: 100%; padding-right: 40px; appearance: none; -webkit-appearance: none; background: #fff;"
                                    required>
                                <option value="" disabled {{ old('vehicle_type') ? '' : 'selected' }}>-- Chọn loại xe --</option>
                                <option value="xe máy" {{ old('vehicle_type') === 'xe máy' ? 'selected' : '' }}>Xe máy</option>
                                <option value="xe điện" {{ old('vehicle_type') === 'xe điện' ? 'selected' : '' }}>Xe điện</option>
                                <option value="ô tô" {{ old('vehicle_type') === 'ô tô' ? 'selected' : '' }}>Ô tô</option>
                            </select>
                            <div class="vp-chevron">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- HÃNG XE --}}
                <div class="vp-field">
                    <label class="vp-label" style="display: block; font-weight: 600; font-size: 0.9rem; color: #1e293b; margin-bottom: 0.5rem;">
                        Hãng xe / Nhãn hiệu
                    </label>
                    <input type="text"
                           name="brand"
                           class="vp-input @error('brand') vp-input--err @enderror"
                           value="{{ old('brand') }}"
                           placeholder="Ví dụ: Honda SH, VinFast VF3, Yamaha Exciter...">
                </div>

                {{-- KHU VỰC TẢI ẢNH CHUYÊN NGHIỆP --}}
                <div class="vp-field">
                    <label class="vp-label" style="display: block; font-weight: 600; font-size: 0.9rem; color: #1e293b; margin-bottom: 0.5rem;">
                        Ảnh đăng ký phương tiện
                    </label>

                    <div class="vp-upload" id="upload-box" onclick="document.getElementById('vehicle-image').click()">
                        <input type="file"
                               name="image"
                               id="vehicle-image"
                               class="vp-upload__input"
                               accept="image/*"
                               onchange="previewVehicleImage(this)">

                        {{-- Trạng thái chờ ảnh --}}
                        <div id="upload-placeholder" class="vp-upload__placeholder">
                            <div style="background: #eff6ff; color: #1d4ed8; padding: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                            </div>
                            <p>Nhấp vào đây để tải ảnh xe lên</p>
                            <span>Định dạng hỗ trợ: JPG, PNG, WEBP (Tối đa 2MB)</span>
                        </div>

                        {{-- Trạng thái đã chọn ảnh thành công --}}
                        <div id="upload-preview" class="vp-upload__preview" style="display: none;">
                            <img id="preview-img" src="" alt="Ảnh xem trước">
                            <div style="position: absolute; top: 12px; right: 12px; background: rgba(15, 23, 42, 0.75); color: #fff; padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; backdrop-filter: blur(4px);" onclick="event.stopPropagation(); removeVehiclePreview();">
                                Thay đổi ảnh khác
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ACTION FOOTER --}}
                <div class="vp-panel__actions" style="margin-top: 0.5rem; display: flex; justify-content: flex-end; gap: 12px;">
                    <a href="{{ route('resident.vehicles.index') }}"
                       class="vp-btn vp-btn--outline">
                        Hủy bỏ
                    </a>

                    <button type="submit"
                            class="vp-btn vp-btn--primary" style="display: inline-flex; align-items: center; gap: 6px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        Gửi đăng ký duyệt
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- JAVASCRIPT ĐIỀU KHIỂN UPLOAD PREVIEW KHỚP HOÀN HẢO VỚI LỚP CSS --}}
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

function removeVehiclePreview() {
    document.getElementById('vehicle-image').value = "";
    document.getElementById('upload-preview').style.display = 'none';
    document.getElementById('upload-placeholder').style.display = 'flex';
}
</script>

@endsection
