@extends('layouts.admin.master')

@section('page_title', 'Sửa quyền nhân sự')
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
                <h1>Sửa quyền nhân sự</h1>
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
                        <h2>Phân quyền & Trạng thái</h2>
                        
                        <div style="background:#f8f9fa; padding:16px; border-radius:8px; margin-bottom:20px; font-size:14px; color:#475569;">
                            <p style="margin:0 0 8px 0;"><strong>Nhân sự:</strong> {{ $user->name }}</p>
                            <p style="margin:0 0 8px 0;"><strong>Email:</strong> {{ $user->email }}</p>
                            <p style="margin:0;"><strong>Điện thoại:</strong> {{ $user->phone }}</p>
                            <p style="margin-top:12px; font-size:13px; color:#0b57d0;"><i class="fa-solid fa-circle-info"></i> Để sửa đổi các thông tin trên, vui lòng sang trang <strong>Quản lý nhân sự</strong>.</p>
                        </div>

                        <div class="user-create-form__grid">
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
                        </div>
                    </section>
                </div>
                <div class="user-edit-form__actions">
                    <a href="{{ portal_route('users.index') }}" class="users-button users-button--secondary">Hủy bản
                        thảo</a>
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
