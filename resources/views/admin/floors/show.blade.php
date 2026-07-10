@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Tầng')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')

    <div class="dashboard-content">

        <div class="page-header">

            <div>
                <h1 class="page-title">{{ $floor->name ?? 'Tầng ' . $floor->floor_number }}</h1>
                <p class="page-subtitle">
                    {{ $floor->block->name }} - Quản lý phòng & mặt bằng tầng
                </p>
            </div>

            <div class="page-header-actions">
                <a href="{{ route('admin.floors.edit', $floor) }}" class="btn btn-light">
                    Sửa tầng
                </a>
                <a href="{{ route('admin.blocks.show', $floor->block_id) }}" class="btn btn-secondary">
                    ← Tòa nhà
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
                        <span class="detail-label">Tòa nhà</span>
                        <span class="detail-value"
                            style="font-weight: 700; color: #082b7a;">{{ $floor->block->name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tên tầng / Số tầng</span>
                        <span class="detail-value"
                            style="font-weight: 600;">{{ $floor->name ?? 'Tầng ' . $floor->floor_number }} (Tầng
                            {{ $floor->floor_number }})</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Loại tầng</span>
                        <span>
                            @if ($floor->floor_type === 'resident')
                                <span class="badge badge-success">Cư dân</span>
                            @elseif($floor->floor_type === 'basement')
                                <span class="badge badge-danger" style="background-color: #fee2e2; color: #b91c1c;">Tầng
                                    hầm</span>
                            @elseif($floor->floor_type === 'commercial')
                                <span class="badge badge-warning" style="background-color: #e0f2fe; color: #0369a1;">Thương
                                    mại / Shophouse</span>
                            @else
                                <span class="badge badge-danger" style="background-color: #f1f5f9; color: #475569;">Kỹ thuật
                                    / Dịch vụ</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Trạng thái tầng</span>
                        <span>
                            @if (($floor->status ?? 'active') === 'active')
                                <span class="badge badge-success">Hoạt động</span>
                            @elseif(($floor->status ?? 'active') === 'maintenance')
                                <span class="badge badge-warning">Bảo trì</span>
                            @else
                                <span class="badge badge-danger">Ngưng hoạt động</span>
                            @endif
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Căn hộ đã khai báo</span>
                        <span class="detail-value" style="font-weight: 700;">{{ $floor->apartments->count() }} căn</span>
                    </div>

                    <div class="detail-row" style="flex-direction: column; align-items: flex-start; gap: 8px;">
                        <span class="detail-label">Ghi chú / Mô tả</span>
                        <span class="detail-value"
                            style="color: #475569; font-weight: 400; line-height: 1.6;">{{ $floor->description ?? 'Không có mô tả chi tiết' }}</span>
                    </div>
                </div>
            </article>

            {{-- Thống kê căn hộ thuộc tầng --}}
            <article class="dashboard-card">
                <div class="card-header">
                    <h2>Thống kê phòng</h2>
                </div>

                <div class="card-body stats-container">
                    <div class="premium-stat-card">
                        <span class="premium-stat-label">Tổng phòng</span>
                        <span class="premium-stat-value">{{ $stats['total'] }}</span>
                    </div>
                    <div class="premium-stat-card">
                        <span class="premium-stat-label">Tỷ lệ lấp đầy</span>
                        <span class="premium-stat-value" style="color: #082b7a;">{{ $stats['occupancy_rate'] }}%</span>
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

        {{-- Sơ đồ phòng dạng lưới --}}
        <article class="dashboard-card" style="margin-top: 28px;">
            <div class="card-header"
                style="border-bottom: 1px solid #f1f5f9; padding-bottom: 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h2>Sơ đồ căn hộ & Trạng thái lấp đầy</h2>
                    <p style="font-size: 13px; color: #64748b; margin-top: 4px; font-weight: 500;">
                        Trực quan hóa mặt bằng phòng trên tầng theo mã màu
                    </p>
                </div>
                <a href="{{ route('admin.apartments.create', ['floor_id' => $floor->id]) }}" class="btn btn-primary"
                    style="height: 40px; padding: 0 16px; border-radius: 10px; font-size: 13px;">
                    + Thêm căn hộ mới
                </a>
            </div>

            <div class="card-body" style="padding: 0; overflow: hidden;">
                @if ($floor->apartments->count() > 0)
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th style="padding-left: 24px; width: 30%;">Số hiệu căn</th>
                                <th style="width: 25%;">Số cư dân</th>
                                <th style="width: 20%;">Trạng thái</th>
                                <th style="text-align: right; padding-right: 24px; width: 25%;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($floor->apartments as $apartment)
                                <tr>
                                    <td style="padding-left: 24px; padding-top: 14px; padding-bottom: 14px;">
                                        <a href="{{ route('admin.apartments.show', $apartment->id) }}" style="font-weight: 700; color: #0b57d0; text-decoration: none; font-size: 16px;">
                                            Căn {{ $apartment->apartment_number }}
                                        </a>
                                        <div style="font-size: 12px; color: #64748b; margin-top: 4px; font-weight: 500;">
                                            Diện tích: {{ number_format($apartment->area, 2) }} m²
                                        </div>
                                    </td>
                                    <td style="font-weight: 600; color: #334155;">
                                        {{ $apartment->residents_count + $apartment->declared_members_count }} người
                                    </td>
                                    <td>
                                        @if ($apartment->status == 'occupied')
                                            <span class="badge badge-success">Đang ở</span>
                                        @elseif($apartment->status == 'vacant')
                                            <span class="badge badge-warning">Còn trống</span>
                                        @else
                                            <span class="badge badge-danger">Bảo trì</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; padding-right: 24px;">
                                        <div style="display: inline-flex; gap: 8px; align-items: center;">
                                            <a href="{{ route('admin.apartments.show', $apartment->id) }}" class="btn btn-light btn-sm"
                                                style="background: #eff6ff; color: #2563eb; border: none; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 34px; border-radius: 8px; padding: 0 12px; font-size: 12px; text-decoration: none;">
                                                Chi tiết
                                            </a>
                                            <a href="{{ route('admin.apartments.edit', $apartment->id) }}" class="btn btn-light btn-sm"
                                                style="background: #f1f5f9; color: #475569; border: none; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 34px; border-radius: 8px; padding: 0 12px; font-size: 12px; text-decoration: none;">
                                                Sửa
                                            </a>
                                            <form action="{{ route('admin.apartments.destroy', $apartment->id) }}" method="POST"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa căn hộ này?')" style="display: inline-flex;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light btn-sm"
                                                    style="background: #fee2e2; color: #b91c1c; border: none; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; height: 34px; border-radius: 8px; padding: 0 12px; font-size: 12px; cursor: pointer;">
                                                    Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state"
                        style="padding: 50px; text-align: center; color: #64748b; font-size: 15px; font-weight: 500;">
                        Chưa có căn hộ nào được khai báo ở tầng này.
                    </div>
                @endif
            </div>
        </article>

    </div>

@endsection

@push('styles')
    @vite(['resources/css/pages/admin/floors/show.css'])
@endpush
