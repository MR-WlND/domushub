@extends('layouts.admin.master')

@section('page_title', 'Sửa Căn hộ')
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
            <h1 class="page-title">Sửa Căn hộ</h1>

            <p class="page-subtitle">
                Cập nhật thông tin căn hộ hiện có
            </p>
        </div>

        <a href="{{ route('admin.apartments.index') }}" class="btn btn-light">
            ← Quay lại
        </a>

    </div>

    {{-- Form --}}
    <article class="dashboard-card form-card">

        <form action="{{ route('admin.apartments.update', $apartment) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Chọn Tầng --}}
            <div class="form-group">

                <label class="form-label">
                    Tầng
                    <span class="required">*</span>
                </label>

                <select
                    name="floor_id"
                    class="form-input @error('floor_id') input-error @enderror"
                    required
                >
                    <option value="">-- Chọn Tầng --</option>

                    @foreach($floors as $floor)
                        <option value="{{ $floor->id }}"
                            {{ old('floor_id', $apartment->floor_id) == $floor->id ? 'selected' : '' }}>
                            {{ $floor->block->name }} - {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                        </option>
                    @endforeach

                </select>

                @error('floor_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Số căn hộ --}}
            <div class="form-group">

                <label class="form-label">
                    Số căn hộ
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="apartment_number"
                    value="{{ old('apartment_number', $apartment->apartment_number) }}"
                    placeholder="VD: 101, A1..."
                    class="form-input @error('apartment_number') input-error @enderror"
                    required
                >

                @error('apartment_number')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Diện tích --}}
            <div class="form-group">

                <label class="form-label">
                    Diện tích (m²)
                    <span class="required">*</span>
                </label>

                <input
                    type="number"
                    step="0.01"
                    min="0.01"
                    name="area"
                    value="{{ old('area', $apartment->area) }}"
                    placeholder="VD: 45.5"
                    class="form-input @error('area') input-error @enderror"
                    required
                >

                @error('area')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Trạng thái --}}
            <div class="form-group">

                <label class="form-label">
                    Trạng thái
                </label>

                <select
                    name="status"
                    class="form-input @error('status') input-error @enderror"
                >
                    <option value="vacant" {{ old('status', $apartment->status) == 'vacant' ? 'selected' : '' }}>
                        Trống
                    </option>
                    <option value="occupied" {{ old('status', $apartment->status) == 'occupied' ? 'selected' : '' }}>
                        Đang ở
                    </option>
                    <option value="maintenance" {{ old('status', $apartment->status) == 'maintenance' ? 'selected' : '' }}>
                        Bảo trì
                    </option>
                </select>

                @error('status')
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
                    placeholder="Nhập mô tả căn hộ..."
                    class="form-input form-textarea @error('description') input-error @enderror"
                    rows="4"
                >{{ old('description', $apartment->description) }}</textarea>

                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror

            </div>

            {{-- Actions --}}
            <div class="form-actions">

                <button type="submit" class="btn btn-primary">
                    Lưu thay đổi
                </button>

                <a href="{{ route('admin.apartments.index') }}" class="btn btn-secondary">
                    Hủy
                </a>

            </div>

        </form>

    </article>

</div>

@endsection
