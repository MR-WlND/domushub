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

        {{-- Thông tin chung --}}
        <article class="dashboard-card">
            <div class="card-header">
                <h2>Thông tin chung</h2>
            </div>

            <div class="card-body">
                <div class="detail-row">
                    <span class="detail-label">Mã tòa nhà</span>
                    <span class="detail-value">{{ $block->code ?? 'Chưa có' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Trạng thái</span>
                    <span>
                        @if(($block->status ?? 'active') === 'active')
                            <span class="badge badge-success">Hoạt động</span>
                        @elseif(($block->status ?? 'active') === 'maintenance')
                            <span class="badge badge-warning">Bảo trì</span>
                        @else
                            <span class="badge badge-danger">Ngưng hoạt động</span>
                        @endif
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Số tầng</span>
                    <span class="detail-value">{{ $block->number_of_floors ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tổng căn hộ</span>
                    <span class="detail-value">{{ $block->total_apartments ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Người quản lý</span>
                    <span class="detail-value">{{ $block->manager_name ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Liên hệ</span>
                    <span class="detail-value">{{ $block->manager_contact ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="detail-row" style="flex-direction: column; align-items: flex-start; gap: 8px;">
                    <span class="detail-label">Mô tả</span>
                    <span class="detail-value" style="color: #475569; font-weight: 400; line-height: 1.6;">{{ $block->description ?? 'Không có mô tả' }}</span>
                </div>
            </div>
        </article>

        {{-- Thống kê --}}
        <article class="dashboard-card">
            <div class="card-header">
                <h2>Thống kê</h2>
            </div>

            <div class="card-body stats-container">
                <div class="premium-stat-card">
                    <span class="premium-stat-label">Tầng</span>
                    <span class="premium-stat-value">{{ $stats['floors'] }}</span>
                </div>
                <div class="premium-stat-card">
                    <span class="premium-stat-label">Căn hộ</span>
                    <span class="premium-stat-value">{{ $stats['apartments'] }}</span>
                </div>
                <div class="premium-stat-card">
                    <span class="premium-stat-label">Đang ở</span>
                    <span class="premium-stat-value" style="color: #16a34a;">{{ $stats['occupied'] }}</span>
                </div>
                <div class="premium-stat-card">
                    <span class="premium-stat-label">Trống</span>
                    <span class="premium-stat-value" style="color: #d97706;">{{ $stats['vacant'] }}</span>
                </div>
                <div class="premium-stat-card" style="grid-column: span 2;">
                    <span class="premium-stat-label">Bảo trì</span>
                    <span class="premium-stat-value" style="color: #dc2626;">{{ $stats['maintenance'] }}</span>
                </div>
            </div>
        </article>

    </div>

    {{-- Danh sách tầng --}}
    <article class="dashboard-card">
        <div class="card-header" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 18px;">
            <h2>Danh sách tầng</h2>
        </div>

        <div class="card-body" style="padding: 0; overflow: hidden; border-radius: 0 0 20px 20px;">
            @if($floors->count() > 0)
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="padding-left: 24px;">Số tầng</th>
                            <th>Tên tầng</th>
                            <th>Trạng thái</th>
                            <th>Số căn hộ</th>
                            <th style="text-align: right; padding-right: 24px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($floors as $floor)
                            <tr>
                                <td style="font-weight: 700; color: #082b7a; padding-left: 24px;">{{ $floor->floor_number }}</td>
                                <td style="font-weight: 600;">{{ $floor->name ?? 'Tầng ' . $floor->floor_number }}</td>
                                <td>
                                    @if(($floor->status ?? 'active') === 'active')
                                        <span class="badge badge-success">Hoạt động</span>
                                    @elseif(($floor->status ?? 'active') === 'maintenance')
                                        <span class="badge badge-warning">Bảo trì</span>
                                    @else
                                        <span class="badge badge-danger">Ngưng hoạt động</span>
                                    @endif
                                </td>
                                <td style="font-weight: 600;">{{ $floor->apartments_count }} căn hộ</td>
                                <td style="text-align: right; padding-right: 24px;">
                                    <a href="{{ route('admin.floors.show', $floor->id) }}" class="btn btn-light btn-sm" style="background: #eff6ff; color: #2563eb; border: none; font-weight: 700;">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state" style="padding: 40px; text-align: center; color: #64748b;">
                    Chưa có tầng nào trong tòa nhà này.
                </div>
            @endif
        </div>
    </article>

</div>

@endsection

@push('styles')
<style>
    /* Premium Detail Rows for General Info */
    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 15px;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-weight: 600;
        color: #64748b;
    }
    .detail-value {
        color: #0f172a;
        font-weight: 500;
    }
    
    /* Stats Grid Tuning */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }
    .premium-stat-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: all 0.2s ease;
    }
    .premium-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.05);
        border-color: #cbd5e1;
    }
    .premium-stat-label {
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .premium-stat-value {
        font-size: 32px;
        font-weight: 800;
        color: #082b7a;
        line-height: 1;
    }

    /* Premium Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 13px;
        font-weight: 700;
    }
    .badge-success {
        background-color: #dcfce7;
        color: #15803d;
    }
    .badge-warning {
        background-color: #fef3c7;
        color: #b45309;
    }
    .badge-danger {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    
    /* Premium Table styling */
    .premium-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }
    .premium-table th {
        padding: 16px 20px;
        font-size: 14px;
        font-weight: 700;
        color: #475569;
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
    }
    .premium-table td {
        padding: 16px 20px;
        font-size: 15px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .premium-table tr:hover td {
        background-color: #f8fafc;
    }
    
    /* Buttons Custom styling for table actions */
    .btn-sm {
        height: 38px;
        padding: 0 16px;
        border-radius: 10px;
        font-size: 13px;
    }
</style>
@endpush
