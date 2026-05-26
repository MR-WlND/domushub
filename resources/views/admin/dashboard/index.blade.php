@extends('layouts.admin.master')

@section('page_title', 'Admin Dashboard')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')

<div class="dashboard-content">

    {{-- Welcome --}}
    <section class="dashboard-hero">

        <div>
            <p class="dashboard-hero__kicker">
                Admin Portal
            </p>

            <h1 class="dashboard-hero__title">
                Xin chào,
                {{ auth()->user()->name ?? 'Admin' }}
            </h1>

            <p class="dashboard-hero__desc">
                Theo dõi toàn bộ hệ thống quản lý tòa nhà,
                cư dân và căn hộ tại đây.
            </p>
        </div>

        <div class="dashboard-hero__badge">
            Hệ thống hoạt động ổn định
        </div>

    </section>

    {{-- Stats --}}
    <section class="stats-grid">

        <article class="dashboard-card stat-card">
            <p class="stat-label">Tổng Toà nhà</p>
            <h2 class="stat-number">
                {{ $totalBlocks ?? 0 }}
            </h2>
        </article>

        <article class="dashboard-card stat-card">
            <p class="stat-label">Tổng Tầng</p>
            <h2 class="stat-number">
                {{ $totalFloors ?? 0 }}
            </h2>
        </article>

        <article class="dashboard-card stat-card">
            <p class="stat-label">Tổng Phòng</p>
            <h2 class="stat-number">
                {{ $totalApartments ?? 0 }}
            </h2>
        </article>

        <article class="dashboard-card stat-card">
            <p class="stat-label">Cư dân đang ở</p>
            <h2 class="stat-number text-success">
                {{ $occupiedApartments ?? 0 }}
            </h2>
        </article>

    </section>

    {{-- Main Grid --}}
    <div class="dashboard-main-grid">

        {{-- Left --}}
        <div class="dashboard-main-left">

            {{-- Building Overview --}}
            <article class="dashboard-card">

                <div class="card-header">
                    <h3>Tổng quan hệ thống</h3>

                    <span class="dashboard-status-pill dashboard-status-pill--success">
                        Online
                    </span>
                </div>

                <div class="overview-grid">

                    <div class="overview-item">
                        <strong>{{ $activeBlocks ?? 0 }}</strong>
                        <span>Toà hoạt động</span>
                    </div>

                    <div class="overview-item">
                        <strong>{{ $vacantApartments ?? 0 }}</strong>
                        <span>Phòng trống</span>
                    </div>

                    <div class="overview-item">
                        <strong>{{ $maintenanceApartments ?? 0 }}</strong>
                        <span>Bảo trì</span>
                    </div>

                    <div class="overview-item">
                        <strong>99.9%</strong>
                        <span>Uptime</span>
                    </div>

                </div>

            </article>

            {{-- Recent Activity --}}
            <article class="dashboard-card">

                <div class="card-header">
                    <h3>Hoạt động gần đây</h3>
                </div>

                <div class="activity-list">

                    <div class="activity-item">
                        <div class="activity-dot"></div>

                        <div>
                            <strong>Thêm Toà nhà mới</strong>
                            <p>Admin vừa tạo Toà A</p>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-dot activity-dot--green"></div>

                        <div>
                            <strong>Cập nhật căn hộ</strong>
                            <p>Phòng A-903 đã được cập nhật</p>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-dot activity-dot--orange"></div>

                        <div>
                            <strong>Bảo trì hệ thống</strong>
                            <p>Kiểm tra định kỳ máy chủ</p>
                        </div>
                    </div>

                </div>

            </article>

        </div>

        {{-- Right --}}
        <div class="dashboard-main-right">

            {{-- Quick Actions --}}
            <article class="dashboard-card">

                <div class="card-header">
                    <h3>Hành động nhanh</h3>
                </div>

                <div class="quick-actions">

                    <a href="{{ route('admin.blocks.create') }}"
                       class="quick-action-btn">
                        + Tạo Toà nhà
                    </a>

                    <a href="{{ route('admin.floors.create') }}"
                       class="quick-action-btn">
                        + Tạo Tầng
                    </a>

                    <a href="#"
                       class="quick-action-btn">
                        + Thêm Phòng
                    </a>

                </div>

            </article>

            {{-- System Status --}}
            <article class="dashboard-card">

                <div class="card-header">
                    <h3>Trạng thái hệ thống</h3>
                </div>

                <div class="system-status">

                    <div class="system-status-item">
                        <span>Máy chủ</span>

                        <span class="status-online">
                            Online
                        </span>
                    </div>

                    <div class="system-status-item">
                        <span>Database</span>

                        <span class="status-online">
                            Connected
                        </span>
                    </div>

                    <div class="system-status-item">
                        <span>Bảo mật</span>

                        <span class="status-online">
                            Secure
                        </span>
                    </div>

                </div>

            </article>

        </div>

    </div>

</div>

@endsection