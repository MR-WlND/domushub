@extends('layouts.admin.master')

@section('page_title', 'Thống kê Vận hành & SLA - DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="stat-page">

    {{-- HEADER + BỘ LỌC NĂM --}}
    <div class="stat-header">
        <div class="stat-header__text">
            <h1 class="stat-title">Thống kê Vận hành & Chất lượng SLA</h1>
            <p class="stat-subtitle">Đánh giá chất lượng dịch vụ phản ánh, thời gian xử lý sự cố kỹ thuật và mức độ hài lòng CSAT.</p>
        </div>
        <div class="stat-header__filter">
            <form method="GET" action="{{ route('admin.statistics.operations') }}" class="year-filter-form">
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
            <a href="{{ route('admin.statistics.operations.export', ['year' => $selectedYear]) }}" class="btn-export">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Xuất Excel
            </a>
        </div>
    </div>

    {{-- KPI: VẬN HÀNH & SLA --}}
    <div class="section-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Chỉ số hiệu suất vận hành năm {{ $selectedYear }}
    </div>
    <div class="kpi-grid">
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Tổng tiếp nhận phản ánh</span>
                <span class="kpi-value">{{ $totalTickets }}</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Đang xử lý / Chờ phân công</span>
                <span class="kpi-value">{{ $pendingCount + $assignedCount + $inProgressCount }}</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Đã xử lý hoàn thành</span>
                <span class="kpi-value">{{ $completedCount }}</span>
            </div>
        </div>
        <div class="kpi-card kpi-card--indigo">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.907c.961 0 1.36 1.24.588 1.81l-3.97 2.883a1 1 0 00-.364 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.971-2.883a1 1 0 00-1.175 0l-3.97 2.883c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.364-1.118L2.98 10.3c-.773-.57-.375-1.81.588-1.81h4.907a1 1 0 00.95-.69l1.519-4.674z"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Điểm đánh giá CSAT</span>
                <span class="kpi-value">{{ $averageRating > 0 ? $averageRating : 'N/A' }} / 5.0</span>
                @if($totalRated > 0)
                    <span style="font-size: 11px; color: #64748b; font-weight: 500;">Dựa trên {{ $totalRated }} đánh giá</span>
                @else
                    <span style="font-size: 11px; color: #94a3b8; font-style: italic;">Chưa có đánh giá</span>
                @endif
            </div>
        </div>
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-icon" style="background: #e0f2fe; color: #0284c7;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Thời gian xử lý TB (SLA)</span>
                <span class="kpi-value">{{ $formattedResolutionTime }}</span>
            </div>
        </div>
    </div>

    {{-- BIỂU ĐỒ --}}
    <div class="charts-grid">
        {{-- Biểu đồ cột ngang (Horizontal Bar Chart) --}}
        <div class="chart-card chart-card--main">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Trạng thái xử lý phản ánh — Năm {{ $selectedYear }}</h3>
                    <p class="chart-card__sub">Biểu diễn phân bố trạng thái các ticket tiếp nhận</p>
                </div>
                <span class="chart-badge chart-badge--bar">Horizontal Bar</span>
            </div>
            <div class="chart-wrap">
                @if($totalTickets > 0)
                    <canvas id="ticketStatusChart"></canvas>
                @else
                    <div class="empty-chart-text">Không có phản ánh được ghi nhận trong năm {{ $selectedYear }}</div>
                @endif
            </div>
        </div>

        {{-- Biểu đồ CSAT (Donut Chart) --}}
        <div class="chart-card">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Cơ cấu đánh giá hài lòng</h3>
                    <p class="chart-card__sub">Tỷ trọng sao đánh giá (CSAT) của cư dân</p>
                </div>
                <span class="chart-badge chart-badge--donut">Donut Chart</span>
            </div>
            <div class="chart-wrap chart-wrap--donut">
                @if($totalRated > 0)
                    <canvas id="csatChart"></canvas>
                @else
                    <div class="empty-chart-text">Chưa nhận được đánh giá nào trong năm {{ $selectedYear }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- HÀNG BIỂU ĐỒ 2: MỨC ĐỘ ƯU TIÊN & SLA TARGETS --}}
    <div class="charts-grid" style="margin-top: 24px; grid-template-columns: 1fr 1fr;">
        {{-- Biểu đồ Donut: Mức độ ưu tiên phản ánh --}}
        <div class="chart-card">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Cơ cấu mức độ ưu tiên phản ánh</h3>
                    <p class="chart-card__sub">Tỷ trọng các phản ánh theo mức độ ưu tiên</p>
                </div>
                <span class="chart-badge chart-badge--donut">Donut Chart</span>
            </div>
            <div class="chart-wrap chart-wrap--donut">
                @if($totalTickets > 0)
                    <canvas id="priorityChart"></canvas>
                @else
                    <div class="empty-chart-text">Không có phản ánh trong năm {{ $selectedYear }}</div>
                @endif
            </div>
        </div>

        {{-- SLA response time summary --}}
        <div class="chart-card">
            <div class="chart-card__header">
                <div>
                    <h3 class="chart-card__title">Cam kết chất lượng xử lý (SLA)</h3>
                    <p class="chart-card__sub">Mục tiêu thời gian khắc phục sự cố theo phân loại</p>
                </div>
                <span class="chart-badge chart-badge--bar" style="background: #ecfdf5; color: #059669;">SLA Target</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 14px; font-size: 14px; color: #475569; padding-top: 12px; justify-content: center; height: 100%;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                    <span style="font-weight: 700; color: #ef4444; display: flex; align-items: center; gap: 6px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                        Khẩn cấp (Urgent)
                    </span>
                    <span style="font-weight: 600; color: #1e293b;">&lt; 2 giờ</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                    <span style="font-weight: 700; color: #f97316; display: flex; align-items: center; gap: 6px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #f97316; display: inline-block;"></span>
                        Cao (High)
                    </span>
                    <span style="font-weight: 600; color: #1e293b;">&lt; 4 giờ</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                    <span style="font-weight: 700; color: #3b82f6; display: flex; align-items: center; gap: 6px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #3b82f6; display: inline-block;"></span>
                        Trung bình (Medium)
                    </span>
                    <span style="font-weight: 600; color: #1e293b;">&lt; 12 giờ</span>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 4px;">
                    <span style="font-weight: 700; color: #94a3b8; display: flex; align-items: center; gap: 6px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #94a3b8; display: inline-block;"></span>
                        Thấp (Low)
                    </span>
                    <span style="font-weight: 600; color: #1e293b;">&lt; 24 giờ</span>
                </div>
            </div>
        </div>
    </div>

    {{-- PHẢN HỒI GẦN NHẤT --}}
    <div class="table-card">
        <div class="table-card__header">
            <h3>Danh sách đánh giá & phản hồi gần nhất — Năm {{ $selectedYear }}</h3>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Cư dân / Căn hộ</th>
                        <th style="width: 25%;">Phản ánh sự cố</th>
                        <th style="width: 15%;">Số sao</th>
                        <th style="width: 25%;">Ý kiến phản hồi</th>
                        <th style="width: 15%;">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentFeedbacks as $fb)
                        <tr>
                            <td>
                                <div><strong>{{ $fb->sender->name ?? 'Cư dân' }}</strong></div>
                                <div style="font-size: 12px; color: #64748b;">P. {{ $fb->apartment->name ?? 'N/A' }}</div>
                            </td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $fb->id) }}" style="color: #3b82f6; font-weight: 500; text-decoration: none;">
                                    {{ $fb->title }}
                                </a>
                            </td>
                            <td>
                                <div style="display: flex; gap: 2px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $fb->rating)
                                            <span style="color: #f59e0b; font-size: 16px;">★</span>
                                        @else
                                            <span style="color: #cbd5e1; font-size: 16px;">★</span>
                                        @endif
                                    @endfor
                                </div>
                            </td>
                            <td>
                                <span style="font-style: italic; color: #475569;">
                                    "{{ $fb->feedback_comment ?? 'Không có bình luận đóng góp' }}"
                                </span>
                            </td>
                            <td>
                                {{ $fb->updated_at ? $fb->updated_at->format('d/m/Y H:i') : '' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-row">Không có đánh giá phản hồi nào từ cư dân trong năm {{ $selectedYear }}.</td>
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
        const tooltipDefaults = {
            padding: 12,
            backgroundColor: '#1e293b',
            titleFont: { size: 13, weight: 'bold' },
            bodyFont: { size: 12 },
            cornerRadius: 8,
        };

        // 1. TICKET STATUS CHART (Horizontal Bar Chart)
        const ctxStatus = document.getElementById('ticketStatusChart');
        if (ctxStatus) {
            new Chart(ctxStatus.getContext('2d'), {
                type: 'bar', // Horizontal bar is set by indexAxis: 'y'
                data: {
                    labels: ['Chờ xử lý', 'Đã phân công', 'Đang xử lý', 'Hoàn thành', 'Đã huỷ'],
                    datasets: [{
                        label: 'Số lượng phản ánh',
                        data: [
                            {{ $pendingCount }},
                            {{ $assignedCount }},
                            {{ $inProgressCount }},
                            {{ $completedCount }},
                            {{ $cancelledCount }}
                        ],
                        backgroundColor: [
                            'rgba(245, 158, 11, 0.85)',  // Amber (Pending)
                            'rgba(99, 102, 241, 0.85)',   // Indigo (Assigned)
                            'rgba(59, 130, 246, 0.85)',   // Blue (In Progress)
                            'rgba(16, 185, 129, 0.85)',   // Green (Completed)
                            'rgba(148, 163, 184, 0.85)'   // Slate (Cancelled)
                        ],
                        borderRadius: 6,
                        borderSkipped: false,
                        barThickness: 24,
                    }]
                },
                options: {
                    indexAxis: 'y',
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

        // 2. CSAT RATING DONUT CHART (Donut/Pie Chart)
        const ctxCsat = document.getElementById('csatChart');
        if (ctxCsat) {
            const csatData = @json(array_values($csatData)); // [1-star, 2-star, ..., 5-star]
            
            // Labels should align with data
            const labels = ['1 Sao', '2 Sao', '3 Sao', '4 Sao', '5 Sao'];
            
            new Chart(ctxCsat.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: csatData,
                        backgroundColor: [
                            '#ef4444', // 1 star (Red)
                            '#f97316', // 2 star (Orange)
                            '#f59e0b', // 3 star (Amber)
                            '#84cc16', // 4 star (Light Green)
                            '#10b981'  // 5 star (Emerald Green)
                        ],
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
                                label: ctx => ` ${ctx.label}: ${ctx.parsed} phản ánh`
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }

        // 3. PRIORITY RATING DONUT CHART (Donut Chart)
        const ctxPriority = document.getElementById('priorityChart');
        if (ctxPriority) {
            const priorityData = @json(array_values($priorityData)); // [low, medium, high, urgent]
            
            new Chart(ctxPriority.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Thấp', 'Trung bình', 'Cao', 'Khẩn cấp'],
                    datasets: [{
                        data: priorityData,
                        backgroundColor: [
                            '#94a3b8', // Low (Slate/Gray)
                            '#3b82f6', // Medium (Blue)
                            '#f59e0b', // High (Amber)
                            '#ef4444'  // Urgent (Red)
                        ],
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
                                label: ctx => ` ${ctx.label}: ${ctx.parsed} phản ánh`
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        }
    });
</script>
@endpush
