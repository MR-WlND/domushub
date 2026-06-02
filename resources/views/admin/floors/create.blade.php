@extends('layouts.admin.master')

@section('page_title', 'Tạo Tầng')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/floors/index.css'])
@endpush

@section('content')
<div class="floors-page">

    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav">
        <a href="{{ route('admin.dashboard') }}">Trang chủ</a>
        <span class="divider">/</span>
        <a href="{{ route('admin.blocks.index') }}">Tầng</a>
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
            <a href="{{ route('admin.blocks.index') }}" class="floors-button floors-button--light">
                ← Quay lại
            </a>
        </div>
    </div>

    {{-- Form --}}
    <article class="dashboard-card form-card-custom shadow-sm border-light">
        <div class="card-badge-custom">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
            THÊM TẦNG MỚI
        </div>

        <form action="{{ route('admin.floors.store') }}" method="POST">
            @csrf

            {{-- Phần 1: Vị trí tầng --}}
            <div class="form-section-header">
                <span class="section-number">01</span>
                <h4>Vị trí trong tòa nhà</h4>
            </div>

            <div class="form-grid-2">
                {{-- Chọn Tòa nhà --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Tòa nhà <span class="required">*</span></label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </span>
                        <select name="block_id" class="form-input-custom @error('block_id') input-error @enderror" required>
                            <option value="">-- Chọn tòa nhà --</option>
                            @foreach($blocks as $block)
                                <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                    {{ $block->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('block_id') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>

                {{-- Số tầng --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Số tầng <span class="required">*</span></label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" /></svg>
                        </span>
                        <input type="number" name="floor_number" value="{{ old('floor_number') }}" placeholder="VD: 1, 2, -1 (tầng hầm)..." class="form-input-custom @error('floor_number') input-error @enderror" required>
                    </div>
                    @error('floor_number') <p class="form-error-custom">{{ $message }}</p> @enderror
                    <p style="font-size:12px; color:#94a3b8; margin-top:4px;">Tầng hầm nhập số âm: -1, -2. Tầng trệt nhập 0.</p>
                </div>
            </div>

            {{-- Phần 2: Thông tin bổ sung --}}
            <div class="form-section-header" style="margin-top: 15px;">
                <span class="section-number">02</span>
                <h4>Thông tin bổ sung <small style="color:#94a3b8; font-weight:400">(tuỳ chọn)</small></h4>
            </div>

            <div class="form-grid-2">
                {{-- Tên tầng --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Tên tầng <small style="color:#94a3b8">(tuỳ chọn)</small></label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">A</span>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="VD: Tầng 1, Tầng Hầm, Tầng Kỹ thuật..." class="form-input-custom @error('name') input-error @enderror">
                    </div>
                    @error('name') <p class="form-error-custom">{{ $message }}</p> @enderror
                </div>

                {{-- Trạng thái --}}
                <div class="form-group-custom">
                    <label class="form-label-custom">Trạng thái</label>
                    <div class="input-wrapper-custom">
                        <span class="input-icon-custom">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </span>
                        <select name="status" class="form-input-custom">
                            <option value="active"      {{ old('status', 'active') == 'active'      ? 'selected' : '' }}>Hoạt động</option>
                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                            <option value="inactive"    {{ old('status') == 'inactive'    ? 'selected' : '' }}>Ngưng hoạt động</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Ghi chú --}}
            <div class="form-group-custom" style="margin-top: 16px; margin-bottom: 24px;">
                <label class="form-label-custom">Ghi chú</label>
                <textarea name="description" placeholder="Ghi chú về tầng này (nếu có)..." class="form-textarea-custom" rows="3">{{ old('description') }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="floors-page__actions" style="justify-content: flex-start; margin-top: 24px;">
                <button type="submit" class="floors-button floors-button--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Xác nhận thêm tầng
                </button>
                <a href="{{ route('admin.blocks.index') }}" class="floors-button floors-button--light">
                    Hủy bỏ
                </a>
            </div>

        </form>
    </article>

</div>
@endsection