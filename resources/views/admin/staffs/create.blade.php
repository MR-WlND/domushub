@extends('layouts.admin.master')

@section('page_title', 'Thêm Nhân sự mới')
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
        <h1 style="font-size:22px; font-weight:700; color:#0b1c30; margin-bottom:24px;">Thêm nhân sự mới</h1>

        @if($errors->any())
            <div style="background:#fee2e2; color:#b91c1c; padding:12px 20px; border-radius:8px; margin-bottom:24px; font-size:13px; font-weight:600;">
                <ul style="margin:0; padding-left:16px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.staffs.store') }}">
            @csrf
            
            <div class="tabs-nav">
                <button type="button" class="tab-btn active" data-target="tab1"><i class="fa-regular fa-address-card"></i> Thông tin cá nhân</button>
                <button type="button" class="tab-btn" data-target="tab2"><i class="fa-solid fa-briefcase"></i> Công việc</button>
                <button type="button" class="tab-btn" data-target="tab3"><i class="fa-solid fa-shield-halved"></i> Tài khoản</button>
            </div>

            <!-- Tab 1: Thông tin cá nhân -->
            <div id="tab1" class="tab-content active">
                <p class="section-desc">Nhập các thông tin cơ bản định danh của nhân sự.</p>
                
                <div class="form-group">
                    <label class="form-label">Họ và tên <span class="req">*</span></label>
                    <input type="text" name="full_name" class="form-input" value="{{ old('full_name') }}" required placeholder="VD: Nguyễn Văn A">
                </div>
                
                <div class="two-cols">
                    <div class="form-group">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-input" value="{{ old('phone') }}" placeholder="VD: 0987654321">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CCCD / CMND</label>
                        <input type="text" name="cccd" class="form-input" value="{{ old('cccd') }}">
                    </div>
                </div>
                
                <div class="two-cols">
                    <div class="form-group">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="dob" class="form-input" value="{{ old('dob') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" class="form-input" value="{{ old('address') }}" placeholder="Số nhà, đường, phường/xã...">
                    </div>
                </div>
            </div>

            <!-- Tab 2: Công việc -->
            <div id="tab2" class="tab-content">
                <p class="section-desc">Phân bổ bộ phận làm việc và thiết lập trạng thái cho nhân sự.</p>

                <div class="two-cols">
                    <div class="form-group">
                        <label class="form-label">Phòng ban</label>
                        <select name="department_id" class="form-input">
                            <option value="">-- Chọn phòng ban --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" @selected(old('department_id') == $dept->id)>{{ $dept->code ? $dept->code . ' - ' : '' }}{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Trạng thái làm việc <span class="req">*</span></label>
                        <select name="status" class="form-input" required>
                            <option value="active" @selected(old('status', 'active') === 'active')>Đang làm việc</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>Đã nghỉ việc</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Tài khoản hệ thống -->
            <div id="tab3" class="tab-content">
                <p class="section-desc">Thiết lập tài khoản đăng nhập vào hệ thống phần mềm DomusHub cho nhân viên.</p>

                <div class="form-group" style="background:#f8f9fa; padding:16px; border-radius:8px; border:1px solid #e2e8f0;">
                    <label style="display:flex; align-items:center; gap:12px; font-weight:600; cursor:pointer; color:#0b1c30; font-size:14px;">
                        <input type="checkbox" name="create_account" id="chkCreateAccount" value="1" @checked(old('create_account')) style="width:20px; height:20px; cursor:pointer;">
                        Cấp tài khoản hệ thống (Để đăng nhập)
                    </label>
                    <p style="font-size:13px; color:#64748b; margin-top:8px; margin-left:32px;">Nếu chọn, tài khoản sẽ được tạo tự động với mật khẩu mặc định là <strong>Chungcu@2026</strong>.</p>
                </div>

                <div id="accountFields" style="display:none;">
                    <div class="two-cols">
                        <div class="form-group">
                            <label class="form-label">Email đăng nhập <span class="req">*</span></label>
                            <input type="email" name="email" id="accEmail" class="form-input" value="{{ old('email') }}" placeholder="ví dụ: nhanvien@domushub.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Vai trò hệ thống <span class="req">*</span></label>
                            <select name="role" id="accRole" class="form-input">
                                <option value="">-- Chọn vai trò --</option>
                                @foreach($roles as $val => $lbl)
                                    <option value="{{ $val }}" @selected(old('role') === $val)>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:12px; margin-top:32px; border-top:1px solid #e2e8f0; padding-top:24px; justify-content:flex-end;">
                <a href="{{ route('admin.staffs.index') }}" style="display:inline-flex; align-items:center; padding:12px 24px; border-radius:8px; background:#f1f5f9; color:#475569; font-weight:600; text-decoration:none;">Hủy</a>
                <button type="submit" class="btn-new-broadcast" style="width:auto; margin:0; padding:12px 32px;"><i class="fa-solid fa-plus"></i> Hoàn tất thêm mới</button>
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

        // Toggle Account Fields
        const chkCreate = document.getElementById('chkCreateAccount');
        const accFields = document.getElementById('accountFields');
        
        function toggleAccountFields() {
            if(chkCreate.checked) {
                accFields.style.display = 'block';
            } else {
                accFields.style.display = 'none';
            }
        }
        
        chkCreate.addEventListener('change', toggleAccountFields);
        toggleAccountFields(); // initial run
    });
</script>
@endpush
