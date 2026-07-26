@extends('layouts.admin.master')

@section('page_title', 'Chấm công – Bắt đầu / Kết thúc ca')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? '')
@section('user_role_label', strtoupper(auth()->user()->role ?? ''))

@push('styles')
    @vite(['resources/css/pages/admin/attendance/checkin.css'])
@endpush

@php
    $user        = auth()->user();
    $hasCheckedIn  = $todayRecord && $todayRecord->check_in_at;
    $hasCheckedOut = $todayRecord && $todayRecord->check_out_at;
    $isWorking     = $todayRecord && $todayRecord->status === 'working';

    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'manager'    => 'Quản lý BQL',
        'staff'      => 'Kế toán',
        'technician' => 'Kỹ thuật viên',
        'security'   => 'Bảo vệ',
        'cleaning'   => 'Vệ sinh',
    ];

    $shiftIcons = [
        'full_day'  => '🏢',
        'morning'   => '🌅',
        'afternoon' => '🌆',
        'night'     => '🌙',
        'office'    => '💼',
    ];
@endphp

@section('content')
<div class="checkin-page">

    {{-- ── Header ── --}}
    <div class="checkin-page__header">
        <div>
            <p class="checkin-eyebrow">Nhân sự › Chấm công</p>
            <h1>Chấm công hôm nay</h1>
        </div>
        <a href="{{ portal_route('attendance.index') }}" class="checkin-btn checkin-btn--ghost">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="9 14 4 9 9 4"/><line x1="20" y1="9" x2="4" y2="9"/>
            </svg>
            Xem toàn bộ chấm công
        </a>
    </div>

    {{-- ── Alerts ── --}}
    @if (session('success'))
        <div class="checkin-alert checkin-alert--success">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="checkin-alert checkin-alert--danger">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    {{-- ── Main Grid ── --}}
    <div class="checkin-main-grid">

        {{-- ── LEFT: Clock + Check-in Card ── --}}
        <div class="checkin-left">

            {{-- Clock Hero Card --}}
            <div class="checkin-clock-card">
                <div class="checkin-clock-card__date" id="clockDate">{{ now()->isoFormat('dddd, DD/MM/YYYY') }}</div>
                <div class="checkin-clock-card__time" id="clockTime">{{ now()->format('H:i:s') }}</div>

                {{-- Status indicator --}}
                <div class="checkin-status-ring
                    @if($hasCheckedOut) checkin-status-ring--done
                    @elseif($isWorking) checkin-status-ring--working
                    @else checkin-status-ring--idle @endif">
                    @if($hasCheckedOut)
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <span>Ca đã hoàn thành</span>
                    @elseif($isWorking)
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>Đang làm việc</span>
                    @else
                        <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span>Chưa bắt đầu ca</span>
                    @endif
                </div>
            </div>

            {{-- Check-in / Check-out Action Card --}}
            @if(! $hasCheckedOut)
                @if(! $hasCheckedIn)
                    {{-- FORM CHECK-IN --}}
                    <div class="checkin-action-card">
                        <div class="checkin-action-card__title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                            </svg>
                            Bắt đầu ca làm việc
                        </div>

                        <form method="POST" action="{{ portal_route('attendance.checkin.store') }}" id="checkinForm">
                            @csrf
                            <div class="checkin-form-group">
                                <label class="checkin-form-label">Ca làm việc <span class="req">*</span></label>
                                <select name="shift" class="checkin-form-control" required>
                                    @foreach($shifts as $key => $label)
                                        <option value="{{ $key }}" {{ $key === 'full_day' ? 'selected' : '' }}>
                                            {{ $shiftIcons[$key] ?? '🕐' }} {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="checkin-form-group">
                                <label class="checkin-form-label">Vị trí làm việc</label>
                                <select name="work_location" class="checkin-form-control">
                                    <option value="">-- Chọn vị trí --</option>
                                    @foreach($workLocations as $loc)
                                        <option value="{{ $loc }}">{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="checkin-form-group">
                                <label class="checkin-form-label">Ghi chú (không bắt buộc)</label>
                                <input type="text" name="note" class="checkin-form-control"
                                       placeholder="Ví dụ: Hỗ trợ thêm tầng 2…" maxlength="300">
                            </div>

                            <button type="submit" class="checkin-btn-action checkin-btn-action--in" id="checkinBtn">
                                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                                </svg>
                                Bắt đầu ca ngay bây giờ
                            </button>
                        </form>
                    </div>

                @else
                    {{-- FORM CHECK-OUT --}}
                    <div class="checkin-action-card checkin-action-card--working">
                        <div class="checkin-action-card__title">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            Đang trong ca làm việc
                        </div>

                        <div class="checkin-working-info">
                            <div class="checkin-working-info__row">
                                <span>Giờ vào:</span>
                                <strong>{{ $todayRecord->check_in_at->format('H:i') }}</strong>
                            </div>
                            <div class="checkin-working-info__row">
                                <span>Ca làm:</span>
                                <strong>{{ $shifts[$todayRecord->shift] ?? $todayRecord->shift }}</strong>
                            </div>
                            @if($todayRecord->work_location)
                            <div class="checkin-working-info__row">
                                <span>Vị trí:</span>
                                <strong>{{ $todayRecord->work_location }}</strong>
                            </div>
                            @endif
                            @if($todayRecord->late_minutes > 0)
                            <div class="checkin-working-info__row checkin-working-info__row--warn">
                                <span>⚠ Đến trễ:</span>
                                <strong>{{ $todayRecord->late_minutes }} phút</strong>
                            </div>
                            @endif
                            <div class="checkin-working-info__row">
                                <span>Đã làm:</span>
                                <strong id="elapsedTime">—</strong>
                            </div>
                        </div>

                        <form method="POST" action="{{ portal_route('attendance.checkout') }}" id="checkoutForm"
                              onsubmit="return confirm('Xác nhận kết thúc ca làm việc?')">
                            @csrf
                            <div class="checkin-form-group">
                                <label class="checkin-form-label">Ghi chú bàn giao ca (không bắt buộc)</label>
                                <input type="text" name="note" class="checkin-form-control"
                                       placeholder="Ví dụ: Bàn giao cho ca chiều, sự cố cần xử lý…" maxlength="300">
                            </div>

                            <button type="submit" class="checkin-btn-action checkin-btn-action--out">
                                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                Kết thúc ca & Check-out
                            </button>
                        </form>
                    </div>
                @endif

            @else
                {{-- ĐÃ HOÀN THÀNH CA --}}
                <div class="checkin-action-card checkin-action-card--done">
                    <div class="checkin-done-icon">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="checkin-done-title">Ca hôm nay đã hoàn thành!</div>
                    <div class="checkin-done-meta">
                        <div>
                            <span>Giờ vào</span>
                            <strong>{{ $todayRecord->check_in_at->format('H:i') }}</strong>
                        </div>
                        <div class="checkin-done-meta__sep">→</div>
                        <div>
                            <span>Giờ ra</span>
                            <strong>{{ $todayRecord->check_out_at->format('H:i') }}</strong>
                        </div>
                        <div class="checkin-done-meta__sep">|</div>
                        <div>
                            <span>Tổng giờ</span>
                            <strong>{{ number_format($todayRecord->working_hours, 1) }}h</strong>
                        </div>
                    </div>
                    <p class="checkin-done-message">Cảm ơn bạn đã hoàn thành ca làm việc. Nghỉ ngơi vui vẻ! 🎉</p>
                </div>
            @endif

        </div>

        {{-- ── RIGHT: Stats + History ── --}}
        <div class="checkin-right">

            {{-- Nhân viên info card --}}
            <div class="checkin-profile-card">
                <div class="checkin-profile-card__avatar">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}">
                    @else
                        <span>{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="checkin-profile-card__info">
                    <div class="checkin-profile-card__name">{{ $user->name }}</div>
                    <div class="checkin-profile-card__role">{{ $roleLabels[$user->role] ?? $user->role }}</div>
                    @if($user->email)
                        <div class="checkin-profile-card__email">{{ $user->email }}</div>
                    @endif
                </div>
            </div>

            {{-- Thống kê tháng này --}}
            <div class="checkin-stats-card">
                <div class="checkin-stats-card__title">Thống kê tháng {{ now()->format('m/Y') }}</div>
                <div class="checkin-stats-grid">
                    <div class="checkin-stat-item checkin-stat-item--green">
                        <div class="checkin-stat-item__val">{{ $monthStats['present'] }}</div>
                        <div class="checkin-stat-item__lbl">Ngày công</div>
                    </div>
                    <div class="checkin-stat-item checkin-stat-item--red">
                        <div class="checkin-stat-item__val">{{ $monthStats['absent'] }}</div>
                        <div class="checkin-stat-item__lbl">Vắng mặt</div>
                    </div>
                    <div class="checkin-stat-item checkin-stat-item--orange">
                        <div class="checkin-stat-item__val">{{ $monthStats['late'] }}</div>
                        <div class="checkin-stat-item__lbl">Đi trễ</div>
                    </div>
                    <div class="checkin-stat-item checkin-stat-item--blue">
                        <div class="checkin-stat-item__val">{{ number_format($monthStats['total_hours'], 0) }}h</div>
                        <div class="checkin-stat-item__lbl">Tổng giờ</div>
                    </div>
                </div>
            </div>

            {{-- Lịch sử 7 ngày --}}
            <div class="checkin-history-card">
                <div class="checkin-history-card__title">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    7 ngày gần nhất
                </div>

                @php
                    $statusColors = [
                        'working'  => 'blue',
                        'present'  => 'green',
                        'late'     => 'orange',
                        'absent'   => 'red',
                        'half_day' => 'purple',
                    ];
                    $statusLabels = [
                        'working'  => 'Đang làm',
                        'present'  => 'Đúng giờ',
                        'late'     => 'Đi trễ',
                        'absent'   => 'Vắng mặt',
                        'half_day' => 'Nửa ngày',
                    ];
                @endphp

                <div class="checkin-history-list">
                    @forelse($recentRecords as $rec)
                        <div class="checkin-history-item">
                            <div class="checkin-history-item__date">
                                <strong>{{ $rec->work_date->format('d/m') }}</strong>
                                <span>{{ $rec->work_date->isoFormat('ddd') }}</span>
                            </div>
                            <div class="checkin-history-item__times">
                                @if($rec->check_in_at)
                                    <span>{{ $rec->check_in_at->format('H:i') }}</span>
                                    @if($rec->check_out_at)
                                        <span>→ {{ $rec->check_out_at->format('H:i') }}</span>
                                    @else
                                        <span class="muted">→ —</span>
                                    @endif
                                @else
                                    <span class="muted">Không có dữ liệu</span>
                                @endif
                            </div>
                            <div class="checkin-history-item__hours">
                                {{ $rec->working_hours ? number_format($rec->working_hours, 1) . 'h' : '—' }}
                            </div>
                            <span class="checkin-badge checkin-badge--{{ $statusColors[$rec->status] ?? 'blue' }}">
                                {{ $statusLabels[$rec->status] ?? $rec->status }}
                            </span>
                        </div>
                    @empty
                        <div class="checkin-history-empty">
                            <p>Chưa có lịch sử chấm công gần đây.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ── Đồng hồ realtime ──────────────────────────────────────────
function updateClock() {
    const now  = new Date();
    const hh   = String(now.getHours()).padStart(2, '0');
    const mm   = String(now.getMinutes()).padStart(2, '0');
    const ss   = String(now.getSeconds()).padStart(2, '0');
    const timeEl = document.getElementById('clockTime');
    if (timeEl) timeEl.textContent = `${hh}:${mm}:${ss}`;
}
setInterval(updateClock, 1000);
updateClock();

// ── Elapsed time (thời gian đã làm) ──────────────────────────
@if($hasCheckedIn && !$hasCheckedOut)
const checkInTime = new Date('{{ $todayRecord->check_in_at->toIso8601String() }}');
function updateElapsed() {
    const now     = new Date();
    const diffMs  = now - checkInTime;
    const hours   = Math.floor(diffMs / 3600000);
    const minutes = Math.floor((diffMs % 3600000) / 60000);
    const el = document.getElementById('elapsedTime');
    if (el) el.textContent = `${hours}h ${minutes}m`;
}
setInterval(updateElapsed, 30000);
updateElapsed();
@endif

// ── Prevent double submit ─────────────────────────────────────
document.querySelectorAll('#checkinForm, #checkoutForm').forEach(form => {
    form.addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.7';
        }
    });
});
</script>
@endpush
