@extends('layouts.admin.master')

@section('page_title', 'Quản lý Bãi đỗ xe')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/parking-lots/index.css'])
@endpush

@section('content')
<div class="parking-page">

    {{-- Header --}}
    <div class="parking-page__header">
        <div>
            <h1>Quản lý Bãi đỗ xe</h1>
            <p class="parking-page__subtitle">Quản lý lốt đỗ cho ô tô và các khu vực dành cho xe máy, xe điện.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="parking-alert parking-alert--success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="parking-alert parking-alert--danger">{{ $errors->first() }}</div>
    @endif

    {{-- Stats --}}
    @php
        $totalCar = $carLots->count();
        $availableCar = $carLots->where('status', 'available')->count();
        $occupiedCar = $carLots->where('status', 'occupied')->count();
        $totalMotoZones = $motorbikeLots->count();
    @endphp
    <div class="parking-stats-grid">
        <div class="pk-stat-card pk-stat--primary">
            <span class="pk-stat-card__value">{{ $totalCar }}</span>
            <span class="pk-stat-card__label">Tổng lốt ô tô</span>
        </div>
        <div class="pk-stat-card pk-stat--success">
            <span class="pk-stat-card__value">{{ $availableCar }}</span>
            <span class="pk-stat-card__label">Lốt trống</span>
        </div>
        <div class="pk-stat-card pk-stat--warning">
            <span class="pk-stat-card__value">{{ $occupiedCar }}</span>
            <span class="pk-stat-card__label">Đang sử dụng</span>
        </div>
        <div class="pk-stat-card pk-stat--purple">
            <span class="pk-stat-card__value">{{ $totalMotoZones }}</span>
            <span class="pk-stat-card__label">Khu vực xe máy</span>
        </div>
    </div>

    {{-- Form tạo lốt --}}
    <div class="parking-create-card">
        <h3 class="parking-create-card__title">Thêm lốt đỗ mới</h3>
        <form action="{{ route('admin.parking-lots.store') }}" method="POST">
            @csrf

            <div class="parking-mode-switch">
                <label><input type="radio" name="creation_mode" value="single" checked onchange="toggleMode()"> Tạo đơn</label>
                <label><input type="radio" name="creation_mode" value="bulk" onchange="toggleMode()"> Tạo hàng loạt (Ô tô)</label>
            </div>

            <div class="parking-form-grid">
                <div class="parking-form-group">
                    <label>Loại</label>
                    <select name="lot_type" class="parking-form-control" id="lot-type-select" required onchange="toggleCapacityInput()">
                        <option value="car">Ô tô</option>
                        <option value="motorbike">Xe máy (Zone)</option>
                    </select>
                </div>

                {{-- Single --}}
                <div class="parking-form-group single-field">
                    <label>Mã lốt</label>
                    <input type="text" name="lot_number" class="parking-form-control" placeholder="VD: B1-01, Zone A">
                </div>
                <div class="parking-form-group single-field">
                    <label>Tầng/Khu vực</label>
                    <input type="text" name="zone" class="parking-form-control" placeholder="VD: Hầm B1">
                </div>
                <div class="parking-form-group single-field" id="capacity-group" style="display: none;">
                    <label>Sức chứa</label>
                    <input type="number" name="capacity" class="parking-form-control" placeholder="VD: 200" min="1">
                </div>

                {{-- Bulk --}}
                <div class="parking-form-group bulk-field" style="display: none;">
                    <label>Tầng/Khu vực</label>
                    <input type="text" name="bulk_zone" class="parking-form-control" placeholder="VD: Hầm B1">
                </div>
                <div class="parking-form-group bulk-field" style="display: none;">
                    <label>Tiền tố mã lốt</label>
                    <input type="text" name="lot_prefix" class="parking-form-control" placeholder="VD: B1">
                </div>
                <div class="parking-form-group bulk-field" style="display: none;">
                    <label>Từ số</label>
                    <input type="number" name="start_num" class="parking-form-control" placeholder="1" min="1">
                </div>
                <div class="parking-form-group bulk-field" style="display: none;">
                    <label>Đến số</label>
                    <input type="number" name="end_num" class="parking-form-control" placeholder="30" min="1">
                </div>

                <div class="parking-form-group" style="justify-content: flex-end;">
                    <label>&nbsp;</label>
                    <button type="submit" class="pk-btn-submit">Tạo mới</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabs --}}
    <div class="parking-tabs-nav">
        <button class="parking-tab-btn active" onclick="switchTab('car-tab', this)">Lốt Ô tô</button>
        <button class="parking-tab-btn" onclick="switchTab('moto-tab', this)">Khu vực Xe máy</button>
    </div>

    {{-- TAB 1: Ô tô (grouped by zone) --}}
    <div id="car-tab" class="parking-tab-content active">
        @php
            $carByZone = $carLots->groupBy(fn($lot) => $lot->zone ?? 'Chưa phân tầng');
        @endphp

        @if($carLots->isEmpty())
            <div class="parking-table-card">
                <div class="pk-table-empty" style="padding:40px;text-align:center;color:#94a3b8;">Chưa có lốt đỗ ô tô nào.</div>
            </div>
        @else
            @foreach($carByZone as $zoneName => $zoneLots)
            <div class="parking-zone-group">
                <div class="parking-zone-header">
                    <span class="parking-zone-name">{{ $zoneName }}</span>
                    <span class="parking-zone-count">{{ $zoneLots->count() }} lốt — {{ $zoneLots->where('status', 'available')->count() }} trống</span>
                </div>
                <div class="parking-table-card">
                    <div class="parking-table-wrap">
                        <table class="parking-table">
                            <thead>
                                <tr>
                                    <th>Số lốt</th>
                                    <th>Trạng thái</th>
                                    <th>Biển số</th>
                                    <th>Căn hộ</th>
                                    <th>Chủ xe</th>
                                    <th class="text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zoneLots as $lot)
                                <tr>
                                    <td><span class="pk-lot-code">{{ $lot->lot_number }}</span></td>
                                    <td>
                                        @if($lot->isAvailable())
                                            <span class="pk-status pk-status--available">Trống</span>
                                        @else
                                            <span class="pk-status pk-status--occupied">Đang dùng</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($lot->vehicle)
                                            <span class="pk-plate">{{ $lot->vehicle->license_plate }}</span>
                                        @else
                                            <span class="pk-empty-cell">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $lot->apartment->apartment_number ?? '—' }}</td>
                                    <td>{{ $lot->apartment?->residents?->first()?->user?->name ?? '—' }}</td>
                                    <td class="text-right">
                                        <div class="pk-table-actions">
                                            @if($lot->isAvailable())
                                                <a href="{{ route('admin.vehicles.index', ['status' => 'pending', 'vehicle_type' => 'car']) }}" class="pk-table-btn pk-table-btn--primary">Gán xe</a>
                                            @elseif($lot->vehicle)
                                                <form action="{{ route('admin.vehicles.releaseLot', $lot->vehicle) }}" method="POST" style="display:inline;" onsubmit="return confirm('Thu hồi lốt {{ $lot->lot_number }}?')">
                                                    @csrf
                                                    <button type="submit" class="pk-table-btn pk-table-btn--warning">Thu hồi</button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.parking-lots.destroy', $lot) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa lốt {{ $lot->lot_number }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="pk-table-btn pk-table-btn--danger" {{ $lot->isOccupied() ? 'disabled' : '' }}>Xóa</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- TAB 2: Xe máy --}}
    <div id="moto-tab" class="parking-tab-content">
        <div class="parking-table-card">
            <div class="parking-table-wrap">
                <table class="parking-table">
                    <thead>
                        <tr>
                            <th>Khu vực</th>
                            <th>Loại</th>
                            <th>Mức lấp đầy</th>
                            <th class="text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($motorbikeLots as $lot)
                            @php
                                $usage = $lot->getCurrentUsage();
                                $capacity = $lot->capacity ?? 1;
                                $percent = min(100, round(($usage / $capacity) * 100));
                                $fillClass = $percent >= 95 ? 'danger' : ($percent > 75 ? 'warning' : '');
                            @endphp
                            <tr>
                                <td><span class="pk-lot-code">{{ $lot->lot_number }}</span></td>
                                <td>Xe máy / Xe điện</td>
                                <td style="min-width: 260px;">
                                    <div class="pk-progress-wrap">
                                        <div class="pk-progress-text">
                                            <span>{{ number_format($usage) }} / {{ number_format($capacity) }} xe</span>
                                            <span class="{{ $percent >= 95 ? 'pk-text-danger' : '' }}">{{ $percent }}%</span>
                                        </div>
                                        <div class="pk-progress-bar">
                                            <div class="pk-progress-fill {{ $fillClass }}" style="width: {{ $percent }}%;"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="pk-table-actions">
                                        <a href="{{ route('admin.vehicles.index', ['vehicle_type' => 'motorbike,electric_bike']) }}" class="pk-table-btn pk-table-btn--outline">Xem xe</a>
                                        <form action="{{ route('admin.parking-lots.destroy', $lot) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa khu vực {{ $lot->lot_number }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="pk-table-btn pk-table-btn--danger">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="pk-table-empty">Chưa có khu vực đỗ xe máy nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function toggleCapacityInput() {
    const type = document.getElementById('lot-type-select').value;
    document.getElementById('capacity-group').style.display = type === 'motorbike' ? 'flex' : 'none';
}

function toggleMode() {
    const mode = document.querySelector('input[name="creation_mode"]:checked').value;
    const singleFields = document.querySelectorAll('.single-field');
    const bulkFields = document.querySelectorAll('.bulk-field');
    const typeSelect = document.getElementById('lot-type-select');

    if (mode === 'bulk') {
        singleFields.forEach(f => f.style.display = 'none');
        bulkFields.forEach(f => f.style.display = 'flex');
        typeSelect.value = 'car';
        typeSelect.style.pointerEvents = 'none';
        typeSelect.style.opacity = '0.6';
    } else {
        singleFields.forEach(f => f.style.display = 'flex');
        bulkFields.forEach(f => f.style.display = 'none');
        typeSelect.style.pointerEvents = '';
        typeSelect.style.opacity = '';
        toggleCapacityInput();
    }
}

function switchTab(tabId, btn) {
    document.querySelectorAll('.parking-tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.parking-tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    btn.classList.add('active');
}

document.addEventListener('DOMContentLoaded', toggleMode);
</script>
@endpush
@endsection
