@extends('layouts.admin.master')

@section('page_title', 'Sửa Toà nhà')
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

    {{-- Breadcrumb Navigation --}}
    <nav class="breadcrumb-nav">
        <a href="{{ portal_route('dashboard') }}">Trang chủ</a>
        <span class="divider">/</span>
        <a href="{{ portal_route('blocks.index') }}">Toà nhà</a>
        <span class="divider">/</span>
        <span class="current">Cập nhật</span>
    </nav>

    {{-- Header --}}
    <div class="blocks-page__header">
        <div>
            <h1>Sửa Toà nhà</h1>
            <p class="blocks-page__subtitle">
                Đang chỉnh sửa tòa nhà: <strong style="color: #0b57d0;">{{ $block->name }}</strong>
            </p>
        </div>
        <div class="blocks-page__actions">
            <a href="{{ portal_route('blocks.show', $block) }}" class="blocks-button blocks-button--view">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                Xem chi tiết
            </a>
            <a href="{{ portal_route('blocks.index') }}" class="blocks-button blocks-button--light">
                ← Quay lại
            </a>
        </div>
    </div>

    {{-- Form Card --}}
    <article class="dashboard-card form-card-custom shadow-sm border-light">
        <div class="card-badge-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            BUILDING UPDATE
        </div>

        <form action="{{ portal_route('blocks.update', $block) }}" method="POST">
            @csrf
            @method('PUT')

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
                            value="{{ old('name', $block->name) }}"
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
                            value="{{ old('code', $block->code) }}"
                            placeholder="VD: BLOCK_A"
                            class="form-input-custom"
                        >
                    </div>
                </div>
            </div>

            {{-- Trạng thái --}}
            <div class="form-grid-2">
                <div class="form-group-custom">
                    <label class="form-label-custom">Trạng thái hoạt động</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </span>
                        <select name="status" class="form-input-custom">
                            <option value="active"       {{ old('status', $block->status) == 'active'       ? 'selected' : '' }}>Hoạt động</option>
                            <option value="maintenance"  {{ old('status', $block->status) == 'maintenance'  ? 'selected' : '' }}>Bảo trì</option>
                            <option value="inactive"     {{ old('status', $block->status) == 'inactive'     ? 'selected' : '' }}>Ngưng hoạt động</option>
                        </select>
                    </div>
                </div>

                {{-- Hàng trống để giữ tỷ lệ 2 cột --}}
                <div></div>
            </div>

            <div class="form-grid-2" style="margin-top: 15px;">
                {{-- Số tầng nổi --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Số tầng nổi</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                        </span>
                        <input type="number" name="total_floors" value="{{ old('total_floors', $block->total_floors) }}" placeholder="VD: 25" min="0" class="form-input-custom @error('total_floors') input-error @enderror">
                    </div>
                    @error('total_floors') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>

                {{-- Số tầng hầm --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Số tầng hầm</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                        </span>
                        <input type="number" name="total_basements" value="{{ old('total_basements', $block->total_basements) }}" placeholder="VD: 2" min="0" class="form-input-custom @error('total_basements') input-error @enderror">
                    </div>
                    @error('total_basements') <p class="form-error-custom">{{ $message }}</p> @enderror
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
                            value="{{ old('manager_name', $block->manager_name) }}"
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
                            value="{{ old('manager_contact', $block->manager_contact) }}"
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
                >{{ old('description', $block->description) }}</textarea>
            </div>

            {{-- Action --}}
            <div class="blocks-page__actions" style="justify-content: flex-start; margin-top: 24px;">
                <button type="submit" class="blocks-button blocks-button--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    Xác nhận cập nhật
                </button>
                <a href="{{ portal_route('blocks.index') }}" class="blocks-button blocks-button--light">
                    Hủy bỏ
                </a>
            </div>

        </form>
    </article>

</div>
@endsection