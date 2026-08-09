@extends('layouts.admin.master')

@section('page_title', 'Chi tiết / Chỉnh sửa Đăng ký Tạm trú / Tạm vắng')

@push('styles')
<style>
    .tr-form-page {
        font-family: 'Inter', 'Segoe UI', sans-serif;
    }
    
    .tr-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
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

    .btn-back {
        font-size: 14px;
        font-weight: 600;
        color: #0b57d0;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .btn-back:hover {
        text-decoration: underline;
    }

    .tr-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 32px;
        margin-bottom: 24px;
    }
    
    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 24px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-pending { background-color: #fef3c7; color: #d97706; }
    .badge-approved { background-color: #dcfce7; color: #16a34a; }
    .badge-rejected { background-color: #fee2e2; color: #dc2626; }

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

    .file-current {
        margin-top: 8px;
        margin-bottom: 12px;
        padding: 10px 14px;
        background: #f1f5f9;
        border-radius: 6px;
        display: inline-block;
    }

    .file-current a {
        font-size: 13px;
        font-weight: 600;
        color: #0b57d0;
        text-decoration: none;
    }
    
    .file-current a:hover {
        text-decoration: underline;
    }

    .rejection-box {
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        padding: 16px;
        border-radius: 0 8px 8px 0;
        margin-top: 8px;
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
        border: none;
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
        color: #ffffff;
    }

    .btn-primary:hover {
        background-color: #0b57d0;
        box-shadow: 0 4px 6px rgba(0, 35, 111, 0.2);
    }
    
    .btn-success {
        background-color: #16a34a;
        color: #ffffff;
    }
    
    .btn-success:hover {
        background-color: #15803d;
        box-shadow: 0 4px 6px rgba(22, 163, 74, 0.2);
    }

    .btn-danger {
        background-color: #dc2626;
        color: #ffffff;
    }
    
    .btn-danger:hover {
        background-color: #b91c1c;
        box-shadow: 0 4px 6px rgba(220, 38, 38, 0.2);
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
        <div>
            <h1 class="tr-title">Chi tiết Đăng ký</h1>
            <p class="tr-subtitle">Xem và xử lý đăng ký tạm trú / tạm vắng.</p>
        </div>
        <a href="{{ route('admin.temporary-registrations.index') }}" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Quay lại danh sách
        </a>
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
        <h3 class="card-title">
            Thông tin đăng ký
            @if($temporaryRegistration->status == 'pending')
                <span class="status-badge badge-pending">Chờ duyệt</span>
            @elseif($temporaryRegistration->status == 'approved')
                <span class="status-badge badge-approved">Đã duyệt</span>
            @else
                <span class="status-badge badge-rejected">Từ chối</span>
            @endif
        </h3>
        
        <form action="{{ route('admin.temporary-registrations.update', $temporaryRegistration->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
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
                        <input type="radio" name="type" value="residence" id="type_residence" required {{ old('type', $temporaryRegistration->type) == 'residence' ? 'checked' : '' }} class="radio-input">
                        Tạm trú (Dành cho người mới chuyển đến)
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="type" value="absence" id="type_absence" required {{ old('type', $temporaryRegistration->type) == 'absence' ? 'checked' : '' }} class="radio-input">
                        Tạm vắng (Dành cho cư dân hiện tại)
                    </label>
                </div>
            </div>

            <!-- Trong chế độ Edit, người dùng đã được tạo ở bước Store, nên ta hiển thị danh sách người dùng bình thường -->
            <div class="resident-guest-section">
                <h3 class="section-title">2. Thông tin cư dân</h3>
                <div class="form-group">
                    <label class="form-label">Cư dân <span class="required">*</span></label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">-- Chọn cư dân --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ (old('user_id', $temporaryRegistration->user_id) == $user->id) ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h3 class="section-title">3. Chi tiết lưu trú</h3>
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
                <textarea name="reason" rows="3" class="form-control">{{ old('reason', $temporaryRegistration->reason) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Giấy tờ đính kèm</label>
                @if($temporaryRegistration->attachment_path)
                    <div class="file-current">
                        <a href="{{ Storage::url($temporaryRegistration->attachment_path) }}" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align: text-bottom; margin-right: 4px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            Xem giấy tờ đính kèm hiện tại
                        </a>
                    </div>
                @endif
                <div class="file-upload-wrapper">
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="file-input">
                </div>
                <div class="form-hint">Tải lên file mới sẽ ghi đè file cũ (nếu có). Định dạng: JPG, PNG, PDF.</div>
            </div>

            @if($temporaryRegistration->status == 'rejected' && $temporaryRegistration->rejection_reason)
                <div class="form-group">
                    <div class="rejection-box">
                        <p style="margin: 0; font-size: 14px; color: #b91c1c;">
                            <strong>Lý do từ chối:</strong> {{ $temporaryRegistration->rejection_reason }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
            </div>
        </form>
    </div>

    {{-- Phần duyệt đơn (chỉ hiển thị nếu trạng thái là pending) --}}
    @if($temporaryRegistration->status == 'pending')
    <div class="tr-card">
        <h3 class="card-title">Xử lý Đơn đăng ký</h3>
        <p style="font-size: 14px; color: #64748b; margin-top: -16px; margin-bottom: 20px;">
            Ban quản lý xác nhận hoặc từ chối đơn này dựa trên thông tin cư dân đã khai báo.
        </p>
        
        <div style="display: flex; gap: 12px;">
            <form action="{{ route('admin.temporary-registrations.approve', $temporaryRegistration->id) }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" onclick="return confirm('Xác nhận duyệt đăng ký này?');" class="btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Duyệt Đơn
                </button>
            </form>

            <button type="button" onclick="promptReject({{ $temporaryRegistration->id }})" class="btn btn-danger">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="margin-right: 6px;">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
                Từ chối Đơn
            </button>
        </div>
    </div>
    @endif
</div>

<!-- Modal Từ chối ẩn (sử dụng javascript để submit form thay vì modal html) -->
@if($temporaryRegistration->status == 'pending')
<form id="globalRejectForm" method="POST" action="" style="display: none;">
    @csrf
    <input type="hidden" name="rejection_reason" id="globalRejectReason">
</form>

<script>
    function promptReject(id) {
        let reason = prompt("Nhập lý do từ chối:");
        if (reason !== null && reason.trim() !== "") {
            let form = document.getElementById('globalRejectForm');
            form.action = `/admin/temporary-registrations/${id}/reject`;
            document.getElementById('globalRejectReason').value = reason.trim();
            form.submit();
        } else if (reason !== null) {
            alert("Lý do từ chối không được để trống!");
        }
    }
</script>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const blockSelect = document.getElementById('block_id');
        const floorSelect = document.getElementById('floor_id');
        const apartmentSelect = document.getElementById('apartment_id');
        
        // Data for pre-selection
        const currentApartmentId = "{{ old('apartment_id', $temporaryRegistration->apartment_id) }}";
        const currentBlockId = "{{ $temporaryRegistration->apartment ? ($temporaryRegistration->apartment->floor ? $temporaryRegistration->apartment->floor->block_id : '') : '' }}";
        const currentFloorId = "{{ $temporaryRegistration->apartment ? $temporaryRegistration->apartment->floor_id : '' }}";

        // Initialization logic for edit view
        if (currentBlockId) {
            blockSelect.value = currentBlockId;
            // trigger change manually to populate floors
            const event = new Event('change');
            blockSelect.dispatchEvent(event);
            
            setTimeout(() => {
                if (currentFloorId) {
                    floorSelect.value = currentFloorId;
                    floorSelect.dispatchEvent(new Event('change'));
                }
                
                setTimeout(() => {
                    if (currentApartmentId) {
                        apartmentSelect.value = currentApartmentId;
                    }
                }, 50);
            }, 50);
        }
        
        // Cascading Dropdowns (same as create view)
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
            
            if (this.value && selectedOption.hasAttribute('data-apartments')) {
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
    });
</script>
@endpush
@endsection
