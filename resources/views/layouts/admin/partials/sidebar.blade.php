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
                Kế toán
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
            <a href="{{ portal_route('dashboard') }}" class="dashboard-nav__item {{ is_portal_route('dashboard') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                </svg>
                <span>Trang tổng quan</span>
            </a>
            <a href="{{ portal_route('statistics') }}" class="dashboard-nav__item {{ is_portal_route('statistics.*') || is_portal_route('statistics') || is_portal_route('amenities.statistics') ? 'dashboard-nav__item--active' : '' }}">
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
            <a href="{{ portal_route('blocks.index') }}" class="dashboard-nav__item {{ is_portal_route('blocks.*') || is_portal_route('floors.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="18" rx="1"></rect>
                    <path d="M9 3v18"></path>
                    <path d="M15 3v18"></path>
                    <path d="M2 9h20"></path>
                    <path d="M2 15h20"></path>
                </svg>
                <span>Toà nhà & Tầng</span>
            </a>
            <a href="{{ portal_route('apartments.index') }}" class="dashboard-nav__item {{ is_portal_route('apartments.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Căn hộ</span>
            </a>
            <a href="{{ portal_route('apartment-types.index') }}" class="dashboard-nav__item {{ is_portal_route('apartment-types.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                    <line x1="15" y1="3" x2="15" y2="21"></line>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                    <line x1="3" y1="15" x2="21" y2="15"></line>
                </svg>
                <span>Loại căn hộ</span>
            </a>
            <a href="{{ portal_route('invitations.index') }}" class="dashboard-nav__item {{ is_portal_route('invitations.*') ? 'dashboard-nav__item--active' : '' }}">
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
        {{-- NƯỚC & HOÁ ĐƠN - Admin & Staff --}}
        {{-- ============================================================== --}}
        @if(in_array($role, ['admin', 'manager', 'staff', 'technician']))
        <div class="nav-section">
            <span class="nav-section__label">NƯỚC & HOÁ ĐƠN</span>
            <a href="{{ portal_route('utility-readings.index') }}" class="dashboard-nav__item {{ is_portal_route('utility-readings.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"></path>
                </svg>
                <span>{{ $role === 'technician' ? 'Ghi số nước' : 'Chốt số nước' }}</span>
            </a>

            @if(in_array($role, ['admin', 'staff']))
            <a href="{{ portal_route('service-prices.index') }}" class="dashboard-nav__item {{ is_portal_route('service-prices.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
                <span>Cấu hình giá</span>
            </a>
            @endif
            @if(in_array($role, ['admin', 'staff']))
            <a href="{{ portal_route('invoices.index') }}" class="dashboard-nav__item {{ is_portal_route('invoices.*') ? 'dashboard-nav__item--active' : '' }}">
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
            <a href="{{ portal_route('tickets.my-tasks') }}" class="dashboard-nav__item {{ is_portal_route('tickets.my-tasks') ? 'dashboard-nav__item--active' : '' }}">
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
            <a href="{{ portal_route('tickets.index') }}" class="dashboard-nav__item {{ is_portal_route('tickets.index') || ($role === 'technician' && is_portal_route('tickets.show')) ? 'dashboard-nav__item--active' : '' }}">
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
            <a href="{{ portal_route('residents.index') }}" class="dashboard-nav__item {{ is_portal_route('residents.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Danh sách cư dân</span>
            </a>
            <a href="{{ portal_route('tickets.index') }}" class="dashboard-nav__item {{ is_portal_route('tickets.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Quản lý phản ánh</span>
            </a>
            {{-- Duyệt đăng ký xe: admin thấy toàn bộ quản lý xe, manager thấy duyệt xe --}}
            <a href="{{ portal_route('vehicles.index') }}" class="dashboard-nav__item {{ is_portal_route('vehicles.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13" rx="2"></rect>
                    <path d="M16 8h4l3 5v3h-7V8z"></path>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
                <span>{{ $role === 'manager' ? 'Duyệt đăng ký xe' : 'Quản lý xe' }}</span>
            </a>
            @if($role === 'admin')
            <a href="{{ portal_route('parking-lots.index') }}" class="dashboard-nav__item {{ is_portal_route('parking-lots.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <path d="M9 3v18"></path>
                    <path d="M14 9h4"></path>
                    <path d="M14 12h4"></path>
                    <path d="M14 15h4"></path>
                </svg>
                <span>Quản lý lốt đỗ</span>
            </a>

            
            <a href="{{ portal_route('visitor-logs.index') }}" class="dashboard-nav__item {{ is_portal_route('visitor-logs.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Khách ghé thăm</span>
            </a>
            @endif

            <a href="{{ portal_route('amenities.index') }}" class="dashboard-nav__item {{ is_portal_route('amenities.index') || is_portal_route('amenities.create') || is_portal_route('amenities.edit') || is_portal_route('amenities.show') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Quản lý tiện ích</span>
            </a>
            <a href="{{ portal_route('amenities.bookings') }}" class="dashboard-nav__item {{ is_portal_route('amenities.bookings') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>Lịch đặt tiện ích</span>
            </a>
        </div>
        @endif



        {{-- ============================================================== --}}
        {{-- QUẢN LÝ VẬN HÀNH - Admin & Manager --}}
        {{-- ============================================================== --}}
        @if(in_array($role, ['admin', 'manager']))
        <div class="nav-section">
            <span class="nav-section__label">QUẢN LÝ VẬN HÀNH</span>
            <a href="{{ portal_route('cleaning-tasks.index') }}" class="dashboard-nav__item {{ is_portal_route('cleaning-tasks.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path>
                    <rect x="9" y="3" width="6" height="4" rx="2"></rect>
                    <path d="M9 14l2 2 4-4"></path>
                </svg>
                <span>Giao việc vệ sinh</span>
            </a>
            <a href="{{ portal_route('cleaning-reports.index') }}" class="dashboard-nav__item {{ is_portal_route('cleaning-reports.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span>Báo cáo sự cố</span>
            </a>
        </div>
        @endif

        {{-- ============================================================== --}}
        {{-- TƯƠNG TÁC & BẢNG TIN - Admin & Manager --}}
        {{-- ============================================================== --}}
        @if(in_array($role, ['admin', 'manager']))
        <div class="nav-section">
            <span class="nav-section__label">TƯƠNG TÁC & BẢNG TIN</span>
            <a href="{{ portal_route('announcements.index') }}" class="dashboard-nav__item {{ is_portal_route('announcements.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.82a16 16 0 0 0 6.29 6.29l.96-.87a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
                </svg>
                <span>Bảng tin chung cư</span>
            </a>
            <a href="{{ portal_route('posts.index') }}" class="dashboard-nav__item {{ is_portal_route('posts.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span>Quản lý bài viết</span>
            </a>
        </div>
        @endif

        {{-- ============================================================== --}}
        {{-- QUẢN TRỊ NHÂN SỰ - Chỉ Admin --}}
        {{-- ============================================================== --}}
        @if($role === 'admin')
        <div class="nav-section">
            <span class="nav-section__label">QUẢN TRỊ NHÂN SỰ</span>
            
            <a href="{{ portal_route('departments.index') }}" class="dashboard-nav__item {{ is_portal_route('departments.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Phòng ban</span>
            </a>

            <a href="{{ portal_route('staffs.index') }}" class="dashboard-nav__item {{ is_portal_route('staffs.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Hồ sơ nhân sự</span>
            </a>

            <a href="{{ portal_route('schedules.index') }}" class="dashboard-nav__item {{ is_portal_route('schedules.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                <span>Bảng phân ca</span>
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

            <a href="{{ portal_route('users.index') }}" class="dashboard-nav__item {{ is_portal_route('users.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                <span>Phân quyền</span>
            </a>

            <a href="{{ portal_route('notification-logs.index') }}" class="dashboard-nav__item {{ is_portal_route('notification-logs.*') ? 'dashboard-nav__item--active' : '' }}">
                <svg class="dashboard-nav__icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <span>Lịch sử Thông báo</span>
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

    <style>
        /* Submenu styling for Admin Sidebar - Synced with reference layout */
        .dashboard-nav__group {
            display: flex;
            flex-direction: column;
        }
        .dashboard-nav__item--parent {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dashboard-nav__chevron {
            margin-left: auto;
            color: #5f6368;
            transition: transform 0.25s ease;
            flex-shrink: 0;
        }
        .dashboard-nav__item--active .dashboard-nav__chevron {
            color: #0b57d0;
        }
        .dashboard-nav__submenu {
            display: none;
            flex-direction: column;
            background-color: #eff5ff !important; /* Màu nền xanh nhạt dính liền */
            border-left: 2px solid #0b57d0 !important; /* Đường kẻ dọc màu xanh đậm bên trái */
            margin-left: 24px;
            margin-right: 12px;
            margin-top: 4px;
            margin-bottom: 4px;
            border-radius: 4px;
            padding: 4px !important;
            box-shadow: none !important;
            border-top: none !important;
            border-right: none !important;
            border-bottom: none !important;
        }
        .dashboard-nav__group--open .dashboard-nav__submenu {
            display: flex;
        }
        .dashboard-nav__group--open .dashboard-nav__chevron {
            transform: rotate(180deg);
        }
        .dashboard-nav__subitem {
            display: flex !important;
            align-items: center;
            color: #3c4563 !important;
            text-decoration: none !important;
            padding: 8px 16px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            transition: all 0.18s ease;
            border-radius: 4px;
            margin: 2px 0;
            border-left: none !important;
            background: transparent !important;
        }
        .dashboard-nav__subitem:hover {
            background-color: rgba(11, 87, 208, 0.05) !important;
            color: #0b57d0 !important;
            padding-left: 16px !important;
        }
        .dashboard-nav__subitem--active {
            background-color: #dce9ff !important;
            color: #0b57d0 !important;
            font-weight: 600 !important;
            border-left: none !important;
        }
    </style>

    <script>
        function toggleSubmenu(event, element) {
            const group = element.parentElement;
            const isActive = element.classList.contains('dashboard-nav__item--active');
            
            if (isActive) {
                // If clicked while active, toggle the open/close state instead of re-navigating
                event.preventDefault();
                group.classList.toggle('dashboard-nav__group--open');
            }
            // If not active, let the browser navigate to the default page and the server will render it open
        }
    </script>
</aside>
 
