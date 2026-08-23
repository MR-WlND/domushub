@extends('layouts.resident.master')

@section('title', 'Quản lý thành viên căn hộ')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/pages/resident/members/index.css'])
@endpush

@section('content')
    <section class="resident-members-page">
        <!-- Header -->
        <div class="res-header">
            <div>
                <div class="res-header__breadcrumb">
                    <a href="{{ route('resident.dashboard') }}">Cư dân</a> &gt; Quản lý thành viên
                </div>
                <h1 class="res-header__title">Quản lý thành viên & Nhân khẩu</h1>
            </div>
            <div class="res-header__actions">
                <button class="btn btn-add-member" onclick="openInviteModal()">
                    <i class="fa-solid fa-plus"></i> Thêm thành viên
                </button>
            </div>
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
                <div class="stats-card__icon stats-card__icon--green">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div class="stats-card__data">
                    <span class="stats-card__label">Số nhân khẩu khai báo</span>
                    <h3 class="stats-card__value">{{ $declaredMembers->count() }}</h3>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-card__icon stats-card__icon--light-blue">
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
                <button type="button" class="tab-link tab-link--active" data-target="registered-tab">
                    <i class="fa-solid fa-circle-user"></i> Cư dân liên kết
                </button>
                
                <button type="button" class="tab-link" data-target="declared-tab">
                    <i class="fa-solid fa-address-book"></i> Nhân khẩu khai báo
                </button>

                @if ($isOwner)
                    <button type="button" class="tab-link" data-target="invitations-tab">
                        <i class="fa-solid fa-key"></i> Mã mời gia đình
                        @if($activeInvitesCount > 0)
                            <span class="tab-badge tab-badge--blue">{{ $activeInvitesCount }}</span>
                        @endif
                    </button>
                @endif
            </nav>
        </div>

        <!-- Tab Content dynamically loaded from partials -->
        <div class="tab-content-wrapper">
            <div id="registered-tab" class="tab-pane active">
                @include('resident.members.partials.registered')
            </div>
            <div id="declared-tab" class="tab-pane" style="display: none;">
                @include('resident.members.partials.declared')
            </div>
            @if ($isOwner)
                <div id="invitations-tab" class="tab-pane" style="display: none;">
                    @include('resident.members.partials.invitations')
                </div>
            @endif
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabLinks = document.querySelectorAll('.tab-link');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Remove active class from all links and hide all panes
                    tabLinks.forEach(l => l.classList.remove('tab-link--active'));
                    tabPanes.forEach(p => p.style.display = 'none');
                    
                    // Add active class to clicked link
                    this.classList.add('tab-link--active');
                    
                    // Show target pane
                    const targetId = this.getAttribute('data-target');
                    document.getElementById(targetId).style.display = 'block';
                });
            });
        });
    </script>
    @endpush
@endsection
