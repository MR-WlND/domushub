@extends('layouts.admin.master')

@section('page_title', 'Sửa Loại căn hộ')
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
        <a href="{{ portal_route('apartment-types.index') }}">Loại căn hộ</a>
        <span class="divider">/</span>
        <span class="current">Cập nhật</span>
    </nav>

    {{-- Header --}}
    <div class="apartments-page__header">
        <div>
            <h1>Sửa Loại căn hộ: <span style="color: #0b57d0;">{{ $apartmentType->name }}</span></h1>
            <p class="apartments-page__subtitle">Cập nhật các thông số phòng ngủ, phòng tắm, đơn giá dịch vụ và mô tả.</p>
        </div>
        <div class="apartments-page__actions">
            <a href="{{ portal_route('apartment-types.index') }}" class="apts-button apts-button--edit">
                ← Quay lại
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <article class="dashboard-card form-card-custom shadow-sm border-light">
        <div class="card-badge-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
            TYPE UPDATE
        </div>

        <form action="{{ portal_route('apartment-types.update', $apartmentType->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Phần 1: Tên & Đơn giá --}}
            <div class="form-section-header">
                <span class="section-number">01</span>
                <h4>Thông tin cơ bản & Phí dịch vụ</h4>
            </div>

            <div class="form-grid-2">
                {{-- Tên loại --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Tên loại căn hộ <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="9" y1="3" x2="9" y2="21"></line>
                                <line x1="15" y1="3" x2="15" y2="21"></line>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="3" y1="15" x2="21" y2="15"></line>
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $apartmentType->name) }}"
                            placeholder="VD: Penthouse Royal, Duplex, Studio..."
                            class="form-input-custom @error('name') input-error @enderror"
                            required
                        >
                    </div>
                    @error('name')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phí dịch vụ cơ bản --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Phí quản lý / m² (VND) <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                                <circle cx="12" cy="12" r="2"></circle>
                                <path d="M6 12h.01M18 12h.01"></path>
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="base_service_fee"
                            id="base_service_fee"
                            value="{{ old('base_service_fee', (int) ($apartmentType->base_service_fee ?? 0)) }}"
                            placeholder="VD: 15.000"
                            class="form-input-custom @error('base_service_fee') input-error @enderror"
                            required
                        >
                    </div>
                    @error('base_service_fee')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Phần 2: Thông số phòng ngủ & phòng tắm --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">02</span>
                <h4>Thông số phòng ngủ & WC</h4>
            </div>

            <div class="form-grid-2">
                {{-- Số phòng ngủ --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Số phòng ngủ <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 4v16M2 11h20M2 17h20M22 8v12M6 8h4v3H6zm10 0h4v3h-4z"></path>
                            </svg>
                        </span>
                        <input
                            type="number"
                            name="bedroom_count"
                            value="{{ old('bedroom_count', $apartmentType->bedroom_count) }}"
                            class="form-input-custom @error('bedroom_count') input-error @enderror"
                            required
                            min="0"
                            max="10"
                        >
                    </div>
                    @error('bedroom_count')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Số phòng tắm --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Số phòng tắm / WC <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16v4H4V4zm0 4h16v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8z"></path>
                            </svg>
                        </span>
                        <input
                            type="number"
                            name="bathroom_count"
                            value="{{ old('bathroom_count', $apartmentType->bathroom_count) }}"
                            class="form-input-custom @error('bathroom_count') input-error @enderror"
                            required
                            min="0"
                            max="10"
                        >
                    </div>
                    @error('bathroom_count')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            </div>

            {{-- Phần 3: Nội thất kèm theo --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">03</span>
                <h4>Thông tin Nội thất</h4>
            </div>



            <div class="form-group-custom" style="margin-bottom: 24px;">
                <label class="form-label-custom">Tiêu chuẩn bàn giao (Nội thất chi tiết)</label>
                <p style="font-size: 13px; color: #64748b; margin-top: 0; margin-bottom: 12px;">Chọn các tiện ích tiêu chuẩn đi kèm với loại căn hộ này.</p>
                
                @php
                    $defaultFurnitures = [
                        'Sàn gỗ cao cấp',
                        'Điều hòa âm trần',
                        'Thiết bị vệ sinh cao cấp',
                        'Tủ bếp trên dưới',
                        'Hệ thống Smarthome',
                        'Khóa cửa từ',
                        'Giường, tủ áo',
                        'Bếp từ & Hút mùi',
                        'Sofa & Bàn trà'
                    ];
                @endphp
                <div class="checkbox-grid" id="furniture-checkbox-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;">
                    @php
                        $savedFurnitures = old('furniture_list', $apartmentType->furniture_list ?? []);
                        $allFurnitures = array_unique(array_merge($defaultFurnitures, $savedFurnitures));
                    @endphp
                    @foreach($allFurnitures as $item)
                        <div class="checkbox-item-wrapper" style="display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0; flex: 1;">
                                <input type="checkbox" name="furniture_list[]" value="{{ $item }}" 
                                    {{ in_array($item, $savedFurnitures) ? 'checked' : '' }}
                                    style="width: 16px; height: 16px; accent-color: #0b57d0; cursor: pointer; border-radius: 4px; border: 1px solid #cbd5e1;">
                                <span style="font-size: 14px; color: #475569; user-select: none;">{{ $item }}</span>
                            </label>
                            @if(!in_array($item, $defaultFurnitures))
                                <button type="button" onclick="this.parentElement.remove()" style="background: transparent; border: none; color: #94a3b8; cursor: pointer; padding: 4px; margin-left: 4px; display: flex; align-items: center; justify-content: center; border-radius: 4px; transition: all 0.2s;" onmouseover="this.style.color='#ef4444'; this.style.background='#fee2e2'" onmouseout="this.style.color='#94a3b8'; this.style.background='transparent'" title="Xóa">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                {{-- Add custom furniture --}}
                <div style="margin-top: 16px; display: flex; gap: 8px; align-items: center;">
                    <input type="text" id="new_furniture_input" placeholder="Nhập tên tiện ích..." style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none; width: 250px;">
                    <button type="button" id="btn_add_furniture" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        Thêm
                    </button>
                </div>
            </div>

            {{-- Phần 4: Ghi chú & mô tả --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">04</span>
                <h4>Mô tả đặc điểm loại căn hộ</h4>
            </div>

            {{-- Ghi chú --}}
            <div class="form-group-custom" style="margin-bottom: 24px;">
                <label class="form-label-custom">Mô tả chi tiết</label>
                <textarea
                    name="description"
                    placeholder="Nhập mô tả cụ thể về loại căn hộ, đặc điểm hoặc đặc quyền kèm theo..."
                    class="form-textarea-custom @error('description') input-error @enderror"
                    rows="4"
                >{{ old('description', $apartmentType->description) }}</textarea>
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
                <a href="{{ portal_route('apartment-types.index') }}" class="apts-button apts-button--edit">
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
    const feeInput = document.getElementById('base_service_fee');
    if (feeInput) {
        function formatNumber(value) {
            value = value.toString();
            if (value.endsWith('.00') || value.endsWith(',00')) {
                value = value.substring(0, value.length - 3);
            }
            value = value.replace(/\D/g, '');
            return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        if (feeInput.value) {
            feeInput.value = formatNumber(feeInput.value);
        }

        feeInput.addEventListener('input', function (e) {
            const cursorPosition = e.target.selectionStart;
            const originalLength = e.target.value.length;
            
            const formatted = formatNumber(e.target.value);
            e.target.value = formatted;
            
            const newLength = formatted.length;
            const pos = cursorPosition + (newLength - originalLength);
            e.target.setSelectionRange(pos, pos);
        });

        if (form) {
            form.addEventListener('submit', function () {
                feeInput.value = feeInput.value.replace(/\./g, '');
            });
        }
    }

    // Add custom furniture logic
    const btnAddFurniture = document.getElementById('btn_add_furniture');
    const inputNewFurniture = document.getElementById('new_furniture_input');
    const checkboxGrid = document.getElementById('furniture-checkbox-grid');

    if (btnAddFurniture && inputNewFurniture && checkboxGrid) {
        function addFurniture() {
            const val = inputNewFurniture.value.trim();
            if (!val) return;

            // Check if already exists
            const existingInputs = checkboxGrid.querySelectorAll('input[type="checkbox"]');
            for (let input of existingInputs) {
                if (input.value.toLowerCase() === val.toLowerCase()) {
                    input.checked = true;
                    inputNewFurniture.value = '';
                    return;
                }
            }

            // Create new checkbox item
            const wrapper = document.createElement('div');
            wrapper.className = 'checkbox-item-wrapper';
            wrapper.style.cssText = 'display: flex; align-items: center; justify-content: space-between; background: #f8fafc; padding: 6px 12px; border-radius: 6px; border: 1px solid #e2e8f0;';
            
            wrapper.innerHTML = `
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin: 0; flex: 1;">
                    <input type="checkbox" name="furniture_list[]" value="${val}" checked
                        style="width: 16px; height: 16px; accent-color: #0b57d0; cursor: pointer; border-radius: 4px; border: 1px solid #cbd5e1;">
                    <span style="font-size: 14px; color: #475569; user-select: none;">${val}</span>
                </label>
                <button type="button" onclick="this.parentElement.remove()" style="background: transparent; border: none; color: #94a3b8; cursor: pointer; padding: 4px; margin-left: 4px; display: flex; align-items: center; justify-content: center; border-radius: 4px; transition: all 0.2s;" onmouseover="this.style.color='#ef4444'; this.style.background='#fee2e2'" onmouseout="this.style.color='#94a3b8'; this.style.background='transparent'" title="Xóa">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            `;
            
            checkboxGrid.appendChild(wrapper);
            inputNewFurniture.value = '';
        }

        btnAddFurniture.addEventListener('click', addFurniture);
        inputNewFurniture.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addFurniture();
            }
        });
    }
});
</script>
@endpush

