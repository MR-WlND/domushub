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
        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <input type="text" class="search-input" placeholder="Tìm kiếm hoá đơn, căn hộ, cư dân...">
    </div>

    <div class="dashboard-topbar__actions">
        <div class="header-icon-wrapper" id="notif-wrapper" style="position: relative;">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="notification-badge" id="notif-badge" style="display:none; position:absolute; top:2px; right:2px; background:#ef4444; color:#fff; font-size:0.65rem; font-weight:700; min-width:16px; height:16px; border-radius:8px; display:none; align-items:center; justify-content:center; padding:0 3px;"></span>

            {{-- Dropdown --}}
            <div id="notif-dropdown" style="display:none; position:absolute; top:calc(100% + 10px); right:-10px; width:340px; background:#fff; border:1px solid #e2e8f0; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.12); z-index:999;">
                <div style="padding:12px 16px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
                    <span style="font-size:0.9rem; font-weight:700; color:#0f172a;">Thanh toán mới</span>
                    <a href="{{ route('admin.invoices.index') }}" style="font-size:0.75rem; color:#2563eb; text-decoration:none;">Xem tất cả</a>
                </div>
                <div id="notif-list" style="max-height:320px; overflow-y:auto;">
                    <div style="padding:20px; text-align:center; color:#94a3b8; font-size:0.85rem;">Không có thông báo mới</div>
                </div>
            </div>
        </div>

        <div class="header-icon-wrapper">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>

        <div class="header-divider"></div>

        <div class="dashboard-user-pill">
            <div class="user-info">
                <strong class="user-name">@yield('user_name', auth()->user()->name ?? 'Admin User')</strong>
                <span class="user-role">@yield('user_role_label', 'SUPER ADMINISTRATOR')</span>
            </div>
            <div class="user-avatar-container">
                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Avatar" class="user-avatar">
            </div>
        </div>
    </div>
</header>
