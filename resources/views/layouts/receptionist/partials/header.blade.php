<header class="dashboard-topbar">
    <button class="sidebar-toggle" id="sidebarToggle" onclick="openSidebar()" aria-label="Open menu">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>

    @hasSection('topbar_left')
        @yield('topbar_left')
    @else
        <div class="dashboard-topbar__search">
            <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" class="search-input" placeholder="Tìm kiếm...">
        </div>
    @endif

    <div class="dashboard-topbar__actions">
        <a href="{{ route('receptionist.profile') }}" class="dashboard-user-pill" style="text-decoration: none; color: inherit;">
            <div class="user-info">
                <strong class="user-name">{{ auth()->user()->name ?? 'Lễ tân' }}</strong>
                <span class="user-role">LỄ TÂN</span>
            </div>
            <div class="user-avatar-container">
                @if(auth()->check() && auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="user-avatar">
                @else
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Avatar" class="user-avatar">
                @endif
            </div>
        </a>
    </div>
</header>
