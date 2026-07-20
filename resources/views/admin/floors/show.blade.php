@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Tầng')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
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
                <a href="{{ portal_route('floors.edit', $floor) }}" class="btn btn-light">
                    Sửa tầng
                </a>
                <a href="{{ portal_route('blocks.show', $floor->block_id) }}" class="btn btn-secondary">
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

                    {{-- Tiến độ khai báo phòng --}}
                    <div class="detail-row" style="flex-direction: column; align-items: flex-start; gap: 8px;">
                        <div style="display: flex; justify-content: space-between; width: 100%;">
                            <span class="detail-label">Căn hộ đã khai báo</span>
                            <span class="detail-value" style="font-weight: 700;">{{ $floor->apartments->count() }} /
                                {{ $floor->expected_apartments ?? '?' }} căn</span>
                        </div>
                        @if (($floor->expected_apartments ?? 0) > 0)
                            @php
                                $progress = min(
                                    100,
                                    round(($floor->apartments->count() / $floor->expected_apartments) * 100),
                                );
                            @endphp
                            <div class="progress-bar-container"
                                style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-top: 4px;">
                                <div class="progress-bar-fill"
                                    style="width: {{ $progress }}%; height: 100%; background: linear-gradient(90deg, #3b82f6, #1d4ed8); border-radius: 4px;">
                                </div>
                            </div>
                            <div
                                style="font-size: 12px; color: #64748b; font-weight: 600; text-align: right; width: 100%; margin-top: 2px;">
                                Đã hoàn thành {{ $progress }}% kế hoạch khai báo phòng
                            </div>
                        @endif
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
                    <h2>Danh sách căn hộ</h2>
                </div>
                <a href="{{ portal_route('apartments.create', ['floor_id' => $floor->id]) }}" class="btn btn-primary">
                    + Thêm căn hộ mới
                </a>
            </div>

            <div class="card-body" style="padding: 0; overflow-x: auto; border-radius: 8px;">
                @if ($floor->apartments->count() > 0)
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>Tên căn hộ</th>
                                <th>Loại căn hộ</th>
                                <th>Diện tích</th>
                                <th>Tên chủ hộ</th>
                                <th>Số điện thoại</th>
                                <th>Trạng thái</th>
                                <th style="text-align: right;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($floor->apartments as $apartment)
                                @php
                                    $area = $apartment->area;
                                    if ($area >= 100) {
                                        $type = '3BR Deluxe';
                                    } elseif ($area >= 70) {
                                        $type = '2BR Classic';
                                    } elseif ($area >= 50) {
                                        $type = '1BR Standard';
                                    } else {
                                        $type = 'Studio Plus';
                                    }

                                    $owner = $apartment->residents()->where('relationship', 'owner')->first() ?? $apartment->residents()->first();
                                    $ownerName = $owner ? ($owner->user->name ?? '-') : '-';
                                    $ownerPhone = $owner ? ($owner->user->phone ?? '-') : '-';
                                @endphp
                                <tr>
                                    <td style="font-weight: 700; color: #082b7a;">
                                        <a href="{{ portal_route('apartments.show', $apartment->id) }}" style="text-decoration: none; color: inherit;">
                                            {{ $apartment->apartment_number }}
                                        </a>
                                    </td>
                                    <td>{{ $type }}</td>
                                    <td>{{ number_format($apartment->area, 0, ',', '.') }} m²</td>
                                    <td>{{ $ownerName }}</td>
                                    <td>{{ $ownerPhone }}</td>
                                    <td>
                                        <span class="badge-status badge-status--{{ $apartment->status }}">
                                            @if ($apartment->status == 'occupied')
                                                Đang ở
                                            @elseif($apartment->status == 'vacant')
                                                Trống
                                            @else
                                                Bảo trì
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons" style="justify-content: flex-end;">
                                            <a href="{{ portal_route('apartments.show', $apartment->id) }}" class="btn-action btn-action--view" title="Chi tiết">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <a href="{{ portal_route('apartments.edit', $apartment->id) }}" class="btn-action btn-action--edit" title="Sửa">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px; color: #64748b; font-weight: 500;">
                                        Chưa có căn hộ nào được khai báo ở tầng này.
                                    </td>
                                </tr>
                            @endforelse
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
