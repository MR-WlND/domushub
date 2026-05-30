@extends('layouts.admin.master')

@section('page_title', 'Tạo Tầng')
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
        <a href="{{ route('admin.floors.index') }}">Tầng</a>
        <span class="divider">/</span>
        <span class="current">Thêm mới</span>
    </nav>

    {{-- Header --}}
    <div class="page-header" style="margin-top: 15px;">
        <div>
            <h1 class="page-title">Tạo Tầng mới</h1>
            <p class="page-subtitle">
                Thêm tầng mới vào hệ thống quản lý
            </p>
        </div>
        <a href="{{ route('admin.floors.index') }}" class="btn btn-secondary" style="display: flex; align-items: center; gap: 8px; text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; height: 38px; box-sizing: border-box; transition: all 0.2s;">
            ← Quay lại
        </a>
    </div>

    {{-- Form Card --}}
    <article class="dashboard-card form-card-custom shadow-sm border-light">
        <div class="card-badge-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
            FLOOR CREATION
        </div>

        <form action="{{ route('admin.floors.store') }}" method="POST">
            @csrf

            {{-- Phần 1: Thông tin cấu trúc tầng --}}
            <div class="form-section-header">
                <span class="section-number">01</span>
                <h4>Thông tin cấu trúc tầng</h4>
            </div>

            <div class="form-grid-2">
                {{-- Tên tầng --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Tên tầng <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
                        </span>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ví dụ: Tầng 15, Tầng Hầm 1..."
                            class="form-input-custom @error('name') input-error @enderror"
                            required
                        >
                    </div>
                    @error('name')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Số lượng căn hộ dự kiến --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Số lượng căn hộ</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">#</span>
                        <input
                            type="number"
                            name="expected_apartments"
                            value="{{ old('expected_apartments', 0) }}"
                            placeholder="0"
                            min="0"
                            class="form-input-custom @error('expected_apartments') input-error @enderror"
                        >
                    </div>
                    @error('expected_apartments')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Phần 2: Vị trí & Phân loại --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">02</span>
                <h4>Vị trí & Phân loại</h4>
            </div>

            <div class="form-grid-2">
                {{-- Block / Tòa nhà --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Block / Tòa nhà <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </span>
                        <select
                            name="block_id"
                            class="form-input-custom @error('block_id') input-error @enderror"
                            required
                        >
                            <option value="">-- Chọn toà nhà --</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                    {{ $block->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('block_id')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Loại tầng --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Loại tầng <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" /></svg>
                        </span>
                        <select
                            name="floor_type"
                            class="form-input-custom @error('floor_type') input-error @enderror"
                            required
                        >
                            <option value="resident" {{ old('floor_type') == 'resident' ? 'selected' : '' }}>Cư dân</option>
                            <option value="basement" {{ old('floor_type') == 'basement' ? 'selected' : '' }}>Tầng hầm</option>
                            <option value="commercial" {{ old('floor_type') == 'commercial' ? 'selected' : '' }}>Thương mại</option>
                            <option value="service" {{ old('floor_type') == 'service' ? 'selected' : '' }}>Tiện ích</option>
                        </select>
                    </div>
                    @error('floor_type')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Phần 3: Trạng thái & Ghi chú --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">03</span>
                <h4>Trạng thái & Ghi chú</h4>
            </div>

            <div class="form-grid-2">
                {{-- Trạng thái --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">
                        Trạng thái <span class="required">*</span>
                    </label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </span>
                        <select
                            name="status"
                            class="form-input-custom @error('status') input-error @enderror"
                            required
                        >
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Ngưng hoạt động</option>
                        </select>
                    </div>
                    @error('status')
                        <p class="form-error-custom">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Ghi chú --}}
            <div class="form-group-custom" style="margin-top: 16px; margin-bottom: 24px;">
                <label class="form-label-custom">Ghi chú</label>
                <textarea
                    name="description"
                    placeholder="Nhập ghi chú chi tiết về tầng này..."
                    class="form-textarea-custom @error('description') input-error @enderror"
                    rows="4"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="form-error-custom">{{ $message }}</p>
                @enderror
            </div>

            {{-- Info Alert Box --}}
            <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #0b57d0; padding: 16px; border-radius: 8px; margin-bottom: 24px; display: flex; gap: 12px; align-items: flex-start;">
                <span style="color: #0b57d0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </span>
                <div>
                    <strong style="color: #1e293b; font-size: 14px; font-weight: 600; display: block; margin-bottom: 4px;">Lưu ý hệ thống</strong>
                    <p style="color: #64748b; font-size: 13px; margin: 0; line-height: 1.5;">Sau khi thêm tầng, hệ thống sẽ tự động tạo sơ đồ mặt bằng trống. Bạn có thể thêm chi tiết từng căn hộ trong phần quản lý căn hộ sau.</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="form-actions-custom">
                <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; padding: 12px 24px; border-radius: 8px; font-size: 14px; cursor: pointer; border: none; background-color: #0b57d0; color: white;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Xác nhận thêm tầng
                </button>
                <a href="{{ route('admin.floors.index') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; justify-content: center; height: 44px; padding: 0 24px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px; background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; transition: all 0.2s;">
                    Hủy bỏ
                </a>
            </div>

        </form>
    </article>

</div>
@endsection