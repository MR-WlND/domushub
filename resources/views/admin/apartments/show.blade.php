@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Căn hộ ' . $apartment->apartment_number)
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')
<div class="dashboard-content">

    {{-- Breadcrumb Navigation --}}
    <nav class="breadcrumb-nav">
        <a href="{{ route('admin.dashboard') }}">Trang chủ</a>
        <span class="divider">/</span>
        <a href="{{ route('admin.apartments.index') }}">Căn hộ</a>
        <span class="divider">/</span>
        <span class="current">Căn hộ {{ $apartment->apartment_number }}</span>
    </nav>

    {{-- Header --}}
    <div class="page-header" style="margin-top: 15px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <h1 class="page-title" style="margin-bottom: 0;">Căn hộ {{ $apartment->apartment_number }}</h1>
                <span class="badge-status badge-status--{{ $apartment->status }}">
                    @if($apartment->status == 'occupied')
                        <span class="dot dot-success"></span> Đang ở
                    @elseif($apartment->status == 'vacant')
                        <span class="dot dot-warning"></span> Trống
                    @else
                        <span class="dot dot-danger"></span> Bảo trì
                    @endif
                </span>
            </div>
            <p class="page-subtitle" style="margin-top: 6px;">
                {{ $apartment->floor->block->name }} — {{ $apartment->floor->name ?? 'Tầng ' . $apartment->floor->floor_number }}
            </p>
        </div>

        <div class="page-header-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('admin.apartments.edit', $apartment) }}" class="btn btn-light" style="display: flex; align-items: center; gap: 8px; border: 1px solid #cbd5e1; background: white; padding: 10px 16px; border-radius: 8px; font-weight: 600; text-decoration: none; color: #334155; font-size: 14px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                Chỉnh sửa
            </a>
            <form action="{{ route('admin.apartments.destroy', $apartment) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa căn hộ này?')" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="display: flex; align-items: center; gap: 8px; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; padding: 10px 16px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Xóa căn hộ
                </button>
            </form>
            <a href="{{ route('admin.apartments.index') }}" class="btn btn-secondary" style="display: flex; align-items: center; justify-content: center; height: 38px; padding: 0 16px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px; background-color: #f1f5f9; color: #475569; border: 1px solid #e2e8f0;">
                Quay lại
            </a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success" style="margin-bottom: 24px; padding: 16px 20px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; border-radius: 12px; font-weight: 500;">
            {{ $message }}
        </div>
    @endif

    <div class="detail-grid">
        {{-- Column Trái: Thông tin chung --}}
        <div class="detail-col-info">
            <article class="dashboard-card shadow-sm border-light" style="padding: 24px; margin-bottom: 0;">
                <div class="card-header-custom">
                    <div class="header-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#0b57d0" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <h3>Thông tin căn hộ</h3>
                </div>

                <div class="card-body-custom">
                    {{-- Thẻ Diện Tích Nổi Bật --}}
                    <div class="area-highlight">
                        <div class="area-value">
                            <span>{{ number_format($apartment->area, 2) }}</span>
                            <span class="unit">m²</span>
                        </div>
                        <div class="area-label">Diện tích sử dụng</div>
                    </div>

                    <div class="detail-list">
                        <div class="detail-item">
                            <span class="item-label">Số hiệu phòng</span>
                            <span class="item-value font-semibold">{{ $apartment->apartment_number }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="item-label">Tòa nhà (Block)</span>
                            <span class="item-value font-semibold text-primary">{{ $apartment->floor->block->name }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="item-label">Vị trí tầng</span>
                            <span class="item-value">{{ $apartment->floor->name ?? 'Tầng ' . $apartment->floor->floor_number }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="item-label">Định dạng trạng thái</span>
                            <span class="item-value">
                                @if($apartment->status == 'occupied')
                                    <span class="badge badge-success-filled">Đang có người ở</span>
                                @elseif($apartment->status == 'vacant')
                                    <span class="badge badge-warning-filled">Đang trống</span>
                                @else
                                    <span class="badge badge-danger-filled">Bảo trì hệ thống</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="description-section">
                        <div class="desc-title">Ghi chú & Mô tả</div>
                        <p class="desc-content">
                            {{ $apartment->description ?? 'Căn hộ này chưa được nhập thông tin mô tả hoặc ghi chú chi tiết.' }}
                        </p>
                    </div>
                </div>
            </article>
        </div>

        {{-- Column Phải: Danh sách cư dân --}}
        <div class="detail-col-residents">
            <article class="dashboard-card shadow-sm border-light" style="padding: 24px; min-height: 100%;">
                <div class="card-header-custom" style="justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="header-icon" style="background-color: #f0fdf4;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#15803d" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <h3>Danh sách cư dân sinh sống</h3>
                    </div>
                    <span class="count-pill">{{ $apartment->residents->count() }} cư dân</span>
                </div>

                <div class="card-body-custom">
                    @if($apartment->residents->count() > 0)
                        <div class="table-container">
                            <table class="premium-table">
                                <thead>
                                    <tr>
                                        <th>Họ & Tên</th>
                                        <th>Thông tin liên hệ</th>
                                        <th>Vai trò quan hệ</th>
                                        <th>Trạng thái hoạt động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($apartment->residents as $resident)
                                        <tr>
                                            <td>
                                                <div class="resident-profile">
                                                    <div class="avatar-circle">
                                                        {{ strtoupper(substr($resident->name, 0, 1)) }}
                                                    </div>
                                                    <div class="resident-name-info">
                                                        <div class="name">{{ $resident->name }}</div>
                                                        <div class="sub">Thành viên căn hộ</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="contact-info">
                                                    <div class="email" title="{{ $resident->email }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                                        {{ $resident->email }}
                                                    </div>
                                                    <div class="phone">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                                        {{ $resident->phone ?? '-' }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if(($resident->relationship ?? '') === 'owner')
                                                    <span class="badge-relationship owner">Chủ hộ</span>
                                                @elseif(($resident->relationship ?? '') === 'tenant')
                                                    <span class="badge-relationship tenant">Người thuê</span>
                                                @else
                                                    <span class="badge-relationship family">Thành viên</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(($resident->status ?? 'active') == 'active')
                                                    <span class="status-indicator-badge active">
                                                        <span class="indicator-dot"></span>
                                                        Đang hoạt động
                                                    </span>
                                                @else
                                                    <span class="status-indicator-badge inactive">
                                                        <span class="indicator-dot"></span>
                                                        Tạm khóa
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state-card">
                            <div class="empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                            </div>
                            <h4>Chưa có cư dân sinh sống</h4>
                            <p>Căn hộ này hiện đang trống hoặc chưa được liên kết với bất kỳ tài khoản cư dân nào trên hệ thống.</p>
                            <div class="empty-actions">
                                <a href="{{ route('admin.invitations.index') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; background-color: #0b57d0; color: white; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 13px; transition: background-color 0.2s ease;">
                                    + Tạo mã mời cư dân
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </article>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Breadcrumb styles */
    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }
    .breadcrumb-nav a {
        color: #0b57d0;
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .breadcrumb-nav a:hover {
        color: #00236f;
        text-decoration: underline;
    }
    .breadcrumb-nav .divider {
        color: #cbd5e1;
        user-select: none;
    }
    .breadcrumb-nav .current {
        color: #334155;
    }

    /* Layout grid */
    .detail-grid {
        display: grid;
        grid-template-columns: 38% 60%;
        gap: 2%;
        margin-top: 24px;
    }

    @media (max-width: 1024px) {
        .detail-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }
    }

    /* Card Header Customization */
    .card-header-custom {
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 16px;
        margin-bottom: 20px;
    }
    .card-header-custom h3 {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .header-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background-color: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Area Highlight Block */
    .area-highlight {
        background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }
    .area-highlight .area-value {
        font-size: 36px;
        font-weight: 800;
        color: #082b7a;
        line-height: 1;
        display: flex;
        align-items: baseline;
        justify-content: center;
        gap: 4px;
    }
    .area-highlight .area-value .unit {
        font-size: 16px;
        font-weight: 700;
        color: #475569;
    }
    .area-highlight .area-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-top: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Detail List styles */
    .detail-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .detail-item:last-child {
        border-bottom: none;
    }
    .item-label {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }
    .item-value {
        font-size: 14px;
        color: #0f172a;
        font-weight: 600;
    }
    .font-semibold {
        font-weight: 700;
    }
    .text-primary {
        color: #082b7a;
    }

    /* Description */
    .description-section {
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }
    .description-section .desc-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .description-section .desc-content {
        font-size: 14px;
        color: #475569;
        line-height: 1.6;
        margin: 0;
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px;
        border-left: 3px solid #cbd5e1;
    }

    /* Count Pill */
    .count-pill {
        background: #eff6ff;
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 9999px;
    }

    /* Badges */
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 9999px;
    }
    .badge-status--occupied {
        background-color: #dcfce7;
        color: #15803d;
    }
    .badge-status--vacant {
        background-color: #fef3c7;
        color: #b45309;
    }
    .badge-status--maintenance {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .dot-success { background-color: #16a34a; }
    .dot-warning { background-color: #d97706; }
    .dot-danger { background-color: #dc2626; }

    .badge-success-filled {
        background: #16a34a;
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
    }
    .badge-warning-filled {
        background: #d97706;
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
    }
    .badge-danger-filled {
        background: #dc2626;
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
    }

    /* Table styles */
    .table-container {
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .premium-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }
    .premium-table th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 700;
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .premium-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: #334155;
    }
    .premium-table tr:last-child td {
        border-bottom: none;
    }
    .premium-table tr:hover td {
        background-color: #f8fafc;
    }

    /* Resident Profile in Table */
    .resident-profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #eff6ff;
        color: #0b57d0;
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: 1px solid #bfdbfe;
    }
    .resident-name-info .name {
        font-weight: 700;
        color: #0f172a;
    }
    .resident-name-info .sub {
        font-size: 11px;
        color: #64748b;
        margin-top: 1px;
    }

    /* Contact Info cell */
    .contact-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
        font-size: 13px;
    }
    .contact-info div {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #475569;
    }
    .contact-info .email {
        color: #0b57d0;
        font-weight: 500;
    }

    /* Relationship Badge */
    .badge-relationship {
        display: inline-flex;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
    }
    .badge-relationship.owner {
        background-color: #ecfdf5;
        color: #047857;
    }
    .badge-relationship.tenant {
        background-color: #eff6ff;
        color: #1d4ed8;
    }
    .badge-relationship.family {
        background-color: #f5f3ff;
        color: #6d28d9;
    }

    /* Status Indicator Badge */
    .status-indicator-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 600;
    }
    .status-indicator-badge .indicator-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }
    .status-indicator-badge.active {
        color: #16a34a;
    }
    .status-indicator-badge.active .indicator-dot {
        background-color: #16a34a;
        box-shadow: 0 0 6px #16a34a;
    }
    .status-indicator-badge.inactive {
        color: #94a3b8;
    }
    .status-indicator-badge.inactive .indicator-dot {
        background-color: #94a3b8;
    }

    /* Empty state */
    .empty-state-card {
        padding: 40px 20px;
        text-align: center;
        background: #f8fafc;
        border-radius: 12px;
        border: 2px dashed #cbd5e1;
        margin: 10px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .empty-state-card .empty-icon {
        margin-bottom: 12px;
    }
    .empty-state-card h4 {
        font-size: 16px;
        font-weight: 700;
        color: #334155;
        margin: 0 0 6px 0;
    }
    .empty-state-card p {
        font-size: 13px;
        color: #64748b;
        margin: 0 0 16px 0;
        max-width: 320px;
        text-align: center;
        line-height: 1.5;
    }
</style>
@endpush
