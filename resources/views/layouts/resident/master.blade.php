<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DomusHub Resident')</title>
    @vite(['resources/css/app.css', 'resources/css/layouts/resident.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="resident-page">
    @include('layouts.resident.partials.header')

    <div class="resident-layout">
        <main class="resident-content">
            @yield('content')
        </main>

        @include('layouts.resident.partials.footer')
    </div>

    @stack('scripts')
</body>

</html>
