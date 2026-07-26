@extends('layouts.admin.master')

@section('page_title', 'Xuất báo cáo chấm công')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', strtoupper(auth()->user()->role ?? 'ADMIN'))

@push('styles')
    @vite(['resources/css/pages/admin/attendance/index.css'])
@endpush

@php
    $statusLabels = [
        'working'  => 'Đang làm',
        'present'  => 'Đúng giờ',
        'late'     => 'Trễ',
        'absent'   => 'Vắng',
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

    // Tạo danh sách các ngày trong tháng
    $daysInMonth = \Carbon\Carbon::createFromDate($year, $mon, 1)->daysInMonth;
@endphp

@section('content')
<div class="attendance-page">

    {{-- ── Header ── --}}
    <div class="attendance-page__header">
        <div>
            <p class="attendance-page__eyebrow">Nhân sự › Chấm công</p>
            <h1>Báo cáo tháng {{ sprintf('%02d', $mon) }}/{{ $year }}</h1>
        </div>
        <div class="attendance-page__actions">
            <button onclick="window.print()" class="att-btn att-btn--secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                In / Xuất PDF
            </button>
            <a href="{{ portal_route('attendance.index') }}" class="att-btn att-btn--secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Quay lại
            </a>
        </div>
    </div>

    {{-- ── Filter tháng ── --}}
    <div class="att-form-card" style="padding: 20px;">
        <form method="GET" action="{{ portal_route('attendance.export') }}"
              style="display:flex; gap:14px; align-items:flex-end; flex-wrap:wrap;">
            <label class="attendance-filter__field" style="flex: 1 1 180px;">
                <span>Tháng</span>
                <input type="month" name="month" value="{{ $month }}" class="att-form-control">
            </label>
            <label class="attendance-filter__field" style="flex: 2 1 220px;">
                <span>Nhân viên</span>
                <select name="user_id" class="att-form-control">
                    <option value="">Tất cả nhân viên</option>
                    @foreach ($staffList as $staff)
                        <option value="{{ $staff->id }}" @selected($userId == $staff->id)>
                            {{ $staff->name }} ({{ $roleLabels[$staff->role] ?? $staff->role }})
                        </option>
                    @endforeach
                </select>
            </label>
            <div style="margin-top:18px;">
                <button type="submit" class="att-btn att-btn--primary">Xem báo cáo</button>
            </div>
        </form>
    </div>

    {{-- ── Báo cáo theo từng nhân viên ── --}}
    @forelse ($grouped as $uid => $userRecords)
        @php
            $employee = $userRecords->first()->user;
            $totalPresent  = $userRecords->whereIn('status', ['present'])->count();
            $totalLate     = $userRecords->where('status', 'late')->count();
            $totalAbsent   = $userRecords->where('status', 'absent')->count();
            $totalHalfDay  = $userRecords->where('status', 'half_day')->count();
            $totalWorking  = $userRecords->where('status', 'working')->count();
            $totalHours    = $userRecords->sum('working_hours');
        @endphp

        <div class="attendance-table-card" style="margin-bottom: 24px;">
            {{-- Employee header --}}
            <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid #e8edf5; background:#f8faff; flex-wrap:wrap; gap:12px;">
                <div style="display:flex; align-items:center; gap:14px;">
                    @if ($employee && $employee->avatar)
                        <img src="{{ asset('storage/' . $employee->avatar) }}"
                             style="width:44px; height:44px; border-radius:50%; object-fit:cover; border:2px solid #bfdbfe;">
                    @else
                        <div style="width:44px; height:44px; border-radius:50%; background:linear-gradient(135deg,#2563eb,#1d4ed8); color:#fff; font-weight:700; font-size:18px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            {{ strtoupper(mb_substr(optional($employee)->name ?? 'N', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div style="font-size:16px; font-weight:700; color:var(--color-text);">{{ optional($employee)->name }}</div>
                        <div style="font-size:13px; color:#64748b;">
                            {{ $roleLabels[optional($employee)->role] ?? optional($employee)->role }}
                        </div>
                    </div>
                </div>

                {{-- Summary pills --}}
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <span style="background:#f0fdf4; color:#16a34a; padding:4px 12px; border-radius:9999px; font-size:12px; font-weight:600;">
                        ✓ Đúng giờ: {{ $totalPresent }}
                    </span>
                    <span style="background:#fff7ed; color:#ea580c; padding:4px 12px; border-radius:9999px; font-size:12px; font-weight:600;">
                        ⏱ Trễ: {{ $totalLate }}
                    </span>
                    <span style="background:#fef2f2; color:#dc2626; padding:4px 12px; border-radius:9999px; font-size:12px; font-weight:600;">
                        ✗ Vắng: {{ $totalAbsent }}
                    </span>
                    <span style="background:#f5f3ff; color:#7c3aed; padding:4px 12px; border-radius:9999px; font-size:12px; font-weight:600;">
                        ½ Nửa ngày: {{ $totalHalfDay }}
                    </span>
                    <span style="background:#f0fdfa; color:#0d9488; padding:4px 12px; border-radius:9999px; font-size:12px; font-weight:600;">
                        ⏰ Tổng: {{ number_format($totalHours, 1) }}h
                    </span>
                </div>
            </div>

            {{-- Records table --}}
            <div class="attendance-table-wrap">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Ngày</th>
                            <th>Ca</th>
                            <th>Vị trí</th>
                            <th>Giờ vào</th>
                            <th>Giờ ra</th>
                            <th>Giờ công</th>
                            <th>Đi muộn</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($userRecords->sortBy('work_date') as $record)
                        <tr>
                            <td>
                                <strong>{{ $record->work_date->format('d/m') }}</strong>
                                <span style="font-size:11px; color:#94a3b8; margin-left:4px;">
                                    {{ $record->work_date->isoFormat('ddd') }}
                                </span>
                            </td>
                            <td>{{ $record->shift_label }}</td>
                            <td>{{ $record->work_location ?: '—' }}</td>
                            <td>{{ optional($record->check_in_at)->format('H:i') ?: '—' }}</td>
                            <td>{{ optional($record->check_out_at)->format('H:i') ?: '—' }}</td>
                            <td>
                                @if ($record->working_hours !== null)
                                    {{ number_format($record->working_hours, 1) }}h
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($record->late_minutes && $record->late_minutes > 0)
                                    <span class="att-late-badge">{{ $record->late_minutes }} phút</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <span class="att-status att-status--{{ $record->status }}" style="font-size:11px; padding:3px 9px;">
                                    <span class="att-status__dot"></span>
                                    {{ $statusLabels[$record->status] ?? $record->status }}
                                </span>
                            </td>
                            <td style="max-width:160px; font-size:13px; color:#64748b;">
                                {{ Str::limit($record->note, 40) ?: '—' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="attendance-table-card">
            <div class="attendance-empty">
                <svg class="attendance-empty__icon" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <p class="attendance-empty__title">Không có dữ liệu chấm công</p>
                <p class="attendance-empty__sub">Tháng {{ sprintf('%02d', $mon) }}/{{ $year }} chưa có bản ghi nào.</p>
            </div>
        </div>
    @endforelse

</div>

@push('scripts')
<style>
    @media print {
        .dashboard-sidebar, .dashboard-main > header,
        .attendance-page__actions, .att-form-card form { display: none !important; }
        .attendance-page { padding: 0; }
        .attendance-table-card { box-shadow: none; border: 1px solid #ccc; }
    }
</style>
@endpush

@endsection
