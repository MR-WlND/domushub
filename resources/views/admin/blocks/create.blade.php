@extends('layouts.admin.master')

@section('page_title', 'Tạo Toà nhà')
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
        <a href="{{ route('admin.blocks.index') }}">Toà nhà</a>
        <span class="divider">/</span>
        <span class="current">Thêm mới</span>
    </nav>

    {{-- Header --}}
    <div class="page-header" style="margin-top: 15px;">
        <div>
            <h1 class="page-title">Tạo Toà nhà mới</h1>
            <p class="page-subtitle">
                Thêm một toà nhà mới vào hệ thống quản lý chung cư
            </p>
        </div>
        <a href="{{ route('admin.blocks.index') }}" class="btn btn-secondary" style="display: flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; height: 38px; box-sizing: border-box; transition: all 0.2s;">
            ← Quay lại
        </a>
    </div>

    {{-- Form Card --}}
    <article class="dashboard-card form-card-custom shadow-sm border-light">
        <div class="card-badge-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            BUILDING CREATION
        </div>

        <form action="{{ route('admin.blocks.store') }}" method="POST">
            @csrf

            {{-- Phần 1: Thông tin cấu trúc --}}
            <div class="form-section-header">
                <span class="section-number">01</span>
                <h4>Thông tin cấu trúc tòa nhà</h4>
            </div>

            <div class="form-grid-2">
                {{-- Tên Toà --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Tên Toà nhà <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </span>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="VD: Toà A, Block B..."
                            class="form-input-custom @error('name') input-error @enderror"
                            required
                        >
                    </div>
                    @error('name')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mã Toà --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Mã Toà nhà</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">#</span>
                        <input
                            type="text"
                            name="code"
                            value="{{ old('code') }}"
                            placeholder="VD: BLOCK_A"
                            class="form-input-custom"
                        >
                    </div>
                </div>
            </div>

            <div class="form-grid-3">
                {{-- Trạng thái --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Trạng thái hoạt động</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </span>
                        <select name="status" class="form-input-custom">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Ngưng hoạt động</option>
                        </select>
                    </div>
                </div>

                {{-- Số tầng --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Số tầng</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
                        </span>
                        <input
                            type="number"
                            name="number_of_floors"
                            value="{{ old('number_of_floors') }}"
                            placeholder="VD: 10"
                            class="form-input-custom @error('number_of_floors') input-error @enderror"
                        >
                    </div>
                    @error('number_of_floors')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Số căn hộ --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Số căn hộ dự kiến</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        </span>
                        <input
                            type="number"
                            name="total_apartments"
                            value="{{ old('total_apartments') }}"
                            placeholder="VD: 100"
                            class="form-input-custom @error('total_apartments') input-error @enderror"
                        >
                    </div>
                    @error('total_apartments')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Phần 2: Thông tin quản lý --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">02</span>
                <h4>Thông tin người quản lý tòa nhà</h4>
            </div>

            <div class="form-grid-2">
                {{-- Người quản lý --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Họ tên người quản lý</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </span>
                        <input
                            type="text"
                            name="manager_name"
                            value="{{ old('manager_name') }}"
                            placeholder="VD: Nguyễn Văn A"
                            class="form-input-custom @error('manager_name') input-error @enderror"
                        >
                    </div>
                    @error('manager_name')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Liên hệ --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Số điện thoại / Email liên hệ</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        </span>
                        <input
                            type="text"
                            name="manager_contact"
                            value="{{ old('manager_contact') }}"
                            placeholder="VD: 0901234567 hoặc admin@domain.com"
                            class="form-input-custom @error('manager_contact') input-error @enderror"
                        >
                    </div>
                    @error('manager_contact')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Phần 3: Ghi chú chi tiết --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">03</span>
                <h4>Ghi chú & Mô tả chi tiết</h4>
            </div>

            {{-- Mô tả --}}
            <div class="form-group-custom" style="margin-bottom: 24px;">
                <label class="form-label-custom">Mô tả toà nhà</label>
                <textarea
                    name="description"
                    placeholder="Nhập mô tả chi tiết, đặc điểm hoặc lưu ý đặc thù về tòa nhà..."
                    class="form-textarea-custom"
                    rows="4"
                >{{ old('description') }}</textarea>
            </div>

            {{-- Action --}}
            <div class="form-actions-custom">
                <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; padding: 12px 24px; border-radius: 8px; font-size: 14px; cursor: pointer; border: none; background-color: #0b57d0; color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Xác nhận thêm toà nhà
                </button>
                <a href="{{ route('admin.blocks.index') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; justify-content: center; height: 44px; padding: 0 24px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px; background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; transition: all 0.2s;">
                    Hủy bỏ
                </a>
            </div>

        </form>
    </article>

</div>
@endsection