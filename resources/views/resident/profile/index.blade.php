@extends('layouts.resident.master')

@section('title', 'Hồ sơ cá nhân - DomusHub')

@push('styles')
    @vite(['resources/css/pages/resident/profile/index.css'])
@endpush

@section('content')
<section class="resident-profile-page">

    {{-- HEADER --}}
    <div class="rp-header">
        <p class="rp-header__eyebrow">Hồ sơ cư dân</p>
        <h1 class="rp-header__title">Thông tin cá nhân</h1>
    </div>

    {{-- ALERT CONTAINER (JS sẽ hiển thị ở đây) --}}
    <div id="rp-alert-container"></div>

    {{-- CARD: THÔNG TIN CƠ BẢN --}}
    <div class="rp-card">
        <div class="rp-card__header">
            <div class="rp-card__header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <h2>Thông tin chủ hộ</h2>
        </div>

        <div class="rp-card__body">
            <form id="profile-form" enctype="multipart/form-data" class="rp-form">
                @csrf

                {{-- Avatar Upload --}}
                <div class="rp-avatar-section">
                    <div class="rp-avatar" onclick="document.getElementById('avatar-input').click()" title="Bấm để thay ảnh đại diện">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" id="rp-avatar-img">
                        @else
                            <div class="rp-avatar__placeholder" id="rp-avatar-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <img src="" alt="Avatar" id="rp-avatar-img" style="display:none;">
                        @endif
                        <div class="rp-avatar__overlay">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        </div>
                    </div>
                    <div class="rp-avatar__info">
                        <h3 id="rp-display-name">{{ $user->name }}</h3>
                        <p>Cư dân chung cư</p>
                        <label class="rp-btn-upload" for="avatar-input">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Thay ảnh đại diện
                        </label>
                        <span class="rp-avatar__hint">JPG, PNG, WEBP — Tối đa 2MB</span>
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none;" onchange="previewResidentAvatar(this)">
                    </div>
                </div>

                {{-- Form Fields --}}
                <div class="rp-form-grid">
                    <div class="rp-form-group">
                        <label class="rp-label">Họ và tên <span class="required">*</span></label>
                        <input type="text"
                               name="name"
                               id="input-name"
                               class="rp-input"
                               value="{{ $user->name }}"
                               placeholder="Nhập họ và tên"
                               required>
                        <span class="rp-error" id="error-name"></span>
                    </div>

                    <div class="rp-form-group">
                        <label class="rp-label">Số điện thoại <span class="required">*</span></label>
                        <input type="text"
                               name="phone"
                               id="input-phone"
                               class="rp-input"
                               value="{{ $user->phone }}"
                               placeholder="VD: 0912345678"
                               required>
                        <span class="rp-error" id="error-phone"></span>
                    </div>

                    <div class="rp-form-group">
                        <label class="rp-label">Email <span class="required">*</span></label>
                        <input type="email"
                               name="email"
                               id="input-email"
                               class="rp-input"
                               value="{{ $user->email }}"
                               placeholder="example@email.com"
                               required>
                        <span class="rp-error" id="error-email"></span>
                    </div>

                    <div class="rp-form-group">
                        <label class="rp-label">Số CCCD</label>
                        <input type="text"
                               name="cccd"
                               id="input-cccd"
                               class="rp-input"
                               value="{{ $user->cccd }}"
                               placeholder="Nhập số căn cước công dân">
                        <span class="rp-error" id="error-cccd"></span>
                    </div>
                </div>

                {{-- Căn hộ sở hữu --}}
                @if($apartments->isNotEmpty())
                    <div class="rp-apartments-info">
                        <label class="rp-label">Căn hộ sở hữu</label>
                        <div class="rp-apartments-list">
                            @foreach($apartments as $apt)
                                <div class="rp-apartment-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                    <span>{{ $apt->apartment_number }} — {{ $apt->floor->block->name ?? '' }} / Tầng {{ $apt->floor->floor_number ?? '' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="rp-form-actions">
                    <button type="submit" class="rp-btn rp-btn--primary" id="btn-save-profile">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Lưu thay đổi
                    </button>
                    <a href="{{ route('resident.dashboard') }}" class="rp-btn rp-btn--outline">Quay lại</a>
                </div>
            </form>
        </div>
    </div>

    {{-- CARD: ĐỔI MẬT KHẨU --}}
    <div class="rp-card" style="margin-top: 24px;">
        <div class="rp-card__header">
            <div class="rp-card__header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <h2>Đổi mật khẩu</h2>
        </div>

        <div class="rp-card__body">
            <form id="password-form" class="rp-form">
                @csrf

                <div class="rp-form-grid rp-form-grid--3col">
                    <div class="rp-form-group">
                        <label class="rp-label">Mật khẩu hiện tại <span class="required">*</span></label>
                        <input type="password"
                               name="current_password"
                               id="input-current_password"
                               class="rp-input"
                               placeholder="Nhập mật khẩu hiện tại"
                               required>
                        <span class="rp-error" id="error-current_password"></span>
                    </div>

                    <div class="rp-form-group">
                        <label class="rp-label">Mật khẩu mới <span class="required">*</span></label>
                        <input type="password"
                               name="password"
                               id="input-password"
                               class="rp-input"
                               placeholder="Tối thiểu 6 ký tự"
                               required>
                        <span class="rp-error" id="error-password"></span>
                    </div>

                    <div class="rp-form-group">
                        <label class="rp-label">Xác nhận mật khẩu <span class="required">*</span></label>
                        <input type="password"
                               name="password_confirmation"
                               id="input-password_confirmation"
                               class="rp-input"
                               placeholder="Nhập lại mật khẩu mới"
                               required>
                    </div>
                </div>

                <div class="rp-form-actions">
                    <button type="submit" class="rp-btn rp-btn--primary" id="btn-save-password">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Cập nhật mật khẩu
                    </button>
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

    // ========== HELPER FUNCTIONS ==========

    function showAlert(type, message) {
        const icon = type === 'success'
            ? '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';

        alertContainer.innerHTML = `
            <div class="rp-alert rp-alert--${type}">
                ${icon}
                <span>${message}</span>
            </div>
        `;

        // Scroll lên để thấy alert
        alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Tự ẩn sau 5s
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
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

    // ========== PROFILE FORM (AJAX) ==========

    document.getElementById('profile-form').addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors('profile-form');

        const btn = document.getElementById('btn-save-profile');
        btn.disabled = true;
        btn.style.opacity = '0.6';

        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        fetch('{{ route("resident.profile.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            btn.disabled = false;
            btn.style.opacity = '1';

            if (status === 422 && data.errors) {
                showErrors(data.errors);
                showAlert('error', 'Vui lòng kiểm tra lại thông tin.');
                return;
            }

            if (data.success) {
                showAlert('success', data.message);

                // Cập nhật avatar ở header
                if (data.avatar_url) {
                    const headerAvatar = document.getElementById('header-user-avatar');
                    if (headerAvatar) headerAvatar.src = data.avatar_url;
                }

                // Cập nhật tên hiển thị
                if (data.name) {
                    document.getElementById('rp-display-name').textContent = data.name;
                }
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.style.opacity = '1';
            showAlert('error', 'Đã có lỗi xảy ra, vui lòng thử lại.');
            console.error(err);
        });
    });

    // ========== PASSWORD FORM (AJAX) ==========

    document.getElementById('password-form').addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors('password-form');

        const btn = document.getElementById('btn-save-password');
        btn.disabled = true;
        btn.style.opacity = '0.6';

        const formData = new FormData(this);
        formData.append('_method', 'PUT');

        fetch('{{ route("resident.profile.change-password") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            btn.disabled = false;
            btn.style.opacity = '1';

            if (status === 422) {
                const errors = data.errors || {};
                showErrors(errors);
                showAlert('error', 'Vui lòng kiểm tra lại thông tin.');
                return;
            }

            if (data.success) {
                showAlert('success', data.message);
                // Reset form mật khẩu
                document.getElementById('password-form').reset();
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.style.opacity = '1';
            showAlert('error', 'Đã có lỗi xảy ra, vui lòng thử lại.');
            console.error(err);
        });
    });

    // ========== AVATAR PREVIEW (chỉ preview, không đổi header) ==========

    window.previewResidentAvatar = function(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('rp-avatar-img');
                const placeholder = document.getElementById('rp-avatar-placeholder');
                img.src = e.target.result;
                img.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    };
});
</script>
@endpush
@endsection
