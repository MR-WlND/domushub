<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'DomusHub - Lễ tân')</title>
    @vite(['resources/css/app.css', 'resources/css/layouts/admin.css', 'resources/css/layouts/forms.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
</head>

<body class="dashboard-page">
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="dashboard-shell">
        @include('layouts.receptionist.partials.sidebar')

        <main class="dashboard-main">
            @include('layouts.receptionist.partials.header')

            <section class="dashboard-content">
                @if(session('success'))
                    <div style="background: #ecfdf5; color: #065f46; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div style="background: #fef2f2; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 500;">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </section>
        </main>
    </div>

    @stack('scripts')
    <script>
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

