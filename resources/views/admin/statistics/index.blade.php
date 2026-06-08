@extends('layouts.admin.master')

@section('page_title', 'Báo cáo & Thống kê')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="stat-page">

    {{-- =========================================================== --}}
    {{-- HEADER + BỘ LỌC NĂM                                        --}}
    {{-- =========================================================== --}}
    <div class="stat-header">
        <div class="stat-header__text">
            <h1 class="stat-title">Báo cáo & Thống kê Doanh thu</h1>
            <p class="stat-subtitle">Theo dõi tình hình phát hành hóa đơn, doanh thu thực tế và công nợ toàn hệ thống.</p>
        </div>
        <div class="stat-header__filter">
            <form method="GET" action="{{ route('admin.statistics') }}" class="year-filter-form">
                <label class="year-filter-label" for="yearSelect">Xem theo năm:</label>
                <div class="year-select-wrap">
                    <select id="yearSelect" name="year" class="year-select" onchange="this.form.submit()">
                        @foreach ($availableYears as $yr)
                            <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>
                                Năm {{ $yr }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="year-select-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- KPI: TỔNG TOÀN THỜI GIAN                                   --}}
    {{-- =========================================================== --}}
    <div class="section-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Tổng quan toàn hệ thống (tất cả các năm)
    </div>
    <div class="kpi-grid">
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Tổng phát hành</span>
                <span class="kpi-value">{{ number_format($totalBilled) }} đ</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Đã thu thực tế</span>
                <span class="kpi-value">{{ number_format($totalCollected) }} đ</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Tổng nợ chưa thu</span>
                <span class="kpi-value">{{ number_format($totalUnpaid) }} đ</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--indigo">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Tỷ lệ thu hồi</span>
                <span class="kpi-value">{{ number_format($collectionRate, 1) }}%</span>
            </div>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- KPI: THEO NĂM ĐƯỢC CHỌN                                    --}}
    {{-- =========================================================== --}}
    <div class="section-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Thống kê năm {{ $selectedYear }}
    </div>
    <div class="kpi-grid kpi-grid--year">
        <div class="kpi-card kpi-card--blue kpi-card--sm">
            <div class="kpi-info">
                <span class="kpi-label">Phát hành năm {{ $selectedYear }}</span>
                <span class="kpi-value kpi-value--sm">{{ number_format($yearBilled) }} đ</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--green kpi-card--sm">
            <div class="kpi-info">
                <span class="kpi-label">Đã thu năm {{ $selectedYear }}</span>
                <span class="kpi-value kpi-value--sm">{{ number_format($yearCollected) }} đ</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--amber kpi-card--sm">
            <div class="kpi-info">
                <span class="kpi-label">Còn nợ năm {{ $selectedYear }}</span>
                <span class="kpi-value kpi-value--sm">{{ number_format($yearUnpaid) }} đ</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--indigo kpi-card--sm">
            <div class="kpi-info">
                <span class="kpi-label">Tỷ lệ thu hồi {{ $selectedYear }}</span>
                <span class="kpi-value kpi-value--sm">{{ number_format($yearCollectionRate, 1) }}%</span>
                <div class="mini-progress">
                    <div class="mini-progress-fill" style="width: {{ min($yearCollectionRate, 100) }}%; background: {{ $yearCollectionRate > 80 ? '#16a34a' : ($yearCollectionRate > 40 ? '#f59e0b' : '#dc2626') }}"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- BIỂU ĐỒ                                                     --}}
    {{-- =========================================================== --}}
    <div class="charts-grid">
        {{-- Biểu đồ cột (Bar Chart) --}}
        <div class="chart-card chart-card--main">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Doanh thu theo tháng — Năm {{ $selectedYear }}</h3>
                    <p class="chart-card__sub">So sánh hóa đơn phát hành và thực tế thu được</p>
                </div>
                <span class="chart-badge chart-badge--bar">Bar Chart</span>
            </div>
            <div class="chart-wrap">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        {{-- Biểu đồ tròn --}}
        <div class="chart-card">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Cơ cấu dịch vụ</h3>
                    <p class="chart-card__sub">Tỷ trọng từng loại phí — {{ $selectedYear }}</p>
                </div>
                <span class="chart-badge chart-badge--donut">Doughnut</span>
            </div>
            <div class="chart-wrap chart-wrap--donut">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Biểu đồ đường (Line Chart) --}}
    <div class="chart-card">
        <div class="chart-card__header">
            <div>
                <h3 class="chart-card__title">Xu hướng doanh thu — Năm {{ $selectedYear }}</h3>
                <p class="chart-card__sub">Đường biểu diễn phát hành và thu được theo từng tháng</p>
            </div>
            <span class="chart-badge chart-badge--line">Line Chart</span>
        </div>
        <div class="chart-wrap chart-wrap--line">
            <canvas id="lineChart"></canvas>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- BẢNG CHI TIẾT                                               --}}
    {{-- =========================================================== --}}
    <div class="table-card">
        <div class="table-card__header">
            <h3>Bảng số liệu chi tiết — Năm {{ $selectedYear }}</h3>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tháng</th>
                        <th>Hóa đơn phát hành</th>
                        <th>Đã thu (VND)</th>
                        <th>Chưa thu (VND)</th>
                        <th>Tỷ lệ thu hồi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monthlyRevenue as $row)
                        @php
                            $unpaid = $row->total_billed - $row->total_collected;
                            $rate   = $row->total_billed > 0 ? ($row->total_collected / $row->total_billed) * 100 : 0;
                        @endphp
                        <tr>
                            <td><strong>Tháng {{ str_pad($row->billing_month, 2, '0', STR_PAD_LEFT) }}/{{ $row->billing_year }}</strong></td>
                            <td>{{ number_format($row->total_billed) }} đ</td>
                            <td class="text-success">{{ number_format($row->total_collected) }} đ</td>
                            <td class="text-danger">{{ number_format($unpaid) }} đ</td>
                            <td>
                                <div class="progress-wrap">
                                    <span class="progress-label">{{ number_format($rate, 1) }}%</span>
                                    <div class="progress-bg">
                                        <div class="progress-fill" style="width: {{ $rate }}%; background: {{ $rate > 80 ? '#16a34a' : ($rate > 40 ? '#f59e0b' : '#dc2626') }}"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-row">Không có dữ liệu cho năm {{ $selectedYear }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- =========================================================== --}}
{{-- STYLES                                                       --}}
{{-- =========================================================== --}}
<style>
    .stat-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding-bottom: 48px;
    }

    /* Header */
    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 16px;
    }
    .stat-title {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px;
    }
    .stat-subtitle {
        font-size: 14px;
        color: #64748b;
        margin: 0;
    }

    /* Year filter */
    .year-filter-form {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .year-filter-label {
        font-size: 13px;
        font-weight: 500;
        color: #475569;
        white-space: nowrap;
    }
    .year-select-wrap {
        position: relative;
    }
    .year-select {
        appearance: none;
        background: #fff;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 36px 8px 14px;
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .year-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
    }
    .year-select-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #64748b;
    }

    /* Section label */
    .section-label {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: -10px;
    }
    .section-label svg { color: #3b82f6; }

    /* KPI Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }
    .kpi-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.08);
    }
    .kpi-card--sm { padding: 16px 18px; }
    .kpi-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .kpi-info { display: flex; flex-direction: column; gap: 3px; }
    .kpi-label { font-size: 12px; font-weight: 500; color: #64748b; }
    .kpi-value { font-size: 20px; font-weight: 700; color: #0f172a; }
    .kpi-value--sm { font-size: 17px; }

    /* Colors */
    .kpi-card--blue  { border-left: 4px solid #3b82f6; }
    .kpi-card--blue .kpi-icon  { background: #eff6ff; color: #3b82f6; }
    .kpi-card--green { border-left: 4px solid #16a34a; }
    .kpi-card--green .kpi-icon { background: #f0fdf4; color: #16a34a; }
    .kpi-card--amber { border-left: 4px solid #d97706; }
    .kpi-card--amber .kpi-icon { background: #fffbeb; color: #d97706; }
    .kpi-card--indigo { border-left: 4px solid #6366f1; }
    .kpi-card--indigo .kpi-icon { background: #eef2ff; color: #6366f1; }

    /* Mini progress (in year KPI) */
    .mini-progress {
        height: 4px;
        background: #e2e8f0;
        border-radius: 4px;
        margin-top: 6px;
        overflow: hidden;
    }
    .mini-progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    /* Charts Grid */
    .charts-grid {
        display: grid;
        grid-template-columns: 3fr 2fr;
        gap: 20px;
    }
    @media (max-width: 1100px) {
        .charts-grid { grid-template-columns: 1fr; }
    }

    /* Chart Card */
    .chart-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 22px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .chart-card__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
    }
    .chart-card__title {
        margin: 0 0 3px;
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
    }
    .chart-card__sub {
        margin: 0;
        font-size: 12px;
        color: #94a3b8;
    }
    .chart-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
        letter-spacing: 0.03em;
        white-space: nowrap;
    }
    .chart-badge--bar   { background: #eff6ff; color: #3b82f6; }
    .chart-badge--donut { background: #f5f3ff; color: #7c3aed; }
    .chart-badge--line  { background: #f0fdf4; color: #16a34a; }

    .chart-wrap         { position: relative; height: 300px; }
    .chart-wrap--donut  { height: 280px; }
    .chart-wrap--line   { height: 260px; }

    /* Data Table */
    .table-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 22px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .table-card__header {
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
        margin-bottom: 16px;
    }
    .table-card__header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #0f172a;
    }
    .table-wrap { overflow-x: auto; }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }
    .data-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 11px 14px;
        border-bottom: 2px solid #e2e8f0;
    }
    .data-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }
    .data-table tr:last-child td { border-bottom: none; }
    .data-table tr:hover td { background: #f8fafc; }
    .text-success { color: #16a34a !important; font-weight: 600; }
    .text-danger  { color: #dc2626 !important; font-weight: 600; }
    .empty-row    { text-align: center; color: #94a3b8; padding: 40px; }

    /* Progress bar */
    .progress-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 150px;
    }
    .progress-label {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        min-width: 42px;
    }
    .progress-bg {
        flex: 1;
        height: 6px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.4s;
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Dữ liệu từ Laravel
        const labels        = @json($monthlyLabels);
        const billedData    = @json($monthlyBilledData);
        const collectedData = @json($monthlyCollectedData);
        const serviceLabels = @json($serviceLabels);
        const serviceData   = @json($serviceData);

        const fmtVND = v => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v);

        const tooltipDefaults = {
            padding: 12,
            backgroundColor: '#1e293b',
            titleFont: { size: 13, weight: 'bold' },
            bodyFont: { size: 12 },
            cornerRadius: 8,
        };

        // ---- 1. BAR CHART ----
        new Chart(document.getElementById('barChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Phát hành',
                        data: billedData,
                        backgroundColor: 'rgba(59,130,246,0.85)',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                    {
                        label: 'Đã thu',
                        data: collectedData,
                        backgroundColor: 'rgba(16,185,129,0.85)',
                        borderRadius: 5,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { size: 12 } } },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ` ${ctx.dataset.label}: ${fmtVND(ctx.parsed.y)}` } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748b' } },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 11 },
                            color: '#64748b',
                            callback: v => (v / 1_000_000).toFixed(0) + ' Tr'
                        }
                    }
                }
            }
        });

        // ---- 2. DOUGHNUT CHART ----
        new Chart(document.getElementById('donutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: serviceLabels,
                datasets: [{
                    data: serviceData,
                    backgroundColor: ['#3b82f6','#0ea5e9','#10b981','#f59e0b','#6366f1','#ec4899','#14b8a6','#f97316'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 }, padding: 14 } },
                    tooltip: {
                        ...tooltipDefaults,
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${fmtVND(ctx.parsed)}`
                        }
                    }
                },
                cutout: '68%'
            }
        });

        // ---- 3. LINE CHART ----
        new Chart(document.getElementById('lineChart').getContext('2d'), {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Hóa đơn phát hành',
                        data: billedData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.06)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#3b82f6'
                    },
                    {
                        label: 'Thực tế thu được',
                        data: collectedData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.06)',
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { size: 12 } } },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ` ${ctx.dataset.label}: ${fmtVND(ctx.parsed.y)}` } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748b' } },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { size: 11 },
                            color: '#64748b',
                            callback: v => (v / 1_000_000).toFixed(0) + ' Tr'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
