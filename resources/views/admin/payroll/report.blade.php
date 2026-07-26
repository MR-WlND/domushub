@extends('layouts.admin.master')

@section('page_title', 'Báo cáo thống kê quỹ lương — ' . $year)
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', strtoupper(auth()->user()->role ?? 'ADMIN'))

@push('styles')
    @vite(['resources/css/pages/admin/payroll/index.css'])
    <style>
        .report-grid { display:grid; grid-template-columns:2fr 1fr; gap:24px; margin-bottom:24px; }
        .report-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.04); }
        .bar-chart { display:flex; align-items:flex-end; gap:12px; height:220px; padding-top:20px; border-bottom:1px solid #e2e8f0; }
        .bar-col { flex:1; display:flex; flex-direction:column; align-items:center; height:100%; justify-content:flex-end; }
        .bar-fill { width:100%; max-width:28px; background:linear-gradient(180deg, #3b82f6, #1d4ed8); border-radius:4px 4px 0 0; transition:height 0.3s; }
        .bar-lbl { font-size:11px; color:#64748b; margin-top:8px; }
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
            <h1>Báo cáo Thống kê Quỹ lương {{ $year }}</h1>
        </div>
        <div>
            <form method="GET" action="{{ portal_route('payroll.report') }}" style="display:flex; align-items:center; gap:10px;">
                <span style="font-size:14px; font-weight:600; color:#475569;">Chọn năm:</span>
                <select name="year" class="pr-form-control" onchange="this.form.submit()">
                    @for($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" @selected($year === $y)>Năm {{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </div>

    {{-- Grid Chart & Role Breakdown --}}
    @php
        $maxVal = max($monthlyStats) > 0 ? max($monthlyStats) : 1;
        $totalYear = array_sum($monthlyStats);
    @endphp

    <div class="report-grid">
        {{-- Chart 12 Tháng --}}
        <div class="report-card">
            <h3 style="margin-top:0; font-size:16px; color:#0f172a;">Biểu đồ chi phí lương theo tháng (Năm {{ $year }})</h3>
            <p style="font-size:13px; color:#64748b; margin-bottom:16px;">Tổng chi trả cả năm: <strong style="color:#1d4ed8;">{{ number_format($totalYear) }} VNĐ</strong></p>

            <div class="bar-chart">
                @foreach($monthlyStats as $m => $val)
                    @php $pct = round(($val / $maxVal) * 100); @endphp
                    <div class="bar-col">
                        @if($val > 0)
                            <span style="font-size:10px; color:#3b82f6; font-weight:600; margin-bottom:4px;">{{ number_format($val/1000000, 1) }}M</span>
                        @endif
                        <div class="bar-fill" style="height: {{ max($pct, 4) }}%;"></div>
                        <span class="bar-lbl">T{{ $m }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Breakdown theo Role --}}
        <div class="report-card">
            <h3 style="margin-top:0; font-size:16px; color:#0f172a;">Phân bổ theo bộ phận</h3>
            <table class="payroll-table" style="margin-top:14px;">
                <thead>
                    <tr>
                        <th>Bộ phận / Vai trò</th>
                        <th style="text-align:right;">Tổng chi trả</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roleStats as $rk => $tot)
                        <tr>
                            <td><strong>{{ $roles[$rk] ?? $rk }}</strong></td>
                            <td style="text-align:right; font-weight:600; color:#1d4ed8;">{{ number_format($tot) }} đ</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" style="color:#94a3b8; text-align:center;">Chưa có dữ liệu năm {{ $year }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
