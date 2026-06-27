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
        <a href="{{ route('resident.posts.index') }}"
            class="resident-header__link {{ request()->routeIs('resident.posts.*') ? 'resident-header__link--active' : '' }}">
            Bảng tin
        </a>
        <div class="resident-header__dropdown-container" id="services-menu-container">
            <button class="resident-header__link resident-header__dropdown-trigger {{ (request()->routeIs('resident.vehicles.*') || request()->routeIs('resident.invoices.*') || request()->routeIs('resident.visitors.*')) ? 'resident-header__link--active' : '' }}">
                Dịch vụ <i class="fa-solid fa-chevron-down nav-dropdown-icon"></i>
            </button>
            <div class="resident-header__nav-dropdown">
                <a href="{{ route('resident.vehicles.index') }}" class="nav-dropdown-item {{ request()->routeIs('resident.vehicles.*') ? 'nav-dropdown-item--active' : '' }}">Phương tiện</a>

                <a href="{{ route('resident.visitors.index') }}" class="nav-dropdown-item {{ request()->routeIs('resident.visitors.*') ? 'nav-dropdown-item--active' : '' }}">Khách viếng thăm</a>
                <a href="{{ route('resident.facilities.index') }}" class="nav-dropdown-item {{ request()->routeIs('resident.facilities.*') ? 'nav-dropdown-item--active' : '' }}">Tiện ích</a>

                <a href="{{ route('resident.invoices.index') }}" class="nav-dropdown-item {{ request()->routeIs('resident.invoices.index') ? 'nav-dropdown-item--active' : '' }}">Thanh toán hóa đơn</a>
                <a href="{{ route('resident.invoices.history') }}" class="nav-dropdown-item {{ request()->routeIs('resident.invoices.history') ? 'nav-dropdown-item--active' : '' }}">Hóa đơn đã thanh toán</a>
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
                        e.preventDefault();
                        markNotificationRead(notif.id, notif.url);
                    }
                });

                listContainer.appendChild(item);
            });
        }

        function markNotificationRead(id, redirectUrl) {
            const csrfToken = document.querySelector('input[name="_token"]')?.value || '';
            fetch(`/resident/notifications/mark-read/${id}`, {
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
                    if (redirectUrl) {
                        window.location.href = redirectUrl;
                    } else {
                        loadNotifications();
                    }
                }
            })
            .catch(err => console.error('Error marking notification read:', err));
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
    });
</script>
