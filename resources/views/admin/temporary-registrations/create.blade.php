@extends('layouts.admin.master')

@section('page_title', 'Tạo Đăng ký Tạm trú / Tạm vắng')

@push('styles')
<style>
    .tr-form-page {
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }
    
    .tr-header {
        margin-bottom: 24px;
    }

    .tr-title {
        font-size: 28px;
        font-weight: 700;
        color: #00236f;
        margin: 0 0 6px 0;
        line-height: 1.2;
    }

    .tr-subtitle {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    .tr-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 32px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
    }
    
    .form-label .required {
        color: #dc2626;
    }

    .form-control {
        width: 100%;
        height: 42px;
        border: 1px solid #d9e2f2;
        border-radius: 8px;
        padding: 0 14px;
        font-size: 14px;
        color: #0f172a;
        background-color: #ffffff;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }

    .form-control:focus {
        border-color: #00236f;
        box-shadow: 0 0 0 3px rgba(0, 35, 111, 0.08);
    }
    
    .form-control:disabled {
        background-color: #f8fafc;
        color: #94a3b8;
        cursor: not-allowed;
    }

    textarea.form-control {
        height: auto;
        padding: 12px 14px;
        resize: vertical;
    }

    .radio-group {
        display: flex;
        gap: 24px;
        margin-top: 8px;
    }

    .radio-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #334155;
        cursor: pointer;
    }

    .radio-input {
        width: 18px;
        height: 18px;
        accent-color: #00236f;
        cursor: pointer;
    }

    .file-upload-wrapper {
        position: relative;
    }
    
    .file-input {
        display: block;
        width: 100%;
        font-size: 14px;
        color: #64748b;
        border: 1px dashed #d9e2f2;
        border-radius: 8px;
        padding: 12px;
        background: #f8f9ff;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    
    .file-input:hover {
        border-color: #00236f;
    }
    
    .file-input::file-selector-button {
        background-color: #e0e7ff;
        color: #00236f;
        border: none;
        padding: 6px 16px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        margin-right: 12px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    
    .file-input::file-selector-button:hover {
        background-color: #c7d2fe;
    }

    .form-hint {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 6px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid #f1f5f9;
    }

    .btn {
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 24px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-secondary {
        background-color: #ffffff;
        border: 1px solid #cbd5e1;
        color: #475569;
    }

    .btn-secondary:hover {
        background-color: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    .btn-primary {
        background-color: #00236f;
        border: 1px solid #00236f;
        color: #ffffff;
    }

    .btn-primary:hover {
        background-color: #0b57d0;
        border-color: #0b57d0;
        box-shadow: 0 4px 6px rgba(0, 35, 111, 0.2);
    }
    
    .grid-cols-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .grid-cols-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 768px) {
        .grid-cols-2, .grid-cols-3 {
            grid-template-columns: 1fr;
        }
    }

    .resident-guest-section {
        margin-bottom: 24px;
    }

    .section-title {
        font-size: 14px;
        font-weight: 700;
        color: #00236f;
        margin: 0 0 16px 0;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>
@endpush

@section('content')
<div class="tr-form-page">
    <div class="tr-header">
        <h1 class="tr-title">Thêm mới Đăng ký</h1>
        <p class="tr-subtitle">Hệ thống sẽ tự động duyệt và ghi nhận thông tin cư dân tạm trú / tạm vắng.</p>
    </div>

    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; border: 1px solid #f87171;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="tr-card">
        <form action="{{ route('admin.temporary-registrations.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <h3 class="section-title">1. Chọn căn hộ</h3>
            <div class="grid-cols-3" style="margin-bottom: 24px;">
                <div class="form-group">
                    <label class="form-label">Tòa nhà <span class="required">*</span></label>
                    <select id="block_id" class="form-control">
                        <option value="">-- Chọn tòa nhà --</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}" data-floors="{{ json_encode($block->floors) }}">{{ $block->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tầng <span class="required">*</span></label>
                    <select id="floor_id" class="form-control" disabled>
                        <option value="">-- Chọn tầng --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Căn hộ <span class="required">*</span></label>
                    <select name="apartment_id" id="apartment_id" required class="form-control" disabled>
                        <option value="">-- Chọn căn hộ --</option>
                    </select>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #f1f5f9;">
                <label class="form-label">Loại đăng ký <span class="required">*</span></label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="type" value="residence" id="type_residence" required {{ old('type', 'residence') == 'residence' ? 'checked' : '' }} class="radio-input">
                        Tạm trú (Dành cho người mới chuyển đến)
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="type" value="absence" id="type_absence" required {{ old('type') == 'absence' ? 'checked' : '' }} class="radio-input">
                        Tạm vắng (Dành cho cư dân hiện tại)
                    </label>
                </div>
            </div>

            <div id="absence_section" style="display: none;">
                <h3 class="section-title">2. Thông tin cư dân tạm vắng</h3>
                <div class="form-group">
                    <label class="form-label">Cư dân <span class="required">*</span></label>
                    <select name="user_id" id="user_id" class="form-control">
                        <option value="">-- Chọn cư dân --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->phone }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-hint">Chỉ hiển thị các cư dân thuộc căn hộ đã chọn (Tính năng demo có toàn bộ danh sách).</div>
                </div>
            </div>

            <div id="residence_section" class="resident-guest-section">
                <h3 class="section-title">2. Thông tin người tạm trú</h3>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Họ và tên <span class="required">*</span></label>
                    <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}" class="form-control" placeholder="Nhập họ và tên...">
                </div>
                <div class="grid-cols-2">
                    <div class="form-group">
                        <label class="form-label">Số điện thoại <span class="required">*</span></label>
                        <input type="text" name="guest_phone" id="guest_phone" value="{{ old('guest_phone') }}" class="form-control" placeholder="Ví dụ: 0912345678">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CCCD/CMND <span class="required">*</span></label>
                        <input type="text" name="guest_cccd" id="guest_cccd" value="{{ old('guest_cccd') }}" class="form-control" placeholder="Số CCCD...">
                    </div>
                </div>
            </div>

            <h3 class="section-title">3. Chi tiết lưu trú</h3>
            <div class="grid-cols-2">
                <div class="form-group">
                    <label class="form-label">Từ ngày <span class="required">*</span></label>
                    <input type="date" name="start_date" required value="{{ old('start_date') }}" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control">
                    <div class="form-hint">Bỏ trống nếu chưa xác định ngày kết thúc</div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Lý do</label>
                <textarea name="reason" rows="3" class="form-control" placeholder="Nhập lý do chi tiết...">{{ old('reason') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Giấy tờ đính kèm (CCCD, Hợp đồng...)</label>
                <div class="file-upload-wrapper">
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="file-input">
                </div>
                <div class="form-hint">Định dạng hỗ trợ: JPG, PNG, PDF. Kích thước tối đa 2MB.</div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.temporary-registrations.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">Lưu thông tin</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const blockSelect = document.getElementById('block_id');
        const floorSelect = document.getElementById('floor_id');
        const apartmentSelect = document.getElementById('apartment_id');
        
        // Cascading Dropdowns
        blockSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            floorSelect.innerHTML = '<option value="">-- Chọn tầng --</option>';
            apartmentSelect.innerHTML = '<option value="">-- Chọn căn hộ --</option>';
            apartmentSelect.disabled = true;
            
            if (this.value) {
                const floors = JSON.parse(selectedOption.getAttribute('data-floors'));
                floors.forEach(floor => {
                    const option = document.createElement('option');
                    option.value = floor.id;
                    option.textContent = floor.name;
                    // Store apartments data for the next dropdown
                    option.setAttribute('data-apartments', JSON.stringify(floor.apartments));
                    floorSelect.appendChild(option);
                });
                floorSelect.disabled = false;
            } else {
                floorSelect.disabled = true;
            }
        });

        floorSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            apartmentSelect.innerHTML = '<option value="">-- Chọn căn hộ --</option>';
            
            if (this.value) {
                const apartments = JSON.parse(selectedOption.getAttribute('data-apartments'));
                apartments.forEach(apt => {
                    const option = document.createElement('option');
                    option.value = apt.id;
                    option.textContent = apt.apartment_number;
                    apartmentSelect.appendChild(option);
                });
                apartmentSelect.disabled = false;
            } else {
                apartmentSelect.disabled = true;
            }
        });

        // Toggle Resident/Guest Logic
        const typeResidence = document.getElementById('type_residence');
        const typeAbsence = document.getElementById('type_absence');
        const residenceSection = document.getElementById('residence_section');
        const absenceSection = document.getElementById('absence_section');
        
        const guestName = document.getElementById('guest_name');
        const guestPhone = document.getElementById('guest_phone');
        const guestCccd = document.getElementById('guest_cccd');
        const userId = document.getElementById('user_id');

        function toggleSections() {
            if (typeResidence.checked) {
                residenceSection.style.display = 'block';
                absenceSection.style.display = 'none';
                
                guestName.required = true;
                guestPhone.required = true;
                guestCccd.required = true;
                userId.required = false;
                userId.value = '';
            } else {
                residenceSection.style.display = 'none';
                absenceSection.style.display = 'block';
                
                guestName.required = false;
                guestPhone.required = false;
                guestCccd.required = false;
                userId.required = true;
            }
        }

        typeResidence.addEventListener('change', toggleSections);
        typeAbsence.addEventListener('change', toggleSections);

        // Run once on load to set initial state
        toggleSections();
    });
</script>
@endpush
@endsection
