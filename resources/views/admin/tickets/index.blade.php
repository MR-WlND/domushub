@extends('layouts.admin.master')

@section('page_title', 'Quản lý Phản ánh')
@section('page_kicker', 'Dịch vụ cư dân')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', auth()->user()->role)

@push('styles')
    @vite(['resources/css/pages/admin/tickets/index.css'])
@endpush

@section('content')
<div class="tickets-page">

    {{-- Header --}}
    <div class="tickets-page__header">
        <div>
            <h1>Quản lý Phản ánh</h1>
            <p class="tickets-page__subtitle">Tiếp nhận và xử lý phản ánh sự cố từ cư dân.</p>
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
        <div class="tk-stat-card" style="border-left: 4px solid #7c3aed;">
            <span class="tk-stat-card__label">Tổng phản ánh</span>
            <span class="tk-stat-card__value" style="color: #7c3aed;">{{ number_format($stats['total']) }}</span>
        </div>
        <div class="tk-stat-card" style="border-left: 4px solid #f59e0b;">
            <span class="tk-stat-card__label">Chờ xử lý</span>
            <span class="tk-stat-card__value" style="color: #f59e0b;">{{ number_format($stats['pending']) }}</span>
        </div>
        <div class="tk-stat-card" style="border-left: 4px solid #8b5cf6;">
            <span class="tk-stat-card__label">Đã phân công</span>
            <span class="tk-stat-card__value" style="color: #8b5cf6;">{{ number_format($stats['assigned']) }}</span>
        </div>
        <div class="tk-stat-card" style="border-left: 4px solid #2563eb;">
            <span class="tk-stat-card__label">Đang xử lý</span>
            <span class="tk-stat-card__value" style="color: #2563eb;">{{ number_format($stats['in_progress']) }}</span>
        </div>
        <div class="tk-stat-card" style="border-left: 4px solid #16a34a;">
            <span class="tk-stat-card__label">Hoàn thành</span>
            <span class="tk-stat-card__value" style="color: #16a34a;">{{ number_format($stats['completed']) }}</span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="tickets-filter-card">
        <form method="GET" id="ticket-filter-form">
            <div class="tickets-filter-grid">
                <div>
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Mã, tiêu đề, căn hộ..."
                           onchange="document.getElementById('ticket-filter-form').submit()">
                </div>
                <div>
                    <label>Trạng thái</label>
                    <select name="status" onchange="document.getElementById('ticket-filter-form').submit()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Đã phân công</option>
                        <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div>
                    <label>Mức độ ưu tiên</label>
                    <select name="priority" onchange="document.getElementById('ticket-filter-form').submit()">
                        <option value="">Tất cả mức độ</option>
                        <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Thấp</option>
                        <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Trung bình</option>
                        <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Cao</option>
                        <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="tickets-table-card">
        <div class="tickets-table-wrap">
            <table class="tickets-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Căn hộ</th>
                        <th>Tiêu đề</th>
                        <th>Ưu tiên</th>
                        <th>Trạng thái</th>
                        <th>KTV xử lý</th>
                        <th>Ngày gửi</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                    <tr>
                        <td><strong>#{{ $ticket->id }}</strong></td>
                        <td>
                            <div>
                                <strong>{{ $ticket->apartment->apartment_number ?? 'N/A' }}</strong>
                                <div style="font-size: 0.78rem; color: #94a3b8;">
                                    {{ $ticket->apartment->floor->block->name ?? '' }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="tk-title-cell">
                                <span class="tk-title-cell__title">{{ $ticket->title }}</span>
                                <span class="tk-title-cell__desc">{{ Str::limit($ticket->description, 60) }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="tk-priority tk-priority--{{ $ticket->priority }}">
                                {{ $ticket->priorityLabel() }}
                            </span>
                        </td>
                        <td>
                            <span class="tk-status tk-status--{{ $ticket->status }}">
                                {{ $ticket->statusLabel() }}
                            </span>
                        </td>
                        <td>
                            @if($ticket->handler)
                                <span style="font-weight: 600;">{{ $ticket->handler->name }}</span>
                            @else
                                <span style="color: #94a3b8;">Chưa phân công</span>
                            @endif
                        </td>
                        <td style="white-space: nowrap; font-size: 0.85rem;">
                            {{ $ticket->created_at->format('d/m/Y') }}
                            <div style="font-size: 0.75rem; color: #94a3b8;">{{ $ticket->created_at->format('H:i') }}</div>
                        </td>
                        <td class="text-right">
                            <div class="tk-table-actions">
                                <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="tk-table-btn tk-table-btn--view">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    Xem
                                </a>

                                @if($ticket->status === 'pending' && in_array(auth()->user()->role, ['admin', 'manager']))
                                    <form action="{{ route('admin.tickets.assign', $ticket->id) }}" method="POST" style="display: flex; gap: 4px;">
                                        @csrf
                                        <select name="handler_id" required style="padding: 5px 8px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.78rem; min-width: 120px;">
                                            <option value="" disabled selected>Chọn KTV</option>
                                            @foreach($technicians as $tech)
                                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="tk-table-btn tk-table-btn--assign">Phân công</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 40px; color: #94a3b8;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.5" style="margin-bottom: 10px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <br>Chưa có phản ánh nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($tickets->hasPages())
        <div class="tickets-pagination">
            {{ $tickets->links() }}
        </div>
    @endif

</div>
@endsection
