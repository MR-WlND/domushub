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

    <div class="building-filter-panel">
        <div class="building-filter-row">
            <form action="{{ route('admin.blocks.index') }}" method="GET" class="dashboard-topbar__search">
                <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Tìm kiếm toà nhà..."
                    class="search-input"
                />
            </form>

            <div class="building-status-tabs">
                @php $filters = request()->except('page', 'status'); @endphp

                <a href="{{ route('admin.blocks.index', $filters) }}"
                   class="building-tab {{ empty($status) ? 'building-tab--active' : '' }}">
                    Tất cả
                </a>
                <a href="{{ route('admin.blocks.index', array_merge($filters, ['status' => 'active'])) }}"
                   class="building-tab {{ $status == 'active' ? 'building-tab--active' : '' }}">
                    Hoạt động
                </a>
                <a href="{{ route('admin.blocks.index', array_merge($filters, ['status' => 'maintenance'])) }}"
                   class="building-tab {{ $status == 'maintenance' ? 'building-tab--active' : '' }}">
                    Bảo trì
                </a>
                <a href="{{ route('admin.blocks.index', array_merge($filters, ['status' => 'inactive'])) }}"
                   class="building-tab {{ $status == 'inactive' ? 'building-tab--active' : '' }}">
                    Ngưng hoạt động
                </a>
            </div>
        </div>

        <div class="building-legend-row">
            <span class="dashboard-status-pill dashboard-status-pill--success">Hoạt động {{ $activeBlocks }}</span>
            <span class="dashboard-status-pill dashboard-status-pill--warning">Bảo trì {{ $maintenanceBlocks }}</span>
            <span class="dashboard-status-pill dashboard-status-pill--danger">Ngưng hoạt động {{ $inactiveBlocks }}</span>
            <span class="dashboard-status-pill">Tổng toà {{ $totalBlocks }}</span>
        </div>
    </div>

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
                            <h3 class="building-title">
                                {{ $block->name }}
                            </h3>

                            <p class="building-code">
                                {{ $block->code ?? 'NO-CODE' }}
                            </p>
                        </div>

                        <span class="building-status
                            {{ $block->status == 'active' ? 'status-active' : '' }}
                            {{ $block->status == 'maintenance' ? 'status-maintenance' : '' }}
                            {{ $block->status == 'inactive' ? 'status-inactive' : '' }}
                        ">
                            @if($block->status == 'active')
                                Hoạt động
                            @elseif($block->status == 'maintenance')
                                Bảo trì
                            @else
                                Ngưng hoạt động
                            @endif
                        </span>

                    </div>

                    {{-- Description --}}
                    <p class="building-desc">
                        {{ $block->description ?? 'Không có mô tả' }}
                    </p>

                    {{-- Stats --}}
                    <div class="building-stats">

                        <div class="building-mini-card">
                            <div class="building-mini-number">
                                {{ $block->floors_count }}
                            </div>
                            <div class="building-mini-label">Tầng</div>
                        </div>

                        <div class="building-mini-card">
                            <div class="building-mini-number">
                                {{ $block->apartments_count ?? 0 }}
                            </div>
                            <div class="building-mini-label">Phòng</div>
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="action-group">

                        <a href="{{ route('admin.floors.index', ['block_id' => $block->id]) }}"
                           class="btn btn-secondary">
                            Xem tầng
                        </a>

                        <a href="{{ route('admin.blocks.edit', $block) }}"
                           class="btn btn-light">
                            Sửa
                        </a>

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