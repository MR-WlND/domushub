@extends('layouts.admin.master')

@section('page_title', 'Trang tổng quan – DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="db-page">

    {{-- ===================== HEADER ===================== --}}
    <div class="db-header">
        <div>
            <h1 class="db-header__title">Trang Tổng Quan</h1>
            <p class="db-header__sub">Xin chào, <strong>{{ auth()->user()->name ?? 'Quản trị viên' }}</strong> — Đây là bảng điều khiển vận hành của chung cư hôm nay.</p>
        </div>
        <div class="db-header__meta">
            <span class="db-online-badge">
                <span class="db-pulse"></span>
                Hệ thống trực tuyến
            </span>
        </div>
    </div>

    {{-- ===================== KPI: HẠ TẦNG ===================== --}}
    <div class="section-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="2" y="3" width="20" height="18" rx="1"/><path d="M9 3v18"/><path d="M15 3v18"/><path d="M2 9h20"/><path d="M2 15h20"/></svg>
        Hạ tầng chung cư
    </div>
    <div class="kpi-grid">
        <a href="{{ route('admin.blocks.index') }}" class="kpi-card kpi-card--blue db-kpi-link">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="1"/><path d="M9 3v18"/><path d="M15 3v18"/><path d="M2 9h20"/><path d="M2 15h20"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Tổng Tòa Nhà</span>
                <span class="kpi-value">{{ $totalBlocks ?? 0 }}</span>
                <span class="kpi-sub">{{ $totalFloors ?? 0 }} tầng — {{ $totalApartments ?? 0 }} căn hộ</span>
            </div>
        </a>

        <a href="{{ route('admin.apartments.index') }}" class="kpi-card kpi-card--indigo db-kpi-link">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Căn Hộ Đang Ở</span>
                <span class="kpi-value">{{ $occupiedApartments ?? 0 }}</span>
                @php
                    $rate = ($totalApartments ?? 0) > 0 ? round(($occupiedApartments / $totalApartments) * 100, 1) : 0;
                @endphp
                <div class="mini-progress">
                    <div class="mini-progress-fill" style="width: {{ $rate }}%; background: #6366f1;"></div>
                </div>
                <span class="kpi-sub">Tỷ lệ lấp đầy: {{ $rate }}%</span>
            </div>
        </a>

        <div class="kpi-card kpi-card--amber">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Tổng Nhân Khẩu</span>
                <span class="kpi-value">{{ $totalResidents ?? 0 }}</span>
                <span class="kpi-sub">Đã đăng ký & khai báo</span>
            </div>
        </div>

        <div class="kpi-card kpi-card--green">
            <div class="kpi-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            </div>
            <div class="kpi-info">
                <span class="kpi-label">Trạng Thái Căn Hộ</span>
                <div class="db-apt-status">
                    <span class="db-apt-dot db-apt-dot--green"></span> Đang ở: <strong>{{ $occupiedApartments ?? 0 }}</strong>
                </div>
                <div class="db-apt-status">
                    <span class="db-apt-dot db-apt-dot--gray"></span> Trống: <strong>{{ $vacantApartments ?? 0 }}</strong>
                </div>
                <div class="db-apt-status">
                    <span class="db-apt-dot db-apt-dot--amber"></span> Bảo trì: <strong>{{ $maintenanceApartments ?? 0 }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== CẢNH BÁO CHỜ XỬ LÝ ===================== --}}
    @if(($pendingTicketsCount ?? 0) > 0 || ($pendingVehiclesCount ?? 0) > 0 || ($pendingBookingsCount ?? 0) > 0)
    <div class="section-label">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Yêu cầu đang chờ xử lý
    </div>
    <div class="db-alerts-row">
        @if(($pendingTicketsCount ?? 0) > 0)
        <a href="{{ route('admin.tickets.index', ['status' => 'pending']) }}" class="db-alert-card db-alert-card--amber">
            <div class="db-alert-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div class="db-alert-body">
                <span class="db-alert-count">{{ $pendingTicketsCount }}</span>
                <span class="db-alert-label">Phản ánh chờ duyệt</span>
            </div>
            <svg class="db-alert-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
        @endif

        @if(($pendingVehiclesCount ?? 0) > 0)
        <a href="{{ route('admin.vehicles.index') }}" class="db-alert-card db-alert-card--blue">
            <div class="db-alert-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <div class="db-alert-body">
                <span class="db-alert-count">{{ $pendingVehiclesCount }}</span>
                <span class="db-alert-label">Đăng ký xe chờ duyệt</span>
            </div>
            <svg class="db-alert-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
        @endif

        @if(($pendingBookingsCount ?? 0) > 0)
        <a href="{{ route('admin.amenities.bookings') }}" class="db-alert-card db-alert-card--indigo">
            <div class="db-alert-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="db-alert-body">
                <span class="db-alert-count">{{ $pendingBookingsCount }}</span>
                <span class="db-alert-label">Đặt tiện ích chờ duyệt</span>
            </div>
            <svg class="db-alert-chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
        @endif
    </div>
    @endif

    {{-- ===================== LƯỚI VẬN HÀNH ===================== --}}
    <div class="db-main-grid">

        {{-- CỘT TRÁI --}}
        <div class="db-col-left">

            {{-- Biểu đồ Thống kê doanh thu --}}
            <div class="chart-card">
                <div class="chart-card__header">
                    <div>
                        <h3 class="chart-card__title">Biểu đồ doanh thu</h3>
                        <p class="chart-card__sub">Doanh thu theo tháng trong năm nay</p>
                    </div>
                </div>
                <div class="chart-card__body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>

            {{-- Bảng phản ánh gần nhất --}}
            <div class="chart-card">
                <div class="chart-card__header">
                    <div>
                        <h3 class="chart-card__title">Phản ánh gần nhất</h3>
                        <p class="chart-card__sub">5 yêu cầu mới nhất từ cư dân</p>
                    </div>
                    <a href="{{ route('admin.tickets.index') }}" class="db-link-btn">
                        Xem tất cả
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tiêu đề phản ánh</th>
                                <th>Căn hộ</th>
                                <th>Mức độ</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTickets ?? [] as $ticket)
                            <tr style="cursor:pointer;" onclick="window.location='{{ route('admin.tickets.show', $ticket->id) }}'">
                                <td><strong>{{ Str::limit($ticket->title, 38) }}</strong></td>
                                <td>{{ $ticket->apartment->apartment_number ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $pMap = ['urgent'=>['Khẩn cấp','#dc2626','#fee2e2'],'high'=>['Cao','#c2410c','#ffedd5'],'medium'=>['TB','#0369a1','#e0f2fe'],'low'=>['Thấp','#475569','#f1f5f9']];
                                        [$pl, $pc, $pbg] = $pMap[$ticket->priority] ?? ['Thấp','#475569','#f1f5f9'];
                                    @endphp
                                    <span class="db-badge" style="color:{{ $pc }};background:{{ $pbg }};">{{ $pl }}</span>
                                </td>
                                <td>
                                    @php
                                        $sMap = ['pending'=>['Chờ duyệt','#d97706','#fef3c7'],'assigned'=>['Điều phối','#2563eb','#dbeafe'],'in_progress'=>['Đang xử lý','#4f46e5','#e0e7ff'],'completed'=>['Hoàn thành','#059669','#d1fae5'],'cancelled'=>['Đã hủy','#6b7280','#f3f4f6']];
                                        [$sl, $sc, $sbg] = $sMap[$ticket->status] ?? ['Chờ duyệt','#d97706','#fef3c7'];
                                    @endphp
                                    <span class="db-badge" style="color:{{ $sc }};background:{{ $sbg }};">{{ $sl }}</span>
                                </td>
                                <td style="color:#64748b;font-size:12px;">{{ $ticket->created_at->format('H:i d/m/Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-row">Chưa có phản ánh nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bảng đặt tiện ích --}}
            <div class="chart-card">
                <div class="chart-card__header">
                    <div>
                        <h3 class="chart-card__title">Lượt đặt tiện ích mới nhất</h3>
                        <p class="chart-card__sub">5 lịch đặt gần nhất của cư dân</p>
                    </div>
                    <a href="{{ route('admin.amenities.bookings') }}" class="db-link-btn">
                        Xem tất cả
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Cư dân</th>
                                <th>Tiện ích</th>
                                <th>Ngày / Khung giờ</th>
                                <th>Phí</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings ?? [] as $booking)
                            <tr>
                                <td><strong>{{ $booking->user->name ?? 'N/A' }}</strong></td>
                                <td>{{ $booking->facility->name ?? 'N/A' }}</td>
                                <td style="font-size:12px;color:#475569;">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}<br>
                                    <span style="color:#94a3b8;">{{ $booking->start_time }} – {{ $booking->end_time }}</span>
                                </td>
                                <td style="font-size:13px;font-weight:600;color:#0f172a;">
                                    {{ ($booking->price_per_slot ?? 0) > 0 ? number_format($booking->price_per_slot, 0, ',', '.') . ' đ' : 'Miễn phí' }}
                                </td>
                                <td>
                                    @php
                                        $bMap = ['pending'=>['Chờ duyệt','#d97706','#fef3c7'],'approved'=>['Đã duyệt','#2563eb','#dbeafe'],'completed'=>['Hoàn thành','#059669','#d1fae5'],'rejected'=>['Từ chối','#6b7280','#f3f4f6'],'cancelled'=>['Đã hủy','#6b7280','#f3f4f6']];
                                        [$bl, $bc, $bbg] = $bMap[$booking->status] ?? ['Chờ duyệt','#d97706','#fef3c7'];
                                    @endphp
                                    <span class="db-badge" style="color:{{ $bc }};background:{{ $bbg }};">{{ $bl }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-row">Chưa có lượt đặt tiện ích nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI --}}
        <div class="db-col-right">

            {{-- Hành động nhanh --}}
            <div class="chart-card">
                <div class="chart-card__header">
                    <div>
                        <h3 class="chart-card__title">Hành động nhanh</h3>
                        <p class="chart-card__sub">Phím tắt truy cập các chức năng chính</p>
                    </div>
                </div>
                <div class="db-quick-grid">
                    <a href="{{ route('admin.blocks.create') }}" class="db-quick-btn db-quick-btn--blue">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="1"/><path d="M9 3v18M15 3v18M2 9h20M2 15h20"/></svg>
                        <span>Thêm Tòa Nhà</span>
                    </a>
                    <a href="{{ route('admin.floors.create') }}" class="db-quick-btn db-quick-btn--indigo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        <span>Thêm Tầng</span>
                    </a>
                    <a href="{{ route('admin.apartments.create') }}" class="db-quick-btn db-quick-btn--green">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <span>Thêm Căn Hộ</span>
                    </a>
                    <a href="{{ route('admin.utility-readings.create') }}" class="db-quick-btn db-quick-btn--amber">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></svg>
                        <span>Ghi Điện Nước</span>
                    </a>
                    <a href="{{ route('admin.invoices.create') }}" class="db-quick-btn db-quick-btn--green">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        <span>Tạo Hóa Đơn</span>
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="db-quick-btn db-quick-btn--blue">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <span>Tạo Tài Khoản</span>
                    </a>
                </div>
            </div>

            {{-- Trạng thái hệ thống --}}
            <div class="chart-card">
                <div class="chart-card__header">
                    <div>
                        <h3 class="chart-card__title">Trạng thái hệ thống</h3>
                        <p class="chart-card__sub">Kiểm tra sức khoẻ các dịch vụ</p>
                    </div>
                </div>
                <div class="db-status-list">
                    <div class="db-status-row">
                        <span class="db-status-label">Máy chủ Web (PHP)</span>
                        <span class="db-status-value db-status-value--ok">
                            <span class="db-pulse db-pulse--sm"></span> Online
                        </span>
                    </div>
                    <div class="db-status-row">
                        <span class="db-status-label">Cơ sở dữ liệu</span>
                        <span class="db-status-value db-status-value--ok">
                            <span class="db-pulse db-pulse--sm"></span> Kết nối
                        </span>
                    </div>
                    <div class="db-status-row">
                        <span class="db-status-label">WebSocket (Reverb)</span>
                        <span class="db-status-value db-status-value--ok">
                            <span class="db-pulse db-pulse--sm"></span> Active
                        </span>
                    </div>
                    <div class="db-status-row">
                        <span class="db-status-label">Uptime</span>
                        <span class="db-status-value" style="color:#0f172a;font-weight:600;">99.98%</span>
                    </div>
                    <div class="db-status-row">
                        <span class="db-status-label">Bảo mật (SSL)</span>
                        <span class="db-status-value" style="color:#0f172a;font-weight:600;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5" style="vertical-align:middle"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Secured
                        </span>
                    </div>
                </div>
            </div>

            {{-- Link nhanh đến báo cáo --}}
            <div class="chart-card">
                <div class="chart-card__header">
                    <div>
                        <h3 class="chart-card__title">Báo cáo & Thống kê</h3>
                        <p class="chart-card__sub">Truy cập nhanh các trang phân tích</p>
                    </div>
                </div>
                <div class="db-report-links">
                    <a href="{{ route('admin.statistics.finance') }}" class="db-report-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                        Thống kê Tài chính
                    </a>
                    <a href="{{ route('admin.statistics.operations') }}" class="db-report-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        Thống kê Vận hành
                    </a>
                    <a href="{{ route('admin.statistics.residents') }}" class="db-report-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Cư dân & Hạ tầng
                    </a>
                    <a href="{{ route('admin.amenities.statistics') }}" class="db-report-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Thống kê Tiện ích
                    </a>
                    <a href="{{ route('admin.invoices.stats') }}" class="db-report-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        Thống kê Hóa đơn
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    @vite(['resources/css/pages/admin/statistics.css', 'resources/css/pages/admin/dashboard.css'])
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    fetch('/api/statistics/revenue')
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: res.labels,
                        datasets: [{
                            label: 'Doanh thu (VNĐ)',
                            data: res.data,
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgb(59, 130, 246)',
                            borderWidth: 1,
                            borderRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return value.toLocaleString('vi-VN') + ' đ';
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.raw.toLocaleString('vi-VN') + ' đ';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
});
</script>
@endpush
@endsection
