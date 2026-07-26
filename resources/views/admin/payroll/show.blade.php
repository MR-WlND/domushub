@extends('layouts.admin.master')

@section('page_title', 'Phiếu lương chi tiết — ' . $bangLuong->user->name)
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', strtoupper(auth()->user()->role ?? 'ADMIN'))

@push('styles')
    @vite(['resources/css/pages/admin/payroll/index.css'])
    <style>
        .payslip-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:24px; margin-bottom:24px; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
        .payslip-header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #f1f5f9; padding-bottom:20px; margin-bottom:20px; }
        .payslip-grid { display:grid; grid-template-columns:1fr 1fr; gap:24px; }
        .payslip-sec-title { font-size:15px; font-weight:700; color:#0f172a; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center; }
        .payslip-table { width:100%; border-collapse:collapse; font-size:13px; }
        .payslip-table th { background:#f8fafc; padding:8px 12px; color:#475569; text-align:left; }
        .payslip-table td { padding:10px 12px; border-bottom:1px solid #f1f5f9; }
    </style>
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
    $isPaid = optional($bangLuong->thanhToan)->trang_thai === 'da_thanh_toan';
    $canEdit = $bangLuong->canRegenerate();
@endphp

@section('content')
<div class="payroll-page">

    {{-- Header --}}
    <div class="payroll-page__header">
        <div>
            <p class="payroll-page__eyebrow">
                <a href="{{ portal_route('payroll.index') }}?month={{ sprintf('%04d-%02d', $bangLuong->nam, $bangLuong->thang) }}" style="color:inherit; text-decoration:none;">
                    ‹ Quay lại danh sách
                </a>
            </p>
            <h1>Phiếu lương Tháng {{ $bangLuong->thang }}/{{ $bangLuong->nam }}</h1>
        </div>

        <div class="payroll-page__actions">
            <a href="{{ portal_route('payroll.export', $bangLuong->id) }}" target="_blank" class="pr-btn pr-btn--secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/>
                </svg>
                In phiếu lương
            </a>

            @if(auth()->user()->role === 'admin' && ! $bangLuong->isApproved())
                <form method="POST" action="{{ portal_route('payroll.approve', $bangLuong->id) }}" style="display:inline;"
                      onsubmit="return confirm('Xác nhận duyệt bảng lương này?')">
                    @csrf
                    <button type="submit" class="pr-btn pr-btn--success">
                        ✓ Duyệt bảng lương
                    </button>
                </form>
            @endif

            @if(auth()->user()->role === 'admin' && $bangLuong->isApproved() && ! $isPaid)
                <button type="button" class="pr-btn pr-btn--primary" onclick="document.getElementById('payModal').style.display='flex'">
                    💳 Xác nhận thanh toán
                </button>
            @endif
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:500;">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:500;">
            ⚠ {{ $errors->first() }}
        </div>
    @endif

    {{-- Main Payslip Card --}}
    <div class="payslip-card">
        <div class="payslip-header">
            <div>
                <h2 style="margin:0; font-size:20px; color:#0f172a;">{{ $bangLuong->user->name }}</h2>
                <p style="margin:4px 0 0; font-size:14px; color:#64748b;">
                    {{ $roleLabels[$bangLuong->user->role] ?? $bangLuong->user->role }} — {{ $bangLuong->user->email }}
                </p>
            </div>
            <div style="text-align:right;">
                <span class="pr-badge pr-badge--{{ $bangLuong->trang_thai_duyet }}" style="font-size:13px; padding:6px 14px;">
                    {{ $bangLuong->trang_thai_duyet_label }}
                </span>
                @if($isPaid)
                    <div style="margin-top:6px;" class="pr-badge pr-badge--da_thanh_toan">
                        ✓ Đã thanh toán vào {{ optional($bangLuong->thanhToan->ngay_thanh_toan)->format('d/m/Y H:i') }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Calculation Breakdown Grid --}}
        <div class="payslip-grid">

            {{-- Cột trái: Lương công + OT + Thưởng + Phụ cấp --}}
            <div>
                <div class="payslip-sec-title">
                    <span>1. Lương & Thu nhập thêm</span>
                </div>
                <table class="payslip-table">
                    <tbody>
                        <tr>
                            <td>Lương cơ bản hợp đồng</td>
                            <td style="text-align:right; font-weight:600;">{{ number_format($bangLuong->luong_co_ban) }} đ</td>
                        </tr>
                        <tr>
                            <td>Số ngày công thực tế ({{ $bangLuong->so_ngay_cong_thuc_te }}/{{ $bangLuong->so_ngay_cong_chuan }})</td>
                            <td style="text-align:right; font-weight:600;">{{ number_format($bangLuong->tien_luong_theo_cong) }} đ</td>
                        </tr>
                        <tr>
                            <td>
                                Lương làm thêm giờ (Tổng {{ $bangLuong->so_gio_ot }}h OT)
                                @if($bangLuong->canh_bao_ot)
                                    <span style="font-size:11px; color:#dc2626; font-weight:700; display:block;">⚠ Vượt trần 40h OT/tháng theo Luật Lao động</span>
                                @endif
                                @if($bangLuong->so_gio_ot > 0)
                                    <div style="font-size:11px; color:#64748b; margin-top:2px;">
                                        • Ngày thường (×1.5): {{ $bangLuong->so_gio_ot_thuong }}h<br>
                                        • Cuối tuần (×2.0): {{ $bangLuong->so_gio_ot_cuoi_tuan }}h<br>
                                        • Ngày lễ (×3.0): {{ $bangLuong->so_gio_ot_ngay_le }}h
                                    </div>
                                @endif
                            </td>
                            <td style="text-align:right; font-weight:600; color:#16a34a; vertical-align:top;">+{{ number_format($bangLuong->tien_ot) }} đ</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Chi tiết Phụ cấp --}}
                <div class="payslip-sec-title" style="margin-top:20px;">
                    <span>Phụ cấp (+{{ number_format($bangLuong->tong_phu_cap) }} đ)</span>
                </div>
                <table class="payslip-table">
                    <tbody>
                        @forelse($bangLuong->chiTietPhuCaps as $pc)
                            <tr>
                                <td>{{ $pc->danhMuc->ten_phu_cap }}</td>
                                <td style="text-align:right; font-weight:600; color:#16a34a;">+{{ number_format($pc->so_tien) }} đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="color:#94a3b8; font-style:italic;">Không có phụ cấp</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Chi tiết Thưởng --}}
                <div class="payslip-sec-title" style="margin-top:20px;">
                    <span>Khoản thưởng (+{{ number_format($bangLuong->tong_thuong) }} đ)</span>
                </div>
                <table class="payslip-table">
                    <tbody>
                        @forelse($bangLuong->chiTietThuongs as $th)
                            <tr>
                                <td>
                                    {{ $th->danhMuc->ten_thuong }}
                                    @if($th->ly_do)<span style="font-size:12px; color:#64748b; display:block;">{{ $th->ly_do }}</span>@endif
                                </td>
                                <td style="text-align:right; font-weight:600; color:#16a34a;">+{{ number_format($th->so_tien) }} đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="color:#94a3b8; font-style:italic;">Không có thưởng</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Cột phải: Khấu trừ & Tổng thực lĩnh --}}
            <div>
                <div class="payslip-sec-title">
                    <span>2. Khấu trừ (-{{ number_format($bangLuong->tong_khau_tru) }} đ)</span>
                </div>
                <table class="payslip-table">
                    <tbody>
                        @forelse($bangLuong->chiTietKhauTrus as $kt)
                            <tr>
                                <td>
                                    {{ $kt->danhMuc->ten_khau_tru }}
                                    <span style="font-size:11px; color:#64748b; font-style:italic; display:block;">{{ $kt->danhMuc->loai === 'tu_dong' ? '[Tự động chấm công]' : '[Thủ công]' }} {{ $kt->ly_do }}</span>
                                </td>
                                <td style="text-align:right; font-weight:600; color:#dc2626;">-{{ number_format($kt->so_tien) }} đ</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" style="color:#94a3b8; font-style:italic;">Không có khoản khấu trừ</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Summary Total Box --}}
                <div style="background:linear-gradient(135deg,#eff6ff,#dbeafe); border:1px solid #bfdbfe; border-radius:12px; padding:20px; margin-top:24px;">
                    <div style="font-size:13px; color:#1e40af; text-transform:uppercase; font-weight:600; letter-spacing:0.5px;">Tổng thực lĩnh</div>
                    <div style="font-size:32px; font-weight:800; color:#1d4ed8; margin:6px 0;">
                        {{ number_format($bangLuong->thuc_linh) }} VNĐ
                    </div>
                    <div style="font-size:12px; color:#3b82f6;">
                        = (Lương công + OT + Phụ cấp + Thưởng) − Khấu trừ
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Modal Thanh toán lương --}}
@if(auth()->user()->role === 'admin' && $bangLuong->isApproved() && ! $isPaid)
<div id="payModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.4); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; width:100%; max-width:440px; padding:24px;">
        <h3 style="margin-top:0;">Xác nhận thanh toán lương</h3>
        <form method="POST" action="{{ portal_route('payroll.pay', $bangLuong->id) }}">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Hình thức thanh toán</label>
                <select name="hinh_thuc" class="pr-form-control" style="width:100%;">
                    <option value="chuyen_khoan">Chuyển khoản ngân hàng</option>
                    <option value="tien_mat">Tiền mặt</option>
                </select>
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Ghi chú thanh toán</label>
                <textarea name="ghi_chu" class="pr-form-control" style="width:100%; height:80px;" placeholder="Mã giao dịch, thông tin chuyển khoản…"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="pr-btn pr-btn--secondary" onclick="document.getElementById('payModal').style.display='none'">Hủy</button>
                <button type="submit" class="pr-btn pr-btn--primary">Lưu thanh toán</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
