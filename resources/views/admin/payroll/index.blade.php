@extends('layouts.admin.master')

@section('page_title', 'Quản lý Bảng lương nhân viên')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', strtoupper(auth()->user()->role ?? 'ADMIN'))

@push('styles')
    @vite(['resources/css/pages/admin/payroll/index.css'])
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
@endphp

@section('content')
<div class="payroll-page">

    {{-- ── Header ── --}}
    <div class="payroll-page__header">
        <div>
            <p class="payroll-page__eyebrow">Nhân sự › Bảng lương</p>
            <h1>Bảng lương tháng {{ $mon }}/{{ $year }}</h1>
        </div>
        <div class="payroll-page__actions">
            @if(auth()->user()->role === 'admin')
                <a href="{{ portal_route('payroll.config.index') }}" class="pr-btn pr-btn--secondary">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                    Cấu hình lương
                </a>
            @endif

            <a href="{{ portal_route('payroll.report') }}?year={{ $year }}" class="pr-btn pr-btn--secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
                </svg>
                Báo cáo thống kê
            </a>

            @if(auth()->user()->role === 'admin')
                <form method="POST" action="{{ portal_route('payroll.generate') }}" style="display:inline;"
                      onsubmit="return confirm('Hệ thống sẽ tổng hợp chấm công tháng {{ $mon }}/{{ $year }} để sinh bảng lương. Bạn có muốn tiếp tục?')">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <button type="submit" class="pr-btn pr-btn--primary">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
                        </svg>
                        Sinh bảng lương tháng này
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ── Alerts ── --}}
    @if (session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:500;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if (session('info'))
        <div style="background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:500;">
            ℹ {{ session('info') }}
        </div>
    @endif
    @if ($errors->any())
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:500;">
            ⚠ {{ $errors->first() }}
        </div>
    @endif

    {{-- ── Stats Cards ── --}}
    <div class="payroll-stats-grid">
        <div class="pr-stat-card">
            <div class="pr-stat-card__icon pr-stat-card__icon--blue">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="pr-stat-card__val">{{ $stats['total_count'] }}</div>
                <div class="pr-stat-card__lbl">Nhân sự trong bảng lương</div>
            </div>
        </div>

        <div class="pr-stat-card">
            <div class="pr-stat-card__icon pr-stat-card__icon--amber">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            <div>
                <div class="pr-stat-card__val">{{ $stats['approved_count'] }} / {{ $stats['total_count'] }}</div>
                <div class="pr-stat-card__lbl">Đã duyệt bảng lương</div>
            </div>
        </div>

        <div class="pr-stat-card">
            <div class="pr-stat-card__icon pr-stat-card__icon--green">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="4" width="20" height="16" rx="2"/><line x1="6" y1="12" x2="18" y2="12"/>
                </svg>
            </div>
            <div>
                <div class="pr-stat-card__val">{{ $stats['paid_count'] }}</div>
                <div class="pr-stat-card__lbl">Đã thanh toán</div>
            </div>
        </div>

        <div class="pr-stat-card">
            <div class="pr-stat-card__icon pr-stat-card__icon--purple">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
            <div>
                <div class="pr-stat-card__val">{{ number_format($stats['total_payroll']) }} đ</div>
                <div class="pr-stat-card__lbl">Tổng quỹ lương thực lĩnh</div>
            </div>
        </div>
    </div>

    {{-- ── Form Lọc ── --}}
    <form class="payroll-filter" method="GET" action="{{ portal_route('payroll.index') }}">
        <div class="payroll-filter__field">
            <span>Chọn Tháng/Năm</span>
            <input type="month" name="month" value="{{ $month }}" class="pr-form-control" onchange="this.form.submit()">
        </div>

        <div class="payroll-filter__field">
            <span>Tìm kiếm</span>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Tên hoặc email…" class="pr-form-control">
        </div>

        <div class="payroll-filter__field">
            <span>Vai trò</span>
            <select name="role" class="pr-form-control">
                <option value="">Tất cả vai trò</option>
                @foreach ($roleLabels as $rv => $rl)
                    <option value="{{ $rv }}" @selected(request('role') === $rv)>{{ $rl }}</option>
                @endforeach
            </select>
        </div>

        <div class="payroll-filter__field">
            <span>Trạng thái duyệt</span>
            <select name="status" class="pr-form-control">
                <option value="">Tất cả trạng thái</option>
                <option value="nhap" @selected(request('status') === 'nhap')>Bản nháp</option>
                <option value="cho_duyet" @selected(request('status') === 'cho_duyet')>Chờ duyệt</option>
                <option value="da_duyet" @selected(request('status') === 'da_duyet')>Đã duyệt</option>
            </select>
        </div>

        <div style="display:flex; gap:8px;">
            <button type="submit" class="pr-btn pr-btn--primary" style="height:40px;">Lọc</button>
            <a href="{{ portal_route('payroll.index') }}" class="pr-btn pr-btn--secondary" style="height:40px;">Xóa lọc</a>
        </div>
    </form>

    {{-- ── Table ── --}}
    <div class="payroll-table-card">
        <div class="payroll-table-wrap">
            <table class="payroll-table">
                <thead>
                    <tr>
                        <th>Nhân viên</th>
                        <th>Lương cơ bản</th>
                        <th>Công thực tế</th>
                        <th>Lương công</th>
                        <th>Phụ cấp + Thưởng</th>
                        <th>Khấu trừ</th>
                        <th>Thực lĩnh</th>
                        <th>Trạng thái duyệt</th>
                        <th>Thanh toán</th>
                        <th style="text-align:right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payrolls as $pl)
                        @php
                            $user = $pl->user;
                            $isPaid = optional($pl->thanhToan)->trang_thai === 'da_thanh_toan';
                        @endphp
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <span style="width:34px; height:34px; border-radius:50%; background:#e2e8f0; color:#334155; font-weight:600; display:flex; align-items:center; justify-content:center; font-size:13px;">
                                        {{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}
                                    </span>
                                    <div>
                                        <a href="{{ portal_route('payroll.history', $user->id) }}" style="font-weight:600; color:#0f172a; text-decoration:none;">
                                            {{ $user->name }}
                                        </a>
                                        <div style="font-size:12px; color:#64748b;">{{ $roleLabels[$user->role] ?? $user->role }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>{{ number_format($pl->luong_co_ban) }} đ</td>
                            <td>
                                <strong>{{ $pl->so_ngay_cong_thuc_te }}</strong> / {{ $pl->so_ngay_cong_chuan }}
                                @if($pl->so_gio_ot > 0)
                                    <span style="font-size:11px; color:#16a34a; font-weight:600; display:block;" title="Thường: {{ $pl->so_gio_ot_thuong }}h | T7/CN: {{ $pl->so_gio_ot_cuoi_tuan }}h | Lễ: {{ $pl->so_gio_ot_ngay_le }}h">
                                        +{{ $pl->so_gio_ot }}h OT
                                    </span>
                                @endif
                                @if($pl->canh_bao_ot)
                                    <span style="font-size:10px; background:#fef2f2; color:#dc2626; border:1px solid #fecaca; border-radius:4px; padding:1px 5px; font-weight:700; display:inline-block; margin-top:2px;">
                                        ⚠ Vượt 40h OT
                                    </span>
                                @endif
                            </td>
                            <td>{{ number_format($pl->tien_luong_theo_cong + $pl->tien_ot) }} đ</td>
                            <td style="color:#16a34a;">+{{ number_format($pl->tong_phu_cap + $pl->tong_thuong) }} đ</td>
                            <td style="color:#dc2626;">-{{ number_format($pl->tong_khau_tru) }} đ</td>
                            <td style="font-size:15px; font-weight:700; color:#1d4ed8;">
                                {{ number_format($pl->thuc_linh) }} đ
                            </td>

                            <td>
                                <span class="pr-badge pr-badge--{{ $pl->trang_thai_duyet }}">
                                    {{ $pl->trang_thai_duyet_label }}
                                </span>
                            </td>

                            <td>
                                @if ($isPaid)
                                    <span class="pr-badge pr-badge--da_thanh_toan">✓ Đã thanh toán</span>
                                @else
                                    <span style="font-size:12px; color:#94a3b8;">Chưa thanh toán</span>
                                @endif
                            </td>

                            <td style="text-align:right;">
                                <div style="display:flex; justify-content:flex-end; gap:6px;">
                                    <a href="{{ portal_route('payroll.show', $pl->id) }}" class="pr-btn pr-btn--secondary" style="height:32px; padding:0 10px; font-size:12px;">
                                        Xem phiếu
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center; padding:40px; color:#64748b;">
                                Bảng lương tháng {{ $mon }}/{{ $year }} chưa được khởi tạo.<br>
                                @if(auth()->user()->role === 'admin')
                                    Nhấn nút <strong>"Sinh bảng lương tháng này"</strong> ở trên để bắt đầu.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payrolls->hasPages())
            <div style="padding:16px 20px; border-top:1px solid #f1f5f9;">
                {{ $payrolls->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
