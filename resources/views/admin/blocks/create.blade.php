@extends('layouts.admin.master')

@section('page_title', 'Tạo Toà nhà')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/blocks/index.css'])
@endpush

@section('content')
<div class="blocks-page">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav">
        <a href="{{ portal_route('dashboard') }}">Trang chủ</a>
        <span class="divider">/</span>
        <a href="{{ portal_route('blocks.index') }}">Toà nhà</a>
        <span class="divider">/</span>
        <span class="current">Thêm mới</span>
    </nav>

    {{-- Header --}}
    <div class="blocks-page__header">
        <div>
            <h1>Tạo Toà nhà mới</h1>
            <p class="blocks-page__subtitle">Thêm một toà nhà mới vào hệ thống</p>
        </div>
        <div class="blocks-page__actions">
            <a href="{{ portal_route('blocks.index') }}" class="blocks-button blocks-button--light">
                ← Quay lại
            </a>
        </div>
    </div>

    {{-- Form --}}
    <article class="dashboard-card form-card-custom shadow-sm border-light">
        <div class="card-badge-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
            THÔNG TIN TÒA NHÀ
        </div>

        <form action="{{ portal_route('blocks.store') }}" method="POST">
            @csrf

            {{-- Phần 1: Thông tin cơ bản --}}
            <div class="form-section-header">
                <span class="section-number">01</span>
                <h4>Thông tin cơ bản</h4>
            </div>

            <div class="form-grid-2">
                {{-- Tên Toà --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Tên Toà nhà <span class="required">*</span></label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </span>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="VD: Toà A, Block B..." class="form-input-custom @error('name') input-error @enderror" required>
                    </div>
                    @error('name') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>

                {{-- Mã Toà --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Mã Toà nhà <small style="color:#94a3b8">(tuỳ chọn)</small></label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">#</span>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="VD: BLOCK_A" class="form-input-custom @error('code') input-error @enderror">
                    </div>
                    @error('code') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Trạng thái --}}
            <div class="form-group-custom">
                <label class="form-label-custom">Trạng thái hoạt động</label>
                <div class="input-wrapper-custom">
                    <span class="input-icon-custom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </span>
                    <select name="status" class="form-input-custom">
                        <option value="active"       {{ old('status', 'active') == 'active'       ? 'selected' : '' }}>Hoạt động</option>
                        <option value="maintenance"  {{ old('status') == 'maintenance'  ? 'selected' : '' }}>Bảo trì</option>
                        <option value="inactive"     {{ old('status') == 'inactive'     ? 'selected' : '' }}>Ngưng hoạt động</option>
                    </select>
                </div>
            </div>

            {{-- Phần 2: Thông tin người quản lý --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">02</span>
                <h4>Thông tin người quản lý <small style="color:#94a3b8; font-weight:400">(tuỳ chọn)</small></h4>
            </div>

            <div class="form-grid-2">
                <div class="form-group-custom">
                    <label class="form-label-custom">Họ tên người quản lý</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </span>
                        <input type="text" name="manager_name" value="{{ old('manager_name') }}" placeholder="VD: Nguyễn Văn A" class="form-input-custom @error('manager_name') input-error @enderror">
                    </div>
                    @error('manager_name') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>

                <div class="form-group-custom">
                    <label class="form-label-custom">Số điện thoại / Email liên hệ</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        </span>
                        <input type="text" name="manager_contact" value="{{ old('manager_contact') }}" placeholder="VD: 0901234567" class="form-input-custom @error('manager_contact') input-error @enderror">
                    </div>
                    @error('manager_contact') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Phần 3: Mô tả --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">03</span>
                <h4>Ghi chú <small style="color:#94a3b8; font-weight:400">(tuỳ chọn)</small></h4>
            </div>

            <div class="form-group-custom" style="margin-bottom: 24px;">
                <label class="form-label-custom">Mô tả toà nhà</label>
                <textarea name="description" placeholder="Nhập mô tả hoặc lưu ý về tòa nhà..." class="form-textarea-custom" rows="3">{{ old('description') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="blocks-page__actions" style="justify-content: flex-start; margin-top: 24px;">
                <button type="submit" class="blocks-button blocks-button--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Xác nhận thêm tòa nhà
                </button>
                <a href="{{ portal_route('blocks.index') }}" class="blocks-button blocks-button--light">
                    Hủy bỏ
                </a>
            </div>

        </form>
    </article>

</div>
@endsection