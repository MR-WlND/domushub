@extends('layouts.receptionist.master')

@section('page_title', 'Bảng điều khiển – Lễ tân DomusHub')

@push('styles')
<style>
    .greeting{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
    .greeting h1{font-size:24px;font-weight:800;color:#1B2559;letter-spacing:-.3px;}
    .greeting p{font-size:14px;color:#707EAE;margin-top:4px;}
    .kpi-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;}
    .kpi-card{background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(123,63,228,.05);display:flex;align-items:center;gap:14px;}
    .kpi-icon{width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
    .kpi-icon--purple{background:#F5F0FF;color:#7B3FE4;}
    .kpi-icon--orange{background:#FFF4E5;color:#FF9B05;}
    .kpi-icon--blue{background:#EEF2FF;color:#3652D9;}
    .kpi-icon--green{background:#E6F9F0;color:#05CD99;}
    .kpi-value{font-size:28px;font-weight:800;color:#1B2559;line-height:1;}
    .kpi-label{font-size:11.5px;color:#A3AED0;margin-top:3px;}
    .cards-row{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .card-box{background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(123,63,228,.05);}
    .card-box-title{font-size:15px;font-weight:700;color:#1B2559;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
    .item-row{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #F4F7FE;}
    .item-row:last-child{border-bottom:none;}
    .item-label{font-size:13px;font-weight:600;color:#1B2559;}
    .item-sub{font-size:11.5px;color:#A3AED0;margin-top:2px;}
    .badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:600;}
    .badge-warning{background:#FFF4E5;color:#FF9B05;}
    .badge-success{background:#E6F9F0;color:#05CD99;}
    .badge-info{background:#EEF2FF;color:#3652D9;}
    .badge-danger{background:#FFF0F0;color:#EE5D50;}
    .btn-sm{display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;background:#7B3FE4;color:white;transition:.2s;}
    .btn-sm:hover{background:#6930cc;transform:translateY(-1px);}
    @media(max-width:900px){.kpi-row{grid-template-columns:1fr 1fr;}.cards-row{grid-template-columns:1fr;}}
</style>
@endpush

@section('topbar_left')
<div class="topbar-search">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" placeholder="Tìm kiếm..." aria-label="Tìm kiếm">
</div>
@endsection

@section('content')

<div class="greeting">
    <div>
        @php $hour = now()->hour; $greet = $hour < 12 ? 'Chào buổi sáng' : ($hour < 18 ? 'Chào buổi chiều' : 'Chào buổi tối'); @endphp
        <h1>{{ $greet }}, {{ explode(' ', auth()->user()->name)[0] }} 👋</h1>
        <p>Hôm nay có <strong>{{ $pendingParcels }}</strong> bưu phẩm chờ xử lý và <strong>{{ $pendingTickets }}</strong> phản ánh mới.</p>
    </div>
    <a href="{{ route('receptionist.parcels.create') }}" class="btn-sm">
        <i class="fa-solid fa-plus"></i> Nhận bưu phẩm
    </a>
</div>

<!-- KPI -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--purple"><i class="fa-solid fa-box"></i></div>
        <div>
            <div class="kpi-value">{{ $pendingParcels }}</div>
            <div class="kpi-label">Bưu phẩm chờ nhận</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--orange"><i class="fa-solid fa-box-open"></i></div>
        <div>
            <div class="kpi-value">{{ $todayParcels }}</div>
            <div class="kpi-label">Bưu phẩm hôm nay</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--blue"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div>
            <div class="kpi-value">{{ $pendingTickets }}</div>
            <div class="kpi-label">Phản ánh chờ xử lý</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--green"><i class="fa-solid fa-calendar-check"></i></div>
        <div>
            <div class="kpi-value">{{ $pendingBookings }}</div>
            <div class="kpi-label">Đặt lịch chờ duyệt</div>
        </div>
    </div>
</div>

<!-- Recent -->
<div class="cards-row">
    <!-- Bưu phẩm gần đây -->
    <div class="card-box">
        <div class="card-box-title">
            <i class="fa-solid fa-box" style="color:#7B3FE4;"></i> Bưu phẩm gần đây
            <a href="{{ route('receptionist.parcels.index') }}" style="margin-left:auto;font-size:12px;color:#7B3FE4;font-weight:500;text-decoration:none;">Xem tất cả →</a>
        </div>
        @forelse($recentParcels as $parcel)
        <div class="item-row">
            <div>
                <div class="item-label">{{ $parcel->sender_name }}</div>
                <div class="item-sub">Căn hộ {{ $parcel->apartment->apartment_number ?? '—' }} · {{ $parcel->arrived_at->format('d/m H:i') }}</div>
            </div>
            <span class="badge badge-{{ $parcel->status_color }}">{{ $parcel->status_label }}</span>
        </div>
        @empty
        <p style="color:#A3AED0;font-size:13px;text-align:center;padding:16px 0;">Không có bưu phẩm nào.</p>
        @endforelse
    </div>

    <!-- Phản ánh gần đây -->
    <div class="card-box">
        <div class="card-box-title">
            <i class="fa-solid fa-triangle-exclamation" style="color:#3652D9;"></i> Phản ánh mới
            <a href="{{ route('receptionist.tickets.index') }}" style="margin-left:auto;font-size:12px;color:#7B3FE4;font-weight:500;text-decoration:none;">Xem tất cả →</a>
        </div>
        @forelse($recentTickets as $ticket)
        <div class="item-row">
            <div>
                <div class="item-label">{{ Str::limit($ticket->title, 40) }}</div>
                <div class="item-sub">{{ $ticket->sender->name ?? '—' }} · {{ $ticket->created_at->diffForHumans() }}</div>
            </div>
            <span class="badge badge-warning">{{ $ticket->status }}</span>
        </div>
        @empty
        <p style="color:#A3AED0;font-size:13px;text-align:center;padding:16px 0;">Không có phản ánh nào.</p>
        @endforelse
    </div>
</div>
@endsection
