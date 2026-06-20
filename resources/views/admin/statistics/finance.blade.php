@extends('layouts.admin.master')

@section('page_title', 'Thống kê Tài chính - DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="stat-page">

    @include('admin.statistics.partials.sub_nav')

    {{-- HEADER + BỘ LỌC NĂM --}}
    <div class="stat-header">
        <div class="stat-header__text">
            <h1 class="stat-title">Thống kê Tài chính & Tiêu thụ</h1>
            <p class="stat-subtitle">Theo dõi doanh thu, cơ cấu dịch vụ tiêu thụ và hiệu suất thu phí của chung cư.</p>
        </div>
        <div class="stat-header__filter">
            <form method="GET" action="{{ route('admin.statistics.finance') }}" class="year-filter-form">
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
            <a href="{{ route('admin.statistics.finance.export', ['year' => $selectedYear, 'block_id' => $selectedBlock]) }}" class="btn-export">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Xuất Excel
            </a>
        </div>
    </div>

    {{-- KPI: TỔNG TOÀN THỜI GIAN --}}
    <div class="section-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Tổng quan tài chính toàn hệ thống (Lũy kế)
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

    {{-- KPI: THEO NĂM ĐƯỢC CHỌN --}}
    <div class="section-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Thống kê chi tiết tài chính năm {{ $selectedYear }}
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

    {{-- BIỂU ĐỒ --}}
    <div class="charts-grid">
        {{-- Biểu đồ cột chồng (Stacked Bar Chart) --}}
        <div class="chart-card chart-card--main">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Cơ cấu doanh thu theo tháng — Năm {{ $selectedYear }}</h3>
                    <p class="chart-card__sub">Phân bổ doanh thu thực thu theo loại phí dịch vụ</p>
                </div>
                <span class="chart-badge chart-badge--bar">Stacked Bar</span>
            </div>
            <div class="chart-wrap">
                <canvas id="monthlyStackedRevenueChart"></canvas>
            </div>
        </div>

        {{-- Biểu đồ tròn đóng phí --}}
        <div class="chart-card">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Tỷ lệ hoàn thành đóng phí</h3>
                    <p class="chart-card__sub">
                        Đóng phí Tháng {{ str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) }}/{{ $selectedYear }}
                    </p>
                </div>
                <span class="chart-badge chart-badge--donut">Donut Chart</span>
            </div>
            <div class="chart-wrap chart-wrap--donut">
                @if($paidAmount > 0 || $unpaidAmount > 0)
                    <canvas id="collectionRateDonutChart"></canvas>
                @else
                    <div class="empty-chart-text">Tháng này chưa phát sinh hoá đơn</div>
                @endif
            </div>
        </div>
    </div>

    {{-- HÀNG BIỂU ĐỒ 2: XU HƯỚNG TIÊU THỤ ĐIỆN & NƯỚC --}}
    <div class="charts-grid" style="margin-top: 12px; margin-bottom: 12px;">
        {{-- Biểu đồ đường: Tiêu thụ Điện --}}
        <div class="chart-card">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Xu hướng tiêu thụ Điện — Năm {{ $selectedYear }}</h3>
                    <p class="chart-card__sub">Tổng sản lượng điện tiêu thụ toàn chung cư (kWh)</p>
                </div>
                <span class="chart-badge chart-badge--bar" style="background: #fef2f2; color: #dc2626;">Line Chart</span>
            </div>
            <div class="chart-wrap">
                <canvas id="electricityConsumptionChart"></canvas>
            </div>
        </div>

        {{-- Biểu đồ đường: Tiêu thụ Nước --}}
        <div class="chart-card">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Xu hướng tiêu thụ Nước — Năm {{ $selectedYear }}</h3>
                    <p class="chart-card__sub">Tổng sản lượng nước tiêu thụ toàn chung cư (m³)</p>
                </div>
                <span class="chart-badge" style="background: #e0f2fe; color: #0284c7;">Line Chart</span>
            </div>
            <div class="chart-wrap">
                <canvas id="waterConsumptionChart"></canvas>
            </div>
        </div>
    </div>

    {{-- BẢNG CHI TIẾT --}}
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
                            <td colspan="5" class="empty-row">Không có dữ liệu tài chính cho năm {{ $selectedYear }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('styles')
    @vite('resources/css/pages/admin/statistics.css')
@endpush
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const labels = @json($monthlyLabels);
        const stackedData = @json($monthlyStackedData);
        
        const fmtVND = v => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(v);

        const tooltipDefaults = {
            padding: 12,
            backgroundColor: '#1e293b',
            titleFont: { size: 13, weight: 'bold' },
            bodyFont: { size: 12 },
            cornerRadius: 8,
        };

        // 1. MONTHLY STACKED REVENUE CHART (Stacked Bar Chart)
        const ctxStacked = document.getElementById('monthlyStackedRevenueChart');
        if (ctxStacked) {
            new Chart(ctxStacked.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Điện',
                            data: stackedData.electricity,
                            backgroundColor: 'rgba(59, 130, 246, 0.85)', // Blue
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                        {
                            label: 'Nước',
                            data: stackedData.water,
                            backgroundColor: 'rgba(14, 165, 233, 0.85)', // Cyan/Light Blue
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                        {
                            label: 'Phí quản lý',
                            data: stackedData.management_fee,
                            backgroundColor: 'rgba(16, 185, 129, 0.85)', // Green
                            borderRadius: 4,
                            borderSkipped: false,
                        },
                        {
                            label: 'Khác',
                            data: stackedData.other,
                            backgroundColor: 'rgba(245, 158, 11, 0.85)', // Amber
                            borderRadius: 4,
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
                        x: { 
                            stacked: true,
                            grid: { display: false }, 
                            ticks: { font: { size: 11 }, color: '#64748b' } 
                        },
                        y: {
                            stacked: true,
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
        }

        // 2. COLLECTION RATE DONUT CHART (Donut Chart)
        const ctxDonut = document.getElementById('collectionRateDonutChart');
        if (ctxDonut) {
            const paid = {{ $paidAmount }};
            const unpaid = {{ $unpaidAmount }};
            
            // Check if there is data
            if (paid > 0 || unpaid > 0) {
                new Chart(ctxDonut.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Đã thanh toán', 'Chưa thanh toán / Quá hạn'],
                        datasets: [{
                            data: [paid, unpaid],
                            backgroundColor: ['#10b981', '#ef4444'], // Green vs Red
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
                        cutout: '70%'
                    }
                });
            } else {
                ctxDonut.parentElement.innerHTML = '<div class="empty-chart-text">Tháng này chưa phát sinh hoá đơn</div>';
            }
        }

        // 3. ELECTRICITY CONSUMPTION CHART
        const ctxElec = document.getElementById('electricityConsumptionChart');
        if (ctxElec) {
            new Chart(ctxElec.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sản lượng Điện (kWh)',
                        data: @json($electricityConsumption),
                        borderColor: '#ef4444', // Red
                        backgroundColor: 'rgba(239, 68, 68, 0.05)',
                        borderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipDefaults,
                            callbacks: { label: ctx => ` ${ctx.parsed.y} kWh` }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748b' } },
                        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#64748b' } }
                    }
                }
            });
        }

        // 4. WATER CONSUMPTION CHART
        const ctxWater = document.getElementById('waterConsumptionChart');
        if (ctxWater) {
            new Chart(ctxWater.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sản lượng Nước (m³)',
                        data: @json($waterConsumption),
                        borderColor: '#0ea5e9', // Blue/Cyan
                        backgroundColor: 'rgba(14, 165, 233, 0.05)',
                        borderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipDefaults,
                            callbacks: { label: ctx => ` ${ctx.parsed.y} m³` }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#64748b' } },
                        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, color: '#64748b' } }
                    }
                }
            });
        }
    });
</script>
@endpush
