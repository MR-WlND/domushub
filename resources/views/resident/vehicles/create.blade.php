@extends('layouts.resident.master')
@section('title', 'Đăng ký xe – DomusHub')
@push('styles')
    @vite(['resources/css/resident/vehicles.css'])
@endpush

@section('content')
<div class="rv">
    <div class="rv-header">
        <div>
            <div class="rv__breadcrumb"><a href="{{ route('resident.vehicles.index') }}">Phương tiện</a> › <strong>Đăng ký mới</strong></div>
            <h1 class="rv__title">Đăng ký Phương tiện mới</h1>
        </div>
    </div>

    @if($errors->any())
    <div class="rv-alert rv-alert--error">
        @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
    </div>
    @endif

    <div class="rv-create">
        {{-- Form --}}
        <div class="rv-form-card">
            <form method="POST" action="{{ route('resident.vehicles.store') }}" enctype="multipart/form-data" class="rv-form">
                @csrf
                <div class="rv-row">
                    <div class="rv-field">
                        <label>Loại phương tiện <span>*</span></label>
                        <select name="vehicle_type" class="rv-input" required>
                            <option value="" disabled {{ old('vehicle_type') ? '' : 'selected' }}>-- Chọn --</option>
                            <option value="car" {{ old('vehicle_type')==='car'?'selected':'' }}>Ô tô</option>
                            <option value="motorbike" {{ old('vehicle_type')==='motorbike'?'selected':'' }}>Xe máy</option>
                            <option value="electric_bike" {{ old('vehicle_type')==='electric_bike'?'selected':'' }}>Xe điện</option>
                            <option value="bicycle" {{ old('vehicle_type')==='bicycle'?'selected':'' }}>Xe đạp</option>
                        </select>
                    </div>
                    <div class="rv-field" id="license-plate-field">
                        <label>Biển số xe <span>*</span></label>
                        <input type="text" name="license_plate" id="license_plate_input" class="rv-input" value="{{ old('license_plate') }}" placeholder="VD: 30A-123.45" style="text-transform:uppercase;">
                    </div>
                </div>

                <div class="rv-row">
                    <div class="rv-field">
                        <label>Thương hiệu & Model</label>
                        <input type="text" name="brand" class="rv-input" value="{{ old('brand') }}" placeholder="VD: Toyota Camry 2023">
                    </div>
                    <div class="rv-field">
                        <label>Chủ sở hữu</label>
                        <input type="text" class="rv-input" value="{{ auth()->user()->name }}" disabled style="background:#f8fafc;">
                    </div>
                </div>

                <div class="rv-field">
                    <label>Ảnh phương tiện</label>
                    <div class="rv-upload" onclick="document.getElementById('rv-img1').click()" style="width:100%;">
                        <input type="file" name="image" id="rv-img1" accept="image/*" onchange="rvPreview(this,'rv-ph1','rv-pv1')">
                        <div id="rv-ph1" class="rv-upload__ph">
                            <div class="rv-upload__icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            </div>
                            <p class="rv-upload__text">Kéo thả hoặc bấm để tải lên</p>
                            <p class="rv-upload__hint">Định dạng JPG, PNG (Tối đa 2MB)</p>
                        </div>
                        <div id="rv-pv1" class="rv-upload__preview"><img src="" alt=""></div>
                    </div>
                </div>

                <div class="rv-actions">
                    <a href="{{ route('resident.vehicles.index') }}" class="rv-btn rv-btn--outline">Hủy bỏ</a>
                    <button type="submit" class="rv-btn rv-btn--primary">Gửi yêu cầu đăng ký</button>
                </div>
            </form>
        </div>

        {{-- Sidebar --}}
        <div class="rv-sidebar">
            <div class="rv-info-card">
                <h4 class="rv-info-card__title">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Quy định & Chính sách
                </h4>
                <ul class="rv-info-card__list">
                    <li>Mỗi căn hộ được đăng ký tối đa 01 ô tô và 03 xe máy.</li>
                    <li>Phương tiện phải chính chủ hoặc có giấy ủy quyền sử dụng hợp lệ.</li>
                    <li>Phí trông giữ được tính theo tháng và tự động cộng vào hóa đơn dịch vụ.</li>
                    <li>Vui lòng tuân thủ biển báo và hướng dẫn của nhân viên điều phối bãi xe.</li>
                </ul>
                <p class="rv-info-card__note">* Phí sẽ được cộng trực tiếp vào thông báo phí hàng tháng sau khi yêu cầu được duyệt.</p>
            </div>

            <div class="rv-steps-card">
                <h4 class="rv-steps-card__title">Quy trình duyệt</h4>
                <div class="rv-steps">
                    <div class="rv-step">
                        <div class="rv-step__num rv-step__num--active">1</div>
                        <div><span class="rv-step__label">Gửi yêu cầu</span><span class="rv-step__desc">Cư dân hoàn tất và gửi form.</span></div>
                    </div>
                    <div class="rv-step">
                        <div class="rv-step__num rv-step__num--inactive">2</div>
                        <div><span class="rv-step__label">Kiểm tra hồ sơ</span><span class="rv-step__desc">Ban quản lý (BQL) xem xét giấy tờ (24-48h).</span></div>
                    </div>
                    <div class="rv-step">
                        <div class="rv-step__num rv-step__num--inactive">3</div>
                        <div><span class="rv-step__label">Cấp thẻ/QR</span><span class="rv-step__desc">Sau khi duyệt, cư dân nhận mã QR.</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    @vite(['resources/js/pages/resident/vehicles/create.js'])
@endpush
