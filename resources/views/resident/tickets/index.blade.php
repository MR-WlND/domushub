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
            background: transparent;
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
            .desktop-view { display: block !important; }
            .mobile-view { display: none !important; }

            .tk { padding: 16px 0; }
            .tk-new-header {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                margin-bottom: 20px;
            }
            .tk-new-title { font-size: 20px; }
            .tk-new-subtitle { font-size: 13px; }
            .tk-btn-new {
                width: 100%;
                justify-content: center;
                padding: 12px;
                font-size: 14px;
            }
            
            .tk-new-stats {
                display: flex;
                overflow-x: auto;
                gap: 12px;
                padding-bottom: 8px;
                margin-bottom: 16px;
                scrollbar-width: none;
            }
            .tk-new-stats::-webkit-scrollbar { display: none; }
            .tk-new-stat-card {
                flex: 0 0 auto;
                min-width: 140px;
                padding: 14px;
                gap: 12px;
            }
            .tk-new-stat-icon { width: 36px; height: 36px; }
            .tk-new-stat-icon svg { width: 16px; height: 16px; }
            .tk-new-stat-value { font-size: 20px; }

            .tk-filter-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
                background: transparent;
                padding: 0;
                margin-bottom: 16px;
            }
            .tk-search { width: 100%; }
            .tk-filter-tabs {
                display: flex;
                overflow-x: auto;
                padding-bottom: 4px;
                scrollbar-width: none;
            }
            .tk-filter-tabs::-webkit-scrollbar { display: none; }
            .tk-filter-tab {
                white-space: nowrap;
            }

            .tk-new-list { gap: 12px; }
            .tk-new-ticket {
                flex-direction: column;
                align-items: flex-start;
                padding: 16px;
                gap: 8px;
                position: relative;
            }
            
            .tk-new-id-box {
                order: 2;
                flex-direction: row;
                align-items: center;
                gap: 4px;
                padding: 4px 8px;
                min-width: 0;
                background: #eff6ff;
                border-radius: 4px;
            }
            .tk-new-id-label { margin-bottom: 0; font-size: 11px; }
            .tk-new-id-val { font-size: 11px; font-weight: 700; }
            
            .tk-new-content {
                order: 1;
                width: 100%;
            }
            .tk-new-meta {
                margin-bottom: 8px;
                padding-right: 90px;
                flex-wrap: wrap;
                gap: 8px;
            }
            .tk-new-cat {
                background: #f1f5f9;
                color: #475569;
                padding: 2px 6px;
                border-radius: 4px;
                font-size: 10px;
            }
            .tk-new-date { font-size: 11px; }
            .tk-new-title-text { font-size: 14px; margin-bottom: 6px; padding-right: 0; }
            .tk-new-desc {
                white-space: normal;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                line-height: 1.4;
                font-size: 12px;
            }
            
            .tk-new-status {
                order: 3;
                position: absolute;
                top: 16px;
                right: 16px;
                padding: 4px 8px;
                font-size: 10px;
            }
            .tk-new-arrow { display: none; }
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



@endsection

@push('scripts')
    @vite(['resources/js/pages/resident/tickets/index.js'])
@endpush
