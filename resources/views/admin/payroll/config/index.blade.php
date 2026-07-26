@extends('layouts.admin.master')

@section('page_title', 'Cấu hình danh mục Lương')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', strtoupper(auth()->user()->role ?? 'ADMIN'))

@push('styles')
    @vite(['resources/css/pages/admin/payroll/index.css'])
    <style>
        .config-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(350px, 1fr)); gap:24px; }
        .config-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
        .config-card h3 { font-size:16px; color:#0f172a; margin-top:0; border-bottom:1px solid #f1f5f9; padding-bottom:12px; }
    </style>
@endpush

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
            <h1>Cấu hình danh mục Phụ cấp, Thưởng & Khấu trừ</h1>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:14px 18px; border-radius:10px; margin-bottom:20px; font-weight:500;">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="config-grid">

        {{-- 1. Danh mục Phụ cấp --}}
        <div class="config-card">
            <h3>1. Danh mục Phụ cấp</h3>
            <form method="POST" action="{{ portal_route('payroll.config.phu-cap.store') }}" style="margin-bottom:20px;">
                @csrf
                <div style="display:flex; gap:10px; margin-bottom:10px;">
                    <input type="text" name="ten_phu_cap" placeholder="Tên phụ cấp (vd: Xăng xe)" class="pr-form-control" style="flex:1;" required>
                    <input type="number" name="muc_mac_dinh" placeholder="Số tiền mặc định" class="pr-form-control" style="width:140px;" required>
                </div>
                <button type="submit" class="pr-btn pr-btn--primary" style="width:100%; height:36px; font-size:13px;">+ Thêm phụ cấp mới</button>
            </form>

            <table class="payroll-table">
                <thead>
                    <tr><th>Tên phụ cấp</th><th>Mức mặc định</th><th>Trạng thái</th></tr>
                </thead>
                <tbody>
                    @forelse($phuCaps as $pc)
                        <tr>
                            <td><strong>{{ $pc->ten_phu_cap }}</strong></td>
                            <td>{{ number_format($pc->muc_mac_dinh) }} đ</td>
                            <td>
                                <span style="font-size:12px; font-weight:600; color: {{ $pc->is_active ? '#16a34a' : '#94a3b8' }};">
                                    {{ $pc->is_active ? '✓ Áp dụng' : 'Khóa' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="color:#94a3b8; text-align:center;">Chưa có phụ cấp nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 2. Danh mục Thưởng --}}
        <div class="config-card">
            <h3>2. Danh mục Thưởng</h3>
            <form method="POST" action="{{ portal_route('payroll.config.thuong.store') }}" style="margin-bottom:20px;">
                @csrf
                <div style="margin-bottom:10px;">
                    <input type="text" name="ten_thuong" placeholder="Tên khoản thưởng (vd: Thưởng KPI)" class="pr-form-control" style="width:100%;" required>
                </div>
                <div style="margin-bottom:10px;">
                    <input type="text" name="mo_ta" placeholder="Mô tả khoản thưởng…" class="pr-form-control" style="width:100%;">
                </div>
                <button type="submit" class="pr-btn pr-btn--primary" style="width:100%; height:36px; font-size:13px;">+ Thêm danh mục thưởng</button>
            </form>

            <table class="payroll-table">
                <thead>
                    <tr><th>Tên thưởng</th><th>Mô tả</th><th>Trạng thái</th></tr>
                </thead>
                <tbody>
                    @forelse($thuongs as $th)
                        <tr>
                            <td><strong>{{ $th->ten_thuong }}</strong></td>
                            <td style="font-size:12px; color:#64748b;">{{ $th->mo_ta ?? '-' }}</td>
                            <td>
                                <span style="font-size:12px; font-weight:600; color: {{ $th->is_active ? '#16a34a' : '#94a3b8' }};">
                                    {{ $th->is_active ? '✓ Áp dụng' : 'Khóa' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="color:#94a3b8; text-align:center;">Chưa có khoản thưởng nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 3. Danh mục Khấu trừ --}}
        <div class="config-card">
            <h3>3. Danh mục Khấu trừ</h3>
            <form method="POST" action="{{ portal_route('payroll.config.khau-tru.store') }}" style="margin-bottom:20px;">
                @csrf
                <div style="display:flex; gap:10px; margin-bottom:10px;">
                    <input type="text" name="ten_khau_tru" placeholder="Tên khoản khấu trừ" class="pr-form-control" style="flex:1;" required>
                    <select name="loai" class="pr-form-control" style="width:130px;">
                        <option value="thu_cong">Thủ công</option>
                        <option value="tu_dong">Tự động</option>
                    </select>
                </div>
                <button type="submit" class="pr-btn pr-btn--primary" style="width:100%; height:36px; font-size:13px;">+ Thêm khoản khấu trừ</button>
            </form>

            <table class="payroll-table">
                <thead>
                    <tr><th>Tên khấu trừ</th><th>Loại sinh</th><th>Trạng thái</th></tr>
                </thead>
                <tbody>
                    @forelse($khauTrus as $kt)
                        <tr>
                            <td><strong>{{ $kt->ten_khau_tru }}</strong></td>
                            <td style="font-size:12px; color:#475569;">
                                {{ $kt->loai === 'tu_dong' ? 'Tự động Chấm công' : 'Thủ công' }}
                            </td>
                            <td>
                                <span style="font-size:12px; font-weight:600; color: {{ $kt->is_active ? '#16a34a' : '#94a3b8' }};">
                                    {{ $kt->is_active ? '✓ Áp dụng' : 'Khóa' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="color:#94a3b8; text-align:center;">Chưa có khoản khấu trừ nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
