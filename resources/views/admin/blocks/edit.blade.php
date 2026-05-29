@extends('layouts.admin.master')

@section('page_title', 'Sửa Toà nhà')
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
            <h1 class="page-title">Sửa Toà nhà</h1>

            <p class="page-subtitle">
                Đang chỉnh sửa:
                <strong>{{ $block->name }}</strong>
            </p>
        </div>

        <a href="{{ route('admin.blocks.index') }}" class="btn btn-secondary">
            ← Quay lại
        </a>

    </div>

    {{-- Form Card --}}
    <article class="dashboard-card form-card">

        <form action="{{ route('admin.blocks.update', $block) }}" method="POST">

            @csrf
            @method('PUT')

            {{-- Tên toà --}}
            <div class="form-group">

                <label class="form-label">
                    Tên Toà nhà
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $block->name) }}"
                    placeholder="VD: Toà A"
                    class="form-input @error('name') input-error @enderror"
                    required
                >

                @error('name')
                    <p class="form-error">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- Mã toà --}}
            <div class="form-group">

                <label class="form-label">
                    Mã Toà nhà
                </label>

                <input
                    type="text"
                    name="code"
                    value="{{ old('code', $block->code) }}"
                    placeholder="VD: BLOCK_A"
                    class="form-input"
                >

            </div>

            {{-- Trạng thái --}}
            <div class="form-group">

                <label class="form-label">
                    Trạng thái
                </label>

                <select name="status" class="form-input">

                    <option value="active"
                        {{ old('status', $block->status) == 'active' ? 'selected' : '' }}>
                        Hoạt động
                    </option>

                    <option value="maintenance"
                        {{ old('status', $block->status) == 'maintenance' ? 'selected' : '' }}>
                        Bảo trì
                    </option>

                    <option value="inactive"
                        {{ old('status', $block->status) == 'inactive' ? 'selected' : '' }}>
                        Ngưng hoạt động
                    </option>

                </select>

            </div>

            {{-- Số tầng --}}
            <div class="form-group">

                <label class="form-label">
                    Số tầng
                </label>

                <input
                    type="number"
                    name="number_of_floors"
                    value="{{ old('number_of_floors', $block->number_of_floors) }}"
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
                    value="{{ old('total_apartments', $block->total_apartments) }}"
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
                    value="{{ old('manager_name', $block->manager_name) }}"
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
                    value="{{ old('manager_contact', $block->manager_contact) }}"
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
                >{{ old('description', $block->description) }}</textarea>

            </div>

            {{-- Buttons --}}
            <div class="form-actions">

                <button type="submit" class="btn btn-primary">
                    ✓ Cập nhật
                </button>

                <a href="{{ route('admin.blocks.index') }}" class="btn btn-light">
                    Hủy
                </a>

            </div>

        </form>

    </article>

</div>
@endsection