@extends('layouts.admin.master')

@section('page_title', 'Cập nhật Nhân sự')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@push('styles')
<style>
    .tabs-nav { 
        display: flex; 
        border-bottom: 1px solid #e2e8f0; 
        margin-bottom: 24px; 
        gap: 32px; 
    }
    .tab-btn { 
        background: none; 
        border: none; 
        padding: 12px 0; 
        font-size: 15px; 
        font-weight: 600; 
        color: #64748b; 
        cursor: pointer; 
        position: relative; 
        transition: color 0.2s;
    }
    .tab-btn:hover { color: #0b1c30; }
    .tab-btn.active { color: #0b57d0; }
    .tab-btn.active::after { 
        content: ''; 
        position: absolute; 
        bottom: -1px; 
        left: 0; 
        width: 100%; 
        height: 2px; 
        background: #0b57d0; 
    }
    .tab-content { display: none; animation: fadeIn 0.3s; }
    .tab-content.active { display: block; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-group { margin-bottom: 24px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 8px; }
    .form-input { 
        width: 100%; 
        padding: 12px 14px; 
        border: 1px solid #cbd5e1; 
        border-radius: 8px; 
        outline: none; 
        font-family: inherit; 
        background: #f8f9fa; 
        transition: all 0.2s;
    }
    .form-input:focus { 
        background: #fff;
        border-color: #0b57d0; 
        box-shadow: 0 0 0 3px rgba(11,87,208,0.1); 
    }
    .req { color: #ba1a1a; }
    .two-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    
    .section-desc { font-size: 13px; color: #64748b; margin-bottom: 24px; }
</style>
@endpush

@section('content')
<div style="max-width:1000px; margin:0 auto;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.staffs.index') }}" style="font-size:14px;color:#0b57d0;text-decoration:none;display:inline-flex;align-items:center;gap:6px;font-weight:600;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="dashboard-card" style="padding:32px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px;">
            <h1 style="font-size:22px; font-weight:700; color:#0b1c30; margin:0;">Chi tiết nhân sự: {{ $staff->full_name }}</h1>
            
            <form method="POST" action="{{ route('admin.staffs.destroy', $staff->id) }}" onsubmit="return confirm('Xóa hồ sơ nhân sự này vĩnh viễn?');">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:none; border:1px solid #fee2e2; background:#fff0f0; color:#ba1a1a; padding:6px 12px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;"><i class="fa-solid fa-trash-can"></i> Xóa nhân sự</button>
            </form>
        </div>

        @if($errors->any())
            <div style="background:#fee2e2; color:#b91c1c; padding:12px 20px; border-radius:8px; margin-bottom:24px; font-size:13px; font-weight:600;">
                <ul style="margin:0; padding-left:16px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.staffs.update', $staff->id) }}">
            @csrf
            @method('PUT')
            
            <div class="tabs-nav">
                <button type="button" class="tab-btn active" data-target="tab1"><i class="fa-regular fa-address-card"></i> Thông tin cá nhân</button>
                <button type="button" class="tab-btn" data-target="tab2"><i class="fa-solid fa-briefcase"></i> Công việc</button>
                <button type="button" class="tab-btn" data-target="tab3"><i class="fa-solid fa-shield-halved"></i> Tài khoản</button>
            </div>

            <!-- Tab 1: Thông tin cá nhân -->
            <div id="tab1" class="tab-content active">
                <p class="section-desc">Cập nhật các thông tin cơ bản định danh của nhân sự.</p>
                
                <div class="form-group">
                    <label class="form-label">Họ và tên <span class="req">*</span></label>
                    <input type="text" name="full_name" class="form-input" value="{{ old('full_name', $staff->full_name) }}" required>
                </div>
                
                <div class="two-cols">
                    <div class="form-group">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-input" value="{{ old('phone', $staff->phone) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CCCD / CMND</label>
                        <input type="text" name="cccd" class="form-input" value="{{ old('cccd', $staff->cccd) }}">
                    </div>
                </div>
                
                <div class="two-cols">
                    <div class="form-group">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="dob" class="form-input" value="{{ old('dob', optional($staff->dob)->format('Y-m-d')) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" class="form-input" value="{{ old('address', $staff->address) }}">
                    </div>
                </div>
            </div>

            <!-- Tab 2: Công việc -->
            <div id="tab2" class="tab-content">
                <p class="section-desc">Phân bổ bộ phận làm việc và trạng thái cho nhân sự.</p>

                <div class="two-cols">
                    <div class="form-group">
                        <label class="form-label">Phòng ban</label>
                        <select name="department_id" class="form-input">
                            <option value="">-- Chọn phòng ban --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" @selected(old('department_id', $staff->department_id) == $dept->id)>{{ $dept->code ? $dept->code . ' - ' : '' }}{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Trạng thái làm việc <span class="req">*</span></label>
                        <select name="status" class="form-input" required>
                            <option value="active" @selected(old('status', $staff->status) === 'active')>Đang làm việc</option>
                            <option value="inactive" @selected(old('status', $staff->status) === 'inactive')>Đã nghỉ việc</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Tài khoản hệ thống -->
            <div id="tab3" class="tab-content">
                <p class="section-desc">Trạng thái liên kết tài khoản hệ thống DomusHub.</p>

                @if($staff->user)
                    <div style="background:#e6f4ea; padding:20px; border-radius:8px; border:1px solid #bce2c7;">
                        <div style="font-size:14px; color:#137333; font-weight:700; margin-bottom:16px;">
                            <i class="fa-solid fa-circle-check"></i> Đã cấp tài khoản đăng nhập
                        </div>
                        <div style="display:grid; grid-template-columns: 100px 1fr; gap:8px; font-size:14px; margin-bottom:20px;">
                            <div style="color:#64748b;">Email:</div>
                            <div style="color:#0b1c30; font-weight:600;">{{ $staff->user->email }}</div>
                            <div style="color:#64748b;">Vai trò:</div>
                            <div style="color:#0b1c30; font-weight:600;">{{ $roles[$staff->user->role] ?? $staff->user->role }}</div>
                        </div>
                        <a href="{{ route('admin.users.edit', $staff->user->id) }}" style="display:inline-flex; align-items:center; gap:8px; padding:8px 16px; background:#fff; color:#137333; font-weight:600; text-decoration:none; border-radius:6px; border:1px solid #bce2c7; font-size:13px;">
                            <i class="fa-solid fa-pen-to-square"></i> Đổi mật khẩu / Quản lý tài khoản này
                        </a>
                    </div>
                @else
                    <div style="background:#f8f9fa; padding:20px; border-radius:8px; border:1px dashed #cbd5e1; text-align:center;">
                        <div style="font-size:14px; color:#64748b; margin-bottom:12px;">Nhân sự chưa được cấp tài khoản hệ thống.</div>
                        <p style="font-size:13px; color:#94a3b8; max-width:400px; margin:0 auto;">Vui lòng chuyển sang tính năng "Phân quyền người dùng" và tạo tài khoản thủ công, chọn Role và liên kết với nhân sự này nếu cần thiết.</p>
                    </div>
                @endif
            </div>

            <div style="display:flex; gap:12px; margin-top:32px; border-top:1px solid #e2e8f0; padding-top:24px; justify-content:flex-end;">
                <a href="{{ route('admin.staffs.index') }}" style="display:inline-flex; align-items:center; padding:12px 24px; border-radius:8px; background:#f1f5f9; color:#475569; font-weight:600; text-decoration:none;">Hủy</a>
                <button type="submit" class="btn-new-broadcast" style="width:auto; margin:0; padding:12px 32px;"><i class="fa-solid fa-check"></i> Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Tab switching
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.target;
                
                // Remove active classes
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active to clicked tab
                btn.classList.add('active');
                document.getElementById(target).classList.add('active');
            });
        });
    });
</script>
@endpush
