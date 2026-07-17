@extends('layouts.admin.master')

@section('page_title', 'Thêm nhân sự')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@push('styles')
    @vite(['resources/css/pages/admin/users/create.css'])
@endpush

@php
    $roleLabels = $roleLabels ?? [];
    $statusLabels = $statusLabels ?? [];
@endphp

@section('content')
    <div class="users-page">
        <div class="users-page__header">
            <div>
                <p class="users-page__eyebrow">Cấu hình hệ thống</p>
                <h1>Thêm nhân sự mới</h1>
            </div>
        </div>

        @if ($errors->any())
            <div class="users-alert users-alert--danger">{{ $errors->first() }}</div>
        @endif

        <form class="user-create-form" method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="user-create-form__section">
                <h2>Thông tin bắt buộc</h2>

                <div class="user-create-form__grid">
                    <label class="user-create-form__field">
                        <span>Họ và tên</span>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nguyễn Văn A"
                            required>
                        @error('name')
                            <small>{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="user-create-form__field">
                        <span>Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="nhansu@domushub.vn"
                            required>
                        @error('email')
                            <small>{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="user-create-form__field">
                        <span>Số điện thoại</span>
                        <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="0901234567" required>
                        @error('phone')
                            <small>{{ $message }}</small>
                        @enderror
                    </label>

                    <label class="user-create-form__field">
                        <span>Vai trò chính</span>
                        <select name="role" required>
                            @foreach ($roleLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', 'staff') === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <small>{{ $message }}</small>
                        @enderror
                    </label>
                </div>
            </div>

            <div class="user-create-form__section">
                <h2>Thiết lập tài khoản</h2>

                <div class="user-create-form__grid user-create-form__grid--single">
                    <label class="user-create-form__field">
                        <span>Trạng thái khi khởi tạo</span>
                        <select name="status" required>
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', 'active') === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <small>{{ $message }}</small>
                        @enderror
                    </label>
                </div>

                <div class="user-create-form__note">
                    Mật khẩu mặc định là: <strong>Chungcu@2026</strong>. Nhân sự cần đổi lại khi đăng nhập lần đầu.
                </div>
            </div>

            <div class="user-create-form__actions">
                <a href="{{ route('admin.users.index') }}" class="users-button users-button--secondary">Hủy</a>
                <button type="submit" class="users-button users-button--primary">Tạo tài khoản</button>
            </div>
        </form>
    </div>
@endsection
