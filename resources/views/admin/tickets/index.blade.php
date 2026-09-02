@extends('layouts.admin.master')

@section('page_title', 'Danh sách Phản ánh & Kiến nghị')
@section('page_kicker', 'Dịch vụ cư dân')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', auth()->user()->role)

@push('styles')
    @vite(['resources/css/pages/admin/tickets/index.css'])
@endpush

@section('content')
<div class="tickets-page">

    {{-- Page Header --}}
    <div class="tickets-page__header">
        <div>
            <h1 class="tickets-page__title">Danh sách Phản ánh & Kiến nghị</h1>
            <p class="tickets-page__subtitle">Quản lý và theo dõi tiến độ xử lý yêu cầu từ cư dân.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="tickets-alert tickets-alert--success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="tickets-alert tickets-alert--danger">{{ $errors->first() }}</div>
    @endif

    {{-- 4 Stat Cards Top --}}
    <div class="tickets-stats-grid">
        {{-- Card 1: Tổng số yêu cầu --}}
        <div class="tk-stat-card">
            <div class="tk-stat-card__content">
                <span class="tk-stat-card__label">Tổng số yêu cầu</span>
                <span class="tk-stat-card__value tk-stat-card__value--dark">{{ number_format($stats['total']) }}</span>
            </div>
            <div class="tk-stat-card__icon tk-stat-card__icon--blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>

        {{-- Card 2: Đang xử lý --}}
        <div class="tk-stat-card">
            <div class="tk-stat-card__content">
                <span class="tk-stat-card__label">Đang xử lý</span>
                <span class="tk-stat-card__value tk-stat-card__value--blue">{{ number_format($stats['in_progress'] + $stats['assigned']) }}</span>
            </div>
            <div class="tk-stat-card__icon tk-stat-card__icon--blue-light">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
        </div>

        {{-- Card 3: Chờ tiếp nhận --}}
        <div class="tk-stat-card">
            <div class="tk-stat-card__content">
                <span class="tk-stat-card__label">Chờ tiếp nhận</span>
                <span class="tk-stat-card__value tk-stat-card__value--red">{{ number_format($stats['pending']) }}</span>
            </div>
            <div class="tk-stat-card__icon tk-stat-card__icon--red-light">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>

        {{-- Card 4: Hoàn thành --}}
        <div class="tk-stat-card">
            <div class="tk-stat-card__content">
                <span class="tk-stat-card__label">Hoàn thành</span>
                <span class="tk-stat-card__value tk-stat-card__value--green">{{ number_format($stats['completed']) }}</span>
            </div>
            <div class="tk-stat-card__icon tk-stat-card__icon--green-light">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="tickets-filter-card">
        <form method="GET" id="ticket-filter-form" class="tickets-filter-form">
            {{-- Tìm kiếm --}}
            <div class="tk-search-box">
                <svg class="tk-search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm tiêu đề, mã số, căn hộ..." class="tk-filter-input" onchange="this.form.submit()">
            </div>

            {{-- Tòa nhà --}}
            <div class="tk-select-wrapper">
                <select name="block_id" class="tk-filter-select" onchange="this.form.submit()">
                    <option value="">Tất cả tòa</option>
                    @foreach($blocks as $block)
                        <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Trạng thái --}}
            <div class="tk-select-wrapper">
                <select name="status" class="tk-filter-select" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending"     {{ request('status')==='pending'     ?'selected':'' }}>Chờ tiếp nhận</option>
                    <option value="assigned"    {{ request('status')==='assigned'    ?'selected':'' }}>Đã phân công</option>
                    <option value="in_progress" {{ request('status')==='in_progress' ?'selected':'' }}>Đang xử lý</option>
                    <option value="completed"   {{ request('status')==='completed'   ?'selected':'' }}>Hoàn thành</option>
                    <option value="cancelled"   {{ request('status')==='cancelled'   ?'selected':'' }}>Đã hủy</option>
                </select>
            </div>

            {{-- Ưu tiên --}}
            <div class="tk-select-wrapper">
                <select name="priority" class="tk-filter-select" onchange="this.form.submit()">
                    <option value="">Mức độ ưu tiên</option>
                    <option value="urgent" {{ request('priority')==='urgent' ?'selected':'' }}>Khẩn cấp</option>
                    <option value="high"   {{ request('priority')==='high'   ?'selected':'' }}>Cao</option>
                    <option value="medium" {{ request('priority')==='medium' ?'selected':'' }}>Bình thường</option>
                    <option value="low"    {{ request('priority')==='low'    ?'selected':'' }}>Thấp</option>
                </select>
            </div>

            {{-- Loại --}}
            <div class="tk-select-wrapper">
                <select name="ticket_type" class="tk-filter-select" onchange="this.form.submit()">
                    <option value="">Loại phản ánh</option>
                    <option value="complaint" {{ request('ticket_type')==='complaint' ?'selected':'' }}>Phản ánh sự cố</option>
                    <option value="report"    {{ request('ticket_type')==='report'    ?'selected':'' }}>Tố cáo</option>
                </select>
            </div>

            {{-- Ngày gửi --}}
            <div class="tk-date-wrapper">
                <input type="date" name="date" value="{{ request('date') }}" class="tk-filter-date" onchange="this.form.submit()">
            </div>

            {{-- Nút Lọc --}}
            <button type="submit" class="tk-filter-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Lọc
            </button>

            @if(request()->hasAny(['block_id','search','status','priority','ticket_type','date']))
                <a href="{{ portal_route('tickets.index') }}" class="tk-filter-reset">× Xóa bộ lọc</a>
            @endif
        </form>
    </div>

    {{-- Main Data Table --}}
    <div class="tickets-table-card">
        <div class="table-responsive">
            <table class="tickets-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>PHÒNG</th>
                        <th>HẠNG MỤC</th>
                        <th>MỨC ĐỘ</th>
                        <th>TRẠNG THÁI</th>
                        <th>NGÀY GỬI</th>
                        <th style="text-align: right;">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        @php
                            $ageHours = $ticket->created_at->diffInHours(now());
                            $slaOver  = match($ticket->priority) {
                                'urgent' => $ageHours >= 2,
                                'high'   => $ageHours >= 8,
                                'medium' => $ageHours >= 24,
                                'low'    => $ageHours >= 72,
                                default  => false,
                            };
                            $isActive = !in_array($ticket->status, ['completed','cancelled']);
                            $overdue  = $slaOver && $isActive;
                            $blockName = $ticket->apartment?->floor?->block?->name ?? '';
                        @endphp
                        <tr class="tk-row tk-desktop-only {{ $overdue ? 'tk-row--overdue' : '' }}" data-id="{{ $ticket->id }}"
                            data-title="{{ $ticket->title }}"
                            data-desc="{{ $ticket->description }}"
                            data-status="{{ $ticket->status }}"
                            data-status-label="{{ $ticket->statusLabel() }}"
                            data-priority="{{ $ticket->priority }}"
                            data-priority-label="{{ $ticket->priorityLabel() }}"
                            data-apartment="{{ $ticket->apartment->apartment_number ?? 'N/A' }}"
                            data-block="{{ $blockName }}"
                            data-floor="{{ $ticket->apartment?->floor?->floor_number ?? '' }}"
                            data-sender="{{ $ticket->sender->name ?? 'N/A' }}"
                            data-handler="{{ $ticket->handler->name ?? '' }}"
                            data-handler-id="{{ $ticket->handler_id ?? '' }}"
                            data-created="{{ $ticket->created_at->diffForHumans() }}"
                            data-created-full="{{ $ticket->created_at->format('d/m/Y H:i') }}"
                            data-assign-url="{{ portal_route('tickets.assign', $ticket->id) }}"
                            data-progress-url="{{ portal_route('tickets.update-progress', $ticket->id) }}"
                            data-detail-url="{{ portal_route('tickets.show', $ticket->id) }}"
                            data-can-assign="{{ in_array($ticket->status, ['pending','assigned']) && in_array(auth()->user()->role, ['admin','manager']) ? '1' : '0' }}"
                            data-can-progress="{{ in_array($ticket->status, ['assigned','in_progress']) ? '1' : '0' }}"
                            data-overdue="{{ $overdue ? '1' : '0' }}"
                            data-ticket-type="{{ $ticket->ticket_type }}"
                            data-reported-person="{{ $ticket->reported_person ?? '' }}"
                        >
                            {{-- ID --}}
                            <td class="tk-cell-id">
                                <a href="{{ portal_route('tickets.show', $ticket->id) }}">
                                    #REQ-{{ sprintf('%04d', $ticket->id) }}
                                </a>
                            </td>

                            {{-- Phòng / Căn hộ --}}
                            <td class="tk-cell-room">
                                @if($ticket->apartment)
                                    {{ $blockName ? $blockName . '-' : '' }}{{ $ticket->apartment->apartment_number }}
                                @else
                                    N/A
                                @endif
                            </td>

                            {{-- Hạng mục / Tiêu đề --}}
                            <td class="tk-cell-title">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    @if($ticket->ticket_type === 'report')
                                        <span class="tk-badge-report">Tố cáo</span>
                                    @endif
                                    <span>{{ $ticket->title }}</span>
                                </div>
                            </td>

                            {{-- Mức độ --}}
                            <td>
                                @if($ticket->priority === 'urgent')
                                    <span class="tk-pill tk-pill--urgent">Khẩn cấp</span>
                                @elseif($ticket->priority === 'high')
                                    <span class="tk-pill tk-pill--high">Cao</span>
                                @elseif($ticket->priority === 'medium')
                                    <span class="tk-pill tk-pill--medium">Bình thường</span>
                                @else
                                    <span class="tk-pill tk-pill--low">Thấp</span>
                                @endif
                            </td>

                            {{-- Trạng thái --}}
                            <td>
                                @if($ticket->status === 'pending')
                                    <span class="tk-pill tk-pill--pending">Chờ tiếp nhận</span>
                                @elseif($ticket->status === 'assigned')
                                    <span class="tk-pill tk-pill--assigned">Chờ xử lý</span>
                                @elseif($ticket->status === 'in_progress')
                                    <span class="tk-pill tk-pill--in-progress">Đang xử lý</span>
                                @elseif($ticket->status === 'completed')
                                    <span class="tk-pill tk-pill--completed">Hoàn thành</span>
                                @elseif($ticket->status === 'cancelled')
                                    <span class="tk-pill tk-pill--cancelled">Đã hủy</span>
                                @else
                                    <span class="tk-pill tk-pill--pending">{{ $ticket->statusLabel() }}</span>
                                @endif
                            </td>

                            {{-- Ngày gửi --}}
                            <td class="tk-cell-date">
                                {{ $ticket->created_at->format('d/m/Y H:i') }}
                            </td>

                            {{-- Thao tác --}}
                            <td class="tk-cell-actions">
                                <a href="{{ portal_route('tickets.show', $ticket->id) }}" class="tk-link-detail">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                        
                        {{-- GIAO DIỆN MỚI CHO MOBILE --}}
                        <tr class="tk-mobile-only tk-row" data-id="{{ $ticket->id }}"
                            data-title="{{ $ticket->title }}"
                            data-desc="{{ $ticket->description }}"
                            data-status="{{ $ticket->status }}"
                            data-status-label="{{ $ticket->statusLabel() }}"
                            data-priority="{{ $ticket->priority }}"
                            data-priority-label="{{ $ticket->priorityLabel() }}"
                            data-apartment="{{ $ticket->apartment->apartment_number ?? 'N/A' }}"
                            data-block="{{ $blockName }}"
                            data-floor="{{ $ticket->apartment?->floor?->floor_number ?? '' }}"
                            data-sender="{{ $ticket->sender->name ?? 'N/A' }}"
                            data-handler="{{ $ticket->handler->name ?? '' }}"
                            data-handler-id="{{ $ticket->handler_id ?? '' }}"
                            data-created="{{ $ticket->created_at->diffForHumans() }}"
                            data-created-full="{{ $ticket->created_at->format('d/m/Y H:i') }}"
                            data-assign-url="{{ portal_route('tickets.assign', $ticket->id) }}"
                            data-progress-url="{{ portal_route('tickets.update-progress', $ticket->id) }}"
                            data-detail-url="{{ portal_route('tickets.show', $ticket->id) }}"
                            data-can-assign="{{ in_array($ticket->status, ['pending','assigned']) && in_array(auth()->user()->role, ['admin','manager']) ? '1' : '0' }}"
                            data-can-progress="{{ in_array($ticket->status, ['assigned','in_progress']) ? '1' : '0' }}"
                            data-overdue="{{ $overdue ? '1' : '0' }}"
                            data-ticket-type="{{ $ticket->ticket_type }}"
                            data-reported-person="{{ $ticket->reported_person ?? '' }}">
                            <td colspan="7" style="padding: 0; border: none; background: transparent;">
                                <div class="tk-mb-card">
                                    {{-- Dòng 1: Mã REQ & Priority --}}
                                    <div class="tk-mb-card__header">
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            @if($ticket->ticket_type === 'report')
                                                <span class="tk-badge-report">Tố cáo</span>
                                            @endif
                                            <span class="tk-mb-card__req">#REQ-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                        @if($ticket->priority === 'urgent')
                                            <span class="tk-mb-pill tk-mb-pill--urgent">Khẩn cấp</span>
                                        @elseif($ticket->priority === 'high')
                                            <span class="tk-mb-pill tk-mb-pill--high">Cao</span>
                                        @elseif($ticket->priority === 'medium')
                                            <span class="tk-mb-pill tk-mb-pill--medium">Bình thường</span>
                                        @else
                                            <span class="tk-mb-pill tk-mb-pill--low">Thấp</span>
                                        @endif
                                    </div>

                                    {{-- Dòng 2: Icon Tòa nhà & Phòng --}}
                                    <div class="tk-mb-card__room">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="tk-mb-card__room-icon"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path></svg>
                                        Phòng {{ str_pad($ticket->apartment->apartment_number ?? '', 4, '0', STR_PAD_LEFT) }}
                                    </div>

                                    {{-- Dòng 3: Tiêu đề --}}
                                    <div class="tk-mb-card__title">
                                        {{ $ticket->title }}
                                    </div>

                                    {{-- Dòng 4: Hình ảnh & Thời gian + Trạng thái --}}
                                    <div class="tk-mb-card__details">
                                        @if(!empty($ticket->images) && is_array($ticket->images) && count($ticket->images) > 0)
                                            @php
                                                $ext = strtolower(pathinfo($ticket->images[0], PATHINFO_EXTENSION));
                                                $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
                                            @endphp
                                            <div class="tk-mb-card__img-box">
                                                @if($isVideo)
                                                    <video src="{{ asset('storage/' . $ticket->images[0]) }}" muted style="width:100%; height:100%; object-fit:cover;"></video>
                                                @else
                                                    <img src="{{ asset('storage/' . $ticket->images[0]) }}" alt="Thumbnail">
                                                @endif
                                            </div>
                                        @elseif(!empty($ticket->image))
                                            @php
                                                $ext = strtolower(pathinfo($ticket->image, PATHINFO_EXTENSION));
                                                $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
                                            @endphp
                                            <div class="tk-mb-card__img-box">
                                                @if($isVideo)
                                                    <video src="{{ asset('storage/' . $ticket->image) }}" muted style="width:100%; height:100%; object-fit:cover;"></video>
                                                @else
                                                    <img src="{{ asset('storage/' . $ticket->image) }}" alt="Thumbnail">
                                                @endif
                                            </div>
                                        @endif
                                        <div class="tk-mb-card__info">
                                            <div class="tk-mb-card__time">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                Báo cáo: {{ $ticket->created_at ? $ticket->created_at->diffForHumans() : '-' }}
                                            </div>
                                            
                                            @if($ticket->status === 'pending')
                                                <span class="tk-mb-status tk-mb-status--pending">Chờ tiếp nhận</span>
                                            @elseif($ticket->status === 'assigned')
                                                <span class="tk-mb-status tk-mb-status--assigned">Chờ xử lý</span>
                                            @elseif($ticket->status === 'in_progress')
                                                <span class="tk-mb-status tk-mb-status--active">Đang xử lý</span>
                                            @elseif($ticket->status === 'completed')
                                                <span class="tk-mb-status tk-mb-status--completed">Hoàn thành</span>
                                            @elseif($ticket->status === 'cancelled')
                                                <span class="tk-mb-status tk-mb-status--cancelled">Đã hủy</span>
                                            @else
                                                <span class="tk-mb-status tk-mb-status--pending">{{ $ticket->statusLabel() }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Dòng 5: Nút thao tác (Full width) --}}
                                    <div class="tk-mb-card__actions">
                                        <a href="{{ portal_route('tickets.show', $ticket->id) }}" class="tk-mb-action-btn tk-mb-action-btn--outline">
                                            Chi tiết
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="tk-table-empty">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p>Không tìm thấy phản ánh nào phù hợp</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer / Pagination --}}
        <div class="tickets-table-footer">
            <div class="tickets-table-footer__info">
                Hiển thị {{ $tickets->firstItem() ?? 0 }}-{{ $tickets->lastItem() ?? 0 }} của {{ number_format($tickets->total()) }}
            </div>
            <div class="tickets-table-footer__pagination">
                {{ $tickets->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>

<script>
// Row click is disabled per user request. You must click the 'Chi tiết' button to view details.

document.addEventListener('DOMContentLoaded', function() {
    // Lắng nghe sự kiện Real-time qua Laravel Echo
    if (window.Echo) {
        window.Echo.private('admin.tickets')
            .listen('TicketCreated', (e) => {
                // Tải lại trang hoặc hiển thị thông báo
                // Ở đây cách đơn giản nhất là hiển thị alert và refresh sau 3s
                const toast = document.createElement('div');
                toast.className = 'tickets-alert tickets-alert--success';
                toast.style.position = 'fixed';
                toast.style.bottom = '20px';
                toast.style.right = '20px';
                toast.style.zIndex = '9999';
                toast.innerText = '📣 Có phản ánh sự cố mới: ' + (e.ticket.title || 'Vừa được gửi');
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            });
    }
});
</script>
@endsection
