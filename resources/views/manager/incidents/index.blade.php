@extends('layouts.manager.master')

@section('page_title', 'Quản lý phản ánh – DomusHub')

@section('content')
<div class="manager-page">
    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-header__title">Quản lý phản ánh sự cố</h1>
            <p class="page-header__subtitle">Tiếp nhận, phân công và theo dõi xử lý phản ánh từ cư dân.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert--success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert--error">{{ session('error') }}</div>
    @endif

    {{-- Thống kê nhanh --}}
    <div class="stats-row">
        <a href="{{ route('manager.incidents.index', ['status' => 'pending']) }}" class="stat-card stat-card--warning" id="stat-pending">
            <div class="stat-card__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <p class="stat-card__value">{{ $stats['pending'] }}</p>
                <p class="stat-card__label">Chờ tiếp nhận</p>
            </div>
        </a>
        <a href="{{ route('manager.incidents.index', ['status' => 'assigned']) }}" class="stat-card stat-card--blue" id="stat-inprogress">
            <div class="stat-card__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                </svg>
            </div>
            <div>
                <p class="stat-card__value">{{ $stats['in_progress'] }}</p>
                <p class="stat-card__label">Đang xử lý</p>
            </div>
        </a>
        <a href="{{ route('manager.incidents.index', ['status' => 'resolved']) }}" class="stat-card stat-card--green" id="stat-resolved">
            <div class="stat-card__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4"></polyline>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
            </div>
            <div>
                <p class="stat-card__value">{{ $stats['resolved'] }}</p>
                <p class="stat-card__label">Chờ xác nhận</p>
            </div>
        </a>
        <a href="{{ route('manager.incidents.index', ['status' => 'confirmed']) }}" class="stat-card stat-card--purple" id="stat-confirmed">
            <div class="stat-card__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            <div>
                <p class="stat-card__value">{{ $stats['confirmed'] }}</p>
                <p class="stat-card__label">Đã hoàn thành</p>
            </div>
        </a>
    </div>

    {{-- Bộ lọc --}}
    <form method="GET" action="{{ route('manager.incidents.index') }}" class="filter-bar" id="filter-form">
        <div class="filter-bar__search">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" name="search" class="filter-input" placeholder="Tìm tiêu đề phản ánh..."
                value="{{ request('search') }}" id="filter-search">
        </div>
        <select name="status" class="filter-select" id="filter-status" onchange="this.form.submit()">
            <option value="">Tất cả trạng thái</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ tiếp nhận</option>
            <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Đã phân công</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Đang xử lý</option>
            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Chờ xác nhận</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Đã hoàn thành</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Đã đóng</option>
        </select>
        <select name="priority" class="filter-select" id="filter-priority" onchange="this.form.submit()">
            <option value="">Tất cả ưu tiên</option>
            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>🔴 Khẩn cấp</option>
            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>🟠 Cao</option>
            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>🟡 Trung bình</option>
            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>🟢 Thấp</option>
        </select>
        <select name="category" class="filter-select" id="filter-category" onchange="this.form.submit()">
            <option value="">Tất cả loại</option>
            <option value="electrical" {{ request('category') === 'electrical' ? 'selected' : '' }}>⚡ Điện</option>
            <option value="plumbing" {{ request('category') === 'plumbing' ? 'selected' : '' }}>💧 Nước</option>
            <option value="elevator" {{ request('category') === 'elevator' ? 'selected' : '' }}>🛗 Thang máy</option>
            <option value="cleaning" {{ request('category') === 'cleaning' ? 'selected' : '' }}>🧹 Vệ sinh</option>
            <option value="security" {{ request('category') === 'security' ? 'selected' : '' }}>🔒 An ninh</option>
            <option value="infrastructure" {{ request('category') === 'infrastructure' ? 'selected' : '' }}>🏗️ Hạ tầng</option>
            <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>📋 Khác</option>
        </select>
        <button type="submit" class="btn-primary btn--sm" id="btn-filter-submit">Tìm</button>
        @if (request()->hasAny(['status','priority','category','search']))
            <a href="{{ route('manager.incidents.index') }}" class="btn-outline btn--sm" id="btn-clear-filter">Xóa lọc</a>
        @endif
    </form>

    {{-- Bảng danh sách --}}
    @if ($incidents->isEmpty())
        <div class="incidents-empty">
            <p>Không có phản ánh nào phù hợp.</p>
        </div>
    @else
        <div class="data-table-wrapper">
            <table class="data-table" id="incidents-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tiêu đề</th>
                        <th>Cư dân</th>
                        <th>Loại</th>
                        <th>Ưu tiên</th>
                        <th>Trạng thái</th>
                        <th>KTV</th>
                        <th>Ngày gửi</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($incidents as $incident)
                        <tr class="data-table__row" id="row-{{ $incident->id }}">
                            <td class="data-table__id">#{{ $incident->id }}</td>
                            <td class="data-table__title">
                                <span class="text-ellipsis">{{ $incident->title }}</span>
                            </td>
                            <td>{{ $incident->resident?->name ?? '—' }}</td>
                            <td><span class="incident-tag incident-tag--{{ $incident->category }}">{{ $incident->category_label }}</span></td>
                            <td><span class="incident-priority incident-priority--{{ $incident->priority }}">{{ $incident->priority_label }}</span></td>
                            <td><span class="incident-status incident-status--{{ $incident->status }}">{{ $incident->status_label }}</span></td>
                            <td>{{ $incident->assignedTo?->name ?? '—' }}</td>
                            <td class="data-table__date">{{ $incident->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('manager.incidents.show', $incident->id) }}"
                                    class="btn-table-action" id="btn-view-{{ $incident->id }}">Xem</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="incidents-pagination">
            {{ $incidents->links() }}
        </div>
    @endif
</div>
@endsection
