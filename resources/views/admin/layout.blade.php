<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') – DomusHub</title>
    @vite(['resources/css/admin/layout.css'])
    @stack('page_css')
</head>
<body>

{{-- ══ SIDEBAR ══════════════════════════════════════════ --}}
<aside class="sidebar">

    <a href="#" class="sidebar-logo">
        <div class="sidebar-logo-icon">🏢</div>
        <div>
            <div class="sidebar-logo-text">DomusHub</div>
            <div class="sidebar-logo-badge">Admin Portal</div>
        </div>
    </a>

    <nav class="sidebar-nav">

        <div class="sidebar-section">Tổng quan</div>

        <a href="#" class="nav-item {{ request()->is('admin') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/>
                <rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            </svg>
            Dashboard
        </a>

        <div class="sidebar-section">Quản lý</div>

        <a href="#" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Tòa nhà & Căn hộ
        </a>

        <a href="#" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
            </svg>
            Cư dân
        </a>

        <a href="{{ route('admin.invoices.index') }}"
           class="nav-item {{ request()->is('admin/invoices*') && !request()->is('admin/invoices/stats') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
            </svg>
            Hóa đơn
        </a>

        <div class="sidebar-section">Thống kê</div>

        <a href="{{ route('admin.invoices.stats') }}"
           class="nav-item {{ request()->is('admin/invoices/stats') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Thống kê hóa đơn
        </a>

        <a href="#" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            Báo cáo tháng
        </a>

        <div class="sidebar-section">Hệ thống</div>

        <a href="#" class="nav-item">
            <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93A10 10 0 1 0 4.93 19.07"/>
                <path d="M12 2v2M12 20v2M2 12h2M20 12h2"/>
            </svg>
            Cài đặt
        </a>

    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="user-avatar">A</div>
            <div style="flex:1;min-width:0">
                <div class="user-info-name">Admin</div>
                <div class="user-info-role">Quản trị viên</div>
            </div>
            <svg width="14" height="14" fill="none" stroke="rgba(255,255,255,.4)" stroke-width="2" viewBox="0 0 24 24">
                <path d="M9 18l6-6-6-6"/>
            </svg>
        </div>
    </div>

</aside>

{{-- ══ MAIN ═══════════════════════════════════════════════ --}}
<div class="admin-main">

    {{-- Top header --}}
    <header class="admin-header">
        <nav class="header-breadcrumb">
            <a href="#">DomusHub</a>
            <span class="sep">›</span>
            @yield('breadcrumb')
        </nav>
        <div class="header-spacer"></div>
        <div class="header-actions">
            <button class="icon-btn" title="Thông báo">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                <span class="notif-dot"></span>
            </button>
            <button class="icon-btn" title="Tìm kiếm">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
                </svg>
            </button>
        </div>
    </header>

    <div class="admin-content">
        @yield('content')
    </div>

</div>

@stack('scripts')
</body>
</html>
