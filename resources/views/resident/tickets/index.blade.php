@extends('layouts.resident.master')

@section('title', 'Phản ánh – DomusHub')

@push('styles')
    @vite(['resources/css/resident/tickets.css'])
@endpush

@section('content')
<div class="tk">

    {{-- HEADER --}}
    <div class="tk__header">
        <div>
            <p class="tk__eyebrow">Hệ thống phản ánh</p>
            <h1 class="tk__title">Phản ánh của tôi</h1>
        </div>

        <a href="{{ route('resident.tickets.create') }}" class="tk-btn tk-btn--primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
            Gửi phản ánh mới
        </a>
    </div>

    {{-- SUCCESS ALERT --}}
    @if(session('success'))
        <div class="tk-alert tk-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="tk-alert tk-alert--error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    {{-- STATS --}}
    <div class="tk-stats">
        <div class="tk-stat stat--total">
            <div class="tk-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div>
                <span class="tk-stat__num">{{ $stats['total'] }}</span>
                <span class="tk-stat__label">Tổng phản ánh</span>
            </div>
        </div>

        <div class="tk-stat stat--pending">
            <div class="tk-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <span class="tk-stat__num color-pending">{{ $stats['pending'] }}</span>
                <span class="tk-stat__label">Chờ xử lý</span>
            </div>
        </div>

        <div class="tk-stat stat--process">
            <div class="tk-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>
            </div>
            <div>
                <span class="tk-stat__num color-process">{{ $stats['in_progress'] }}</span>
                <span class="tk-stat__label">Đang xử lý</span>
            </div>
        </div>

        <div class="tk-stat stat--done">
            <div class="tk-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <span class="tk-stat__num color-done">{{ $stats['completed'] }}</span>
                <span class="tk-stat__label">Hoàn thành</span>
            </div>
        </div>
    </div>

    {{-- FILTER TABS --}}
    <div class="tk-tabs">
        <a href="{{ route('resident.tickets.index') }}" class="tk-tab {{ !request('status') ? 'tk-tab--active' : '' }}">Tất cả</a>
        <a href="{{ route('resident.tickets.index', ['status' => 'pending']) }}" class="tk-tab {{ request('status') === 'pending' ? 'tk-tab--active' : '' }}">Chờ xử lý</a>
        <a href="{{ route('resident.tickets.index', ['status' => 'assigned']) }}" class="tk-tab {{ request('status') === 'assigned' ? 'tk-tab--active' : '' }}">Đã phân công</a>
        <a href="{{ route('resident.tickets.index', ['status' => 'in_progress']) }}" class="tk-tab {{ request('status') === 'in_progress' ? 'tk-tab--active' : '' }}">Đang xử lý</a>
        <a href="{{ route('resident.tickets.index', ['status' => 'completed']) }}" class="tk-tab {{ request('status') === 'completed' ? 'tk-tab--active' : '' }}">Hoàn thành</a>
        <a href="{{ route('resident.tickets.index', ['status' => 'cancelled']) }}" class="tk-tab {{ request('status') === 'cancelled' ? 'tk-tab--active' : '' }}">Đã hủy</a>
    </div>

    {{-- TICKET LIST --}}
    @if($tickets->isEmpty())

        <div class="tk-empty">
            <div class="tk-empty__visual">
                <div class="tk-empty__circle tk-empty__circle--outer"></div>
                <div class="tk-empty__circle tk-empty__circle--inner"></div>
                <div class="tk-empty__icon-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
            </div>
            <h3 class="tk-empty__title">Chưa có phản ánh nào</h3>
            <p class="tk-empty__desc">Bạn chưa gửi phản ánh nào. Nếu gặp sự cố trong căn hộ, hãy gửi phản ánh để Ban quản lý hỗ trợ.</p>
            <a href="{{ route('resident.tickets.create') }}" class="tk-btn tk-btn--primary">
                Gửi phản ánh ngay
            </a>
        </div>

    @else

        <div class="tk-list">
            @foreach($tickets as $ticket)
                <a href="{{ route('resident.tickets.show', $ticket->id) }}" class="tk-card card--{{ $ticket->status }}">
                    <div class="tk-card__accent"></div>

                    <div class="tk-card__body">
                        <div class="tk-card__header">
                            <span class="tk-card__id">#{{ $ticket->id }}</span>
                            <div class="tk-card__badges">
                                <span class="tk-badge badge--{{ $ticket->priority }}">{{ $ticket->priorityLabel() }}</span>
                                <span class="tk-badge badge--{{ $ticket->status }}">{{ $ticket->statusLabel() }}</span>
                                @if($ticket->rating)
                                    <div class="tk-rating-display" style="margin-left: 2px;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span class="tk-star {{ $i <= $ticket->rating ? 'tk-star--filled' : 'tk-star--empty' }}" style="font-size: 0.95rem;">★</span>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        </div>
                        <h3 class="tk-card__title">{{ $ticket->title }}</h3>
                        <p class="tk-card__desc">{{ $ticket->description }}</p>
                        <div class="tk-card__meta">
                            <span class="tk-card__date">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                {{ $ticket->created_at->format('d/m/Y H:i') }}
                            </span>
                            @if($ticket->handler)
                                <span class="tk-card__date">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    KTV: {{ $ticket->handler->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        @if($tickets->hasPages())
            <div class="tk-pagination">
                {{ $tickets->links() }}
            </div>
        @endif

    @endif

    {{-- TỐ CÁO LIÊN QUAN ĐẾN TÔI --}}
    @if($accusations->count() > 0)
        <div style="margin-top: 2rem;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 1rem;">
                <h2 style="font-size: 1.15rem; font-weight: 800; color: #dc2626; margin: 0;">
                    Tố cáo liên quan đến tôi
                </h2>
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #dc2626; color: #fff; border-radius: 50%; font-size: 0.75rem; font-weight: 700;">
                    {{ $accusations->count() }}
                </span>
            </div>

            <div class="tk-list">
                @foreach($accusations as $acc)
                    <a href="{{ route('resident.tickets.show', $acc->id) }}" class="tk-card" style="border-left: 4px solid #dc2626;">
                        <div class="tk-card__body">
                            <div class="tk-card__header">
                                <span class="tk-card__id">#{{ $acc->id }}</span>
                                <div class="tk-card__badges">
                                    <span style="display:inline-flex;padding:2px 8px;background:#fef2f2;color:#dc2626;border-radius:6px;font-size:0.7rem;font-weight:700;border:1px solid #fecaca;">
                                        Tố cáo
                                    </span>
                                    @if($acc->accused_response === 'confirmed')
                                        <span style="display:inline-flex;padding:2px 8px;background:#f0fdf4;color:#166534;border-radius:6px;font-size:0.7rem;font-weight:700;border:1px solid #86efac;">
                                            Đã xác nhận
                                        </span>
                                    @elseif($acc->accused_response === 'denied')
                                        <span style="display:inline-flex;padding:2px 8px;background:#fef2f2;color:#991b1b;border-radius:6px;font-size:0.7rem;font-weight:700;border:1px solid #fecaca;">
                                            Đã phản đối
                                        </span>
                                    @else
                                        <span style="display:inline-flex;padding:2px 8px;background:#fffbeb;color:#92400e;border-radius:6px;font-size:0.7rem;font-weight:700;border:1px solid #fde68a;">
                                            Chờ phản hồi
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <h3 class="tk-card__title">{{ $acc->title }}</h3>
                            <p class="tk-card__desc">{{ Str::limit($acc->description, 100) }}</p>
                            <div class="tk-card__meta">
                                <span class="tk-card__date">
                                    Người tố cáo: {{ $acc->sender->name ?? 'N/A' }}
                                </span>
                                <span class="tk-card__date">
                                    {{ $acc->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
