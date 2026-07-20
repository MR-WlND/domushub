@extends('layouts.admin.master')

@section('page_title', 'Điều phối kỹ thuật')
@section('page_kicker', 'Dịch vụ cư dân')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', auth()->user()->role)

@push('styles')
    @vite(['resources/css/pages/admin/tickets/index.css'])
    <style>
        .dispatch-grid {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 1.5rem;
            align-items: start;
        }
        @media (max-width: 992px) {
            .dispatch-grid {
                grid-template-columns: 1fr;
            }
        }
        .tech-section {
            background: #fff;
            border-radius: 14px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            border: 1px solid #f1f5f9;
        }
        .tech-section-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #f1f5f9;
        }
        .tech-card {
            background: #f8fafc;
            border-radius: 10px;
            padding: 1rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 0.75rem;
            transition: all 0.2s;
        }
        .tech-card:last-child {
            margin-bottom: 0;
        }
        .tech-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,.04);
            border-color: #cbd5e1;
        }
        .tech-card__header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.5rem;
        }
        .tech-card__name {
            font-weight: 700;
            font-size: 0.95rem;
            color: #0f172a;
        }
        .tech-card__phone {
            font-size: 0.8rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 3px;
        }
        .tech-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 700;
        }
        .tech-badge--free {
            background: #d1fae5;
            color: #065f46;
        }
        .tech-badge--busy {
            background: #ede9fe;
            color: #5b21b6;
        }
        .tech-card__jobs {
            border-top: 1px solid #e2e8f0;
            padding-top: 0.5rem;
            margin-top: 0.5rem;
        }
        .tech-job-title {
            font-size: 0.72rem;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .tech-job-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            color: #475569;
            padding: 3px 0;
            border-bottom: 1px dotted #e2e8f0;
        }
        .tech-job-item:last-child {
            border-bottom: none;
        }
        .tech-job-item a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }
        .tech-job-item a:hover {
            text-decoration: underline;
        }

        /* Tabs */
        .dispatch-tabs {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 1.25rem;
        }
        .dispatch-tab-btn {
            padding: 10px 18px;
            border: none;
            background: none;
            font-size: 0.9rem;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }
        .dispatch-tab-btn:hover {
            color: #0f172a;
        }
        .dispatch-tab-btn--active {
            color: #7c3aed;
            border-bottom-color: #7c3aed;
        }
        .dispatch-tab-content {
            display: none;
        }
        .dispatch-tab-content--active {
            display: block;
        }

        /* Ticket card list */
        .dispatch-ticket-card {
            background: #fff;
            border-radius: 14px;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            border: 1px solid #e2e8f0;
            margin-bottom: 1rem;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 1.25rem;
            align-items: center;
        }
        @media (max-width: 768px) {
            .dispatch-ticket-card {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }
        .dispatch-ticket-info {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .dispatch-ticket-title {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            text-decoration: none;
            line-height: 1.4;
        }
        .dispatch-ticket-title:hover {
            color: #7c3aed;
        }
        .dispatch-ticket-desc {
            font-size: 0.85rem;
            color: #64748b;
            margin-bottom: 2px;
        }
        .dispatch-ticket-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            font-size: 0.78rem;
            color: #64748b;
        }
        .dispatch-form {
            display: flex;
            gap: 6px;
            align-items: center;
        }
        .dispatch-select {
            padding: 8px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.82rem;
            color: #0f172a;
            background: #fff;
            min-width: 150px;
            outline: none;
            transition: border-color 0.2s;
        }
        .dispatch-select:focus {
            border-color: #7c3aed;
        }
        .dispatch-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 700;
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            color: #fff;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(124, 58, 237, 0.15);
            transition: all 0.2s;
            white-space: nowrap;
        }
        .dispatch-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);
        }
    </style>
@endpush

@section('content')
<div class="tickets-page">

    {{-- Header --}}
    <div class="tickets-page__header">
        <div>
            <h1>Điều phối kỹ thuật</h1>
            <p class="tickets-page__subtitle">Điều phối nhân lực và quản lý các sự cố kỹ thuật của cư dân.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="tickets-alert tickets-alert--success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="tickets-alert tickets-alert--danger">{{ $errors->first() }}</div>
    @endif

    {{-- Stats Grid --}}
    <div class="tickets-stats-grid">
        <div class="tk-stat-card" style="border-left: 4px solid #f59e0b;">
            <span class="tk-stat-card__label">Chờ điều phối</span>
            <span class="tk-stat-card__value" style="color: #f59e0b;">{{ number_format(count($pendingTickets)) }}</span>
        </div>
        <div class="tk-stat-card" style="border-left: 4px solid #7c3aed;">
            <span class="tk-stat-card__label">Đang thực hiện</span>
            <span class="tk-stat-card__value" style="color: #7c3aed;">{{ number_format(count($activeTickets)) }}</span>
        </div>
        <div class="tk-stat-card" style="border-left: 4px solid #10b981;">
            <span class="tk-stat-card__label">KTV đang hoạt động</span>
            <span class="tk-stat-card__value" style="color: #10b981;">{{ number_format(count($technicians)) }}</span>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="dispatch-grid">

        {{-- Technicians Sidebar (Left) --}}
        <div class="tech-section">
            <h3 class="tech-section-title">Danh sách kỹ thuật viên</h3>
            
            @forelse($technicians as $tech)
                @php
                    $activeCount = $tech->active_tickets_count;
                @endphp
                <div class="tech-card">
                    <div class="tech-card__header">
                        <div>
                            <span class="tech-card__name">{{ $tech->name }}</span>
                            <span class="tech-card__phone">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                {{ $tech->phone ?? 'Không có SĐT' }}
                            </span>
                        </div>
                        <span class="tech-badge {{ $activeCount === 0 ? 'tech-badge--free' : 'tech-badge--busy' }}">
                            {{ $activeCount === 0 ? 'Sẵn sàng' : $activeCount . ' việc' }}
                        </span>
                    </div>

                    {{-- List active jobs of this tech --}}
                    @if($activeCount > 0)
                        <div class="tech-card__jobs">
                            <div class="tech-job-title">Công việc đang làm:</div>
                            @foreach($activeTickets->where('handler_id', $tech->id) as $job)
                                <div class="tech-job-item">
                                    <span>
                                        <strong>P.{{ $job->apartment->apartment_number ?? 'N/A' }}</strong> - 
                                        <span class="tk-priority tk-priority--{{ $job->priority }}" style="padding: 1px 6px; font-size: 0.65rem;">
                                            {{ $job->priorityLabel() }}
                                        </span>
                                    </span>
                                    <a href="{{ portal_route('tickets.show', $job->id) }}">Chi tiết</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p style="text-align: center; color: #94a3b8; padding: 20px 0;">Không có kỹ thuật viên hoạt động.</p>
            @endforelse
        </div>

        {{-- Issues / Dispatching Content Area (Right) --}}
        <div>
            {{-- Tabs --}}
            <div class="dispatch-tabs">
                <button type="button" id="tab-btn-pending" class="dispatch-tab-btn dispatch-tab-btn--active" onclick="switchTab('pending')">
                    Chờ điều phối ({{ count($pendingTickets) }})
                </button>
                <button type="button" id="tab-btn-active" class="dispatch-tab-btn" onclick="switchTab('active')">
                    Đang xử lý / Đã phân công ({{ count($activeTickets) }})
                </button>
            </div>

            {{-- Tab 1: Pending Tickets --}}
            <div id="tab-content-pending" class="dispatch-tab-content dispatch-tab-content--active">
                @forelse($pendingTickets as $ticket)
                    <div class="dispatch-ticket-card">
                        <div class="dispatch-ticket-info">
                            <a href="{{ portal_route('tickets.show', $ticket->id) }}" class="dispatch-ticket-title">
                                #{{ $ticket->id }} - {{ $ticket->title }}
                            </a>
                            <p class="dispatch-ticket-desc">{{ Str::limit($ticket->description, 120) }}</p>
                            <div class="dispatch-ticket-meta">
                                <span class="tk-priority tk-priority--{{ $ticket->priority }}">{{ $ticket->priorityLabel() }}</span>
                                <span>Căn hộ: <strong>{{ $ticket->apartment->apartment_number ?? 'N/A' }}</strong> ({{ $ticket->apartment->floor->block->name ?? '' }})</span>
                                <span>• Gửi bởi: {{ $ticket->sender->name ?? 'Cư dân' }}</span>
                                <span>• {{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        {{-- Dispatch actions --}}
                        <div>
                            <form method="POST" action="{{ portal_route('tickets.assign', $ticket->id) }}" class="dispatch-form">
                                @csrf
                                <select name="handler_id" required class="dispatch-select">
                                    <option value="" disabled selected>-- Giao cho KTV --</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}">{{ $tech->name }} ({{ $tech->active_tickets_count }} việc)</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="dispatch-btn">Phân công</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div style="background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 40px; text-align: center; color: #94a3b8;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 10px; color: #cbd5e1;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p style="font-weight: 600; font-size: 0.95rem; margin: 0;">Tuyệt vời! Không còn sự cố nào chờ điều phối.</p>
                    </div>
                @endforelse
            </div>

            {{-- Tab 2: Active Tickets --}}
            <div id="tab-content-active" class="dispatch-tab-content">
                @forelse($activeTickets as $ticket)
                    <div class="dispatch-ticket-card">
                        <div class="dispatch-ticket-info">
                            <a href="{{ portal_route('tickets.show', $ticket->id) }}" class="dispatch-ticket-title">
                                #{{ $ticket->id }} - {{ $ticket->title }}
                            </a>
                            <p class="dispatch-ticket-desc">{{ Str::limit($ticket->description, 120) }}</p>
                            <div class="dispatch-ticket-meta">
                                <span class="tk-priority tk-priority--{{ $ticket->priority }}">{{ $ticket->priorityLabel() }}</span>
                                <span class="tk-status tk-status--{{ $ticket->status }}">{{ $ticket->statusLabel() }}</span>
                                <span>Căn hộ: <strong>{{ $ticket->apartment->apartment_number ?? 'N/A' }}</strong></span>
                                <span>• Phụ trách: <strong>{{ $ticket->handler->name ?? 'Chưa phân công' }}</strong></span>
                                <span>• {{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        {{-- Re-dispatch/Change technician --}}
                        <div>
                            <form method="POST" action="{{ portal_route('tickets.assign', $ticket->id) }}" class="dispatch-form">
                                @csrf
                                <select name="handler_id" required class="dispatch-select">
                                    <option value="" disabled>-- Chuyển KTV --</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}" {{ $ticket->handler_id == $tech->id ? 'selected' : '' }}>
                                            {{ $tech->name }} ({{ $tech->active_tickets_count }} việc)
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="dispatch-btn" style="background: linear-gradient(135deg, #4f46e5, #3730a3);">Chuyển đổi</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div style="background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 40px; text-align: center; color: #94a3b8;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 10px; color: #cbd5e1;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p style="font-weight: 600; font-size: 0.95rem; margin: 0;">Hiện tại không có sự cố nào đang xử lý.</p>
                    </div>
                @endforelse
            </div>

        </div>

    </div>

</div>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.dispatch-tab-btn').forEach(btn => {
            btn.classList.remove('dispatch-tab-btn--active');
        });
        document.querySelectorAll('.dispatch-tab-content').forEach(content => {
            content.classList.remove('dispatch-tab-content--active');
        });
        document.getElementById('tab-btn-' + tabId).classList.add('dispatch-tab-btn--active');
        document.getElementById('tab-content-' + tabId).classList.add('dispatch-tab-content--active');
    }
</script>
@endsection
