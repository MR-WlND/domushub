@extends('layouts.admin.master')

@section('page_title', 'Chấm công nhân viên')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', strtoupper(auth()->user()->role ?? 'ADMIN'))

@push('styles')
    @vite(['resources/css/pages/admin/attendance/index.css'])
@endpush

@php
    $statusLabels = [
        'working'  => 'Đang làm việc',
        'present'  => 'Đúng giờ',
        'late'     => 'Đi trễ',
        'absent'   => 'Vắng mặt',
        'half_day' => 'Nửa ngày',
    ];

    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'manager'    => 'Quản lý',
        'staff'      => 'Kế toán',
        'technician' => 'Kỹ thuật',
        'security'   => 'An ninh',
        'cleaning'   => 'Vệ sinh',
    ];
@endphp

@section('content')
<div class="attendance-page">

    {{-- ── Header ── --}}
    <div class="attendance-page__header">
        <div>
            <p class="attendance-page__eyebrow">Nhân sự</p>
            <h1>Chấm công nhân viên</h1>
        </div>
        <div class="attendance-page__actions">
            <a href="{{ portal_route('attendance.export') }}" class="att-btn att-btn--secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Xuất báo cáo
            </a>
            <a href="{{ portal_route('attendance.bulk') }}" class="att-btn att-btn--success">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="9 11 12 14 22 4"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
                Chấm công nhanh
            </a>
            <a href="{{ portal_route('attendance.create') }}" class="att-btn att-btn--primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Thêm đơn lẻ
            </a>
        </div>

    </div>

    {{-- ── Alerts ── --}}
    @if (session('success'))
        <div class="att-alert att-alert--success">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="att-alert att-alert--danger">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- ── Stat Cards ── --}}
    <div class="attendance-stats">
        <div class="attendance-stat-card">
            <div class="attendance-stat-card__icon attendance-stat-card__icon--blue">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="attendance-stat-card__label">Tổng nhân viên</div>
            <div class="attendance-stat-card__value">{{ number_format($stats['total_staff']) }}</div>
            <div class="attendance-stat-card__sub">Đang hoạt động</div>
        </div>

        <div class="attendance-stat-card">
            <div class="attendance-stat-card__icon attendance-stat-card__icon--green">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div class="attendance-stat-card__label">Có mặt</div>
            <div class="attendance-stat-card__value">{{ number_format($stats['present']) }}</div>
            <div class="attendance-stat-card__sub">Kể cả đang làm</div>
        </div>

        <div class="attendance-stat-card">
            <div class="attendance-stat-card__icon attendance-stat-card__icon--orange">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="attendance-stat-card__label">Đi trễ</div>
            <div class="attendance-stat-card__value">{{ number_format($stats['late']) }}</div>
            <div class="attendance-stat-card__sub">Trong kỳ lọc</div>
        </div>

        <div class="attendance-stat-card">
            <div class="attendance-stat-card__icon attendance-stat-card__icon--red">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
            </div>
            <div class="attendance-stat-card__label">Vắng mặt</div>
            <div class="attendance-stat-card__value">{{ number_format($stats['absent']) }}</div>
            <div class="attendance-stat-card__sub">Trong kỳ lọc</div>
        </div>

        <div class="attendance-stat-card">
            <div class="attendance-stat-card__icon attendance-stat-card__icon--purple">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="attendance-stat-card__label">Nửa ngày</div>
            <div class="attendance-stat-card__value">{{ number_format($stats['half_day']) }}</div>
            <div class="attendance-stat-card__sub">Trong kỳ lọc</div>
        </div>

        <div class="attendance-stat-card">
            <div class="attendance-stat-card__icon attendance-stat-card__icon--teal">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="attendance-stat-card__label">Tổng giờ công</div>
            <div class="attendance-stat-card__value">{{ number_format($stats['total_hours'], 1) }}</div>
            <div class="attendance-stat-card__sub">Giờ làm việc</div>
        </div>
    </div>

    {{-- ── Filter bar ── --}}
    <form class="attendance-filter" method="GET" action="{{ portal_route('attendance.index') }}">
        <label class="attendance-filter__field" style="flex: 2 1 200px; min-width: 180px;">
            <span>Tìm kiếm</span>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Tên hoặc email nhân viên…">
        </label>

        <label class="attendance-filter__field">
            <span>Nhân viên</span>
            <select name="user_id">
                <option value="">Tất cả</option>
                @foreach ($staffList as $staff)
                    <option value="{{ $staff->id }}" @selected(request('user_id') == $staff->id)>
                        {{ $staff->name }} ({{ $roleLabels[$staff->role] ?? $staff->role }})
                    </option>
                @endforeach
            </select>
        </label>

        <label class="attendance-filter__field">
            <span>Từ ngày</span>
            <input type="date" name="date_from" value="{{ request('date_from', $dateFrom->toDateString()) }}">
        </label>

        <label class="attendance-filter__field">
            <span>Đến ngày</span>
            <input type="date" name="date_to" value="{{ request('date_to', $dateTo->toDateString()) }}">
        </label>

        <label class="attendance-filter__field">
            <span>Trạng thái</span>
            <select name="status">
                <option value="">Tất cả</option>
                @foreach ($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label class="attendance-filter__field">
            <span>Ca làm</span>
            <select name="shift">
                <option value="">Tất cả</option>
                @foreach (config('attendance.shifts') as $value => $label)
                    <option value="{{ $value }}" @selected(request('shift') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <div class="attendance-filter__actions">
            <button type="submit" class="att-btn att-btn--primary">Lọc</button>
            <a href="{{ portal_route('attendance.index') }}" class="att-btn att-btn--secondary">Xóa</a>
        </div>
    </form>

    {{-- ── Table ── --}}
    <div class="attendance-table-card">
        <div class="attendance-table-wrap">
            <table class="attendance-table">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Ngày</th>
                        <th>Ca làm</th>
                        <th>Giờ vào</th>
                        <th>Giờ ra</th>
                        <th>Giờ công</th>
                        <th>Trạng thái</th>
                        <th>Vị trí</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            {{-- Nhân viên --}}
                            <td>
                                <div class="att-identity">
                                    @if ($record->user && $record->user->avatar)
                                        <img src="{{ asset('storage/' . $record->user->avatar) }}"
                                             alt="{{ $record->user->name }}" class="att-avatar">
                                    @else
                                        <span class="att-avatar-initial">
                                            {{ strtoupper(mb_substr($record->user->name ?? 'N', 0, 1)) }}
                                        </span>
                                    @endif
                                    <div>
                                        <span class="att-identity__name">{{ $record->user->name ?? '—' }}</span>
                                        <span class="att-identity__role">
                                            {{ $roleLabels[$record->user->role ?? ''] ?? ($record->user->role ?? '—') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Ngày --}}
                            <td>
                                <strong>{{ optional($record->work_date)->format('d/m/Y') }}</strong>
                                <br>
                                <span style="font-size:11px; color:#94a3b8;">
                                    {{ optional($record->work_date)->isoFormat('dddd') }}
                                </span>
                            </td>

                            {{-- Ca làm --}}
                            <td>{{ $record->shift_label }}</td>

                            {{-- Giờ vào --}}
                            <td>
                                @if ($record->check_in_at)
                                    <div class="att-time">
                                        <span class="att-time__value">{{ $record->check_in_at->format('H:i') }}</span>
                                        @if ($record->late_minutes && $record->late_minutes > 0)
                                            <span class="att-late-badge">+{{ $record->late_minutes }} phút</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="att-time--empty">—</span>
                                @endif
                            </td>

                            {{-- Giờ ra --}}
                            <td>
                                @if ($record->check_out_at)
                                    <div class="att-time">
                                        <span class="att-time__value">{{ $record->check_out_at->format('H:i') }}</span>
                                    </div>
                                @else
                                    <span class="att-time--empty">Chưa ra</span>
                                @endif
                            </td>

                            {{-- Giờ công --}}
                            <td>
                                @if ($record->working_hours !== null)
                                    <span class="att-hours">{{ number_format($record->working_hours, 1) }}h</span>
                                @else
                                    <span class="att-time--empty">—</span>
                                @endif
                            </td>

                            {{-- Trạng thái --}}
                            <td>
                                <span class="att-status att-status--{{ $record->status }}">
                                    <span class="att-status__dot"></span>
                                    {{ $statusLabels[$record->status] ?? $record->status }}
                                </span>
                            </td>

                            {{-- Vị trí --}}
                            <td>{{ $record->work_location ?: '—' }}</td>

                            {{-- Thao tác --}}
                            <td>
                                <div class="att-action-wrap">
                                    <a href="{{ portal_route('attendance.show', $record->id) }}"
                                       class="btn-att-view" title="Xem chi tiết">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>
                                    <a href="{{ portal_route('attendance.edit', $record->id) }}"
                                       class="btn-att-edit" title="Chỉnh sửa">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                        Sửa
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="attendance-empty">
                                    <svg class="attendance-empty__icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                        <line x1="16" y1="2" x2="16" y2="6"/>
                                        <line x1="8" y1="2" x2="8" y2="6"/>
                                        <line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    <p class="attendance-empty__title">Không có bản ghi chấm công</p>
                                    <p class="attendance-empty__sub">Thử thay đổi bộ lọc hoặc thêm bản ghi mới.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="attendance-table-footer">
            <span>Hiển thị {{ $records->count() }} / {{ $records->total() }} bản ghi</span>
            <div>{{ $records->appends(request()->query())->links() }}</div>
        </div>
    </div>

</div>
@endsection
