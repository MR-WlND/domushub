<aside class="dashboard-sidebar" id="dashboardSidebar">
    <div class="dashboard-brand">
        <h2 class="dashboard-brand__title">Chung cư Số</h2>
        <p class="dashboard-brand__label">
            @php
                $role = auth()->user()->role;
            @endphp
            @if($role === 'admin')
                Admin Portal
            @elseif($role === 'manager')
                Quản lý
            @elseif($role === 'staff')
                Nhân viên
            @elseif($role === 'technician')
                Kỹ thuật viên
            @endif
        </p>
    </div>

    <nav class="dashboard-nav">

        {{-- ============================================================== --}}
        {{-- TỔNG QUAN - Admin & Manager --}}
        {{-- ============================================================== --}}
        @if(in_array($role, ['admin', 'manager']))
        <div class="nav-section">
            <span class="nav-section__label">TỔNG QUAN</span>
            <a href="{{ route('admin.dashboard') }}" class="dashboard-nav__item {{ request()->routeIs('admin.dashboard') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                </svg>
                <span>Trang tổng quan</span>
            </a>
            <a href="{{ route('admin.statistics') }}" class="dashboard-nav__item {{ request()->routeIs('admin.statistics') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                <span>Báo cáo & Thống kê</span>
            </a>
        </div>
        @endif

        {{-- ============================================================== --}}
        {{-- QUẢN LÝ HẠ TẦNG - Chỉ Admin --}}
        {{-- ============================================================== --}}
        @if($role === 'admin')
        <div class="nav-section">
            <span class="nav-section__label">QUẢN LÝ HẠ TẦNG</span>
            <a href="{{ route('admin.blocks.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.blocks.*') || request()->routeIs('admin.floors.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="18" rx="1"></rect>
                    <path d="M9 3v18"></path>
                    <path d="M15 3v18"></path>
                    <path d="M2 9h20"></path>
                    <path d="M2 15h20"></path>
                </svg>
                <span>Toà nhà & Tầng</span>
            </a>
            <a href="{{ route('admin.apartments.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.apartments.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Căn hộ</span>
            </a>
            <a href="{{ route('admin.invitations.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.invitations.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <span>Mã mời đăng ký</span>
            </a>
        </div>
        @endif

        {{-- ============================================================== --}}
        {{-- ĐIỆN NƯỚC & HOÁ ĐƠN - Admin, Staff & Technician --}}
        {{-- ============================================================== --}}
        @if(in_array($role, ['admin', 'staff', 'technician']))
        <div class="nav-section">
            <span class="nav-section__label">ĐIỆN NƯỚC & HOÁ ĐƠN</span>
            <a href="{{ route('admin.utility-readings.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.utility-readings.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"></path>
                </svg>
                <span>Chốt số điện nước</span>
            </a>
            @if($role === 'admin')
            <a href="{{ route('admin.service-prices.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.service-prices.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                <span>Cấu hình giá</span>
            </a>
            @endif
            @if(in_array($role, ['admin', 'staff']))
            <a href="{{ route('admin.invoices.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.invoices.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
                <span>Hoá đơn</span>
            </a>
            @endif
        </div>
        @endif

        {{-- ============================================================== --}}
        {{-- NHIỆM VỤ KỸ THUẬT - Chỉ Technician --}}
        {{-- ============================================================== --}}
        @if($role === 'technician')
        <div class="nav-section">
            <span class="nav-section__label">NHIỆM VỤ KỸ THUẬT</span>
            <a href="{{ route('admin.tickets.my-tasks') }}" class="dashboard-nav__item {{ request()->routeIs('admin.tickets.my-tasks') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                </svg>
                <span>Nhiệm vụ của tôi</span>
                {{-- Badge: số nhiệm vụ đang chờ --}}
                @php
                    $myPendingCount = \App\Models\Ticket::where('handler_id', auth()->id())
                        ->whereIn('status', ['assigned', 'in_progress'])->count();
                @endphp
                @if($myPendingCount > 0)
                    <span style="margin-left:auto; background:#f97316; color:#fff; font-size:.68rem; font-weight:800; padding:1px 7px; border-radius:12px; min-width:20px; text-align:center;">{{ $myPendingCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.tickets.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.tickets.index') || request()->routeIs('admin.tickets.show') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Tất cả phản ánh</span>
            </a>
        </div>
        @endif

        {{-- ============================================================== --}}
        {{-- DỊCH VỤ CƯ DÂN - Admin & Manager --}}
        {{-- ============================================================== --}}
        @if(in_array($role, ['admin', 'manager']))
        <div class="nav-section">
            <span class="nav-section__label">DỊCH VỤ CƯ DÂN</span>
            <a href="{{ route('admin.residents.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.residents.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Danh sách cư dân</span>
            </a>
            <a href="{{ route('admin.tickets.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.tickets.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Quản lý phản ánh</span>
            </a>
            <a href="{{ route('admin.vehicles.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.vehicles.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13" rx="2"></rect>
                    <path d="M16 8h4l3 5v3h-7V8z"></path>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
                <span>Quản lý xe</span>
            </a>
            <a href="{{ route('admin.parking-lots.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.parking-lots.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <path d="M9 3v18"></path>
                    <path d="M14 9h4"></path>
                    <path d="M14 12h4"></path>
                    <path d="M14 15h4"></path>
                </svg>
                <span>Quản lý lốt đỗ</span>
            </a>

            <a href="{{ route('admin.amenities.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.amenities.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>Đặt tiện ích</span>
            </a>
        </div>
        @endif



        {{-- ============================================================== --}}
        {{-- TƯƠNG TÁC & BẢNG TIN - Admin & Manager --}}
        {{-- ============================================================== --}}
        @if(in_array($role, ['admin', 'manager']))
        <div class="nav-section">
            <span class="nav-section__label">TƯƠNG TÁC & BẢNG TIN</span>
            <a href="{{ route('admin.announcements.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.announcements.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.82a16 16 0 0 0 6.29 6.29l.96-.87a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <span>Bảng tin chung cư</span>
            </a>
            <a href="{{ route('admin.posts.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.posts.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Quản lý bài viết</span>
            </a>
        </div>
        @endif

        {{-- ============================================================== --}}
        {{-- CẤU HÌNH HỆ THỐNG - Chỉ Admin --}}
        {{-- Manager, Staff, Technician KHÔNG thấy mục này --}}
        {{-- ============================================================== --}}
        @if($role === 'admin')
        <div class="nav-section">
            <span class="nav-section__label">CẤU HÌNH HỆ THỐNG</span>
            <a href="{{ route('admin.users.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.users.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <span>Phân quyền</span>
            </a>
            <a href="{{ route('admin.activity-logs.index') }}" class="dashboard-nav__item {{ request()->routeIs('admin.activity-logs.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                <span>Lịch sử thao tác</span>
            </a>
        </div>
        @endif

    </nav>

    <div class="dashboard-sidebar__bottom">
        <div class="dashboard-sidebar__footer">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dashboard-footer__item">
                <svg class="dashboard-footer__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span>Đăng xuất</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</aside>
