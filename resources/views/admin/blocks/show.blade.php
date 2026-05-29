@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Toà nhà')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')

<div class="dashboard-content">

    <div class="page-header">

        <div>
            <h1 class="page-title">{{ $block->name }}</h1>
            <p class="page-subtitle">
                Thông tin chi tiết về tòa nhà
            </p>
        </div>

        <div class="page-header-actions">
            <a href="{{ route('admin.blocks.edit', $block) }}" class="btn btn-light">
                Sửa
            </a>
            <a href="{{ route('admin.blocks.index') }}" class="btn btn-secondary">
                Quay lại
            </a>
        </div>

    </div>

    @if ($message = Session::get('success'))
        <div class="alert-success">
            {{ $message }}
        </div>
    @endif

    <div class="dashboard-grid">

        <article class="dashboard-card">
            <div class="card-header">
                <h2>Thông tin chung</h2>
            </div>

            <div class="card-body">
                <div class="detail-row">
                    <span class="detail-label">Mã tòa nhà</span>
                    <span>{{ $block->code ?? 'Chưa có' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Trạng thái</span>
                    <span>{{ ucfirst($block->status ?? 'active') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Số tầng</span>
                    <span>{{ $block->number_of_floors ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tổng căn hộ</span>
                    <span>{{ $block->total_apartments ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Người quản lý</span>
                    <span>{{ $block->manager_name ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Liên hệ</span>
                    <span>{{ $block->manager_contact ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Mô tả</span>
                    <span>{{ $block->description ?? 'Không có mô tả' }}</span>
                </div>
            </div>
        </article>

        <article class="dashboard-card">
            <div class="card-header">
                <h2>Thống kê</h2>
            </div>

            <div class="card-body stats-grid">
                <div class="stat-card">
                    <p class="stat-label">Tầng</p>
                    <h3>{{ $stats['floors'] }}</h3>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Căn hộ</p>
                    <h3>{{ $stats['apartments'] }}</h3>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Đang ở</p>
                    <h3>{{ $stats['occupied'] }}</h3>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Trống</p>
                    <h3>{{ $stats['vacant'] }}</h3>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Bảo trì</p>
                    <h3>{{ $stats['maintenance'] }}</h3>
                </div>
            </div>
        </article>

    </div>

    <article class="dashboard-card">
        <div class="card-header">
            <h2>Danh sách tầng</h2>
        </div>

        <div class="card-body">
            @if($floors->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tầng</th>
                            <th>Tên</th>
                            <th>Trạng thái</th>
                            <th>Số căn</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($floors as $floor)
                            <tr>
                                <td>{{ $floor->floor_number }}</td>
                                <td>{{ $floor->name ?? 'Tầng ' . $floor->floor_number }}</td>
                                <td>{{ ucfirst($floor->status) }}</td>
                                <td>{{ $floor->apartments_count }}</td>
                                <td>
                                    <a href="{{ route('admin.apartments.index', ['floor_id' => $floor->id]) }}" class="btn btn-light btn-sm">
                                        Xem căn
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    Chưa có tầng nào trong tòa nhà này.
                </div>
            @endif
        </div>
    </article>

</div>

@endsection
