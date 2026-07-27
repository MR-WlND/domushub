@extends('layouts.receptionist.master')

@section('page_title', 'Bưu phẩm – Lễ tân DomusHub')

@push('styles')
<style>
    .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
    .page-header h1{font-size:22px;font-weight:800;color:#1B2559;}
    .btn-primary{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;background:#7B3FE4;color:white;font-size:13px;font-weight:600;text-decoration:none;transition:.2s;border:none;cursor:pointer;font-family:inherit;}
    .btn-primary:hover{background:#6930cc;transform:translateY(-1px);box-shadow:0 6px 16px rgba(123,63,228,.3);}
    .filter-bar{background:white;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;box-shadow:0 2px 8px rgba(123,63,228,.05);}
    .filter-bar select,.filter-bar input{border:1px solid #E9EDF7;border-radius:8px;padding:8px 14px;font-size:13px;font-family:inherit;color:#1B2559;outline:none;background:white;}
    .filter-bar select:focus,.filter-bar input:focus{border-color:#7B3FE4;}
    .filter-bar button{padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;font-family:inherit;background:#7B3FE4;color:white;border:none;cursor:pointer;}
    .table-box{background:white;border-radius:14px;box-shadow:0 2px 8px rgba(123,63,228,.05);overflow:hidden;}
    table{width:100%;border-collapse:collapse;}
    thead{background:#F8F5FF;}
    thead th{padding:14px 16px;font-size:12px;font-weight:700;color:#7B3FE4;text-transform:uppercase;letter-spacing:.5px;text-align:left;}
    tbody tr{border-bottom:1px solid #F4F7FE;transition:.15s;}
    tbody tr:hover{background:#FAFBFF;}
    tbody td{padding:14px 16px;font-size:13px;color:#1B2559;}
    .badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:600;}
    .badge-warning{background:#FFF4E5;color:#FF9B05;}
    .badge-info{background:#EEF2FF;color:#3652D9;}
    .badge-success{background:#E6F9F0;color:#05CD99;}
    .badge-secondary{background:#F4F7FE;color:#707EAE;}
    .action-btn{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;border:none;cursor:pointer;font-family:inherit;transition:.15s;}
    .action-btn-green{background:#E6F9F0;color:#05a56a;}.action-btn-green:hover{background:#c6f0dc;}
    .action-btn-gray{background:#F4F7FE;color:#707EAE;}.action-btn-gray:hover{background:#e5e9f5;}
    .empty-state{text-align:center;padding:60px 20px;color:#A3AED0;}
    .empty-state i{font-size:40px;margin-bottom:12px;display:block;}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-box" style="color:#7B3FE4;margin-right:8px;"></i>Quản lý bưu phẩm</h1>
    <a href="{{ route('receptionist.parcels.create') }}" class="btn-primary">
        <i class="fa-solid fa-plus"></i> Nhận bưu phẩm mới
    </a>
</div>

<!-- Filter -->
<div class="filter-bar">
    <form method="GET" action="{{ route('receptionist.parcels.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;width:100%;align-items:center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm mã vận đơn, người gửi, căn hộ...">
        <select name="status">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Chờ nhận</option>
            <option value="notified" {{ request('status') === 'notified' ? 'selected' : '' }}>Đã thông báo</option>
            <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Đã nhận</option>
            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Đã hoàn trả</option>
        </select>
        <button type="submit"><i class="fa-solid fa-filter"></i> Lọc</button>
    </form>
</div>

<!-- Table -->
<div class="table-box">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Căn hộ</th>
                <th>Người gửi</th>
                <th>Mã vận đơn</th>
                <th>Đơn vị vận chuyển</th>
                <th>Ngày đến</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($parcels as $parcel)
            <tr>
                <td style="color:#A3AED0;font-size:12px;">{{ $parcel->id }}</td>
                <td><strong>{{ $parcel->apartment->apartment_number ?? '—' }}</strong></td>
                <td>{{ $parcel->sender_name }}</td>
                <td>{{ $parcel->tracking_code ?? '—' }}</td>
                <td>{{ $parcel->carrier ?? '—' }}</td>
                <td>{{ $parcel->arrived_at->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="badge badge-{{ $parcel->status_color }}">{{ $parcel->status_label }}</span>
                </td>
                <td style="display:flex;gap:6px;flex-wrap:wrap;">
                    @if($parcel->status === 'pending')
                    <form method="POST" action="{{ route('receptionist.parcels.notify', $parcel->id) }}">
                        @csrf
                        <button class="action-btn action-btn-gray" title="Đã thông báo cư dân">
                            <i class="fa-solid fa-bell"></i> Báo
                        </button>
                    </form>
                    <form method="POST" action="{{ route('receptionist.parcels.received', $parcel->id) }}">
                        @csrf
                        <button class="action-btn action-btn-green">
                            <i class="fa-solid fa-check"></i> Đã nhận
                        </button>
                    </form>
                    @elseif($parcel->status === 'notified')
                    <form method="POST" action="{{ route('receptionist.parcels.received', $parcel->id) }}">
                        @csrf
                        <button class="action-btn action-btn-green">
                            <i class="fa-solid fa-check"></i> Đã nhận
                        </button>
                    </form>
                    <form method="POST" action="{{ route('receptionist.parcels.returned', $parcel->id) }}">
                        @csrf
                        <button class="action-btn action-btn-gray">
                            <i class="fa-solid fa-rotate-left"></i> Hoàn trả
                        </button>
                    </form>
                    @else
                    <span style="font-size:12px;color:#A3AED0;">—</span>
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
