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
                                    <span class="tech-value">{{ sprintf('%02d', $apartment->apartmentType->living_room_count ?? 1) }}</span>
                                </div>
                                <div class="tech-detail-row">
                                    <span class="tech-label">Số phòng ngủ:</span>
                                    <span class="tech-value">{{ sprintf('%02d', $apartment->apartmentType->bedroom_count ?? 1) }}</span>
                                </div>
                                <div class="tech-detail-row">
                                    <span class="tech-label">Số phòng tắm:</span>
                                    <span class="tech-value">{{ sprintf('%02d', $apartment->apartmentType->bathroom_count ?? 1) }}</span>
                                </div>
                                <div class="tech-detail-row">
                                    <span class="tech-label">Hướng ban công:</span>
                                    <span class="tech-value">{{ $apartment->apartmentType->balcony_direction ?: 'Chưa cập nhật' }}</span>
                                </div>
                                <div class="tech-detail-row">
                                    <span class="tech-label">Tình trạng nội thất:</span>
                                    <span class="tech-value">{{ $apartment->apartmentType->furniture_status ?: 'Chưa cập nhật' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="info-section" style="margin-top: 32px;">
                            <div class="section-title-wrapper">
                                <div class="section-accent-line"></div>
                                <h4 class="info-section-title">Nội thất kèm theo</h4>
                            </div>
                            <ul class="furniture-list">
                                @if(!empty($apartment->apartmentType->furniture_list))
                                    @foreach($apartment->apartmentType->furniture_list as $item)
                                        <li>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#059669" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $item }}
                                        </li>
                                    @endforeach
                                @else
                                    <li>
                                        <span style="color: #64748b; font-style: italic;">Chưa có thông tin nội thất.</span>
                                    </li>
                                @endif
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
                                {{ $apartment->description ?? 'Chưa có ghi chú quản lý.' }}
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
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #64748b; padding: 24px;">Chưa có cư dân nào.</td>
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
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #64748b; padding: 24px;">Chưa có hóa đơn nào.</td>
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
                        <div class="vehicle-card" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; background: #f8fafc; display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 16px;">
                                <div class="vehicle-icon" style="width: 48px; height: 48px; border-radius: 50%; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center;">
                                    @if($vehicle->isCar())
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M5 10V8a2 2 0 012-2h10a2 2 0 012 2v2M3 10a2 2 0 00-2 2v5h2a2 2 0 002 2h10a2 2 0 002-2h2v-5a2 2 0 00-2-2" /></svg>
                                    @elseif($vehicle->isBicycle())
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12m-3 0a3 3 0 106 0 3 3 0 10-6 0m-9 9a3 3 0 106 0 3 3 0 10-6 0m18 0a3 3 0 106 0 3 3 0 10-6 0M15 12h-3v-3m0 0l-3 3M12 9V5" /></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v8l9-11h-7z" /></svg>
                                    @endif
                                </div>
                                <div>
                                    <h4 style="margin: 0; font-size: 16px; color: #0f172a;">{{ $vehicle->license_plate }}</h4>
                                    <p style="margin: 4px 0 0; font-size: 13px; color: #64748b;">
                                        {{ $vehicle->brand ?: 'Không xác định' }} &bull; {{ $vehicle->typeLabel() }}
                                    </p>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <span class="badge-status-pill badge-status-pill--{{ $vehicle->status == 'active' ? 'success' : ($vehicle->status == 'pending' ? 'warning' : 'secondary') }}">
                                    {{ $vehicle->statusLabel() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1; text-align: center; padding: 24px; color: #64748b;">
                            Chưa có phương tiện nào được đăng ký.
                        </div>
                    @endforelse
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

<!-- Vehicle Registration Modal -->
<div id="vehicleModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content dashboard-card shadow-sm" style="background-color: #fff; margin: 5% auto; padding: 24px; border-radius: 12px; width: 90%; max-width: 500px; position: relative;">
        <span class="close-modal" onclick="closeVehicleModal()" style="position: absolute; right: 24px; top: 24px; font-size: 24px; font-weight: bold; cursor: pointer; color: #64748b;">&times;</span>
        <h3 style="margin-bottom: 24px; color: #0f172a;">Đăng ký phương tiện mới</h3>
        
        <form action="{{ portal_route('vehicles.store') }}" method="POST">
            @csrf
            <input type="hidden" name="apartment_id" value="{{ $apartment->id }}">
            
            <div class="form-group-custom" style="margin-bottom: 16px;">
                <label class="form-label-custom">Loại phương tiện <span class="required">*</span></label>
                <div class="input-wrapper-custom">
                    <select name="vehicle_type" class="form-input-custom" required style="width: 100%; border: none; outline: none; background: transparent; padding-left: 8px;">
                        <option value="motorbike">Xe máy</option>
                        <option value="electric_bike">Xe máy điện</option>
                        <option value="car">Ô tô</option>
                        <option value="bicycle">Xe đạp</option>
                    </select>
                </div>
            </div>

            <div class="form-group-custom" style="margin-bottom: 16px;">
                <label class="form-label-custom">Biển số xe <span class="required">*</span></label>
                <div class="input-wrapper-custom">
                    <input type="text" name="license_plate" class="form-input-custom" placeholder="VD: 29A-12345" required style="border: none; padding-left: 8px; width: 100%;">
                </div>
            </div>

            <div class="form-group-custom" style="margin-bottom: 24px;">
                <label class="form-label-custom">Nhãn hiệu (Tùy chọn)</label>
                <div class="input-wrapper-custom">
                    <input type="text" name="brand" class="form-input-custom" placeholder="VD: Honda SH, Toyota Vios" style="border: none; padding-left: 8px; width: 100%;">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="closeVehicleModal()" class="apts-button apts-button--outline" style="padding: 10px 20px;">Hủy bỏ</button>
                <button type="submit" class="apts-button apts-button--primary" style="padding: 10px 20px;">Xác nhận tạo</button>
            </div>
        </form>
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

    function openVehicleModal() {
        document.getElementById('vehicleModal').style.display = 'block';
    }

    function closeVehicleModal() {
        document.getElementById('vehicleModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        var modal = document.getElementById('vehicleModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
@endpush
