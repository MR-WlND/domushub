@extends('layouts.resident.master')

@section('title', 'Lịch đặt tiện ích – DomusHub')

@section('content')
<div class="rfh-page">

    {{-- Header --}}
    <div class="rfh-header">
        <div>
            <p class="rfh-eyebrow">Tiện ích chung cư</p>
            <h1 class="rfh-title">Lịch đặt của tôi</h1>
            <p class="rfh-subtitle">Theo dõi và quản lý các lịch đặt tiện ích của bạn.</p>
        </div>
        <a href="{{ route('resident.facilities.index') }}" class="rfh-btn rfh-btn--primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Đặt lịch mới
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div class="rfh-alert rfh-alert--success">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="rfh-alert rfh-alert--error">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Stats --}}
    <div class="rfh-stats">
        <a href="{{ route('resident.facility-bookings.index') }}" class="rfh-stat {{ !request('status') ? 'rfh-stat--active' : '' }}">
            <span class="rfh-stat-num">{{ array_sum($stats) }}</span>
            <span class="rfh-stat-label">Tất cả</span>
        </a>
        <a href="{{ route('resident.facility-bookings.index', ['status' => 'pending']) }}" class="rfh-stat rfh-stat--pending {{ request('status') === 'pending' ? 'rfh-stat--active' : '' }}">
            <span class="rfh-stat-num">{{ $stats['pending'] }}</span>
            <span class="rfh-stat-label">Chờ duyệt</span>
        </a>
        <a href="{{ route('resident.facility-bookings.index', ['status' => 'approved']) }}" class="rfh-stat rfh-stat--approved {{ request('status') === 'approved' ? 'rfh-stat--active' : '' }}">
            <span class="rfh-stat-num">{{ $stats['approved'] }}</span>
            <span class="rfh-stat-label">Đã duyệt</span>
        </a>
        <a href="{{ route('resident.facility-bookings.index', ['status' => 'used']) }}" class="rfh-stat rfh-stat--used {{ request('status') === 'used' ? 'rfh-stat--active' : '' }}">
            <span class="rfh-stat-num">{{ $stats['used'] }}</span>
            <span class="rfh-stat-label">Đã sử dụng</span>
        </a>
        <a href="{{ route('resident.facility-bookings.index', ['status' => 'cancelled']) }}" class="rfh-stat rfh-stat--cancelled {{ request('status') === 'cancelled' ? 'rfh-stat--active' : '' }}">
            <span class="rfh-stat-num">{{ $stats['cancelled'] }}</span>
            <span class="rfh-stat-label">Đã hủy</span>
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="rfh-filter">
        <select name="facility_id" onchange="this.form.submit()">
            <option value="">Tất cả tiện ích</option>
            @foreach($facilities as $f)
            <option value="{{ $f->id }}" {{ request('facility_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
            @endforeach
        </select>
        @if(request('facility_id') || request('status'))
        <a href="{{ route('resident.facility-bookings.index') }}" class="rfh-clear-filter">Xóa bộ lọc</a>
        @endif
    </form>

    {{-- List --}}
    @if($bookings->isEmpty())
    <div class="rfh-empty">
        <div class="rfh-empty-icon">📅</div>
        <p class="rfh-empty-title">Chưa có lịch đặt nào</p>
        <p class="rfh-empty-sub">Hãy khám phá các tiện ích và đặt lịch sử dụng.</p>
        <a href="{{ route('resident.facilities.index') }}" class="rfh-btn rfh-btn--primary">Xem tiện ích</a>
    </div>
    @else
    <div class="rfh-list">
        @foreach($bookings as $booking)
        <div class="rfh-item rfh-item--{{ $booking->status }}">
            {{-- Left: Status bar --}}
            <div class="rfh-item-bar rfh-bar--{{ $booking->status_class }}"></div>

            {{-- Main content --}}
            <div class="rfh-item-main">
                <div class="rfh-item-top">
                    <div class="rfh-item-facility">
                        <h3 class="rfh-facility-name">{{ $booking->facility->name ?? '—' }}</h3>
                        <span class="rfh-badge rfh-badge--{{ $booking->status_class }}">{{ $booking->status_label }}</span>
                    </div>
                    <div class="rfh-item-id">#{{ $booking->id }}</div>
                </div>

                <div class="rfh-item-details">
                    <div class="rfh-detail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}
                        ({{ \Carbon\Carbon::parse($booking->booking_date)->locale('vi')->dayName }})
                    </div>
                    <div class="rfh-detail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}
                    </div>
                    <div class="rfh-detail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        {{ $booking->number_of_people ?? 1 }} người
                    </div>
                    @php $amount = $booking->amount; @endphp
                    @if($amount > 0)
                    <div class="rfh-detail rfh-detail--price">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        {{ number_format($amount) }}đ
                        @if($booking->payment_status === 'paid')
                        <span class="rfh-paid-badge">✓ Đã TT</span>
                        @else
                        <span class="rfh-unpaid-badge">Chưa TT</span>
                        @endif
                    </div>
                    @endif
                </div>

                @if($booking->checked_in_at)
                <div class="rfh-checkin-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Check-in lúc {{ $booking->checked_in_at->format('H:i d/m/Y') }}
                </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="rfh-item-actions">
                @if($booking->status === 'approved')
                    @if($booking->amount > 0 && $booking->payment_status !== 'paid')
                    <a href="{{ route('resident.invoices.index') }}" class="rfh-action rfh-action--pay">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                        Thanh toán hóa đơn
                    </a>
                    @endif
                @endif

                @if(in_array($booking->status, ['pending', 'approved']))
                <form method="POST" action="{{ route('resident.facility-bookings.cancel', $booking) }}" onsubmit="return confirm('Bạn có chắc muốn hủy lịch này?')">
                    @csrf
                    <button type="submit" class="rfh-action rfh-action--cancel">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        Hủy lịch
                    </button>
                </form>
                @endif

                <span class="rfh-created-at">{{ $booking->created_at->format('H:i d/m/Y') }}</span>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($bookings->hasPages())
    <div class="rfh-pagination">{{ $bookings->links() }}</div>
    @endif
    @endif

</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

.rfh-page { max-width: 900px; margin: 0 auto; padding: 28px 20px; font-family: 'Inter', sans-serif; }

.rfh-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.rfh-eyebrow { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; margin: 0 0 4px; font-weight: 600; }
.rfh-title { font-size: 1.8rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
.rfh-subtitle { font-size: 0.875rem; color: #64748b; margin: 0; }

.rfh-alert { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 500; margin-bottom: 20px; }
.rfh-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.rfh-alert--error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }

/* Stats */
.rfh-stats { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
.rfh-stat { display: flex; flex-direction: column; align-items: center; padding: 12px 18px; background: #fff; border: 1.5px solid #e2e8f0; border-radius: 12px; text-decoration: none; color: inherit; transition: all 0.15s; min-width: 80px; }
.rfh-stat:hover { border-color: #3b82f6; background: #eff6ff; }
.rfh-stat--active { border-color: #3b82f6; background: #eff6ff; }
.rfh-stat-num { font-size: 1.4rem; font-weight: 800; color: #0f172a; }
.rfh-stat-label { font-size: 0.72rem; font-weight: 600; color: #64748b; white-space: nowrap; }
.rfh-stat--pending .rfh-stat-num { color: #d97706; }
.rfh-stat--approved .rfh-stat-num { color: #16a34a; }
.rfh-stat--used .rfh-stat-num { color: #0369a1; }
.rfh-stat--cancelled .rfh-stat-num { color: #94a3b8; }

/* Filter */
.rfh-filter { display: flex; gap: 10px; align-items: center; margin-bottom: 20px; flex-wrap: wrap; }
.rfh-filter select { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; color: #374151; background: #fff; outline: none; }
.rfh-filter select:focus { border-color: #3b82f6; }
.rfh-clear-filter { font-size: 0.8rem; color: #64748b; text-decoration: none; padding: 6px 12px; background: #f1f5f9; border-radius: 7px; }
.rfh-clear-filter:hover { background: #e2e8f0; }

/* List */
.rfh-list { display: flex; flex-direction: column; gap: 14px; }
.rfh-item { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; display: flex; overflow: hidden; transition: box-shadow 0.2s; }
.rfh-item:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

.rfh-item-bar { width: 5px; flex-shrink: 0; }
.rfh-bar--warning   { background: linear-gradient(to bottom, #f59e0b, #fbbf24); }
.rfh-bar--success   { background: linear-gradient(to bottom, #22c55e, #4ade80); }
.rfh-bar--info      { background: linear-gradient(to bottom, #0ea5e9, #38bdf8); }
.rfh-bar--secondary { background: linear-gradient(to bottom, #94a3b8, #cbd5e1); }
.rfh-bar--danger    { background: linear-gradient(to bottom, #ef4444, #f87171); }

.rfh-item-main { flex: 1; padding: 16px 18px; }
.rfh-item-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; gap: 10px; }
.rfh-item-facility { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.rfh-facility-name { font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0; }
.rfh-item-id { font-size: 0.75rem; color: #94a3b8; font-weight: 500; white-space: nowrap; }

.rfh-badge { font-size: 0.7rem; font-weight: 700; padding: 3px 9px; border-radius: 20px; white-space: nowrap; }
.rfh-badge--warning   { background: #fef9c3; color: #a16207; }
.rfh-badge--success   { background: #dcfce7; color: #15803d; }
.rfh-badge--info      { background: #e0f2fe; color: #0369a1; }
.rfh-badge--secondary { background: #f1f5f9; color: #64748b; }
.rfh-badge--danger    { background: #fee2e2; color: #b91c1c; }

.rfh-item-details { display: flex; flex-wrap: wrap; gap: 14px; }
.rfh-detail { display: flex; align-items: center; gap: 5px; font-size: 0.82rem; color: #475569; }
.rfh-detail--price { font-weight: 600; color: #2563eb; }
.rfh-detail--free  { color: #16a34a; font-weight: 500; }
.rfh-paid-badge   { font-size: 0.68rem; background: #dcfce7; color: #15803d; padding: 2px 6px; border-radius: 10px; font-weight: 700; }
.rfh-unpaid-badge { font-size: 0.68rem; background: #fff7ed; color: #c2410c; padding: 2px 6px; border-radius: 10px; font-weight: 700; }

.rfh-checkin-info { margin-top: 8px; display: flex; align-items: center; gap: 5px; font-size: 0.75rem; color: #16a34a; font-weight: 500; }

/* Actions */
.rfh-item-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; padding: 16px 16px 16px 0; justify-content: center; }
.rfh-action { display: inline-flex; align-items: center; gap: 5px; padding: 7px 12px; border-radius: 8px; font-size: 0.78rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; white-space: nowrap; }
.rfh-action--qr { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
.rfh-action--qr:hover { background: #dbeafe; }
.rfh-action--pay { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.rfh-action--pay:hover { background: #d1fae5; }
.rfh-action--cancel { background: #fff; color: #ef4444; border: 1px solid #fecaca; }
.rfh-action--cancel:hover { background: #fef2f2; }
.rfh-created-at { font-size: 0.72rem; color: #cbd5e1; }

/* Button */
.rfh-btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; border-radius: 10px; font-size: 0.875rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.15s; }
.rfh-btn--primary { background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; box-shadow: 0 3px 10px rgba(59,130,246,0.3); }
.rfh-btn--primary:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(59,130,246,0.4); }

/* Empty */
.rfh-empty { text-align: center; padding: 64px 20px; display: flex; flex-direction: column; align-items: center; gap: 12px; }
.rfh-empty-icon { font-size: 3rem; }
.rfh-empty-title { font-size: 1.1rem; font-weight: 700; color: #334155; margin: 0; }
.rfh-empty-sub { font-size: 0.875rem; color: #94a3b8; margin: 0; }

/* Pagination */
.rfh-pagination { padding: 20px 0 0; }
</style>

@endsection
