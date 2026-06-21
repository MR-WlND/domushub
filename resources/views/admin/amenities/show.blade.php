@extends('layouts.admin.master')

@section('page_title', $facility->name . ' - Lịch đặt')

@section('content')
<div class="ams-page">

    {{-- Breadcrumb --}}
    <div class="ams-breadcrumb">
        <a href="{{ route('admin.amenities.index') }}">Tiện ích chung cư</a>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        <span>{{ $facility->name }}</span>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="ams-alert ams-alert--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="ams-alert ams-alert--error">{{ session('error') }}</div>
    @endif

    {{-- Header --}}
    <div class="ams-header">
        <div class="ams-header-info">
            <span class="ams-status-badge ams-status--{{ $facility->status }}">{{ $facility->status_label }}</span>
            <h1 class="ams-title">{{ $facility->name }}</h1>
            @if($facility->description)
                <p class="ams-desc">{{ $facility->description }}</p>
            @endif
            <p class="ams-capacity">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Sức chứa: <strong>{{ $facility->capacity }} người</strong>
            </p>
        </div>
        <a href="{{ route('admin.amenities.edit', $facility) }}" class="ams-btn ams-btn--outline">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Chỉnh sửa
        </a>
    </div>

    {{-- Thống kê --}}
    <div class="ams-stats">
        <div class="ams-stat">
            <p class="ams-stat-num">{{ $stats['total'] }}</p>
            <p class="ams-stat-lbl">Tổng lịch đặt</p>
        </div>
        <div class="ams-stat ams-stat--yellow">
            <p class="ams-stat-num">{{ $stats['pending'] }}</p>
            <p class="ams-stat-lbl">Chờ duyệt</p>
        </div>
        <div class="ams-stat ams-stat--green">
            <p class="ams-stat-num">{{ $stats['approved'] }}</p>
            <p class="ams-stat-lbl">Đã duyệt</p>
        </div>
        <div class="ams-stat ams-stat--blue">
            <p class="ams-stat-num">{{ $stats['completed'] }}</p>
            <p class="ams-stat-lbl">Hoàn thành</p>
        </div>
    </div>

    {{-- Bảng lịch đặt --}}
    <div class="ams-card">
        <div class="ams-card-header">
            <span>Danh sách lịch đặt</span>
        </div>

        @if($bookings->isEmpty())
        <div class="ams-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <p>Chưa có lịch đặt nào</p>
        </div>
        @else
        <table class="ams-table">
            <thead>
                <tr>
                    <th>Cư dân</th>
                    <th>Ngày đặt</th>
                    <th>Giờ sử dụng</th>
                    <th>Trạng thái</th>
                    <th>Đặt lúc</th>
                    <th style="width:130px"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>
                        <div class="ams-user">
                            <div class="ams-avatar">{{ mb_substr($booking->user->name ?? 'N', 0, 1) }}</div>
                            <div>
                                <p class="ams-user-name">{{ $booking->user->name ?? '—' }}</p>
                                <p class="ams-user-email">{{ $booking->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="ams-date">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</span>
                    </td>
                    <td>
                        <span class="ams-time">{{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}</span>
                    </td>
                    <td>
                        <span class="ams-badge ams-badge--{{ $booking->status_class }}">
                            {{ $booking->status_label }}
                        </span>
                    </td>
                    <td class="ams-created">{{ $booking->created_at->format('d/m H:i') }}</td>
                    <td>
                        @if($booking->status === 'pending')
                        <div class="ams-row-actions">
                            <form method="POST" action="{{ route('admin.amenities.bookings.approve', $booking) }}">
                                @csrf
                                <button type="submit" class="ams-btn ams-btn--xs ams-btn--approve">Duyệt</button>
                            </form>
                            <form method="POST" action="{{ route('admin.amenities.bookings.reject', $booking) }}">
                                @csrf
                                <button type="submit" class="ams-btn ams-btn--xs ams-btn--reject" onclick="return confirm('Từ chối lịch đặt này?')">Từ chối</button>
                            </form>
                        </div>
                        @else
                            <span class="ams-no-action">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($bookings->hasPages())
        <div class="ams-pagination">
            {{ $bookings->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

<style>
.ams-page { max-width: 1100px; margin: 0 auto; padding: 24px 20px; }

.ams-breadcrumb { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; color: #64748b; margin-bottom: 20px; }
.ams-breadcrumb a { color: #2563eb; text-decoration: none; font-weight: 500; }
.ams-breadcrumb a:hover { text-decoration: underline; }

.ams-alert { padding: 12px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 500; margin-bottom: 20px; }
.ams-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.ams-alert--error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }

.ams-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; gap: 16px; flex-wrap: wrap; }
.ams-title { font-size: 1.6rem; font-weight: 700; color: #0f172a; margin: 6px 0 4px; }
.ams-desc { font-size: 0.875rem; color: #64748b; margin: 0 0 8px; }
.ams-capacity { font-size: 0.82rem; color: #475569; display: flex; align-items: center; gap: 5px; margin: 0; }
.ams-capacity strong { color: #0f172a; }

.ams-status-badge { font-size: 0.72rem; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
.ams-status--available   { background: #dcfce7; color: #15803d; }
.ams-status--maintenance { background: #fef9c3; color: #a16207; }
.ams-status--closed      { background: #fee2e2; color: #b91c1c; }

/* Stats */
.ams-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px; }
.ams-stat { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px 18px; text-align: center; }
.ams-stat--yellow .ams-stat-num { color: #d97706; }
.ams-stat--green  .ams-stat-num { color: #16a34a; }
.ams-stat--blue   .ams-stat-num { color: #2563eb; }
.ams-stat-num { font-size: 2rem; font-weight: 700; color: #0f172a; margin: 0; line-height: 1; }
.ams-stat-lbl { font-size: 0.78rem; color: #64748b; margin: 4px 0 0; font-weight: 500; }

/* Table card */
.ams-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.ams-card-header { padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; font-weight: 600; color: #334155; background: #f8fafc; }

.ams-table { width: 100%; border-collapse: collapse; }
.ams-table th { text-align: left; padding: 10px 14px; font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
.ams-table td { padding: 12px 14px; font-size: 0.875rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.ams-table tr:last-child td { border-bottom: none; }
.ams-table tr:hover td { background: #fafbff; }

.ams-user { display: flex; align-items: center; gap: 10px; }
.ams-avatar { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; flex-shrink: 0; }
.ams-user-name { font-weight: 600; color: #0f172a; margin: 0; font-size: 0.875rem; }
.ams-user-email { font-size: 0.75rem; color: #94a3b8; margin: 1px 0 0; }

.ams-date { font-weight: 600; color: #0f172a; }
.ams-time { font-size: 0.82rem; color: #475569; background: #f1f5f9; padding: 3px 8px; border-radius: 5px; font-family: monospace; }
.ams-created { font-size: 0.78rem; color: #94a3b8; }

.ams-badge { font-size: 0.72rem; font-weight: 600; padding: 3px 9px; border-radius: 20px; }
.ams-badge--warning   { background: #fef9c3; color: #a16207; }
.ams-badge--success   { background: #dcfce7; color: #15803d; }
.ams-badge--danger    { background: #fee2e2; color: #b91c1c; }
.ams-badge--secondary { background: #f1f5f9; color: #64748b; }
.ams-badge--info      { background: #e0f2fe; color: #0369a1; }

.ams-row-actions { display: flex; gap: 5px; }
.ams-no-action { color: #cbd5e1; font-size: 0.85rem; }

.ams-btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 14px; border-radius: 7px; font-size: 0.85rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; }
.ams-btn--outline { background: #fff; color: #475569; border: 1px solid #e2e8f0; }
.ams-btn--outline:hover { background: #f8fafc; }
.ams-btn--xs { padding: 4px 10px; font-size: 0.75rem; }
.ams-btn--approve { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.ams-btn--approve:hover { background: #bbf7d0; }
.ams-btn--reject  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.ams-btn--reject:hover  { background: #fecaca; }

.ams-pagination { padding: 14px 18px; border-top: 1px solid #f1f5f9; }
.ams-empty { text-align: center; padding: 48px; color: #94a3b8; }
.ams-empty p { margin-top: 12px; font-size: 0.9rem; }

@media (max-width: 768px) {
    .ams-stats { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endsection
