@extends('layouts.receptionist.master')

@section('page_title', 'Đặt lịch tiện ích – Lễ tân DomusHub')

@push('styles')
<style>
    .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
    .page-header h1{font-size:22px;font-weight:800;color:#1B2559;}
    .filter-bar{background:white;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;gap:12px;flex-wrap:wrap;align-items:center;box-shadow:0 2px 8px rgba(123,63,228,.05);}
    .filter-bar select,.filter-bar input{border:1px solid #E9EDF7;border-radius:8px;padding:8px 14px;font-size:13px;font-family:inherit;color:#1B2559;outline:none;background:white;}
    .filter-bar select:focus{border-color:#7B3FE4;}
    .filter-bar button{padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;font-family:inherit;background:#7B3FE4;color:white;border:none;cursor:pointer;}
    .table-box{background:white;border-radius:14px;box-shadow:0 2px 8px rgba(123,63,228,.05);overflow:hidden;}
    table{width:100%;border-collapse:collapse;}
    thead{background:#F8F5FF;}
    thead th{padding:14px 16px;font-size:12px;font-weight:700;color:#7B3FE4;text-transform:uppercase;letter-spacing:.5px;text-align:left;}
    tbody tr{border-bottom:1px solid #F4F7FE;transition:.15s;}
    tbody tr:hover{background:#FAFBFF;}
    tbody td{padding:13px 16px;font-size:13px;color:#1B2559;vertical-align:middle;}
    .badge{display:inline-flex;align-items:center;padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:600;}
    .badge-warning{background:#FFF4E5;color:#FF9B05;}
    .badge-success{background:#E6F9F0;color:#05CD99;}
    .badge-danger{background:#FFF0F0;color:#EE5D50;}
    .badge-secondary{background:#F4F7FE;color:#707EAE;}
    .action-btn{display:inline-flex;align-items:center;gap:5px;padding:5px 12px;border-radius:7px;font-size:12px;font-weight:600;border:none;cursor:pointer;font-family:inherit;transition:.15s;}
    .action-btn-green{background:#E6F9F0;color:#05a56a;}.action-btn-green:hover{background:#c6f0dc;}
    .action-btn-red{background:#FFF0F0;color:#dc2626;}.action-btn-red:hover{background:#fee2e2;}
    .empty-state{text-align:center;padding:60px 20px;color:#A3AED0;}
    .empty-state i{font-size:40px;margin-bottom:12px;display:block;}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1><i class="fa-solid fa-calendar-check" style="color:#05CD99;margin-right:8px;"></i>Đặt lịch tiện ích</h1>
</div>

<!-- Filter -->
<div class="filter-bar">
    <form method="GET" action="{{ route('receptionist.amenities.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;width:100%;align-items:center;">
        <select name="facility_id">
            <option value="">-- Tất cả tiện ích --</option>
            @foreach($facilities as $facility)
            <option value="{{ $facility->id }}" {{ request('facility_id') == $facility->id ? 'selected' : '' }}>
                {{ $facility->name }}
            </option>
            @endforeach
        </select>
        <select name="status">
            <option value="">-- Tất cả trạng thái --</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Chờ duyệt</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã duyệt</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Đã từ chối</option>
            <option value="cancelled"{{ request('status') === 'cancelled'? 'selected' : '' }}>Đã hủy</option>
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
                <th>Tiện ích</th>
                <th>Cư dân</th>
                <th>Thời gian đặt</th>
                <th>Thời gian sử dụng</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
            <tr>
                <td style="color:#A3AED0;font-size:12px;">{{ $booking->id }}</td>
                <td><strong>{{ $booking->facility->name ?? '—' }}</strong></td>
                <td>{{ $booking->user->name ?? '—' }}</td>
                <td>{{ $booking->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($booking->start_time)->format('d/m H:i') }}
                    – {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                </td>
                <td>
                    @php
                        $map = [
                            'pending'   => ['Chờ duyệt', 'badge-warning'],
                            'approved'  => ['Đã duyệt',  'badge-success'],
                            'rejected'  => ['Từ chối',   'badge-danger'],
                            'cancelled' => ['Đã hủy',    'badge-secondary'],
                        ];
                        [$label, $cls] = $map[$booking->status] ?? [$booking->status, 'badge-secondary'];
                    @endphp
                    <span class="badge {{ $cls }}">{{ $label }}</span>
                </td>
                <td style="display:flex;gap:6px;">
                    @if($booking->status === 'pending')
                    <form method="POST" action="{{ route('receptionist.amenities.approve', $booking->id) }}">
                        @csrf
                        <button class="action-btn action-btn-green">
                            <i class="fa-solid fa-check"></i> Duyệt
                        </button>
                    </form>
                    <form method="POST" action="{{ route('receptionist.amenities.reject', $booking->id) }}">
                        @csrf
                        <button class="action-btn action-btn-red">
                            <i class="fa-solid fa-xmark"></i> Từ chối
                        </button>
                    </form>
                    @else
                    <span style="font-size:12px;color:#A3AED0;">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="fa-solid fa-calendar-xmark"></i>
                        Chưa có đặt lịch nào.
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($bookings->hasPages())
<div style="margin-top:20px;">{{ $bookings->links() }}</div>
@endif
@endsection
