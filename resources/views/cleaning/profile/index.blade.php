@extends('layouts.cleaning.master')

@section('page_title', 'Thông tin cá nhân – DomusHub')

@push('styles')
<style>
    .profile-page{max-width:720px;}
    .profile-header{display:flex;align-items:center;gap:20px;margin-bottom:28px;}
    .profile-avatar{width:72px;height:72px;border-radius:50%;background:linear-gradient(135deg,#3652D9,#6B8AFF);display:flex;align-items:center;justify-content:center;color:white;font-size:28px;font-weight:800;flex-shrink:0;}
    .profile-header-info h1{font-size:22px;font-weight:800;color:#1B2559;letter-spacing:-.3px;}
    .profile-header-info p{font-size:13px;color:#A3AED0;margin-top:2px;}
    .profile-card{background:white;border-radius:14px;padding:28px;box-shadow:0 2px 12px rgba(54,82,217,.04);margin-bottom:20px;}
    .profile-card__title{font-size:15px;font-weight:700;color:#1B2559;margin-bottom:18px;display:flex;align-items:center;gap:8px;}
    .profile-card__title i{color:#3652D9;font-size:14px;}
    .profile-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #F4F7FE;}
    .profile-row:last-child{border-bottom:none;}
    .profile-row__label{font-size:13px;color:#A3AED0;font-weight:500;}
    .profile-row__value{font-size:13px;color:#1B2559;font-weight:600;text-align:right;}
    .status-active{display:inline-flex;align-items:center;gap:5px;color:#05CD99;font-weight:700;font-size:12px;}
    .status-active::before{content:"";width:7px;height:7px;border-radius:50%;background:#05CD99;}

    /* FORM */
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
    .form-group{margin-bottom:0;}
    .form-group.full{grid-column:1/-1;}
    .form-label{display:block;font-size:12px;font-weight:600;color:#1B2559;margin-bottom:6px;}
    .form-input{width:100%;height:44px;border:1.5px solid #E9EDF7;border-radius:10px;padding:0 14px;font-size:13px;color:#1B2559;font-family:inherit;background:#FAFCFE;transition:.2s;}
    .form-input:focus{outline:none;border-color:#3652D9;background:white;box-shadow:0 0 0 3px rgba(54,82,217,.08);}
    .form-input:disabled{background:#F4F7FE;color:#A3AED0;cursor:not-allowed;}
    .form-actions{display:flex;gap:10px;margin-top:20px;}
    .btn-save{display:inline-flex;align-items:center;gap:8px;background:#3652D9;color:white;border:none;padding:11px 24px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;transition:.2s;}
    .btn-save:hover{background:#2a43b8;transform:translateY(-1px);box-shadow:0 6px 16px rgba(54,82,217,.3);}
    .btn-cancel{display:inline-flex;align-items:center;gap:8px;background:#F4F7FE;color:#707EAE;border:none;padding:11px 24px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;transition:.2s;text-decoration:none;}
    .btn-cancel:hover{background:#E9EDF7;}

    .alert{padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
    .alert--success{background:#E6F9F0;color:#065F46;border:1px solid #A7F3D0;}
    .alert--error{background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;}

    @media(max-width:640px){.form-grid{grid-template-columns:1fr;}.profile-header{flex-direction:column;text-align:center;}}
</style>
@endpush

@section('topbar_left')
<a href="{{ route('cleaning.dashboard') }}" style="width:36px;height:36px;border-radius:10px;border:1px solid #E9EDF7;display:inline-flex;align-items:center;justify-content:center;color:#707EAE;text-decoration:none;" aria-label="Quay lại">
    <i class="fa-solid fa-arrow-left"></i>
</a>
@endsection

@section('content')
<div class="profile-page">

    <!-- HEADER -->
    <div class="profile-header">
        <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
        <div class="profile-header-info">
            <h1>{{ auth()->user()->name }}</h1>
            <p>Nhân viên vệ sinh • Tham gia {{ auth()->user()->created_at->format('d/m/Y') }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert--success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert--error"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
    @endif

    <!-- THÔNG TIN -->
    <div class="profile-card">
        <div class="profile-card__title"><i class="fa-solid fa-user"></i> Thông tin tài khoản</div>
        <div class="profile-row">
            <span class="profile-row__label">Họ tên</span>
            <span class="profile-row__value">{{ auth()->user()->name }}</span>
        </div>
        <div class="profile-row">
            <span class="profile-row__label">Email</span>
            <span class="profile-row__value">{{ auth()->user()->email }}</span>
        </div>
        <div class="profile-row">
            <span class="profile-row__label">Số điện thoại</span>
            <span class="profile-row__value">{{ auth()->user()->phone ?? '—' }}</span>
        </div>
        <div class="profile-row">
            <span class="profile-row__label">Vai trò</span>
            <span class="profile-row__value">Nhân viên vệ sinh</span>
        </div>
        <div class="profile-row">
            <span class="profile-row__label">Trạng thái</span>
            <span class="profile-row__value"><span class="status-active">Đang hoạt động</span></span>
        </div>
        <div class="profile-row">
            <span class="profile-row__label">Ngày tham gia</span>
            <span class="profile-row__value">{{ auth()->user()->created_at->format('d/m/Y') }}</span>
        </div>
    </div>

    <!-- CẬP NHẬT THÔNG TIN -->
    <div class="profile-card">
        <div class="profile-card__title"><i class="fa-solid fa-pen-to-square"></i> Cập nhật thông tin</div>
        <form method="POST" action="{{ route('cleaning.profile.update') }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Họ tên</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name', auth()->user()->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', auth()->user()->phone) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', auth()->user()->email) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Vai trò</label>
                    <input type="text" class="form-input" value="Nhân viên vệ sinh" disabled>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi</button>
                <a href="{{ route('cleaning.dashboard') }}" class="btn-cancel">Hủy</a>
            </div>
        </form>
    </div>

    <!-- ĐỔI MẬT KHẨU -->
    <div class="profile-card">
        <div class="profile-card__title"><i class="fa-solid fa-lock"></i> Đổi mật khẩu</div>
        <form method="POST" action="{{ route('cleaning.profile.change-password') }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-group full">
                    <label class="form-label">Mật khẩu hiện tại</label>
                    <input type="password" name="current_password" class="form-input" placeholder="Nhập mật khẩu hiện tại" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mật khẩu mới</label>
                    <input type="password" name="password" class="form-input" placeholder="Tối thiểu 6 ký tự" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="Nhập lại mật khẩu mới" required>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-save"><i class="fa-solid fa-key"></i> Đổi mật khẩu</button>
            </div>
        </form>
    </div>

</div>
@endsection
