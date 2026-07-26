@extends('layouts.admin.master')

@section('page_title', 'Bảng Chấm Công Tổng Nhân Viên')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? '')
@section('user_role_label', strtoupper(auth()->user()->role ?? ''))

@push('styles')
    @vite(['resources/css/pages/admin/attendance/checkin.css'])
@endpush

@php
    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'manager'    => 'Quản lý BQL',
        'staff'      => 'Kế toán',
        'technician' => 'Kỹ thuật viên',
        'security'   => 'Bảo vệ',
        'cleaning'   => 'Vệ sinh',
    ];

    $statusLabels = [
        'working'  => 'Đang làm việc',
        'present'  => 'Đúng giờ',
        'late'     => 'Đi trễ',
        'absent'   => 'Vắng mặt',
        'half_day' => 'Nửa ngày',
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
            <h1>Bảng Chấm Công Tổng Nhân Viên</h1>
            <p style="margin:4px 0 0; color:#64748b; font-size:14px;">
                Theo dõi & Chấm công thời gian thực ngày {{ now()->isoFormat('dddd, DD/MM/YYYY') }}
            </p>
        </div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ portal_route('attendance.bulk') }}" class="checkin-btn checkin-btn--ghost">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
                Điểm danh hàng loạt
            </a>
            <a href="{{ portal_route('attendance.index') }}" class="checkin-btn checkin-btn--ghost">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                Bảng theo dõi lịch sử
            </a>
        </div>
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

    {{-- ── Hero Clock & Summary Stats ── --}}
    <div style="display:grid; grid-template-columns: 320px 1fr; gap:20px; align-items:stretch;">
        {{-- Clock hero --}}
        <div class="checkin-clock-card" style="padding:28px 24px; display:flex; flex-direction:column; justify-content:center;">
            <div class="checkin-clock-card__date" id="clockDate">{{ now()->isoFormat('dddd, DD/MM/YYYY') }}</div>
            <div class="checkin-clock-card__time" id="clockTime" style="font-size:54px;">{{ now()->format('H:i:s') }}</div>
            <div style="margin-top:14px; font-size:12px; color:rgba(255,255,255,0.7); text-align:center;">
                ⚡ Đồng hồ thời gian thực tòa nhà
            </div>
        </div>

        {{-- Stat Cards Grid --}}
        <div class="checkin-stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap:14px; margin:0;">
            <div class="checkin-stat-item checkin-stat-item--blue" style="display:flex; flex-direction:column; justify-content:center;">
                <div class="checkin-stat-item__val">{{ $todayStats['total'] }}</div>
                <div class="checkin-stat-item__lbl">Tổng nhân sự</div>
            </div>
            <div class="checkin-stat-item checkin-stat-item--green" style="display:flex; flex-direction:column; justify-content:center;">
                <div class="checkin-stat-item__val">{{ $todayStats['working'] }}</div>
                <div class="checkin-stat-item__lbl">Đang trong ca</div>
            </div>
            <div class="checkin-stat-item checkin-stat-item--blue" style="display:flex; flex-direction:column; justify-content:center; background:#f0fdfa;">
                <div class="checkin-stat-item__val" style="color:#0d9488;">{{ $todayStats['completed'] }}</div>
                <div class="checkin-stat-item__lbl">Đã xong ca</div>
            </div>
            <div class="checkin-stat-item checkin-stat-item--orange" style="display:flex; flex-direction:column; justify-content:center;">
                <div class="checkin-stat-item__val">{{ $todayStats['not_yet'] }}</div>
                <div class="checkin-stat-item__lbl">Chưa check-in</div>
            </div>
            <div class="checkin-stat-item checkin-stat-item--red" style="display:flex; flex-direction:column; justify-content:center;">
                <div class="checkin-stat-item__val">{{ $todayStats['absent'] }}</div>
                <div class="checkin-stat-item__lbl">Vắng mặt</div>
            </div>
        </div>
    </div>

    {{-- ── Admin Monitor Info Banner ── --}}
    @if(auth()->user()->role === 'admin')
        <div style="background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; padding:14px 18px; border-radius:12px; font-size:14px; margin-bottom:16px; display:flex; align-items:center; gap:10px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
            </svg>
            <span>
                <strong>Chế độ Giám sát (Admin):</strong> Bạn đang ở chế độ xem lịch sử & thời gian thực. Thao tác bấm Check-in / Check-out trực tiếp do <strong>Quản lý Ban Quản Lý (Manager)</strong> thực hiện khi nhân viên trình diện tại phòng BQL / Sảnh tòa nhà.
            </span>
        </div>
    @endif

    {{-- ── Filter bar ── --}}
    <form method="GET" action="{{ portal_route('attendance.checkin') }}" class="checkin-action-card" style="padding:16px 20px;">
        <div style="display:flex; gap:14px; flex-wrap:wrap; align-items:flex-end;">
            <div style="flex:2; min-width:200px;">
                <label class="checkin-form-label" style="margin-bottom:4px;">Tìm kiếm nhân viên</label>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Tên, email, số điện thoại…" class="checkin-form-control" style="height:38px;">
            </div>

            <div style="flex:1; min-width:150px;">
                <label class="checkin-form-label" style="margin-bottom:4px;">Bộ phận / Vai trò</label>
                <select name="role" class="checkin-form-control" style="height:38px;">
                    <option value="">Tất cả bộ phận</option>
                    @foreach($roleLabels as $rv => $rl)
                        <option value="{{ $rv }}" @selected(request('role') === $rv)>{{ $rl }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex; gap:8px;">
                <button type="submit" class="checkin-btn--ghost" style="height:38px; background:#2563eb; color:#fff; border:none;">Lọc</button>
                <a href="{{ portal_route('attendance.checkin') }}" class="checkin-btn--ghost" style="height:38px;">Xóa lọc</a>
            </div>
        </div>
    </form>

    {{-- ── Master Employees Table / Grid ── --}}
    <div class="checkin-action-card" style="padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #f1f5f9; background:#f8faff; font-weight:700; color:#334155; font-size:14px;">
            📋 Danh sách trạng thái ca trực hôm nay ({{ $staffList->count() }} nhân viên)
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <thead>
                    <tr style="background:#f8fafc; border-bottom:1px solid #e2e8f0; font-size:11px; text-transform:uppercase; color:#64748b; letter-spacing:.05em;">
                        <th style="padding:12px 18px; text-align:left;">Nhân viên</th>
                        <th style="padding:12px 18px; text-align:left;">Bộ phận</th>
                        <th style="padding:12px 18px; text-align:left;">Trạng thái ca hôm nay</th>
                        <th style="padding:12px 18px; text-align:left;">Giờ vào</th>
                        <th style="padding:12px 18px; text-align:left;">Giờ ra</th>
                        <th style="padding:12px 18px; text-align:left;">Ca & Vị trí</th>
                        <th style="padding:12px 18px; text-align:right;">Thao tác Chấm Công</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffList as $staff)
                        @php
                            $rec = $todayRecords->get($staff->id);
                            $isSelf = $staff->id === $currentUser->id;
                            $hasCheckedIn = $rec && $rec->check_in_at;
                            $hasCheckedOut = $rec && $rec->check_out_at;
                            $isWorking = $rec && $rec->status === 'working';
                        @endphp
                        <tr style="border-bottom:1px solid #f1f5f9; background: {{ $isSelf ? '#f0f9ff' : '#fff' }};">
                            {{-- Nhân viên --}}
                            <td style="padding:14px 18px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,#3b82f6,#1d4ed8); color:#fff; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                        @if($staff->avatar)
                                            <img src="{{ asset('storage/' . $staff->avatar) }}" alt="{{ $staff->name }}" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                                        @else
                                            {{ strtoupper(mb_substr($staff->name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight:700; color:#0f172a;">
                                            {{ $staff->name }}
                                            @if($isSelf)
                                                <span style="font-size:10px; background:#dbeafe; color:#1d4ed8; border-radius:4px; padding:1px 6px; margin-left:4px;">Bạn</span>
                                            @endif
                                        </div>
                                        <div style="font-size:12px; color:#64748b;">{{ $staff->email }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Bộ phận --}}
                            <td style="padding:14px 18px;">
                                <span style="font-size:12px; font-weight:600; color:#475569; background:#f1f5f9; padding:4px 10px; border-radius:6px;">
                                    {{ $roleLabels[$staff->role] ?? $staff->role }}
                                </span>
                            </td>

                            {{-- Trạng thái ca --}}
                            <td style="padding:14px 18px;">
                                @if(!$rec)
                                    <span class="checkin-badge checkin-badge--orange">Chưa check-in</span>
                                @elseif($rec->status === 'working')
                                    <span class="checkin-badge checkin-badge--blue" style="animation:pulse-blue 2s infinite;">🔵 Đang trong ca</span>
                                @elseif($rec->status === 'absent')
                                    <span class="checkin-badge checkin-badge--red">⚪ Vắng mặt</span>
                                @else
                                    <span class="checkin-badge checkin-badge--green">🟢 Đã xong ca ({{ number_format($rec->working_hours, 1) }}h)</span>
                                @endif
                            </td>

                            {{-- Giờ vào --}}
                            <td style="padding:14px 18px; font-weight:600;">
                                @if($rec && $rec->check_in_at)
                                    <span style="color:#0f172a;">{{ $rec->check_in_at->format('H:i') }}</span>
                                    @if($rec->late_minutes > 0)
                                        <span style="font-size:10px; color:#ea580c; display:block;">+{{ $rec->late_minutes }}p muộn</span>
                                    @endif
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>

                            {{-- Giờ ra --}}
                            <td style="padding:14px 18px; font-weight:600;">
                                @if($rec && $rec->check_out_at)
                                    <span style="color:#0f172a;">{{ $rec->check_out_at->format('H:i') }}</span>
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>

                            {{-- Ca & Vị trí --}}
                            <td style="padding:14px 18px; font-size:13px; color:#475569;">
                                @if($rec)
                                    <div>{{ $shifts[$rec->shift] ?? $rec->shift }}</div>
                                    @if($rec->work_location)
                                        <div style="font-size:11px; color:#64748b;">📍 {{ $rec->work_location }}</div>
                                    @endif
                                @else
                                    <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>

                            {{-- Thao tác Chấm Công --}}
                            <td style="padding:14px 18px; text-align:right;">
                                @if(auth()->user()->role === 'admin')
                                    <span style="font-size:12px; color:#64748b; font-style:italic;">Chỉ xem (Admin)</span>
                                @elseif(!$rec)
                                    <button type="button" class="checkin-btn--ghost" style="background:#16a34a; color:#fff; border:none; height:34px; font-size:12px;"
                                            onclick="openCheckinModal({{ json_encode($staff) }})">
                                        🟢 Check-In ngay
                                    </button>
                                @elseif($isWorking)
                                    <button type="button" class="checkin-btn--ghost" style="background:#dc2626; color:#fff; border:none; height:34px; font-size:12px;"
                                            onclick="openCheckoutModal({{ json_encode($staff) }}, {{ json_encode($rec) }})">
                                        🔴 Check-Out ngay
                                    </button>
                                @else
                                    <span style="font-size:12px; color:#16a34a; font-weight:600;">✓ Đã chốt ca</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:40px; color:#64748b;">
                                Không tìm thấy nhân viên nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ── Modal Quick Check-In ── --}}
<div id="quickCheckinModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.4); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; width:100%; max-width:440px; padding:24px; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
        <h3 style="margin-top:0; margin-bottom:16px; font-size:17px;" id="modalStaffTitle">Check-In cho Nhân viên</h3>
        <form method="POST" action="{{ portal_route('attendance.checkin.store') }}">
            @csrf
            <input type="hidden" name="user_id" id="checkin_user_id">

            <div style="margin-bottom:14px;">
                <label class="checkin-form-label">Ca làm việc <span class="req">*</span></label>
                <select name="shift" class="checkin-form-control" style="width:100%;" required>
                    @foreach($shifts as $key => $label)
                        <option value="{{ $key }}" {{ $key === 'full_day' ? 'selected' : '' }}>
                            {{ $shiftIcons[$key] ?? '🕐' }} {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:14px;">
                <label class="checkin-form-label">Vị trí trực (theo Block / Khu vực)</label>
                <select name="work_location" class="checkin-form-control" style="width:100%;">
                    <option value="">-- Chọn vị trí --</option>
                    @foreach($workLocations as $loc)
                        <option value="{{ $loc }}">{{ $loc }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:20px;">
                <label class="checkin-form-label">Ghi chú</label>
                <input type="text" name="note" class="checkin-form-control" style="width:100%;" placeholder="Ví dụ: Trực ca sáng tầng 1…">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="checkin-btn--ghost" onclick="document.getElementById('quickCheckinModal').style.display='none'">Hủy</button>
                <button type="submit" class="checkin-btn--ghost" style="background:#16a34a; color:#fff; border:none;">Xác nhận Check-In</button>
            </div>
        </form>
    </div>
</div>

{{-- ── Modal Quick Check-Out ── --}}
<div id="quickCheckoutModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.4); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; width:100%; max-width:440px; padding:24px; box-shadow:0 10px 40px rgba(0,0,0,0.15);">
        <h3 style="margin-top:0; margin-bottom:16px; font-size:17px;" id="modalCheckoutTitle">Check-Out cho Nhân viên</h3>
        <form method="POST" action="{{ portal_route('attendance.checkout') }}">
            @csrf
            <input type="hidden" name="user_id" id="checkout_user_id">

            <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#1d4ed8;" id="checkoutInfoBox">
                Giờ vào: --:--
            </div>

            <div style="margin-bottom:20px;">
                <label class="checkin-form-label">Ghi chú bàn giao / Kết thúc ca</label>
                <input type="text" name="note" class="checkin-form-control" style="width:100%;" placeholder="Ghi chú bàn giao ca, sự cố…">
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="checkin-btn--ghost" onclick="document.getElementById('quickCheckoutModal').style.display='none'">Hủy</button>
                <button type="submit" class="checkin-btn--ghost" style="background:#dc2626; color:#fff; border:none;">Xác nhận Check-Out</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

function openCheckinModal(staff) {
    document.getElementById('checkin_user_id').value = staff.id;
    document.getElementById('modalStaffTitle').textContent = `Check-In cho: ${staff.name}`;
    document.getElementById('quickCheckinModal').style.display = 'flex';
}

function openCheckoutModal(staff, rec) {
    document.getElementById('checkout_user_id').value = staff.id;
    document.getElementById('modalCheckoutTitle').textContent = `Check-Out cho: ${staff.name}`;
    const checkinTime = rec && rec.check_in_at ? rec.check_in_at.substring(11, 16) : '--:--';
    document.getElementById('checkoutInfoBox').innerHTML = `Giờ check-in vào ca: <strong>${checkinTime}</strong> — Đang chuyển sang kết thúc ca.`;
    document.getElementById('quickCheckoutModal').style.display = 'flex';
}
</script>
@endpush
