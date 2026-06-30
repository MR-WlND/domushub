@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Căn hộ ' . $apartment->apartment_number)
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/apartments/index.css', 'resources/css/pages/admin/apartments/show.css'])
@endpush

@section('content')
<div class="apartments-page">

    {{-- Breadcrumb Navigation --}}
    <nav class="breadcrumb-nav">
        <a href="{{ route('admin.dashboard') }}">Trang chủ</a>
        <span class="divider">/</span>
        <a href="{{ route('admin.apartments.index') }}">Căn hộ</a>
        <span class="divider">/</span>
        <span class="current">Căn hộ {{ $apartment->apartment_number }}</span>
    </nav>

    {{-- Header --}}
    <div class="apartments-page__header">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <h1>Căn hộ {{ $apartment->apartment_number }}</h1>
                <span class="apt-status apt-status--{{ $apartment->status }}">
                    @if ($apartment->status == 'occupied')
                        Đang ở
                    @elseif($apartment->status == 'vacant')
                        Trống
                    @else
                        Bảo trì
                    @endif
                </span>
            </div>
            <p class="apartments-page__subtitle">
                {{ $apartment->floor->block->name }} —
                {{ $apartment->floor->name ?? 'Tầng ' . $apartment->floor->floor_number }}
            </p>
        </div>

        <div class="apartments-page__actions">
            <a href="{{ route('admin.apartments.edit', $apartment) }}" class="apts-button apts-button--primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Chỉnh sửa
            </a>
            <form action="{{ route('admin.apartments.destroy', $apartment) }}" method="POST"
                onsubmit="return confirm('Bạn có chắc chắn muốn xóa căn hộ này?')" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="apts-button apts-button--delete">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Xóa căn hộ
                </button>
            </form>
            <a href="{{ route('admin.apartments.index') }}" class="apts-button apts-button--edit">
                ← Quay lại
            </a>
        </div>
    </div>

    {{-- Success Alert --}}
    @if ($message = Session::get('success'))
        <div class="apartments-alert apartments-alert--success">
            {{ $message }}
        </div>
    @endif

    {{-- Detail Grid --}}
    <div class="detail-grid">
        {{-- Column Trái: Thông tin chung --}}
        <div class="detail-col-info">
            <article class="dashboard-card shadow-sm border-light" style="padding: 24px; margin-bottom: 0;">
                <div class="card-header-custom">
                    <div class="header-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                            viewBox="0 0 24 24" stroke="#0b57d0" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3>Thông tin căn hộ</h3>
                </div>

                <div class="card-body-custom">
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
                            <span class="item-label">Trạng thái</span>
                            <span class="item-value">
                                @if ($apartment->status == 'occupied')
                                    <span class="badge-success-filled">Đang có người ở</span>
                                @elseif($apartment->status == 'vacant')
                                    <span class="badge-warning-filled">Đang trống</span>
                                @else
                                    <span class="badge-danger-filled">Bảo trì hệ thống</span>
                                @endif
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="item-label">Số cư dân</span>
                            <span class="item-value">
                                <span class="count-pill">{{ $apartment->residents->count() + ($declaredMembers->count() ?? 0) }} cư dân</span>
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
                <div class="card-header-custom"
                    style="justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div class="header-icon" style="background-color: #f0fdf4;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
                                viewBox="0 0 24 24" stroke="#15803d" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3>Danh sách cư dân sinh sống</h3>
                    </div>
                    <span class="count-pill">{{ $apartment->residents->count() }} cư dân</span>
                </div>

                <div class="card-body-custom">
                    @if ($apartment->residents->count() > 0)
                        <div class="table-container">
                            <table class="premium-table">
                                <thead>
                                    <tr>
                                        <th>Họ & Tên</th>
                                        <th>Thông tin liên hệ</th>
                                        <th>Vai trò</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($apartment->residents as $resident)
                                        @php $user = $resident->user; @endphp
                                        <tr>
                                            <td>
                                                <div class="resident-profile">
                                                    <div class="avatar-circle">
                                                        {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                                                    </div>
                                                    <div class="resident-name-info">
                                                        <div class="name">{{ $user->name ?? 'N/A' }}</div>
                                                        <div class="sub">Thành viên căn hộ</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="contact-info">
                                                    <div class="email" title="{{ $user->email ?? '' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $user->email ?? '—' }}
                                                    </div>
                                                    <div class="phone">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                        </svg>
                                                        {{ $user->phone ?? '—' }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if (($resident->relationship ?? '') === 'owner')
                                                    <span class="badge-relationship owner">Chủ hộ</span>
                                                @elseif(($resident->relationship ?? '') === 'tenant')
                                                    <span class="badge-relationship tenant">Người thuê</span>
                                                @else
                                                    <span class="badge-relationship family">Thành viên gia đình</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (($user->status ?? 'active') == 'active')
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none"
                                    viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <h4>Chưa có cư dân sinh sống</h4>
                            <p>Căn hộ này hiện đang trống hoặc chưa được liên kết với bất kỳ tài khoản cư dân nào trên hệ thống.</p>
                            <div class="empty-actions">
                                <a href="{{ route('admin.invitations.index') }}" class="apts-button apts-button--primary">
                                    + Tạo mã mời cư dân
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Nhân khẩu khai báo --}}
                @if(isset($declaredMembers) && $declaredMembers->count() > 0)
                <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                        <h4 style="margin:0;font-size:0.9rem;font-weight:700;color:#475569;">Nhân khẩu khai báo</h4>
                        <span class="count-pill">{{ $declaredMembers->count() }} người</span>
                    </div>
                    <div class="table-container">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Họ & Tên</th>
                                    <th>Năm sinh</th>
                                    <th>Quan hệ</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($declaredMembers as $member)
                                <tr>
                                    <td>
                                        <div class="resident-profile">
                                            <div class="avatar-circle" style="background:#f59e0b;">
                                                {{ strtoupper(substr($member->name ?? '?', 0, 1)) }}
                                            </div>
                                            <div class="resident-name-info">
                                                <div class="name">{{ $member->name }}</div>
                                                <div class="sub">Nhân khẩu khai báo</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $member->birth_year ?? '—' }}</td>
                                    <td>
                                        @switch($member->relationship)
                                            @case('spouse') <span class="badge-relationship owner">Vợ/Chồng</span> @break
                                            @case('child') <span class="badge-relationship family">Con</span> @break
                                            @case('parent') <span class="badge-relationship family">Cha/Mẹ</span> @break
                                            @case('sibling') <span class="badge-relationship family">Anh/Chị/Em</span> @break
                                            @default <span class="badge-relationship family">{{ $member->relationship ?? 'Khác' }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($member->status === 'verified')
                                            <span class="status-indicator-badge active"><span class="indicator-dot"></span>Đã xác minh</span>
                                        @else
                                            <span class="status-indicator-badge inactive"><span class="indicator-dot"></span>Chờ xác minh</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </article>
        </div>
    </div>
</div>
@endsection
