@extends('layouts.resident.master')

@section('title', 'Resident Dashboard')

@section('content')
    <section class="resident-dashboard">
        <div class="resident-dashboard__hero">
            <div>
                <p class="resident-dashboard__eyebrow">Cư dân</p>
                <h2 class="resident-dashboard__title">Bảng điều khiển Cư dân</h2>
                <p class="resident-dashboard__intro">
                    Chào mừng bạn trở lại, {{ auth()->user()->name ?? 'Cư dân' }}. Đây là trang tổng quan đơn giản dành cho
                    cư dân.
                </p>
            </div>
            <span class="resident-dashboard__badge">Hoạt động</span>
        </div>

        <div class="dashboard-grid">
            <article class="dashboard-card dashboard-card--primary">
                <p class="dashboard-card__label">Tổng quan</p>
                <h2>Resident dashboard</h2>
                <p>Trang cư dân đã được chuyển sang layout resident với header và footer riêng.</p>
            </article>

            <article class="dashboard-card">
                <p class="dashboard-card__label">Thông tin nhanh</p>
                <div class="dashboard-stat-row">
                    <span class="dashboard-status-pill dashboard-status-pill--success">Hoạt động</span>
                    <strong>Đang cập nhật</strong>
                </div>
                <p class="dashboard-card__muted">Các thông báo, lịch sự kiện và yêu cầu hỗ trợ sẽ hiển thị tại đây.</p>
            </article>

            <article class="dashboard-card">
                <p class="dashboard-card__label">Các mục gần đây</p>
                <ul class="dashboard-list">
                    <li>Thông báo chung trong khu</li>
                    <li>Quản lý yêu cầu hỗ trợ</li>
                    <li>Xem lịch sự kiện cộng đồng</li>
                </ul>
            </article>
        </div>
    </section>
@endsection
