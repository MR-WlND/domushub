@extends('layouts.admin.master')

@section('page_title', 'Quản lý Tầng')
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

                Danh sách Tầng

                @if($block)
                    <span class="page-title-muted">
                        - {{ $block->name }}
                    </span>
                @endif

            </h1>

            <p class="page-subtitle">
                Quản lý các tầng trong hệ thống
            </p>

        </div>

        <a href="{{ route('admin.floors.create') }}"
           class="btn btn-primary">

            + Thêm Tầng

        </a>

    </div>

    {{-- Filter --}}
    @if($blocks->count() > 1)

        <div class="dashboard-card filter-card">

            <form method="GET">

                <div class="filter-group">

                    <label class="form-label">
                        Lọc theo Toà nhà
                    </label>

                    <select
                        name="block_id"
                        onchange="this.form.submit()"
                        class="form-input"
                    >

                        <option value="">
                            -- Tất cả Toà --
                        </option>

                        @foreach($blocks as $b)

                            <option value="{{ $b->id }}"
                                {{ $block && $block->id == $b->id ? 'selected' : '' }}>

                                {{ $b->name }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </form>

        </div>

    @endif

    {{-- Message --}}
    @if ($message = Session::get('success'))

        <div class="alert-success">
            {{ $message }}
        </div>

    @endif

    {{-- Floors --}}
    @if($floors->count() > 0)

        <div class="floor-grid">

            @foreach($floors as $floor)

                <article class="dashboard-card floor-card">

                    {{-- Top --}}
                    <div class="floor-top">

                        <div>

                            <h3 class="floor-title">

                                {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}

                            </h3>

                            <p class="floor-building">

                                {{ $floor->block->name }}

                            </p>

                        </div>

                        <span class="status-badge
                            {{ $floor->status == 'active' ? 'status-success' : '' }}
                            {{ $floor->status == 'maintenance' ? 'status-warning' : '' }}
                            {{ $floor->status == 'inactive' ? 'status-danger' : '' }}
                        ">

                            @if($floor->status == 'active')
                                Hoạt động
                            @elseif($floor->status == 'maintenance')
                                Bảo trì
                            @else
                                Ngưng
                            @endif

                        </span>

                    </div>

                    {{-- Description --}}
                    <p class="floor-description">

                        {{ $floor->description ?? 'Không có mô tả' }}

                    </p>

                    {{-- Stats --}}
                    <div class="floor-stats">

                        <div class="floor-stat-item">

                            <strong>
                                {{ $floor->apartments_count }}
                            </strong>

                            <span>Căn hộ</span>

                        </div>

                        <div class="floor-stat-item">

                            <strong>
                                #{{ $floor->floor_number }}
                            </strong>

                            <span>Số tầng</span>

                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="floor-actions">

                        <a href="{{ route('admin.apartments.index', ['floor_id' => $floor->id]) }}"
                           class="btn btn-secondary">

                            Xem Căn

                        </a>

                        <a href="{{ route('admin.floors.edit', $floor) }}"
                           class="btn btn-light">

                            Sửa

                        </a>

                        <form action="{{ route('admin.floors.destroy', $floor) }}"
                              method="POST"
                              onsubmit="return confirm('Bạn chắc chắn muốn xóa tầng này?')">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-danger">

                                Xóa

                            </button>

                        </form>

                    </div>

                </article>

            @endforeach

        </div>

        {{-- Pagination --}}
        <div class="pagination-wrapper">
            {{ $floors->links() }}
        </div>

    @else

        <article class="dashboard-card empty-state">

            <h3>Chưa có tầng nào</h3>

            <p>
                Hãy tạo tầng đầu tiên cho hệ thống.
            </p>

            <a href="{{ route('admin.floors.create') }}"
               class="btn btn-primary">

                + Tạo Tầng

            </a>

        </article>

    @endif

</div>

@endsection