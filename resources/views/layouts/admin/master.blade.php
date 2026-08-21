<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page_title', 'DomusHub')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/css/layouts/admin.css', 'resources/css/layouts/forms.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
</head>

<body class="dashboard-page">
    {{-- Mobile sidebar overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="dashboard-shell">
        @include('layouts.admin.partials.sidebar')

        <main class="dashboard-main">
            @include('layouts.admin.partials.header')

            <section class="dashboard-content">
                @yield('content')
            </section>
        </main>
    </div>

    {{-- MOBILE BOTTOM NAVIGATION (NO ICONS) --}}
    <nav class="mobile-bottom-nav" aria-label="Mobile Navigation">
        <a href="{{ portal_route('dashboard') }}" class="mobile-nav-item {{ is_portal_route('dashboard') ? 'active' : '' }}">
            <span>Tổng quan</span>
        </a>
        <a href="{{ portal_route('tickets.index') }}" class="mobile-nav-item {{ is_portal_route('tickets.*') ? 'active' : '' }}">
            <span>Phản ánh</span>
        </a>
        <a href="{{ portal_route('residents.index') }}" class="mobile-nav-item {{ is_portal_route('residents.*') ? 'active' : '' }}">
            <span>Cư dân</span>
        </a>
        <a href="{{ portal_route('invoices.index') }}" class="mobile-nav-item {{ is_portal_route('invoices.*') ? 'active' : '' }}">
            <span>Hóa đơn</span>
        </a>
        <a href="{{ portal_route('profile.index') }}" class="mobile-nav-item {{ is_portal_route('profile.*') ? 'active' : '' }}">
            <span>Tài khoản</span>
        </a>
    </nav>

    @stack('scripts')
    <script>
        // Preserve sidebar scroll position
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarNav = document.querySelector('.dashboard-nav');
            if (sidebarNav) {
                const scrollPos = sessionStorage.getItem('sidebar-scroll');
                if (scrollPos) {
                    sidebarNav.scrollTop = parseInt(scrollPos, 10);
                }
            }
        });

        window.addEventListener('beforeunload', function() {
            const sidebarNav = document.querySelector('.dashboard-nav');
            if (sidebarNav) {
                sessionStorage.setItem('sidebar-scroll', sidebarNav.scrollTop);
            }
        });

        function openSidebar() {
            document.getElementById('dashboardSidebar').classList.add('sidebar--open');
            document.getElementById('sidebarOverlay').classList.add('sidebar-overlay--visible');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            document.getElementById('dashboardSidebar').classList.remove('sidebar--open');
            document.getElementById('sidebarOverlay').classList.remove('sidebar-overlay--visible');
            document.body.style.overflow = '';
        }
    </script>
</body>

</html>
