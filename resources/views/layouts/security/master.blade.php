<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'DomusHub') — Security</title>
    @vite(['resources/css/app.css', 'resources/css/layouts/security.css'])
    @stack('styles')
</head>

<body class="dashboard-page">
    {{-- Mobile sidebar overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="dashboard-shell">
        @include('layouts.security.partials.sidebar')

        <main class="dashboard-main">
            @include('layouts.security.partials.header')

            <section class="dashboard-content">
                @yield('content')
            </section>
        </main>
    </div>

    @stack('scripts')
    <script>
        function openSidebar() {
            document.getElementById('securitySidebar').classList.add('sidebar--open');
            document.getElementById('sidebarOverlay').classList.add('sidebar-overlay--visible');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            document.getElementById('securitySidebar').classList.remove('sidebar--open');
            document.getElementById('sidebarOverlay').classList.remove('sidebar-overlay--visible');
            document.body.style.overflow = '';
        }
    </script>
</body>

</html>
