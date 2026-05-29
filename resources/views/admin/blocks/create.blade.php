@extends('layouts.admin.master')

@section('page_title', 'Tạo Toà nhà')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')
<div class="dashboard-content">

    {{-- Header --}}
    <div class="page-header">

        <div>
            <h1 class="page-title">Tạo Toà nhà mới</h1>

            <p class="page-subtitle">
                Thêm một toà nhà mới vào hệ thống quản lý chung cư
            </p>
        </div>

        <a href="{{ route('admin.blocks.index') }}" class="btn btn-light">
            <span>←</span>
            Quay lại
        </a>

    </div>

    {{-- Form Card --}}
    <article class="dashboard-card form-card">

        <div class="card-badge">
            Building Management
        </div>

        <form action="{{ route('admin.blocks.store') }}" method="POST">
            @csrf

            {{-- Tên Toà --}}
            <div class="form-group">

                <label class="form-label">
                    Tên Toà nhà
                    <span class="required">*</span>
                </label>

                <div class="input-wrapper">

                    <span class="input-icon">🏢</span>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="VD: Toà A"
                        class="form-input @error('name') input-error @enderror"
                        required
                    >

                </div>

                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Mã Toà --}}
            <div class="form-group">

                <label class="form-label">
                    Mã Toà nhà
                </label>

                <div class="input-wrapper">

                    <span class="input-icon">#</span>

                    <input
                        type="text"
                        name="code"
                        value="{{ old('code') }}"
                        placeholder="VD: BLOCK_A"
                        class="form-input"
                    >

                </div>

            </div>

            {{-- Trạng thái --}}
            <div class="form-group">

                <label class="form-label">
                    Trạng thái
                </label>

                <div class="input-wrapper">

                    <span class="input-icon">⚡</span>

                    <select name="status" class="form-input">

                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                            Hoạt động
                        </option>

                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>
                            Bảo trì
                        </option>

                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                            Ngưng hoạt động
                        </option>

                    </select>

                </div>

            </div>

            {{-- Số tầng --}}
            <div class="form-group">

                <label class="form-label">
                    Số tầng
                </label>

                <input
                    type="number"
                    name="number_of_floors"
                    value="{{ old('number_of_floors') }}"
                    placeholder="VD: 10"
                    class="form-input @error('number_of_floors') input-error @enderror"
                >

                @error('number_of_floors')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Số căn hộ --}}
            <div class="form-group">

                <label class="form-label">
                    Số căn hộ
                </label>

                <input
                    type="number"
                    name="total_apartments"
                    value="{{ old('total_apartments') }}"
                    placeholder="VD: 100"
                    class="form-input @error('total_apartments') input-error @enderror"
                >

                @error('total_apartments')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Người quản lý --}}
            <div class="form-group">

                <label class="form-label">
                    Người quản lý
                </label>

                <input
                    type="text"
                    name="manager_name"
                    value="{{ old('manager_name') }}"
                    placeholder="VD: Nguyễn Văn A"
                    class="form-input @error('manager_name') input-error @enderror"
                >

                @error('manager_name')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Liên hệ --}}
            <div class="form-group">

                <label class="form-label">
                    Số điện thoại / email
                </label>

                <input
                    type="text"
                    name="manager_contact"
                    value="{{ old('manager_contact') }}"
                    placeholder="VD: 0901234567 hoặc manager@example.com"
                    class="form-input @error('manager_contact') input-error @enderror"
                >

                @error('manager_contact')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Mô tả --}}
            <div class="form-group">

                <label class="form-label">
                    Mô tả
                </label>

                <textarea
                    name="description"
                    placeholder="Nhập mô tả toà nhà..."
                    class="form-input form-textarea"
                >{{ old('description') }}</textarea>

            </div>

            {{-- Action --}}
            <div class="form-actions">

                <button type="submit" class="btn btn-primary">
                    + Tạo Toà nhà
                </button>

                <a href="{{ route('admin.blocks.index') }}" class="btn btn-secondary">
                    Hủy
                </a>

            </div>

        </form>

    </article>

</div>
@endsection