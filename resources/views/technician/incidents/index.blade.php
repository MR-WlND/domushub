@extends('layouts.technician.master')

@section('page_title', 'Danh sách công việc – DomusHub')

@section('content')
<div class="manager-page">
    {{-- Header --}}
    <div class="page-header">
        <div>
            <h1 class="page-header__title">Danh sách công việc phân công</h1>
            <p class="page-header__subtitle">Theo dõi, xử lý và cập nhật tiến độ các sự cố được giao.</p>
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
        <div class="stat-card stat-card--warning" id="stat-assigned">
            <div class="stat-card__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <p class="stat-card__value">{{ $stats['assigned'] }}</p>
                <p class="stat-card__label">Đã phân công</p>
            </div>
        </div>
        <div class="stat-card stat-card--blue" id="stat-inprogress">
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
        </div>
        <div class="stat-card stat-card--green" id="stat-resolved">
            <div class="stat-card__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4"></polyline>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
            </div>
            <div>
                <p class="stat-card__value">{{ $stats['resolved'] }}</p>
                <p class="stat-card__label">Đã xử lý xong</p>
            </div>
        </div>
    </div>

    {{-- Bảng danh sách công việc --}}
    @if ($incidents->isEmpty())
        <div class="incidents-empty">
            <p>Hiện tại bạn không có công việc nào được phân công.</p>
        </div>
    @else
        <div class="data-table-wrapper">
            <table class="data-table" id="tech-incidents-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tiêu đề</th>
                        <th>Căn hộ</th>
                        <th>Cư dân</th>
                        <th>Loại sự cố</th>
                        <th>Ưu tiên</th>
                        <th>Trạng thái</th>
                        <th>Ngày phân công</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($incidents as $incident)
                        <tr class="data-table__row" id="row-{{ $incident->id }}">
                            <td class="data-table__id">#{{ $incident->id }}</td>
                            <td class="data-table__title">
                                <span class="text-ellipsis">{{ $incident->title }}</span>
                            </td>
                            <td>{{ $incident->apartment?->unit_number ?? '—' }}</td>
                            <td>{{ $incident->resident?->name ?? '—' }}</td>
                            <td><span class="incident-tag incident-tag--{{ $incident->category }}">{{ $incident->category_label }}</span></td>
                            <td><span class="incident-priority incident-priority--{{ $incident->priority }}">{{ $incident->priority_label }}</span></td>
                            <td><span class="incident-status incident-status--{{ $incident->status }}">{{ $incident->status_label }}</span></td>
                            <td class="data-table__date">{{ $incident->assigned_at ? $incident->assigned_at->format('d/m/Y H:i') : '—' }}</td>
                            <td>
                                <a href="{{ route('technician.incidents.show', $incident->id) }}"
                                    class="btn-table-action" id="btn-view-{{ $incident->id }}">Xử lý</a>
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
