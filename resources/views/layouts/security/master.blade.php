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
</body>

</html>

