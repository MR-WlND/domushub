@extends('layouts.admin.master')

@section('page_title', $facility->name . ' - Chi tiết')

@push('styles')
<style>
.af-page { max-width:1200px; margin:0 auto; }
.af-back { font-size:13px; color:#64748b; text-decoration:none; display:inline-block; margin-bottom:14px; } .af-back:hover { color:#0b57d0; }
.af-alert { padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:13px; }
.af-alert--success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
.af-alert--error { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }

/* Two-column layout */
.af-detail-layout { display:grid; grid-template-columns:300px 1fr; gap:20px; align-items:start; }
@media(max-width:900px) { .af-detail-layout { grid-template-columns:1fr; } }

/* Left panel */
.af-sidebar { background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:20px; position:sticky; top:80px; }
.af-sidebar__badge { display:inline-block; padding:3px 10px; border-radius:5px; font-size:11px; font-weight:600; margin-bottom:10px; }
.af-sidebar__badge--available { background:#dcfce7; color:#166534; }
.af-sidebar__badge--maintenance { background:#fef3c7; color:#92400e; }
.af-sidebar__badge--closed { background:#fee2e2; color:#991b1b; }
.af-sidebar__name { font-size:18px; font-weight:700; color:#0b1c30; margin-bottom:4px; }
.af-sidebar__desc { font-size:12px; color:#64748b; margin-bottom:16px; line-height:1.5; }
.af-sidebar__list { list-style:none; padding:0; margin:0; }
.af-sidebar__list li { display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid #f1f5f9; font-size:13px; }
.af-sidebar__list li:last-child { border-bottom:none; }
.af-sidebar__list-label { color:#64748b; }
.af-sidebar__list-value { color:#0b1c30; font-weight:600; text-align:right; }
.af-sidebar__actions { margin-top:16px; padding-top:14px; border-top:1px solid #e2e8f0; display:flex; gap:8px; }
.af-btn { padding:8px 16px; border-radius:7px; font-size:12px; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:.15s; }
.af-btn--edit { background:#f1f5f9; color:#334155; } .af-btn--edit:hover { background:#e2e8f0; }
.af-btn--sm { padding:5px 12px; font-size:11px; }
.af-btn--approve { background:#dcfce7; color:#166534; } .af-btn--approve:hover { background:#bbf7d0; }
.af-btn--reject { background:#fef2f2; color:#dc2626; } .af-btn--reject:hover { background:#fee2e2; }

/* Right panel */
.af-main { }
.af-main__header { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
.af-main__title { font-size:16px; font-weight:700; color:#0b1c30; }
.af-main__stats { display:flex; gap:16px; font-size:12px; color:#64748b; }
.af-main__stats strong { color:#0b1c30; font-weight:700; }

/* Table */
.af-table-wrap { background:#fff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; }
.af-table { width:100%; border-collapse:collapse; }
.af-table th { text-align:left; padding:10px 16px; font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.3px; background:#f8fafc; border-bottom:1px solid #e2e8f0; }
.af-table td { padding:12px 16px; font-size:13px; color:#334155; border-bottom:1px solid #f1f5f9; }
.af-table tr:last-child td { border-bottom:none; }
.af-table tr:hover td { background:#fafbff; }
.af-table-name { font-weight:600; color:#0b1c30; }
.af-table-sub { font-size:11px; color:#94a3b8; }
.af-table-time { font-family:monospace; font-size:12px; background:#f1f5f9; padding:2px 6px; border-radius:4px; }
.af-badge { display:inline-block; padding:3px 9px; border-radius:5px; font-size:11px; font-weight:600; }
.af-badge--warning { background:#fef3c7; color:#92400e; }
.af-badge--success { background:#dcfce7; color:#166534; }
.af-badge--danger { background:#fee2e2; color:#991b1b; }
.af-badge--info { background:#dbeafe; color:#1e40af; }
.af-badge--muted { background:#f1f5f9; color:#64748b; }
.af-row-actions { display:flex; gap:6px; }
.af-empty { text-align:center; padding:40px 20px; color:#94a3b8; font-size:13px; }
.af-pagination { padding:12px 16px; border-top:1px solid #f1f5f9; }
</style>
@endpush

@section('content')
<div class="af-page">

    <a href="{{ portal_route('amenities.index') }}" class="af-back">← Danh sách tiện ích</a>

    @if(session('success'))<div class="af-alert af-alert--success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="af-alert af-alert--error">{{ session('error') }}</div>@endif

    <div class="af-detail-layout">
        {{-- LEFT: Info Panel --}}
        <div class="af-sidebar">
            <span class="af-sidebar__badge af-sidebar__badge--{{ $facility->status }}">
                {{ $facility->status=='available'?'Hoạt động':($facility->status=='maintenance'?'Bảo trì':'Đóng cửa') }}
            </span>
            <h1 class="af-sidebar__name">{{ $facility->name }}</h1>
            @if($facility->description)
            <p class="af-sidebar__desc">{{ $facility->description }}</p>
            @endif

            <ul class="af-sidebar__list">
                <li><span class="af-sidebar__list-label">Sức chứa</span><span class="af-sidebar__list-value">{{ $facility->capacity }} người</span></li>
                <li><span class="af-sidebar__list-label">Giờ hoạt động</span><span class="af-sidebar__list-value">{{ $facility->operating_hours ?: '—' }}</span></li>
                <li><span class="af-sidebar__list-label">Giá</span><span class="af-sidebar__list-value">{{ $facility->price_label ?: 'Miễn phí' }}</span></li>
                <li><span class="af-sidebar__list-label">Đặt chỗ</span><span class="af-sidebar__list-value">{{ $facility->booking_type_label }}</span></li>
                <li><span class="af-sidebar__list-label">Thời lượng/lượt</span><span class="af-sidebar__list-value">{{ ($facility->slot_duration ?? 0) > 0 ? $facility->slot_duration.' phút' : '—' }}</span></li>
                <li><span class="af-sidebar__list-label">Vị trí</span><span class="af-sidebar__list-value">{{ $facility->block?->name ?: '—' }}{{ $facility->floor ? ', '.$facility->floor->name : '' }}</span></li>
            </ul>

            <div class="af-sidebar__actions">
                <a href="{{ portal_route('amenities.edit', $facility) }}" class="af-btn af-btn--edit">Chỉnh sửa</a>
            </div>
        </div>

        {{-- RIGHT: Bookings --}}
        <div class="af-main">
            <div class="af-main__header">
                <span class="af-main__title">Lịch đặt</span>
            </div>

            <div class="af-table-wrap">
                @if($bookings->isEmpty())
                <div class="af-empty">Chưa có lịch đặt nào.</div>
                @else
                <table class="af-table">
                    <thead>
                        <tr>
                            <th>Cư dân</th>
                            <th>Ngày</th>
                            <th>Giờ</th>
                            <th>Trạng thái</th>
                            <th>Đặt lúc</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr>
                            <td>
                                <div class="af-table-name">{{ $booking->user->name ?? '—' }}</div>
                                <div class="af-table-sub">{{ $booking->user->phone ?? '' }}</div>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                            <td><span class="af-table-time">{{ substr($booking->start_time, 0, 5) }}–{{ substr($booking->end_time, 0, 5) }}</span></td>
                            <td><span class="af-badge af-badge--{{ $booking->status_class }}">{{ $booking->status_label }}</span></td>
                            <td style="font-size:11px;color:#94a3b8;">{{ $booking->created_at->format('d/m H:i') }}</td>
                            <td>
                                @if($booking->status === 'pending')
                                <div class="af-row-actions">
                                    <form method="POST" action="{{ portal_route('amenities.bookings.approve', $booking) }}">@csrf<button class="af-btn af-btn--sm af-btn--approve">Duyệt</button></form>
                                    <form method="POST" action="{{ portal_route('amenities.bookings.reject', $booking) }}" onsubmit="return confirm('Từ chối?')">@csrf<button class="af-btn af-btn--sm af-btn--reject">Từ chối</button></form>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($bookings->hasPages())
                <div class="af-pagination">{{ $bookings->links() }}</div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
