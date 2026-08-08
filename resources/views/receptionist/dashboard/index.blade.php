@extends('layouts.receptionist.master')

@section('page_title', 'Bảng điều khiển – Lễ tân DomusHub')

@section('topbar_left')
<div class="dashboard-topbar__search">
    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
    </svg>
    <input type="text" class="search-input" placeholder="Tìm kiếm...">
</div>
@endsection

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
    <div>
        @php $hour = now()->hour; $greet = $hour < 12 ? 'Chào buổi sáng' : ($hour < 18 ? 'Chào buổi chiều' : 'Chào buổi tối'); @endphp
        <h1 style="font-size:24px;font-weight:700;color:#00236f;margin-bottom:8px;">{{ $greet }}, {{ explode(' ', auth()->user()->name)[0] }}</h1>
        <p style="color:#475569;margin:0;">Hôm nay có <strong>{{ $pendingParcels }}</strong> bưu phẩm chờ xử lý, <strong>{{ $pendingTickets }}</strong> phản ánh mới và <strong>{{ $visitorsInside }}</strong> khách đang ở trong tòa nhà.</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('receptionist.walk-in.index') }}" class="btn-new-broadcast" style="width:auto; margin-bottom:0; background:#16a34a;">
            <i class="fa-solid fa-user-plus"></i> Đăng ký khách
        </a>
        <a href="{{ route('receptionist.parcels.create') }}" class="btn-new-broadcast" style="width:auto; margin-bottom:0;">
            <i class="fa-solid fa-plus"></i> Nhận bưu phẩm
        </a>
    </div>
</div>

<!-- KPI -->
<div class="dashboard-grid" style="margin-bottom:32px;">
    <div class="dashboard-card dashboard-card--primary">
        <div class="dashboard-card__label">Bưu phẩm chờ nhận</div>
        <h2 style="font-size:32px;margin:0;">{{ $pendingParcels }}</h2>
    </div>
    <div class="dashboard-card dashboard-card--primary">
        <div class="dashboard-card__label">Bưu phẩm hôm nay</div>
        <h2 style="font-size:32px;margin:0;">{{ $todayParcels }}</h2>
    </div>
    <div class="dashboard-card dashboard-card--primary">
        <div class="dashboard-card__label">Phản ánh chờ xử lý</div>
        <h2 style="font-size:32px;margin:0;">{{ $pendingTickets }}</h2>
    </div>
    <div class="dashboard-card dashboard-card--primary">
        <div class="dashboard-card__label">Khách đang ở</div>
        <h2 style="font-size:32px;margin:0;color:#16a34a;">{{ $visitorsInside }}</h2>
    </div>
    <div class="dashboard-card dashboard-card--primary">
        <div class="dashboard-card__label">Lượt khách hôm nay</div>
        <h2 style="font-size:32px;margin:0;">{{ $todayVisitors }}</h2>
    </div>
</div>

<!-- Recent -->
<div class="dashboard-grid">
    <!-- Bưu phẩm gần đây -->
    <div class="dashboard-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="font-size:16px; margin:0;">Bưu phẩm gần đây</h2>
            <a href="{{ route('receptionist.parcels.index') }}" style="font-size:13px; color:#0b57d0; font-weight:600; text-decoration:none;">Xem tất cả →</a>
        </div>
        <div style="display:flex; flex-direction:column; gap:12px;">
            @forelse($recentParcels as $parcel)
            <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; border-bottom:1px solid #e2e8f0;">
                <div>
                    <div style="font-weight:600; color:#0b1c30; font-size:14px;">{{ $parcel->sender_name }}</div>
                    <div style="font-size:12px; color:#757682; margin-top:4px;">Căn hộ {{ $parcel->apartment->apartment_number ?? '—' }} · {{ $parcel->arrived_at->format('d/m H:i') }}</div>
                </div>
                <span class="dashboard-status-pill dashboard-status-pill--{{ $parcel->status === 'pending' ? 'warning' : 'success' }}">{{ $parcel->status_label ?? $parcel->status }}</span>
            </div>
            @empty
            <p style="color:#757682; font-size:14px; text-align:center; padding:16px 0;">Không có bưu phẩm nào.</p>
            @endforelse
        </div>
    </div>

    <!-- Phản ánh gần đây -->
    <div class="dashboard-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="font-size:16px; margin:0;">Phản ánh mới</h2>
            <a href="{{ route('receptionist.tickets.index') }}" style="font-size:13px; color:#0b57d0; font-weight:600; text-decoration:none;">Xem tất cả →</a>
        </div>
        <div style="display:flex; flex-direction:column; gap:12px;">
            @forelse($recentTickets as $ticket)
            <div style="display:flex; justify-content:space-between; align-items:center; padding-bottom:12px; border-bottom:1px solid #e2e8f0;">
                <div>
                    <div style="font-weight:600; color:#0b1c30; font-size:14px;">{{ Str::limit($ticket->title, 40) }}</div>
                    <div style="font-size:12px; color:#757682; margin-top:4px;">{{ $ticket->sender->name ?? '—' }} · {{ $ticket->created_at->diffForHumans() }}</div>
                </div>
                <span class="dashboard-status-pill dashboard-status-pill--warning">{{ $ticket->status }}</span>
            </div>
            @empty
            <p style="color:#757682; font-size:14px; text-align:center; padding:16px 0;">Không có phản ánh nào.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
