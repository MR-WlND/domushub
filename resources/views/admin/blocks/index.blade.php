@extends('layouts.admin.master')

@section('page_title', 'Quản lý Toà nhà')
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
                Quản lý Toà nhà
            </h1>

            <p class="page-subtitle">
                Quản lý tất cả toà nhà trong khu dân cư
            </p>
        </div>

        <a href="{{ route('admin.blocks.create') }}"
           class="btn btn-primary">
            + Thêm Toà nhà
        </a>

    </div>

    {{-- Success Message --}}
    @if ($message = Session::get('success'))
        <div class="alert-success">
            {{ $message }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="stats-grid">

        <article class="dashboard-card stat-card">
            <p class="stat-label">Tổng Toà</p>
            <h2 class="stat-number">
                {{ $blocks->total() }}
            </h2>
        </article>

        <article class="dashboard-card stat-card">
            <p class="stat-label">Đang hoạt động</p>
            <h2 class="stat-number text-success">
                {{ $blocks->where('status', 'active')->count() }}
            </h2>
        </article>

        <article class="dashboard-card stat-card">
            <p class="stat-label">Bảo trì</p>
            <h2 class="stat-number text-warning">
                {{ $blocks->where('status', 'maintenance')->count() }}
            </h2>
        </article>

    </div>

    {{-- Buildings --}}
    @if($blocks->count() > 0)

        <div class="building-grid">

            @foreach($blocks as $block)

                <article class="dashboard-card building-card">

                    {{-- Top --}}
                    <div class="building-top">

                        <div>
                            <h3 class="building-name">
                                {{ $block->name }}
                            </h3>

                            <p class="building-code">
                                {{ $block->code ?? 'NO-CODE' }}
                            </p>
                        </div>

                        <span class="status-badge
                            {{ $block->status == 'active' ? 'status-success' : '' }}
                            {{ $block->status == 'maintenance' ? 'status-warning' : '' }}
                            {{ $block->status == 'inactive' ? 'status-danger' : '' }}
                        ">
                            @if($block->status == 'active')
                                Hoạt động
                            @elseif($block->status == 'maintenance')
                                Bảo trì
                            @else
                                Ngưng
                            @endif
                        </span>

                    </div>

                    {{-- Description --}}
                    <p class="building-description">
                        {{ $block->description ?? 'Không có mô tả' }}
                    </p>

                    {{-- Stats --}}
                    <div class="building-stats">

                        <div class="building-stat-box">
                            <strong>{{ $block->floors_count }}</strong>
                            <span>Tầng</span>
                        </div>

                        <div class="building-stat-box">
                            <strong>
                                {{ $block->apartments_count ?? 0 }}
                            </strong>
                            <span>Phòng</span>
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="building-actions">

                        <a href="{{ route('admin.buildings.show', $block) }}"
                           class="btn btn-secondary">
                            Chi tiết
                        </a>

                        <a href="{{ route('admin.floors.index', ['block_id' => $block->id]) }}"
                           class="btn btn-light">
                            Xem tầng
                        </a>

                        <a href="{{ route('admin.blocks.edit', $block) }}"
                           class="btn btn-light">
                            Sửa
                        </a>

                        <form action="{{ route('admin.blocks.destroy', $block) }}"
                              method="POST"
                              onsubmit="return confirm('Bạn chắc chắn muốn xoá toà này?')">

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
            {{ $blocks->links() }}
        </div>

    @else

        <article class="dashboard-card empty-state">

            <h3>Chưa có Toà nhà nào</h3>

            <p>
                Hãy tạo toà nhà đầu tiên cho hệ thống.
            </p>

            <a href="{{ route('admin.blocks.create') }}"
               class="btn btn-primary">
                + Tạo Toà nhà
            </a>

        </article>

    @endif

</div>

@endsection