@extends('layouts.admin.master')

@section('page_title', 'Quản lý Phản ánh')

@push('styles')
    @vite(['resources/css/pages/admin/tickets/index.css'])
@endpush

@section('content')
<div class="tickets-page">

    {{-- Header --}}
    <div class="tickets-page__header">
        <div>
            <h1>Quản lý Phản ánh</h1>
            <p class="tickets-page__subtitle">Tiếp nhận, điều phối và nghiệm thu phản ánh sự cố từ cư dân.</p>
        </div>
        @if(in_array(auth()->user()->role, ['admin', 'manager']))
        <button class="rpt-btn rpt-btn--ghost" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            In báo cáo
        </button>
        @endif
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="tickets-alert tickets-alert--success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="tickets-alert tickets-alert--danger">{{ $errors->first() }}</div>
    @endif

    {{-- Tabs --}}
    @if(in_array(auth()->user()->role, ['admin', 'manager']))
    <div class="tk-tabs">
        <button type="button" class="tk-tab-btn tk-tab-btn--active" data-tab="tickets">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            Tiếp nhận
            <span class="tk-tab-badge">{{ $stats['total'] }}</span>
        </button>
        <button type="button" class="tk-tab-btn" data-tab="dispatch">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
            Điều phối
            <span class="tk-tab-badge">{{ $dispatchStats['pending'] }}</span>
        </button>
        <button type="button" class="tk-tab-btn" data-tab="report">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
            Nghiệm thu
            <span class="tk-tab-badge">{{ $reportStats['pending'] }}</span>
        </button>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: TIẾP NHẬN PHẢN ÁNH --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="tk-tab-content tk-tab-content--active" id="tab-tickets">

        {{-- Stats --}}
        <div class="tickets-stats-grid mb-6">
            <div class="tk-stat-card border-l-4 border-slate-900">
                <span class="tk-stat-card__label">Tổng</span>
                <span class="tk-stat-card__value text-slate-900">{{ number_format($stats['total']) }}</span>
            </div>
            <div class="tk-stat-card border-l-4 border-amber-500">
                <span class="tk-stat-card__label">Chờ xử lý</span>
                <span class="tk-stat-card__value text-amber-500">{{ number_format($stats['pending']) }}</span>
            </div>
            <div class="tk-stat-card border-l-4 border-indigo-500">
                <span class="tk-stat-card__label">Đã phân công</span>
                <span class="tk-stat-card__value text-indigo-500">{{ number_format($stats['assigned']) }}</span>
            </div>
            <div class="tk-stat-card border-l-4 border-blue-600">
                <span class="tk-stat-card__label">Đang xử lý</span>
                <span class="tk-stat-card__value text-blue-600">{{ number_format($stats['in_progress']) }}</span>
            </div>
            <div class="tk-stat-card border-l-4 border-green-600">
                <span class="tk-stat-card__label">Hoàn thành</span>
                <span class="tk-stat-card__value text-green-600">{{ number_format($stats['completed']) }}</span>
            </div>
        </div>

        {{-- Filters --}}
        <div class="tickets-filter-card mb-6">
            <form id="ticket-filter-form">
                <div class="tickets-filter-grid">
                    <div>
                        <label>Tòa nhà</label>
                        <select name="block_id" id="filter-block">
                            <option value="">Tất cả tòa</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Tìm kiếm</label>
                        <input type="text" name="search" id="filter-search" value="{{ request('search') }}" placeholder="Tiêu đề, căn hộ...">
                    </div>
                    <div>
                        <label>Trạng thái</label>
                        <select name="status" id="filter-status">
                            <option value="">Tất cả</option>
                            <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Chờ xử lý</option>
                            <option value="assigned" {{ request('status')==='assigned'?'selected':'' }}>Đã phân công</option>
                            <option value="in_progress" {{ request('status')==='in_progress'?'selected':'' }}>Đang xử lý</option>
                            <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Hoàn thành</option>
                            <option value="cancelled" {{ request('status')==='cancelled'?'selected':'' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div>
                        <label>Ưu tiên</label>
                        <select name="priority" id="filter-priority">
                            <option value="">Tất cả</option>
                            <option value="urgent" {{ request('priority')==='urgent'?'selected':'' }}>Khẩn cấp</option>
                            <option value="high" {{ request('priority')==='high'?'selected':'' }}>Cao</option>
                            <option value="medium" {{ request('priority')==='medium'?'selected':'' }}>Trung bình</option>
                            <option value="low" {{ request('priority')==='low'?'selected':'' }}>Thấp</option>
                        </select>
                    </div>
                </div>
                <div class="mt-2.5 hidden" id="clear-filter-wrap">
                    <a href="#" id="clear-filter-btn" class="text-xs text-red-600 no-underline font-semibold">× Xóa bộ lọc</a>
                </div>
            </form>
        </div>

        {{-- Ticket Table Container (updated via AJAX) --}}
        <div id="ticket-table-container">
            @include('admin.tickets._ticket_table', ['tickets' => $tickets, 'blocks' => $blocks])
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: ĐIỀU PHỐI KỸ THUẬT --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if(in_array(auth()->user()->role, ['admin', 'manager']))
    <div class="tk-tab-content" id="tab-dispatch">

        <div class="tickets-stats-grid mb-6">
            <div class="tk-stat-card border-l-4 border-amber-500">
                <span class="tk-stat-card__label">Chờ điều phối</span>
                <span class="tk-stat-card__value text-amber-500">{{ $dispatchStats['pending'] }}</span>
            </div>
            <div class="tk-stat-card border-l-4 border-blue-600">
                <span class="tk-stat-card__label">Đang thực hiện</span>
                <span class="tk-stat-card__value text-blue-600">{{ $dispatchStats['active'] }}</span>
            </div>
            <div class="tk-stat-card border-l-4 border-green-600">
                <span class="tk-stat-card__label">KTV hoạt động</span>
                <span class="tk-stat-card__value text-green-600">{{ count($technicians) }}</span>
            </div>
        </div>

        {{-- Dispatch Filters --}}
        <div class="tickets-filter-card mb-6">
            <form action="{{ route('admin.tickets.index') }}" method="GET" id="dispatch-filter-form">
                <input type="hidden" name="tab" value="dispatch">
                <div class="tickets-filter-grid">
                    <div>
                        <label>Tìm kiếm</label>
                        <input type="text" name="dispatch_search" id="dispatch-filter-search" value="{{ request('dispatch_search') }}" placeholder="Tiêu đề, căn hộ...">
                    </div>
                    <div>
                        <label>Tòa nhà</label>
                        <select name="dispatch_block_id" id="dispatch-filter-block">
                            <option value="">Tất cả tòa</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" {{ request('dispatch_block_id') == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Ưu tiên</label>
                        <select name="dispatch_priority" id="dispatch-filter-priority">
                            <option value="">Tất cả</option>
                            <option value="urgent" {{ request('dispatch_priority')==='urgent'?'selected':'' }}>Khẩn cấp</option>
                            <option value="high" {{ request('dispatch_priority')==='high'?'selected':'' }}>Cao</option>
                            <option value="medium" {{ request('dispatch_priority')==='medium'?'selected':'' }}>Trung bình</option>
                            <option value="low" {{ request('dispatch_priority')==='low'?'selected':'' }}>Thấp</option>
                        </select>
                    </div>
                </div>
                @if(request()->filled('dispatch_block_id') || request()->filled('dispatch_priority') || request()->filled('dispatch_search'))
                    <div class="mt-2.5">
                        <a href="{{ route('admin.tickets.index', ['tab' => 'dispatch']) }}" class="text-xs text-red-600 no-underline font-semibold">× Xóa bộ lọc</a>
                    </div>
                @endif
            </form>
        </div>

        <div class="dispatch-grid">
            {{-- Technicians sidebar --}}
            <div class="tech-section">
                <h3 class="tech-section-title">Kỹ thuật viên</h3>
                @forelse($technicians as $tech)
                    @php $activeCount = $tech->active_tickets_count; @endphp
                    <div class="tech-card">
                        <div class="tech-card__header">
                            <div>
                                <span class="tech-card__name">{{ $tech->name }}</span>
                                <span class="tech-card__phone">{{ $tech->phone ?? 'Không có SĐT' }}</span>
                            </div>
                            <span class="tech-badge {{ $activeCount === 0 ? 'tech-badge--free' : 'tech-badge--busy' }}">
                                {{ $activeCount === 0 ? 'Sẵn sàng' : $activeCount . ' việc' }}
                            </span>
                        </div>
                        @if($activeCount > 0)
                        <div class="tech-card__jobs">
                            <div class="tech-job-title">Đang làm:</div>
                            @foreach($activeTickets->where('handler_id', $tech->id)->take(3) as $job)
                                <div class="tech-job-item">
                                    <span><strong>P.{{ $job->apartment->apartment_number ?? 'N/A' }}</strong></span>
                                    <a href="{{ route('admin.tickets.show', $job->id) }}">Chi tiết</a>
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                @empty
                    <p class="text-center text-slate-400 py-5">Không có KTV hoạt động.</p>
                @endforelse
            </div>

            {{-- Dispatch content --}}
            <div>
                <div class="rpt-section__header mb-4">
                    <span>Phản ánh chờ điều phối</span>
                    <span class="rpt-section__count">{{ count($pendingTickets) }}</span>
                </div>

                @forelse($pendingTickets as $ticket)
                    <div class="dispatch-ticket-card">
                        <div class="dispatch-ticket-info">
                            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="dispatch-ticket-title">
                                #{{ $ticket->id }} — {{ $ticket->title }}
                            </a>
                            <p class="dispatch-ticket-desc">{{ Str::limit($ticket->description, 100) }}</p>
                            <div class="dispatch-ticket-meta">
                                <span class="tk-priority tk-priority--{{ $ticket->priority }}">{{ $ticket->priorityLabel() }}</span>
                                <span>{{ $ticket->apartment->apartment_number ?? 'N/A' }} ({{ $ticket->apartment->floor->block->name ?? '' }})</span>
                                <span>• {{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.tickets.assign', $ticket->id) }}" class="dispatch-form">
                            @csrf
                            <select name="handler_id" required class="dispatch-select">
                                <option value="" disabled selected>-- Giao KTV --</option>
                                @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}">{{ $tech->name }} ({{ $tech->active_tickets_count }} việc)</option>
                                @endforeach
                            </select>
                            <button type="submit" class="dispatch-btn">Phân công</button>
                        </form>
                    </div>
                @empty
                    <div class="rpt-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p><strong>Tuyệt vời!</strong> Không còn sự cố nào chờ điều phối.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: NGHIỆM THU BÁO CÁO --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if(in_array(auth()->user()->role, ['admin', 'manager']))
    <div class="tk-tab-content" id="tab-report">

        <div class="tickets-stats-grid mb-6">
            <div class="tk-stat-card border-l-4 border-slate-900">
                <span class="tk-stat-card__label">Tổng báo cáo</span>
                <span class="tk-stat-card__value text-slate-900">{{ $reportStats['total'] }}</span>
            </div>
            <div class="tk-stat-card border-l-4 border-amber-500">
                <span class="tk-stat-card__label">Chờ nghiệm thu</span>
                <span class="tk-stat-card__value text-amber-500">{{ $reportStats['pending'] }}</span>
            </div>
            <div class="tk-stat-card border-l-4 border-green-600">
                <span class="tk-stat-card__label">Đã nghiệm thu</span>
                <span class="tk-stat-card__value text-green-600">{{ $reportStats['approved'] }}</span>
            </div>
            <div class="tk-stat-card border-l-4 border-red-600">
                <span class="tk-stat-card__label">Yêu cầu làm lại</span>
                <span class="tk-stat-card__value text-red-600">{{ $reportStats['rework'] }}</span>
            </div>
        </div>

        {{-- Report Filters --}}
        <div class="tickets-filter-card mb-6">
            <form action="{{ route('admin.tickets.index') }}" method="GET" id="report-filter-form">
                <input type="hidden" name="tab" value="report">
                <div class="tickets-filter-grid">
                    <div>
                        <label>Tìm kiếm</label>
                        <input type="text" name="report_search" id="report-filter-search" value="{{ request('report_search') }}" placeholder="Tiêu đề, căn hộ...">
                    </div>
                    <div>
                        <label>Tòa nhà</label>
                        <select name="report_block_id" id="report-filter-block">
                            <option value="">Tất cả tòa</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" {{ request('report_block_id') == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label>Kỹ thuật viên</label>
                        <select name="report_technician_id" id="report-filter-technician">
                            <option value="">Tất cả KTV</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ request('report_technician_id') == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if(request()->filled('report_block_id') || request()->filled('report_technician_id') || request()->filled('report_search'))
                    <div class="mt-2.5">
                        <a href="{{ route('admin.tickets.index', ['tab' => 'report']) }}" class="text-xs text-red-600 no-underline font-semibold">× Xóa bộ lọc</a>
                    </div>
                @endif
            </form>
        </div>

        {{-- Pending Review Cards --}}
        <div class="rpt-section mb-6">
            <div class="rpt-section__header">
                <span>Chờ nghiệm thu</span>
                <span class="rpt-section__count">{{ $pendingReview->count() }}</span>
            </div>

            @forelse($pendingReview as $ticket)
                @php
                    $lastProgress = $ticket->progress->last();
                    $reportText = $lastProgress?->comment ?? 'Không có báo cáo chi tiết.';
                    $proofImage = $lastProgress?->image_proof ? asset('storage/' . $lastProgress->image_proof) : null;
                @endphp
                <div class="rpt-card rpt-card--pending">
                    <div class="rpt-card__left">
                        <div class="rpt-card__head">
                            <span class="rpt-card__id">#{{ $ticket->id }}</span>
                            @if($ticket->priority === 'urgent')
                                <span class="rpt-badge rpt-badge--urgent">Khẩn cấp</span>
                            @elseif($ticket->priority === 'high')
                                <span class="rpt-badge rpt-badge--high">Cao</span>
                            @endif
                        </div>
                        <h3 class="rpt-card__title">{{ $ticket->title }}</h3>
                        <div class="rpt-card__meta">
                            <span>{{ $ticket->apartment?->floor?->block?->name ?? '' }} · Căn {{ $ticket->apartment?->apartment_number ?? 'N/A' }}</span>
                            <span>KTV: {{ $ticket->handler?->name ?? 'N/A' }}</span>
                        </div>
                        <div class="rpt-card__report">
                            <p class="rpt-card__report-label">Báo cáo KTV</p>
                            <p class="rpt-card__report-text">{{ Str::limit($reportText, 200) }}</p>
                        </div>
                        @if($proofImage)
                        <div class="rpt-card__images">
                            <div class="rpt-card__image-group">
                                <p class="rpt-card__image-label">Ảnh nghiệm thu</p>
                                <img src="{{ $proofImage }}" class="rpt-card__thumb" onclick="openLightbox(this.src)">
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="rpt-card__right">
                        <button class="rpt-btn rpt-btn--success rpt-btn--full" data-approve-ticket="{{ $ticket->id }}">Xác nhận đạt</button>
                        <button class="rpt-btn rpt-btn--danger rpt-btn--full" data-reject-ticket="{{ $ticket->id }}">Yêu cầu làm lại</button>
                        <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="rpt-btn rpt-btn--ghost rpt-btn--full">Xem chi tiết</a>
                    </div>
                </div>
            @empty
                <div class="rpt-empty"><p>Chưa có báo cáo nào chờ nghiệm thu.</p></div>
            @endforelse
        </div>

        {{-- Approved Table --}}
        <div class="rpt-section mb-6">
            <div class="rpt-section__header">
                <span>Đã nghiệm thu</span>
                <span class="rpt-section__count">{{ $approvedReports->count() }}</span>
            </div>
            <div class="rpt-table-wrap">
                <table class="rpt-table">
                    <thead><tr><th>Mã</th><th>Tiêu đề</th><th>Căn hộ</th><th>KTV</th><th>Ngày</th><th></th></tr></thead>
                    <tbody>
                        @forelse($approvedReports as $ticket)
                        <tr>
                            <td><strong>#{{ $ticket->id }}</strong></td>
                            <td>{{ Str::limit($ticket->title, 40) }}</td>
                            <td>{{ $ticket->apartment?->apartment_number ?? 'N/A' }}</td>
                            <td>{{ $ticket->handler?->name ?? 'N/A' }}</td>
                            <td>{{ $ticket->updated_at->format('d/m/Y') }}</td>
                            <td><a href="{{ route('admin.tickets.show', $ticket->id) }}" class="rpt-btn rpt-btn--ghost rpt-btn--sm">Chi tiết</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="rpt-table__empty">Chưa có báo cáo nào.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Rework Table --}}
        @if($reworkReports->count() > 0)
        <div class="rpt-section">
            <div class="rpt-section__header">
                <span>Yêu cầu làm lại</span>
                <span class="rpt-section__count">{{ $reworkReports->count() }}</span>
            </div>
            <div class="rpt-table-wrap">
                <table class="rpt-table">
                    <thead><tr><th>Mã</th><th>Tiêu đề</th><th>KTV</th><th>Lần</th><th></th></tr></thead>
                    <tbody>
                        @foreach($reworkReports as $ticket)
                        <tr>
                            <td><strong>#{{ $ticket->id }}</strong></td>
                            <td>{{ Str::limit($ticket->title, 40) }}</td>
                            <td>{{ $ticket->handler?->name ?? 'N/A' }}</td>
                            <td>{{ $ticket->reopened_count ?? 1 }}</td>
                            <td><a href="{{ route('admin.tickets.show', $ticket->id) }}" class="rpt-btn rpt-btn--ghost rpt-btn--sm">Chi tiết</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    @endif

</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- SLIDE PANEL (Ticket Quick View) --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<div class="tk-panel-overlay" id="tkOverlay" onclick="closePanel()"></div>
<div class="tk-panel" id="tkPanel">
    <div class="tk-panel__header">
        <div class="tk-panel__header-left">
            <span class="tk-priority-dot" id="panelDot"></span>
            <div>
                <p class="tk-panel__eyebrow" id="panelEyebrow"></p>
                <h2 class="tk-panel__title" id="panelTitle"></h2>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a id="panelDetailLink" href="#" class="tk-panel__detail-btn" target="_blank">Chi tiết ↗</a>
            <button class="tk-panel__close" onclick="closePanel()">×</button>
        </div>
    </div>
    <div class="tk-panel__body">
        <div class="tk-panel__overdue hidden" id="panelOverdueWarn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span>Ticket này đã vượt SLA — cần xử lý ngay!</span>
        </div>
        <div class="tk-panel__section">
            <div class="tk-panel__info-grid">
                <div><span class="tk-panel__lbl">Căn hộ</span><span class="tk-panel__val" id="panelApartment"></span></div>
                <div><span class="tk-panel__lbl">Tòa nhà</span><span class="tk-panel__val" id="panelBlock"></span></div>
                <div><span class="tk-panel__lbl">Người gửi</span><span class="tk-panel__val" id="panelSender"></span></div>
                <div><span class="tk-panel__lbl">Gửi lúc</span><span class="tk-panel__val" id="panelCreated"></span></div>
            </div>
            <p class="tk-panel__desc" id="panelDesc"></p>
        </div>
        <div class="tk-panel__section hidden" id="panelAssignSection">
            <p class="tk-panel__section-title">Phân công kỹ thuật viên</p>
            <form id="panelAssignForm" method="POST">
                @csrf
                <div class="flex items-center gap-2">
                    <select name="handler_id" id="panelTechSelect" class="tk-panel__select" required>
                        <option value="" disabled selected>-- Chọn KTV --</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="tk-panel__btn tk-panel__btn--primary">Phân công</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Approve/Reject Modals --}}
<div class="rpt-modal-overlay" id="approveOverlay">
    <div class="rpt-modal">
        <div class="rpt-modal__icon rpt-modal__icon--green">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="rpt-modal__title">Xác nhận nghiệm thu đạt?</h3>
        <p class="rpt-modal__desc">Sự cố sẽ được đánh dấu hoàn tất và thông báo cho cư dân.</p>
        <div class="rpt-modal__actions">
            <button class="rpt-btn rpt-btn--ghost" onclick="closeModals()">Hủy</button>
            <button class="rpt-btn rpt-btn--success" onclick="submitApprove()">Xác nhận</button>
        </div>
    </div>
</div>
<div class="rpt-modal-overlay" id="rejectOverlay">
    <div class="rpt-modal">
        <div class="rpt-modal__icon rpt-modal__icon--red">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        </div>
        <h3 class="rpt-modal__title">Yêu cầu làm lại</h3>
        <p class="rpt-modal__desc">Nhập lý do để KTV biết cần sửa gì.</p>
        <div class="rpt-modal__field">
            <label class="rpt-modal__label">Lý do <span class="text-red-500">*</span></label>
            <textarea id="rejectReason" class="rpt-modal__textarea" placeholder="VD: Vẫn còn rò rỉ..." rows="3"></textarea>
            <p class="rpt-modal__error hidden" id="rejectError">Vui lòng nhập lý do.</p>
        </div>
        <div class="rpt-modal__actions">
            <button class="rpt-btn rpt-btn--ghost" onclick="closeModals()">Hủy</button>
            <button class="rpt-btn rpt-btn--danger" onclick="submitReject()">Gửi yêu cầu</button>
        </div>
    </div>
</div>

{{-- Lightbox --}}
<div class="rpt-lightbox" id="lightbox" onclick="closeLightbox()">
    <div class="rpt-lightbox__inner" onclick="event.stopPropagation()">
        <div class="rpt-lightbox__header">
            <span>Ảnh nghiệm thu</span>
            <button class="rpt-lightbox__close" onclick="closeLightbox()">×</button>
        </div>
        <img id="lightboxImg" src="" alt="">
    </div>
</div>

<script>
const CSRF = '{{ csrf_token() }}';
const TICKET_INDEX_URL = '{{ route("admin.tickets.index") }}';

// ── AJAX Filter ───────────────────────────────────────────────────────
let filterTimeout = null;

function getFilterParams() {
    const params = new URLSearchParams();
    const blockId = document.getElementById('filter-block').value;
    const search = document.getElementById('filter-search').value.trim();
    const status = document.getElementById('filter-status').value;
    const priority = document.getElementById('filter-priority').value;

    if (blockId) params.set('block_id', blockId);
    if (search) params.set('search', search);
    if (status) params.set('status', status);
    if (priority) params.set('priority', priority);

    return params;
}

function hasActiveFilters() {
    return document.getElementById('filter-block').value ||
           document.getElementById('filter-search').value.trim() ||
           document.getElementById('filter-status').value ||
           document.getElementById('filter-priority').value;
}

function toggleClearButton() {
    document.getElementById('clear-filter-wrap').classList.toggle('hidden', !hasActiveFilters());
}

async function fetchTickets() {
    const params = getFilterParams();
    const container = document.getElementById('ticket-table-container');

    // Show loading state
    container.style.opacity = '0.5';
    container.style.pointerEvents = 'none';

    try {
        const url = TICKET_INDEX_URL + (params.toString() ? '?' + params.toString() : '');
        const res = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.success) {
            container.innerHTML = data.html;
            // Update stats
            if (data.stats) {
                updateStats(data.stats);
            }
            // Re-bind row click events for slide panel
            bindRowClicks();
        }
    } catch (err) {
        showToast('❌ Lỗi tải dữ liệu', 'error');
    } finally {
        container.style.opacity = '1';
        container.style.pointerEvents = '';
    }

    toggleClearButton();
    // Update URL without reload
    const newUrl = TICKET_INDEX_URL + (params.toString() ? '?' + params.toString() : '');
    window.history.replaceState({}, '', newUrl);
}

function updateStats(stats) {
    const statCards = document.querySelectorAll('#tab-tickets .tickets-stats-grid .tk-stat-card__value');
    if (statCards.length >= 5) {
        statCards[0].textContent = Number(stats.total).toLocaleString();
        statCards[1].textContent = Number(stats.pending).toLocaleString();
        statCards[2].textContent = Number(stats.assigned).toLocaleString();
        statCards[3].textContent = Number(stats.in_progress).toLocaleString();
        statCards[4].textContent = Number(stats.completed).toLocaleString();
    }
}

// Select filters: immediate fetch
document.getElementById('filter-block').addEventListener('change', fetchTickets);
document.getElementById('filter-status').addEventListener('change', fetchTickets);
document.getElementById('filter-priority').addEventListener('change', fetchTickets);

// Search: debounce 400ms
document.getElementById('filter-search').addEventListener('input', function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(fetchTickets, 400);
});

// Clear filters
document.getElementById('clear-filter-btn').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('filter-block').value = '';
    document.getElementById('filter-search').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-priority').value = '';
    fetchTickets();
});

// Show/hide clear button on load
toggleClearButton();

// ── Pagination via AJAX ───────────────────────────────────────────────
document.getElementById('ticket-table-container').addEventListener('click', async function(e) {
    const link = e.target.closest('.tickets-pagination a');
    if (!link) return;
    e.preventDefault();

    const url = new URL(link.href);
    url.searchParams.set('_', ''); // force param to ensure URL change
    url.searchParams.delete('_');

    const container = document.getElementById('ticket-table-container');
    container.style.opacity = '0.5';
    container.style.pointerEvents = 'none';

    try {
        const res = await fetch(url.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            container.innerHTML = data.html;
            if (data.stats) updateStats(data.stats);
            bindRowClicks();
        }
    } catch (err) {
        showToast('❌ Lỗi tải dữ liệu', 'error');
    } finally {
        container.style.opacity = '1';
        container.style.pointerEvents = '';
    }

    window.history.replaceState({}, '', url.toString());
});

// ── Tab Switching ─────────────────────────────────────────────────────
document.querySelectorAll('.tk-tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tk-tab-btn').forEach(b => b.classList.remove('tk-tab-btn--active'));
        document.querySelectorAll('.tk-tab-content').forEach(c => c.classList.remove('tk-tab-content--active'));
        btn.classList.add('tk-tab-btn--active');
        document.getElementById('tab-' + btn.dataset.tab).classList.add('tk-tab-content--active');
    });
});

// Restore active tab from URL parameter on load
const urlParams = new URLSearchParams(window.location.search);
const activeTab = urlParams.get('tab');
if (activeTab) {
    const tabBtn = document.querySelector(`.tk-tab-btn[data-tab="${activeTab}"]`);
    if (tabBtn) {
        tabBtn.click();
    }
}

// Auto-submit Dispatch filters on change
const dispatchForm = document.getElementById('dispatch-filter-form');
if (dispatchForm) {
    const dBlock = document.getElementById('dispatch-filter-block');
    const dPriority = document.getElementById('dispatch-filter-priority');
    const dSearch = document.getElementById('dispatch-filter-search');

    if (dBlock) dBlock.addEventListener('change', () => dispatchForm.submit());
    if (dPriority) dPriority.addEventListener('change', () => dispatchForm.submit());
    
    let dispatchSearchTimeout = null;
    if (dSearch) {
        dSearch.addEventListener('input', () => {
            clearTimeout(dispatchSearchTimeout);
            dispatchSearchTimeout = setTimeout(() => dispatchForm.submit(), 600);
        });
    }
}

// Auto-submit Report filters on change
const reportForm = document.getElementById('report-filter-form');
if (reportForm) {
    const rBlock = document.getElementById('report-filter-block');
    const rTech = document.getElementById('report-filter-technician');
    const rSearch = document.getElementById('report-filter-search');

    if (rBlock) rBlock.addEventListener('change', () => reportForm.submit());
    if (rTech) rTech.addEventListener('change', () => reportForm.submit());
    
    let reportSearchTimeout = null;
    if (rSearch) {
        rSearch.addEventListener('input', () => {
            clearTimeout(reportSearchTimeout);
            reportSearchTimeout = setTimeout(() => reportForm.submit(), 600);
        });
    }
}

// ── Slide Panel ───────────────────────────────────────────────────────
function bindRowClicks() {
    document.querySelectorAll('.tk-row').forEach(row => {
        row.addEventListener('click', () => openPanel(row.dataset));
    });
}
bindRowClicks();

function openPanel(d) {
    document.getElementById('panelDot').className = 'tk-priority-dot tk-priority-dot--' + d.priority;
    document.getElementById('panelEyebrow').textContent = d.priorityLabel + ' · ' + d.statusLabel;
    document.getElementById('panelTitle').textContent = d.title;
    document.getElementById('panelDetailLink').href = d.detailUrl;
    document.getElementById('panelApartment').textContent = d.apartment + (d.floor ? ' · Tầng ' + d.floor : '');
    document.getElementById('panelBlock').textContent = 'Tòa ' + d.block;
    document.getElementById('panelSender').textContent = d.sender;
    document.getElementById('panelCreated').textContent = d.createdFull + ' (' + d.created + ')';
    document.getElementById('panelDesc').textContent = d.desc;
    document.getElementById('panelOverdueWarn').classList.toggle('hidden', d.overdue !== '1');

    const assignSec = document.getElementById('panelAssignSection');
    assignSec.classList.toggle('hidden', d.canAssign !== '1');
    if (d.canAssign === '1') {
        document.getElementById('panelAssignForm').dataset.url = d.assignUrl;
        document.getElementById('panelTechSelect').value = d.handlerId || '';
    }

    document.getElementById('tkPanel').classList.add('tk-panel--open');
    document.getElementById('tkOverlay').classList.add('tk-panel-overlay--visible');
    document.body.style.overflow = 'hidden';
}

function closePanel() {
    document.getElementById('tkPanel').classList.remove('tk-panel--open');
    document.getElementById('tkOverlay').classList.remove('tk-panel-overlay--visible');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closePanel(); closeModals(); closeLightbox(); } });

// ── Panel Assign (AJAX) ──────────────────────────────────────────────
document.getElementById('panelAssignForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const url = this.dataset.url;
    const handlerId = document.getElementById('panelTechSelect').value;
    if (!handlerId) return;
    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true; btn.textContent = 'Đang xử lý...';
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ handler_id: handlerId }),
        });
        const data = await res.json();
        if (data.success) { showToast('✅ ' + data.message); setTimeout(() => location.reload(), 800); }
        else { showToast('❌ ' + (data.message || 'Lỗi'), 'error'); }
    } catch { showToast('❌ Lỗi kết nối', 'error'); }
    finally { btn.disabled = false; btn.textContent = 'Phân công'; }
});

// ── Report: Approve / Reject ─────────────────────────────────────────
let pendingId = null;
document.querySelectorAll('[data-approve-ticket]').forEach(el => {
    el.addEventListener('click', () => { pendingId = el.dataset.approveTicket; document.getElementById('approveOverlay').classList.add('rpt-modal-overlay--visible'); });
});
document.querySelectorAll('[data-reject-ticket]').forEach(el => {
    el.addEventListener('click', () => { pendingId = el.dataset.rejectTicket; document.getElementById('rejectReason').value = ''; document.getElementById('rejectError').classList.add('hidden'); document.getElementById('rejectOverlay').classList.add('rpt-modal-overlay--visible'); });
});

function closeModals() {
    document.getElementById('approveOverlay').classList.remove('rpt-modal-overlay--visible');
    document.getElementById('rejectOverlay').classList.remove('rpt-modal-overlay--visible');
    pendingId = null;
}

async function submitApprove() {
    if (!pendingId) return closeModals();
    try {
        const res = await fetch(`/admin/tickets/${pendingId}/review/approve`, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: '{}',
        });
        const data = await res.json();
        if (data.success) { showToast('✅ Đã nghiệm thu thành công'); setTimeout(() => location.reload(), 800); }
        else { showToast('❌ ' + (data.message || 'Lỗi'), 'error'); closeModals(); }
    } catch { showToast('❌ Lỗi kết nối', 'error'); closeModals(); }
}

async function submitReject() {
    const reason = document.getElementById('rejectReason').value.trim();
    if (!reason) { document.getElementById('rejectError').classList.remove('hidden'); return; }
    try {
        const res = await fetch(`/admin/tickets/${pendingId}/review/reject`, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ reject_reason: reason }),
        });
        const data = await res.json();
        if (data.success) { showToast('✅ Đã gửi yêu cầu làm lại'); setTimeout(() => location.reload(), 800); }
        else { showToast('❌ ' + (data.message || 'Lỗi'), 'error'); closeModals(); }
    } catch { showToast('❌ Lỗi kết nối', 'error'); closeModals(); }
}

// ── Lightbox ─────────────────────────────────────────────────────────
function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').classList.add('rpt-lightbox--visible');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('rpt-lightbox--visible');
    document.body.style.overflow = '';
}

// ── Toast ─────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = 'tk-toast tk-toast--' + type;
    t.textContent = msg;
    document.body.appendChild(t);
    requestAnimationFrame(() => t.classList.add('tk-toast--show'));
    setTimeout(() => { t.classList.remove('tk-toast--show'); setTimeout(() => t.remove(), 300); }, 3000);
}
</script>
@endsection
