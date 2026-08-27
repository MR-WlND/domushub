<header class="resident-header">
    <div class="resident-header__brand-mobile-wrapper">
        <button id="mobileMenuToggle" class="resident-header__mobile-toggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="resident-header__brand">
            <a href="{{ route('resident.dashboard') }}" class="resident-header__brand-link">DomusHub</a>
        </div>
    </div>

    <nav class="resident-header__nav" id="residentNavMenu" aria-label="Resident navigation">
        <a href="{{ route('resident.dashboard') }}"
            class="resident-header__link {{ request()->routeIs('resident.dashboard') ? 'resident-header__link--active' : '' }}">
            Trang chủ
        </a>
        <a href="{{ route('resident.posts.index') }}"
            class="resident-header__link {{ request()->routeIs('resident.posts.index') ? 'resident-header__link--active' : '' }}">
            Bản tin
        </a>
        <a href="{{ route('resident.members.index') }}"
            class="resident-header__link {{ request()->routeIs('resident.members.*') ? 'resident-header__link--active' : '' }}">
            Thành viên
        </a>

        <div class="resident-header__dropdown-container" id="services-menu-container">
            <button class="resident-header__link resident-header__dropdown-trigger {{ (request()->routeIs('resident.vehicles.*') || request()->routeIs('resident.invoices.*') || request()->routeIs('resident.visitors.*') || request()->routeIs('resident.facilities.*')) ? 'resident-header__link--active' : '' }}">
                Dịch vụ <i class="fa-solid fa-chevron-down nav-dropdown-icon"></i>
            </button>
            <div class="resident-header__nav-dropdown">
                <a href="{{ route('resident.temporary-registrations.index') }}" class="nav-dropdown-item {{ request()->routeIs('resident.temporary-registrations.*') ? 'nav-dropdown-item--active' : '' }}">Tạm trú / Tạm vắng</a>
                <a href="{{ route('resident.vehicles.index') }}" class="nav-dropdown-item {{ request()->routeIs('resident.vehicles.*') ? 'nav-dropdown-item--active' : '' }}">Phương tiện</a>
                <a href="{{ route('resident.facilities.index') }}" class="nav-dropdown-item {{ request()->routeIs('resident.facilities.*') || request()->routeIs('resident.facility-bookings.*') ? 'nav-dropdown-item--active' : '' }}">Tiện ích</a>
                <a href="{{ route('resident.visitors.index') }}" class="nav-dropdown-item {{ request()->routeIs('resident.visitors.*') ? 'nav-dropdown-item--active' : '' }}">Khách ghé thăm</a>
                <a href="{{ route('resident.invoices.index') }}" class="nav-dropdown-item {{ request()->routeIs('resident.invoices.*') ? 'nav-dropdown-item--active' : '' }}">Thanh toán hóa đơn</a>
            </div>
        </div>

        <a href="{{ route('resident.tickets.index') }}"
            class="resident-header__link {{ request()->routeIs('resident.tickets.*') ? 'resident-header__link--active' : '' }}">
            Phản Ánh
        </a>
        <a href="{{ route('resident.contact') }}"
            class="resident-header__link {{ request()->routeIs('resident.contact') ? 'resident-header__link--active' : '' }}">
            Liên hệ
        </a>
    </nav>

    <div class="resident-header__actions">
        @csrf {{-- CSRF token for AJAX requests --}}
        {{-- Bell notification --}}
        <div class="header-icon-wrapper" id="residentNotificationBellWrapper" style="position: relative;">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="notification-badge" id="residentNotificationBadge" style="display: none;">0</span>

            {{-- Notifications Dropdown --}}
            <div class="notification-dropdown" id="residentNotificationDropdown">
                <div class="notification-dropdown__header" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-bottom: 1px solid #f1f5f9; background-color: #f8fafc;">
                    <span style="font-weight: 700; color: #0f172a; font-size: 14px;">Thông báo</span>
                    <button type="button" id="residentMarkAllReadBtn" style="border: none; background: transparent; color: #0b57d0; font-size: 12px; font-weight: 600; cursor: pointer; padding: 0;">Đánh dấu tất cả đã đọc</button>
                </div>
                <div id="residentNotificationList" style="max-height: 320px; overflow-y: auto;">
                    <!-- Sẽ được thêm bằng JS -->
                </div>
            </div>
        </div>

        <a href="{{ route('resident.guide') }}" class="header-icon-wrapper" title="Hướng dẫn & FAQs" style="color: inherit; text-decoration: none;">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </a>

        <div class="resident-header__user-menu" id="user-menu-container">
            <div class="resident-header__user-avatar-container">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                        alt="Avatar" class="resident-header__user-avatar" id="header-user-avatar">
                @else
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                        alt="Avatar" class="resident-header__user-avatar" id="header-user-avatar">
                @endif
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
                    class="dropdown-item dropdown-item--logout" style="color: #ef4444;">Đăng xuất</a>
                <form id="resident-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</header>

<!-- Mobile Sidebar Backdrop -->
<div class="mobile-sidebar-backdrop" id="mobileSidebarBackdrop"></div>

<!-- Mobile Sidebar (matches image design) -->
<div class="mobile-sidebar" id="mobileSidebar">
    <div class="mobile-sidebar__header">
        <div class="mobile-sidebar__brand">
            DomusHub
        </div>
        <button id="mobileMenuClose" class="mobile-sidebar__close">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="mobile-sidebar__label">CƯ DÂN</div>

    <div class="mobile-sidebar__menu">
        <a href="{{ route('resident.dashboard') }}" class="mobile-sidebar__link {{ request()->routeIs('resident.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-house nav-icon"></i> Trang chủ
        </a>
        <a href="{{ route('resident.posts.index') }}" class="mobile-sidebar__link {{ request()->routeIs('resident.posts.index') ? 'active' : '' }}">
            <i class="fa-regular fa-newspaper nav-icon"></i> Bản tin
        </a>
        <a href="{{ route('resident.members.index') }}" class="mobile-sidebar__link {{ request()->routeIs('resident.members.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users nav-icon"></i> Thành viên
        </a>
        
        <div class="mobile-sidebar__dropdown-container" id="mobileServicesContainer">
            <button class="mobile-sidebar__link w-100" style="width: 100%; border: none; background: transparent; text-align: left; cursor: pointer;" id="mobileServicesToggle">
                <div style="display: flex; align-items: center; width: 100%;">
                    <i class="fa-solid fa-layer-group nav-icon"></i> Dịch vụ
                    <i class="fa-solid fa-chevron-down ms-auto" style="margin-left: auto; font-size: 0.8rem;"></i>
                </div>
            </button>
            <div class="mobile-sidebar__dropdown-menu" id="mobileServicesMenu" style="display: none; flex-direction: column; padding-left: 24px; gap: 4px; margin-top: 4px;">
                <a href="{{ route('resident.temporary-registrations.index') }}" class="mobile-sidebar__sublink {{ request()->routeIs('resident.temporary-registrations.*') ? 'active' : '' }}">Tạm trú / Tạm vắng</a>
                <a href="{{ route('resident.vehicles.index') }}" class="mobile-sidebar__sublink {{ request()->routeIs('resident.vehicles.*') ? 'active' : '' }}">Phương tiện</a>
                <a href="{{ route('resident.facilities.index') }}" class="mobile-sidebar__sublink {{ request()->routeIs('resident.facilities.*') || request()->routeIs('resident.facility-bookings.*') ? 'active' : '' }}">Tiện ích</a>
                <a href="{{ route('resident.visitors.index') }}" class="mobile-sidebar__sublink {{ request()->routeIs('resident.visitors.*') ? 'active' : '' }}">Khách ghé thăm</a>
                <a href="{{ route('resident.invoices.index') }}" class="mobile-sidebar__sublink {{ request()->routeIs('resident.invoices.*') ? 'active' : '' }}">Thanh toán hóa đơn</a>
            </div>
        </div>

        <a href="{{ route('resident.tickets.index') }}" class="mobile-sidebar__link {{ request()->routeIs('resident.tickets.*') ? 'active' : '' }}">
            <i class="fa-solid fa-file-contract nav-icon"></i> Phản Ánh
        </a>
        <a href="{{ route('resident.contact') }}" class="mobile-sidebar__link {{ request()->routeIs('resident.contact') ? 'active' : '' }}">
            <i class="fa-regular fa-envelope nav-icon"></i> Liên hệ
        </a>
    </div>

    <div class="mobile-sidebar__spacer"></div>

    <div class="mobile-sidebar__footer">
        <a href="#" class="mobile-sidebar__link">
            <i class="fa-solid fa-gear nav-icon"></i> Cài đặt
        </a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('resident-logout-form').submit();" class="mobile-sidebar__link logout">
            <i class="fa-solid fa-arrow-right-from-bracket nav-icon"></i> Đăng xuất
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userMenu = document.getElementById('user-menu-container');
        const servicesMenu = document.getElementById('services-menu-container');

        if (userMenu) {
            userMenu.addEventListener('click', function(e) {
                e.stopPropagation();
                if (servicesMenu) servicesMenu.classList.remove('active');
                if (dropdown) dropdown.style.display = 'none';
                this.classList.toggle('active');
            });
        }

        if (servicesMenu) {
            servicesMenu.addEventListener('click', function(e) {
                e.stopPropagation();
                if (userMenu) userMenu.classList.remove('active');
                if (dropdown) dropdown.style.display = 'none';
                this.classList.toggle('active');
            });
        }

        // Hamburger Menu Toggle
        const mobileToggle = document.getElementById('mobileMenuToggle');
        const mobileSidebar = document.getElementById('mobileSidebar');
        const mobileClose = document.getElementById('mobileMenuClose');
        const mobileBackdrop = document.getElementById('mobileSidebarBackdrop');
        
        function openMobileSidebar() {
            if (mobileSidebar) mobileSidebar.classList.add('active');
            if (mobileBackdrop) mobileBackdrop.classList.add('active');
        }

        function closeMobileSidebar() {
            if (mobileSidebar) mobileSidebar.classList.remove('active');
            if (mobileBackdrop) mobileBackdrop.classList.remove('active');
        }

        if (mobileToggle) {
            mobileToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                openMobileSidebar();
            });
        }
        if (mobileClose) {
            mobileClose.addEventListener('click', function(e) {
                e.stopPropagation();
                closeMobileSidebar();
            });
        }
        if (mobileBackdrop) {
            mobileBackdrop.addEventListener('click', function(e) {
                e.stopPropagation();
                closeMobileSidebar();
            });
        }

        // Mobile Services Dropdown Toggle
        const mobileServicesToggle = document.getElementById('mobileServicesToggle');
        const mobileServicesMenu = document.getElementById('mobileServicesMenu');
        if (mobileServicesToggle && mobileServicesMenu) {
            mobileServicesToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const isExpanded = mobileServicesMenu.style.display === 'flex';
                mobileServicesMenu.style.display = isExpanded ? 'none' : 'flex';
                const icon = this.querySelector('.fa-chevron-down');
                if (icon) {
                    icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
                    icon.style.transition = 'transform 0.2s';
                }
            });
        }

        // Xử lý thông báo cư dân
        const bellWrapper = document.getElementById('residentNotificationBellWrapper');
        const badge = document.getElementById('residentNotificationBadge');
        const dropdown = document.getElementById('residentNotificationDropdown');
        const listContainer = document.getElementById('residentNotificationList');
        const markAllReadBtn = document.getElementById('residentMarkAllReadBtn');

        let notificationsData = [];

        function loadNotifications() {
            fetch('{{ route("resident.notifications.index") }}')
                .then(res => res.json())
                .then(data => {
                    notificationsData = data.notifications || [];
                    const unreadCount = data.unread_count || 0;
                    
                    if (unreadCount > 0) {
                        badge.innerText = unreadCount;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                    renderNotifications();
                })
                .catch(err => console.error('Error fetching notifications:', err));
        }

        function renderNotifications() {
            if (notificationsData.length === 0) {
                listContainer.innerHTML = '<div style="padding: 20px; text-align: center; color: #64748b; font-size: 13px;">Chưa có thông báo nào.</div>';
                return;
            }

            listContainer.innerHTML = '';
            notificationsData.forEach(notif => {
                const item = document.createElement('a');
                item.href = notif.url || '#';
                item.className = 'notification-item' + (!notif.read_at ? ' notification-item--unread' : '');
                item.innerHTML = `
                    <div class="notification-item__title">${escapeHtml(notif.title)}</div>
                    <div style="font-size: 12px; color: #475569; margin-top: 2px;">${escapeHtml(notif.message)}</div>
                    <span class="notification-item__time">${notif.created_at}</span>
                `;

                item.addEventListener('click', function(e) {
                    if (!notif.read_at) {
                        // Gọi mark-as-read ngầm (không chặn navigation)
                        markNotificationRead(notif.id);
                    }
                });

                listContainer.appendChild(item);
            });
        }

        function markNotificationRead(id) {
            const csrfToken = document.querySelector('input[name="_token"]')?.value || '';
            fetch(`/resident/notifications/mark-read/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                keepalive: true  // đảm bảo request hoàn thành dù trang đã chuyển
            }).catch(() => {});
        }

        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const csrfToken = document.querySelector('input[name="_token"]')?.value || '';
                fetch('/resident/notifications/mark-read/all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                    }
                })
                .catch(err => console.error('Error marking all notifications read:', err));
            });
        }

        if (bellWrapper) {
            bellWrapper.addEventListener('click', function(e) {
                e.stopPropagation();
                if (userMenu) userMenu.classList.remove('active');
                if (servicesMenu) servicesMenu.classList.remove('active');

                const isVisible = dropdown.style.display === 'block';
                dropdown.style.display = isVisible ? 'none' : 'block';

                // Tự động đánh dấu tất cả đã đọc khi mở dropdown
                if (!isVisible && badge.style.display !== 'none') {
                    const csrfToken = document.querySelector('input[name="_token"]')?.value || '';
                    fetch('/resident/notifications/mark-read/all', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        keepalive: true
                    }).then(() => {
                        // Ẩn badge ngay lập tức
                        badge.style.display = 'none';
                        // Cập nhật lại danh sách (bỏ highlight chưa đọc)
                        loadNotifications();
                    }).catch(() => {});
                }
            });
        }

        document.addEventListener('click', function() {
            if (userMenu) userMenu.classList.remove('active');
            if (servicesMenu) servicesMenu.classList.remove('active');
            if (dropdown) dropdown.style.display = 'none';
        });

        function escapeHtml(text) {
            if (!text) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        loadNotifications();
        setInterval(loadNotifications, 60000);

        // Lắng nghe sự kiện Real-time qua Laravel Echo
        if (window.Echo && '{{ auth()->check() ? auth()->id() : "" }}') {
            window.Echo.private('App.Models.User.{{ auth()->id() }}')
                .notification((notification) => {
                    // Reload notifications to update badge and list
                    loadNotifications();
                });
        }
    });
</script>
