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

    {{-- MOBILE BOTTOM NAVIGATION (MATCHING DESIGN) --}}
    <nav class="mobile-bottom-nav" aria-label="Mobile Navigation">
        <a href="{{ portal_route('dashboard') }}" class="mobile-nav-item {{ is_portal_route('dashboard') ? 'active' : '' }}" title="Tổng quan">
            <i class="fa-solid fa-house"></i>
        </a>
        <a href="{{ portal_route('invoices.index') }}" class="mobile-nav-item {{ is_portal_route('invoices.*') ? 'active' : '' }}" title="Hóa đơn">
            <i class="fa-solid fa-receipt"></i>
        </a>
        <a href="{{ portal_route('announcements.index') }}" class="mobile-nav-item {{ is_portal_route('announcements.*') ? 'active' : '' }}" title="Bản tin">
            <i class="fa-regular fa-newspaper"></i>
        </a>
        <a href="{{ portal_route('tickets.index') }}" class="mobile-nav-item {{ is_portal_route('tickets.*') ? 'active' : '' }}" title="Phản ánh">
            <i class="fa-regular fa-calendar-check"></i>
        </a>
        <a href="{{ portal_route('profile.index') }}" class="mobile-nav-chat-btn" title="Tài khoản">
            <i class="fa-solid fa-comments"></i>
            <span class="online-dot"></span>
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
