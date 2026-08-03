@extends('layouts.resident.master')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.bk-page { 
    max-width: 1100px; 
    margin: 0 auto; 
    padding: 30px 20px; 
    font-family: 'Inter', sans-serif;
    background-color: #f8fafc;
    min-height: calc(100vh - 100px);
}

/* Header */
.bk-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: flex-start; 
    margin-bottom: 24px; 
    flex-wrap: wrap; 
    gap: 16px; 
}
.bk-title { 
    font-size: 26px; 
    font-weight: 700; 
    color: #0f172a; 
    margin: 0 0 4px; 
}
.bk-subtitle { 
    font-size: 14px; 
    color: #475569; 
    margin: 0; 
}
.bk-btn-new { 
    display: inline-flex; 
    align-items: center; 
    gap: 8px; 
    padding: 10px 20px; 
    border-radius: 8px; 
    background-color: #0b1e4a; /* Dark Blue from image */
    color: #fff; 
    font-size: 14px; 
    font-weight: 600; 
    text-decoration: none; 
    transition: all 0.2s; 
}
.bk-btn-new:hover { 
    background-color: #081533; 
}

/* Dashboard Cards */
.bk-dash { 
    display: flex; 
    gap: 24px; 
    margin-bottom: 24px; 
    flex-wrap: wrap; 
}
.bk-card-white { 
    flex: 2; 
    background: #fff; 
    border-radius: 12px; 
    padding: 24px; 
    display: flex; 
    justify-content: space-between; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.05); 
    border: 1px solid #e2e8f0;
}
.bk-card-white-title { 
    font-size: 12px; 
    font-weight: 700; 
    color: #94a3b8; 
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
    margin-bottom: 8px; 
}
.bk-card-white-num { 
    font-size: 42px; 
    font-weight: 800; 
    color: #0f172a; 
    margin-bottom: 8px; 
    line-height: 1; 
}
.bk-card-white-sub { 
    font-size: 13px; 
    font-weight: 600; 
    color: #16a34a; 
}
.bk-card-white-icon { 
    color: #e0e7ff; 
    font-size: 48px;
    margin-top: 10px;
}

.bk-card-blue { 
    flex: 1; 
    background-color: #1a3673; /* Navy blue */
    border-radius: 12px; 
    padding: 24px; 
    color: #fff; 
    display: flex; 
    flex-direction: column; 
    justify-content: center;
    box-shadow: 0 4px 10px rgba(26,54,115,0.2);
}
.bk-card-blue-title { 
    font-size: 12px; 
    font-weight: 600; 
    color: #93c5fd; 
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
    margin-bottom: 20px; 
}
.bk-card-blue-row { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-bottom: 8px; 
}
.bk-card-blue-label { 
    font-size: 15px; 
    color: #bfdbfe; 
}
.bk-card-blue-num { 
    font-size: 16px; 
    font-weight: 700; 
}
.bk-progress-bg { 
    width: 100%; 
    height: 4px; 
    background: rgba(255,255,255,0.2); 
    border-radius: 2px; 
    margin-bottom: 8px; 
    overflow: hidden; 
}
.bk-progress-fill { 
    height: 100%; 
    background: #4ade80; 
    border-radius: 2px; 
}
.bk-card-blue-sub { 
    font-size: 12px; 
    color: #93c5fd; 
}

/* Toolbar */
.bk-toolbar { 
    background: #fff; 
    border-radius: 12px; 
    padding: 16px; 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    flex-wrap: wrap; 
    gap: 16px; 
    border: 1px solid #e2e8f0; 
    margin-bottom: 24px;
}
.bk-filters-left { 
    display: flex; 
    gap: 12px; 
    align-items: center; 
    flex-wrap: wrap; 
}
.bk-search { 
    position: relative; 
    width: 250px; 
}
.bk-search input { 
    width: 100%; 
    padding: 10px 16px 10px 36px; 
    border: 1px solid #e2e8f0; 
    border-radius: 8px; 
    font-size: 13px; 
    outline: none; 
}
.bk-search svg { 
    position: absolute; 
    left: 12px; 
    top: 50%; 
    transform: translateY(-50%); 
    color: #94a3b8; 
}
.bk-select { 
    padding: 10px 16px; 
    border: 1px solid #e2e8f0; 
    border-radius: 8px; 
    font-size: 13px; 
    color: #475569; 
    background: #fff; 
    outline: none; 
}
.bk-filter-btn { 
    background: none; 
    border: none; 
    color: #475569; 
    font-size: 13px; 
    font-weight: 600; 
    display: flex; 
    align-items: center; 
    gap: 6px; 
    cursor: pointer; 
}
.bk-filter-btn:hover { 
    color: #0f172a; 
}

/* Table */
.bk-table-wrap { 
    background: #fff; 
    border-radius: 12px; 
    border: 1px solid #e2e8f0; 
    overflow-x: auto; 
}
.bk-table { 
    width: 100%; 
    border-collapse: collapse; 
    min-width: 800px; 
}
.bk-table th { 
    background: #f8fafc; 
    padding: 16px 20px; 
    text-align: left; 
    font-size: 12px; 
    font-weight: 600; 
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
    border-bottom: 1px solid #e2e8f0; 
}
.bk-table th:last-child {
    text-align: center;
}
.bk-table td { 
    padding: 16px 20px; 
    border-bottom: 1px solid #e2e8f0; 
    vertical-align: middle; 
}
.bk-table td:last-child {
    text-align: center;
}
.bk-table tr:last-child td { 
    border-bottom: none; 
}

/* Facility Cell */
.bk-cell-facility { 
    display: flex; 
    align-items: center; 
    gap: 16px; 
}
.bk-facility-img { 
    width: 50px; 
    height: 50px; 
    border-radius: 8px; 
    object-fit: cover; 
    background: #e2e8f0; 
}
.bk-facility-name { 
    font-size: 15px; 
    font-weight: 700; 
    color: #0f172a; 
    margin: 0 0 4px; 
}
.bk-facility-sub { 
    font-size: 12px; 
    color: #64748b; 
    margin: 0; 
}

/* Time Cell */
.bk-cell-time { 
    font-size: 13px; 
}
.bk-time-date { 
    color: #0f172a; 
    margin: 0 0 4px; 
}
.bk-time-hours { 
    color: #64748b; 
    margin: 0; 
}

/* ID Cell */
.bk-cell-id { 
    font-size: 13px; 
    color: #64748b; 
    font-weight: 500; 
}

/* Status Badge */
.bk-badge { 
    display: inline-flex; 
    align-items: center; 
    gap: 6px; 
    padding: 6px 12px; 
    border-radius: 20px; 
    font-size: 12px; 
    font-weight: 600; 
}
.bk-badge-dot { 
    width: 6px; 
    height: 6px; 
    border-radius: 50%; 
}
/* Approved */
.bk-badge-approved { background: #dcfce7; color: #166534; }
.bk-badge-approved .bk-badge-dot { background: #16a34a; }
/* Used */
.bk-badge-used { background: #e2e8f0; color: #475569; }
.bk-badge-used .bk-badge-dot { background: #64748b; }
/* Pending */
.bk-badge-pending { background: #fef9c3; color: #854d0e; }
.bk-badge-pending .bk-badge-dot { background: #eab308; }
/* Cancelled */
.bk-badge-cancelled { background: #fee2e2; color: #991b1b; }
.bk-badge-cancelled .bk-badge-dot { background: #ef4444; }

/* Actions Cell */
.bk-action-icon { 
    color: #0f172a; 
    cursor: pointer; 
    transition: color 0.2s; 
}
.bk-action-icon:hover { color: #2563eb; }
.bk-action-btn { 
    padding: 6px 14px; 
    border-radius: 6px; 
    font-size: 13px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: all 0.2s; 
    background: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.bk-action-btn-danger { 
    border: 1px solid #fecaca; 
    color: #ef4444; 
}
.bk-action-btn-danger:hover { 
    background: #fef2f2; 
}
.bk-action-btn-primary { 
    background: #e0e7ff; 
    color: #1e40af; 
    border: 1px solid transparent; 
}
.bk-action-btn-primary:hover { 
    background: #dbeafe; 
}

/* Pagination */
.bk-pagination { padding: 20px; }

@media (max-width: 768px) {
    .bk-dash { flex-direction: column; }
    .bk-toolbar { flex-direction: column; align-items: stretch; }
    .bk-search { width: 100%; }
}
</style>
@endpush

@section('title', 'Lịch sử đặt chỗ – DomusHub')

@section('content')
<div class="bk-page">

    {{-- Flash Messages --}}
    @if(session('success'))
    <div style="background: #dcfce7; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display:flex; align-items:center; gap:8px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; display:flex; align-items:center; gap:8px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="bk-header">
        <div>
            <h1 class="bk-title">Lịch sử đặt chỗ</h1>
            <p class="bk-subtitle">Quản lý các tiện ích đã đặt và xem lịch trình sắp tới của bạn.</p>
        </div>
        <a href="{{ route('resident.facilities.index') }}" class="bk-btn-new">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            Đặt chỗ mới
        </a>
    </div>

    @php
        $totalBookings = array_sum($stats);
        $upcomingCount = $stats['pending'] + $stats['approved'];
        $successCount = $stats['used'] + $stats['approved'];
        $successRate = $totalBookings > 0 ? round(($successCount / $totalBookings) * 100) : 0;
        
        // Find nearest upcoming booking dynamically for subtitle
        $nearest = collect($bookings->items())
            ->whereIn('status', ['pending', 'approved'])
            ->where('booking_date', '>=', now()->startOfDay())
            ->sortBy(function($b) { return $b->booking_date->format('Y-m-d') . ' ' . $b->start_time; })
            ->first();
            
        if ($nearest) {
            $isToday = $nearest->booking_date->isToday();
            $nearestText = "Lượt đặt " . Str::lower($nearest->facility->name ?? 'tiện ích') 
                         . " tiếp theo vào " . substr($nearest->start_time, 0, 5) 
                         . ($isToday ? " hôm nay" : " ngày " . $nearest->booking_date->format('d/m'));
        } else {
            $nearestText = "Bạn không có lượt đặt nào sắp tới.";
        }
    @endphp

    {{-- Dash Cards --}}
    <div class="bk-dash">
        <div class="bk-card-white">
            <div>
                <div class="bk-card-white-title">ĐẶT CHỖ SẮP TỚI</div>
                <div class="bk-card-white-num">{{ str_pad($upcomingCount, 2, '0', STR_PAD_LEFT) }}</div>
                <div class="bk-card-white-sub">{{ $nearestText }}</div>
            </div>
            <div class="bk-card-white-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><polyline points="10 15 12 17 16 13"/></svg>
            </div>
        </div>
        
        <div class="bk-card-blue">
            <div class="bk-card-blue-title">TRẠNG THÁI GẦN ĐÂY</div>
            <div class="bk-card-blue-row">
                <span class="bk-card-blue-label">Thành công</span>
                <span class="bk-card-blue-num">{{ $successCount }}</span>
            </div>
            <div class="bk-progress-bg">
                <div class="bk-progress-fill" style="width: {{ $successRate }}%;"></div>
            </div>
            <div class="bk-card-blue-sub">{{ $successRate }}% tổng số lượt đặt</div>
        </div>
    </div>

    {{-- Toolbar --}}
    <form method="GET" class="bk-toolbar">
        <div class="bk-filters-left">
            <div class="bk-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="search" placeholder="Tìm theo ID hoặc tên dịch vụ..." value="{{ request('search') }}">
            </div>
            <select name="status" class="bk-select" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã xác nhận</option>
                <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Đã hoàn thành</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
            </select>
            <select name="facility_id" class="bk-select" onchange="this.form.submit()">
                <option value="">Tất cả tiện ích</option>
                @foreach($facilities as $f)
                <option value="{{ $f->id }}" {{ request('facility_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" class="bk-filter-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            Lọc nâng cao
        </button>
    </form>

    {{-- Table --}}
    <div class="bk-table-wrap">
        <table class="bk-table">
            <thead>
                <tr>
                    <th>TIỆN ÍCH</th>
                    <th>THỜI GIAN</th>
                    <th>SỐ NGƯỜI</th>
                    <th>THANH TOÁN</th>
                    <th>TRẠNG THÁI</th>
                    <th>HÀNH ĐỘNG</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                @php
                    // Define badges based on exact design matching
                    $badgeClass = '';
                    $badgeText = '';
                    if ($booking->status === 'approved') {
                        $badgeClass = 'bk-badge-approved';
                        $badgeText = 'Đã xác nhận';
                    } elseif ($booking->status === 'used') {
                        $badgeClass = 'bk-badge-used';
                        $badgeText = 'Đã hoàn thành';
                    } elseif ($booking->status === 'cancelled') {
                        $badgeClass = 'bk-badge-cancelled';
                        $badgeText = 'Đã hủy';
                    } elseif ($booking->status === 'pending') {
                        $badgeClass = 'bk-badge-pending';
                        $badgeText = 'Chờ duyệt';
                    } else {
                        $badgeClass = 'bk-badge-used';
                        $badgeText = $booking->status_label ?? $booking->status;
                    }
                @endphp
                <tr>
                    <td class="bk-cell-facility">
                        @if($booking->facility && $booking->facility->images && count($booking->facility->images) > 0)
                            <img src="{{ asset('storage/' . $booking->facility->images[0]) }}" alt="Facility" class="bk-facility-img">
                        @else
                            <div class="bk-facility-img" style="display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            </div>
                        @endif
                        <div>
                            <p class="bk-facility-name">{{ $booking->facility->name ?? '—' }}</p>
                            <p class="bk-facility-sub">
                                @if($booking->facility)
                                    @php
                                        $loc = [];
                                        if($booking->facility->floor_id && $booking->facility->floor) $loc[] = $booking->facility->floor->name;
                                        if($booking->facility->block_id && $booking->facility->block) $loc[] = $booking->facility->block->name;
                                    @endphp
                                    {{ count($loc) > 0 ? implode(' - ', $loc) : ($booking->facility->facility_type ?? 'Tiện ích chung') }}
                                @else
                                    Không xác định
                                @endif
                            </p>
                        </div>
                    </td>
                    <td class="bk-cell-time">
                        <p class="bk-time-date">{{ \Carbon\Carbon::parse($booking->booking_date)->locale('vi')->translatedFormat('d M, Y') }}</p>
                        <p class="bk-time-hours">{{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }}</p>
                    </td>
                    <td style="font-size: 13px; font-weight: 600; color: #475569;">
                        {{ $booking->number_of_people ?? 1 }}
                    </td>
                    <td>
                        @if($booking->amount > 0)
                            <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">{{ number_format($booking->amount) }}đ</div>
                            @if($booking->payment_status === 'paid')
                                <span style="font-size: 11px; padding: 3px 8px; border-radius: 4px; background: #dcfce7; color: #166534; font-weight: 600;">Đã thanh toán</span>
                            @else
                                <span style="font-size: 11px; padding: 3px 8px; border-radius: 4px; background: #ffedd5; color: #9a3412; font-weight: 600;">Chưa thanh toán</span>
                            @endif
                        @else
                            <span style="font-size: 12px; font-weight: 600; color: #16a34a;">Miễn phí</span>
                        @endif
                    </td>
                    <td>
                        <span class="bk-badge {{ $badgeClass }}">
                            <span class="bk-badge-dot"></span>
                            {{ $badgeText }}
                        </span>
                    </td>
                    <td>
                        @if($booking->status === 'approved')
                        <a href="{{ route('resident.facility-bookings.qr', $booking) }}" class="bk-action-btn bk-action-btn-primary" style="display:inline-flex; align-items:center; gap:4px; margin-right:4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                            Mã QR
                        </a>
                        @endif

                        @if(in_array($booking->status, ['pending', 'approved']))
                        <form method="POST" action="{{ route('resident.facility-bookings.cancel', $booking) }}" style="margin:0; display:inline-block;" onsubmit="return confirm('Bạn có chắc muốn hủy lịch này?')">
                            @csrf
                            <button type="submit" class="bk-action-btn bk-action-btn-danger">Hủy bỏ</button>
                        </form>
                        @else
                        <a href="{{ route('resident.facilities.show', $booking->facility_id) }}" class="bk-action-btn bk-action-btn-primary" style="display:inline-block;">Đặt lại</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center; padding:40px; color:#64748b;">
                        Không có lịch đặt nào phù hợp với bộ lọc.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($bookings->hasPages())
        <div class="bk-pagination">
            {{ $bookings->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
