@extends('layouts.admin.master')

@section('page_title', 'Sửa hồ sơ & phân quyền nhân sự')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@push('styles')
    @vite(['resources/css/pages/admin/users/edit.css'])
@endpush

@section('content')
    <div class="users-page">
        <div class="users-page__header">
            <div>
                <p class="users-page__eyebrow">Cấu hình hệ thống</p>
                <h1>Sửa hồ sơ & phân quyền nhân sự</h1>
            </div>
        </div>

        @if (session('success'))
            <div class="users-alert users-alert--success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="users-alert users-alert--danger">{{ $errors->first() }}</div>
        @endif

        <form id="resetPasswordForm" method="POST" action="{{ portal_route('users.resetPassword', $user) }}"
            onsubmit="return confirm('Đặt lại mật khẩu tài khoản này về Chungcu@2026?')">
            @csrf
            @method('PUT')
        </form>

        <div class="user-edit-page__layout">
            <form class="user-edit-form" method="POST" action="{{ portal_route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="user-edit-page__main">
                    <section class="user-create-form__section user-edit-section">
                        <h2>Thông tin tài khoản & Phân quyền</h2>
                        
                        <div class="user-create-form__grid" style="margin-bottom:16px;">
                            <label class="user-create-form__field">
                                <span>Họ và tên (*)</span>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required placeholder="Nhập họ và tên">
                                @error('name')
                                    <small>{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="user-create-form__field">
                                <span>Số điện thoại</span>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Nhập số điện thoại">
                                @error('phone')
                                    <small>{{ $message }}</small>
                                @enderror
                            </label>
                        </div>

                        <div class="user-create-form__grid" style="margin-bottom:16px;">
                            <label class="user-create-form__field">
                                <span>Email (Tài khoản đăng nhập)</span>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required placeholder="Nhập email">
                                @error('email')
                                    <small>{{ $message }}</small>
                                @enderror
                            </label>

                            <label class="user-create-form__field user-edit-form__role">
                                <span>Vai trò chính</span>
                                <select name="role" id="roleSelect" required>
                                    @foreach ($roleLabels as $value => $label)
                                        <option value="{{ $value }}" @selected(old('role', $user->role) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <small>{{ $message }}</small>
                                @enderror
                            </label>
                        </div>

                        <div class="user-create-form__grid">
                            <label class="user-create-form__field">
                                <span>Trạng thái tài khoản</span>
                                <select name="status" required>
                                    @foreach ($statusLabels as $value => $label)
                                        <option value="{{ $value }}" @selected(old('status', $user->status) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <small>{{ $message }}</small>
                                @enderror
                            </label>

                            <div class="user-create-form__field">
                                <span>Liên kết Nhân sự</span>
                                <input type="text" value="{{ $user->staff ? ($user->staff->staff_code . ' - ' . $user->staff->full_name) : 'Chưa liên kết hồ sơ nhân sự' }}" readonly disabled style="background:#f8fafc; color:#64748b;">
                            </div>
                        </div>
                    </section>
                </div>
                <div class="user-edit-form__actions">
                    <a href="{{ portal_route('roles.index') }}" class="users-button users-button--secondary">Hủy bản thảo</a>
                    <button type="submit" class="users-button users-button--primary">Lưu thay đổi</button>
                </div>
            </form>

            <aside class="user-edit-page__side">
                <section class="user-create-form__section user-edit-card user-edit-card--highlight">
                    <h2>Đặt lại mật khẩu</h2>
                    <p class="user-edit-form__side-text">
                        Đưa mật khẩu về chuỗi mặc định để xử lý nhanh trường hợp nhân sự quên mật khẩu.
                    </p>
                    <button type="submit" form="resetPasswordForm" class="user-edit-form__reset">
                        Đặt lại mật khẩu mặc định
                    </button>
                </section>

                <section class="user-create-form__section user-edit-card">
                    <h2>Thông tin hệ thống</h2>
                    <div class="user-edit-meta user-edit-meta--inline">
                        <span>Ngày tạo</span>
                        <strong>{{ optional($user->created_at)->format('d/m/Y H:i') ?? 'Chưa có' }}</strong>
                    </div>
                    <div class="user-edit-meta user-edit-meta--inline">
                        <span>Cập nhật gần nhất</span>
                        <strong>{{ optional($user->updated_at)->format('d/m/Y H:i') ?? 'Chưa có' }}</strong>
                    </div>
                </section>
            </aside>
        </div>
    </div>
@endsection
