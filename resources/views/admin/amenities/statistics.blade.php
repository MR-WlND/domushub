@extends('layouts.admin.master')

@section('page_title', 'Thống kê tiện ích')

@section('content')
<div class="ams-stat-page">

    {{-- Header --}}
    <div class="ams-stat-header">
        <div>
            <p class="ams-stat-eyebrow">Tiện ích chung cư</p>
            <h1 class="ams-stat-title">Báo cáo thống kê</h1>
        </div>
        <a href="{{ route('admin.amenities.index') }}" class="ams-stat-btn ams-stat-btn--outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Danh sách tiện ích
        </a>
    </div>

    {{-- Summary cards --}}
    <div class="ams-stat-summary">
        <div class="ams-sum-card">
            <div class="ams-sum-icon" style="background:#eff6ff;color:#2563eb">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            </div>
            <div>
                <p class="ams-sum-value">{{ $summary['total_facilities'] }}</p>
                <p class="ams-sum-label">Tổng tiện ích</p>
            </div>
        </div>
        <div class="ams-sum-card">
            <div class="ams-sum-icon" style="background:#faf5ff;color:#7c3aed">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
                <p class="ams-sum-value">{{ number_format($summary['total_bookings']) }}</p>
                <p class="ams-sum-label">Tổng lịch đặt</p>
            </div>
        </div>
        <div class="ams-sum-card">
            <div class="ams-sum-icon" style="background:#f0fdf4;color:#16a34a">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <div>
                <p class="ams-sum-value">{{ number_format($summary['total_revenue']) }}đ</p>
                <p class="ams-sum-label">Tổng doanh thu</p>
            </div>
        </div>
        <div class="ams-sum-card">
            <div class="ams-sum-icon" style="background:#fef9c3;color:#a16207">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <p class="ams-sum-value">{{ $summary['pending_total'] }}</p>
                <p class="ams-sum-label">Chờ duyệt</p>
            </div>
        </div>
    </div>

    {{-- Bảng chi tiết theo tiện ích --}}
    <div class="ams-stat-card">
        <div class="ams-stat-card-header">Thống kê theo từng tiện ích</div>

        @if($facilities->isEmpty())
        <div class="ams-stat-empty">Chưa có dữ liệu.</div>
        @else
        <table class="ams-stat-table">
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
                            <a href="{{ route('admin.amenities.show', $f) }}" class="ams-facility-link">{{ $f->name }}</a>
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
                            <span style="color:#94a3b8">Miễn phí</span>
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
                <tr class="ams-tfoot">
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
        @endif
    </div>

    {{-- Chart lượt đặt theo tiện ích --}}
    @if($facilities->where('bookings_count', '>', 0)->isNotEmpty())
    <div class="ams-stat-card">
        <div class="ams-stat-card-header">Biểu đồ lượt đặt</div>
        <div class="ams-chart">
            @php $maxB = $facilities->max('bookings_count') ?: 1; @endphp
            @foreach($facilities->sortByDesc('bookings_count') as $f)
            <div class="ams-chart-row">
                <div class="ams-chart-label">{{ $f->name }}</div>
                <div class="ams-chart-bar-wrap">
                    <div class="ams-chart-bar" style="width:{{ round($f->bookings_count / $maxB * 100) }}%">
                        <span class="ams-chart-val">{{ $f->bookings_count }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

<style>
.ams-stat-page { max-width: 1200px; margin: 0 auto; padding: 24px 20px; display: flex; flex-direction: column; gap: 20px; }

.ams-stat-header { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; }
.ams-stat-eyebrow { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; margin: 0 0 4px; font-weight: 600; }
.ams-stat-title { font-size: 1.65rem; font-weight: 700; color: #0f172a; margin: 0; }

/* Summary */
.ams-stat-summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
.ams-sum-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; display: flex; align-items: center; gap: 14px; }
.ams-sum-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ams-sum-value { font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0; line-height: 1; }
.ams-sum-label { font-size: 0.78rem; color: #64748b; margin: 4px 0 0; font-weight: 500; }

/* Table card */
.ams-stat-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.ams-stat-card-header { padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; font-weight: 600; color: #334155; background: #f8fafc; }

.ams-stat-table { width: 100%; border-collapse: collapse; }
.ams-stat-table th { text-align: left; padding: 10px 14px; font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; background: #f8fafc; white-space: nowrap; }
.ams-stat-table td { padding: 12px 14px; font-size: 0.875rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.ams-stat-table tr:last-child td { border-bottom: none; }
.ams-stat-table tr:hover td { background: #fafbff; }
.text-right { text-align: right !important; }
.font-bold { font-weight: 700; }

.ams-facility-link { font-weight: 600; color: #2563eb; text-decoration: none; }
.ams-facility-link:hover { text-decoration: underline; }
.ams-facility-cell { display: flex; flex-direction: column; gap: 5px; }
.ams-bar-wrap { height: 4px; background: #f1f5f9; border-radius: 2px; width: 120px; }
.ams-bar { height: 4px; background: #2563eb; border-radius: 2px; transition: width 0.3s; }

.ams-stat-badge { font-size: 0.7rem; font-weight: 600; padding: 2px 8px; border-radius: 20px; }
.ams-badge-available   { background: #dcfce7; color: #15803d; }
.ams-badge-maintenance { background: #fef9c3; color: #a16207; }
.ams-badge-closed      { background: #fee2e2; color: #b91c1c; }

.color-approved  { color: #16a34a; }
.color-completed { color: #2563eb; }
.color-rejected  { color: #b91c1c; }
.color-pending   { color: #d97706; }
.color-revenue   { color: #0f172a; }

.ams-rate { font-size: 0.78rem; font-weight: 700; padding: 2px 8px; border-radius: 20px; }
.ams-rate--high { background: #dcfce7; color: #15803d; }
.ams-rate--mid  { background: #fef9c3; color: #a16207; }
.ams-rate--low  { background: #fee2e2; color: #b91c1c; }

.ams-tfoot td { background: #f8fafc; font-size: 0.85rem; border-top: 2px solid #e2e8f0; }

.ams-stat-empty { text-align: center; padding: 40px; color: #94a3b8; }

/* Chart */
.ams-chart { padding: 16px 18px; display: flex; flex-direction: column; gap: 10px; }
.ams-chart-row { display: flex; align-items: center; gap: 12px; }
.ams-chart-label { min-width: 150px; font-size: 0.82rem; font-weight: 600; color: #334155; text-align: right; flex-shrink: 0; }
.ams-chart-bar-wrap { flex: 1; height: 28px; background: #f1f5f9; border-radius: 6px; overflow: hidden; }
.ams-chart-bar { height: 100%; background: linear-gradient(90deg, #2563eb, #60a5fa); border-radius: 6px; display: flex; align-items: center; justify-content: flex-end; padding-right: 8px; min-width: 30px; transition: width 0.4s; }
.ams-chart-val { font-size: 0.75rem; font-weight: 700; color: #fff; white-space: nowrap; }

.ams-stat-btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 14px; border-radius: 7px; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.15s; border: none; cursor: pointer; }
.ams-stat-btn--outline { background: #fff; color: #475569; border: 1px solid #e2e8f0; }
.ams-stat-btn--outline:hover { background: #f8fafc; }

@media (max-width: 900px) {
    .ams-stat-summary { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 640px) {
    .ams-stat-summary { grid-template-columns: 1fr 1fr; }
    .ams-stat-table { font-size: 0.78rem; }
}
</style>
@endsection
