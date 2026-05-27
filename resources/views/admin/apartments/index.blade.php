@extends('layouts.admin.master')
@section('title', 'Quản lý Căn hộ')
@section('content')

<div class="apartment-page">

    {{-- Header --}}
    <div class="apartment-page__header">

        <div>
            <h1 class="apartment-page__title">
                Danh sách Căn hộ
            </h1>

            <p class="apartment-page__subtitle">
                Quản lý toàn bộ căn hộ trong hệ thống
            </p>
        </div>

        <a href="{{ route('admin.apartments.create') }}"
            class="btn-add-apartment">
            + Thêm Căn hộ
        </a>

    </div>

    {{-- Stats --}}
    <div class="apartment-stats">

        <div class="apartment-stat-card">
            <span class="apartment-stat-card__label">
                Tổng căn hộ
            </span>

            <h3>
                {{ $totalApartments ?? 0 }}
            </h3>
        </div>

        <div class="apartment-stat-card">
            <span class="apartment-stat-card__label">
                Đang ở
            </span>

            <h3 class="text-success">
                {{ $occupiedApartments ?? 0 }}
            </h3>
        </div>

        <div class="apartment-stat-card">
            <span class="apartment-stat-card__label">
                Trống
            </span>

            <h3 class="text-warning">
                {{ $vacantApartments ?? 0 }}
            </h3>
        </div>

        <div class="apartment-stat-card">
            <span class="apartment-stat-card__label">
                Bảo trì
            </span>

            <h3 class="text-danger">
                {{ $maintenanceApartments ?? 0 }}
            </h3>
        </div>

    </div>

    {{-- Filters --}}
    <div class="apartment-filter-card">

        <form method="GET">

            <div class="apartment-filter-grid">

                <div>
                    <label>Tòa nhà</label>

                    <select name="block_id">

                        <option value="">
                            Tất cả tòa
                        </option>

                        @foreach($blocks as $block)

                        <option value="{{ $block->id }}"
                            {{ request('block_id') == $block->id ? 'selected' : '' }}>

                            {{ $block->name }}

                        </option>

                        @endforeach

                    </select>
                </div>

                <div>
                    <label>Tầng</label>

                    <select name="floor_id">

                        <option value="">
                            Tất cả tầng
                        </option>

                        @foreach($floors as $floor)

                        <option value="{{ $floor->id }}"
                            {{ request('floor_id') == $floor->id ? 'selected' : '' }}>

                            {{ $floor->name }}

                        </option>

                        @endforeach

                    </select>
                </div>

                <div>
                    <label>Trạng thái</label>

                    <select name="status">

                        <option value="">
                            Tất cả
                        </option>

                        <option value="occupied">
                            Đang ở
                        </option>

                        <option value="vacant">
                            Trống
                        </option>

                        <option value="maintenance">
                            Bảo trì
                        </option>

                    </select>
                </div>

            </div>

        </form>

    </div>

    {{-- Apartments Grid --}}
    <div class="apartment-grid">

        @forelse($apartments as $apartment)

        <div class="apartment-card">

            {{-- top --}}
            <div class="apartment-card__top">

                <div>

                    <div class="apartment-number">
                        {{ $apartment->apartment_number }}
                    </div>

                    <div class="apartment-location">

                        {{ $apartment->floor->name ?? '' }}
                        -
                        {{ $apartment->floor->block->name ?? '' }}

                    </div>

                </div>

                <div class="apartment-status
                        @if($apartment->status == 'occupied')
                            apartment-status--occupied
                        @elseif($apartment->status == 'vacant')
                            apartment-status--vacant
                        @else
                            apartment-status--maintenance
                        @endif
                    ">

                    @if($apartment->status == 'occupied')
                    Đang ở
                    @elseif($apartment->status == 'vacant')
                    Trống
                    @else
                    Bảo trì
                    @endif

                </div>

            </div>

            {{-- description --}}
            <div class="apartment-description">

                {{ $apartment->description ?? 'Không có mô tả' }}

            </div>

            {{-- stats --}}
            <div class="apartment-card__stats">

                <div class="apartment-mini-stat">

                    <div class="apartment-mini-stat__value">
                        {{ $apartment->area ?? 0 }}
                    </div>

                    <div class="apartment-mini-stat__label">
                        m²
                    </div>

                </div>

                <div class="apartment-mini-stat">

                    <div class="apartment-mini-stat__value">
                        {{ $apartment->residents_count ?? 0 }}
                    </div>

                    <div class="apartment-mini-stat__label">
                        Cư dân
                    </div>

                </div>

            </div>

            {{-- actions --}}
            <div class="apartment-actions">

                <a href="#"
                    class="btn-apartment btn-apartment--view">

                    Chi tiết

                </a>

                <a href="{{ route('admin.apartments.edit', $apartment->id) }}"
                    class="btn-apartment btn-apartment--edit">

                    Sửa

                </a>

            </div>

        </div>

        @empty

        <div class="empty-apartment">

            Chưa có căn hộ nào

        </div>

        @endforelse

    </div>

</div>

@endsection