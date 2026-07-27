<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page_title', 'DomusHub – Lễ tân')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Inter',system-ui,sans-serif;background:#F4F7FE;color:#1B2559;min-height:100vh;display:flex;}

        /* Sidebar */
        .sidebar{width:240px;background:white;border-right:1px solid #E9EDF7;display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:50;padding:28px 0;transition:transform .3s;}
        .sidebar-brand{padding:0 24px;margin-bottom:32px;}
        .sidebar-brand h2{font-size:18px;font-weight:800;color:#7B3FE4;letter-spacing:-.3px;}
        .sidebar-brand p{font-size:11px;color:#A3AED0;font-weight:500;margin-top:2px;}
        .sidebar-nav{flex:1;display:flex;flex-direction:column;gap:2px;padding:0 12px;}
        .sidebar-nav a{display:flex;align-items:center;gap:10px;padding:11px 16px;border-radius:10px;font-size:13.5px;font-weight:500;color:#707EAE;text-decoration:none;transition:.2s;}
        .sidebar-nav a i{width:16px;text-align:center;}
        .sidebar-nav a:hover{background:#F5F0FF;color:#7B3FE4;}
        .sidebar-nav a.active{background:#F5F0FF;color:#7B3FE4;font-weight:700;}
        .sidebar-footer{padding:16px 24px;border-top:1px solid #E9EDF7;margin-top:auto;}
        .sidebar-footer button{display:flex;align-items:center;gap:8px;font-size:13px;color:#707EAE;font-weight:500;background:none;border:none;cursor:pointer;font-family:inherit;padding:0;}
        .sidebar-footer button:hover{color:#dc2626;}
        .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.3);z-index:45;}

        /* Main */
        .main{margin-left:240px;flex:1;min-height:100vh;display:flex;flex-direction:column;}
        .topbar{background:white;padding:16px 32px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #E9EDF7;position:sticky;top:0;z-index:40;}
        .topbar-left{display:flex;align-items:center;gap:16px;}
        .hamburger{display:none;width:36px;height:36px;border-radius:10px;border:1px solid #E9EDF7;align-items:center;justify-content:center;color:#707EAE;font-size:16px;cursor:pointer;background:white;}
        .topbar-search{display:flex;align-items:center;gap:10px;background:#F4F7FE;border-radius:10px;padding:10px 16px;width:320px;}
        .topbar-search i{color:#A3AED0;font-size:14px;}
        .topbar-search input{border:none;background:none;outline:none;font-size:13px;color:#1B2559;width:100%;font-family:inherit;}
        .topbar-search input::placeholder{color:#A3AED0;}
        .topbar-right{display:flex;align-items:center;gap:20px;}
        .topbar-user{display:flex;align-items:center;gap:10px;}
        .topbar-user-info{text-align:right;}
        .topbar-user-info .name{font-size:13px;font-weight:700;color:#1B2559;}
        .topbar-user-info .role{font-size:11px;color:#A3AED0;font-weight:500;}
        .topbar-avatar{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#7B3FE4,#A67BF8);display:flex;align-items:center;justify-content:center;color:white;font-size:14px;font-weight:700;text-decoration:none;}
        .content{padding:28px 32px;flex:1;}

        /* Alert */
        .alert{padding:12px 18px;border-radius:10px;font-size:13px;font-weight:500;margin-bottom:20px;display:flex;align-items:center;gap:10px;}
        .alert-success{background:#E6F9F0;color:#05a56a;border:1px solid #b3f0d8;}
        .alert-error{background:#FFF0F0;color:#dc2626;border:1px solid #fecaca;}
        .alert-info{background:#EEF2FF;color:#3652D9;border:1px solid #c7d2fe;}

        @media(max-width:768px){
            .sidebar{transform:translateX(-100%);}
            .sidebar.open{transform:translateX(0);}
            .sidebar-overlay.open{display:block;}
            .hamburger{display:flex;}
            .main{margin-left:0;}
            .content{padding:20px 16px;}
            .topbar-search{width:160px;}
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
    <aside class="sidebar" id="sidebar" role="navigation" aria-label="Menu lễ tân">
        <div class="sidebar-brand">
            <a href="{{ route('receptionist.dashboard') }}" style="text-decoration:none;color:inherit;">
                <h2><i class="fa-solid fa-concierge-bell" style="font-size:15px;margin-right:6px;"></i> DomusHub</h2>
                <p>Lễ tân</p>
            </a>
        </div>
        <nav class="sidebar-nav" role="menubar">
            <a href="{{ route('receptionist.dashboard') }}" role="menuitem"
               class="{{ request()->routeIs('receptionist.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge"></i> Bảng điều khiển
            </a>
            <a href="{{ route('receptionist.parcels.index') }}" role="menuitem"
               class="{{ request()->routeIs('receptionist.parcels*') ? 'active' : '' }}">
                <i class="fa-solid fa-box"></i> Bưu phẩm
            </a>
            <a href="{{ route('receptionist.tickets.index') }}" role="menuitem"
               class="{{ request()->routeIs('receptionist.tickets*') ? 'active' : '' }}">
                <i class="fa-solid fa-triangle-exclamation"></i> Phản ánh
            </a>
            <a href="{{ route('receptionist.amenities.index') }}" role="menuitem"
               class="{{ request()->routeIs('receptionist.amenities*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check"></i> Đặt lịch tiện ích
            </a>
        </nav>
        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</button>
            </form>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-left">
                <button class="hamburger" onclick="openSidebar()" aria-label="Mở menu">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h12M3 9h12M3 12h12"/></svg>
                </button>
                @yield('topbar_left')
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="topbar-user-info">
                        <div class="name">{{ auth()->user()->name }}</div>
                        <div class="role">LỄ TÂN</div>
                    </div>
                    <div class="topbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                </div>
            </div>
        </header>
        <main class="content" role="main">
            @if(session('success'))
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    <script>
    function openSidebar(){document.getElementById('sidebar').classList.add('open');document.getElementById('sidebarOverlay').classList.add('open');document.body.style.overflow='hidden';}
    function closeSidebar(){document.getElementById('sidebar').classList.remove('open');document.getElementById('sidebarOverlay').classList.remove('open');document.body.style.overflow='';}
    </script>
    @stack('scripts')
</body>
</html>
