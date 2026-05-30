@extends('layouts.admin.master')

@section('page_title', 'Sửa Căn hộ')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')
<div class="dashboard-content">

    {{-- Breadcrumb Navigation --}}
    <nav class="breadcrumb-nav">
        <a href="{{ route('admin.dashboard') }}">Trang chủ</a>
        <span class="divider">/</span>
        <a href="{{ route('admin.apartments.index') }}">Căn hộ</a>
        <span class="divider">/</span>
        <span class="current">Cập nhật</span>
    </nav>

    {{-- Header --}}
    <div class="page-header" style="margin-top: 15px;">
        <div>
            <h1 class="page-title">Sửa Căn hộ</h1>
            <p class="page-subtitle">
                Đang chỉnh sửa căn hộ: <strong style="color: #0b57d0;">{{ $apartment->apartment_number }}</strong>
            </p>
        </div>
        <a href="{{ route('admin.apartments.index') }}" class="btn btn-secondary" style="display: flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; height: 38px; box-sizing: border-box; transition: all 0.2s;">
            ← Quay lại
        </a>
    </div>

    {{-- Form Card --}}
    <article class="dashboard-card form-card-custom shadow-sm border-light">
        <div class="card-badge-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2-2H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            APARTMENT UPDATE
        </div>

        <form action="{{ route('admin.apartments.update', $apartment) }}" method="POST">
            @csrf
            @method('PUT')

            @php
                $selectedFloorId = old('floor_id', $apartment->floor_id);
                $selectedBlockId = null;
                if ($selectedFloorId) {
                    $selectedFloor = $floors->firstWhere('id', $selectedFloorId);
                    if ($selectedFloor) {
                        $selectedBlockId = $selectedFloor->block_id;
                    }
                }
            @endphp

            {{-- Phần 1: Vị trí căn hộ --}}
            <div class="form-section-header">
                <span class="section-number">01</span>
                <h4>Vị trí căn hộ trong tòa nhà</h4>
            </div>

            <div class="form-grid-2">
                {{-- Chọn Tòa --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Tòa nhà <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </span>
                        <select id="block_select" class="form-input-custom" required>
                            <option value="">-- Chọn Tòa nhà --</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" {{ $selectedBlockId == $block->id ? 'selected' : '' }}>
                                    {{ $block->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Chọn Tầng --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Tầng <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
                        </span>
                        <select name="floor_id" id="floor_select" class="form-input-custom @error('floor_id') input-error @enderror" required disabled>
                            <option value="">-- Chọn Tầng --</option>
                            @foreach($floors as $floor)
                                <option value="{{ $floor->id }}" data-block-id="{{ $floor->block_id }}" {{ old('floor_id', $apartment->floor_id) == $floor->id ? 'selected' : '' }}>
                                    {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('floor_id')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Phần 2: Thông số kỹ thuật --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">02</span>
                <h4>Thông số kỹ thuật & Trạng thái</h4>
            </div>

            <div class="form-grid-3">
                {{-- Số căn hộ --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Số hiệu căn hộ <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">#</span>
                        <input
                            type="text"
                            name="apartment_number"
                            value="{{ old('apartment_number', $apartment->apartment_number) }}"
                            placeholder="VD: 101, A1..."
                            class="form-input-custom @error('apartment_number') input-error @enderror"
                            required
                        >
                    </div>
                    @error('apartment_number')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Diện tích --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Diện tích sử dụng (m²) <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5" /></svg>
                        </span>
                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            name="area"
                            value="{{ old('area', $apartment->area) }}"
                            placeholder="VD: 45.5"
                            class="form-input-custom @error('area') input-error @enderror"
                            required
                        >
                    </div>
                    @error('area')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Trạng thái --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Trạng thái căn hộ</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </span>
                        <select name="status" class="form-input-custom @error('status') input-error @enderror">
                            <option value="vacant" {{ old('status', $apartment->status) == 'vacant' ? 'selected' : '' }}>Trống</option>
                            <option value="occupied" {{ old('status', $apartment->status) == 'occupied' ? 'selected' : '' }}>Đang ở</option>
                            <option value="maintenance" {{ old('status', $apartment->status) == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                        </select>
                    </div>
                    @error('status')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Phần 3: Ghi chú & mô tả --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">03</span>
                <h4>Ghi chú & Mô tả chi tiết</h4>
            </div>

            {{-- Ghi chú --}}
            <div class="form-group-custom" style="margin-bottom: 24px;">
                <label class="form-label-custom">Mô tả chi tiết căn hộ</label>
                <textarea
                    name="description"
                    placeholder="Nhập ghi chú chi tiết hoặc mô tả đặc điểm căn hộ..."
                    class="form-textarea-custom @error('description') input-error @enderror"
                    rows="4"
                >{{ old('description', $apartment->description) }}</textarea>
                @error('description')
                    <p class="form-error-custom">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="form-actions-custom">
                <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; padding: 12px 24px; border-radius: 8px; font-size: 14px; cursor: pointer; border: none; background-color: #0b57d0; color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    Xác nhận cập nhật
                </button>
                <a href="{{ route('admin.apartments.index') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; justify-content: center; height: 44px; padding: 0 24px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px; background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; transition: all 0.2s;">
                    Hủy bỏ
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
        
        // Run updateFloors on load to handle pre-selected values
        if (blockSelect.value) {
            const currentFloorValue = "{{ old('floor_id', $apartment->floor_id) }}";
            updateFloors();
            if (currentFloorValue) {
                floorSelect.value = currentFloorValue;
            }
        }
    });
</script>
@endpush
