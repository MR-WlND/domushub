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

    .cccd-capture-group { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }

    @media (max-width: 768px) {
        .grid-cols-2, .grid-cols-3 {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .radio-group {
            flex-direction: column;
            gap: 12px;
        }
        .cccd-capture-group {
            grid-template-columns: 1fr;
        }
        .tr-card {
            padding: 16px;
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

    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px; font-size: 14px; font-weight: 500;">
            {{ session('error') }}
        </div>
    @endif
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
                    <select name="block_id" id="block_id" class="form-control" required>
                        <option value="">-- Chọn tòa nhà --</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}" data-floors="{{ json_encode($block->floors) }}">{{ $block->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tầng <span class="required">*</span></label>
                    <select name="floor_id" id="floor_id" class="form-control" required disabled>
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
                <div class="grid-cols-2">
                    <div class="form-group">
                        <label class="form-label">Họ và tên <span class="required">*</span></label>
                        <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name', $extendRegistration ? $extendRegistration->guest_name : '') }}" class="form-control" placeholder="Nhập họ và tên...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="guest_email" id="guest_email" value="{{ old('guest_email', $extendRegistration ? $extendRegistration->guest_email : '') }}" class="form-control" placeholder="Nhập email (không bắt buộc)">
                    </div>
                </div>
                <div class="grid-cols-3">
                    <div class="form-group">
                        <label class="form-label">Số điện thoại <span class="required">*</span></label>
                        <input type="text" name="guest_phone" id="guest_phone" value="{{ old('guest_phone', $extendRegistration ? $extendRegistration->guest_phone : '') }}" class="form-control" placeholder="Ví dụ: 0912345678">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CCCD/CMND <span class="required">*</span></label>
                        <input type="text" name="guest_cccd" id="guest_cccd" value="{{ old('guest_cccd', $extendRegistration ? $extendRegistration->guest_cccd : '') }}" class="form-control" placeholder="Số CCCD...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Giới tính</label>
                        <select name="guest_gender" class="form-control">
                            <option value="">-- Chọn giới tính --</option>
                            <option value="male" {{ old('guest_gender', $extendRegistration ? $extendRegistration->guest_gender : '') == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('guest_gender', $extendRegistration ? $extendRegistration->guest_gender : '') == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('guest_gender', $extendRegistration ? $extendRegistration->guest_gender : '') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                </div>
                <div class="grid-cols-3">
                    <div class="form-group">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="guest_dob" value="{{ old('guest_dob', ($extendRegistration && $extendRegistration->guest_dob) ? $extendRegistration->guest_dob->format('Y-m-d') : '') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quê quán / Thường trú</label>
                        <input type="text" name="guest_hometown" value="{{ old('guest_hometown', $extendRegistration ? $extendRegistration->guest_hometown : '') }}" class="form-control" placeholder="Nhập quê quán...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mối quan hệ với chủ hộ</label>
                        <select name="relationship" class="form-control">
                            <option value="">-- Chọn --</option>
                            <option value="Khách thuê" {{ old('relationship', $extendRegistration ? $extendRegistration->relationship : '') == 'Khách thuê' ? 'selected' : '' }}>Khách thuê</option>
                            <option value="Người nhà" {{ old('relationship', $extendRegistration ? $extendRegistration->relationship : '') == 'Người nhà' ? 'selected' : '' }}>Người nhà</option>
                            <option value="Giúp việc" {{ old('relationship', $extendRegistration ? $extendRegistration->relationship : '') == 'Giúp việc' ? 'selected' : '' }}>Giúp việc</option>
                            <option value="Khác" {{ old('relationship', $extendRegistration ? $extendRegistration->relationship : '') == 'Khác' ? 'selected' : '' }}>Khác</option>
                        </select>
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
                <label class="form-label">Ảnh Căn cước công dân (Mặt trước & mặt sau)</label>
                <div class="cccd-capture-group">
                    <button type="button" onclick="openGlobalCameraModal('CCCD_Mat_Truoc')" style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; padding: 24px 12px; text-align: center; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; transition: all 0.2s; color: #00236f; width: 100%; box-sizing: border-box;" onmouseover="this.style.background='#eff6ff'; this.style.borderColor='#93c5fd';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 4px"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <span style="font-weight: 600; font-size: 14px;">Chụp mặt trước</span>
                    </button>
                    <button type="button" onclick="openGlobalCameraModal('CCCD_Mat_Sau')" style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; padding: 24px 12px; text-align: center; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px; transition: all 0.2s; color: #00236f; width: 100%; box-sizing: border-box;" onmouseover="this.style.background='#eff6ff'; this.style.borderColor='#93c5fd';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 4px"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <span style="font-weight: 600; font-size: 14px;">Chụp mặt sau</span>
                    </button>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; margin-bottom: 8px;">
                    <div style="font-size: 13px; color: #64748b; font-weight: 500; white-space: nowrap;">Hoặc tải lên từ máy:</div>
                    <label for="admin-file-input" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px 16px; cursor: pointer; color: #334155; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#94a3b8';" onmouseout="this.style.background='#ffffff'; this.style.borderColor='#cbd5e1';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/></svg>
                        Chọn tệp
                    </label>
                    <span id="admin-file-name-display" style="font-size: 14px; color: #64748b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 200px;">Không có tệp nào được chọn</span>
                    <input type="file" name="attachments[]" accept="image/*, application/pdf" class="file-input" id="admin-file-input" multiple style="display: none;" onchange="document.getElementById('admin-file-name-display').textContent = this.files.length > 0 ? (this.files.length + ' tệp được chọn') : 'Không có tệp nào được chọn';">
                </div>
                <div class="form-hint">Định dạng hỗ trợ: JPG, PNG, PDF. Kích thước tối đa 10MB/file. Có thể chọn nhiều file.</div>
                
                <!-- Preview container -->
                <div id="admin-file-preview-container" style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 16px;"></div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.temporary-registrations.index') }}" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">Lưu thông tin</button>
            </div>
        </form>
    </div>
</div>

@include('components.webrtc-camera')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const blockSelect = document.getElementById('block_id');
        const floorSelect = document.getElementById('floor_id');
        const apartmentSelect = document.getElementById('apartment_id');
        
        const oldBlockId = "{{ old('block_id') }}";
        const oldFloorId = "{{ old('floor_id') }}";
        const oldApartmentId = "{{ old('apartment_id') }}";

        // Cascading Dropdowns
        blockSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            floorSelect.innerHTML = '<option value="">-- Chọn tầng --</option>';
            apartmentSelect.innerHTML = '<option value="">-- Chọn căn hộ --</option>';
            apartmentSelect.disabled = true;
            
            if (this.value && selectedOption.hasAttribute('data-floors')) {
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
                
                if (oldFloorId && !floorSelect.value) {
                    floorSelect.value = oldFloorId;
                    floorSelect.dispatchEvent(new Event('change'));
                }
            } else {
                floorSelect.disabled = true;
            }
        });

        floorSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            apartmentSelect.innerHTML = '<option value="">-- Chọn căn hộ --</option>';
            
            if (this.value && selectedOption.hasAttribute('data-apartments')) {
                const apartments = JSON.parse(selectedOption.getAttribute('data-apartments'));
                apartments.forEach(apt => {
                    const option = document.createElement('option');
                    option.value = apt.id;
                    option.textContent = apt.apartment_number;
                    apartmentSelect.appendChild(option);
                });
                apartmentSelect.disabled = false;
                
                if (oldApartmentId && !apartmentSelect.value) {
                    apartmentSelect.value = oldApartmentId;
                }
            } else {
                apartmentSelect.disabled = true;
            }
        });

        // Restore old values if validation failed
        if (oldBlockId) {
            blockSelect.value = oldBlockId;
            blockSelect.dispatchEvent(new Event('change'));
        }

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

        // Image Preview and Multiple Files Logic for Admin
        const adminFileInput = document.getElementById('admin-file-input');
        const adminPreviewContainer = document.getElementById('admin-file-preview-container');
        let adminSelectedFiles = new DataTransfer();

        if (adminFileInput) {
            adminFileInput.addEventListener('change', function(e) {
                const newFiles = Array.from(e.target.files);
                
                newFiles.forEach(file => {
                    adminSelectedFiles.items.add(file);
                });
                
                adminFileInput.files = adminSelectedFiles.files;
                renderAdminPreviews();
            });

            window.addEventListener('global-camera-captured', function(e) {
                adminSelectedFiles.items.add(e.detail.file);
                adminFileInput.files = adminSelectedFiles.files;
                
                const display = document.getElementById('admin-file-name-display');
                if (display) display.textContent = adminSelectedFiles.files.length > 0 ? (adminSelectedFiles.files.length + ' tệp được chọn') : 'Không có tệp nào được chọn';
                
                renderAdminPreviews();
            });
            
            function removeAdminFile(index) {
                const dt = new DataTransfer();
                const files = Array.from(adminSelectedFiles.files);
                files.splice(index, 1);
                files.forEach(f => dt.items.add(f));
                adminSelectedFiles = dt;
                adminFileInput.files = adminSelectedFiles.files;
                
                const display = document.getElementById('admin-file-name-display');
                if (display) display.textContent = adminSelectedFiles.files.length > 0 ? (adminSelectedFiles.files.length + ' tệp được chọn') : 'Không có tệp nào được chọn';
                
                renderAdminPreviews();
            }

            function renderAdminPreviews() {
                adminPreviewContainer.innerHTML = '';
                
                Array.from(adminSelectedFiles.files).forEach((file, index) => {
                    const reader = new FileReader();
                    
                    const wrapper = document.createElement('div');
                    wrapper.style.position = 'relative';
                    wrapper.style.width = '100px';
                    wrapper.style.height = '100px';
                    wrapper.style.borderRadius = '8px';
                    wrapper.style.overflow = 'hidden';
                    wrapper.style.border = '1px solid #e2e8f0';
                    wrapper.style.display = 'flex';
                    wrapper.style.alignItems = 'center';
                    wrapper.style.justifyContent = 'center';
                    wrapper.style.backgroundColor = '#f8fafc';
                    
                    const removeBtn = document.createElement('div');
                    removeBtn.innerHTML = '&times;';
                    removeBtn.style.position = 'absolute';
                    removeBtn.style.top = '4px';
                    removeBtn.style.right = '4px';
                    removeBtn.style.background = 'rgba(0,0,0,0.6)';
                    removeBtn.style.color = '#fff';
                    removeBtn.style.borderRadius = '50%';
                    removeBtn.style.width = '20px';
                    removeBtn.style.height = '20px';
                    removeBtn.style.display = 'flex';
                    removeBtn.style.alignItems = 'center';
                    removeBtn.style.justifyContent = 'center';
                    removeBtn.style.cursor = 'pointer';
                    removeBtn.style.fontSize = '14px';
                    removeBtn.style.zIndex = '10';
                    removeBtn.onclick = function(e) {
                        e.preventDefault();
                        removeAdminFile(index);
                    };
                    
                    if (file.type.startsWith('image/')) {
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.width = '100%';
                            img.style.height = '100%';
                            img.style.objectFit = 'cover';
                            wrapper.appendChild(img);
                            
                            let labelText = '';
                            if (file.name.startsWith('CCCD_Mat_Truoc')) labelText = 'Mặt trước';
                            else if (file.name.startsWith('CCCD_Mat_Sau')) labelText = 'Mặt sau';
                            
                            if (labelText) {
                                const label = document.createElement('div');
                                label.textContent = labelText;
                                label.style.position = 'absolute';
                                label.style.bottom = '0';
                                label.style.left = '0';
                                label.style.right = '0';
                                label.style.background = 'rgba(0, 35, 111, 0.75)';
                                label.style.color = '#fff';
                                label.style.fontSize = '11px';
                                label.style.fontWeight = '600';
                                label.style.textAlign = 'center';
                                label.style.padding = '4px 0';
                                wrapper.appendChild(label);
                            }
                            
                            wrapper.appendChild(removeBtn);
                        }
                        reader.readAsDataURL(file);
                    } else if (file.type === 'application/pdf') {
                        wrapper.innerHTML = '<div style="text-align: center; color: #dc2626;"><i class="fa-solid fa-file-pdf" style="font-size: 24px; margin-bottom: 8px;"></i><div style="font-size: 11px; font-weight: 600; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 90px;">' + file.name + '</div></div>';
                        wrapper.appendChild(removeBtn);
                    }
                    
                    adminPreviewContainer.appendChild(wrapper);
                });
            }
        }
    });
</script>
@endpush
@endsection
