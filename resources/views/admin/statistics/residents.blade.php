@extends('layouts.admin.master')

@section('page_title', 'Thống kê Cư dân & Hạ tầng - DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@push('styles')
    @vite('resources/css/pages/admin/statistics.css')
@endpush

@section('content')
<div class="stat-page">

    @include('admin.statistics.partials.sub_nav')

    {{-- HEADER --}}
    <div class="stat-header">
        <div class="stat-header__text">
            <h1 class="stat-title">Thống kê Cư dân & Hạ tầng</h1>
            <p class="stat-subtitle">Tổng quan tỷ lệ lấp đầy, phân bổ cư dân và hiệu suất sử dụng hạ tầng chung cư.</p>
        </div>
        <div class="stat-header__filter">
            <form method="GET" action="{{ route('admin.statistics.residents') }}" class="year-filter-form">
                <label class="year-filter-label" for="blockSelect">Tòa nhà:</label>
                <div class="year-select-wrap">
                    <select id="blockSelect" name="block_id" class="year-select" onchange="this.form.submit()">
                        <option value="">Tất cả các Block</option>
                        @foreach ($blocks as $bl)
                            <option value="{{ $bl->id }}" {{ $selectedBlock == $bl->id ? 'selected' : '' }}>
                                {{ $bl->name }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="year-select-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>

                <label class="year-filter-label" for="yearSelect" style="margin-left: 12px;">Năm:</label>
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

                <label class="year-filter-label" for="monthSelect" style="margin-left: 12px;">Tháng:</label>
                <div class="year-select-wrap">
                    <select id="monthSelect" name="month" class="year-select" onchange="this.form.submit()">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                Tháng {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                            </option>
                        @endfor
                    </select>
                    <svg class="year-select-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </form>
            <a href="{{ route('admin.statistics.residents.export', ['block_id' => $selectedBlock]) }}" class="btn-export">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Xuất Excel
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         KPI: HẠ TẦNG
    ═══════════════════════════════════════════════════════ --}}
    <div class="section-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Tổng quan hạ tầng
    </div>
    <div class="kpi-grid">
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-info">
                <span class="kpi-label">Tổng tòa nhà</span>
                <span class="kpi-value">{{ $totalBlocks }}</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--indigo">
            <div class="kpi-info">
                <span class="kpi-label">Tổng tầng</span>
                <span class="kpi-value">{{ $totalFloors }}</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--green">
            <div class="kpi-info">
                <span class="kpi-label">Đang có người ở</span>
                <span class="kpi-value">{{ $occupied }} / {{ $totalApartments }}</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-info">
                <span class="kpi-label">Tỷ lệ lấp đầy</span>
                <span class="kpi-value">{{ $occupancyRate }}%</span>
                <div class="mini-progress">
                    <div class="mini-progress-fill" style="width: {{ $occupancyRate }}%; background: {{ $occupancyRate > 70 ? '#16a34a' : ($occupancyRate > 40 ? '#f59e0b' : '#dc2626') }}"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         KPI: CƯ DÂN
    ═══════════════════════════════════════════════════════ --}}
    <div class="section-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
        Tổng quan cư dân
    </div>
    <div class="kpi-grid">
        <div class="kpi-card kpi-card--blue kpi-card--sm">
            <div class="kpi-info">
                <span class="kpi-label">Tổng cư dân</span>
                <span class="kpi-value kpi-value--sm">{{ number_format($totalResidents) }}</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--green kpi-card--sm">
            <div class="kpi-info">
                <span class="kpi-label">Chủ hộ</span>
                <span class="kpi-value kpi-value--sm">{{ number_format($ownerCount) }}</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--amber kpi-card--sm">
            <div class="kpi-info">
                <span class="kpi-label">Người thuê</span>
                <span class="kpi-value kpi-value--sm">{{ number_format($tenantCount) }}</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--indigo kpi-card--sm">
            <div class="kpi-info">
                <span class="kpi-label">Thành viên GĐ</span>
                <span class="kpi-value kpi-value--sm">{{ number_format($familyCount) }}</span>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         BIỂU ĐỒ HÀNG 1: Xu hướng + Phân loại cư dân
    ═══════════════════════════════════════════════════════ --}}
    <div class="charts-grid">
        {{-- Biểu đồ đường: Xu hướng đăng ký cư dân 12 tháng --}}
        <div class="chart-card chart-card--main">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Xu hướng cư dân đăng ký — 12 tháng gần nhất</h3>
                    <p class="chart-card__sub">Số lượng cư dân mới đăng ký theo tháng</p>
                </div>
                <span class="chart-badge chart-badge--bar">Line Chart</span>
            </div>
            <div class="chart-wrap">
                <canvas id="residentTrendChart"></canvas>
            </div>
        </div>

        {{-- Biểu đồ donut: Phân loại cư dân --}}
        <div class="chart-card">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Phân loại cư dân</h3>
                    <p class="chart-card__sub">Tỷ lệ chủ hộ / người thuê / thành viên GĐ</p>
                </div>
                <span class="chart-badge chart-badge--donut">Donut Chart</span>
            </div>
            <div class="chart-wrap chart-wrap--donut">
                @if($totalResidents > 0)
                    <canvas id="residentTypeChart"></canvas>
                @else
                    <div class="empty-chart-text">Chưa có cư dân trong hệ thống</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         BIỂU ĐỒ HÀNG 2: Trạng thái căn hộ + Phân bố theo tòa
    ═══════════════════════════════════════════════════════ --}}
    <div class="charts-grid">
        {{-- Biểu đồ donut: Trạng thái căn hộ --}}
        <div class="chart-card">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Trạng thái căn hộ</h3>
                    <p class="chart-card__sub">Tỷ lệ đang ở / trống / bảo trì</p>
                </div>
                <span class="chart-badge chart-badge--donut">Donut Chart</span>
            </div>
            <div class="chart-wrap chart-wrap--donut">
                <canvas id="apartmentStatusChart"></canvas>
            </div>
        </div>

        {{-- Biểu đồ cột nhóm: Phân bố theo tòa nhà --}}
        <div class="chart-card chart-card--main">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Phân bố căn hộ theo tòa nhà</h3>
                    <p class="chart-card__sub">So sánh số căn đang ở / trống / bảo trì theo từng tòa</p>
                </div>
                <span class="chart-badge chart-badge--bar">Bar Chart</span>
            </div>
            <div class="chart-wrap">
                <canvas id="blockDistributionChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         BẢNG: Top căn hộ đông người + Tình trạng theo tầng
    ═══════════════════════════════════════════════════════ --}}
    <div class="charts-grid charts-grid--equal">

        {{-- Bảng Top căn hộ đông người --}}
        <div class="table-card">
            <div class="table-card__header">
                <h3>🏆 Top căn hộ đông người nhất</h3>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Căn hộ</th>
                            <th>Tòa nhà</th>
                            <th style="text-align:right;">Số cư dân</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topApartments as $i => $apt)
                            <tr>
                                <td style="color:#94a3b8; font-weight:700; width:40px;">{{ $i + 1 }}</td>
                                <td><strong>{{ $apt->apartment_number }}</strong></td>
                                <td>{{ $apt->block_name }}</td>
                                <td style="text-align:right;">
                                    <span style="background:#eff6ff; color:#0b57d0; font-weight:700; padding:3px 10px; border-radius:9999px; font-size:12px;">
                                        {{ $apt->resident_count }} người
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-row">Chưa có cư dân đăng ký trong hệ thống</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bảng tỷ lệ lấp đầy theo tầng --}}
        <div class="table-card">
            <div class="table-card__header">
                <h3>📊 Tỷ lệ lấp đầy theo tầng</h3>
            </div>
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tòa</th>
                            <th>Tầng</th>
                            <th>Tổng</th>
                            <th>Đang ở</th>
                            <th>Tỷ lệ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($floorStats as $floor)
                            @php
                                $rate = $floor->total_apts > 0 ? round(($floor->occupied_apts / $floor->total_apts) * 100) : 0;
                                $color = $rate >= 75 ? '#16a34a' : ($rate >= 40 ? '#f59e0b' : '#dc2626');
                            @endphp
                            <tr>
                                <td>{{ $floor->block_name }}</td>
                                <td>Tầng {{ $floor->floor_number }}</td>
                                <td style="color:#64748b;">{{ $floor->total_apts }}</td>
                                <td style="font-weight:600;">{{ $floor->occupied_apts }}</td>
                                <td>
                                    <div class="progress-wrap" style="min-width:100px;">
                                        <span class="progress-label">{{ $rate }}%</span>
                                        <div class="progress-bg">
                                            <div class="progress-fill" style="width:{{ $rate }}%; background:{{ $color }}"></div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="empty-row">Chưa có dữ liệu tầng</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const tooltipDefaults = {
        padding: 12,
        backgroundColor: '#1e293b',
        titleFont: { size: 13, weight: 'bold' },
        bodyFont: { size: 12 },
        cornerRadius: 8,
    };

    // ─── 1. XU HƯỚNG CƯ DÂN (Line Chart) ───────────────────────────
    const ctxTrend = document.getElementById('residentTrendChart');
    if (ctxTrend) {
        new Chart(ctxTrend.getContext('2d'), {
            type: 'line',
            data: {
                labels: @json($trendLabels),
                datasets: [{
                    label: 'Cư dân đăng ký mới',
                    data: @json($trendValues),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    borderWidth: 2.5,
                    pointRadius: 5,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ` ${ctx.parsed.y} cư dân` } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748b' } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11 }, color: '#64748b', stepSize: 1 }
                    }
                }
            }
        });
    }

    // ─── 2. PHÂN LOẠI CƯ DÂN (Donut) ───────────────────────────────
    const ctxType = document.getElementById('residentTypeChart');
    if (ctxType) {
        const typeData = @json(array_values($residentTypeData));
        if (typeData.some(v => v > 0)) {
            new Chart(ctxType.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Chủ hộ', 'Người thuê', 'Thành viên GĐ'],
                    datasets: [{
                        data: typeData,
                        backgroundColor: ['#3b82f6', '#f59e0b', '#a78bfa'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 }, padding: 14 } },
                        tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} người` } }
                    },
                    cutout: '70%'
                }
            });
        } else {
            ctxType.parentElement.innerHTML = '<div class="empty-chart-text">Chưa có cư dân</div>';
        }
    }

    // ─── 3. TRẠNG THÁI CĂN HỘ (Donut) ──────────────────────────────
    const ctxAptStatus = document.getElementById('apartmentStatusChart');
    if (ctxAptStatus) {
        new Chart(ctxAptStatus.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Đang có người ở', 'Còn trống', 'Đang bảo trì'],
                datasets: [{
                    data: [{{ $occupied }}, {{ $vacant }}, {{ $maintenance }}],
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { size: 11 }, padding: 14 } },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed} căn` } }
                },
                cutout: '70%'
            }
        });
    }

    // ─── 4. PHÂN BỐ CĂN HỘ THEO TÒA (Grouped Bar) ──────────────────
    const ctxBlock = document.getElementById('blockDistributionChart');
    if (ctxBlock) {
        const blockData = @json($blockStats);
        new Chart(ctxBlock.getContext('2d'), {
            type: 'bar',
            data: {
                labels: blockData.map(b => b.name),
                datasets: [
                    {
                        label: 'Đang ở',
                        data: blockData.map(b => b.occupied_count),
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderRadius: 6,
                    },
                    {
                        label: 'Còn trống',
                        data: blockData.map(b => b.vacant_count),
                        backgroundColor: 'rgba(245, 158, 11, 0.85)',
                        borderRadius: 6,
                    },
                    {
                        label: 'Bảo trì',
                        data: blockData.map(b => b.maintenance_count),
                        backgroundColor: 'rgba(239, 68, 68, 0.85)',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8, font: { size: 12 } } },
                    tooltip: { ...tooltipDefaults, callbacks: { label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y} căn` } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 12 }, color: '#334155' } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 11 }, color: '#64748b', stepSize: 1 }
                    }
                }
            }
        });
    }
});
</script>
@endpush
