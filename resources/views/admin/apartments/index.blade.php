@extends('layouts.admin.master')
@section('title', 'Quản lý Căn hộ')
@section('content')

<div class="apartment-page">

    {{-- Header --}}
    <div class="apartment-page__header">

        <div>
            <h1 class="apartment-page__title">
                Danh sách Căn hộ
            </h1>

            <p class="apartment-page__subtitle">
                Quản lý toàn bộ căn hộ trong hệ thống
            </p>
        </div>

        <a href="{{ route('admin.apartments.create') }}"
            class="btn-add-apartment">
            + Thêm Căn hộ
        </a>

    </div>

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px; padding: 15px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 8px;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 8px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="apartment-stats">

        <div class="apartment-stat-card">
            <span class="apartment-stat-card__label">
                Tổng căn hộ
            </span>

            <h3>
                {{ $totalApartments ?? 0 }}
            </h3>
        </div>

        <div class="apartment-stat-card">
            <span class="apartment-stat-card__label">
                Đang ở
            </span>

            <h3 class="text-success">
                {{ $occupiedApartments ?? 0 }}
            </h3>
        </div>

        <div class="apartment-stat-card">
            <span class="apartment-stat-card__label">
                Trống
            </span>

            <h3 class="text-warning">
                {{ $vacantApartments ?? 0 }}
            </h3>
        </div>

        <div class="apartment-stat-card">
            <span class="apartment-stat-card__label">
                Bảo trì
            </span>

            <h3 class="text-danger">
                {{ $maintenanceApartments ?? 0 }}
            </h3>
        </div>

    </div>

    {{-- Filters --}}
    <div class="apartment-filter-card">

        <form method="GET">

            <div class="apartment-filter-grid">

                <div>
                    <label>Tòa nhà</label>

                    <select name="block_id">

                        <option value="">
                            Tất cả tòa
                        </option>

                        @foreach($blocks as $block)

                        <option value="{{ $block->id }}"
                            {{ request('block_id') == $block->id ? 'selected' : '' }}>

                            {{ $block->name }}

                        </option>

                        @endforeach

                    </select>
                </div>

                <div>
                    <label>Tầng</label>

                    <select name="floor_id">

                        <option value="">
                            Tất cả tầng
                        </option>

                        @foreach($floors as $floor)

                        <option value="{{ $floor->id }}"
                            data-block-id="{{ $floor->block_id }}"
                            {{ request('floor_id') == $floor->id ? 'selected' : '' }}>

                            {{ $floor->name }}

                        </option>

                        @endforeach

                    </select>
                </div>

                <div>
                    <label>Trạng thái</label>

                    <select name="status">

                        <option value="">
                            Tất cả
                        </option>

                        <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>
                            Đang ở
                        </option>

                        <option value="vacant" {{ request('status') == 'vacant' ? 'selected' : '' }}>
                            Trống
                        </option>

                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>
                            Bảo trì
                        </option>

                    </select>
                </div>

            </div>

        </form>

    </div>

    {{-- Apartments Grid --}}
    <div class="apartment-grid">

        @forelse($apartments as $apartment)

        <div class="apartment-card">

            {{-- top --}}
            <div class="apartment-card__top">

                <div>

                    <div class="apartment-number">
                        {{ $apartment->apartment_number }}
                    </div>

                    <div class="apartment-location">

                        {{ $apartment->floor->name ?? '' }}
                        -
                        {{ $apartment->floor->block->name ?? '' }}

                    </div>

                </div>

                <div class="apartment-status
                        @if($apartment->status == 'occupied')
                            apartment-status--occupied
                        @elseif($apartment->status == 'vacant')
                            apartment-status--vacant
                        @else
                            apartment-status--maintenance
                        @endif
                    ">

                    @if($apartment->status == 'occupied')
                    Đang ở
                    @elseif($apartment->status == 'vacant')
                    Trống
                    @else
                    Bảo trì
                    @endif

                </div>

            </div>

            {{-- description --}}
            <div class="apartment-description">

                {{ $apartment->description ?? 'Không có mô tả' }}

            </div>

            {{-- stats --}}
            <div class="apartment-card__stats">

                <div class="apartment-mini-stat">

                    <div class="apartment-mini-stat__value">
                        {{ $apartment->area ?? 0 }}
                    </div>

                    <div class="apartment-mini-stat__label">
                        m²
                    </div>

                </div>

                <div class="apartment-mini-stat">

                    <div class="apartment-mini-stat__value">
                        {{ $apartment->residents_count ?? 0 }}
                    </div>

                    <div class="apartment-mini-stat__label">
                        Cư dân
                    </div>

                </div>

            </div>

            {{-- actions --}}
            <div class="apartment-actions">

                <a href="{{ route('admin.apartments.show', $apartment->id) }}"
                    class="btn-apartment btn-apartment--view">

                    Chi tiết

                </a>

                <a href="{{ route('admin.apartments.edit', $apartment->id) }}"
                    class="btn-apartment btn-apartment--edit">

                    Sửa

                </a>

                <form action="{{ route('admin.apartments.destroy', $apartment->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa căn hộ này?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-apartment btn-apartment--delete">
                        Xóa
                    </button>
                </form>

            </div>

        </div>

        @empty

        <div class="empty-apartment">

            Chưa có căn hộ nào

        </div>

        @endforelse

    </div>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const blockFilter = document.querySelector('select[name="block_id"]');
        const floorFilter = document.querySelector('select[name="floor_id"]');
        const statusFilter = document.querySelector('select[name="status"]');
        
        // Cache floor options (excluding the placeholder)
        const floorOptions = Array.from(floorFilter.querySelectorAll('option[data-block-id]'));
        
        function updateFloors(resetSelected = false) {
            const selectedBlockId = blockFilter.value;
            const currentFloorValue = floorFilter.value;
            
            // Clear floor filter options (keep placeholder)
            floorFilter.innerHTML = '<option value="">Tất cả tầng</option>';
            
            if (selectedBlockId) {
                // Filter and append matching floors
                const filteredOptions = floorOptions.filter(opt => opt.getAttribute('data-block-id') === selectedBlockId);
                
                filteredOptions.forEach(opt => {
                    floorFilter.appendChild(opt);
                });
                
                floorFilter.disabled = false;
                
                // Retain selected floor if it belongs to the active block
                if (!resetSelected && currentFloorValue) {
                    const selectedOpt = filteredOptions.find(opt => opt.value === currentFloorValue);
                    if (selectedOpt) {
                        floorFilter.value = currentFloorValue;
                    }
                }
            } else {
                // Show all floor options if no block is selected
                floorOptions.forEach(opt => {
                    floorFilter.appendChild(opt);
                });
                floorFilter.disabled = false;
                
                if (!resetSelected && currentFloorValue) {
                    floorFilter.value = currentFloorValue;
                }
            }
        }
        
        // Auto-submit form when status or floor filter changes
        statusFilter.addEventListener('change', function () {
            this.form.submit();
        });
        
        floorFilter.addEventListener('change', function () {
            this.form.submit();
        });
        
        // When block changes: reset floor filter to empty, update floors, then submit
        blockFilter.addEventListener('change', function () {
            floorFilter.value = "";
            updateFloors(true);
            this.form.submit();
        });
        
        // Initialize floors filter on page load
        updateFloors(false);
    });
</script>
@endpush