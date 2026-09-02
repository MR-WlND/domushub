@php
    $role = auth()->check() ? auth()->user()->role : null;
@endphp

@if($role === 'technician')
<style>
    @media (min-width: 769px) {
        .technician-mobile-header { display: none !important; }
    }
    @media (max-width: 768px) {
        .dashboard-topbar { display: none !important; }
        .technician-mobile-header { display: grid !important; }
    }
</style>
{{-- Mobile Header for Technician (Resident Design) --}}
<header class="resident-header technician-mobile-header">
    <div class="resident-header__brand-mobile-wrapper">
        <button id="mobileMenuToggle" class="resident-header__mobile-toggle" onclick="openSidebar()" aria-label="Open menu" style="display:block;">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="resident-header__brand">
            <a href="{{ portal_route('tickets.my-tasks') }}" class="resident-header__brand-link">DomusHub</a>
        </div>
    </div>

    <nav class="resident-header__nav" id="residentNavMenu" aria-label="Technician navigation" style="display:none;">
    </nav>

    <div class="resident-header__actions">
        {{-- Bell notification --}}
        <div class="header-icon-wrapper" id="mobileNotificationBellWrapper" style="position: relative;">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="notification-badge" id="mobileNotificationBadge" style="display: none;">0</span>
        </div>

        <div class="resident-header__user-menu" id="mobile-user-menu-container">
            <div class="resident-header__user-avatar-container">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}"
                        alt="Avatar" class="resident-header__user-avatar">
                @else
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                        alt="Avatar" class="resident-header__user-avatar">
                @endif
            </div>
            <div class="resident-header__dropdown">
                <div class="dropdown-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span style="font-size: 11px; color: #0b57d0; font-weight: 600;">KỸ THUẬT VIÊN</span>
                </div>
                <hr class="dropdown-divider">
                <a href="{{ portal_route('profile.index') }}" class="dropdown-item">Hồ sơ</a>
                <a href="#"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="dropdown-item dropdown-item--logout" style="color: #ef4444;">Đăng xuất</a>
            </div>
        </div>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mobileUserMenu = document.getElementById('mobile-user-menu-container');
    if (mobileUserMenu) {
        mobileUserMenu.addEventListener('click', function(e) {
            e.stopPropagation();
            this.classList.toggle('active');
        });
    }
});
</script>
@endif

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
        <div class="header-icon-wrapper" id="notificationBellWrapper" style="position: relative;">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>

            {{-- Notifications Dropdown --}}
            <div class="notification-dropdown" id="notificationDropdown">
                <div style="padding: 12px 16px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                    <span style="font-weight: 700; color: #0f172a; font-size: 14px;">Thông báo</span>
                    <button id="markAllReadBtn" style="font-size: 12px; color: #0b57d0; background: none; border: none; cursor: pointer; font-weight: 600; padding: 0;">Đánh dấu đã đọc</button>
                </div>
                <div id="notificationList" style="max-height: 320px; overflow-y: auto;">
                    <div style="padding: 30px; text-align: center; color: #94a3b8; font-size: 13px;">Đang tải thông báo...</div>
                </div>
                <div style="padding: 10px; border-top: 1px solid #e2e8f0; text-align: center; background: #f8fafc; border-bottom-left-radius: 8px; border-bottom-right-radius: 8px;">
                    <a href="{{ portal_route('notification-logs.index') }}" style="font-size: 13px; color: #0b57d0; font-weight: 600; text-decoration: none; display: block; padding: 4px;">Xem lịch sử thông báo</a>
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

        <a href="{{ portal_route('profile.index') }}" class="dashboard-user-pill" style="text-decoration: none; color: inherit;">
            <div class="user-info">
                <strong class="user-name">@yield('user_name', auth()->user()->name ?? 'Admin User')</strong>
                <span class="user-role">
                    @yield('user_role_label',
                        match(auth()->user()->role ?? 'admin') {
                            'admin' => 'SUPER ADMINISTRATOR',
                            'manager' => 'QUẢN LÝ',
                            'staff' => 'NHÂN VIÊN KẾ TOÁN',
                            'technician' => 'KỸ THUẬT VIÊN',
                            default => strtoupper(auth()->user()->role ?? 'ADMIN'),
                        }
                    )
                </span>
            </div>
            <div class="user-avatar-container">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Avatar" class="user-avatar">
                @else
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Avatar" class="user-avatar">
                @endif
            </div>
        </a>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bellWrapper = document.getElementById('notificationBellWrapper');
    const badge = document.getElementById('notificationBadge');
    const dropdown = document.getElementById('notificationDropdown');
    const listContainer = document.getElementById('notificationList');
    const markAllBtn = document.getElementById('markAllReadBtn');
    
    let notificationsData = [];
    
    // Tải danh sách thông báo
    function loadNotifications() {
        fetch('{{ portal_route('notifications.index') }}')
            .then(res => res.json())
            .then(data => {
                const count = data.unread_count || 0;
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
                
                notificationsData = data.notifications || [];
                renderNotifications();
            })
            .catch(err => console.error('Error fetching notifications:', err));
    }
    
    function renderNotifications() {
        if (!listContainer) return;
        if (notificationsData.length === 0) {
            listContainer.innerHTML = '<div style="padding: 30px; text-align: center; color: #94a3b8; font-size: 13px;">Không có thông báo nào.</div>';
            return;
        }
        
        listContainer.innerHTML = '';
        notificationsData.forEach(notif => {
            const item = document.createElement('a');
            item.href = notif.url || '#';
            item.className = 'notification-item' + (!notif.read_at ? ' notification-item--unread' : '');
            item.setAttribute('data-id', notif.id);
            
            item.innerHTML = `
                <div class="notification-item__title">${notif.title}</div>
                <div style="font-size: 12px; color: #475569; margin-top: 2px;">${notif.message}</div>
                <span class="notification-item__time">${notif.created_at}</span>
            `;
            
            item.addEventListener('click', function (e) {
                if (!notif.read_at) {
                    e.preventDefault();
                    markAsRead(notif.id, notif.url);
                }
            });
            
            listContainer.appendChild(item);
        });
    }
    
    function markAsRead(id, redirectUrl) {
        let url = '{{ portal_route('notifications.mark-read', 'ID_HERE') }}'.replace('ID_HERE', id);
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (redirectUrl && redirectUrl !== '#') {
                    window.location.href = redirectUrl;
                } else {
                    loadNotifications();
                }
            }
        })
        .catch(err => {
            console.error('Error marking notification read:', err);
            if (redirectUrl && redirectUrl !== '#') {
                window.location.href = redirectUrl;
            }
        });
    }
    
    if (bellWrapper && dropdown) {
        bellWrapper.addEventListener('click', function (e) {
            e.stopPropagation();
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
        });
        
        dropdown.addEventListener('click', function (e) {
            e.stopPropagation();
        });
        
        document.addEventListener('click', function (e) {
            if (!bellWrapper.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }
    
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            fetch('{{ portal_route('notifications.mark-read', 'all') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
    
    loadNotifications();
    setInterval(loadNotifications, 60000);

    if (window.Echo && '{{ auth()->check() ? auth()->id() : "" }}') {
        window.Echo.private('App.Models.User.{{ auth()->id() }}')
            .notification((notification) => {
                loadNotifications();
            });
    }
});
</script>
