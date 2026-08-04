@extends('layouts.admin.master')

@section('page_title', 'Nhiệm vụ của tôi')
@section('page_kicker', 'Kỹ thuật viên')
@section('role_title', 'Kỹ thuật viên')
@section('home_route', portal_route('tickets.my-tasks'))
@section('user_name', auth()->user()->name ?? 'KTV')
@section('user_role', 'technician')

@push('styles')
    @vite(['resources/css/pages/admin/tickets/technician.css'])
@endpush

@section('content')
<div class="ktv-page">

    {{-- Page Header --}}
    <div class="ktv-header">
        <div class="ktv-header__left">
            <h1 class="ktv-header__title">Nhiệm vụ của tôi</h1>
            <p class="ktv-header__sub ktv-desktop-sub">Quản lý và theo dõi tiến độ các yêu cầu kỹ thuật được phân công.</p>
            <p class="ktv-header__sub ktv-mobile-sub">{{ date('d') }} Tháng {{ date('m') }}, {{ date('Y') }}</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="ktv-alert ktv-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="ktv-alert ktv-alert--danger">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Stat Cards Grid --}}
    <div class="ktv-stats-grid">
        {{-- Card 1: Tổng nhiệm vụ --}}
        <div class="ktv-stat-card ktv-stat-card--total">
            <div class="ktv-stat-card__icon ktv-stat-card__icon--blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div class="ktv-stat-card__content">
                <span class="ktv-stat-card__label">TỔNG NHIỆM VỤ</span>
                <span class="ktv-stat-card__value">{{ $stats['total'] }}</span>
            </div>
        </div>

        {{-- Card 2: Đang thực hiện --}}
        <div class="ktv-stat-card ktv-stat-card--active">
            <div class="ktv-stat-card__icon ktv-stat-card__icon--orange">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="ktv-stat-card__content">
                <span class="ktv-stat-card__label">ĐANG THỰC HIỆN</span>
                <span class="ktv-stat-card__value">{{ $stats['active'] }}</span>
            </div>
        </div>

        {{-- Card 3: Chờ xử lý / Hoàn thành tuần --}}
        <div class="ktv-stat-card ktv-stat-card--new">
            <div class="ktv-stat-card__icon ktv-stat-card__icon--cyan">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ktv-stat-card__content">
                <span class="ktv-stat-card__label ktv-desktop-label">CHỜ XỬ LÝ</span>
                <span class="ktv-stat-card__label ktv-mobile-label">
                    <svg class="ktv-mb-icon-inline" style="color: #00236f; margin-right: 2px;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l4.992-5.99a.75.75 0 0 0-.018-1.047z"/></svg>
                    Hoàn thành tuần này
                </span>
                <span class="ktv-stat-card__value ktv-desktop-val">{{ $stats['new'] }}</span>
                <span class="ktv-stat-card__value ktv-mobile-val">{{ $stats['completed_this_week'] ?? $stats['completed_this_month'] }}</span>
            </div>
        </div>

        {{-- Card 4: Hoàn thành tháng / Đánh giá --}}
        <div class="ktv-stat-card ktv-stat-card--completed">
            <div class="ktv-stat-card__icon ktv-stat-card__icon--green">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ktv-stat-card__content">
                <span class="ktv-stat-card__label ktv-desktop-label">HOÀN THÀNH THÁNG NÀY</span>
                <span class="ktv-stat-card__label ktv-mobile-label" style="color: #059669;">
                    <svg class="ktv-mb-icon-inline" style="color: #059669; margin-right: 2px;" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>
                    Đánh giá trung bình
                </span>
                <span class="ktv-stat-card__value ktv-desktop-val">{{ $stats['completed_this_month'] }}</span>
                <span class="ktv-stat-card__value ktv-mobile-val">{{ $stats['avg_rating'] ?? '4.9/5' }}</span>
            </div>
        </div>
    </div>

    {{-- Mobile Status Tabs (Horizontal Scrollable) --}}
    <div class="ktv-mobile-tabs">
        <a href="{{ portal_route('tickets.my-tasks', array_merge(request()->except('page'), ['status' => 'all'])) }}"
           class="ktv-mb-tab {{ !request('status') || request('status') === 'all' ? 'active' : '' }}">
            Tất cả
        </a>
        <a href="{{ portal_route('tickets.my-tasks', array_merge(request()->except('page'), ['status' => 'assigned'])) }}"
           class="ktv-mb-tab {{ request('status') === 'assigned' ? 'active' : '' }}">
            Chờ xử lý
        </a>
        <a href="{{ portal_route('tickets.my-tasks', array_merge(request()->except('page'), ['status' => 'in_progress'])) }}"
           class="ktv-mb-tab {{ request('status') === 'in_progress' ? 'active' : '' }}">
            Đang xử lý
        </a>
        <a href="{{ portal_route('tickets.my-tasks', array_merge(request()->except('page'), ['status' => 'completed'])) }}"
           class="ktv-mb-tab {{ request('status') === 'completed' ? 'active' : '' }}">
            Hoàn thành
        </a>
    </div>



    {{-- Main Data Table Card --}}
    <div class="ktv-table-card">
        <div class="table-responsive">
            <table class="ktv-table">
                <thead>
                    <tr>
                        <th>MÃ YÊU CẦU</th>
                        <th>CĂN HỘ</th>
                        <th>LOẠI SỰ CỐ</th>
                        <th>MỨC ĐỘ ƯU TIÊN</th>
                        <th>TRẠNG THÁI</th>
                        <th>NGÀY GỬI</th>
                        <th style="text-align: center;">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr class="ktv-row-item ktv-desktop-only">
                            {{-- Mã yêu cầu (Desktop only) --}}
                            <td class="ktv-table__req-code">
                                <a href="{{ portal_route('tickets.show', $ticket->id) }}">
                                    #REQ-{{ $ticket->created_at ? $ticket->created_at->format('Y') : date('Y') }}-{{ sprintf('%03d', $ticket->id) }}
                                </a>
                            </td>

                            {{-- Căn hộ / Địa điểm --}}
                            <td class="ktv-table__apt">
                                <svg class="ktv-mb-icon-loc" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                @if($ticket->apartment)
                                    Phòng {{ $ticket->apartment->floor && $ticket->apartment->floor->block ? $ticket->apartment->floor->block->name . '-' : '' }}{{ $ticket->apartment->apartment_number }}
                                @else
                                    Sảnh / Khu vực chung
                                @endif
                            </td>

                            {{-- Loại sự cố / Tiêu đề --}}
                            <td class="ktv-table__title">
                                <a href="{{ portal_route('tickets.show', $ticket->id) }}" style="color: inherit; text-decoration: none;">
                                    {{ $ticket->title }}
                                </a>
                            </td>

                            {{-- Mức độ ưu tiên --}}
                            <td class="ktv-table__priority">
                                @if($ticket->priority === 'urgent')
                                    <span class="ktv-pill ktv-pill--urgent">Khẩn cấp</span>
                                @elseif($ticket->priority === 'high')
                                    <span class="ktv-pill ktv-pill--high">Cao</span>
                                @elseif($ticket->priority === 'medium')
                                    <span class="ktv-pill ktv-pill--medium">Bình thường</span>
                                @else
                                    <span class="ktv-pill ktv-pill--low">Thấp</span>
                                @endif
                            </td>

                            {{-- Trạng thái --}}
                            <td class="ktv-table__status">
                                @if($ticket->status === 'assigned')
                                    <span class="ktv-pill ktv-pill--status-assigned">Chờ xử lý</span>
                                @elseif($ticket->status === 'in_progress')
                                    @if($ticket->reopened_count > 0)
                                        <span class="ktv-pill ktv-pill--status-recheck">Cần kiểm tra lại</span>
                                    @else
                                        <span class="ktv-pill ktv-pill--status-active">Đang xử lý</span>
                                    @endif
                                @elseif($ticket->status === 'completed')
                                    <span class="ktv-pill ktv-pill--status-completed">Hoàn thành</span>
                                @else
                                    <span class="ktv-pill ktv-pill--status-assigned">{{ $ticket->statusLabel() }}</span>
                                @endif
                            </td>

                            {{-- Ngày gửi / Thời gian --}}
                            <td class="ktv-table__date">
                                <span class="ktv-desktop-date">{{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-' }}</span>
                                <span class="ktv-mobile-date">
                                    @if($ticket->created_at)
                                        @if($ticket->created_at->isToday())
                                            {{ $ticket->created_at->format('h:i A') }}
                                        @elseif($ticket->created_at->isYesterday())
                                            Hôm qua
                                        @else
                                            {{ $ticket->created_at->format('d/m') }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </span>
                            </td>

                            {{-- Thao tác --}}
                            <td class="ktv-table__actions">
                                @if($ticket->status === 'assigned')
                                    <button type="button" class="ktv-action-btn ktv-action-btn--primary ktv-accept-btn"
                                            data-url="{{ portal_route('tickets.accept', $ticket->id) }}">
                                        Bắt đầu
                                    </button>
                                @elseif($ticket->status === 'in_progress')
                                    <button type="button" class="ktv-action-btn ktv-action-btn--primary"
                                            onclick="openProgressModal(this)"
                                            data-url="{{ portal_route('tickets.update-progress', $ticket->id) }}"
                                            data-title="{{ $ticket->title }}"
                                            data-apt="Căn {{ $ticket->apartment->apartment_number ?? '' }}"
                                            data-block="{{ $ticket->apartment->floor->block->name ?? '' }}"
                                            data-priority="{{ $ticket->priority }}"
                                            data-status="{{ $ticket->status }}">
                                        Cập nhật tiến độ
                                    </button>
                                @endif
                                <a href="{{ portal_route('tickets.show', $ticket->id) }}" class="ktv-action-btn ktv-action-btn--ghost ktv-desktop-action">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                        
                        {{-- GIAO DIỆN MỚI CHO MOBILE --}}
                        <tr class="ktv-mobile-only">
                            <td colspan="7" style="padding: 0; border: none; background: transparent;">
                                <div class="ktv-mb-card">
                                    {{-- Dòng 1: Mã REQ & Priority --}}
                                    <div class="ktv-mb-card__header">
                                        <span class="ktv-mb-card__req">#REQ-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</span>
                                        @if($ticket->priority === 'urgent')
                                            <span class="ktv-mb-pill ktv-mb-pill--urgent"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Khẩn cấp</span>
                                        @elseif($ticket->priority === 'high')
                                            <span class="ktv-mb-pill ktv-mb-pill--high"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg> Cao</span>
                                        @elseif($ticket->priority === 'medium')
                                            <span class="ktv-mb-pill ktv-mb-pill--medium"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg> Bình thường</span>
                                        @else
                                            <span class="ktv-mb-pill ktv-mb-pill--low"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Thấp</span>
                                        @endif
                                    </div>

                                    {{-- Dòng 2: Icon Tòa nhà & Phòng --}}
                                    <div class="ktv-mb-card__room">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ktv-mb-card__room-icon"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path></svg>
                                        Phòng {{ str_pad($ticket->apartment->apartment_number ?? '', 4, '0', STR_PAD_LEFT) }}
                                    </div>

                                    {{-- Dòng 3: Tiêu đề --}}
                                    <div class="ktv-mb-card__title">
                                        {{ $ticket->title }}
                                    </div>

                                    {{-- Dòng 4: Hình ảnh & Thời gian + Trạng thái --}}
                                    <div class="ktv-mb-card__details">
                                        @if(!empty($ticket->images) && is_array($ticket->images) && count($ticket->images) > 0)
                                            <div class="ktv-mb-card__img-box">
                                                <img src="{{ asset('storage/' . $ticket->images[0]) }}" alt="Thumbnail">
                                            </div>
                                        @elseif(!empty($ticket->image))
                                            <div class="ktv-mb-card__img-box">
                                                <img src="{{ asset('storage/' . $ticket->image) }}" alt="Thumbnail">
                                            </div>
                                        @endif
                                        <div class="ktv-mb-card__info">
                                            <div class="ktv-mb-card__time">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                                Báo cáo: {{ $ticket->created_at ? $ticket->created_at->diffForHumans() : '-' }}
                                            </div>
                                            
                                            @if($ticket->status === 'assigned')
                                                <span class="ktv-mb-status ktv-mb-status--assigned">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg> Chờ xử lý
                                                </span>
                                            @elseif($ticket->status === 'in_progress')
                                                <span class="ktv-mb-status ktv-mb-status--active">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg> Đang xử lý
                                                </span>
                                            @elseif($ticket->status === 'completed')
                                                <span class="ktv-mb-status ktv-mb-status--completed">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg> Hoàn thành
                                                </span>
                                            @else
                                                <span class="ktv-mb-status ktv-mb-status--assigned">{{ $ticket->statusLabel() }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Dòng 5: Nút thao tác (Full width) --}}
                                    <div class="ktv-mb-card__actions">
                                        @if($ticket->status === 'assigned')
                                            <button type="button" class="ktv-mb-action-btn ktv-mb-action-btn--primary ktv-accept-btn"
                                                    data-url="{{ portal_route('tickets.accept', $ticket->id) }}">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                                                Bắt đầu xử lý
                                            </button>
                                        @elseif($ticket->status === 'in_progress')
                                            <button type="button" class="ktv-mb-action-btn ktv-mb-action-btn--primary"
                                                    onclick="openProgressModal(this)"
                                                    data-url="{{ portal_route('tickets.update-progress', $ticket->id) }}"
                                                    data-title="{{ $ticket->title }}"
                                                    data-apt="Căn {{ $ticket->apartment->apartment_number ?? '' }}"
                                                    data-block="{{ $ticket->apartment->floor->block->name ?? '' }}"
                                                    data-priority="{{ $ticket->priority }}"
                                                    data-status="{{ $ticket->status }}">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Cập nhật tiến độ
                                            </button>
                                        @else
                                            <a href="{{ portal_route('tickets.show', $ticket->id) }}" class="ktv-mb-action-btn ktv-mb-action-btn--outline">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                                Xem chi tiết
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="ktv-table__empty">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p>Không tìm thấy nhiệm vụ nào phù hợp</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer / Pagination --}}
        <div class="ktv-table-footer">
            <div class="ktv-table-footer__info">
                Hiển thị {{ $tickets->firstItem() ?? 0 }}-{{ $tickets->lastItem() ?? 0 }} trong số {{ $tickets->total() }} kết quả
            </div>
            <div class="ktv-table-footer__pagination">
                {{ $tickets->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>

{{-- Mobile Floating Action Button --}}
<button type="button" class="ktv-mb-fab" onclick="window.scrollTo({top:0, behavior:'smooth'})">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
</button>

{{-- Modal: Cập nhật tiến độ --}}
<div class="ktv-modal-overlay" id="progressModalOverlay" onclick="closeProgressModal()"></div>
<div class="ktv-modal" id="progressModal">
    <div class="ktv-modal__header">
        <div>
            <p class="ktv-modal__eyebrow" id="modalEyebrow">Cập nhật tiến độ</p>
            <h2 class="ktv-modal__title" id="modalTitle">—</h2>
        </div>
        <button class="ktv-modal__close" onclick="closeProgressModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <form id="progressForm" method="POST" enctype="multipart/form-data" class="ktv-modal__body">
        @csrf
        <div class="ktv-modal__info" id="modalInfo"></div>

        <div class="ktv-modal__field">
            <label for="progressStatus">Trạng thái mới <span class="required">*</span></label>
            <select name="status" id="progressStatus" required onchange="updateProgressModalNotes(this.value)">
                <option value="" disabled selected>-- Chọn trạng thái --</option>
                <option value="in_progress">🔄 Đang xử lý (tiếp tục)</option>
                <option value="completed">✅ Hoàn thành</option>
            </select>
        </div>

        <div class="ktv-modal__field">
            <label for="progressComment">Báo cáo / Ghi chú <span id="commentFieldNote" class="ktv-field-note">(bắt buộc khi hoàn thành)</span></label>
            <textarea name="comment" id="progressComment" placeholder="Mô tả công việc đã thực hiện, vật tư sử dụng, kết quả..."></textarea>
        </div>

        <div class="ktv-modal__field">
            <label>Ảnh nghiệm thu / Ảnh tiến trình <span id="proofFieldNote" class="ktv-field-note">(tùy chọn khi đang xử lý, bắt buộc khi hoàn thành)</span></label>
            <div class="ktv-modal__upload" id="uploadZone" onclick="openFilePicker()">
                <input type="file" name="image_proof" id="imgProofInput" accept="image/*" capture="environment" style="display:none" onchange="handleFileSelect(this)">
                <div id="uploadPlaceholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Nhấn để chọn ảnh nghiệm thu</span>
                </div>
                <div id="uploadPreview" style="display:none">
                    <img id="previewImg" src="" alt="preview">
                    <span id="previewName"></span>
                </div>
            </div>
            <button type="button" class="ktv-btn ktv-btn--sm ktv-btn--ghost" style="margin-top:0.75rem;" onclick="openCameraCapture()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 7h-2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"/><path d="M9 7l1.5-3h3L15 7"/><circle cx="12" cy="15" r="3"/></svg>
                Mở camera máy tính
            </button>
        </div>

        <div class="ktv-modal__footer">
            <button type="button" class="ktv-btn ktv-btn--ghost" onclick="closeProgressModal()">Hủy</button>
            <button type="submit" class="ktv-btn ktv-btn--primary" id="submitProgressBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Cập nhật tiến độ
            </button>
        </div>
    </form>
</div>

<div id="cameraModalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:10000;cursor:pointer;" onclick="closeCameraModal()"></div>
<div id="cameraModal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%, -50%);width:92vw;max-width:520px;background:#fff;border-radius:16px;box-shadow:0 24px 48px rgba(15,23,42,0.25);z-index:10001;padding:16px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <div style="font-weight:700;font-size:0.95rem;color:#0f172a;">Camera máy tính</div>
        <button type="button" class="ktv-btn ktv-btn--ghost" style="padding:6px 10px;font-size:0.85rem;" onclick="closeCameraModal()">Đóng</button>
    </div>
    <video id="cameraVideo" autoplay playsinline style="width:100%;height:auto;border-radius:14px;background:#000;"></video>
    <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:12px;">
        <button type="button" class="ktv-btn ktv-btn--ghost" onclick="closeCameraModal()">Hủy</button>
        <button type="button" class="ktv-btn ktv-btn--primary" onclick="captureCameraPhoto()">Chụp ảnh</button>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

document.querySelectorAll('.ktv-accept-btn').forEach(btn => {
    btn.addEventListener('click', async function (e) {
        e.stopPropagation();

        if (!confirm('Bạn có chắc chắn muốn nhận nhiệm vụ này và bắt đầu thực hiện không?')) {
            return;
        }

        const url     = this.dataset.url;
        const origTxt = this.innerHTML;

        this.disabled = true;
        this.innerHTML = 'Đang nhận...';

        try {
            const res  = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const data = await res.json();

            if (data.success) {
                showKtvToast('✅ ' + data.message, 'success');
                setTimeout(() => location.reload(), 600);
            } else {
                showKtvToast('❌ ' + data.message, 'error');
                this.disabled = false;
                this.innerHTML = origTxt;
            }
        } catch {
            showKtvToast('❌ Lỗi kết nối. Vui lòng thử lại.', 'error');
            this.disabled = false;
            this.innerHTML = origTxt;
        }
    });
});

function openProgressModal(btn) {
    const url   = btn.dataset.url;
    const title = btn.dataset.title;
    const apt   = btn.dataset.apt;
    const block = btn.dataset.block;
    const pri   = btn.dataset.priority;
    const status = btn.dataset.status;

    document.getElementById('progressForm').action = url;
    document.getElementById('modalTitle').textContent   = title;
    document.getElementById('modalEyebrow').textContent = 'Cập nhật tiến độ';
    document.getElementById('modalInfo').innerHTML =
        `<span class="ktv-modal__apt">${apt} · ${block}</span>
         <span class="tk-priority tk-priority--${pri}">${getPriorityLabel(pri)}</span>`;

    const statusSelect = document.getElementById('progressStatus');

    if (status === 'in_progress') {
        statusSelect.value = 'in_progress';
    } else {
        statusSelect.value = '';
    }

    updateProgressModalNotes(statusSelect.value);
    document.getElementById('progressComment').value = '';
    document.getElementById('imgProofInput').value = '';
    document.getElementById('uploadPreview').style.display = 'none';
    document.getElementById('uploadPlaceholder').style.display = 'flex';

    document.getElementById('progressModalOverlay').classList.add('active');
    document.getElementById('progressModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function updateProgressModalNotes(status) {
    const proofNote = document.getElementById('proofFieldNote');
    const commentNote = document.getElementById('commentFieldNote');
    if (!proofNote || !commentNote) return;

    if (status === 'completed') {
        proofNote.textContent = '(bắt buộc khi hoàn thành)';
        commentNote.textContent = '(bắt buộc khi hoàn thành)';
    } else {
        proofNote.textContent = '(tùy chọn)';
        commentNote.textContent = '(tùy chọn)';
    }
}

function closeProgressModal() {
    document.getElementById('progressModalOverlay').classList.remove('active');
    document.getElementById('progressModal').classList.remove('active');
    document.body.style.overflow = '';
}

let cameraStream = null;

function openFilePicker() {
    document.getElementById('imgProofInput').click();
}

function openCameraCapture() {
    const overlay = document.getElementById('cameraModalOverlay');
    const modal   = document.getElementById('cameraModal');
    const video   = document.getElementById('cameraVideo');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Trình duyệt của bạn không hỗ trợ mở camera.');
        return;
    }

    overlay.style.display = 'block';
    modal.style.display   = 'block';

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            cameraStream = stream;
            video.srcObject = stream;
            video.play();
        })
        .catch(error => {
            alert('Không thể kết nối camera máy tính. Bạn có thể tải ảnh lên thủ công.');
            closeCameraModal();
        });
}

function closeCameraModal() {
    const overlay = document.getElementById('cameraModalOverlay');
    const modal   = document.getElementById('cameraModal');
    const video   = document.getElementById('cameraVideo');

    if (overlay) overlay.style.display = 'none';
    if (modal)   modal.style.display   = 'none';

    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
    if (video) video.srcObject = null;
}

function captureCameraPhoto() {
    const video = document.getElementById('cameraVideo');
    if (!video || !video.videoWidth) {
        alert('Camera chưa sẵn sàng. Vui lòng thử lại.');
        return;
    }

    const canvas  = document.createElement('canvas');
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;

    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(blob => {
        if (!blob) return;
        const file = new File([blob], 'camera-capture.jpg', { type: 'image/jpeg' });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        const input = document.getElementById('imgProofInput');
        input.files = dataTransfer.files;
        handleFileSelect(input);
        closeCameraModal();
    });
}

const progressForm = document.getElementById('progressForm');
if (progressForm) {
    progressForm.addEventListener('submit', function (e) {
        const btn = document.getElementById('submitProgressBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'Đang lưu...';
        }
    });
}

function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('previewName').textContent = file.name;
        document.getElementById('uploadPlaceholder').style.display = 'none';
        document.getElementById('uploadPreview').style.display = 'flex';
    };
    reader.readAsDataURL(file);
}

function getPriorityLabel(p) {
    return { urgent: 'Khẩn cấp', high: 'Cao', medium: 'Trung bình', low: 'Thấp' }[p] || p;
}

function showKtvToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = 'ktv-toast ktv-toast--' + type;
    t.textContent = msg;
    document.body.appendChild(t);
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => {
        t.classList.remove('show');
        setTimeout(() => t.remove(), 300);
    }, 3000);
}
</script>
@endsection
