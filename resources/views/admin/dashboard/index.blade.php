@extends('layouts.admin.master')

@section('page_title', 'Admin Dashboard')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('home'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')
    <div class="dashboard-grid">
        <article class="dashboard-card dashboard-card--primary">
            <p class="dashboard-card__label">Tổng quan</p>
            <h2>Chào mừng quay lại, {{ auth()->user()->name ?? 'Admin' }}.</h2>
            <p>Trang quản trị đang hoạt động. Bạn có thể theo dõi hệ thống và quản lý các tài khoản tại đây.</p>
        </article>

        <article class="dashboard-card">
            <p class="dashboard-card__label">Trạng thái hệ thống</p>
            <div class="dashboard-stat-row">
                <span class="dashboard-status-pill dashboard-status-pill--success">Đang hoạt động</span>
                <strong>99.9%</strong>
            </div>
            <p class="dashboard-card__muted">Hệ thống ổn định và sẵn sàng phục vụ.</p>
        </article>

        <article class="dashboard-card">
            <p class="dashboard-card__label">Hành động nhanh</p>
            <ul class="dashboard-list">
                <li>Kiểm tra người dùng mới</li>
                <li>Theo dõi báo cáo hoạt động</li>
                <li>Đảm bảo bảo mật hệ thống</li>
            </ul>
        </article>
    </div>
@endsection
