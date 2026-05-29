@extends('layouts.admin.master')

@section('page_title', 'Tạo Căn hộ')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')

<div class="dashboard-content">

    {{-- Header --}}
    <div class="page-header">

        <div>
            <h1 class="page-title">Tạo Căn hộ mới</h1>

            <p class="page-subtitle">
                Thêm căn hộ mới vào hệ thống quản lý chung cư
            </p>
        </div>

        <a href="{{ route('admin.apartments.index') }}" class="btn btn-light">
            ← Quay lại
        </a>

    </div>

    {{-- Form --}}
    <article class="dashboard-card form-card">

        <form action="{{ route('admin.apartments.store') }}" method="POST">
            @csrf

            @php
                $selectedFloorId = old('floor_id');
                $selectedBlockId = null;
                if ($selectedFloorId) {
                    $selectedFloor = $floors->firstWhere('id', $selectedFloorId);
                    if ($selectedFloor) {
                        $selectedBlockId = $selectedFloor->block_id;
                    }
                }
            @endphp

            {{-- Chọn Tòa --}}
            <div class="form-group">

                <label class="form-label">
                    Tòa nhà
                    <span class="required">*</span>
                </label>

                <select
                    id="block_select"
                    class="form-input"
                    required
                >
                    <option value="">-- Chọn Tòa nhà --</option>
                    @foreach($blocks as $block)
                        <option value="{{ $block->id }}"
                            {{ $selectedBlockId == $block->id ? 'selected' : '' }}>
                            {{ $block->name }}
                        </option>
                    @endforeach
                </select>

            </div>

            {{-- Chọn Tầng --}}
            <div class="form-group">

                <label class="form-label">
                    Tầng
                    <span class="required">*</span>
                </label>

                <select
                    name="floor_id"
                    id="floor_select"
                    class="form-input @error('floor_id') input-error @enderror"
                    required
                    disabled
                >
                    <option value="">-- Chọn Tầng --</option>
                    @foreach($floors as $floor)
                        <option value="{{ $floor->id }}"
                            data-block-id="{{ $floor->block_id }}"
                            {{ old('floor_id') == $floor->id ? 'selected' : '' }}>
                            {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                        </option>
                    @endforeach
                </select>

                @error('floor_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Số căn hộ --}}
            <div class="form-group">

                <label class="form-label">
                    Số căn hộ
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="apartment_number"
                    value="{{ old('apartment_number') }}"
                    placeholder="VD: 101, A1..."
                    class="form-input @error('apartment_number') input-error @enderror"
                    required
                >

                @error('apartment_number')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Diện tích --}}
            <div class="form-group">

                <label class="form-label">
                    Diện tích (m²)
                    <span class="required">*</span>
                </label>

                <input
                    type="number"
                    step="0.01"
                    min="0.01"
                    name="area"
                    value="{{ old('area') }}"
                    placeholder="VD: 45.5"
                    class="form-input @error('area') input-error @enderror"
                    required
                >

                @error('area')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Trạng thái --}}
            <div class="form-group">

                <label class="form-label">
                    Trạng thái
                </label>

                <select
                    name="status"
                    class="form-input @error('status') input-error @enderror"
                >
                    <option value="vacant" {{ old('status') == 'vacant' ? 'selected' : '' }}>
                        Trống
                    </option>
                    <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>
                        Đang ở
                    </option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>
                        Bảo trì
                    </option>
                </select>

                @error('status')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Mô tả --}}
            <div class="form-group">

                <label class="form-label">
                    Mô tả
                </label>

                <textarea
                    name="description"
                    placeholder="Nhập mô tả căn hộ..."
                    class="form-input form-textarea @error('description') input-error @enderror"
                    rows="4"
                >{{ old('description') }}</textarea>

                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Actions --}}
            <div class="form-actions">

                <button type="submit" class="btn btn-primary">
                    + Tạo Căn hộ
                </button>

                <a href="{{ route('admin.apartments.index') }}" class="btn btn-secondary">
                    Hủy
                </a>

            </div>

        </form>

    </article>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const blockSelect = document.getElementById('block_select');
        const floorSelect = document.getElementById('floor_select');
        
        // Cache all floor options except the placeholder
        const floorOptions = Array.from(floorSelect.querySelectorAll('option[data-block-id]'));
        
        function updateFloors() {
            const selectedBlockId = blockSelect.value;
            
            // Clear current floor options (keep placeholder)
            floorSelect.innerHTML = '<option value="">-- Chọn Tầng --</option>';
            
            if (selectedBlockId) {
                // Filter and append matching floors
                const filteredOptions = floorOptions.filter(opt => opt.getAttribute('data-block-id') === selectedBlockId);
                
                filteredOptions.forEach(opt => {
                    floorSelect.appendChild(opt);
                });
                
                floorSelect.disabled = false;
            } else {
                floorSelect.disabled = true;
            }
        }
        
        // Run updateFloors on change
        blockSelect.addEventListener('change', function () {
            // Reset selected floor to placeholder on block change
            floorSelect.value = "";
            updateFloors();
        });
        
        // Run updateFloors on load to handle pre-selected values (e.g. from validation old values)
        if (blockSelect.value) {
            const currentFloorValue = "{{ old('floor_id') }}";
            updateFloors();
            if (currentFloorValue) {
                floorSelect.value = currentFloorValue;
            }
        }
    });
</script>
@endpush
