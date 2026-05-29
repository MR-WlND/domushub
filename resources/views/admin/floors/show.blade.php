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
            <a href="{{ route('admin.buildings.show', $floor->block_id) }}" class="btn btn-secondary">
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
                    <span class="detail-value" style="font-weight: 700; color: #082b7a;">{{ $floor->block->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tên tầng / Số tầng</span>
                    <span class="detail-value" style="font-weight: 600;">{{ $floor->name ?? 'Tầng ' . $floor->floor_number }} (Tầng {{ $floor->floor_number }})</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Loại tầng</span>
                    <span>
                        @if($floor->floor_type === 'resident')
                            <span class="badge badge-success">Cư dân</span>
                        @elseif($floor->floor_type === 'basement')
                            <span class="badge badge-danger" style="background-color: #fee2e2; color: #b91c1c;">Tầng hầm</span>
                        @elseif($floor->floor_type === 'commercial')
                            <span class="badge badge-warning" style="background-color: #e0f2fe; color: #0369a1;">Thương mại / Shophouse</span>
                        @else
                            <span class="badge badge-danger" style="background-color: #f1f5f9; color: #475569;">Kỹ thuật / Dịch vụ</span>
                        @endif
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Trạng thái tầng</span>
                    <span>
                        @if(($floor->status ?? 'active') === 'active')
                            <span class="badge badge-success">Hoạt động</span>
                        @elseif(($floor->status ?? 'active') === 'maintenance')
                            <span class="badge badge-warning">Bảo trì</span>
                        @else
                            <span class="badge badge-danger">Ngưng hoạt động</span>
                        @endif
                    </span>
                </div>
                
                {{-- Tiến độ khai báo phòng --}}
                <div class="detail-row" style="flex-direction: column; align-items: flex-start; gap: 8px;">
                    <div style="display: flex; justify-content: space-between; width: 100%;">
                        <span class="detail-label">Căn hộ đã khai báo</span>
                        <span class="detail-value" style="font-weight: 700;">{{ $floor->apartments->count() }} / {{ $floor->expected_apartments ?? '?' }} căn</span>
                    </div>
                    @if(($floor->expected_apartments ?? 0) > 0)
                        @php
                            $progress = min(100, round(($floor->apartments->count() / $floor->expected_apartments) * 100));
                        @endphp
                        <div class="progress-bar-container" style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-top: 4px;">
                            <div class="progress-bar-fill" style="width: {{ $progress }}%; height: 100%; background: linear-gradient(90deg, #3b82f6, #1d4ed8); border-radius: 4px;"></div>
                        </div>
                        <div style="font-size: 12px; color: #64748b; font-weight: 600; text-align: right; width: 100%; margin-top: 2px;">
                            Đã hoàn thành {{ $progress }}% kế hoạch khai báo phòng
                        </div>
                    @endif
                </div>

                <div class="detail-row" style="flex-direction: column; align-items: flex-start; gap: 8px;">
                    <span class="detail-label">Ghi chú / Mô tả</span>
                    <span class="detail-value" style="color: #475569; font-weight: 400; line-height: 1.6;">{{ $floor->description ?? 'Không có mô tả chi tiết' }}</span>
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
        <div class="card-header" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <h2>Sơ đồ căn hộ & Trạng thái lấp đầy</h2>
                <p style="font-size: 13px; color: #64748b; margin-top: 4px; font-weight: 500;">
                    Trực quan hóa mặt bằng phòng trên tầng theo mã màu
                </p>
            </div>
            <a href="{{ route('admin.apartments.create', ['floor_id' => $floor->id]) }}" class="btn btn-primary" style="height: 40px; padding: 0 16px; border-radius: 10px; font-size: 13px;">
                + Thêm căn hộ mới
            </a>
        </div>

        <div class="card-body">
            @if($floor->apartments->count() > 0)
                <div class="apartment-grid-map">
                    @foreach($floor->apartments as $apartment)
                        <div class="apartment-map-card apartment-map-card--{{ $apartment->status }}">
                            <div class="map-card-header">
                                <span class="map-card-number">{{ $apartment->apartment_number }}</span>
                                <span class="map-card-badge map-card-badge--{{ $apartment->status }}">
                                    @if($apartment->status == 'occupied')
                                        Đang ở
                                    @elseif($apartment->status == 'vacant')
                                        Trống
                                    @else
                                        Bảo trì
                                    @endif
                                </span>
                            </div>
                            
                            <div class="map-card-body">
                                <div class="map-card-info">
                                    <span class="info-label">Diện tích</span>
                                    <span class="info-value">{{ $apartment->area }} m²</span>
                                </div>
                                <div class="map-card-info">
                                    <span class="info-label">Cư dân</span>
                                    <span class="info-value">{{ $apartment->residents_count }} người</span>
                                </div>
                            </div>
                            
                            <div class="map-card-footer">
                                <a href="{{ route('admin.apartments.show', $apartment->id) }}" class="map-card-btn map-card-btn--view">
                                    Chi tiết
                                </a>
                                <a href="{{ route('admin.apartments.edit', $apartment->id) }}" class="map-card-btn map-card-btn--edit">
                                    Sửa
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="padding: 50px; text-align: center; color: #64748b; font-size: 15px; font-weight: 500;">
                    Chưa có căn hộ nào được khai báo ở tầng này.
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

    /* Apartment Grid Map styles */
    .apartment-grid-map {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        padding: 10px 0;
    }
    .apartment-map-card {
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        transition: all 0.25s ease;
        position: relative;
        background: white;
    }
    .apartment-map-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
    }
    
    /* Specific Colors for status cards */
    .apartment-map-card--occupied {
        border-color: #bbf7d0;
        background: #f0fdf4;
    }
    .apartment-map-card--vacant {
        border-color: #fef08a;
        background: #fefdf0;
    }
    .apartment-map-card--maintenance {
        border-color: #fca5a5;
        background: #fdf2f2;
    }

    .map-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .map-card-number {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
    }
    .map-card-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 9999px;
    }
    .map-card-badge--occupied {
        background: #dcfce7;
        color: #15803d;
    }
    .map-card-badge--vacant {
        background: #fef3c7;
        color: #b45309;
    }
    .map-card-badge--maintenance {
        background: #fee2e2;
        color: #b91c1c;
    }

    .map-card-body {
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 13px;
    }
    .map-card-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px dashed rgba(226, 232, 240, 0.6);
        padding-bottom: 6px;
    }
    .map-card-info:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    .info-label {
        color: #64748b;
        font-weight: 500;
    }
    .info-value {
        color: #0f172a;
        font-weight: 700;
    }

    .map-card-footer {
        display: flex;
        gap: 8px;
        margin-top: 4px;
    }
    .map-card-btn {
        flex: 1;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .map-card-btn--view {
        background: #eff6ff;
        color: #2563eb;
    }
    .map-card-btn--view:hover {
        background: #dbeafe;
    }
    .map-card-btn--edit {
        background: #f1f5f9;
        color: #475569;
    }
    .map-card-btn--edit:hover {
        background: #e2e8f0;
    }
</style>
@endpush
