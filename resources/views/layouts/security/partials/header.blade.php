<header class="dashboard-topbar">
    {{-- Hamburger (mobile only) --}}
    <button class="sidebar-toggle" id="sidebarToggle" onclick="openSidebar()" aria-label="Open menu">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>

    <div class="dashboard-topbar__search">
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" class="search-input" placeholder="Tìm kiếm xe, khách...">
    </div>

    <div class="dashboard-topbar__actions">

        {{-- Bell notification --}}
        <div class="header-icon-wrapper">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="notification-badge"></span>
        </div>

        <div class="header-divider"></div>

        {{-- User info --}}
        <div class="dashboard-user-pill">
            <div class="user-info">
                <strong class="user-name">{{ auth()->user()?->name ?? 'Bảo Vệ' }}</strong>
                <span class="user-role">BẢO VỆ</span>
            </div>
            <div class="user-avatar-container">
                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt="Avatar" class="user-avatar">
            </div>
        </div>

    </div>
</header>
