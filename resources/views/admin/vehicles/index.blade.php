@extends('layouts.admin.master')

@section('page_title', 'Quản lý Phương tiện')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/vehicles/index.css'])
@endpush

@section('content')
<div class="vehicles-page">

    {{-- Header --}}
    <div class="vehicles-page__header">
        <div>
            <h1>Quản lý Phương tiện</h1>
            <p class="vehicles-page__subtitle">Theo dõi và quản lý phương tiện lưu thông trong khu chung cư</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="vehicles-alert vehicles-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="vehicles-alert vehicles-alert--danger">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="vehicles-stats-grid">
        <div class="veh-stat-card veh-stat--primary">
            <span class="veh-stat-card__value">{{ number_format($vehicles->total()) }}</span>
            <span class="veh-stat-card__label">Tổng phương tiện</span>
        </div>
        <div class="veh-stat-card veh-stat--success">
            <span class="veh-stat-card__value">{{ number_format(App\Models\Vehicle::where('status', 'active')->count()) }}</span>
            <span class="veh-stat-card__label">Đang hoạt động</span>
        </div>
        <div class="veh-stat-card veh-stat--warning">
            <span class="veh-stat-card__value">{{ number_format(App\Models\Vehicle::whereIn('status', ['pending', 'pending_renewal'])->count()) }}</span>
            <span class="veh-stat-card__label">Chờ duyệt / Gia hạn</span>
        </div>
        <div class="veh-stat-card veh-stat--danger">
            <span class="veh-stat-card__value">{{ number_format(App\Models\Vehicle::where('status', 'locked')->count()) }}</span>
            <span class="veh-stat-card__label">Đã khóa</span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="vehicles-filter-card">
        <form method="GET" id="filter-form">
            <div class="vehicles-filter-grid">
                <div>
                    <label>Tòa nhà</label>
                    <select name="block_id" onchange="document.getElementById('filter-form').submit()">
                        <option value="">Tất cả tòa nhà</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                                {{ $block->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Loại phương tiện</label>
                    <select name="vehicle_type" onchange="document.getElementById('filter-form').submit()">
                        <option value="">Tất cả loại xe</option>
                        <option value="car" {{ request('vehicle_type') === 'car' ? 'selected' : '' }}>Ô tô</option>
                        <option value="motorbike" {{ request('vehicle_type') === 'motorbike' ? 'selected' : '' }}>Xe máy</option>
                        <option value="electric_bike" {{ request('vehicle_type') === 'electric_bike' ? 'selected' : '' }}>Xe điện</option>
                    </select>
                </div>
                <div>
                    <label>Trạng thái</label>
                    <select name="status" onchange="document.getElementById('filter-form').submit()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="pending_renewal" {{ request('status') === 'pending_renewal' ? 'selected' : '' }}>Chờ gia hạn</option>
                        <option value="locked" {{ request('status') === 'locked' ? 'selected' : '' }}>Đã khóa</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Ngừng sử dụng</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabs --}}
    <div class="veh-tabs-nav">
        <a href="{{ route('admin.vehicles.index', array_merge(request()->except('status', 'page'), ['status' => ''])) }}"
           class="veh-tab-btn {{ !request('status') ? 'active' : '' }}">
            Tất cả
        </a>
        <a href="{{ route('admin.vehicles.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}"
           class="veh-tab-btn {{ request('status') == 'pending' ? 'active' : '' }}">
            Chờ duyệt
        </a>
        <a href="{{ route('admin.vehicles.index', array_merge(request()->except('page'), ['status' => 'active'])) }}"
           class="veh-tab-btn {{ request('status') == 'active' ? 'active' : '' }}">
            Đang hoạt động
        </a>
        <a href="{{ route('admin.vehicles.index', array_merge(request()->except('page'), ['status' => 'locked'])) }}"
           class="veh-tab-btn {{ request('status') == 'locked' ? 'active' : '' }}">
            Đã khóa
        </a>
    </div>

    {{-- Table --}}
    <div class="vehicles-table-card">
        <div class="vehicles-table-wrap">
            <table class="vehicles-table">
                <thead>
                    <tr>
                        <th>Căn hộ</th>
                        <th>Tòa / Tầng</th>
                        <th>Chủ xe</th>
                        <th>Biển số</th>
                        <th>Loại xe</th>
                        <th>Lốt đỗ</th>
                        <th>Trạng thái</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $v)
                    @php
                        $owner = $v->apartment?->residents?->first()?->user ?? null;
                        $ownerName = $owner?->name ?? '—';
                        $ownerPhone = $owner?->phone ?? null;
                        $blockName = $v->apartment?->floor?->block?->name ?? '—';
                        $floorName = $v->apartment?->floor?->name ?? '';
                    @endphp
                    <tr>
                        <td>
                            <span class="veh-code">{{ $v->apartment?->apartment_number ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <div class="veh-location-cell">
                                <span class="veh-location-block">{{ $blockName }}</span>
                                @if($floorName)
                                    <span class="veh-location-floor">{{ $floorName }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="veh-owner-cell">
                                <span class="veh-owner-name">{{ $ownerName }}</span>
                                @if($ownerPhone)
                                    <a href="tel:{{ $ownerPhone }}" class="veh-owner-phone">{{ $ownerPhone }}</a>
                                @endif
                            </div>
                        </td>
                        <td>
                            <strong style="font-family: 'JetBrains Mono', 'Fira Code', monospace; font-size: 0.8125rem;">{{ $v->license_plate }}</strong>
                        </td>
                        <td>
                            @if($v->vehicle_type === 'car')
                                <span style="display:inline-flex;align-items:center;gap:4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                                    Ô tô
                                </span>
                            @elseif($v->vehicle_type === 'electric_bike')
                                <span style="display:inline-flex;align-items:center;gap:4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                    Xe điện
                                </span>
                            @else
                                <span style="display:inline-flex;align-items:center;gap:4px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M12 17h5M5 17h2M12 12l2.5-5.5H19l-3 7H9.7L7 11.5"/></svg>
                                    Xe máy
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($v->parking_lot_id && $v->parkingLot)
                                <span style="font-weight:700; color:#4f46e5; background:#eef2ff; padding:3px 8px; border-radius:5px; font-size:0.75rem;">
                                    {{ $v->parkingLot->lot_number }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="veh-status veh-status--{{ $v->status }}">
                                {{ $v->statusLabel() }}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="veh-table-actions">
                                {{-- Ô tô chờ duyệt → gán lốt --}}
                                @if($v->status === 'pending' && $v->vehicle_type === 'car')
                                    <form action="{{ route('admin.vehicles.assignLot', $v) }}" method="POST" style="display:flex; gap:6px; align-items:center;">
                                        @csrf
                                        <select name="parking_lot_id" required class="veh-lot-select">
                                            <option value="" disabled selected>Chọn lốt</option>
                                            @foreach($availableLots as $lot)
                                                <option value="{{ $lot->id }}">{{ $lot->lot_number }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="veh-table-btn veh-table-btn--assign" title="Gán lốt & duyệt">Gán</button>
                                    </form>
                                @endif

                                {{-- Xe máy/điện chờ duyệt → duyệt --}}
                                @if($v->status === 'pending' && $v->isMotorbike())
                                    <form action="{{ route('admin.vehicles.approve', $v) }}" method="POST" style="display:contents;">
                                        @csrf
                                        <button type="submit" class="veh-table-btn veh-table-btn--approve" title="Duyệt xe">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                @endif

                                {{-- Active/Pending Renewal → thu hồi lốt + khóa --}}
                                @if(in_array($v->status, ['active', 'pending_renewal']))
                                    @if($v->vehicle_type === 'car' && $v->parking_lot_id)
                                        <form action="{{ route('admin.vehicles.releaseLot', $v) }}" method="POST" style="display:contents;" onsubmit="return confirm('Thu hồi lốt đỗ của xe này?')">
                                            @csrf
                                            <button type="submit" class="veh-table-btn veh-table-btn--release" title="Thu hồi lốt">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.vehicles.lock', $v) }}" method="POST" style="display:contents;" onsubmit="return confirm('Khóa xe này?')">
                                        @csrf
                                        <button type="submit" class="veh-table-btn veh-table-btn--lock" title="Khóa xe">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                        </button>
                                    </form>
                                @endif

                                {{-- Locked → mở khóa --}}
                                @if($v->status === 'locked')
                                    <form action="{{ route('admin.vehicles.unlock', $v) }}" method="POST" style="display:contents;" onsubmit="return confirm('Mở khóa xe này?')">
                                        @csrf
                                        <button type="submit" class="veh-table-btn veh-table-btn--unlock" title="Mở khóa">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 019.9-1"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="vehicles-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/>
                                <circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>
                            </svg>
                            <p style="margin:8px 0 0; font-size:0.9rem;">Không tìm thấy phương tiện nào</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($vehicles->hasPages())
        <div class="vehicles-pagination">
            {{ $vehicles->links() }}
        </div>
    @endif

</div>
@endsection
