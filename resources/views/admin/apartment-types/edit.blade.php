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

            <div class="form-grid-2" style="margin-top: 24px;">
                {{-- Số phòng khách --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Số phòng khách
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        </span>
                        <input type="number" name="living_room_count" value="{{ old('living_room_count', $apartmentType->living_room_count ?? 1) }}" class="form-input-custom @error('living_room_count') input-error @enderror" min="0" max="10">
                    </div>
                    @error('living_room_count')<p class="form-error-custom">{{ $message }}</p>@enderror
                </div>

                {{-- Hướng ban công --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Hướng ban công
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 14.76V3.5a9 9 0 00-8.08 9h8.08zm-7.6 1.5A9 9 0 0014 20.5v-4.24H6.4zm8.6-1.5h6.58A9.004 9.004 0 0015 3.5v11.26zm0 1.5v4.24a9 9 0 006.58-4.24H15z" /></svg>
                        </span>
                        <input type="text" name="balcony_direction" value="{{ old('balcony_direction', $apartmentType->balcony_direction) }}" placeholder="VD: Đông Nam..." class="form-input-custom @error('balcony_direction') input-error @enderror">
                    </div>
                    @error('balcony_direction')<p class="form-error-custom">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Phần 3: Nội thất kèm theo --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">03</span>
                <h4>Thông tin Nội thất</h4>
            </div>

            <div class="form-group-custom" style="margin-bottom: 24px;">
                <label class="form-label-custom">
                    Tình trạng nội thất
                </label>
                <div class="input-wrapper-custom">
                    <span class="input-icon-custom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                    </span>
                    <input type="text" name="furniture_status" value="{{ old('furniture_status', $apartmentType->furniture_status) }}" placeholder="VD: Full nội thất cơ bản..." class="form-input-custom @error('furniture_status') input-error @enderror">
                </div>
                @error('furniture_status')<p class="form-error-custom">{{ $message }}</p>@enderror
            </div>

            <div class="form-group-custom" style="margin-bottom: 24px;">
                <label class="form-label-custom">Danh sách nội thất chi tiết (Thêm từng món)</label>
                <div id="furniture_list_container">
                    @php
                        $furnitureList = old('furniture_list', $apartmentType->furniture_list ?? []);
                    @endphp
                    @if(count($furnitureList) > 0)
                        @foreach($furnitureList as $item)
                            <div class="furniture-item" style="display: flex; gap: 8px; margin-bottom: 8px;">
                                <input type="text" name="furniture_list[]" value="{{ $item }}" class="form-input-custom" placeholder="VD: Hệ thống máy lạnh Daikin (03 cái)">
                                <button type="button" class="apts-button apts-button--danger" onclick="this.parentElement.remove()" style="padding: 8px 12px; height: 42px;">Xóa</button>
                            </div>
                        @endforeach
                    @else
                        <div class="furniture-item" style="display: flex; gap: 8px; margin-bottom: 8px;">
                            <input type="text" name="furniture_list[]" class="form-input-custom" placeholder="VD: Hệ thống máy lạnh Daikin (03 cái)">
                            <button type="button" class="apts-button apts-button--danger" onclick="this.parentElement.remove()" style="padding: 8px 12px; height: 42px;">Xóa</button>
                        </div>
                    @endif
                </div>
                <div style="text-align: center; margin-top: 16px;">
                    <button type="button" onclick="addFurnitureItem()" style="background: none; border: none; font-weight: 600; color: #0b57d0; font-size: 14px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Thêm nội thất
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
});

function addFurnitureItem() {
    const container = document.getElementById('furniture_list_container');
    const div = document.createElement('div');
    div.className = 'furniture-item';
    div.style.cssText = 'display: flex; gap: 8px; margin-bottom: 8px;';
    div.innerHTML = `
        <input type="text" name="furniture_list[]" class="form-input-custom" placeholder="VD: Hệ thống máy lạnh Daikin (03 cái)">
        <button type="button" class="apts-button apts-button--danger" onclick="this.parentElement.remove()" style="padding: 8px 12px; height: 42px;">Xóa</button>
    `;
    container.appendChild(div);
}
</script>
@endpush

