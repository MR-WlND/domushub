@extends('layouts.admin.master')

@section('page_title', 'Chi tiết chấm công')
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

    <div class="attendance-page__header">
        <div>
            <p class="attendance-page__eyebrow">Nhân sự › Chấm công</p>
            <h1>Chi tiết bản ghi chấm công</h1>
        </div>
        <div class="attendance-page__actions">
            <a href="{{ portal_route('attendance.edit', $record->id) }}" class="att-btn att-btn--primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Chỉnh sửa
            </a>
            <a href="{{ portal_route('attendance.index') }}" class="att-btn att-btn--secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    {{-- Employee banner --}}
    <div class="att-employee-banner">
        @if ($record->user && $record->user->avatar)
            <img src="{{ asset('storage/' . $record->user->avatar) }}"
                 alt="{{ $record->user->name }}" class="att-employee-banner__avatar">
        @else
            <div class="att-employee-banner__initial">
                {{ strtoupper(mb_substr(optional($record->user)->name ?? 'N', 0, 1)) }}
            </div>
        @endif
        <div>
            <div class="att-employee-banner__name">{{ optional($record->user)->name }}</div>
            <div class="att-employee-banner__meta">
                {{ $roleLabels[optional($record->user)->role] ?? optional($record->user)->role }}
                &nbsp;|&nbsp; {{ optional($record->user)->email }}
            </div>
        </div>
    </div>

    <div class="att-form-card">

        {{-- Info grid --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 28px;">

            <div>
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:6px;">Ngày làm việc</div>
                <div style="font-size:17px; font-weight:700; color:var(--color-text);">{{ $record->work_date->format('d/m/Y') }}</div>
                <div style="font-size:13px; color:#64748b;">{{ $record->work_date->isoFormat('dddd') }}</div>
            </div>

            <div>
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:6px;">Ca làm việc</div>
                <div style="font-size:17px; font-weight:700; color:var(--color-text);">{{ $record->shift_label }}</div>
            </div>

            <div>
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:6px;">Vị trí</div>
                <div style="font-size:17px; font-weight:700; color:var(--color-text);">{{ $record->work_location ?: '—' }}</div>
            </div>

            <div>
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:6px;">Trạng thái</div>
                <span class="att-status att-status--{{ $record->status }}" style="font-size:14px;">
                    <span class="att-status__dot"></span>
                    {{ $statusLabels[$record->status] ?? $record->status }}
                </span>
            </div>

        </div>

        <div style="border-top: 1px solid #f1f5f9; padding-top: 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 24px; margin-bottom: 28px;">

            <div>
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:6px;">Giờ vào</div>
                @if ($record->check_in_at)
                    <div style="font-size:28px; font-weight:700; color:var(--color-text); font-variant-numeric: tabular-nums;">
                        {{ $record->check_in_at->format('H:i') }}
                    </div>
                    @if ($record->late_minutes && $record->late_minutes > 0)
                        <span class="att-late-badge" style="margin-top:6px; display:inline-block;">Trễ {{ $record->late_minutes }} phút</span>
                    @endif
                @else
                    <div style="color:#94a3b8; font-style:italic;">Chưa check-in</div>
                @endif
            </div>

            <div>
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:6px;">Giờ ra</div>
                @if ($record->check_out_at)
                    <div style="font-size:28px; font-weight:700; color:var(--color-text); font-variant-numeric: tabular-nums;">
                        {{ $record->check_out_at->format('H:i') }}
                    </div>
                @else
                    <div style="color:#3b82f6; font-weight:600; font-style:italic;">Đang làm việc…</div>
                @endif
            </div>

            <div>
                <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:6px;">Tổng giờ công</div>
                @if ($record->working_hours !== null)
                    <div style="font-size:28px; font-weight:700; color:#16a34a; font-variant-numeric: tabular-nums;">
                        {{ number_format($record->working_hours, 1) }}<span style="font-size:16px; font-weight:600; color:#64748b;">h</span>
                    </div>
                @else
                    <div style="color:#94a3b8; font-style:italic;">—</div>
                @endif
            </div>

        </div>

        @if ($record->note)
        <div style="border-top: 1px solid #f1f5f9; padding-top: 20px; margin-bottom: 20px;">
            <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#94a3b8; margin-bottom:8px;">Ghi chú</div>
            <div style="font-size:14px; color:var(--color-text); line-height:1.6; background:#f8faff; border-radius:8px; padding:14px 16px;">
                {{ $record->note }}
            </div>
        </div>
        @endif

        <div style="border-top: 1px solid #f1f5f9; padding-top: 16px; font-size:13px; color:#94a3b8; display:flex; gap:24px; flex-wrap:wrap;">
            <span>Tạo lúc: {{ $record->created_at->format('H:i d/m/Y') }}</span>
            <span>Cập nhật: {{ $record->updated_at->format('H:i d/m/Y') }}</span>
            @if ($record->recorder)
                <span>Người sửa: <strong>{{ $record->recorder->name }}</strong></span>
            @endif
        </div>

    </div>

</div>
@endsection
