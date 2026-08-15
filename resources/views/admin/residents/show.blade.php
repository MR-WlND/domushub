@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Hồ sơ Cư dân')

@push('styles')
<style>
    .res-profile {
        max-width: 1100px;
        margin: 0 auto;
        font-family: 'Inter', sans-serif;
        color: #1e293b;
    }

    /* Header */
    .res-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
    }

    .res-header__breadcrumb {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 8px;
    }

    .res-header__breadcrumb a {
        color: #64748b;
        text-decoration: none;
    }

    .res-header__breadcrumb a:hover {
        color: #2563eb;
    }

    .res-header__title {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .res-header__actions {
        display: flex;
        gap: 12px;
    }

    .res-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }

    .res-btn--outline {
        background: #fff;
        border: 1px solid #cbd5e1;
        color: #475569;
    }

    .res-btn--outline:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #1e293b;
    }

    .res-btn--primary {
        background: #002f5a; /* Đổi màu xanh đen cho nút Chỉnh sửa */
        border: 1px solid #002f5a;
        color: #fff;
    }

    .res-btn--primary:hover {
        background: #001f3f;
    }

    /* Card Box */
    .res-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.02);
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid #f1f5f9;
    }

    .res-card__title {
        font-size: 16px;
        font-weight: 700;
        color: #002f5a;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0 0 20px 0;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        justify-content: space-between;
    }

    .res-card__title i {
        color: #3b82f6;
    }

    .res-card__title-right {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
    }

    .res-card__title-right a {
        color: #2563eb;
        text-decoration: none;
    }

    /* Hero */
    .res-hero {
        display: flex;
        align-items: center;
        gap: 32px;
    }

    .res-hero__avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e0e7ff;
    }

    .res-hero__info h2 {
        font-size: 22px;
        font-weight: 700;
        color: #002f5a;
        margin: 0 0 16px 0;
    }

    .res-hero__details {
        display: flex;
        gap: 40px;
        color: #475569;
        font-size: 14px;
    }

    .res-hero__item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .res-hero__item i {
        color: #94a3b8;
    }

    .res-hero__item strong {
        color: #0f172a;
    }

    /* Grid Layout */
    .res-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 24px;
        align-items: start;
    }

    /* Info Grid */
    .res-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px 16px;
    }

    .res-info-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .res-info-label {
        font-size: 12px;
        color: #64748b;
    }

    .res-info-value {
        font-size: 14px;
        color: #1e293b;
        font-weight: 500;
    }

    .res-info-item--full {
        grid-column: 1 / -1;
    }

    /* Tables */
    .res-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .res-table th {
        text-align: left;
        padding: 12px;
        background: #f8fafc;
        color: #475569;
        font-weight: 600;
        border-bottom: 1px solid #e2e8f0;
    }

    .res-table td {
        padding: 12px;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .res-table tr:last-child td {
        border-bottom: none;
    }

    .res-table-badge {
        background: #e0e7ff;
        color: #4338ca;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    /* List Items (Vehicles, Invoices) */
    .res-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .res-list-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
    }

    .res-list-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .res-list-item__icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
        font-size: 20px;
    }

    .res-list-item__icon--car { background: #dbeafe; color: #1d4ed8; }
    .res-list-item__icon--moto { background: #f1f5f9; color: #475569; }

    .res-list-item__content {
        flex: 1;
    }

    .res-list-item__title {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 2px 0;
    }

    .res-list-item__desc {
        font-size: 12px;
        color: #64748b;
        margin: 0;
    }

    .res-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
    }

    .res-badge--success {
        background: #dcfce7;
        color: #16a34a;
    }

    .res-badge--danger {
        background: #fef2f2;
        color: #ef4444;
    }

    .res-badge--warning {
        background: #fffbeb;
        color: #f59e0b;
    }

    .res-invoice-amount {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        text-align: right;
        margin-bottom: 4px;
    }
</style>
@endpush

@section('content')
<div class="res-profile">

    {{-- Header --}}
    <div class="res-header">
        <div>
            <div class="res-header__breadcrumb">
                <a href="{{ route('admin.residents.index') }}">Cư dân</a> &gt; Hồ sơ chi tiết
            </div>
            <h1 class="res-header__title">Hồ sơ Cư dân</h1>
        </div>
        <div class="res-header__actions">
            <button class="res-btn res-btn--outline">
                <i class="fa-solid fa-download"></i> Xuất dữ liệu
            </button>
            <a href="#" class="res-btn res-btn--primary">
                <i class="fa-solid fa-pen"></i> Chỉnh sửa
            </a>
        </div>
    </div>

    {{-- Hero --}}
    <div class="res-card res-hero">
        @if($user->avatar)
            <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="res-hero__avatar">
        @else
            <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar" class="res-hero__avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=e0e7ff&color=4338ca&size=100'">
        @endif
        
        <div class="res-hero__info">
            <h2>{{ $user->name }}</h2>
            <div class="res-hero__details">
                <div class="res-hero__item">
                    <i class="fa-solid fa-building"></i>
                    <span>Căn hộ: <strong>{{ $user->apartment->floor->block->name ?? '' }} - {{ $user->apartment->apartment_number ?? '' }}</strong></span>
                </div>
                <div class="res-hero__item">
                    <i class="fa-regular fa-calendar"></i>
                    <span>Ngày chuyển đến: <strong>{{ $residentInfo ? \Carbon\Carbon::parse($residentInfo->start_date)->format('d/m/Y') : '—' }}</strong></span>
                </div>
                <div class="res-hero__item">
                    <i class="fa-regular fa-id-badge"></i>
                    <span>Vai trò: <strong>{{ $residentInfo ? \App\Helpers\Helper::translateRelationship($residentInfo->relationship) : '—' }}</strong></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="res-grid">
        
        {{-- Left Column --}}
        <div>
            <div class="res-card">
                <div class="res-card__title">
                    <div><i class="fa-regular fa-user"></i> Thông tin cá nhân</div>
                </div>
                <div class="res-info-grid">
                    <div class="res-info-item">
                        <span class="res-info-label">Số điện thoại</span>
                        <span class="res-info-value">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div class="res-info-item">
                        <span class="res-info-label">Email</span>
                        <span class="res-info-value">{{ $user->email ?? '—' }}</span>
                    </div>
                    <div class="res-info-item">
                        <span class="res-info-label">Số CCCD/CMND</span>
                        <span class="res-info-value">{{ $user->cccd ?? '—' }}</span>
                    </div>
                    <div class="res-info-item">
                        <span class="res-info-label">Ngày sinh</span>
                        <span class="res-info-value">{{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('d/m/Y') : '—' }}</span>
                    </div>
                    <div class="res-info-item res-info-item--full">
                        <span class="res-info-label">Quê quán / Địa chỉ</span>
                        <span class="res-info-value">{{ $user->address ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="res-card" style="padding-bottom: 0; overflow: hidden;">
                <div class="res-card__title" style="margin-bottom: 0;">
                    <div><i class="fa-solid fa-users"></i> Danh sách thành viên</div>
                    <div class="res-badge" style="background: #eef2ff; color: #4f46e5;">{{ $familyMembers->count() }} người</div>
                </div>
                <table class="res-table">
                    <thead>
                        <tr>
                            <th>Họ tên</th>
                            <th>Quan hệ</th>
                            <th>Ngày sinh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($familyMembers as $member)
                        <tr>
                            <td>{{ $member->user->name ?? '—' }}</td>
                            <td>{{ \App\Helpers\Helper::translateRelationship($member->relationship) }}</td>
                            <td>{{ $member->user && $member->user->birthday ? \Carbon\Carbon::parse($member->user->birthday)->format('d/m/Y') : '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #94a3b8;">Không có thành viên nào khác</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right Column --}}
        <div>
            <div class="res-card">
                <div class="res-card__title">
                    <div><i class="fa-solid fa-car"></i> Phương tiện đăng ký</div>
                </div>
                <div class="res-list">
                    @forelse($vehicles as $vehicle)
                    <div class="res-list-item">
                        <div class="res-list-item__icon {{ $vehicle->vehicle_type === 'car' ? 'res-list-item__icon--car' : 'res-list-item__icon--moto' }}">
                            <i class="fa-solid {{ $vehicle->vehicle_type === 'car' ? 'fa-car' : 'fa-motorcycle' }}"></i>
                        </div>
                        <div class="res-list-item__content">
                            <h4 class="res-list-item__title">{{ $vehicle->license_plate }}</h4>
                            <p class="res-list-item__desc">{{ $vehicle->typeLabel() }} - {{ $vehicle->brand }}</p>
                        </div>
                        <div>
                            <span class="res-badge res-badge--success">Thẻ hoạt động</span>
                        </div>
                    </div>
                    @empty
                    <div style="text-align: center; color: #94a3b8; font-size: 13px;">Không có phương tiện</div>
                    @endforelse
                </div>
            </div>

            <div class="res-card">
                <div class="res-card__title">
                    <div><i class="fa-solid fa-file-invoice"></i> Hóa đơn gần nhất</div>
                    <div class="res-card__title-right">
                        <a href="{{ route('admin.invoices.index', ['apartment_id' => $user->apartment_id]) }}">Xem tất cả</a>
                    </div>
                </div>
                <div class="res-list">
                    @forelse($invoices as $invoice)
                    <div class="res-list-item">
                        <div class="res-list-item__content">
                            <h4 class="res-list-item__title">{{ $invoice->title }}</h4>
                            <p class="res-list-item__desc">Mã HĐ: {{ $invoice->invoice_code }}</p>
                        </div>
                        <div style="text-align: right;">
                            <div class="res-invoice-amount">{{ number_format($invoice->total_amount) }} đ</div>
                            <span class="res-badge {{ $invoice->status === 'paid' ? 'res-badge--success' : ($invoice->status === 'unpaid' ? 'res-badge--danger' : 'res-badge--warning') }}">
                                {{ \App\Models\Invoice::statusLabel($invoice->status) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div style="text-align: center; color: #94a3b8; font-size: 13px;">Không có hóa đơn</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
