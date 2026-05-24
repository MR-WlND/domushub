<aside class="dashboard-sidebar">
    <div>
        <div class="dashboard-brand">
            <h2 class="dashboard-brand__title">DomusHub</h2>
            <p class="dashboard-brand__label">Security Portal</p>
        </div>

        <nav class="dashboard-nav">
            {{-- Dashboard --}}
            <a href="{{ route('security.dashboard') }}" class="dashboard-nav__item {{ request()->routeIs('security.dashboard') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Tổng quan</span>
            </a>

            {{-- Quản lý bãi xe (có submenu) --}}
            <div class="dashboard-nav__group {{ request()->routeIs('security.vehicle*') ? 'dashboard-nav__group--open' : '' }}" id="group-vehicle">
                <button type="button" class="dashboard-nav__item dashboard-nav__item--parent {{ request()->routeIs('security.vehicle*') ? 'dashboard-nav__item--active' : '' }}" onclick="toggleNavGroup('group-vehicle')">
                    <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    <span>Quản lý bãi xe</span>
                    <svg class="dashboard-nav__chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="dashboard-nav__submenu">
                    <a href="{{ route('security.vehicle-checkin.index') }}" class="dashboard-nav__subitem {{ request()->routeIs('security.vehicle-checkin*') ? 'dashboard-nav__subitem--active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <rect x="7" y="7" width="3" height="3"></rect>
                            <rect x="14" y="7" width="3" height="3"></rect>
                            <rect x="7" y="14" width="3" height="3"></rect>
                            <rect x="14" y="14" width="3" height="3"></rect>
                        </svg>
                        <span>Quét QR Xe Vào</span>
                    </a>
                    <a href="{{ route('security.vehicle-checkout.index') }}" class="dashboard-nav__subitem {{ request()->routeIs('security.vehicle-checkout*') ? 'dashboard-nav__subitem--active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <rect x="7" y="7" width="3" height="3"></rect>
                            <rect x="14" y="7" width="3" height="3"></rect>
                            <rect x="7" y="14" width="3" height="3"></rect>
                            <line x1="14" y1="14" x2="17" y2="17"></line>
                        </svg>
                        <span>Xe Ra</span>
                    </a>
                </div>
            </div>

            {{-- Quản lý khách --}}
            <a href="{{ route('security.visitor-check.index') }}" class="dashboard-nav__item {{ request()->routeIs('security.visitor*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Quản lý khách</span>
            </a>
        </nav>
    </div>

    <div class="dashboard-sidebar__bottom">
        <div class="dashboard-sidebar__footer">
            <a href="#" class="dashboard-footer__item">
                <svg class="dashboard-footer__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>Hỗ trợ</span>
            </a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('security-logout-form').submit();" class="dashboard-footer__item">
                <svg class="dashboard-footer__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Đăng xuất</span>
            </a>
            <form id="security-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</aside>

<script>
function toggleNavGroup(groupId) {
    const group = document.getElementById(groupId);
    if (group) {
        group.classList.toggle('dashboard-nav__group--open');
    }
}
</script>
