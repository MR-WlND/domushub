@extends('layouts.resident.master')

@section('title', 'Hồ sơ chủ hộ')

@push('styles')
    @vite(['resources/css/pages/resident/profile/index.css'])
@endpush

@section('content')
    <section class="resident-profile-page">
        <div class="page-header">
            <p class="page-eyebrow">Hồ sơ cư dân</p>
            <h1>Thông tin cá nhân và nhân khẩu</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-24">
            <div class="card__header">
                <h2>Thông tin chủ hộ</h2>
            </div>
            <div class="card__body">
                <form action="{{ route('resident.profile.update') }}" method="POST" class="profile-form">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>CCCD</label>
                        <input type="text" name="cccd" value="{{ old('cccd', $user->cccd) }}">
                        @error('cccd')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Cập nhật hồ sơ</button>
                </form>
            </div>
        </div>

    </section>
@endsection
