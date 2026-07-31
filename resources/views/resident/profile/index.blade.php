@extends('layouts.resident.master')

@section('title', 'Hồ sơ cá nhân - DomusHub')

@push('styles')
    @vite(['resources/css/pages/resident/profile/index.css'])
@endpush

@section('content')
<section class="resident-profile-page">

    {{-- ALERT CONTAINER --}}
    <div id="rp-alert-container"></div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- HERO CARD --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="rp-hero">
        <div class="rp-hero__left">
            <div class="rp-hero__avatar" onclick="document.getElementById('avatar-input').click()">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" id="rp-avatar-img">
                @else
                    <div class="rp-hero__avatar-placeholder" id="rp-avatar-placeholder">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <img src="" alt="" id="rp-avatar-img" style="display:none;">
                @endif
                <div class="rp-hero__avatar-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4" fill="#fff"/></svg>
                </div>
            </div>
            <div class="rp-hero__info">
                <h1 class="rp-hero__name" id="rp-display-name">{{ $user->name }}</h1>
                <p class="rp-hero__apartment">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    @if($apartments->isNotEmpty())
                        @php $apt = $apartments->first(); @endphp
                        Căn hộ {{ $apt->apartment_number }} • {{ $apt->floor->block->name ?? '' }}
                    @else
                        Chưa có căn hộ
                    @endif
                </p>
                <div class="rp-hero__badges">
                    @if($selfResident && $selfResident->relationship)
                        <span class="rp-badge rp-badge--role">
                            @switch($selfResident->relationship)
                                @case('owner') Chủ sở hữu @break
                                @case('spouse') Vợ/Chồng @break
                                @case('child') Con @break
                                @case('tenant') Người thuê @break
                                @default Cư dân
                            @endswitch
                        </span>
                    @else
                        <span class="rp-badge rp-badge--role">Cư dân</span>
                    @endif
                    <span class="rp-badge rp-badge--time">Thành viên từ {{ $user->created_at->format('Y') }}</span>
                </div>
            </div>
        </div>
        <div class="rp-hero__right">
            <span class="rp-hero__balance-label">SỐ DƯ NỢ HIỆN TẠI</span>
            <span class="rp-hero__balance-value {{ $outstandingBalance > 0 ? 'rp-hero__balance-value--debt' : '' }}">
                {{ number_format($outstandingBalance, 0, ',', '.') }}
            </span>
            <span class="rp-hero__balance-unit">VNĐ</span>
        </div>
    </div>

    {{-- Hidden file input for avatar --}}
    <form id="avatar-form" style="display:none;">@csrf
        <input type="file" id="avatar-input" name="avatar" accept="image/*" onchange="previewAndUploadAvatar(this)">
    </form>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- 2 COLUMNS: Thành viên + Phương tiện --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="rp-grid">

        {{-- LEFT: Quản lý thành viên --}}
        <div class="rp-card">
            <div class="rp-card__header">
                <div class="rp-card__header-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <h2>Quản lý thành viên</h2>
                </div>
                <a href="{{ route('resident.members.index') }}" class="rp-card__header-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Thêm thành viên
                </a>
            </div>
            <div class="rp-card__body rp-card__body--flush">
                @if($registeredMembers->isEmpty() && $declaredMembers->isEmpty())
                    <div class="rp-empty"><p>Chưa có thành viên nào</p></div>
                @else
                    <table class="rp-members-table">
                        <thead>
                            <tr><th>Thành viên</th><th>Vai trò</th><th>Trạng thái</th></tr>
                        </thead>
                        <tbody>
                            @foreach($registeredMembers as $member)
                            <tr>
                                <td>
                                    <div class="rp-member-cell">
                                        <div class="rp-member-avatar">{{ mb_substr($member->user->name ?? '?', 0, 1) }}</div>
                                        <div>
                                            <span class="rp-member-name">{{ $member->user->name ?? 'N/A' }}</span>
                                            <span class="rp-member-rel">@switch($member->relationship)@case('spouse') Vợ/Chồng @break @case('child') Con @break @case('parent') Cha/Mẹ @break @case('sibling') Anh/Chị/Em @break @case('tenant') Người thuê @break @default {{ $member->relationship ?? '' }} @endswitch</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="rp-role-tag">Cư dân</span></td>
                                <td><span class="rp-verify-badge rp-verify-badge--yes">Đã xác minh</span></td>
                            </tr>
                            @endforeach
                            @foreach($declaredMembers as $member)
                            <tr>
                                <td>
                                    <div class="rp-member-cell">
                                        <div class="rp-member-avatar rp-member-avatar--alt">{{ mb_substr($member->name ?? '?', 0, 1) }}</div>
                                        <div>
                                            <span class="rp-member-name">{{ $member->name }}</span>
                                            <span class="rp-member-rel">@switch($member->relationship)@case('spouse') Vợ/Chồng @break @case('child') Con @break @case('parent') Cha/Mẹ @break @case('sibling') Anh/Chị/Em @break @default {{ $member->relationship ?? 'Khác' }} @endswitch</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="rp-role-tag">Nhân khẩu</span></td>
                                <td>
                                    @if($member->status === 'verified')
                                        <span class="rp-verify-badge rp-verify-badge--yes">Đã xác minh</span>
                                    @else
                                        <span class="rp-verify-badge rp-verify-badge--pending">Chờ xác minh</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- RIGHT: Quản lý phương tiện (with QR) --}}
        <div class="rp-card">
            <div class="rp-card__header">
                <div class="rp-card__header-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    <h2>Phương tiện</h2>
                </div>
                <a href="{{ route('resident.vehicles.create') }}" class="rp-card__header-btn rp-card__header-btn--icon" title="Đăng ký phương tiện">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </a>
            </div>
            <div class="rp-card__body">
                @if($vehicles->isEmpty())
                    <div class="rp-empty"><p>Chưa đăng ký phương tiện nào</p></div>
                @else
                    <div class="rp-vehicles-list">
                        @foreach($vehicles as $v)
                        <div class="rp-vehicle-item">
                            <div class="rp-vehicle-main">
                                <div class="rp-vehicle-icon rp-vehicle-icon--{{ $v->vehicle_type }}">
                                    @if($v->vehicle_type === 'car')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
                                    @elseif($v->vehicle_type === 'electric_bike')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M12 17h5M5 17h2M12 12l2.5-5.5H19l-3 7H9.7L7 11.5"/></svg>
                                    @endif
                                </div>
                                <div class="rp-vehicle-info">
                                    <span class="rp-vehicle-name">{{ $v->brand ?? $v->license_plate }}</span>
                                    <span class="rp-vehicle-plate">{{ $v->license_plate }}</span>
                                </div>
                                <span class="rp-vehicle-type">@switch($v->vehicle_type)@case('car') Ô tô @break @case('electric_bike') Xe điện @break @default Xe máy @endswitch</span>
                            </div>

                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- QUẢN LÝ CƯ TRÚ --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if($apartments->isNotEmpty())
        <div class="rp-card rp-card--full" style="border-top: 4px solid #ef4444;">
            <div class="rp-card__header">
                <div class="rp-card__header-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <h2>Quản lý cư trú</h2>
                </div>
            </div>
            <div class="rp-card__body" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                <div>
                    <p style="margin: 0; font-size: 14px; color: #4b5563;">
                        Bạn đang tham gia căn hộ <strong>{{ $apartments->first()->apartment_number }}</strong> ({{ $apartments->first()->floor->block->name ?? '' }}).
                    </p>
                    <p style="margin: 4px 0 0 0; font-size: 12px; color: #6b7280;">
                        Lưu ý: Nếu bạn là chủ hộ và muốn rời khỏi căn hộ, bạn phải chuyển quyền chủ sở hữu cho một thành viên khác trước.
                    </p>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    @if($selfResident && $selfResident->relationship === 'owner' && $registeredMembers->count() > 0)
                        <button type="button" class="rp-btn" style="background-color: #f59e0b; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-weight: 500; font-size: 14px;" onclick="openTransferModal()">
                            <i class="fa-solid fa-crown"></i> Chuyển quyền Chủ hộ
                        </button>
                    @endif
                    <form action="{{ route('resident.profile.leave') }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn rời khỏi căn hộ này? Mọi quyền truy cập của bạn sẽ bị thu hồi ngay lập tức.');" style="margin: 0;">
                        @csrf
                        <button type="submit" class="rp-btn rp-btn--danger" style="background-color: #ef4444; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-weight: 500; font-size: 14px;">
                            <i class="fa-solid fa-right-from-bracket"></i> Rời khỏi căn hộ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL CHUYỂN QUYỀN CHỦ HỘ --}}
    <div id="transferModal" class="invite-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div class="invite-modal" style="background: white; padding: 24px; border-radius: 12px; max-width: 450px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <div class="invite-modal__header" style="text-align: center; margin-bottom: 20px;">
                <div class="invite-modal__icon-wrap" style="background: #fef3c7; color: #d97706; font-size: 32px; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                    <i class="fa-solid fa-crown"></i>
                </div>
                <h3 class="invite-modal__title" style="font-size: 18px; font-weight: 600; color: #1f2937; margin: 0 0 6px 0;">Chuyển quyền Chủ hộ</h3>
                <p class="invite-modal__subtitle" style="font-size: 13px; color: #6b7280; margin: 0;">Bàn giao quyền quản lý và thanh toán căn hộ cho thành viên khác.</p>
            </div>

            {{-- Form chọn cư dân --}}
            <div id="transfer-step-select">
                <div class="rp-form-group" style="margin-bottom: 16px;">
                    <label class="rp-label" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Chọn thành viên nhận bàn giao</label>
                    <select id="transfer-target" class="rp-input" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;">
                        <option value="">-- Chọn thành viên --</option>
                        @foreach($registeredMembers as $member)
                            <option value="{{ $member->user->id }}">{{ $member->user->name }} ({{ $member->user->phone ?? '—' }})</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeTransferModal()" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #d1d5db; background: none; cursor: pointer;">Hủy</button>
                    <button type="button" class="rp-btn" onclick="sendTransferOtp()" style="padding: 8px 16px; border-radius: 6px; background: #3b82f6; color: white; border: none; cursor: pointer;">Gửi mã OTP</button>
                </div>
            </div>

            {{-- Form xác nhận OTP --}}
            <div id="transfer-step-otp" style="display: none;">
                <div id="otp-demo-alert" class="rp-alert rp-alert--success" style="margin-bottom: 12px; padding: 10px; border-radius: 6px; font-size: 13px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; display: block; border-left: 4px solid #10b981; text-align: left;">
                    <strong>[DEMO ONLY]</strong> Mã OTP chuyển quyền của bạn là: <strong id="otp-demo-code" style="text-decoration: underline;">------</strong>
                </div>
                <div class="rp-form-group" style="margin-bottom: 16px;">
                    <label class="rp-label" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Nhập mã xác nhận OTP</label>
                    <input type="text" id="transfer-otp-input" class="rp-input" placeholder="------" maxlength="6" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #d1d5db; text-align: center; letter-spacing: 4px; font-weight: bold; font-size: 18px;">
                    <span class="rp-error" id="transfer-otp-error" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;"></span>
                </div>
                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="backToSelectStep()" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #d1d5db; background: none; cursor: pointer;">Quay lại</button>
                    <button type="button" class="rp-btn" onclick="confirmTransfer()" style="padding: 8px 16px; border-radius: 6px; background: #22c55e; color: white; border: none; cursor: pointer;">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- CHỈNH SỬA THÔNG TIN --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="rp-card rp-card--full">
        <div class="rp-card__header">
            <div class="rp-card__header-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                <h2>Chỉnh sửa thông tin</h2>
            </div>
        </div>
        <div class="rp-card__body">
            <form id="profile-form" class="rp-form">@csrf
                <div class="rp-form-grid">
                    <div class="rp-form-group">
                        <label class="rp-label">Họ và tên <span class="required">*</span></label>
                        <input type="text" name="name" id="input-name" class="rp-input" value="{{ $user->name }}" required>
                        <span class="rp-error" id="error-name"></span>
                    </div>
                    <div class="rp-form-group">
                        <label class="rp-label">Số điện thoại <span class="required">*</span></label>
                        <input type="text" name="phone" id="input-phone" class="rp-input" value="{{ $user->phone }}" required>
                        <span class="rp-error" id="error-phone"></span>
                    </div>
                    <div class="rp-form-group">
                        <label class="rp-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" id="input-email" class="rp-input" value="{{ $user->email }}" required>
                        <span class="rp-error" id="error-email"></span>
                    </div>
                    <div class="rp-form-group">
                        <label class="rp-label">Số CCCD</label>
                        <input type="text" name="cccd" id="input-cccd" class="rp-input" value="{{ $user->cccd }}">
                        <span class="rp-error" id="error-cccd"></span>
                    </div>
                </div>
                <div class="rp-form-actions">
                    <button type="submit" class="rp-btn rp-btn--primary" id="btn-save-profile">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ĐỔI MẬT KHẨU --}}
    <div class="rp-card rp-card--full">
        <div class="rp-card__header">
            <div class="rp-card__header-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <h2>Đổi mật khẩu</h2>
            </div>
        </div>
        <div class="rp-card__body">
            <form id="password-form" class="rp-form">@csrf
                <div class="rp-form-grid rp-form-grid--3col">
                    <div class="rp-form-group">
                        <label class="rp-label">Mật khẩu hiện tại <span class="required">*</span></label>
                        <input type="password" name="current_password" id="input-current_password" class="rp-input" required>
                        <span class="rp-error" id="error-current_password"></span>
                    </div>
                    <div class="rp-form-group">
                        <label class="rp-label">Mật khẩu mới <span class="required">*</span></label>
                        <input type="password" name="password" id="input-password" class="rp-input" required>
                        <span class="rp-error" id="error-password"></span>
                    </div>
                    <div class="rp-form-group">
                        <label class="rp-label">Xác nhận mật khẩu <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" id="input-password_confirmation" class="rp-input" required>
                    </div>
                </div>
                <div class="rp-form-actions">
                    <button type="submit" class="rp-btn rp-btn--primary" id="btn-save-password">Cập nhật mật khẩu</button>
                </div>
            </form>
        </div>
    </div>

</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('input[name="_token"]').value;
    const alertContainer = document.getElementById('rp-alert-container');

    function showAlert(type, msg) {
        alertContainer.innerHTML = `<div class="rp-alert rp-alert--${type}"><span>${msg}</span></div>`;
        alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => { alertContainer.innerHTML = ''; }, 5000);
    }
    function clearErrors(formId) {
        const form = document.getElementById(formId);
        form.querySelectorAll('.rp-error').forEach(el => el.textContent = '');
        form.querySelectorAll('.rp-input--error').forEach(el => el.classList.remove('rp-input--error'));
    }
    function showErrors(errors) {
        Object.keys(errors).forEach(field => {
            const input = document.getElementById('input-' + field);
            const errorEl = document.getElementById('error-' + field);
            if (input) input.classList.add('rp-input--error');
            if (errorEl) errorEl.textContent = errors[field][0];
        });
    }

    // Avatar upload
    window.previewAndUploadAvatar = function(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('rp-avatar-img');
            const ph = document.getElementById('rp-avatar-placeholder');
            img.src = e.target.result; img.style.display = 'block';
            if (ph) ph.style.display = 'none';
        };
        reader.readAsDataURL(file);

        const fd = new FormData();
        fd.append('avatar', file); fd.append('_method', 'PUT');
        fd.append('name', '{{ $user->name }}');
        fd.append('phone', '{{ $user->phone }}');
        fd.append('email', '{{ $user->email }}');
        fetch('{{ route("resident.profile.update") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        }).then(r => r.json()).then(d => {
            if (d.success) showAlert('success', 'Ảnh đại diện đã cập nhật.');
        }).catch(() => showAlert('error', 'Lỗi khi tải ảnh lên.'));
    };

    // Profile form
    document.getElementById('profile-form').addEventListener('submit', function(e) {
        e.preventDefault(); clearErrors('profile-form');
        const btn = document.getElementById('btn-save-profile');
        btn.disabled = true; btn.style.opacity = '0.6';
        const fd = new FormData(this); fd.append('_method', 'PUT');
        fetch('{{ route("resident.profile.update") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        }).then(r => r.json().then(d => ({ status: r.status, data: d })))
        .then(({ status, data }) => {
            btn.disabled = false; btn.style.opacity = '1';
            if (status === 422 && data.errors) { showErrors(data.errors); showAlert('error', 'Kiểm tra lại thông tin.'); return; }
            if (data.success) { showAlert('success', data.message); if (data.name) document.getElementById('rp-display-name').textContent = data.name; }
        }).catch(() => { btn.disabled = false; btn.style.opacity = '1'; showAlert('error', 'Đã có lỗi xảy ra.'); });
    });

    // Password form
    document.getElementById('password-form').addEventListener('submit', function(e) {
        e.preventDefault(); clearErrors('password-form');
        const btn = document.getElementById('btn-save-password');
        btn.disabled = true; btn.style.opacity = '0.6';
        const fd = new FormData(this); fd.append('_method', 'PUT');
        fetch('{{ route("resident.profile.change-password") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: fd,
        }).then(r => r.json().then(d => ({ status: r.status, data: d })))
        .then(({ status, data }) => {
            btn.disabled = false; btn.style.opacity = '1';
            if (status === 422) { showErrors(data.errors || {}); showAlert('error', 'Kiểm tra lại thông tin.'); return; }
            if (data.success) { showAlert('success', data.message); document.getElementById('password-form').reset(); }
        }).catch(() => { btn.disabled = false; btn.style.opacity = '1'; showAlert('error', 'Đã có lỗi xảy ra.'); });
    });

    // Owner transfer modal logic
    window.openTransferModal = function() {
        document.getElementById('transferModal').style.display = 'flex';
        document.getElementById('transfer-step-select').style.display = 'block';
        document.getElementById('transfer-step-otp').style.display = 'none';
        document.getElementById('transfer-otp-input').value = '';
        document.getElementById('transfer-otp-error').textContent = '';
        document.getElementById('transfer-target').value = '';
    };

    window.closeTransferModal = function() {
        document.getElementById('transferModal').style.display = 'none';
    };

    window.backToSelectStep = function() {
        document.getElementById('transfer-step-select').style.display = 'block';
        document.getElementById('transfer-step-otp').style.display = 'none';
    };

    window.sendTransferOtp = function() {
        const targetId = document.getElementById('transfer-target').value;
        if (!targetId) {
            alert('Vui lòng chọn thành viên nhận bàn giao.');
            return;
        }

        fetch('{{ route("resident.profile.transfer-owner.send-otp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ target_user_id: targetId })
        })
        .then(r => r.json().then(data => ({ status: r.status, data })))
        .then(({ status, data }) => {
            if (data.success) {
                document.getElementById('transfer-step-select').style.display = 'none';
                document.getElementById('transfer-step-otp').style.display = 'block';
                document.getElementById('otp-demo-code').textContent = data.otp_demo;
            } else {
                alert(data.message || 'Có lỗi xảy ra.');
            }
        })
        .catch(() => alert('Có lỗi xảy ra khi kết nối.'));
    };

    window.confirmTransfer = function() {
        const otp = document.getElementById('transfer-otp-input').value;
        if (!otp) {
            document.getElementById('transfer-otp-error').textContent = 'Vui lòng nhập mã OTP.';
            return;
        }

        fetch('{{ route("resident.profile.transfer-owner.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ otp: otp })
        })
        .then(r => r.json().then(data => ({ status: r.status, data })))
        .then(({ status, data }) => {
            if (data.success) {
                alert('Chuyển quyền chủ hộ thành công!');
                window.location.reload();
            } else {
                if (data.errors && data.errors.otp) {
                    document.getElementById('transfer-otp-error').textContent = data.errors.otp[0];
                } else {
                    document.getElementById('transfer-otp-error').textContent = data.message || 'Mã OTP không chính xác.';
                }
            }
        })
        .catch(() => alert('Có lỗi xảy ra khi kết nối.'));
    };
});
</script>

@endpush
@endsection
