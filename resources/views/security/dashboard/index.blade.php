@extends('layouts.security.master')

@section('page_title', 'Security Dashboard')
@section('page_kicker', 'Giám sát an ninh')
@section('role_title', 'Bảng điều khiển Bảo vệ')
@section('home_route', route('security.dashboard'))
@section('user_name', auth()->user()->name ?? 'Bảo vệ')
@section('user_role', 'security')

@section('content')
    <div class="dashboard-grid">
        <article class="dashboard-card dashboard-card--primary">
            <p class="dashboard-card__label">Tổng quan</p>
            <h2>Xin chào, {{ auth()->user()->name ?? 'Bảo vệ' }}.</h2>
            <p>Trang làm việc của bảo vệ đã được thiết lập đơn giản để theo dõi nhanh các tình huống và cập nhật tình trạng.
            </p>
        </article>

        <article class="dashboard-card">
            <p class="dashboard-card__label">Theo dõi nhanh</p>
            <div class="dashboard-stat-row">
                <span class="dashboard-status-pill dashboard-status-pill--warning">Cần chú ý</span>
                <strong>3 sự cố</strong>
            </div>
            <p class="dashboard-card__muted">Kiểm tra lịch làm việc và các cảnh báo gần đây.</p>
        </article>

        <article class="dashboard-card">
            <p class="dashboard-card__label">Nhiệm vụ hôm nay</p>
            <ul class="dashboard-list">
                <li>Chốt vòng tuần tra</li>
                <li>Kiểm tra camera</li>
                <li>Báo cáo sự kiện</li>
            </ul>
        </article>
    </div>
@endsection
