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
            <p class="vehicles-page__subtitle">Hệ thống theo dõi và quản lý phương tiện lưu thông trong tòa nhà.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="vehicles-alert vehicles-alert--success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="vehicles-alert vehicles-alert--danger">{{ $errors->first() }}</div>
    @endif

    {{-- Stats Grid --}}
    <div class="vehicles-stats-grid">
        <div class="veh-stat-card" style="border-left: 4px solid #0b57d0;">
            <span class="veh-stat-card__label">Tổng số phương tiện</span>
            <span class="veh-stat-card__value" style="color: #0b57d0;">{{ number_format($vehicles->total()) }}</span>
        </div>
        <div class="veh-stat-card" style="border-left: 4px solid #16a34a;">
            <span class="veh-stat-card__label">Đang hoạt động</span>
            <span class="veh-stat-card__value" style="color: #16a34a;">{{ number_format(App\Models\Vehicle::where('status', 'active')->count()) }}</span>
        </div>
        <div class="veh-stat-card" style="border-left: 4px solid #f59e0b;">
            <span class="veh-stat-card__label">Chờ duyệt / gia hạn</span>
            <span class="veh-stat-card__value" style="color: #f59e0b;">{{ number_format(App\Models\Vehicle::whereIn('status', ['pending', 'pending_renewal'])->count()) }}</span>
        </div>
        <div class="veh-stat-card" style="border-left: 4px solid #dc2626;">
            <span class="veh-stat-card__label">Đã khóa</span>
            <span class="veh-stat-card__value" style="color: #dc2626;">{{ number_format(App\Models\Vehicle::where('status', 'locked')->count()) }}</span>
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
                        <option value="car"           {{ request('vehicle_type') === 'car'           ? 'selected' : '' }}>Ô tô</option>
                        <option value="motorbike"     {{ request('vehicle_type') === 'motorbike'     ? 'selected' : '' }}>Xe máy</option>
                        <option value="electric_bike" {{ request('vehicle_type') === 'electric_bike' ? 'selected' : '' }}>Xe điện</option>
                    </select>
                </div>

                <div>
                    <label>Trạng thái</label>
                    <select name="status" onchange="document.getElementById('filter-form').submit()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending"         {{ request('status') === 'pending'         ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="active"          {{ request('status') === 'active'          ? 'selected' : '' }}>Đang hoạt động</option>
                        <option value="pending_renewal" {{ request('status') === 'pending_renewal' ? 'selected' : '' }}>Chờ gia hạn</option>
                        <option value="locked"          {{ request('status') === 'locked'          ? 'selected' : '' }}>Đã khóa</option>
                        <option value="inactive"        {{ request('status') === 'inactive'        ? 'selected' : '' }}>Ngừng sử dụng</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabs Navigation --}}
    <div class="veh-tabs-nav" style="display: flex; gap: 12px; border-bottom: 1px solid #e2e8f0; margin-bottom: 20px;">
        <a href="{{ route('admin.vehicles.index', array_merge(request()->query(), ['status' => ''])) }}" 
           class="veh-tab-btn {{ request('status') == '' ? 'active' : '' }}" 
           style="padding: 12px 20px; color: {{ request('status') == '' ? '#0b57d0' : '#64748b' }}; border-bottom: 3px solid {{ request('status') == '' ? '#0b57d0' : 'transparent' }}; font-weight: 600; text-decoration: none;">
           Tất cả
        </a>
        <a href="{{ route('admin.vehicles.index', array_merge(request()->query(), ['status' => 'pending'])) }}" 
           class="veh-tab-btn {{ request('status') == 'pending' ? 'active' : '' }}" 
           style="padding: 12px 20px; color: {{ request('status') == 'pending' ? '#0b57d0' : '#64748b' }}; border-bottom: 3px solid {{ request('status') == 'pending' ? '#0b57d0' : 'transparent' }}; font-weight: 600; text-decoration: none;">
           Chờ duyệt
        </a>
        <a href="{{ route('admin.vehicles.index', array_merge(request()->query(), ['status' => 'active'])) }}" 
           class="veh-tab-btn {{ request('status') == 'active' ? 'active' : '' }}" 
           style="padding: 12px 20px; color: {{ request('status') == 'active' ? '#0b57d0' : '#64748b' }}; border-bottom: 3px solid {{ request('status') == 'active' ? '#0b57d0' : 'transparent' }}; font-weight: 600; text-decoration: none;">
           Đang hoạt động
        </a>
        <a href="{{ route('admin.vehicles.index', array_merge(request()->query(), ['status' => 'locked'])) }}" 
           class="veh-tab-btn {{ request('status') == 'locked' ? 'active' : '' }}" 
           style="padding: 12px 20px; color: {{ request('status') == 'locked' ? '#0b57d0' : '#64748b' }}; border-bottom: 3px solid {{ request('status') == 'locked' ? '#0b57d0' : 'transparent' }}; font-weight: 600; text-decoration: none;">
           Đã khóa
        </a>
    </div>

    {{-- Table --}}
    <div class="vehicles-table-card">
        <div class="vehicles-table-wrap">
            <table class="vehicles-table">
                <thead>
                    <tr>
                        <th>Mã căn hộ</th>
                        <th>Tòa / Tầng</th>
                        <th>Chủ xe</th>
                        <th>Biển số xe</th>
                        <th>Loại xe</th>
                        <th>Lốt đỗ</th>
                        <th>Trạng thái</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicles as $v)
                    @php
                        $ownerName = $v->apartment?->residents?->first()?->user?->name ?? '—';
                        $blockName = $v->apartment?->floor?->block?->name ?? '—';
                        $floorName = $v->apartment?->floor?->name ?? '';
                    @endphp
                    <tr>
                        {{-- Mã căn hộ --}}
                        <td>
                            <span class="veh-code">{{ $v->apartment?->apartment_number ?? 'N/A' }}</span>
                        </td>

                        {{-- Tòa / Tầng --}}
                        <td>
                            <div class="veh-location-cell">
                                <span class="veh-location-block">{{ $blockName }}</span>
                                <span class="veh-location-floor">{{ $floorName }}</span>
                            </div>
                        </td>

                        {{-- Chủ xe --}}
                        <td>
                            <div class="veh-owner-cell">
                                <span class="veh-owner-name">{{ $ownerName }}</span>
                            </div>
                        </td>

                        {{-- Biển số --}}
                        <td>
                            <strong>{{ $v->license_plate }}</strong>
                        </td>

                        {{-- Loại xe --}}
                        <td>
                            @if($v->vehicle_type === 'car')
                                Ô tô
                            @elseif($v->vehicle_type === 'electric_bike')
                                Xe điện
                            @else
                                Xe máy
                            @endif
                        </td>

                        {{-- Lốt đỗ --}}
                        <td>
                            @if($v->parking_lot_id && $v->parkingLot)
                                <strong style="color: #0b57d0;">{{ $v->parkingLot->lot_number }}</strong>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Trạng thái --}}
                        <td>
                            <span class="veh-status veh-status--{{ $v->status }}">
                                {{ $v->statusLabel() }}
                            </span>
                        </td>

                        {{-- Thao tác --}}
                        <td class="text-right">
                            <div class="veh-table-actions">
                                
                                {{-- Ô tô chờ duyệt --}}
                                @if($v->status === 'pending' && $v->vehicle_type === 'car')
                                    <form action="{{ route('admin.vehicles.assignLot', $v) }}" method="POST" style="display:flex; gap:6px;">
                                        @csrf
                                        <select name="parking_lot_id" required class="veh-lot-select">
                                            <option value="" disabled selected>Chọn lốt</option>
                                            @foreach($availableLots as $lot)
                                                <option value="{{ $lot->id }}">{{ $lot->lot_number }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="veh-table-btn veh-table-btn--assign" title="Gán lốt">Gán lốt</button>
                                    </form>
                                @endif

                                {{-- Xe máy chờ duyệt --}}
                                @if($v->status === 'pending' && $v->isMotorbike())
                                    <form action="{{ route('admin.vehicles.approve', $v) }}" method="POST" style="display:contents;">
                                        @csrf
                                        <button type="submit" class="veh-table-btn veh-table-btn--approve" title="Duyệt">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                @endif

                                {{-- Active/Pending_Renewal --}}
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

                                {{-- Locked --}}
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
                        <td colspan="8" class="text-center text-muted" style="padding: 40px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.5" style="margin-bottom: 10px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <br>Chưa có phương tiện nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($vehicles->hasPages())
        <div class="vehicles-pagination">
            {{ $vehicles->links() }}
        </div>
    @endif

</div>
@endsection
