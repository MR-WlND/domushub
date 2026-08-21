@extends('layouts.resident.master')

@section('title', 'Phản ánh – DomusHub')

@push('styles')
    @vite(['resources/css/resident/tickets.css'])
    <style>
        .tk {
            font-family: 'Inter', sans-serif;
        }
        .tk-new-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }
        .tk-new-title {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 4px 0;
        }
        .tk-new-subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 0;
            max-width: 500px;
            line-height: 1.5;
        }
        .tk-btn-new {
            background: #1e3a8a;
            color: #fff;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background 0.2s;
        }
        .tk-btn-new:hover { background: #172554; color: #fff; }

        .tk-new-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }
        .tk-new-stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #f1f5f9;
        }
        .tk-new-stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .tk-new-stat-icon.total { background: #eff6ff; color: #1e40af; }
        .tk-new-stat-icon.pending { background: #ecfdf5; color: #059669; }
        .tk-new-stat-icon.done { background: #eff6ff; color: #1e40af; }
        
        .tk-new-stat-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
        .tk-new-stat-value { font-size: 24px; font-weight: 800; color: #1e3a8a; line-height: 1; }
        .tk-new-stat-value.pending { color: #059669; }

        .tk-filter-bar {
            background: #f8fafc;
            padding: 16px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .tk-search {
            position: relative;
            width: 300px;
        }
        .tk-search input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }
        .tk-search svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .tk-filter-tabs {
            display: flex;
            gap: 8px;
        }
        .tk-filter-tab {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            background: #fff;
            transition: all 0.2s;
        }
        .tk-filter-tab.active {
            background: #1e3a8a;
            color: #fff;
            border-color: #1e3a8a;
        }

        .tk-new-list { display: flex; flex-direction: column; gap: 16px; margin-bottom: 40px; }
        .tk-new-ticket {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            transition: transform 0.2s;
        }
        .tk-new-ticket:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.05); }
        .tk-new-id-box {
            background: #eff6ff;
            color: #1e40af;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
            min-width: 70px;
        }
        .tk-new-id-label { font-size: 11px; font-weight: 700; margin-bottom: 4px; }
        .tk-new-id-val { font-size: 15px; font-weight: 800; }
        .tk-new-content { flex: 1; min-width: 0; }
        .tk-new-meta { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
        .tk-new-cat { font-size: 11px; font-weight: 800; color: #0284c7; text-transform: uppercase; }
        .tk-new-date { font-size: 12px; color: #64748b; font-weight: 600; }
        .tk-new-title-text { font-size: 16px; font-weight: 700; color: #1e293b; margin: 0 0 6px 0; }
        .tk-new-desc { font-size: 13px; color: #64748b; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 500px; }
        .tk-new-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }
        .tk-new-status.pending { background: #fef2f2; color: #dc2626; }
        .tk-new-status.in_progress { background: #dcfce7; color: #15803d; }
        .tk-new-status.completed { background: #eff6ff; color: #1e40af; }
        .tk-new-status.cancelled { background: #f1f5f9; color: #64748b; }
        
        .tk-new-status-dot { width: 6px; height: 6px; border-radius: 50%; }
        .tk-new-status.pending .tk-new-status-dot { background: #dc2626; }
        .tk-new-status.in_progress .tk-new-status-dot { background: #15803d; }
        .tk-new-status.completed .tk-new-status-dot { background: #1e40af; }
        .tk-new-status.cancelled .tk-new-status-dot { background: #64748b; }

        .tk-new-arrow { color: #94a3b8; margin-left: 16px; }

        .tk-new-support {
            text-align: center;
            padding: 40px 0;
            position: relative;
            font-family: 'Inter', sans-serif;
        }
        
        @keyframes floatUpDown {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }

        .tk-new-support-icon {
            width: 70px;
            height: 70px;
            background: #fff;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            color: #1e3a8a;
            position: relative;
            z-index: 2;
            animation: floatUpDown 3s ease-in-out infinite;
        }
        .tk-new-support-bg {
            position: absolute;
            width: 70px;
            height: 70px;
            background: #cbd5e1;
            border-radius: 16px;
            top: 48px;
            left: 50%;
            transform: translateX(-56%) rotate(-10deg);
            z-index: 1;
            opacity: 0.5;
        }
        .tk-new-support p { font-size: 14px; color: #475569; margin: 0 0 4px 0; font-family: 'Inter', sans-serif; }
        .tk-new-support strong { color: #1e3a8a; font-size: 16px; font-weight: 700; font-family: 'Inter', sans-serif; }
        
        .desktop-view { display: block; }
        .mobile-view { display: none; }

        @media (max-width: 768px) {
            .desktop-view { display: none !important; }
            .mobile-view { display: block !important; min-height: 100vh; background: #f8fafc; padding-bottom: 80px; }
            .resident-content { padding: 0 !important; }
            .resident-header { display: none !important; }
            
            .mob-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 20px;
                background: #fff;
                border-bottom: 1px solid #f1f5f9;
                position: sticky;
                top: 0;
                z-index: 20;
            }
            .mob-menu-btn, .mob-bell-btn {
                background: none;
                border: none;
                font-size: 20px;
                color: #1e3a8a;
                padding: 0;
                cursor: pointer;
            }
            .mob-header-title {
                font-size: 18px;
                font-weight: 700;
                color: #1e3a8a;
                flex: 1;
                margin-left: 20px;
            }

            .mob-filter {
                display: flex;
                gap: 12px;
                padding: 12px 20px;
                overflow-x: auto;
                background: #fff;
                border-bottom: 1px solid #f1f5f9;
                position: sticky;
                top: 57px; /* Header height */
                z-index: 10;
            }
            .mob-filter::-webkit-scrollbar { display: none; }
            .mob-tab {
                padding: 8px 18px;
                border-radius: 20px;
                font-size: 13px;
                font-weight: 600;
                white-space: nowrap;
                text-decoration: none;
                background: #e0e7ff;
                color: #3730a3;
            }
            .mob-tab.active { background: #1e3a8a; color: #fff; }

            .mob-list { padding: 16px; display: flex; flex-direction: column; gap: 16px; }
            .mob-card {
                display: block;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 16px;
                text-decoration: none;
                color: inherit;
            }
            .mob-card-top { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
            .mob-icon-box {
                width: 40px;
                height: 40px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                flex-shrink: 0;
            }
            .mob-icon-box.repair { background: #eff6ff; color: #1e3a8a; }
            .mob-icon-box.electric { background: #eff6ff; color: #1e3a8a; }
            .mob-icon-box.clean { background: #ecfdf5; color: #059669; }
            .mob-icon-box.ac { background: #eff6ff; color: #1e3a8a; }
            
            .mob-card-info { flex: 1; min-width: 0; }
            .mob-card-title {
                font-size: 15px;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 4px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .mob-card-meta { font-size: 11px; color: #64748b; }
            
            .mob-status {
                padding: 4px 10px;
                border-radius: 12px;
                font-size: 11px;
                font-weight: 700;
                white-space: nowrap;
            }
            .mob-status.pending { background: #dbeafe; color: #1e40af; }
            .mob-status.in_progress { background: #dcfce7; color: #15803d; }
            .mob-status.completed { background: #6ee7b7; color: #065f46; }
            .mob-status.cancelled { background: #f1f5f9; color: #475569; }
            
            .mob-card-desc { font-size: 13px; color: #475569; line-height: 1.5; }
            .mob-card-img { margin-top: 12px; border-radius: 8px; overflow: hidden; height: 140px; }
            .mob-card-img img { width: 100%; height: 100%; object-fit: cover; }

            .mob-fab {
                position: fixed;
                bottom: 80px;
                right: 16px;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #001e71;
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(0,30,113,0.3);
                z-index: 20;
                text-decoration: none;
                font-size: 24px;
            }

            .mob-bottom-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 65px;
                background: #fff;
                border-top: 1px solid #e2e8f0;
                display: flex;
                align-items: center;
                justify-content: space-around;
                z-index: 20;
            }
            .mob-nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 4px;
                color: #64748b;
                font-size: 10px;
                font-weight: 600;
                text-decoration: none;
                width: 64px;
                height: 48px;
                border-radius: 24px;
            }
            .mob-nav-item i { font-size: 18px; }
            .mob-nav-item.active { background: #6ee7b7; color: #065f46; }
        }
    </style>
@endpush

@section('content')
<div class="desktop-view">
<div class="tk">
    {{-- SUCCESS ALERT --}}
    @if(session('success'))
        <div class="tk-alert tk-alert--success" style="margin-bottom: 20px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="tk-new-header">
        <div>
            <h1 class="tk-new-title">Phản ánh & Kiến nghị</h1>
            <p class="tk-new-subtitle">Chúng tôi luôn lắng nghe ý kiến của cư dân để cải thiện chất lượng dịch vụ và không gian sống tại ResiCare.</p>
        </div>
        <a href="{{ route('resident.tickets.create') }}" class="tk-btn-new">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Gửi phản ánh mới
        </a>
    </div>

    {{-- STATS --}}
    <div class="tk-new-stats">
        <div class="tk-new-stat-card">
            <div class="tk-new-stat-icon total">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div>
                <div class="tk-new-stat-label">Tổng số yêu cầu</div>
                <div class="tk-new-stat-value">{{ str_pad($stats['total'] ?? 0, 2, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
        <div class="tk-new-stat-card">
            <div class="tk-new-stat-icon pending">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><circle cx="12" cy="14" r="3"/><line x1="12" y1="11" x2="12" y2="14"/></svg>
            </div>
            <div>
                <div class="tk-new-stat-label">Đang xử lý</div>
                <div class="tk-new-stat-value pending">{{ str_pad(($stats['in_progress'] ?? 0), 2, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
        <div class="tk-new-stat-card">
            <div class="tk-new-stat-icon done">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <div class="tk-new-stat-label">Đã hoàn thành</div>
                <div class="tk-new-stat-value">{{ str_pad(($stats['completed'] ?? 0), 2, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <form action="{{ route('resident.tickets.index') }}" method="GET" class="tk-filter-bar">
        <div class="tk-search">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo tiêu đề hoặc ID...">
        </div>
        <div class="tk-filter-tabs">
            <a href="{{ route('resident.tickets.index') }}" class="tk-filter-tab {{ !request('status') ? 'active' : '' }}">Tất cả</a>
            <a href="{{ route('resident.tickets.index', ['status' => 'pending']) }}" class="tk-filter-tab {{ request('status') === 'pending' ? 'active' : '' }}">Chờ tiếp nhận</a>
            <a href="{{ route('resident.tickets.index', ['status' => 'in_progress']) }}" class="tk-filter-tab {{ request('status') === 'in_progress' ? 'active' : '' }}">Đang xử lý</a>
            <a href="{{ route('resident.tickets.index', ['status' => 'completed']) }}" class="tk-filter-tab {{ request('status') === 'completed' ? 'active' : '' }}">Hoàn thành</a>
            <button type="submit" style="display:none;"></button>
        </div>
    </form>

    {{-- LIST --}}
    <div class="tk-new-list">
        @forelse($tickets as $ticket)
            <a href="{{ route('resident.tickets.show', $ticket->id) }}" class="tk-new-ticket">
                <div class="tk-new-id-box">
                    <div class="tk-new-id-label">ID</div>
                    <div class="tk-new-id-val">#{{ $ticket->id }}</div>
                </div>
                <div class="tk-new-content">
                    <div class="tk-new-meta">
                        <span class="tk-new-cat">{{ mb_strtoupper($ticket->department->name ?? 'CHUNG') }}</span>
                        <span class="tk-new-date">{{ $ticket->created_at->format('d/m/Y') }}</span>
                    </div>
                    <h3 class="tk-new-title-text">{{ $ticket->title }}</h3>
                    <p class="tk-new-desc">{{ $ticket->description }}</p>
                </div>
                
                @php
                    $statusClass = 'pending';
                    $statusText = 'Chờ tiếp nhận';
                    if(in_array($ticket->status, ['in_progress', 'assigned'])) {
                        $statusClass = 'in_progress';
                        $statusText = 'Đang xử lý';
                    } elseif($ticket->status === 'completed') {
                        $statusClass = 'completed';
                        $statusText = 'Đã hoàn thành';
                    } elseif($ticket->status === 'cancelled') {
                        $statusClass = 'cancelled';
                        $statusText = 'Đã hủy';
                    }
                @endphp
                <div class="tk-new-status {{ $statusClass }}">
                    <div class="tk-new-status-dot"></div>
                    {{ $statusText }}
                </div>
                <div class="tk-new-arrow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </div>
            </a>
        @empty
            <div style="text-align: center; padding: 40px; color: #64748b;">
                Không tìm thấy phản ánh nào.
            </div>
        @endforelse
    </div>
    
    @if($tickets->hasPages())
        <div class="tk-pagination" style="margin-bottom: 24px;">
            {{ $tickets->links() }}
        </div>
    @endif

    {{-- SUPPORT SECTION --}}
    <div class="tk-new-support">
        <div class="tk-new-support-bg"></div>
        <div class="tk-new-support-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6ZM20 6L12 11L4 6H20ZM20 18H4V8L12 13L20 8V18Z"/></svg>
        </div>
        <p>Bạn cần hỗ trợ thêm?</p>
        <strong>Liên hệ Hotline: 1900 1234</strong>
    </div>

</div>
</div> <!-- End desktop-view -->

<!-- Mobile View -->
<div class="mobile-view">
    <!-- Mobile Menu Drawer (Matches Master Header 100%) -->
    <div id="mobMenuOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.45); backdrop-filter:blur(3px); -webkit-backdrop-filter:blur(3px); z-index:999;"></div>
    <div id="mobMenuDrawer" style="position:fixed; top:0; left:-300px; width:270px; height:100%; background:#ffffff; z-index:1000; transition:left 0.28s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 10px 0 30px rgba(0, 35, 111, 0.15); display:flex; flex-direction:column;">
        <div style="padding: 18px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <strong style="color:#00236f; font-size:1.15rem; font-weight:800; letter-spacing:-0.01em;">DomusHub</strong>
            <button id="mobMenuClose" style="background:#f1f5f9; border:none; width:32px; height:32px; border-radius:8px; font-size:16px; color:#64748b; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.2s;"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div style="padding: 16px 12px; display: flex; flex-direction: column; gap: 6px; overflow-y: auto;">
            <a href="{{ route('resident.dashboard') }}" class="mob-drawer-item {{ request()->routeIs('resident.dashboard') ? 'mob-drawer-item--active' : '' }}">Trang chủ</a>
            <a href="{{ route('resident.posts.index') }}" class="mob-drawer-item {{ request()->routeIs('resident.posts.*') ? 'mob-drawer-item--active' : '' }}">Bản tin</a>
            <a href="{{ route('resident.members.index') }}" class="mob-drawer-item {{ request()->routeIs('resident.members.*') ? 'mob-drawer-item--active' : '' }}">Thành viên</a>
            
            <div class="mob-drawer-dropdown">
                <button type="button" class="mob-drawer-item mob-drawer-trigger {{ (request()->routeIs('resident.vehicles.*') || request()->routeIs('resident.invoices.*') || request()->routeIs('resident.visitors.*') || request()->routeIs('resident.facilities.*') || request()->routeIs('resident.temporary-registrations.*')) ? 'mob-drawer-item--active' : '' }}" onclick="this.nextElementSibling.classList.toggle('show'); this.querySelector('.chevron').classList.toggle('rotate');">
                    <span>Dịch vụ</span>
                    <i class="fa-solid fa-chevron-down chevron" style="font-size: 0.75rem; transition: transform 0.2s ease;"></i>
                </button>
                <div class="mob-drawer-sub" style="display: none; flex-direction: column; gap: 2px; padding-left: 12px; margin-top: 4px; border-left: 2px solid #e2e8f0;">
                    <a href="{{ route('resident.temporary-registrations.index') }}" class="mob-drawer-sub-item {{ request()->routeIs('resident.temporary-registrations.*') ? 'mob-drawer-item--active' : '' }}">Tạm trú / Tạm vắng</a>
                    <a href="{{ route('resident.vehicles.index') }}" class="mob-drawer-sub-item {{ request()->routeIs('resident.vehicles.*') ? 'mob-drawer-item--active' : '' }}">Phương tiện</a>
                    <a href="{{ route('resident.facilities.index') }}" class="mob-drawer-sub-item {{ request()->routeIs('resident.facilities.*') || request()->routeIs('resident.facility-bookings.*') ? 'mob-drawer-item--active' : '' }}">Tiện ích</a>
                    <a href="{{ route('resident.visitors.index') }}" class="mob-drawer-sub-item {{ request()->routeIs('resident.visitors.*') ? 'mob-drawer-item--active' : '' }}">Khách ghé thăm</a>
                    <a href="{{ route('resident.invoices.index') }}" class="mob-drawer-sub-item {{ request()->routeIs('resident.invoices.*') ? 'mob-drawer-item--active' : '' }}">Thanh toán hóa đơn</a>
                </div>
            </div>

            <a href="{{ route('resident.tickets.index') }}" class="mob-drawer-item {{ request()->routeIs('resident.tickets.*') ? 'mob-drawer-item--active' : '' }}">Phản Ánh</a>
            <a href="{{ route('resident.contact') }}" class="mob-drawer-item {{ request()->routeIs('resident.contact') ? 'mob-drawer-item--active' : '' }}">Liên hệ</a>
            
            <hr style="border:0; border-bottom:1px solid #f1f5f9; margin: 8px 4px;">
            <a href="#" onclick="event.preventDefault(); document.getElementById('resident-logout-form').submit();" class="mob-drawer-item mob-drawer-item--logout">Đăng xuất</a>
        </div>
    </div>

    <!-- Top Header -->
    <div class="mob-header">
        <button class="mob-menu-btn" id="mobMenuOpenBtn"><i class="fa-solid fa-bars"></i></button>
        <div class="mob-header-title">Phản ánh</div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <button class="mob-bell-btn"><i class="fa-regular fa-bell"></i></button>
            <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80' }}" alt="Avatar" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
        </div>
    </div>

    <!-- Top Filter -->
    <div class="mob-filter">
        <a href="{{ route('resident.tickets.index') }}" class="mob-tab {{ !request('status') ? 'active' : '' }}">Tất cả</a>
        <a href="{{ route('resident.tickets.index', ['status' => 'pending']) }}" class="mob-tab {{ request('status') === 'pending' ? 'active' : '' }}">Chờ duyệt</a>
        <a href="{{ route('resident.tickets.index', ['status' => 'in_progress']) }}" class="mob-tab {{ request('status') === 'in_progress' ? 'active' : '' }}">Đang sửa</a>
        <a href="{{ route('resident.tickets.index', ['status' => 'completed']) }}" class="mob-tab {{ request('status') === 'completed' ? 'active' : '' }}">Xong</a>
    </div>

    <!-- Cards -->
    <div class="mob-list">
        @forelse($tickets as $ticket)
        <a href="{{ route('resident.tickets.show', $ticket->id) }}" class="mob-card">
            <div class="mob-card-top">
                @php
                    $cat = mb_strtolower($ticket->department->name ?? '');
                    $iconClass = 'fa-solid fa-list-check';
                    $boxClass = 'clean';
                    if (str_contains($cat, 'nước') || str_contains($cat, 'kỹ thuật')) {
                        $iconClass = 'fa-solid fa-wrench';
                        $boxClass = 'repair';
                    } elseif (str_contains($cat, 'điện')) {
                        $iconClass = 'fa-solid fa-bolt';
                        $boxClass = 'electric';
                    } elseif (str_contains($cat, 'điều hòa') || str_contains($cat, 'ac')) {
                        $iconClass = 'fa-solid fa-snowflake';
                        $boxClass = 'ac';
                    }
                    
                    $mobStatusClass = 'pending';
                    $mobStatusText = 'Chờ duyệt';
                    if(in_array($ticket->status, ['in_progress', 'assigned'])) {
                        $mobStatusClass = 'in_progress';
                        $mobStatusText = 'Đang sửa';
                    } elseif($ticket->status === 'completed') {
                        $mobStatusClass = 'completed';
                        $mobStatusText = 'Xong';
                    } elseif($ticket->status === 'cancelled') {
                        $mobStatusClass = 'cancelled';
                        $mobStatusText = 'Đã hủy';
                    }
                @endphp
                <div class="mob-icon-box {{ $boxClass }}">
                    <i class="{{ $iconClass }}"></i>
                </div>
                <div class="mob-card-info">
                    <div class="mob-card-title">{{ $ticket->title }}</div>
                    <div class="mob-card-meta">{{ $ticket->created_at->format('d/m/Y') }} • Căn hộ {{ $ticket->apartment->name ?? 'A2' }}</div>
                </div>
                <div class="mob-status {{ $mobStatusClass }}">
                    {{ $mobStatusText }}
                </div>
            </div>
            <div class="mob-card-desc">
                {{ Str::limit($ticket->description, 80) }}
            </div>
            @if($ticket->images && count($ticket->images) > 0)
            <div class="mob-card-img">
                @php
                    $imgs = $ticket->images;
                @endphp
                <img src="{{ asset('storage/'.$imgs[0]) }}">
            </div>
            @endif
        </a>
        @empty
        <div style="text-align:center; padding: 40px; color: #64748b;">
            Không có phản ánh nào.
        </div>
        @endforelse
    </div>

    <!-- FAB -->
    <a href="{{ route('resident.tickets.create') }}" class="mob-fab">
        <i class="fa-solid fa-plus"></i>
    </a>

    <!-- Bottom Nav -->
    <div class="mob-bottom-nav">
        <a href="#" class="mob-nav-item">
            <i class="fa-solid fa-house"></i>
            <span>Home</span>
        </a>
        <a href="#" class="mob-nav-item">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span>Bills</span>
        </a>
        <a href="{{ route('resident.tickets.index') }}" class="mob-nav-item active">
            <i class="fa-solid fa-screwdriver-wrench"></i>
            <span>Requests</span>
        </a>
        <a href="#" onclick="event.preventDefault(); document.getElementById('resident-logout-form').submit();" class="mob-nav-item">
            <i class="fa-solid fa-user"></i>
            <span>Profile</span>
        </a>
    </div>
</div>

@endsection

@push('scripts')
    @vite(['resources/js/pages/resident/tickets/index.js'])
@endpush
