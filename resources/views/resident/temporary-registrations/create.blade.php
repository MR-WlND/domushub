@extends('layouts.resident.master')

@section('title', 'Tạo Đăng ký Tạm trú / Tạm vắng')

@push('styles')
<style>
    .tr-form-page { font-family: 'Inter', 'Segoe UI', sans-serif; padding: 24px; }
    .tr-header { margin-bottom: 24px; }
    .tr-title { font-size: 24px; font-weight: 700; color: #00236f; margin: 0 0 6px 0; }
    .tr-subtitle { font-size: 14px; color: #64748b; margin: 0; }
    .tr-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; }
    .form-group { margin-bottom: 20px; }
    .form-label { display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px; }
    .form-label .required { color: #dc2626; }
    .form-control { width: 100%; height: 42px; border: 1px solid #d9e2f2; border-radius: 8px; padding: 0 14px; font-size: 14px; }
    .radio-group { display: flex; gap: 24px; margin-top: 8px; }
    .radio-label { display: flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer; }
    .grid-cols-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .grid-cols-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
    .section-title { font-size: 14px; font-weight: 700; color: #00236f; margin: 0 0 16px 0; text-transform: uppercase; }
    .btn { height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0 24px; font-weight: 600; border-radius: 8px; cursor: pointer; border: none; }
    .btn-primary { background-color: #00236f; color: #fff; }
    .btn-secondary { background-color: #f1f5f9; color: #475569; text-decoration: none; }
</style>
@endpush

@section('content')
<div class="tr-form-page">
    <div class="tr-header">
        <h1 class="tr-title">Đăng ký Tạm trú / Tạm vắng</h1>
        <p class="tr-subtitle">Gửi yêu cầu lên Ban quản lý để được xét duyệt.</p>
    </div>

    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="tr-card">
        <form action="{{ route('resident.temporary-registrations.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group" style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #f1f5f9;">
                <label class="form-label">Loại đăng ký <span class="required">*</span></label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="type" value="residence" id="type_residence" required {{ old('type', 'residence') == 'residence' ? 'checked' : '' }}>
                        Tạm trú (Đăng ký cho khách/người thân đến ở)
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="type" value="absence" id="type_absence" required {{ old('type') == 'absence' ? 'checked' : '' }}>
                        Tạm vắng (Chủ hộ/cư dân vắng mặt dài ngày)
                    </label>
                </div>
            </div>

            <div id="residence_section">
                <h3 class="section-title">Thông tin người tạm trú</h3>
                <div class="grid-cols-2">
                    <div class="form-group">
                        <label class="form-label">Họ và tên <span class="required">*</span></label>
                        <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="guest_email" id="guest_email" value="{{ old('guest_email') }}" class="form-control">
                    </div>
                </div>
                <div class="grid-cols-3">
                    <div class="form-group">
                        <label class="form-label">Số điện thoại <span class="required">*</span></label>
                        <input type="text" name="guest_phone" id="guest_phone" value="{{ old('guest_phone') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CCCD/CMND <span class="required">*</span></label>
                        <input type="text" name="guest_cccd" id="guest_cccd" value="{{ old('guest_cccd') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Giới tính</label>
                        <select name="guest_gender" class="form-control">
                            <option value="">-- Chọn --</option>
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                </div>
                <div class="grid-cols-3">
                    <div class="form-group">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="guest_dob" value="{{ old('guest_dob') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quê quán</label>
                        <input type="text" name="guest_hometown" value="{{ old('guest_hometown') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mối quan hệ</label>
                        <select name="relationship" class="form-control">
                            <option value="">-- Chọn --</option>
                            <option value="Khách thuê">Khách thuê</option>
                            <option value="Người nhà">Người nhà</option>
                            <option value="Giúp việc">Giúp việc</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="absence_section" style="display: none; margin-bottom: 24px;">
                <p style="color: #64748b; font-size: 14px;">Bạn đang đăng ký tạm vắng cho chính mình.</p>
            </div>

            <h3 class="section-title">Chi tiết lưu trú</h3>
            <div class="grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Từ ngày <span class="required">*</span></label>
                    <input type="date" name="start_date" required value="{{ old('start_date') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Lý do</label>
                <textarea name="reason" rows="3" class="form-control" style="height: auto; padding: 12px;">{{ old('reason') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Giấy tờ đính kèm</label>
                <input type="file" name="attachments[]" accept=".jpg,.jpeg,.png,.pdf" class="form-control" style="padding-top: 8px;" multiple>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Có thể chọn nhiều file.</div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Gửi đăng ký</button>
                <a href="{{ route('resident.temporary-registrations.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeResidence = document.getElementById('type_residence');
        const typeAbsence = document.getElementById('type_absence');
        const residenceSection = document.getElementById('residence_section');
        const absenceSection = document.getElementById('absence_section');
        
        function toggleSections() {
            if (typeResidence.checked) {
                residenceSection.style.display = 'block';
                absenceSection.style.display = 'none';
            } else {
                residenceSection.style.display = 'none';
                absenceSection.style.display = 'block';
            }
        }
        
        typeResidence.addEventListener('change', toggleSections);
        typeAbsence.addEventListener('change', toggleSections);
        toggleSections();
    });
</script>
@endpush
@endsection
