@extends('layouts.admin.master')

@section('page_title', 'Quản lý Lốt đỗ xe')
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

    {{-- Stats Grid --}}
    @php
        $totalCar = $carLots->count();
        $availableCar = $carLots->where('status', 'available')->count();
        $occupiedCar = $carLots->where('status', 'occupied')->count();
        $totalMotoZones = $motorbikeLots->count();
    @endphp
    <div class="parking-stats-grid">
        <div class="pk-stat-card" style="border-left: 4px solid #0b57d0;">
            <span class="pk-stat-card__label">Tổng lốt ô tô</span>
            <span class="pk-stat-card__value" style="color: #0b57d0;">{{ $totalCar }}</span>
        </div>
        <div class="pk-stat-card" style="border-left: 4px solid #16a34a;">
            <span class="pk-stat-card__label">Lốt trống</span>
            <span class="pk-stat-card__value" style="color: #16a34a;">{{ $availableCar }}</span>
        </div>
        <div class="pk-stat-card" style="border-left: 4px solid #f59e0b;">
            <span class="pk-stat-card__label">Đang sử dụng</span>
            <span class="pk-stat-card__value" style="color: #f59e0b;">{{ $occupiedCar }}</span>
        </div>
        <div class="pk-stat-card" style="border-left: 4px solid #8b5cf6;">
            <span class="pk-stat-card__label">Khu vực xe máy</span>
            <span class="pk-stat-card__value" style="color: #8b5cf6;">{{ $totalMotoZones }}</span>
        </div>
    </div>

    {{-- Thêm Lốt / Khu vực mới --}}
    <div class="parking-create-card">
        <form action="{{ route('admin.parking-lots.store') }}" method="POST">
            @csrf

            <div class="parking-mode-switch">
                <label><input type="radio" name="creation_mode" value="single" checked onchange="toggleMode()"> Tạo đơn (1 lốt/zone)</label>
                <label><input type="radio" name="creation_mode" value="bulk" onchange="toggleMode()"> Tạo hàng loạt (Chỉ Ô tô)</label>
            </div>

            <div class="parking-form-grid">
                <div class="parking-form-group">
                    <label>Loại</label>
                    <select name="lot_type" class="parking-form-control" id="lot-type-select" required onchange="toggleCapacityInput()">
                        <option value="car">Ô tô</option>
                        <option value="motorbike">Xe máy (Zone)</option>
                    </select>
                </div>

                {{-- Single Mode Fields --}}
                <div class="parking-form-group single-field">
                    <label>Tên / Số lốt (VD: B1-01, Zone A)</label>
                    <input type="text" name="lot_number" class="parking-form-control" placeholder="Nhập mã lốt / khu vực">
                </div>
                <div class="parking-form-group single-field" id="capacity-group" style="display: none;">
                    <label>Sức chứa (Capacity)</label>
                    <input type="number" name="capacity" class="parking-form-control" placeholder="VD: 200" min="1">
                </div>

                {{-- Bulk Mode Fields --}}
                <div class="parking-form-group bulk-field" style="display: none;">
                    <label>Tiền tố (Prefix)</label>
                    <input type="text" name="lot_prefix" class="parking-form-control" placeholder="VD: B1">
                </div>
                <div class="parking-form-group bulk-field" style="display: none;">
                    <label>Từ số</label>
                    <input type="number" name="start_num" class="parking-form-control" placeholder="VD: 1" min="1">
                </div>
                <div class="parking-form-group bulk-field" style="display: none;">
                    <label>Đến số</label>
                    <input type="number" name="end_num" class="parking-form-control" placeholder="VD: 20" min="1">
                </div>

                <div style="display: flex; align-items: flex-end;">
                    <button type="submit" class="pk-table-btn pk-table-btn--primary" style="height: 40px; padding: 0 24px; font-size: 14px;">
                        Tạo
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Tabs Navigation --}}
    <div class="parking-tabs-nav">
        <button class="parking-tab-btn active" onclick="switchTab('car-tab', this)">🚗 Lốt Ô tô</button>
        <button class="parking-tab-btn" onclick="switchTab('moto-tab', this)">🛵 Khu vực Xe máy / Xe điện</button>
    </div>

    {{-- ======================================================= --}}
    {{-- TAB 1: CAR SLOTS (TABLE)                                --}}
    {{-- ======================================================= --}}
    <div id="car-tab" class="parking-tab-content active">
        <div class="parking-table-card">
            <div class="parking-table-wrap">
                <table class="parking-table">
                    <thead>
                        <tr>
                            <th>Số Lốt</th>
                            <th>Trạng thái</th>
                            <th>Biển số xe</th>
                            <th>Căn hộ</th>
                            <th>Chủ xe</th>
                            <th class="text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($carLots as $lot)
                            <tr>
                                <td><span class="pk-lot-code">{{ $lot->lot_number }}</span></td>
                                <td>
                                    @if($lot->isAvailable())
                                        <span class="pk-status pk-status--available">Còn trống</span>
                                    @else
                                        <span class="pk-status pk-status--occupied">Đang sử dụng</span>
                                    @endif
                                </td>
                                <td>
                                    @if($lot->vehicle)
                                        <strong style="color: #0b57d0;">{{ $lot->vehicle->license_plate }}</strong>
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $lot->apartment->apartment_number ?? '—' }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $lot->apartment?->residents?->first()?->user?->name ?? '—' }}</strong>
                                </td>
                                <td class="text-right">
                                    <div class="pk-table-actions">
                                        @if($lot->isAvailable())
                                            <a href="{{ route('admin.vehicles.index', ['status' => 'pending', 'vehicle_type' => 'car']) }}" class="pk-table-btn pk-table-btn--primary">Gán xe</a>
                                        @elseif($lot->vehicle)
                                            <form action="{{ route('admin.vehicles.releaseLot', $lot->vehicle) }}" method="POST" style="display:inline;" onsubmit="return confirm('Xác nhận thu hồi lốt {{ $lot->lot_number }} của xe {{ $lot->vehicle->license_plate }}?')">
                                                @csrf
                                                <button type="submit" class="pk-table-btn pk-table-btn--warning">Thu hồi</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.parking-lots.destroy', $lot) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có muốn xóa lốt đỗ này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="pk-table-btn pk-table-btn--danger" {{ $lot->isOccupied() ? 'disabled' : '' }}>Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    Chưa có lốt đỗ ô tô nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ======================================================= --}}
    {{-- TAB 2: MOTORBIKE ZONES (TABLE)                          --}}
    {{-- ======================================================= --}}
    <div id="moto-tab" class="parking-tab-content">
        <div class="parking-table-card">
            <div class="parking-table-wrap">
                <table class="parking-table">
                    <thead>
                        <tr>
                            <th>Khu vực (Zone)</th>
                            <th>Loại xe</th>
                            <th>Mức độ lấp đầy (Usage / Capacity)</th>
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
                                <td>🛵 Xe máy / Xe điện</td>
                                <td style="min-width: 300px;">
                                    <div class="pk-progress-wrap">
                                        <div class="pk-progress-text">
                                            <span>Đang gửi: <strong>{{ number_format($usage) }}</strong> xe</span>
                                            <span>Sức chứa: <strong>{{ number_format($capacity) }}</strong> xe</span>
                                        </div>
                                        <div class="pk-progress-bar">
                                            <div class="pk-progress-fill {{ $fillClass }}" style="width: {{ $percent }}%;"></div>
                                        </div>
                                        <div class="pk-progress-percent {{ $percent >= 95 ? 'danger' : '' }}">
                                            {{ $percent }}% Full {{ $percent >= 95 ? '⚠ Sắp quá tải' : '' }}
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="pk-table-actions">
                                        <a href="{{ route('admin.vehicles.index', ['vehicle_type' => 'motorbike']) }}" class="pk-table-btn pk-table-btn--outline" title="Quản lý danh sách xe máy">Quản lý xe</a>

                                        <form action="{{ route('admin.parking-lots.destroy', $lot) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa khu vực này?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="pk-table-btn pk-table-btn--danger">Xóa Zone</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    Chưa có khu vực đỗ xe máy nào.
                                </td>
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
        const capGroup = document.getElementById('capacity-group');

        if (type === 'motorbike') {
            capGroup.style.display = 'flex';
        } else {
            capGroup.style.display = 'none';
        }
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
            typeSelect.disabled = true;

            document.querySelector('input[name="lot_number"]').removeAttribute('required');
            document.querySelector('input[name="lot_prefix"]').setAttribute('required', 'required');
            document.querySelector('input[name="start_num"]').setAttribute('required', 'required');
            document.querySelector('input[name="end_num"]').setAttribute('required', 'required');
        } else {
            singleFields.forEach(f => f.style.display = 'flex');
            bulkFields.forEach(f => f.style.display = 'none');
            typeSelect.disabled = false;

            toggleCapacityInput();

            document.querySelector('input[name="lot_number"]').setAttribute('required', 'required');
            document.querySelector('input[name="lot_prefix"]').removeAttribute('required');
            document.querySelector('input[name="start_num"]').removeAttribute('required');
            document.querySelector('input[name="end_num"]').removeAttribute('required');
        }
    }

    function switchTab(tabId, btn) {
        document.querySelectorAll('.parking-tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.parking-tab-btn').forEach(el => el.classList.remove('active'));

        document.getElementById(tabId).classList.add('active');
        btn.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleMode();
    });
</script>
@endpush
@endsection
