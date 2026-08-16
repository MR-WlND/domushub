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
            <button class="tab-button" onclick="openTab(event, 'tab-temporary')">Tạm trú / Tạm vắng</button>
            <button class="tab-button" onclick="openTab(event, 'tab-history')">Lịch sử cư trú</button>
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
                                    <span class="tech-label">Số phòng ngủ:</span>
                                    <span class="tech-value">{{ sprintf('%02d', $apartment->apartmentType->bedroom_count ?? 1) }}</span>
                                </div>
                                <div class="tech-detail-row">
                                    <span class="tech-label">Số phòng tắm:</span>
                                    <span class="tech-value">{{ sprintf('%02d', $apartment->apartmentType->bathroom_count ?? 1) }}</span>
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

                        <div class="info-section" style="margin-top: 32px;">
                            <div class="section-title-wrapper">
                                <div class="section-accent-line"></div>
                                <h4 class="info-section-title">Pháp lý & Bàn giao</h4>
                            </div>
                            <div class="tech-detail-list" style="margin-bottom: 16px;">
                                <div class="tech-detail-row">
                                    <span class="tech-label">Ngày bàn giao:</span>
                                    <span class="tech-value">{{ $apartment->handover_date ? \Carbon\Carbon::parse($apartment->handover_date)->format('d/m/Y') : 'Chưa xác định' }}</span>
                                </div>
                                <div class="tech-detail-row">
                                    <span class="tech-label">Trạng thái Sổ hồng:</span>
                                    <span class="tech-value">
                                        @if($apartment->legal_status == 'issued')
                                            <span class="badge-status-pill badge-status-pill--success" style="padding: 4px 8px; font-size: 11px;">Đã cấp sổ</span>
                                        @elseif($apartment->legal_status == 'processing')
                                            <span class="badge-status-pill badge-status-pill--warning" style="padding: 4px 8px; font-size: 11px;">Đang làm thủ tục</span>
                                        @else
                                            <span class="badge-status-pill badge-status-pill--danger" style="padding: 4px 8px; font-size: 11px;">Chưa cấp</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <button class="btn-outline-custom" onclick="openUpdateLegalModal()" style="width: 100%; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Cập nhật pháp lý
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 2: Cư dân --}}
            <div id="tab-residents" class="tab-pane" style="display: none;">
                
                {{-- Chủ hộ Section --}}
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h4 style="margin: 0; color: #0f172a; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#0b57d0" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Chủ Sở Hữu
                    </h4>
                    <button onclick="openAssignOwnerModal()" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; border: 1px solid #bfdbfe; background-color: #eff6ff; color: #1d4ed8; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.backgroundColor='#dbeafe'; this.style.borderColor='#93c5fd'" onmouseout="this.style.backgroundColor='#eff6ff'; this.style.borderColor='#bfdbfe'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Gán chủ hộ
                    </button>
                </div>
                <div class="table-responsive" style="margin-bottom: 32px;">
                    <table class="clean-table">
                        <thead>
                            <tr>
                                <th>Họ và tên</th>
                                <th>Số điện thoại</th>
                                <th>Ngày nhận nhà</th>
                                <th style="text-align: right;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apartment->residents->where('relationship', 'owner') as $resident)
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
                                                <div class="td-sub" style="color: #0b57d0; font-weight: 600;">Chủ hộ</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $residentPhone }}</td>
                                    <td>{{ $resident->start_date ? \Carbon\Carbon::parse($resident->start_date)->format('d/m/Y') : '--' }}</td>
                                    <td style="text-align: right;">
                                        <form action="{{ route('admin.residents.destroy', $resident->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn gỡ chủ hộ này khỏi căn hộ không?');" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="color: #ef4444; background: none; border: none; font-weight: 600; font-size: 13px; cursor: pointer; padding: 6px 10px; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#fee2e2'" onmouseout="this.style.backgroundColor='transparent'">
                                                Gỡ bỏ
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #64748b; padding: 24px;">Chưa gán chủ sở hữu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Thành Viên Cùng Ở Section --}}
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h4 style="margin: 0; color: #0f172a; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Thành Viên Cùng Ở
                    </h4>
                    <button style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; border: 1px solid #a7f3d0; background-color: #ecfdf5; color: #047857; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.backgroundColor='#d1fae5'; this.style.borderColor='#6ee7b7'" onmouseout="this.style.backgroundColor='#ecfdf5'; this.style.borderColor='#a7f3d0'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Thêm thành viên
                    </button>
                </div>
                <div class="table-responsive" style="margin-bottom: 32px;">
                    <table class="clean-table">
                        <thead>
                            <tr>
                                <th>Họ và tên</th>
                                <th>Số điện thoại</th>
                                <th>Quan hệ</th>
                                <th>Ngày chuyển vào</th>
                                <th style="text-align: right;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apartment->residents->whereNotIn('relationship', ['owner', 'tenant']) as $resident)
                                @php
                                    $user = $resident->user;
                                    $residentName = $user->name ?? 'Chưa có tên';
                                    $residentPhone = $user->phone ?? '—';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="td-profile">
                                            <div class="td-avatar" style="background: #10b981; color: white;">
                                                {{ mb_strtoupper(mb_substr($residentName, 0, 2)) }}
                                            </div>
                                            <div class="td-name-info">
                                                <div class="td-name">{{ $residentName }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $residentPhone }}</td>
                                    <td>
                                        <span class="badge-status-pill badge-status-pill--success">Thành viên</span>
                                    </td>
                                    <td>{{ $resident->start_date ? \Carbon\Carbon::parse($resident->start_date)->format('d/m/Y') : '--' }}</td>
                                    <td style="text-align: right;">
                                        <form action="{{ route('admin.residents.destroy', $resident->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn gỡ thành viên này khỏi căn hộ không?');" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="color: #ef4444; background: none; border: none; font-weight: 600; font-size: 13px; cursor: pointer; padding: 6px 10px; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#fee2e2'" onmouseout="this.style.backgroundColor='transparent'">
                                                Gỡ bỏ
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #64748b; padding: 24px;">Chưa có thành viên nào khác.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Khách Thuê Chính Section --}}
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h4 style="margin: 0; color: #0f172a; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        Khách Thuê Hợp Đồng
                    </h4>
                    <button onclick="openAssignTenantModal()" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; border: 1px solid #fde68a; background-color: #fffbeb; color: #d97706; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.backgroundColor='#fef3c7'; this.style.borderColor='#fcd34d'" onmouseout="this.style.backgroundColor='#fffbeb'; this.style.borderColor='#fde68a'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Thêm khách thuê
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="clean-table">
                        <thead>
                            <tr>
                                <th>Họ và tên</th>
                                <th>Số điện thoại</th>
                                <th>Ngày bắt đầu thuê</th>
                                <th>Ngày hết hạn hợp đồng</th>
                                <th style="text-align: right;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apartment->residents->where('relationship', 'tenant') as $resident)
                                @php
                                    $user = $resident->user;
                                    $residentName = $user->name ?? 'Chưa có tên';
                                    $residentPhone = $user->phone ?? '—';
                                @endphp
                                <tr>
                                    <td>
                                        <div class="td-profile">
                                            <div class="td-avatar" style="background: #f59e0b; color: white;">
                                                {{ mb_strtoupper(mb_substr($residentName, 0, 2)) }}
                                            </div>
                                            <div class="td-name-info">
                                                <div class="td-name">{{ $residentName }}</div>
                                                <div class="td-sub" style="color: #d97706; font-weight: 600;">Khách thuê</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $residentPhone }}</td>
                                    <td>{{ $resident->start_date ? \Carbon\Carbon::parse($resident->start_date)->format('d/m/Y') : '--' }}</td>
                                    <td>
                                        @if($resident->end_date)
                                            @php
                                                $endDate = \Carbon\Carbon::parse($resident->end_date);
                                                $isExpired = $endDate->isPast();
                                            @endphp
                                            <span style="color: {{ $isExpired ? '#dc2626' : '#0f172a' }}; font-weight: {{ $isExpired ? 'bold' : 'normal' }}">
                                                {{ $endDate->format('d/m/Y') }}
                                            </span>
                                        @else
                                            <span style="color: #64748b;">Vô thời hạn</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <form action="{{ route('admin.residents.destroy', $resident->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn gỡ khách thuê này khỏi căn hộ không?');" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="color: #ef4444; background: none; border: none; font-weight: 600; font-size: 13px; cursor: pointer; padding: 6px 10px; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#fee2e2'" onmouseout="this.style.backgroundColor='transparent'">
                                                Gỡ bỏ
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #64748b; padding: 24px;">Chưa có khách thuê chính.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

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

            {{-- Tab 5: Tạm trú/Tạm vắng --}}
            <div id="tab-temporary" class="tab-pane" style="display: none;">
                <div class="table-responsive">
                    <table class="clean-table">
                        <thead>
                            <tr>
                                <th>Người Tạm Trú / Vắng</th>
                                <th>Loại đơn</th>
                                <th>Thời gian</th>
                                <th>Trạng thái</th>
                                <th style="text-align: right;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($temporaryRegistrations as $reg)
                                <tr>
                                    <td>
                                        @if($reg->type == 'residence')
                                            {{ $reg->guest_name ?? 'Khách' }}
                                        @else
                                            {{ $reg->user->name ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>{{ $reg->type == 'residence' ? 'Tạm trú' : 'Tạm vắng' }}</td>
                                    <td>
                                        {{ $reg->start_date->format('d/m/Y') }} - 
                                        {{ $reg->end_date ? $reg->end_date->format('d/m/Y') : 'Vô thời hạn' }}
                                    </td>
                                    <td>
                                        @if($reg->status == 'pending')
                                            <span class="badge-status-pill badge-status-pill--warning">Chờ duyệt</span>
                                        @elseif($reg->status == 'approved')
                                            <span class="badge-status-pill badge-status-pill--success">Đã duyệt</span>
                                        @else
                                            <span class="badge-status-pill badge-status-pill--danger">Từ chối</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('admin.temporary-registrations.edit', $reg->id) }}" class="td-action-link">Chi tiết</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #64748b; padding: 24px;">Chưa có đơn đăng ký tạm trú / tạm vắng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab Lịch sử cư trú --}}
            <div id="tab-history" class="tab-pane" style="display: none;">
                <div class="table-responsive">
                    <table class="clean-table">
                        <thead>
                            <tr>
                                <th>Cư dân</th>
                                <th>Vai trò</th>
                                <th>Ngày chuyển vào</th>
                                <th>Ngày rời đi</th>
                                <th>Thời gian lưu trú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($residentsHistory->whereNotNull('deleted_at') as $history)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: #0f172a;">{{ $history->user->name ?? 'N/A' }}</div>
                                        <div style="color: #64748b; font-size: 13px;">{{ $history->user->phone ?? '--' }}</div>
                                    </td>
                                    <td>
                                        @if($history->relationship == 'owner')
                                            <span class="badge-status-pill badge-status-pill--primary">Chủ hộ cũ</span>
                                        @elseif($history->relationship == 'tenant')
                                            <span class="badge-status-pill badge-status-pill--warning">Khách thuê cũ</span>
                                        @else
                                            <span class="badge-status-pill badge-status-pill--success">Thành viên cũ</span>
                                        @endif
                                    </td>
                                    <td>{{ $history->start_date ? \Carbon\Carbon::parse($history->start_date)->format('d/m/Y') : '--' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($history->deleted_at)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($history->start_date)
                                            {{ \Carbon\Carbon::parse($history->start_date)->diffInDays(\Carbon\Carbon::parse($history->deleted_at)) }} ngày
                                        @else
                                            --
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: #64748b; padding: 24px;">Chưa có lịch sử chuyển nhượng/rời đi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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

<!-- Assign Owner Modal -->
<div id="assignOwnerModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
    <div class="modal-content shadow-lg" style="background-color: #ffffff; margin: 10vh auto; padding: 32px; border-radius: 24px; width: 90%; max-width: 480px; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <span class="close-modal" onclick="closeAssignOwnerModal()" style="position: absolute; right: 24px; top: 24px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: #f8fafc; cursor: pointer; color: #64748b; transition: all 0.2s;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </span>
        
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
            <div style="width: 48px; height: 48px; border-radius: 14px; background: #eff6ff; display: flex; align-items: center; justify-content: center; color: #3b82f6; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h3 style="margin: 0; color: #0f172a; font-size: 20px; font-weight: 800; letter-spacing: -0.5px;">Gán Chủ Hộ</h3>
                <p style="margin: 4px 0 0; font-size: 14px; color: #64748b; font-weight: 500;">Chỉ định cư dân làm chủ tài sản này.</p>
            </div>
        </div>
        
        <form action="{{ route('admin.apartments.assign-owner', $apartment->id) }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Chọn Cư dân <span style="color: #ef4444;">*</span></label>
                <select name="user_id" required style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #cbd5e1; background-color: #f8fafc; font-size: 15px; color: #0f172a; outline: none; transition: all 0.2s; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.02); appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=%22none%22 stroke=%22%2364748b%22 stroke-width=%222%22 viewBox=%220 0 24 24%22 xmlns=%22http://www.w3.org/2000/svg%22><path stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M19 9l-7 7-7-7%22></path></svg>'); background-repeat: no-repeat; background-position: right 16px center; background-size: 16px;">
                    <option value="">-- Vui lòng chọn --</option>
                    @foreach($allResidents as $res)
                        <option value="{{ $res->id }}">{{ $res->name }} - {{ $res->phone }}</option>
                    @endforeach
                </select>
                <small style="color: #64748b; font-size: 13px; display: flex; align-items: center; gap: 6px; margin-top: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Chỉ hiển thị các tài khoản có role là resident.
                </small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="closeAssignOwnerModal()" style="padding: 10px 20px; border-radius: 12px; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s;">Hủy bỏ</button>
                <button type="submit" style="padding: 10px 24px; border-radius: 12px; border: none; background: #3b82f6; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); transition: all 0.2s;">Xác nhận Gán</button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Tenant Modal -->
<div id="assignTenantModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
    <div class="modal-content shadow-lg" style="background-color: #ffffff; margin: 10vh auto; padding: 32px; border-radius: 24px; width: 90%; max-width: 480px; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <span class="close-modal" onclick="closeAssignTenantModal()" style="position: absolute; right: 24px; top: 24px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: #f8fafc; cursor: pointer; color: #64748b; transition: all 0.2s;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </span>
        
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
            <div style="width: 48px; height: 48px; border-radius: 14px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; color: #10b981; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <div>
                <h3 style="margin: 0; color: #0f172a; font-size: 20px; font-weight: 800; letter-spacing: -0.5px;">Thêm Khách Thuê</h3>
                <p style="margin: 4px 0 0; font-size: 14px; color: #64748b; font-weight: 500;">Đăng ký người thuê mới cho căn hộ.</p>
            </div>
        </div>
        
        <form action="{{ route('admin.apartments.assign-tenant', $apartment->id) }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Chọn Cư dân <span style="color: #ef4444;">*</span></label>
                <select name="user_id" required style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #cbd5e1; background-color: #f8fafc; font-size: 15px; color: #0f172a; outline: none; transition: all 0.2s; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.02); appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=%22none%22 stroke=%22%2364748b%22 stroke-width=%222%22 viewBox=%220 0 24 24%22 xmlns=%22http://www.w3.org/2000/svg%22><path stroke-linecap=%22round%22 stroke-linejoin=%22round%22 d=%22M19 9l-7 7-7-7%22></path></svg>'); background-repeat: no-repeat; background-position: right 16px center; background-size: 16px;">
                    <option value="">-- Vui lòng chọn --</option>
                    @foreach($allResidents as $res)
                        <option value="{{ $res->id }}">{{ $res->name }} - {{ $res->phone }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Ngày bắt đầu thuê <span style="color: #ef4444;">*</span></label>
                <input type="date" name="start_date" required style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #cbd5e1; background-color: #f8fafc; font-size: 15px; color: #0f172a; outline: none; transition: all 0.2s; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.02); font-family: inherit;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Ngày kết thúc hợp đồng</label>
                <input type="date" name="end_date" style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #cbd5e1; background-color: #f8fafc; font-size: 15px; color: #0f172a; outline: none; transition: all 0.2s; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.02); font-family: inherit;">
                <small style="color: #64748b; font-size: 13px; display: block; margin-top: 8px;">Bỏ trống trường này nếu hợp đồng vô thời hạn.</small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="closeAssignTenantModal()" style="padding: 10px 20px; border-radius: 12px; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s;">Hủy bỏ</button>
                <button type="submit" style="padding: 10px 24px; border-radius: 12px; border: none; background: #10b981; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); transition: all 0.2s;">Thêm Người Thuê</button>
            </div>
        </form>
    </div>
</div>

@endsection

<!-- Update Legal Modal -->
<div id="updateLegalModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px);">
    <div class="modal-content shadow-lg" style="background-color: #ffffff; margin: 10vh auto; padding: 32px; border-radius: 24px; width: 90%; max-width: 480px; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <span class="close-modal" onclick="closeUpdateLegalModal()" style="position: absolute; right: 24px; top: 24px; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: #f8fafc; cursor: pointer; color: #64748b; transition: all 0.2s;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </span>
        
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
            <div style="width: 48px; height: 48px; border-radius: 14px; background: #f8fafc; display: flex; align-items: center; justify-content: center; color: #0f172a; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h3 style="margin: 0; color: #0f172a; font-size: 20px; font-weight: 800; letter-spacing: -0.5px;">Cập nhật Pháp lý</h3>
                <p style="margin: 4px 0 0; font-size: 14px; color: #64748b; font-weight: 500;">Cập nhật thông tin sổ hồng & bàn giao.</p>
            </div>
        </div>
        
        <form action="{{ route('admin.apartments.update-legal', $apartment->id) }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Ngày bàn giao</label>
                <input type="date" name="handover_date" value="{{ $apartment->handover_date ? \Carbon\Carbon::parse($apartment->handover_date)->format('Y-m-d') : '' }}" style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #cbd5e1; background-color: #f8fafc; font-size: 15px; color: #0f172a; outline: none; font-family: inherit;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #334155; margin-bottom: 8px;">Trạng thái Sổ hồng <span style="color: #ef4444;">*</span></label>
                <select name="legal_status" required style="width: 100%; padding: 12px 16px; border-radius: 12px; border: 1px solid #cbd5e1; background-color: #f8fafc; font-size: 15px; color: #0f172a; outline: none;">
                    <option value="pending" {{ $apartment->legal_status == 'pending' ? 'selected' : '' }}>Chưa cấp</option>
                    <option value="processing" {{ $apartment->legal_status == 'processing' ? 'selected' : '' }}>Đang làm thủ tục</option>
                    <option value="issued" {{ $apartment->legal_status == 'issued' ? 'selected' : '' }}>Đã cấp sổ</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 1px solid #f1f5f9;">
                <button type="button" onclick="closeUpdateLegalModal()" style="padding: 10px 20px; border-radius: 12px; border: 1px solid #cbd5e1; background: #fff; color: #475569; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s;">Hủy bỏ</button>
                <button type="submit" style="padding: 10px 24px; border-radius: 12px; border: none; background: #0f172a; color: #fff; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s;">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

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

    function openAssignOwnerModal() {
        document.getElementById('assignOwnerModal').style.display = 'block';
    }

    function closeAssignOwnerModal() {
        document.getElementById('assignOwnerModal').style.display = 'none';
    }

    function openAssignTenantModal() {
        document.getElementById('assignTenantModal').style.display = 'block';
    }

    function closeAssignTenantModal() {
        document.getElementById('assignTenantModal').style.display = 'none';
    }

    function openUpdateLegalModal() {
        document.getElementById('updateLegalModal').style.display = 'block';
    }

    function closeUpdateLegalModal() {
        document.getElementById('updateLegalModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        var vehicleModal = document.getElementById('vehicleModal');
        var ownerModal = document.getElementById('assignOwnerModal');
        var tenantModal = document.getElementById('assignTenantModal');
        var legalModal = document.getElementById('updateLegalModal');
        
        if (event.target == vehicleModal) {
            vehicleModal.style.display = "none";
        }
        if (event.target == ownerModal) {
            ownerModal.style.display = "none";
        }
        if (event.target == tenantModal) {
            tenantModal.style.display = "none";
        }
        if (event.target == legalModal) {
            legalModal.style.display = "none";
        }
    }
</script>
@endpush
