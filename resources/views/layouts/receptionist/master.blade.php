<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'DomusHub - Lễ tân')</title>
    @vite(['resources/css/app.css', 'resources/css/layouts/admin.css', 'resources/css/layouts/forms.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
    <style>
        /* Filter Bar Premium Styles */
        .filter-card {
            background: #ffffff;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 35, 111, 0.02);
        }

        .filter-form {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            width: 100%;
        }

        .filter-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .filter-input-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
        }

        .filter-input {
            height: 42px;
            padding: 10px 16px 10px 40px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            background-color: #ffffff;
            outline: none;
            transition: all 0.2s ease;
            width: 320px;
            box-sizing: border-box;
        }

        .filter-input:focus {
            border-color: #0b57d0;
            box-shadow: 0 0 0 3px rgba(11, 87, 208, 0.08);
        }

        .filter-select {
            height: 42px;
            padding: 10px 36px 10px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: #1e293b;
            background-color: #ffffff;
            outline: none;
            transition: all 0.2s ease;
            cursor: pointer;
            box-sizing: border-box;
            min-width: 200px;
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'></polyline></svg>");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }

        .filter-select:focus {
            border-color: #0b57d0;
            box-shadow: 0 0 0 3px rgba(11, 87, 208, 0.08);
        }

        .filter-btn-submit {
            height: 42px;
            padding: 0 20px;
            background-color: #00236f;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background-color 0.2s ease;
        }

        .filter-btn-submit:hover {
            background-color: #001850;
        }

        .filter-btn-reset {
            height: 42px;
            padding: 0 16px;
            background-color: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .filter-btn-reset:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
            color: #1e293b;
        }

        /* Empty State Premium Styles */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
            color: #64748b;
            text-align: center;
            font-size: 14px;
            font-weight: 500;
            gap: 12px;
            width: 100%;
            box-sizing: border-box;
        }

        .empty-state i {
            font-size: 44px;
            color: #94a3b8;
            opacity: 0.6;
            display: block;
            margin-bottom: 4px;
        }

        @media (max-width: 640px) {
            .filter-input {
                width: 100%;
            }
            .filter-select {
                min-width: 100%;
            }
            .filter-btn-submit,
            .filter-btn-reset {
                width: 100%;
            }
        }
    </style>
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

