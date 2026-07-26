@extends('layouts.admin.master')

@section('page_title', 'Sửa Căn hộ')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/apartments/index.css'])
@endpush

@section('content')
<div class="apartments-page">

    {{-- Breadcrumb Navigation --}}
    <nav class="breadcrumb-nav">
        <a href="{{ portal_route('dashboard') }}">Trang chủ</a>
        <span class="divider">/</span>
        <a href="{{ portal_route('apartments.index') }}">Căn hộ</a>
        <span class="divider">/</span>
        <span class="current">Cập nhật</span>
    </nav>

    {{-- Header --}}
    <div class="apartments-page__header">
        <div>
            <h1>Sửa Căn hộ</h1>
            <p class="apartments-page__subtitle">Đang chỉnh sửa căn hộ: <strong style="color: #0b57d0;">{{ $apartment->apartment_number }}</strong></p>
        </div>
        <div class="apartments-page__actions">
            <a href="{{ portal_route('apartments.show', $apartment) }}" class="apts-button apts-button--view">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                Xem chi tiết
            </a>
            <a href="{{ portal_route('apartments.index') }}" class="apts-button apts-button--edit">
                ← Quay lại
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <article class="dashboard-card form-card-custom shadow-sm border-light">
        <div class="card-badge-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2-2H7a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            APARTMENT UPDATE
        </div>

        <form action="{{ portal_route('apartments.update', $apartment) }}" method="POST">
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

            <div class="form-grid-2">
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

                {{-- Loại căn hộ --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Loại căn hộ</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line></svg>
                        </span>
                        <select name="apartment_type_id" class="form-input-custom @error('apartment_type_id') input-error @enderror">
                            <option value="">-- Chọn Loại căn hộ --</option>
                            @foreach($apartmentTypes as $type)
                                <option value="{{ $type->id }}" {{ old('apartment_type_id', $apartment->apartment_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }} ({{ $type->bedroom_count }} PN / {{ $type->bathroom_count }} WC - {{ number_format($type->base_service_fee, 0, ',', '.') }}đ/m²)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('apartment_type_id')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Diện tích --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Diện tích (m²) <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 12v-4m0 0H2m2 0h2m8-4v-4m0 12v-4m0 0h-2m2 0h2" /></svg>
                        </span>
                        <input
                            type="number"
                            name="area"
                            step="0.01"
                            value="{{ old('area', $apartment->area) }}"
                            placeholder="VD: 75.5..."
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
                        @php
                            $hasResidents = $apartment->residents()->exists();
                        @endphp

                        @if($hasResidents)
                            <input type="hidden" name="status" value="occupied">
                            <select class="form-input-custom" disabled>
                                <option value="occupied" selected>Đang ở (Có cư dân)</option>
                            </select>
                            <small style="color: #64748b; margin-top: 4px; display: block;">
                                Căn hộ đang có người ở. Trạng thái tự động khóa.
                            </small>
                        @else
                            <select name="status" class="form-input-custom @error('status') input-error @enderror">
                                <option value="vacant" {{ old('status', $apartment->status) == 'vacant' ? 'selected' : '' }}>Trống</option>
                                <option value="maintenance" {{ old('status', $apartment->status) == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                            </select>
                        @endif
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
            <div class="apartments-page__actions" style="justify-content: flex-start; margin-top: 24px;">
                <button type="submit" class="apts-button apts-button--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    Xác nhận cập nhật
                </button>
                <a href="{{ portal_route('apartments.index') }}" class="apts-button apts-button--edit">
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
