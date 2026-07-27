@extends('layouts.receptionist.master')

@section('page_title', 'Bưu phẩm – Lễ tân DomusHub')



@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <h1 style="font-size:24px;font-weight:700;color:#00236f;margin:0;">Quản lý bưu phẩm</h1>
    <a href="{{ route('receptionist.parcels.create') }}" class="btn-new-broadcast" style="width:auto; margin-bottom:0;">
        <i class="fa-solid fa-plus"></i> Nhận bưu phẩm mới
    </a>
</div>

<!-- Filter -->
<div class="filter-card">
    <form method="GET" action="{{ route('receptionist.parcels.index') }}" class="filter-form">
        <div class="filter-input-wrapper">
            <i class="fa-solid fa-magnifying-glass filter-input-icon"></i>
            <input type="text" name="search" value="{{ request('search') }}" class="filter-input" placeholder="Tìm mã vận đơn, người gửi, căn hộ...">
        </div>
        <select name="status" class="filter-select">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Chờ nhận</option>
            <option value="notified" {{ request('status') === 'notified' ? 'selected' : '' }}>Đã thông báo</option>
            <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Đã nhận</option>
            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Đã hoàn trả</option>
        </select>
        <button type="submit" class="filter-btn-submit"><i class="fa-solid fa-filter"></i> Lọc</button>
        @if(request('search') || request('status'))
            <a href="{{ route('receptionist.parcels.index') }}" class="filter-btn-reset">
                <i class="fa-solid fa-arrows-rotate"></i> Reset
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="dashboard-card" style="padding:0; overflow:hidden;">
    <table style="width:100%; border-collapse:collapse; text-align:left;">
        <thead style="background:#f8f9ff; border-bottom:1px solid #e2e8f0;">
            <tr>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">ID</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Căn hộ</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Người gửi</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Mã vận đơn</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Đơn vị vận chuyển</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Ngày đến</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Trạng thái</th>
                <th style="padding:14px 20px; font-size:12px; font-weight:700; color:#757682; text-transform:uppercase;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($parcels as $parcel)
            <tr style="border-bottom:1px solid #e2e8f0; transition:background-color 0.2s ease;">
                <td style="padding:14px 20px; color:#757682; font-size:13px;">{{ $parcel->id }}</td>
                <td style="padding:14px 20px; color:#0b1c30; font-size:14px;"><strong>{{ $parcel->apartment->apartment_number ?? '—' }}</strong></td>
                <td style="padding:14px 20px; color:#0b1c30; font-size:14px;">{{ $parcel->sender_name }}</td>
                <td style="padding:14px 20px; color:#475569; font-size:14px;">{{ $parcel->tracking_code ?? '—' }}</td>
                <td style="padding:14px 20px; color:#475569; font-size:14px;">{{ $parcel->carrier ?? '—' }}</td>
                <td style="padding:14px 20px; color:#475569; font-size:14px;">{{ $parcel->arrived_at->format('d/m/Y H:i') }}</td>
                <td style="padding:14px 20px;">
                    <span class="dashboard-status-pill dashboard-status-pill--{{ $parcel->status === 'pending' ? 'warning' : 'success' }}">{{ $parcel->status_label }}</span>
                </td>
                <td style="padding:14px 20px; display:flex; gap:8px; flex-wrap:wrap;">
                    @if($parcel->status === 'pending')
                    <form method="POST" action="{{ route('receptionist.parcels.notify', $parcel->id) }}">
                        @csrf
                        <button style="display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:6px; font-size:13px; font-weight:600; border:1px solid #cbd5e1; background:white; color:#475569; cursor:pointer;" title="Đã thông báo cư dân">
                            <i class="fa-solid fa-bell"></i> Báo
                        </button>
                    </form>
                    <form method="POST" action="{{ route('receptionist.parcels.received', $parcel->id) }}">
                        @csrf
                        <button style="display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:6px; font-size:13px; font-weight:600; border:none; background:#e6f4ea; color:#137333; cursor:pointer;">
                            <i class="fa-solid fa-check"></i> Đã nhận
                        </button>
                    </form>
                    @elseif($parcel->status === 'notified')
                    <form method="POST" action="{{ route('receptionist.parcels.received', $parcel->id) }}">
                        @csrf
                        <button style="display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:6px; font-size:13px; font-weight:600; border:none; background:#e6f4ea; color:#137333; cursor:pointer;">
                            <i class="fa-solid fa-check"></i> Đã nhận
                        </button>
                    </form>
                    <form method="POST" action="{{ route('receptionist.parcels.returned', $parcel->id) }}">
                        @csrf
                        <button style="display:inline-flex; align-items:center; gap:5px; padding:6px 12px; border-radius:6px; font-size:13px; font-weight:600; border:1px solid #cbd5e1; background:white; color:#475569; cursor:pointer;">
                            <i class="fa-solid fa-rotate-left"></i> Hoàn trả
                        </button>
                    </form>
                    @else
                    <span style="font-size:13px;color:#94a3b8;">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i class="fa-solid fa-box-open"></i>
                        Chưa có bưu phẩm nào.
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($parcels->hasPages())
<div style="margin-top:20px;">{{ $parcels->links() }}</div>
@endif
@endsection
