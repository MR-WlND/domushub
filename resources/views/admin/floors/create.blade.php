@extends('layouts.admin.master')

@section('page_title', 'Tạo Tầng')
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
                Tạo Tầng mới
            </h1>

            <p class="page-subtitle">
                Thêm tầng mới vào hệ thống quản lý
            </p>
        </div>

        <a href="{{ route('admin.floors.index') }}"
           class="btn btn-secondary">
            ← Quay lại
        </a>

    </div>

    {{-- Form --}}
    <article class="dashboard-card form-card">

        <form action="{{ route('admin.floors.store') }}"
              method="POST">

            @csrf

            <div class="form-row">

                {{-- Tên tầng --}}
                <div class="form-group">

                    <label class="form-label">
                        Tên tầng
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Ví dụ: Tầng 15, Tầng Hầm 1..."
                        class="form-input @error('name') input-error @enderror"
                        required
                    >

                    @error('name')
                        <p class="form-error">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Số lượng căn hộ dự kiến --}}
                <div class="form-group">

                    <label class="form-label">
                        Số lượng căn hộ dự kiến
                    </label>

                    <input
                        type="number"
                        name="expected_apartments"
                        value="{{ old('expected_apartments', 0) }}"
                        placeholder="0"
                        min="0"
                        class="form-input @error('expected_apartments') input-error @enderror"
                    >

                    @error('expected_apartments')
                        <p class="form-error">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

            <div class="form-row">

                {{-- Block / Tòa nhà --}}
                <div class="form-group">

                    <label class="form-label">
                        Block / Tòa nhà
                        <span class="required">*</span>
                    </label>

                    <select
                        name="block_id"
                        class="form-input @error('block_id') input-error @enderror"
                        required
                    >

                        <option value="">
                            -- Chọn toà nhà --
                        </option>

                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}"
                                {{ old('block_id') == $block->id ? 'selected' : '' }}>
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

                {{-- Loại tầng --}}
                <div class="form-group">

                    <label class="form-label">
                        Loại tầng
                        <span class="required">*</span>
                    </label>

                    <select
                        name="floor_type"
                        class="form-input @error('floor_type') input-error @enderror"
                        required
                    >
                        <option value="resident" {{ old('floor_type') == 'resident' ? 'selected' : '' }}>
                            Cư dân
                        </option>
                        <option value="basement" {{ old('floor_type') == 'basement' ? 'selected' : '' }}>
                            Tầng hầm
                        </option>
                        <option value="commercial" {{ old('floor_type') == 'commercial' ? 'selected' : '' }}>
                            Thương mại
                        </option>
                        <option value="service" {{ old('floor_type') == 'service' ? 'selected' : '' }}>
                            Tiện ích
                        </option>
                    </select>

                    @error('floor_type')
                        <p class="form-error">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

            <div class="form-row">

                {{-- Trạng thái --}}
                <div class="form-group">

                    <label class="form-label">
                        Trạng thái
                        <span class="required">*</span>
                    </label>

                    <select
                        name="status"
                        class="form-input @error('status') input-error @enderror"
                        required
                    >
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                            Hoạt động
                        </option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>
                            Bảo trì
                        </option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                            Ngưng hoạt động
                        </option>
                    </select>

                    @error('status')
                        <p class="form-error">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

                {{-- Ghi chú --}}
                <div class="form-group">

                    <label class="form-label">
                        Ghi chú
                    </label>

                    <textarea
                        name="description"
                        placeholder="Nhập ghi chú chi tiết về tầng này..."
                        class="form-input form-textarea @error('description') input-error @enderror"
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <p class="form-error">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

            <div class="info-box">
                <i class="icon">ℹ️</i>
                <div>
                    <strong>Lưu ý hệ thống</strong>
                    <p>Sau khi thêm tầng, hệ thống sẽ tự động tạo sơ đồ mặt bằng trống. Bạn có thể thêm chi tiết từng căn hộ trong phần quản lý căn hộ sau.</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Xác nhận thêm tầng
                </button>

                <a href="{{ route('admin.floors.index') }}" class="btn btn-light">
                    Hủy
                </a>
            </div>

        </form>

    </article>

</div>

@endsection