@extends('layouts.admin.master')

@section('page_title', 'Ghi chỉ số điện nước')
@section('page_kicker', 'Quản lý hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Ghi chỉ số điện nước</h1>
            <p class="page-subtitle">Nhập chỉ số mới cho căn hộ</p>
        </div>
        <a href="{{ route('admin.utility-readings.index') }}" class="btn btn-secondary">
            ← Quay lại danh sách
        </a>
    </div>

    @if ($errors->any())
        <div class="alert-danger">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.utility-readings.store') }}" method="POST" class="form-card">
        @csrf

        <div class="form-grid-2" style="gap: 20px;">
            <div class="form-group-custom">
                <label class="form-label-custom">Căn hộ</label>
                <select name="apartment_id" class="form-input-custom @error('apartment_id') input-error @enderror" required>
                    <option value="">Chọn căn hộ</option>
                    @foreach ($apartments as $apartment)
                        <option value="{{ $apartment->id }}" {{ old('apartment_id') == $apartment->id ? 'selected' : '' }}>
                            {{ $apartment->apartment_number }} - {{ $apartment->floor->block->name ?? '' }} /
                            {{ $apartment->floor->name ?? 'Tầng ' . $apartment->floor->floor_number }}
                        </option>
                    @endforeach
                </select>
                @error('apartment_id')
                    <p class="form-error-custom">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Loại</label>
                <select name="type" class="form-input-custom @error('type') input-error @enderror" required>
                    <option value="">Chọn loại</option>
                    <option value="electricity" {{ old('type') == 'electricity' ? 'selected' : '' }}>Điện</option>
                    <option value="water" {{ old('type') == 'water' ? 'selected' : '' }}>Nước</option>
                </select>
                @error('type')
                    <p class="form-error-custom">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-grid-3" style="gap: 20px; margin-top: 20px;">
            <div class="form-group-custom">
                <label class="form-label-custom">Tháng</label>
                <input type="number" name="record_month" value="{{ old('record_month', now()->month) }}" min="1"
                    max="12" class="form-input-custom @error('record_month') input-error @enderror" required>
                @error('record_month')
                    <p class="form-error-custom">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Năm</label>
                <input type="number" name="record_year" value="{{ old('record_year', now()->year) }}" min="2020"
                    max="2100" class="form-input-custom @error('record_year') input-error @enderror" required>
                @error('record_year')
                    <p class="form-error-custom">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group-custom">
                <label class="form-label-custom">Chỉ số cũ</label>
                <input type="number" name="old_value" value="{{ old('old_value') }}" min="0"
                    class="form-input-custom @error('old_value') input-error @enderror" required>
                @error('old_value')
                    <p class="form-error-custom">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="form-group-custom" style="margin-top: 20px;">
            <label class="form-label-custom">Chỉ số mới</label>
            <input type="number" name="new_value" value="{{ old('new_value') }}" min="0"
                class="form-input-custom @error('new_value') input-error @enderror" required>
            @error('new_value')
                <p class="form-error-custom">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary" style="margin-top: 24px;">Lưu chỉ số</button>
    </form>
@endsection
