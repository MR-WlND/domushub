@extends('layouts.admin.master')

@section('page_title', 'Lịch sử lương — ' . $user->name)
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

    {{-- Header --}}
    <div class="payroll-page__header">
        <div>
            <p class="payroll-page__eyebrow">
                <a href="{{ portal_route('payroll.index') }}" style="color:inherit; text-decoration:none;">
                    ‹ Quay lại bảng lương
                </a>
            </p>
            <h1>Lịch sử nhận lương: {{ $user->name }}</h1>
            <p style="margin:4px 0 0; color:#64748b; font-size:14px;">
                Chức vụ: <strong>{{ $roleLabels[$user->role] ?? $user->role }}</strong> — Lương cơ bản: <strong>{{ number_format($user->base_salary) }} đ</strong>
            </p>
        </div>
    </div>

    {{-- Table --}}
    <div class="payroll-table-card">
        <div class="payroll-table-wrap">
            <table class="payroll-table">
                <thead>
                    <tr>
                        <th>Kỳ lương (Tháng/Năm)</th>
                        <th>Công thực tế</th>
                        <th>Số giờ OT</th>
                        <th>Lương công</th>
                        <th>Phụ cấp + Thưởng</th>
                        <th>Khấu trừ</th>
                        <th>Thực lĩnh</th>
                        <th>Trạng thái duyệt</th>
                        <th>Thanh toán</th>
                        <th style="text-align:right;">Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $pl)
                        @php $isPaid = optional($pl->thanhToan)->trang_thai === 'da_thanh_toan'; @endphp
                        <tr>
                            <td><strong>Tháng {{ sprintf('%02d/%04d', $pl->thang, $pl->nam) }}</strong></td>
                            <td>{{ $pl->so_ngay_cong_thuc_te }} / {{ $pl->so_ngay_cong_chuan }}</td>
                            <td>{{ $pl->so_gio_ot }}h</td>
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
                                @if($isPaid)
                                    <span class="pr-badge pr-badge--da_thanh_toan">✓ Đã thanh toán</span>
                                @else
                                    <span style="font-size:12px; color:#94a3b8;">Chưa thanh toán</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <a href="{{ portal_route('payroll.show', $pl->id) }}" class="pr-btn pr-btn--secondary" style="height:32px; padding:0 10px; font-size:12px;">
                                    Xem phiếu
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center; padding:40px; color:#64748b;">
                                Nhân viên chưa có bản ghi lịch sử lương nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payrolls->hasPages())
            <div style="padding:16px 20px; border-top:1px solid #f1f5f9;">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
