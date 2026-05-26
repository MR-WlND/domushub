@extends('layouts.admin.master')

@section('page_title', 'Chỉnh sửa Tầng')
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

            <h1 class="page-title">
                Chỉnh sửa Tầng
            </h1>

            <p class="page-subtitle">
                Đang chỉnh sửa:
                <strong>
                    {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                </strong>
            </p>

        </div>

        <a href="{{ route('admin.floors.index') }}"
           class="btn btn-secondary">

            ← Quay lại

        </a>

    </div>

    {{-- Form --}}
    <article class="dashboard-card form-card">

        <form action="{{ route('admin.floors.update', $floor) }}"
              method="POST">

            @csrf
            @method('PUT')

            {{-- Toà nhà --}}
            <div class="form-group">

                <label class="form-label">

                    Toà nhà
                    <span class="required">*</span>

                </label>

                <select
                    name="block_id"
                    class="form-input @error('block_id') input-error @enderror"
                    required
                >

                    <option value="">
                        -- Chọn Toà nhà --
                    </option>

                    @foreach($blocks as $block)

                        <option value="{{ $block->id }}"
                            {{ old('block_id', $floor->block_id) == $block->id ? 'selected' : '' }}>

                            {{ $block->name }}

                        </option>

                    @endforeach

                </select>

                @error('block_id')

                    <p class="form-error">
                        {{ $message }}
                    </p>

                @enderror

            </div>

            {{-- Số tầng --}}
            <div class="form-group">

                <label class="form-label">

                    Số tầng
                    <span class="required">*</span>

                </label>

                <input
                    type="number"
                    name="floor_number"
                    value="{{ old('floor_number', $floor->floor_number) }}"
                    placeholder="VD: 1, 2, 3..."
                    class="form-input @error('floor_number') input-error @enderror"
                    required
                >

                @error('floor_number')

                    <p class="form-error">
                        {{ $message }}
                    </p>

                @enderror

            </div>

            {{-- Tên tầng --}}
            <div class="form-group">

                <label class="form-label">
                    Tên tầng
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $floor->name) }}"
                    placeholder="VD: Tầng VIP"
                    class="form-input"
                >

            </div>

            {{-- Trạng thái --}}
            <div class="form-group">

                <label class="form-label">
                    Trạng thái
                </label>

                <select
                    name="status"
                    class="form-input"
                >

                    <option value="active"
                        {{ old('status', $floor->status) == 'active' ? 'selected' : '' }}>
                        Hoạt động
                    </option>

                    <option value="maintenance"
                        {{ old('status', $floor->status) == 'maintenance' ? 'selected' : '' }}>
                        Bảo trì
                    </option>

                    <option value="inactive"
                        {{ old('status', $floor->status) == 'inactive' ? 'selected' : '' }}>
                        Ngưng hoạt động
                    </option>

                </select>

            </div>

            {{-- Mô tả --}}
            <div class="form-group">

                <label class="form-label">
                    Mô tả
                </label>

                <textarea
                    name="description"
                    placeholder="Nhập mô tả tầng..."
                    class="form-input form-textarea"
                >{{ old('description', $floor->description) }}</textarea>

            </div>

            {{-- Actions --}}
            <div class="form-actions">

                <button type="submit"
                        class="btn btn-primary">

                    ✓ Cập nhật Tầng

                </button>

                <a href="{{ route('admin.floors.index') }}"
                   class="btn btn-light">

                    Hủy

                </a>

            </div>

        </form>

    </article>

</div>

@endsection