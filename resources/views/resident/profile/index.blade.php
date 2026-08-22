@extends('layouts.resident.master')

@section('title', 'Hồ sơ cá nhân - DomusHub')

@push('styles')
    @vite(['resources/css/pages/resident/profile/index.css'])
@endpush

@section('content')
<section class="resident-profile-page">

    {{-- ALERT CONTAINER --}}
    <div id="rp-alert-container"></div>

    <div class="res-header">
        <div>
            <div class="res-header__breadcrumb">
                <a href="{{ route('resident.dashboard') }}">Cư dân</a> &gt; Hồ sơ chi tiết
            </div>
            <h1 class="res-header__title">Hồ sơ Cư dân</h1>
        </div>
        <div class="res-header__actions">
            <a href="javascript:void(0)" class="res-btn res-btn--primary" onclick="openPasswordModal()">
                <i class="fa-solid fa-key"></i> Đổi mật khẩu
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- HERO CARD --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="res-card res-hero">
        <div class="res-hero__avatar-wrapper" onclick="document.getElementById('avatar-input').click()" style="cursor: pointer;">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="res-hero__avatar" id="rp-avatar-img">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=e0e7ff&color=4338ca&size=100" alt="{{ $user->name }}" class="res-hero__avatar" id="rp-avatar-img">
            @endif
        </div>
        
        <div class="res-hero__info">
            <h2 id="rp-display-name">{{ $user->name }}</h2>
            <div class="res-hero__details">
                <div class="res-hero__item">
                    <i class="fa-solid fa-building"></i>
                    <span>Căn hộ: <strong>
                        @if($apartments->isNotEmpty())
                            @php $apt = $apartments->first(); @endphp
                            {{ $apt->floor->block->name ?? 'Tòa' }} - {{ $apt->apartment_number }}
                        @else
                            Chưa có
                        @endif
                    </strong></span>
                </div>
                <div class="res-hero__item">
                    <i class="fa-regular fa-calendar"></i>
                    <span>Ngày chuyển đến: <strong>{{ $selfResident ? \Carbon\Carbon::parse($selfResident->start_date)->format('d/m/Y') : $user->created_at->format('d/m/Y') }}</strong></span>
                </div>
                <div class="res-hero__item">
                    <i class="fa-regular fa-id-badge"></i>
                    <span>Vai trò: <strong>
                        @if($selfResident && $selfResident->relationship)
                            @switch($selfResident->relationship)
                                @case('owner') Chủ hộ @break
                                @case('spouse') Vợ/Chồng @break
                                @case('child') Con @break
                                @case('tenant') Người thuê @break
                                @default Cư dân
                            @endswitch
                        @else
                            Cư dân
                        @endif
                    </strong></span>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden file input for avatar --}}
    <form id="avatar-form" style="display:none;">@csrf
        <input type="file" id="avatar-input" name="avatar" accept="image/*" onchange="previewAndUploadAvatar(this)">
    </form>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- MAIN GRID --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="res-grid">
        
        {{-- LEFT COLUMN --}}
        <div>
            {{-- THÔNG TIN CÁ NHÂN --}}
            <div class="res-card">
                <div class="res-card__title">
                    <div><i class="fa-regular fa-user"></i> Thông tin cá nhân</div>
                    <div class="res-card__title-right">
                        <a href="javascript:void(0)" onclick="openEditModal()"><i class="fa-solid fa-pen"></i> Chỉnh sửa</a>
                    </div>
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
                        <span class="res-info-label">Quê quán</span>
                        <span class="res-info-value">{{ $user->address ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- DANH SÁCH THÀNH VIÊN --}}
            <div class="res-card" style="padding-bottom: 0; overflow: hidden;">
                <div class="res-card__title" style="margin-bottom: 0;">
                    <div><i class="fa-solid fa-users"></i> Danh sách thành viên</div>
                    <div class="res-card__title-right">
                        <a href="{{ route('resident.members.index') }}" class="d-desktop-only"><i class="fa-solid fa-plus"></i> Thêm thành viên</a>
                    </div>
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
                        @foreach($registeredMembers as $member)
                        <tr>
                            <td>{{ $member->user->name ?? 'N/A' }}</td>
                            <td>@switch($member->relationship)@case('spouse') Vợ/Chồng @break @case('child') Con @break @case('parent') Cha/Mẹ @break @case('sibling') Anh/Chị/Em @break @case('tenant') Người thuê @break @default {{ $member->relationship ?? '' }} @endswitch</td>
                            <td>{{ $member->user && $member->user->birthday ? \Carbon\Carbon::parse($member->user->birthday)->format('d/m/Y') : '—' }}</td>
                        </tr>
                        @endforeach
                        @foreach($declaredMembers as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>@switch($member->relationship)@case('spouse') Vợ/Chồng @break @case('child') Con @break @case('parent') Cha/Mẹ @break @case('sibling') Anh/Chị/Em @break @default {{ $member->relationship ?? 'Khác' }} @endswitch</td>
                            <td>{{ $member->birthday ? \Carbon\Carbon::parse($member->birthday)->format('d/m/Y') : '—' }}</td>
                        </tr>
                        @endforeach
                        @if($registeredMembers->isEmpty() && $declaredMembers->isEmpty())
                        <tr>
                            <td colspan="3" style="text-align: center; color: #94a3b8;">Không có thành viên nào khác</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div>
            {{-- PHƯƠNG TIỆN --}}
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
                    <div style="text-align: center; color: #94a3b8; font-size: 13px;">Chưa đăng ký phương tiện nào</div>
                    @endforelse
                </div>
            </div>

            {{-- HÓA ĐƠN --}}
            <div class="res-card">
                <div class="res-card__title">
                    <div><i class="fa-solid fa-file-invoice"></i> Hóa đơn gần nhất</div>
                    <div class="res-card__title-right">
                        <a href="{{ route('resident.invoices.index') }}">Xem tất cả</a>
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

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- QUẢN LÝ CƯ TRÚ --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if($apartments->isNotEmpty())
        <div class="rp-card rp-card--full" style="border-top: 4px solid #f87171;">
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
                        <button type="submit" class="rp-btn rp-btn--danger" style="background-color: #f43f5e; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-weight: 500; font-size: 14px;">
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

    {{-- MODAL CHỈNH SỬA THÔNG TIN --}}
    <div id="editModal" class="invite-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div class="invite-modal" style="background: white; padding: 24px; border-radius: 12px; max-width: 500px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <div class="invite-modal__header" style="text-align: center; margin-bottom: 20px;">
                <div class="invite-modal__icon-wrap" style="background: #eff6ff; color: #3b82f6; font-size: 32px; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <h3 class="invite-modal__title" style="font-size: 18px; font-weight: 600; color: #1f2937; margin: 0 0 6px 0;">Chỉnh sửa thông tin</h3>
                <p class="invite-modal__subtitle" style="font-size: 13px; color: #6b7280; margin: 0;">Cập nhật thông tin cá nhân của bạn.</p>
            </div>

            <form id="profile-form" class="rp-form">@csrf
                <div class="rp-form-group" style="margin-bottom: 12px;">
                    <label class="rp-label" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Họ và tên <span class="required">*</span></label>
                    <input type="text" name="name" id="input-name" class="rp-input" value="{{ $user->name }}" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;" required>
                    <span class="rp-error" id="error-name" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;"></span>
                </div>
                <div class="rp-form-group" style="margin-bottom: 12px;">
                    <label class="rp-label" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Số điện thoại <span class="required">*</span></label>
                    <input type="text" name="phone" id="input-phone" class="rp-input" value="{{ $user->phone }}" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;" required>
                    <span class="rp-error" id="error-phone" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;"></span>
                </div>
                <div class="rp-form-group" style="margin-bottom: 12px;">
                    <label class="rp-label" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Email <span class="required">*</span></label>
                    <input type="email" name="email" id="input-email" class="rp-input" value="{{ $user->email }}" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;" required>
                    <span class="rp-error" id="error-email" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;"></span>
                </div>
                <div class="rp-form-group" style="margin-bottom: 16px;">
                    <label class="rp-label" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Số CCCD</label>
                    <input type="text" name="cccd" id="input-cccd" class="rp-input" value="{{ $user->cccd }}" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;">
                    <span class="rp-error" id="error-cccd" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;"></span>
                </div>
                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #d1d5db; background: none; cursor: pointer;">Hủy</button>
                    <button type="submit" class="rp-btn rp-btn--primary" id="btn-save-profile" style="padding: 8px 16px; border-radius: 6px; background: #3b82f6; color: white; border: none; cursor: pointer;">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL ĐỔI MẬT KHẨU --}}
    <div id="passwordModal" class="invite-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div class="invite-modal" style="background: white; padding: 24px; border-radius: 12px; max-width: 500px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
            <div class="invite-modal__header" style="text-align: center; margin-bottom: 20px;">
                <div class="invite-modal__icon-wrap" style="background: #eff6ff; color: #3b82f6; font-size: 32px; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h3 class="invite-modal__title" style="font-size: 18px; font-weight: 600; color: #1f2937; margin: 0 0 6px 0;">Đổi mật khẩu</h3>
                <p class="invite-modal__subtitle" style="font-size: 13px; color: #6b7280; margin: 0;">Vui lòng nhập mật khẩu hiện tại và mật khẩu mới.</p>
            </div>

            <form id="password-form" class="rp-form">@csrf
                <div class="rp-form-group" style="margin-bottom: 12px;">
                    <label class="rp-label" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Mật khẩu hiện tại <span class="required">*</span></label>
                    <input type="password" name="current_password" id="input-current_password" class="rp-input" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;" required>
                    <span class="rp-error" id="error-current_password" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;"></span>
                </div>
                <div class="rp-form-group" style="margin-bottom: 12px;">
                    <label class="rp-label" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Mật khẩu mới <span class="required">*</span></label>
                    <input type="password" name="password" id="input-password" class="rp-input" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;" required>
                    <span class="rp-error" id="error-password" style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;"></span>
                </div>
                <div class="rp-form-group" style="margin-bottom: 16px;">
                    <label class="rp-label" style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Xác nhận mật khẩu <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" id="input-password_confirmation" class="rp-input" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #d1d5db;" required>
                </div>
                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closePasswordModal()" style="padding: 8px 16px; border-radius: 6px; border: 1px solid #d1d5db; background: none; cursor: pointer;">Hủy</button>
                    <button type="submit" class="rp-btn rp-btn--primary" id="btn-save-password" style="padding: 8px 16px; border-radius: 6px; background: #3b82f6; color: white; border: none; cursor: pointer;">Cập nhật mật khẩu</button>
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
            if (data.success) { showAlert('success', data.message); if (data.name) document.getElementById('rp-display-name').textContent = data.name; closeEditModal(); setTimeout(() => location.reload(), 1500); }
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
            if (data.success) { showAlert('success', data.message); document.getElementById('password-form').reset(); closePasswordModal(); }
        }).catch(() => { btn.disabled = false; btn.style.opacity = '1'; showAlert('error', 'Đã có lỗi xảy ra.'); });
    });

    // Owner transfer modal logic
    window.openEditModal = function() {
        document.getElementById('editModal').style.display = 'flex';
        clearErrors('profile-form');
    };

    window.closeEditModal = function() {
        document.getElementById('editModal').style.display = 'none';
    };

    window.openPasswordModal = function() {
        document.getElementById('passwordModal').style.display = 'flex';
        document.getElementById('password-form').reset();
        clearErrors('password-form');
    };

    window.closePasswordModal = function() {
        document.getElementById('passwordModal').style.display = 'none';
    };

    window.openInviteModal = function() {
        document.getElementById('addMemberModal').style.display = 'flex';
    };

    window.closeInviteModal = function() {
        document.getElementById('addMemberModal').style.display = 'none';
    };

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
