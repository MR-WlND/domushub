<header class="resident-header">
    <div class="resident-header__brand">
        <a href="{{ route('resident.dashboard') }}" class="resident-header__brand-link">DomusHub</a>
    </div>

    <nav class="resident-header__nav" aria-label="Resident navigation">
        <a href="{{ route('resident.dashboard') }}"
            class="resident-header__link {{ request()->routeIs('resident.dashboard') ? 'resident-header__link--active' : '' }}">
            Home
        </a>
        <a href="{{ route('resident.members.index') }}"
            class="resident-header__link {{ request()->routeIs('resident.members.*') ? 'resident-header__link--active' : '' }}">
            Thành viên
        </a>
        <div class="resident-header__dropdown-container" id="services-menu-container">
            <button class="resident-header__link resident-header__dropdown-trigger {{ (request()->routeIs('resident.vehicles.*') || request()->routeIs('resident.invoices.*')) ? 'resident-header__link--active' : '' }}">
                Dịch vụ <i class="fa-solid fa-chevron-down nav-dropdown-icon"></i>
            </button>
            <div class="resident-header__nav-dropdown">
                <a href="{{ route('resident.vehicles.index') }}" class="nav-dropdown-item {{ request()->routeIs('resident.vehicles.*') ? 'nav-dropdown-item--active' : '' }}">Phương tiện</a>
                <a href="#" class="nav-dropdown-item">Tiện ích</a>
                <a href="{{ route('resident.invoices.index') }}" class="nav-dropdown-item {{ request()->routeIs('resident.invoices.*') ? 'nav-dropdown-item--active' : '' }}">Hóa đơn</a>
            </div>
        </div>

        <a href="#" class="resident-header__link">
            Phản Ánh
        </a>
        <a href="{{ route('resident.contact') }}"
            class="resident-header__link {{ request()->routeIs('resident.contact') ? 'resident-header__link--active' : '' }}">
            Liên hệ
        </a>
    </nav>

    <div class="resident-header__actions">
        <div class="header-icon-wrapper">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
        </div>

        <div class="header-icon-wrapper">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>

        <div class="resident-header__user-menu" id="user-menu-container">
            <div class="resident-header__user-avatar-container">
                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt="Avatar" class="resident-header__user-avatar">
            </div>
            <div class="resident-header__dropdown">
                <div class="dropdown-info">
                    <strong>{{ auth()->user()->name ?? 'Cư dân' }}</strong>
                    <span>Cư dân</span>
                </div>
                <hr class="dropdown-divider">
                <a href="{{ route('resident.profile.index') }}" class="dropdown-item">Hồ sơ</a>
                <a href="#" class="dropdown-item">Cài đặt</a>
                <a href="#"
                    onclick="event.preventDefault(); document.getElementById('resident-logout-form').submit();"
                    class="dropdown-item dropdown-item--logout">Đăng xuất</a>
                <form id="resident-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userMenu = document.getElementById('user-menu-container');
        const servicesMenu = document.getElementById('services-menu-container');

        if (userMenu) {
            userMenu.addEventListener('click', function(e) {
                e.stopPropagation();
                if (servicesMenu) servicesMenu.classList.remove('active');
                this.classList.toggle('active');
            });
        }

        if (servicesMenu) {
            servicesMenu.addEventListener('click', function(e) {
                e.stopPropagation();
                if (userMenu) userMenu.classList.remove('active');
                this.classList.toggle('active');
            });
        }

        document.addEventListener('click', function() {
            if (userMenu) userMenu.classList.remove('active');
            if (servicesMenu) servicesMenu.classList.remove('active');
        });
    });
</script>
