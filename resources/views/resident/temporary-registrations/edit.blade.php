@extends('layouts.resident.master')

@section('title', 'Chỉnh sửa Đăng ký Tạm trú / Tạm vắng')

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
        <h1 class="tr-title">Chỉnh sửa Đăng ký Tạm trú / Tạm vắng</h1>
        <p class="tr-subtitle">Chỉnh sửa và gửi lại đơn đăng ký đã bị từ chối.</p>
    </div>

    @if(session('error'))
        <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    @if($temporaryRegistration->status == 'rejected' && $temporaryRegistration->rejection_reason)
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
            <div style="font-weight: 700; margin-bottom: 4px;">Lý do từ chối trước đó:</div>
            <div>{{ $temporaryRegistration->rejection_reason }}</div>
        </div>
    @endif

    <div class="tr-card">
        <form action="{{ route('resident.temporary-registrations.update', $temporaryRegistration->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group" style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #f1f5f9;">
                <label class="form-label">Loại đăng ký <span class="required">*</span></label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="type" value="residence" id="type_residence" required {{ old('type', $temporaryRegistration->type) == 'residence' ? 'checked' : '' }}>
                        Tạm trú (Đăng ký cho khách/người thân đến ở)
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="type" value="absence" id="type_absence" required {{ old('type', $temporaryRegistration->type) == 'absence' ? 'checked' : '' }}>
                        Tạm vắng (Chủ hộ/cư dân vắng mặt dài ngày)
                    </label>
                </div>
            </div>

            <div id="residence_section">
                <h3 class="section-title">Thông tin người tạm trú</h3>
                <div class="grid-cols-2">
                    <div class="form-group">
                        <label class="form-label">Họ và tên <span class="required">*</span></label>
                        <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name', $temporaryRegistration->guest_name) }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="guest_email" id="guest_email" value="{{ old('guest_email', $temporaryRegistration->guest_email) }}" class="form-control">
                    </div>
                </div>
                <div class="grid-cols-3">
                    <div class="form-group">
                        <label class="form-label">Số điện thoại <span class="required">*</span></label>
                        <input type="text" name="guest_phone" id="guest_phone" value="{{ old('guest_phone', $temporaryRegistration->guest_phone) }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CCCD/CMND <span class="required">*</span></label>
                        <input type="text" name="guest_cccd" id="guest_cccd" value="{{ old('guest_cccd', $temporaryRegistration->guest_cccd) }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Giới tính</label>
                        <select name="guest_gender" class="form-control">
                            <option value="">-- Chọn --</option>
                            <option value="male" {{ old('guest_gender', $temporaryRegistration->guest_gender) == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('guest_gender', $temporaryRegistration->guest_gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('guest_gender', $temporaryRegistration->guest_gender) == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>
                </div>
                <div class="grid-cols-3">
                    <div class="form-group">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="guest_dob" value="{{ old('guest_dob', $temporaryRegistration->guest_dob ? $temporaryRegistration->guest_dob->format('Y-m-d') : '') }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Quê quán</label>
                        <input type="text" name="guest_hometown" value="{{ old('guest_hometown', $temporaryRegistration->guest_hometown) }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mối quan hệ</label>
                        <select name="relationship" class="form-control">
                            <option value="">-- Chọn --</option>
                            <option value="Khách thuê" {{ old('relationship', $temporaryRegistration->relationship) == 'Khách thuê' ? 'selected' : '' }}>Khách thuê</option>
                            <option value="Người nhà" {{ old('relationship', $temporaryRegistration->relationship) == 'Người nhà' ? 'selected' : '' }}>Người nhà</option>
                            <option value="Giúp việc" {{ old('relationship', $temporaryRegistration->relationship) == 'Giúp việc' ? 'selected' : '' }}>Giúp việc</option>
                            <option value="Khác" {{ old('relationship', $temporaryRegistration->relationship) == 'Khác' ? 'selected' : '' }}>Khác</option>
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
                    <input type="date" name="start_date" required value="{{ old('start_date', $temporaryRegistration->start_date ? $temporaryRegistration->start_date->format('Y-m-d') : '') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $temporaryRegistration->end_date ? $temporaryRegistration->end_date->format('Y-m-d') : '') }}" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Lý do</label>
                <textarea name="reason" rows="3" class="form-control" style="height: auto; padding: 12px;">{{ old('reason', $temporaryRegistration->reason) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Ảnh Căn cước công dân (Mặt trước & mặt sau)</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                    <button type="button" onclick="openGlobalCameraModal('CCCD_Mat_Truoc')" style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; padding: 20px 12px; text-align: center; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; color: #00236f; width: 100%; box-sizing: border-box;" onmouseover="this.style.background='#eff6ff'; this.style.borderColor='#93c5fd';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 4px"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <span style="font-weight: 600; font-size: 14px;">Chụp mặt trước</span>
                    </button>
                    <button type="button" onclick="openGlobalCameraModal('CCCD_Mat_Sau')" style="background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 8px; padding: 20px 12px; text-align: center; cursor: pointer; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; color: #00236f; width: 100%; box-sizing: border-box;" onmouseover="this.style.background='#eff6ff'; this.style.borderColor='#93c5fd';" onmouseout="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 4px"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <span style="font-weight: 600; font-size: 14px;">Chụp mặt sau</span>
                    </button>
                </div>
                <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 8px;">
                    <div style="font-size: 13px; color: #64748b; font-weight: 500; white-space: nowrap;">Hoặc tải lên:</div>
                    <input type="file" name="attachments[]" accept="image/*, application/pdf" class="form-control" style="flex: 1; height: 42px; padding: 7px 14px; box-sizing: border-box; margin: 0;" id="file-input" multiple>
                </div>
                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;">Tải lên file mới sẽ không xoá file cũ trừ khi bạn nhấn dấu X ở file cũ. Tối đa 10MB mỗi file.</div>
                
                <!-- Old Attachments -->
                @if($temporaryRegistration->attachments && count($temporaryRegistration->attachments) > 0)
                    <div style="margin-top: 16px;">
                        <div style="font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px;">File đã tải lên:</div>
                        <div style="display: flex; gap: 12px; flex-wrap: wrap;" id="old-attachments-container">
                            @foreach($temporaryRegistration->attachments as $path)
                                <div class="old-attachment-item" style="position: relative; width: 100px; height: 100px; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; background-color: #f8fafc;" data-path="{{ $path }}">
                                    @if(preg_match('/\.(jpg|jpeg|png)$/i', $path))
                                        <img src="{{ Storage::url($path) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <div style="text-align: center; color: #dc2626;">
                                            <i class="fa-solid fa-file-pdf" style="font-size: 24px; margin-bottom: 8px;"></i>
                                            <div style="font-size: 11px; font-weight: 600; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 90px;">PDF Document</div>
                                        </div>
                                    @endif
                                    
                                    <label style="position: absolute; top: 4px; right: 4px; background: rgba(0,0,0,0.6); color: #fff; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; margin: 0;">
                                        <input type="checkbox" name="remove_attachments[]" value="{{ $path }}" style="display: none;" class="remove-attachment-cb">
                                        <span class="remove-icon" style="font-size: 14px;">&times;</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- New File Preview container -->
                <div id="file-preview-container" style="display: flex; gap: 12px; flex-wrap: wrap; margin-top: 16px;"></div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Lưu và gửi lại</button>
                <a href="{{ route('resident.temporary-registrations.index') }}" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

@include('components.webrtc-camera')

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
        
        // Handle old attachments removal visual
        const oldCheckboxes = document.querySelectorAll('.remove-attachment-cb');
        oldCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const parent = this.closest('.old-attachment-item');
                if (this.checked) {
                    parent.style.opacity = '0.3';
                } else {
                    parent.style.opacity = '1';
                }
            });
        });

        // Image Preview and Multiple Files Logic
        const fileInput = document.getElementById('file-input');
        const previewContainer = document.getElementById('file-preview-container');
        let selectedFiles = new DataTransfer();

        fileInput.addEventListener('change', function(e) {
            const newFiles = Array.from(e.target.files);
            
            newFiles.forEach(file => {
                selectedFiles.items.add(file);
            });
            
            fileInput.files = selectedFiles.files;
            renderPreviews();
        });

        document.addEventListener('global-camera-captured', function(e) {
            selectedFiles.items.add(e.detail.file);
            fileInput.files = selectedFiles.files;
            renderPreviews();
        });
        
        function removeFile(index) {
            const dt = new DataTransfer();
            const files = Array.from(selectedFiles.files);
            files.splice(index, 1);
            files.forEach(f => dt.items.add(f));
            selectedFiles = dt;
            fileInput.files = selectedFiles.files;
            renderPreviews();
        }

        function renderPreviews() {
            previewContainer.innerHTML = '';
            
            Array.from(selectedFiles.files).forEach((file, index) => {
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
                    removeFile(index);
                };
                
                if (file.type.startsWith('image/')) {
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        wrapper.appendChild(img);
                        wrapper.appendChild(removeBtn);
                    }
                    reader.readAsDataURL(file);
                } else if (file.type === 'application/pdf') {
                    wrapper.innerHTML = '<div style="text-align: center; color: #dc2626;"><i class="fa-solid fa-file-pdf" style="font-size: 24px; margin-bottom: 8px;"></i><div style="font-size: 11px; font-weight: 600; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 90px;">' + file.name + '</div></div>';
                    wrapper.appendChild(removeBtn);
                }
                
                previewContainer.appendChild(wrapper);
            });
        }
    });
</script>
@endpush
@endsection
