@extends('layouts.admin.master')

@section('page_title', 'Tạo Loại căn hộ')
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
        <span class="current">Thêm mới</span>
    </nav>

    {{-- Header --}}
    <div class="apartments-page__header">
        <div>
            <h1>Tạo Loại căn hộ mới</h1>
            <p class="apartments-page__subtitle">Định cấu hình nhóm phân khúc căn hộ cùng hệ số diện tích.</p>
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
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
            TYPE CREATION
        </div>

        <form action="{{ portal_route('apartment-types.store') }}" method="POST">
            @csrf

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
                            value="{{ old('name') }}"
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
                            type="number"
                            name="base_service_fee"
                            value="{{ old('base_service_fee', 0) }}"
                            placeholder="VD: 15000"
                            class="form-input-custom @error('base_service_fee') input-error @enderror"
                            required
                            min="0"
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
                            value="{{ old('bedroom_count', 1) }}"
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
                            value="{{ old('bathroom_count', 1) }}"
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

            {{-- Phần 3: Ghi chú & mô tả --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">03</span>
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
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="form-error-custom">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="apartments-page__actions" style="justify-content: flex-start; margin-top: 24px;">
                <button type="submit" class="apts-button apts-button--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Xác nhận tạo loại căn hộ
                </button>
                <a href="{{ portal_route('apartment-types.index') }}" class="apts-button apts-button--edit">
                    Hủy bỏ
                </a>
            </div>

        </form>
    </article>

</div>
@endsection
