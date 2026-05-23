<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'DomusHub')</title>
    @vite(['resources/css/app.css', 'resources/css/layouts/admin.css'])
    @stack('styles')
</head>

<body class="dashboard-page">
    <div class="dashboard-shell">
        <aside class="dashboard-sidebar">
            <div>
                <p class="dashboard-brand__label">DomusHub Admin</p>
                <h2 class="dashboard-brand__title">@yield('role_title', 'Trang quản lý')</h2>
            </div>

            <nav class="dashboard-nav">
                <a href="@yield('home_route', '#')" class="dashboard-nav__item dashboard-nav__item--active">
                    Tổng quan
                </a>
                <a href="#" class="dashboard-nav__item">
                    Hồ sơ
                </a>
                <a href="#" class="dashboard-nav__item">
                    Cài đặt
                </a>
            </nav>

            <div class="dashboard-sidebar__footer">
                <p class="dashboard-sidebar__caption">Đăng nhập bằng</p>
                <p class="dashboard-sidebar__value">@yield('user_role', 'Người dùng')</p>
            </div>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-topbar">
                <div>
                    <p class="dashboard-eyebrow">@yield('page_kicker', 'Dashboard')</p>
                    <h1 class="dashboard-page__title">@yield('page_title', 'DomusHub')</h1>
                </div>

                <div class="dashboard-topbar__actions">
                    <div class="dashboard-user-pill">
                        <strong>@yield('user_name', 'Người dùng')</strong>
                        <span>@yield('user_role', 'Vai trò')</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dashboard-logout-btn">
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </header>

            <section class="dashboard-content">
                @yield('content')
            </section>
        </main>
    </div>

    @stack('scripts')
</body>

</html>
