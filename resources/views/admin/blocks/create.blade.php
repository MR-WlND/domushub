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

                        <option value="active">
                            Hoạt động
                        </option>

                        <option value="maintenance">
                            Bảo trì
                        </option>

                        <option value="inactive">
                            Ngưng hoạt động
                        </option>

                    </select>

                </div>

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