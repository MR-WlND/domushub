@extends('layouts.resident.master')

@section('title', 'Chi tiết thành viên')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/pages/resident/members/show.css'])
@endpush

@section('content')
<section class="resident-member-detail">
    <div class="res-header">
        <h1 class="res-header__title">Hồ sơ thành viên</h1>
        <a href="{{ route('resident.members.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
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

    @php
        $name = $type === 'registered' ? ($member->user->name ?? 'Cư dân') : $member->name;
        $isLinked = $type === 'registered' || ($type === 'declared' && $member->invite_id && $member->invite && ($member->invite->uses_count > 0 || $member->invite->status == 'expired'));
        $avatarBg = $type === 'registered' ? '#f1f5f9' : '#fef2f2';
        $avatarColor = $type === 'registered' ? '#475569' : '#ef4444';

        $displayRelationship = $member->relationship;
        if ($displayRelationship === 'family_member' || $displayRelationship === 'Thành viên gia đình') {
            $displayRelationship = 'Gia đình';
        } elseif ($displayRelationship === 'owner') {
            $displayRelationship = 'Chủ hộ';
        } elseif ($displayRelationship === 'tenant') {
            $displayRelationship = 'Người thuê';
        }
    @endphp

    <div class="cards-stack">
        <!-- Card 1: Header Profile -->
        <div class="detail-card profile-header-card">
            <div class="profile-header-card__left">
                <div class="profile-avatar" style="background-color: {{ $avatarBg }}; color: {{ $avatarColor }};">
                    {{ mb_substr($name, 0, 1) }}
                </div>
                <div class="profile-info">
                    <h2>{{ $name }}</h2>
                    <div class="profile-badges">
                        @if ($isLinked)
                            <span class="badge badge-status--verified"><i class="fa-solid fa-check"></i> Đã có tài khoản</span>
                        @else
                            <span class="badge badge-status--pending"><i class="fa-regular fa-clock"></i> Chưa liên kết</span>
                        @endif
                        <span class="badge badge--relationship">{{ $displayRelationship ?? 'Khác' }}</span>
                    </div>
                </div>
            </div>
            <div class="profile-header-card__right">
                <button class="btn btn-primary">
                    <i class="fa-solid fa-pen"></i> Chỉnh sửa
                </button>
            </div>
        </div>

        <!-- Card 2: Basic Info -->
        <div class="detail-card">
            <div class="detail-card__header">
                <h3>Thông tin cơ bản</h3>
            </div>
            <div class="detail-card__body">
                <div class="info-grid">
                    <div class="info-group">
                        <span class="info-label">Căn hộ</span>
                        <span class="info-value">
                            <span class="apartment-tag">
                                {{ $member->apartment->apartment_number ?? '—' }} ({{ $member->apartment->floor->block->name ?? '' }})
                            </span>
                        </span>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Vai trò</span>
                        <span class="info-value">{{ $displayRelationship ?? 'Khác' }}</span>
                    </div>

                    @if ($type === 'registered')
                        <div class="info-group">
                            <span class="info-label">Số điện thoại</span>
                            <span class="info-value">{{ $member->user->phone ?? '—' }}</span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $member->user->email ?? '—' }}</span>
                        </div>
                    @else
                        <div class="info-group">
                            <span class="info-label">Năm sinh</span>
                            <span class="info-value">{{ $member->birth_year ?? '—' }}</span>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Ngày khai báo</span>
                            <span class="info-value">{{ $member->created_at ? $member->created_at->format('d/m/Y') : '—' }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 3: Invite Info (if applicable) -->
        @if ($type === 'declared' && $member->invite_id && $member->invite && $member->invite->uses_count == 0 && $member->invite->status == 'active')
            <div class="detail-card">
                <div class="detail-card__header">
                    <h3>Thông tin mã mời</h3>
                </div>
                <div class="detail-card__body">
                    <div class="invite-box-modern">
                        <div class="invite-box-modern__top">
                            <span class="text-muted">Mã liên kết:</span>
                        </div>
                        <div class="invite-box-modern__main">
                            <code class="invite-code" id="memberInviteCode">{{ $member->invite->invite_code }}</code>
                            <button class="btn btn-outline-secondary btn-sm" onclick="copyInviteCode()" id="copyBtn">
                                <i class="fa-regular fa-copy"></i> <span>Sao chép mã</span>
                            </button>
                        </div>
                        <div class="invite-box-modern__bottom">
                            <div class="expiry-text"><i class="fa-regular fa-clock"></i> Hết hạn: {{ $member->invite->expired_at ? \Carbon\Carbon::parse($member->invite->expired_at)->format('d/m/Y H:i') : 'Không hạn' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Card 4: Actions -->
        @if ($isOwner && $type === 'declared' && (!$member->invite_id || ($member->invite && $member->invite->uses_count == 0 && $member->invite->status == 'expired')))
            <div class="detail-card">
                <div class="detail-card__header">
                    <h3>Thao tác quản lý</h3>
                </div>
                <div class="detail-card__body">
                    <div class="action-buttons">
                        <form action="{{ route('resident.members.declared.generate-invite', $member->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-key"></i> Tạo mã mời liên kết
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
    function copyInviteCode() {
        const code = document.getElementById('memberInviteCode').textContent.trim();
        const btn = document.getElementById('copyBtn');
        navigator.clipboard.writeText(code).then(() => {
            btn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Đã sao chép!</span>';
            btn.style.color = '#10b981';
            setTimeout(() => {
                btn.innerHTML = '<i class="fa-regular fa-copy"></i> <span>Sao chép mã</span>';
                btn.style.color = '';
            }, 3000);
        });
    }
</script>
@endpush
@endsection
