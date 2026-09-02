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
    @if(auth()->check() && auth()->user()->role === 'technician')
        @vite(['resources/css/layouts/resident.css'])
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
</head>

<body class="dashboard-page {{ auth()->check() && auth()->user()->role === 'technician' ? 'technician-page' : '' }}">
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
            const mobileSidebar = document.getElementById('mobileSidebar');
            const dashboardSidebar = document.getElementById('dashboardSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (mobileSidebar) mobileSidebar.classList.add('active');
            if (dashboardSidebar) dashboardSidebar.classList.add('sidebar--open');
            if (overlay) overlay.classList.add('sidebar-overlay--visible', 'active');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            const mobileSidebar = document.getElementById('mobileSidebar');
            const dashboardSidebar = document.getElementById('dashboardSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (mobileSidebar) mobileSidebar.classList.remove('active');
            if (dashboardSidebar) dashboardSidebar.classList.remove('sidebar--open');
            if (overlay) overlay.classList.remove('sidebar-overlay--visible', 'active');
            document.body.style.overflow = '';
        }
    </script>
</body>

</html>
