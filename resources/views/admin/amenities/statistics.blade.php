@extends('layouts.admin.master')

@section('page_title', 'Thống kê tiện ích - DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="stat-page">

    {{-- Tabs chuyển đổi thống kê --}}
    @include('admin.statistics.partials.sub_nav')

    {{-- Header --}}
    <div class="stat-header">
        <div class="stat-header__text">
            <h1 class="stat-title">Thống kê Tiện ích chung cư</h1>
            <p class="stat-subtitle">Theo dõi tần suất đặt tiện ích, doanh thu và tỷ lệ phê duyệt lịch đặt của chung cư.</p>
        </div>
        <div class="stat-header__filter">
            <form method="GET" action="{{ portal_route('amenities.statistics') }}" class="year-filter-form">
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
                        <option value="">Tất cả các tháng</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                Tháng {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                            </option>
                        @endfor
                    </select>
                    <svg class="year-select-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </form>
            <a href="{{ portal_route('amenities.statistics.export', ['year' => $selectedYear, 'month' => $selectedMonth, 'block_id' => $selectedBlock]) }}" class="btn-export">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Xuất Excel
            </a>
            <a href="{{ portal_route('amenities.index') }}" class="btn-export" style="background-color: #3b82f6;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Danh sách tiện ích
            </a>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="kpi-grid">
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Tổng tiện ích</span>
                <span class="kpi-value">{{ $summary['total_facilities'] }}</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--indigo">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Tổng lịch đặt</span>
                <span class="kpi-value">{{ number_format($summary['total_bookings']) }}</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Tổng doanh thu</span>
                <span class="kpi-value">{{ number_format($summary['total_revenue']) }}đ</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Chờ duyệt</span>
                <span class="kpi-value">{{ $summary['pending_total'] }}</span>
            </div>
        </div>
    </div>

    {{-- Bảng chi tiết theo tiện ích --}}
    <div class="table-card">
        <div class="table-card__header">
            <h3>Thống kê số liệu chi tiết theo từng tiện ích</h3>
        </div>

        @if($facilities->isEmpty())
        <div class="empty-row">Chưa có tiện ích nào được tạo trong hệ thống.</div>
        @else
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tiện ích</th>
                        <th>Trạng thái</th>
                        <th class="text-right">Tổng lịch đặt</th>
                        <th class="text-right">Đã duyệt</th>
                        <th class="text-right">Hoàn thành</th>
                        <th class="text-right">Từ chối</th>
                        <th class="text-right">Chờ duyệt</th>
                        <th class="text-right">Doanh thu</th>
                        <th class="text-right">Tỉ lệ duyệt</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facilities->sortByDesc('bookings_count') as $f)
                    @php
                        $total = $f->bookings_count;
                        $approved = $f->approved_count;
                        $completed = $f->completed_count;
                        $rate = $total > 0 ? round(($approved + $completed) / $total * 100) : 0;
                        $maxBookings = $facilities->max('bookings_count') ?: 1;
                        $barWidth = $total > 0 ? round($total / $maxBookings * 100) : 0;
                    @endphp
                    <tr>
                        <td>
                            <div class="ams-facility-cell">
                                <a href="{{ portal_route('amenities.show', $f) }}" class="ams-facility-link">{{ $f->name }}</a>
                                @if($total > 0)
                                <div class="ams-bar-wrap">
                                    <div class="ams-bar" style="width:{{ $barWidth }}%"></div>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="ams-stat-badge ams-badge-{{ $f->status }}">{{ $f->status_label }}</span>
                        </td>
                        <td class="text-right font-bold">{{ number_format($total) }}</td>
                        <td class="text-right color-approved">{{ number_format($approved) }}</td>
                        <td class="text-right color-completed">{{ number_format($completed) }}</td>
                        <td class="text-right color-rejected">{{ number_format($f->rejected_count) }}</td>
                        <td class="text-right color-pending">{{ number_format($f->pending_bookings_count) }}</td>
                        <td class="text-right font-bold color-revenue">
                            @if($f->revenue > 0)
                                {{ number_format($f->revenue) }}đ
                            @else
                                <span style="color:#94a3b8; font-weight: normal;">Miễn phí</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="ams-rate-wrap">
                                <span class="ams-rate {{ $rate >= 70 ? 'ams-rate--high' : ($rate >= 40 ? 'ams-rate--mid' : 'ams-rate--low') }}">{{ $rate }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><strong>Tổng cộng</strong></td>
                        <td class="text-right font-bold">{{ number_format($summary['total_bookings']) }}</td>
                        <td class="text-right">{{ number_format($facilities->sum('approved_count')) }}</td>
                        <td class="text-right">{{ number_format($facilities->sum('completed_count')) }}</td>
                        <td class="text-right">{{ number_format($facilities->sum('rejected_count')) }}</td>
                        <td class="text-right">{{ number_format($summary['pending_total']) }}</td>
                        <td class="text-right font-bold color-revenue">{{ number_format($summary['total_revenue']) }}đ</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    {{-- Biểu đồ Chart.js so sánh lượt đặt --}}
    @if($facilities->where('bookings_count', '>', 0)->isNotEmpty())
    <div class="charts-grid" style="grid-template-columns: 1fr;">
        <div class="chart-card">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Biểu đồ so sánh lượt đặt giữa các tiện ích</h3>
                    <p class="chart-card__sub">Tổng quan phân bố số lượng đặt phòng/chỗ theo từng tiện ích</p>
                </div>
                <span class="chart-badge chart-badge--bar">Bar Chart</span>
            </div>
            <div class="chart-wrap" style="height: {{ max(240, $facilities->where('bookings_count', '>', 0)->count() * 45) }}px;">
                <canvas id="bookingsChart"></canvas>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('styles')
    @vite('resources/css/pages/admin/statistics.css')
    <style>
        .ams-facility-link { font-weight: 600; color: #2563eb; text-decoration: none; }
        .ams-facility-link:hover { text-decoration: underline; }
        .ams-facility-cell { display: flex; flex-direction: column; gap: 5px; }
        .ams-bar-wrap { height: 4px; background: #f1f5f9; border-radius: 2px; width: 120px; }
        .ams-bar { height: 4px; background: #3b82f6; border-radius: 2px; transition: width 0.3s; }

        .ams-stat-badge { font-size: 11px; font-weight: 600; padding: 2px 10px; border-radius: 20px; display: inline-block; }
        .ams-badge-available   { background: #dcfce7; color: #15803d; }
        .ams-badge-maintenance { background: #fef9c3; color: #a16207; }
        .ams-badge-closed      { background: #fee2e2; color: #b91c1c; }

        .color-approved  { color: #16a34a; font-weight: 600; }
        .color-completed { color: #2563eb; font-weight: 600; }
        .color-rejected  { color: #b91c1c; font-weight: 600; }
        .color-pending   { color: #d97706; font-weight: 600; }
        .color-revenue   { color: #0f172a; font-weight: 700; }

        .ams-rate { font-size: 11px; font-weight: 700; padding: 2px 10px; border-radius: 20px; display: inline-block; }
        .ams-rate--high { background: #dcfce7; color: #15803d; }
        .ams-rate--mid  { background: #fef9c3; color: #a16207; }
        .ams-rate--low  { background: #fee2e2; color: #b91c1c; }

        .text-right { text-align: right !important; }
        .font-bold { font-weight: 700; }
        
        .data-table tfoot td { background: #f8fafc; font-weight: bold; border-top: 2px solid #e2e8f0; color: #0f172a; }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctxBookings = document.getElementById('bookingsChart');
        if (ctxBookings) {
            const facilityNames = @json($facilities->sortByDesc('bookings_count')->pluck('name'));
            const bookingsCounts = @json($facilities->sortByDesc('bookings_count')->pluck('bookings_count'));

            const tooltipDefaults = {
                padding: 12,
                backgroundColor: '#1e293b',
                titleFont: { size: 13, weight: 'bold' },
                bodyFont: { size: 12 },
                cornerRadius: 8,
            };

            new Chart(ctxBookings.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: facilityNames,
                    datasets: [{
                        label: 'Số lượt đặt chỗ',
                        data: bookingsCounts,
                        backgroundColor: 'rgba(99, 102, 241, 0.85)', // Indigo
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 20
                    }]
                },
                options: {
                    indexAxis: 'y', // Biểu đồ ngang
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { ...tooltipDefaults }
                    },
                    scales: {
                        x: {
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { size: 11 }, color: '#64748b', precision: 0 }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { font: { size: 12, weight: 'bold' }, color: '#1e293b' }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
