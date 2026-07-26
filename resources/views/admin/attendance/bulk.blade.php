@extends('layouts.admin.master')

@section('page_title', 'Chấm công nhanh — ' . \Carbon\Carbon::parse($date)->format('d/m/Y'))
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', strtoupper(auth()->user()->role ?? 'ADMIN'))

@push('styles')
    @vite(['resources/css/pages/admin/attendance/index.css'])
@endpush

@php
    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'manager'    => 'Quản lý',
        'staff'      => 'Kế toán',
        'technician' => 'Kỹ thuật',
        'security'   => 'An ninh',
        'cleaning'   => 'Vệ sinh',
    ];

    $statusOptions = [
        ''         => '— chọn —',
        'working'  => 'Đang làm việc',
        'present'  => 'Đúng giờ',
        'late'     => 'Đi trễ',
        'absent'   => 'Vắng mặt',
        'half_day' => 'Nửa ngày',
    ];

    $startTime = config('attendance.start_time', '08:00');
    $endTime   = config('attendance.end_time', '17:00');
    $dateLabel = \Carbon\Carbon::parse($date)->isoFormat('dddd, DD/MM/YYYY');
@endphp

@section('content')
<div class="attendance-page">

    {{-- ── Header ── --}}
    <div class="attendance-page__header">
        <div>
            <p class="attendance-page__eyebrow">Nhân sự › Chấm công</p>
            <h1>Chấm công nhanh</h1>
            <p style="margin:4px 0 0; color:#64748b; font-size:14px;">{{ $dateLabel }}</p>
        </div>
        <div class="attendance-page__actions">
            <a href="{{ portal_route('attendance.index') }}" class="att-btn att-btn--secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Xem danh sách
            </a>
            <a href="{{ portal_route('attendance.create') }}" class="att-btn att-btn--secondary">
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

    {{-- ── Chọn ngày ── --}}
    <div class="att-form-card" style="padding: 18px 22px;">
        <form method="GET" action="{{ portal_route('attendance.bulk') }}"
              style="display:flex; align-items:flex-end; gap:14px; flex-wrap:wrap;">
            <label class="attendance-filter__field" style="flex:1 1 180px; min-width:160px;">
                <span>Chọn ngày điểm danh</span>
                <input type="date" name="date" value="{{ $date }}" class="att-form-control" max="{{ today()->toDateString() }}">
            </label>
            <div style="margin-top:18px;">
                <button type="submit" class="att-btn att-btn--secondary">Chuyển ngày</button>
            </div>
        </form>
    </div>

    {{-- ── Info banner ── --}}
    <div style="background:linear-gradient(135deg,#eff6ff,#dbeafe); border:1px solid #bfdbfe; border-radius:12px; padding:16px 22px; display:flex; gap:24px; align-items:center; flex-wrap:wrap;">
        <div style="display:flex; align-items:center; gap:8px; font-size:14px; color:#1d4ed8;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            <span>Giờ chuẩn vào: <strong>{{ $startTime }}</strong></span>
        </div>
        <div style="display:flex; align-items:center; gap:8px; font-size:14px; color:#1d4ed8;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            <span>Giờ chuẩn ra: <strong>{{ $endTime }}</strong></span>
        </div>
        <div style="font-size:13px; color:#3b82f6; margin-left:auto; font-style:italic;">
            Hệ thống tự tính giờ muộn và trạng thái khi bạn lưu.
        </div>
    </div>

    {{-- ── Form bulk ── --}}
    <form method="POST" action="{{ portal_route('attendance.bulk.store') }}" id="bulk-form">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="attendance-table-card">
            <div class="attendance-table-wrap">
                <table class="attendance-table" id="bulk-table">
                    <thead>
                        <tr>
                            <th style="min-width:200px;">Nhân viên</th>
                            <th>Trạng thái <span style="color:#ef4444">*</span></th>
                            <th>Giờ vào</th>
                            <th>Giờ ra</th>
                            <th>Ca làm</th>
                            <th>Vị trí</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($staffList as $staff)
                            @php
                                $rec = $existing->get($staff->id);
                                $uid = $staff->id;
                            @endphp
                            <tr class="{{ $rec ? 'bulk-row--recorded' : '' }}" id="row-{{ $uid }}">

                                {{-- Nhân viên --}}
                                <td>
                                    <div class="att-identity">
                                        @if ($staff->avatar)
                                            <img src="{{ asset('storage/' . $staff->avatar) }}"
                                                 alt="{{ $staff->name }}" class="att-avatar">
                                        @else
                                            <span class="att-avatar-initial" style="width:34px;height:34px;font-size:13px;">
                                                {{ strtoupper(mb_substr($staff->name, 0, 1)) }}
                                            </span>
                                        @endif
                                        <div>
                                            <span class="att-identity__name" style="font-size:13px;">{{ $staff->name }}</span>
                                            <span class="att-identity__role" style="font-size:11px;">
                                                {{ $roleLabels[$staff->role] ?? $staff->role }}
                                            </span>
                                            @if ($rec)
                                                <span style="font-size:10px; color:#16a34a; font-weight:600; display:block;">✓ Đã có bản ghi</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Trạng thái --}}
                                <td>
                                    <select name="records[{{ $uid }}][status]"
                                            class="att-form-control bulk-status-select"
                                            style="height:38px; min-width:140px; font-size:13px;"
                                            data-uid="{{ $uid }}"
                                            onchange="onStatusChange(this)">
                                        @foreach ($statusOptions as $val => $label)
                                            <option value="{{ $val }}"
                                                    @selected(($rec ? $rec->status : '') === $val)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- Giờ vào --}}
                                <td>
                                    <input type="time" name="records[{{ $uid }}][check_in_at]"
                                           class="att-form-control"
                                           style="height:38px; width:110px; font-size:13px;"
                                           value="{{ optional($rec?->check_in_at)->format('H:i') ?? $startTime }}">
                                </td>

                                {{-- Giờ ra --}}
                                <td>
                                    <input type="time" name="records[{{ $uid }}][check_out_at]"
                                           class="att-form-control"
                                           style="height:38px; width:110px; font-size:13px;"
                                           value="{{ optional($rec?->check_out_at)->format('H:i') ?? '' }}">
                                </td>

                                {{-- Ca làm --}}
                                <td>
                                    <select name="records[{{ $uid }}][shift]"
                                            class="att-form-control"
                                            style="height:38px; font-size:13px; min-width:100px;">
                                        @foreach ($shifts as $sv => $sl)
                                            <option value="{{ $sv }}"
                                                    @selected(($rec?->shift ?? 'full_day') === $sv)>
                                                {{ $sl }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- Vị trí --}}
                                <td>
                                    <select name="records[{{ $uid }}][work_location]"
                                            class="att-form-control"
                                            style="height:38px; font-size:13px; min-width:110px;">
                                        <option value="">—</option>
                                        @foreach ($workLocations as $loc)
                                            <option value="{{ $loc }}"
                                                    @selected(($rec?->work_location ?? '') === $loc)>
                                                {{ $loc }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>

                                {{-- Ghi chú --}}
                                <td>
                                    <input type="text" name="records[{{ $uid }}][note]"
                                           class="att-form-control"
                                           style="height:38px; width:140px; font-size:13px;"
                                           placeholder="Ghi chú…"
                                           value="{{ $rec?->note ?? '' }}">
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer actions --}}
            <div class="attendance-table-footer" style="padding:18px 22px; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                    {{-- Quick-fill buttons --}}
                    <span style="font-size:13px; color:#64748b; font-weight:500;">Điền nhanh:</span>
                    <button type="button" class="att-btn att-btn--secondary" style="height:36px; font-size:13px;"
                            onclick="fillAll('present')">
                        ✓ Tất cả đúng giờ
                    </button>
                    <button type="button" class="att-btn att-btn--secondary" style="height:36px; font-size:13px;"
                            onclick="fillAll('absent')">
                        ✗ Tất cả vắng
                    </button>
                    <button type="button" class="att-btn att-btn--secondary" style="height:36px; font-size:13px; color:#ea580c;"
                            onclick="clearAll()">
                        Xóa tất cả
                    </button>
                </div>
                <button type="submit" class="att-btn att-btn--primary" id="save-bulk-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Lưu toàn bộ chấm công
                </button>
            </div>
        </div>
    </form>

</div>

@push('scripts')
<style>
    .bulk-row--recorded { background: #f0fdf4 !important; }
    .bulk-row--recorded:hover td { background: #dcfce7 !important; }
    .bulk-row--absent td { opacity: .55; }
    .bulk-row--absent td input, .bulk-row--absent td select { pointer-events: none; }
</style>

<script>
    // Khi trạng thái = absent: disable các ô giờ
    function onStatusChange(sel) {
        const row = sel.closest('tr');
        if (sel.value === 'absent') {
            row.classList.add('bulk-row--absent');
        } else {
            row.classList.remove('bulk-row--absent');
        }
    }

    // Điền nhanh tất cả nhân viên cùng 1 trạng thái
    function fillAll(status) {
        document.querySelectorAll('.bulk-status-select').forEach(sel => {
            sel.value = status;
            onStatusChange(sel);
        });
    }

    // Xóa tất cả trạng thái về trống
    function clearAll() {
        document.querySelectorAll('.bulk-status-select').forEach(sel => {
            sel.value = '';
            onStatusChange(sel);
        });
    }

    // Áp dụng trạng thái ban đầu khi load
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.bulk-status-select').forEach(sel => onStatusChange(sel));

        // Confirm trước khi submit
        document.getElementById('save-bulk-btn').addEventListener('click', function (e) {
            const hasStatus = Array.from(document.querySelectorAll('.bulk-status-select'))
                .some(sel => sel.value !== '');
            if (!hasStatus) {
                e.preventDefault();
                alert('Vui lòng chọn trạng thái cho ít nhất một nhân viên.');
            }
        });
    });
</script>
@endpush

@endsection
