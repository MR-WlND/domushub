@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Loại căn hộ: ' . $apartmentType->name)
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    <style>
        .type-detail-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Breadcrumbs */
        .custom-breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #64748b;
            margin-bottom: 28px;
        }
        .custom-breadcrumb a {
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .custom-breadcrumb a:hover {
            color: #0b57d0;
        }
        .custom-breadcrumb .current {
            font-weight: 600;
            color: #0f172a;
        }

        /* Header Section */
        .type-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .type-title-group h1 {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .type-title-group p {
            color: #64748b;
            font-size: 14px;
            margin: 0;
            max-width: 600px;
            line-height: 1.5;
        }

        .btn-edit-header {
            background: #0b57d0;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .btn-edit-header:hover {
            background: #0a4bb5;
            color: white;
        }

        /* Summary Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.05);
            border-color: #cbd5e1;
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .stat-icon-blue { background: #eff6ff; color: #3b82f6; }
        .stat-icon-purple { background: #faf5ff; color: #a855f7; }
        .stat-icon-green { background: #f0fdf4; color: #22c55e; }
        .stat-icon-orange { background: #fff7ed; color: #f97316; }

        .stat-content {
            flex: 1;
        }
        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .stat-value {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }
        .stat-value.highlight-green {
            color: #16a34a;
        }

        /* Main Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }
        
        @media (max-width: 992px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Info Card */
        .info-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 24px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .info-card-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 16px;
            border-bottom: 1px dashed #e2e8f0;
        }

        /* Amenities Tags */
        .amenities-container {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .amenity-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 16px;
            border-radius: 50px;
            font-size: 14px;
            color: #334155;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .amenity-tag:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .amenity-icon {
            color: #0ea5e9;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 32px 16px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px dashed #cbd5e1;
            color: #64748b;
        }

        /* Associated Apartments List */
        .assoc-apartments {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .assoc-apt-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            transition: background 0.2s ease;
        }
        .assoc-apt-item:hover {
            background: #f1f5f9;
        }
        .apt-number {
            font-weight: 600;
            color: #0f172a;
            font-size: 15px;
        }
        .apt-status {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
        }
        .status-occupied { background: #dcfce7; color: #166534; }
        .status-vacant { background: #f1f5f9; color: #475569; }
        .status-maintenance { background: #fef08a; color: #854d0e; }

    </style>
@endpush

@section('content')
<div class="apartments-page type-detail-wrapper">
    
    {{-- Breadcrumbs --}}
    <div class="custom-breadcrumb">
        <a href="{{ portal_route('dashboard') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard
        </a>
        <span>›</span>
        <a href="{{ portal_route('apartment-types.index') }}">Quản lý Loại căn hộ</a>
        <span>›</span>
        <span class="current">{{ $apartmentType->name }}</span>
    </div>

    {{-- Header Card --}}
    <div class="type-header">
        <div class="type-title-group">
            <h1>
                Chi tiết loại căn hộ: {{ $apartmentType->name }}
            </h1>
            <p>{{ $apartmentType->description ?? 'Loại căn hộ này chưa có mô tả chi tiết. Vui lòng cập nhật để quản lý tốt hơn.' }}</p>
        </div>
        <a href="{{ portal_route('apartment-types.edit', $apartmentType) }}" class="btn-edit-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            Chỉnh sửa thông tin
        </a>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Số phòng ngủ</div>
                <div class="stat-value">{{ sprintf('%02d', $apartmentType->bedroom_count ?? 1) }} PN</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-purple">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Số phòng tắm</div>
                <div class="stat-value">{{ sprintf('%02d', $apartmentType->bathroom_count ?? 1) }} WC</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-green">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Đơn giá dịch vụ</div>
                <div class="stat-value highlight-green">{{ number_format($apartmentType->base_service_fee, 0, ',', '.') }} <span style="font-size: 16px; font-weight: 500;">đ/m²</span></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper stat-icon-orange">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Tổng căn hộ</div>
                <div class="stat-value">{{ $apartmentType->apartments()->count() }} <span style="font-size: 16px; font-weight: 500;">căn</span></div>
            </div>
        </div>
    </div>

    {{-- Content Grid --}}
    <div class="content-grid">
        {{-- Left Column --}}
        <div class="info-card">
            <h3 class="info-card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#0ea5e9" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                Nội thất & Tiện ích đi kèm
            </h3>
            
            @if(!empty($apartmentType->furniture_list))
                <div class="amenities-container">
                    @foreach($apartmentType->furniture_list as $item)
                        <div class="amenity-tag">
                            <svg class="amenity-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.5" style="margin-bottom: 12px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p>Chưa có danh sách nội thất cho loại căn hộ này.</p>
                </div>
            @endif
        </div>

        {{-- Right Column --}}
        <div class="info-card">
            <h3 class="info-card-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#8b5cf6" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Căn hộ thuộc loại này
            </h3>
            
            @php
                $apartments = $apartmentType->apartments()->take(5)->get();
                $total = $apartmentType->apartments()->count();
            @endphp

            @if($apartments->count() > 0)
                <div class="assoc-apartments">
                    @foreach($apartments as $apt)
                        <div class="assoc-apt-item">
                            <span class="apt-number">P. {{ $apt->apartment_number }}</span>
                            <span class="apt-status 
                                @if($apt->status == 'occupied') status-occupied
                                @elseif($apt->status == 'vacant') status-vacant
                                @else status-maintenance @endif
                            ">
                                @if($apt->status == 'occupied') Đang ở
                                @elseif($apt->status == 'vacant') Trống
                                @else Bảo trì @endif
                            </span>
                        </div>
                    @endforeach
                </div>
                @if($total > 5)
                    <div style="text-align: center; margin-top: 16px;">
                        <a href="{{ portal_route('apartments.index') }}?type={{ $apartmentType->id }}" style="color: #0b57d0; font-size: 14px; font-weight: 500; text-decoration: none;">
                            Xem tất cả {{ $total }} căn hộ →
                        </a>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <p style="margin: 0;">Chưa có căn hộ nào.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
