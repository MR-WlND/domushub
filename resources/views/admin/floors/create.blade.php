@extends('layouts.admin.master')

@section('page_title', 'Tạo Tầng')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/floors/index.css'])
@endpush

@section('content')
<div class="floors-page">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav">
        <a href="{{ portal_route('dashboard') }}">Trang chủ</a>
        <span class="divider">/</span>
        <a href="{{ portal_route('blocks.index') }}">Tầng</a>
        <span class="divider">/</span>
        <span class="current">Thêm mới</span>
    </nav>

    {{-- Header --}}
    <div class="floors-page__header">
        <div>
            <h1>Tạo Tầng mới</h1>
            <p class="floors-page__subtitle">Thêm tầng mới vào một tòa nhà</p>
        </div>
        <div class="floors-page__actions">
            <a href="{{ portal_route('blocks.index') }}" class="floors-button floors-button--light">
                ← Quay lại
            </a>
        </div>
    </div>

    {{-- Form --}}
    <article class="dashboard-card form-card-custom shadow-sm border-light">
        <div class="card-badge-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
            FLOOR CREATION
        </div>

        <form action="{{ portal_route('floors.store') }}" method="POST">
            @csrf

            {{-- Phần 1: Vị trí & Phân loại --}}
            <div class="form-section-header">
                <span class="section-number">01</span>
                <h4>Vị trí & Phân loại</h4>
            </div>

            <div class="form-grid-2">
                {{-- Chọn Tòa nhà --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Block / Tòa nhà <span class="required">*</span></label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </span>
                        <select name="block_id" id="block_select" class="form-input-custom @error('block_id') input-error @enderror" required>
                            <option value="">-- Chọn tòa nhà --</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" data-apartments="{{ $block->apartments_per_floor }}" {{ old('block_id', $selectedBlockId ?? '') == $block->id ? 'selected' : '' }}>
                                    {{ $block->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('block_id') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>

                {{-- Loại tầng --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Loại tầng <span class="required">*</span></label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
                        </span>
                        <select name="floor_type" id="floor_type_select" class="form-input-custom @error('floor_type') input-error @enderror" required>
                            <option value="above_ground" {{ old('floor_type', 'above_ground') == 'above_ground' ? 'selected' : '' }}>Tầng nổi</option>
                            <option value="basement"     {{ old('floor_type') == 'basement' ? 'selected' : '' }}>Tầng hầm</option>
                        </select>
                    </div>
                    @error('floor_type') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Phần 2: Thông tin cấu trúc tầng --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">02</span>
                <h4>Thông tin cấu trúc tầng</h4>
            </div>

            <div class="form-grid-2">
                {{-- Tên tầng --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Tên tầng <span class="required">*</span></label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="VD: Tầng 5, Tầng Hầm 1, Tầng Trệt..." class="form-input-custom @error('name') input-error @enderror" required>
                    </div>
                    @error('name') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>

                {{-- Số lượng căn hộ --}}
                <div class="form-group-custom" id="apartments_count_group">
                    <label class="form-label-custom">Số lượng căn hộ <small style="color:#94a3b8">(tuỳ chọn)</small></label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </span>
                        <input type="number" name="number_of_apartments" value="{{ old('number_of_apartments') }}" placeholder="VD: 10, 5..." min="0" max="100" class="form-input-custom @error('number_of_apartments') input-error @enderror">
                    </div>
                    @error('number_of_apartments') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Phần 3: Ghi chú bổ sung --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">03</span>
                <h4>Ghi chú bổ sung</h4>
            </div>

            {{-- Ghi chú --}}
            <div class="form-group-custom" style="margin-top: 16px; margin-bottom: 24px;">
                <label class="form-label-custom">Ghi chú</label>
                <textarea name="description" placeholder="Nhập ghi chú chi tiết về tầng này..." class="form-textarea-custom" rows="3">{{ old('description') }}</textarea>
            </div>

            <input type="hidden" name="status" value="active">

            {{-- Actions --}}
            <div class="floors-page__actions" style="justify-content: flex-start; margin-top: 24px;">
                <button type="submit" class="floors-button floors-button--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Xác nhận thêm tầng
                </button>
                <a href="{{ portal_route('blocks.index') }}" class="floors-button floors-button--light">
                    Hủy bỏ
                </a>
            </div>

        </form>
    </article>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const floorTypeSelect = document.getElementById('floor_type_select');
        const apartmentsCountGroup = document.getElementById('apartments_count_group');
        const apartmentsCountInput = apartmentsCountGroup.querySelector('input');
        const blockSelect = document.getElementById('block_select');

        function toggleApartmentsCount() {
            if (floorTypeSelect.value === 'basement') {
                apartmentsCountGroup.style.display = 'none';
                if (apartmentsCountInput) {
                    apartmentsCountInput.value = '';
                }
            } else {
                apartmentsCountGroup.style.display = 'block';
                // Auto-fill if empty
                if (apartmentsCountInput && !apartmentsCountInput.value && blockSelect.selectedIndex > 0) {
                    const defaultApts = blockSelect.options[blockSelect.selectedIndex].getAttribute('data-apartments');
                    if (defaultApts) {
                        apartmentsCountInput.value = defaultApts;
                    }
                }
            }
        }

        function handleBlockChange() {
            if (floorTypeSelect.value !== 'basement' && blockSelect.selectedIndex > 0) {
                const defaultApts = blockSelect.options[blockSelect.selectedIndex].getAttribute('data-apartments');
                if (defaultApts) {
                    apartmentsCountInput.value = defaultApts;
                }
            }
        }

        toggleApartmentsCount();
        if(floorTypeSelect) {
            floorTypeSelect.addEventListener('change', toggleApartmentsCount);
        }
        if(blockSelect) {
            blockSelect.addEventListener('change', handleBlockChange);
        }
    });
</script>
@endpush