@extends('layouts.admin.master')

@section('page_title', 'Thống kê Hóa đơn')

@section('content')

{{-- ── Page header ──────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Thống kê Hóa đơn</h1>
        <p class="page-subtitle">Tổng quan tài chính và tình trạng thu phí toàn tòa nhà</p>
    </div>
    <div class="page-actions">
        <form method="GET" action="{{ route('admin.statistics.finance') }}" style="display:flex;gap:8px;align-items:center">
            <select name="year" class="period-select" onchange="this.form.submit()">
                @foreach(range(now()->year, now()->year - 2) as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Tạo hóa đơn
        </a>
        <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline">
            Xem tất cả
        </a>
    </div>
</div>

{{-- ── KPI Cards ────────────────────────────────── --}}
<div class="kpi-grid">

    {{-- Tổng doanh thu --}}
    <div class="kpi-card kpi-total">
        <div class="kpi-header">
            <span class="kpi-label">Tổng đã thu</span>
            <div class="kpi-icon">💰</div>
        </div>
        <div class="kpi-value">{{ number_format($totalRevenue / 1000000, 1) }}M</div>
        <div class="kpi-meta">
            <span class="kpi-delta up">↑ tháng này</span>
            <span>{{ number_format($thisMonthRevenue / 1000000, 1) }}M đ</span>
        </div>
    </div>

    {{-- Đã thanh toán --}}
    <div class="kpi-card kpi-paid">
        <div class="kpi-header">
            <span class="kpi-label">Đã thanh toán</span>
            <div class="kpi-icon">✅</div>
        </div>
        <div class="kpi-value">{{ $paidCount }}</div>
        <div class="kpi-meta">
            <span class="kpi-delta up">
                {{ $totalInvoices > 0 ? round($paidCount / $totalInvoices * 100) : 0 }}%
            </span>
            <span>tổng số hóa đơn</span>
        </div>
    </div>

    {{-- Chưa thanh toán --}}
    <div class="kpi-card kpi-unpaid">
        <div class="kpi-header">
            <span class="kpi-label">Chưa thanh toán</span>
            <div class="kpi-icon">⏳</div>
        </div>
        <div class="kpi-value">{{ $unpaidCount }}</div>
        <div class="kpi-meta">
            <span style="color:var(--amber-600);font-weight:600">
                {{ number_format($totalUnpaid / 1000000, 1) }}M đ
            </span>
            <span>cần thu</span>
        </div>
    </div>

    {{-- Quá hạn --}}
    <div class="kpi-card kpi-overdue">
        <div class="kpi-header">
            <span class="kpi-label">Quá hạn</span>
            <div class="kpi-icon">🚨</div>
        </div>
        <div class="kpi-value">{{ $overdueCount }}</div>
        <div class="kpi-meta">
            <span class="kpi-delta down" style="background:var(--red-50);color:var(--red-600)">
                {{ number_format($totalOverdue / 1000000, 1) }}M đ
            </span>
            <span>rủi ro</span>
        </div>
    </div>

</div>

{{-- ── Charts Row ──────────────────────────────── --}}
<div class="charts-row">

    {{-- Biểu đồ đường: doanh thu 12 tháng --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Doanh thu 12 tháng gần đây</div>
                <div class="chart-card-subtitle">So sánh đã thu / chưa thu / quá hạn theo tháng</div>
            </div>
            <div class="chart-legend">
                <span class="legend-item">
                    <span class="legend-dot" style="background:#3b6ef8"></span> Đã thu
                </span>
                <span class="legend-item">
                    <span class="legend-dot" style="background:#f59e0b"></span> Chưa thu
                </span>
                <span class="legend-item">
                    <span class="legend-dot" style="background:#ef4444"></span> Quá hạn
                </span>
            </div>
        </div>
        <div class="chart-body">
            <div class="chart-wrap">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Biểu đồ tròn: theo loại --}}
    <div class="chart-card">
        <div class="chart-card-header">
            <div>
                <div class="chart-card-title">Phân tích theo loại phí</div>
                <div class="chart-card-subtitle">Tỉ trọng doanh thu từng loại</div>
            </div>
        </div>
        <div class="chart-body">
            <div class="donut-wrap">
                <canvas id="typeChart"></canvas>
                <div class="donut-center-label">
                    <div class="donut-center-value">{{ $byType->count() }}</div>
                    <div class="donut-center-text">Loại phí</div>
                </div>
            </div>

            {{-- Legend danh sách --}}
            <div class="donut-legend">
                @php
                    $typeColors = ['#3b6ef8','#10b981','#f59e0b','#8b5cf6','#06b6d4'];
                    $totalByType = $byType->sum('total') ?: 1;
                @endphp
                @foreach($byType as $i => $t)
                <div class="donut-legend-item">
                    <div class="legend-dot" style="background:{{ $typeColors[$i % 5] }};width:8px;height:8px;border-radius:50%;flex-shrink:0"></div>
                    <span class="donut-legend-label">{{ $t['label'] }}</span>
                    <div class="donut-legend-bar">
                        <div class="donut-legend-fill"
                             style="width:{{ round($t['total']/$totalByType*100) }}%;background:{{ $typeColors[$i % 5] }}"></div>
                    </div>
                    <span class="donut-legend-value">{{ number_format($t['total']/1000000, 1) }}M</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- ── Bottom Row ──────────────────────────────── --}}
<div class="bottom-row">

    {{-- Hóa đơn mới nhất --}}
    <div class="table-card">
        <div class="table-card-header">
            <div class="table-card-title">Hóa đơn gần đây</div>
            <a href="{{ route('admin.invoices.index') }}" class="btn btn-outline btn-sm">Xem tất cả →</a>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mã HD</th>
                    <th>Căn hộ</th>
                    <th>Loại phí</th>
                    <th>Số tiền</th>
                    <th>Trạng thái</th>
                    <th>Hạn TT</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentInvoices as $inv)
                <tr>
                    <td>
                        <span class="invoice-code">{{ $inv->invoice_code }}</span>
                    </td>
                    <td>
                        <div class="apt-label">
                            <div class="apt-icon">🏠</div>
                            <div>
                                <div class="apt-name">{{ $inv->apartment->apartment_number ?? '—' }}</div>
                                <div class="apt-block">
                                    {{ optional(optional($inv->apartment->floor)->block)->name ?? '' }}
                                    {{ optional($inv->apartment->floor)->floor_number ? ' – Tầng ' . $inv->apartment->floor->floor_number : '' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-{{ $inv->type }}">
                            {{ \App\Models\Invoice::typeLabel($inv->type) }}
                        </span>
                    </td>
                    <td class="amount">{{ number_format($inv->amount) }} đ</td>
                    <td>
                        <span class="badge badge-{{ $inv->status }}">
                            {{ \App\Models\Invoice::statusLabel($inv->status) }}
                        </span>
                    </td>
                    <td class="text-muted">{{ $inv->due_date->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <div class="empty-state-icon">📄</div>
                            <div class="empty-state-text">Chưa có hóa đơn nào</div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Top nợ đọng --}}
    <div class="chart-card">
        <div class="chart-card-header" style="padding-bottom:12px">
            <div>
                <div class="chart-card-title">🔴 Nợ đọng theo căn hộ</div>
                <div class="chart-card-subtitle">Top căn hộ chưa thanh toán</div>
            </div>
        </div>
        @php $maxDebt = $topDebt->max('debt') ?: 1; @endphp
        <div class="debt-rank-list">
            @forelse($topDebt as $i => $d)
            <div class="debt-rank-item">
                <div class="debt-rank-num {{ $i===0 ? 'top1' : ($i===1 ? 'top2' : ($i===2 ? 'top3' : '')) }}">
                    {{ $i + 1 }}
                </div>
                <div class="debt-info">
                    <div class="debt-apt">
                        {{ optional($d->apartment)->apartment_number ?? '—' }}
                        @if(optional(optional($d->apartment)->floor)->block)
                            – {{ $d->apartment->floor->block->name }}
                        @endif
                    </div>
                    <div class="debt-sub">{{ $d->count }} hóa đơn</div>
                    <div class="debt-bar-wrap">
                        <div class="debt-bar-fill" style="width:{{ round($d->debt / $maxDebt * 100) }}%"></div>
                    </div>
                </div>
                <div class="debt-amount">{{ number_format($d->debt / 1000) }}K</div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-state-icon">🎉</div>
                <div class="empty-state-text">Không có nợ đọng!</div>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Collection rate ──────────────────────────── --}}
<div class="chart-card" style="margin-bottom:24px">
    <div class="chart-card-header">
        <div>
            <div class="chart-card-title">Tỉ lệ thu tiền theo tháng – Năm {{ $year }}</div>
            <div class="chart-card-subtitle">% hóa đơn đã thanh toán trên tổng số phát hành mỗi tháng</div>
        </div>
    </div>
    <div class="collection-grid">
        @php
            $monthNames = ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'];
            $rateMap = $collectionRate->keyBy('month');
        @endphp
        @for($m = 1; $m <= 12; $m++)
            @php
                $rate = $rateMap->get($m)['rate'] ?? 0;
                $color = $rate >= 80 ? '#10b981' : ($rate >= 50 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="collection-bar-wrap">
                <div class="collection-bar-pct">{{ $rate }}%</div>
                <div class="collection-bar-outer">
                    <div class="collection-bar-inner"
                         style="height:{{ max(4, $rate) }}%;background:{{ $color }}"></div>
                </div>
                <div class="collection-bar-label">{{ $monthNames[$m-1] }}</div>
            </div>
        @endfor
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// ─── Revenue Line Chart ────────────────────────────────────────────
const revenueData = @json($revenueByMonth);

const revenueChart = new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels: revenueData.map(d => d.label),
        datasets: [
            {
                label: 'Đã thu',
                data: revenueData.map(d => d.paid),
                backgroundColor: 'rgba(59,110,248,.85)',
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Chưa thu',
                data: revenueData.map(d => d.unpaid),
                backgroundColor: 'rgba(245,158,11,.75)',
                borderRadius: 6,
                borderSkipped: false,
            },
            {
                label: 'Quá hạn',
                data: revenueData.map(d => d.overdue),
                backgroundColor: 'rgba(239,68,68,.75)',
                borderRadius: 6,
                borderSkipped: false,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0f1729',
                titleColor: '#a8b8d8',
                bodyColor: '#fff',
                borderColor: 'rgba(255,255,255,.08)',
                borderWidth: 1,
                padding: 12,
                callbacks: {
                    label: ctx => ` ${ctx.dataset.label}: ${(ctx.parsed.y/1000000).toFixed(1)}M đ`
                }
            }
        },
        scales: {
            x: {
                stacked: false,
                grid: { display: false },
                ticks: {
                    color: '#9ba8bf',
                    font: { size: 11, family: 'Inter' },
                    maxRotation: 30,
                }
            },
            y: {
                grid: { color: '#eef2fa', drawBorder: false },
                border: { display: false },
                ticks: {
                    color: '#9ba8bf',
                    font: { size: 11, family: 'Inter' },
                    callback: v => (v/1000000).toFixed(0) + 'M'
                }
            }
        }
    }
});

// ─── Donut Chart (by type) ────────────────────────────────────────
const typeData  = @json($byType);
const typeColors = ['#3b6ef8','#10b981','#f59e0b','#8b5cf6','#06b6d4'];

new Chart(document.getElementById('typeChart'), {
    type: 'doughnut',
    data: {
        labels: typeData.map(d => d.label),
        datasets: [{
            data:            typeData.map(d => d.total),
            backgroundColor: typeColors.slice(0, typeData.length),
            borderWidth:     3,
            borderColor:     '#fff',
            hoverOffset:     8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0f1729',
                titleColor: '#a8b8d8',
                bodyColor: '#fff',
                padding: 10,
                callbacks: {
                    label: ctx => ` ${(ctx.parsed/1000000).toFixed(1)}M đ (${ctx.dataset.data.reduce((a,b)=>a+b,0) ? Math.round(ctx.parsed/ctx.dataset.data.reduce((a,b)=>a+b,0)*100) : 0}%)`
                }
            }
        }
    }
});
</script>
@endpush
