@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Căn hộ ' . $apartment->apartment_number)
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/apartments/show.css'])
@endpush

@section('content')
<div class="apartments-page">

    {{-- Breadcrumb Navigation --}}
    <nav class="breadcrumb-nav">
        <a href="{{ portal_route('dashboard') }}" style="display: inline-flex; align-items: center; gap: 4px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            Infrastructure
        </a>
        <span class="divider">›</span>
        <a href="{{ portal_route('blocks.show', $apartment->floor->block) }}">{{ $apartment->floor->block->name }}</a>
        <span class="divider">›</span>
        <a href="{{ portal_route('floors.show', $apartment->floor) }}">{{ $apartment->floor->name ?? 'Tầng ' . $apartment->floor->floor_number }}</a>
        <span class="divider">›</span>
        <span class="current" style="font-weight: 700; color: #0f172a;">Apartment {{ $apartment->apartment_number }}</span>
    </nav>

    {{-- Header --}}
    <div class="apartments-page__header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-top: 16px; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <h1 style="font-size: 24px; font-weight: 700; color: #0f172a; margin: 0;">Chi tiết Căn hộ {{ $apartment->apartment_number }}</h1>
                <span class="badge-status-pill badge-status-pill--{{ $apartment->status }}">
                    @if ($apartment->status == 'occupied')
                        ĐANG Ở
                    @elseif($apartment->status == 'vacant')
                        TRỐNG
                    @else
                        BẢO TRÌ
                    @endif
                </span>
            </div>
            <p class="apartments-page__subtitle" style="margin-top: 8px; color: #64748b; font-size: 14px;">
                Cập nhật lần cuối: {{ $apartment->updated_at->format('H:i - d/m/Y') }}
            </p>
        </div>

        <div class="apartments-page__actions" style="display: flex; gap: 12px;">
            <a href="#" class="btn-outline-custom">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Lịch sử cư dân
            </a>
            <a href="{{ portal_route('apartments.edit', $apartment) }}" class="btn-primary-custom">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Chỉnh sửa thông tin
            </a>
        </div>
    </div>

    {{-- Success/Error Alerts --}}
    @if ($message = Session::get('success'))
        <div class="apartments-alert apartments-alert--success" style="margin-bottom: 24px;">{{ $message }}</div>
    @endif
    @if ($message = Session::get('error'))
        <div class="apartments-alert apartments-alert--danger" style="margin-bottom: 24px;">{{ $message }}</div>
    @endif

    {{-- Summary Cards --}}
    <div class="summary-cards-grid">
        <div class="summary-card">
            <div class="summary-card-header">
                <svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                </svg>
                <span class="summary-label">Diện tích</span>
            </div>
            <div class="summary-value-wrapper">
                <h3 class="summary-value">{{ number_format($apartment->area, 0, ',', '.') }} m²</h3>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-card-header">
                <svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="summary-label">Loại căn hộ</span>
            </div>
            <div class="summary-value-wrapper">
                <h3 class="summary-value">{{ $apartment->apartmentType->name ?? '2 Phòng ngủ' }}</h3>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-card-header">
                <svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="summary-label">Số cư dân</span>
            </div>
            <div class="summary-value-wrapper">
                <h3 class="summary-value">{{ sprintf("%02d", $apartment->residents->count() + ($apartment->declaredMembers->count() ?? 0)) }} Người</h3>
            </div>
        </div>

        <div class="summary-card summary-card--debt">
            <div class="summary-card-header" style="color: #dc2626;">
                <svg class="summary-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="summary-label">Công nợ</span>
            </div>
            <div class="summary-value-wrapper">
                {{-- Giả lập số dư công nợ nếu không có hàm tính tổng hóa đơn --}}
                @php
                    $totalDebt = 1250000;
                @endphp
                <h3 class="summary-value" style="color: #dc2626;">{{ number_format($totalDebt, 0, ',', '.') }} VNĐ</h3>
            </div>
        </div>
    </div>

    {{-- Tabs Section --}}
    <div class="tabs-container dashboard-card shadow-sm border-light">
        <div class="tabs-header">
            <button class="tab-button active" onclick="openTab(event, 'tab-info')">Thông tin chung</button>
            <button class="tab-button" onclick="openTab(event, 'tab-residents')">Cư dân</button>
            <button class="tab-button" onclick="openTab(event, 'tab-invoices')">Hóa đơn</button>
            <button class="tab-button" onclick="openTab(event, 'tab-vehicles')">Phương tiện</button>
        </div>

        <div class="tabs-content">
            {{-- Tab 1: Thông tin chung --}}
            <div id="tab-info" class="tab-pane active" style="display: block;">
                <div class="info-grid">
                    <div class="info-col-left">
                        <div class="info-section">
                            <div class="section-title-wrapper">
                                <div class="section-accent-line"></div>
                                <h4 class="info-section-title">Chi tiết kỹ thuật</h4>
                            </div>
                            <div class="tech-detail-list">
                                <div class="tech-detail-row">
                                    <span class="tech-label">Số phòng khách:</span>
                                    <span class="tech-value">01</span>
                                </div>
                                <div class="tech-detail-row">
                                    <span class="tech-label">Số phòng ngủ:</span>
                                    <span class="tech-value">02</span>
                                </div>
                                <div class="tech-detail-row">
                                    <span class="tech-label">Hướng ban công:</span>
                                    <span class="tech-value">Hướng Đông Nam</span>
                                </div>
                                <div class="tech-detail-row">
                                    <span class="tech-label">Tình trạng nội thất:</span>
                                    <span class="tech-value">Full nội thất cơ bản</span>
                                </div>
                            </div>
                        </div>

                        <div class="info-section" style="margin-top: 32px;">
                            <div class="section-title-wrapper">
                                <div class="section-accent-line"></div>
                                <h4 class="info-section-title">Nội thất kèm theo</h4>
                            </div>
                            <ul class="furniture-list">
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Hệ thống máy lạnh Daikin (03 cái)
                                </li>
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Bếp từ và máy hút mùi Hafele
                                </li>
                                <li>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Sàn gỗ công nghiệp cao cấp
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="info-col-right">
                        <div class="info-section">
                            <div class="section-title-wrapper">
                                <div class="section-accent-line"></div>
                                <h4 class="info-section-title">Ghi chú quản lý</h4>
                            </div>
                            <div class="management-note">
                                {{ $apartment->description ?? '"Cư dân có phản hồi về áp lực nước tại nhà vệ sinh phụ vào ngày 15/10. Đã cử kỹ thuật kiểm tra và xử lý xong. Khách thuê hiện tại dự kiến gia hạn hợp đồng vào tháng 12/2023."' }}
                            </div>
                            <button class="btn-dashed-custom">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Thêm ghi chú mới
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 2: Cư dân --}}
            <div id="tab-residents" class="tab-pane" style="display: none;">
                <div class="table-responsive">
                    <table class="clean-table">
                        <thead>
                            <tr>
                                <th>Họ và tên</th>
                                <th>Quan hệ</th>
                                <th>Số điện thoại</th>
                                <th>Ngày bắt đầu ở</th>
                                <th style="text-align: right;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apartment->residents as $resident)
                                @php
                                    $user = $resident->user;
                                    $residentName = $user->name ?? 'Chưa có tên';
                                    $residentPhone = $user->phone ?? '—';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="td-profile">
                                            <div class="td-avatar" style="background: #1e3a8a; color: white;">
                                                {{ mb_strtoupper(mb_substr($residentName, 0, 2)) }}
                                            </div>
                                            <div class="td-name-info">
                                                <div class="td-name">{{ $residentName }}</div>
                                                <div class="td-sub">{{ $resident->relationship == 'owner' ? 'Chủ hộ' : 'Thành viên' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $resident->relationship == 'owner' ? 'Chủ hộ' : 'Thành viên' }}</td>
                                    <td>{{ $residentPhone }}</td>
                                    <td>{{ $resident->created_at ? $resident->created_at->format('d/m/Y') : '12/05/2021' }}</td>
                                    <td style="text-align: right;">
                                        <a href="#" class="td-action-link">Chi tiết</a>
                                    </td>
                                </tr>
                            @empty
                                {{-- Mock Data để hiển thị theo mockup --}}
                                <tr>
                                    <td>
                                        <div class="td-profile">
                                            <div class="td-avatar" style="background: #1e3a8a; color: white;">NT</div>
                                            <div class="td-name-info">
                                                <div class="td-name">Nguyễn Văn Thành</div>
                                                <div class="td-sub">Chủ hộ</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Chủ hộ</td>
                                    <td>0901 234 567</td>
                                    <td>12/05/2021</td>
                                    <td style="text-align: right;"><a href="#" class="td-action-link">Chi tiết</a></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="td-profile">
                                            <div class="td-avatar" style="background: #34d399; color: white;">LH</div>
                                            <div class="td-name-info">
                                                <div class="td-name">Lê Thị Hoa</div>
                                                <div class="td-sub">Vợ</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Vợ</td>
                                    <td>0901 888 999</td>
                                    <td>12/05/2021</td>
                                    <td style="text-align: right;"><a href="#" class="td-action-link">Chi tiết</a></td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 3: Hóa đơn --}}
            <div id="tab-invoices" class="tab-pane" style="display: none;">
                <div class="table-responsive">
                    <table class="clean-table">
                        <thead>
                            <tr>
                                <th>Kỳ hóa đơn</th>
                                <th>Tổng số tiền</th>
                                <th>Trạng thái</th>
                                <th>Ngày thanh toán</th>
                                <th style="text-align: center;">Chứng từ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apartment->invoices ?? [] as $invoice)
                                <tr>
                                    <td style="font-weight: 600; color: #334155;">Tháng {{ \Carbon\Carbon::parse($invoice->due_date)->format('m/Y') }}</td>
                                    <td style="font-weight: 700; color: {{ $invoice->status == 'unpaid' ? '#dc2626' : '#0f172a' }};">{{ number_format($invoice->total_amount, 0, ',', '.') }} VNĐ</td>
                                    <td>
                                        @if($invoice->status == 'unpaid')
                                            <span class="badge-status-pill badge-status-pill--danger">Chưa thanh toán</span>
                                        @else
                                            <span class="badge-status-pill badge-status-pill--success">Đã thanh toán</span>
                                        @endif
                                    </td>
                                    <td>{{ $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('d/m/Y') : '--' }}</td>
                                    <td style="text-align: center;">
                                        <a href="#" style="color: #0b57d0;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                {{-- Mock Data --}}
                                <tr>
                                    <td style="font-weight: 600; color: #334155;">Tháng 10/2023</td>
                                    <td style="font-weight: 700; color: #dc2626;">1.250.000 VNĐ</td>
                                    <td><span class="badge-status-pill badge-status-pill--danger">Chưa thanh toán</span></td>
                                    <td>--</td>
                                    <td style="text-align: center;">
                                        <a href="#" style="color: #0b57d0;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-weight: 600; color: #334155;">Tháng 09/2023</td>
                                    <td style="font-weight: 700; color: #0f172a;">1.420.000 VNĐ</td>
                                    <td><span class="badge-status-pill badge-status-pill--success" style="background: #a7f3d0; color: #047857;">Đã thanh toán</span></td>
                                    <td>05/10/2023</td>
                                    <td style="text-align: center;">
                                        <a href="#" style="color: #0b57d0;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg></a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 4: Phương tiện --}}
            <div id="tab-vehicles" class="tab-pane" style="display: none;">
                <div class="vehicles-grid">
                    @forelse($apartment->vehicles ?? [] as $vehicle)
                        {{-- Real Data would go here --}}
                    @empty
                        {{-- Mock Data --}}
                        <div class="vehicle-card">
                            <div class="vehicle-card-header">
                                <div class="vehicle-info-block">
                                    <div class="vehicle-icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#0b57d0" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h8l2 4v7a1 1 0 01-1 1h-1a1 1 0 01-1-1v-1H9v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-7l2-4zm0 0v-1a2 2 0 012-2h4a2 2 0 012 2v1m-9 4h10" />
                                        </svg>
                                    </div>
                                    <div class="vehicle-info">
                                        <h4 class="vehicle-plate">29A-123.45</h4>
                                        <p class="vehicle-name">VinFast Lux A2.0</p>
                                    </div>
                                </div>
                                <span class="badge-status-pill badge-status-pill--success" style="background: #a7f3d0; color: #047857; margin-top: 4px;">Active</span>
                            </div>
                            <div class="vehicle-card-footer">
                                <span class="vehicle-location">Vị trí đỗ: P1-B12</span>
                                <span class="vehicle-type">Ô tô</span>
                            </div>
                        </div>

                        <div class="vehicle-card">
                            <div class="vehicle-card-header">
                                <div class="vehicle-info-block">
                                    <div class="vehicle-icon-wrapper">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#0b57d0" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V6m-7 6h14" />
                                        </svg>
                                    </div>
                                    <div class="vehicle-info">
                                        <h4 class="vehicle-plate">29B1-999.88</h4>
                                        <p class="vehicle-name">Honda SH 150i</p>
                                    </div>
                                </div>
                                <span class="badge-status-pill badge-status-pill--success" style="background: #a7f3d0; color: #047857; margin-top: 4px;">Active</span>
                            </div>
                            <div class="vehicle-card-footer">
                                <span class="vehicle-location">Vị trí đỗ: Area C</span>
                                <span class="vehicle-type">Xe máy</span>
                            </div>
                        </div>
                    @endforelse
                    
                    <div class="vehicle-add-card">
                        <button class="btn-dashed-full">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#64748b" stroke-width="2" style="margin-bottom: 8px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Đăng ký xe mới
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Banner --}}
    <div class="apartment-notification-banner">
        <div class="banner-content-wrapper">
            <div class="banner-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
            </div>
            <div class="banner-text">
                <h4>Thông báo riêng cho căn hộ</h4>
                <p>Gửi tin nhắn hoặc thông báo đẩy trực tiếp đến ứng dụng cư dân {{ $apartment->apartment_number }}</p>
            </div>
        </div>
        <button class="btn-banner-action">
            Gửi ngay
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function openTab(evt, tabName) {
        // Hide all tab panes
        const tabPanes = document.getElementsByClassName("tab-pane");
        for (let i = 0; i < tabPanes.length; i++) {
            tabPanes[i].style.display = "none";
            tabPanes[i].classList.remove("active");
        }
        
        // Remove active class from all buttons
        const tabButtons = document.getElementsByClassName("tab-button");
        for (let i = 0; i < tabButtons.length; i++) {
            tabButtons[i].classList.remove("active");
        }
        
        // Show current tab and add active class to button
        const currentPane = document.getElementById(tabName);
        currentPane.style.display = "block";
        setTimeout(() => {
            currentPane.classList.add("active");
        }, 10);
        evt.currentTarget.classList.add("active");
    }
</script>
@endpush
