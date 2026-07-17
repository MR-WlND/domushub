@extends('layouts.resident.master')

@section('title', 'Quản lý thành viên căn hộ')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/pages/resident/members/index.css'])
@endpush

@section('content')
    <section class="resident-members-page">
        <!-- Header -->
        <div class="page-header">
            <p class="page-eyebrow">Hộ gia đình</p>
            <h1>Quản lý thành viên & Nhân khẩu</h1>
            <p class="page-subtitle">Xem danh sách cư dân liên kết, khai báo nhân khẩu gia đình và tạo mã mời thành viên mới.</p>
        </div>

        <!-- Thống kê nhanh (Stats Cards) -->
        <div class="stats-grid">
            <div class="stats-card">
                <div class="stats-card__icon stats-card__icon--blue">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stats-card__data">
                    <span class="stats-card__label">Tổng số nhân khẩu</span>
                    <h3 class="stats-card__value">{{ $totalMembersCount }}</h3>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-card__icon stats-card__icon--amber">
                    <i class="fa-solid fa-address-book"></i>
                </div>
                <div class="stats-card__data">
                    <span class="stats-card__label">Số nhân khẩu khai báo</span>
                    <h3 class="stats-card__value">{{ $declaredMembers->count() }}</h3>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-card__icon stats-card__icon--emerald">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <div class="stats-card__data">
                    <span class="stats-card__label">Mã mời hoạt động</span>
                    <h3 class="stats-card__value">{{ $activeInvitesCount }}</h3>
                </div>
            </div>
        </div>

        <!-- Flash Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Banner cảnh báo Cư dân thường -->
        @if (!$isOwner)
            <div class="alert alert-warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <strong>Lưu ý quan trọng:</strong> Bạn đang truy cập với vai trò <strong>Cư dân thường / Người thuê</strong>. Chỉ có <strong>Chủ hộ (Owner)</strong> mới có quyền khai báo nhân khẩu gia đình, xóa thành viên hoặc tạo mã mời mới.
                </div>
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="tabs-container">
            <nav class="tabs-nav" aria-label="Members Navigation">
                <a href="{{ route('resident.members.index', ['tab' => 'registered']) }}" 
                   class="tab-link {{ $activeTab === 'registered' ? 'tab-link--active' : '' }}">
                    <i class="fa-solid fa-circle-user"></i> Cư dân liên kết
                </a>
                
                <a href="{{ route('resident.members.index', ['tab' => 'declared']) }}" 
                   class="tab-link {{ $activeTab === 'declared' ? 'tab-link--active' : '' }}">
                    <i class="fa-solid fa-address-book"></i> Nhân khẩu khai báo
                </a>

                @if ($isOwner)
                    <a href="{{ route('resident.members.index', ['tab' => 'invitations']) }}" 
                       class="tab-link {{ $activeTab === 'invitations' ? 'tab-link--active' : '' }}">
                        <i class="fa-solid fa-key"></i> Mã mời gia đình
                        @if($activeInvitesCount > 0)
                            <span class="tab-badge tab-badge--emerald">{{ $activeInvitesCount }}</span>
                        @endif
                    </a>
                @endif
            </nav>
        </div>

        <!-- Tab Content dynamically loaded from partials -->
        <div class="tab-content-wrapper">
            @include('resident.members.partials.' . $activeTab)
        </div>
    </section>
@endsection
