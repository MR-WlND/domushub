@extends('layouts.admin.master')

@section('page_title', 'Thông tin cá nhân – DomusHub')

@push('styles')
    @vite(['resources/css/pages/admin/profile.css'])
@endpush

@section('content')
<div class="admin-profile">

    {{-- HEADER --}}
    <div class="admin-profile__header">
        <h1>Thông tin cá nhân</h1>
        <p>Quản lý thông tin tài khoản và bảo mật của bạn</p>
    </div>

    {{-- SUCCESS ALERT --}}
    @if(session('success'))
        <div class="profile-alert profile-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ERROR ALERT --}}
    @if($errors->any())
        <div class="profile-alert profile-alert--error">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Vui lòng kiểm tra lại thông tin bên dưới.
        </div>
    @endif

    {{-- THÔNG TIN CƠ BẢN --}}
    <div class="profile-card">
        <div class="profile-card__header">
            <div class="profile-card__header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <h2>Thông tin cơ bản</h2>
        </div>

        <div class="profile-card__body">
            <form action="{{ portal_route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Avatar --}}
                <div class="avatar-section">
                    <div class="avatar-preview" onclick="document.getElementById('avatar-input').click()">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" id="avatar-img">
                        @else
                            <div class="avatar-preview__placeholder" id="avatar-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <img src="" alt="Avatar" id="avatar-img" style="display:none;">
                        @endif
                    </div>
                    <div class="avatar-info">
                        <h3>{{ $user->name }}</h3>
                        <p>JPG, PNG hoặc WEBP. Tối đa 2MB.</p>
                        <label class="btn-upload" for="avatar-input">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Thay đổi ảnh đại diện
                        </label>
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none;" onchange="previewAvatar(this)">
                    </div>
                </div>

                {{-- Form Fields --}}
                <div class="profile-form-grid">
                    <div class="profile-form-group">
                        <label class="profile-form-label">Họ và tên <span class="required">*</span></label>
                        <input type="text"
                               name="name"
                               class="profile-form-input @error('name') input-error @enderror"
                               value="{{ old('name', $user->name) }}"
                               placeholder="Nhập họ và tên"
                               required>
                        @error('name')
                            <span class="profile-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="profile-form-group">
                        <label class="profile-form-label">Số điện thoại <span class="required">*</span></label>
                        <input type="text"
                               name="phone"
                               class="profile-form-input @error('phone') input-error @enderror"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="VD: 0912345678"
                               required>
                        @error('phone')
                            <span class="profile-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="profile-form-group">
                        <label class="profile-form-label">Email <span class="required">*</span></label>
                        <input type="email"
                               name="email"
                               class="profile-form-input @error('email') input-error @enderror"
                               value="{{ old('email', $user->email) }}"
                               placeholder="admin@domushub.vn"
                               required>
                        @error('email')
                            <span class="profile-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="profile-form-group">
                        <label class="profile-form-label">Số CCCD</label>
                        <input type="text"
                               name="cccd"
                               class="profile-form-input @error('cccd') input-error @enderror"
                               value="{{ old('cccd', $user->cccd) }}"
                               placeholder="Nhập số căn cước công dân">
                        @error('cccd')
                            <span class="profile-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="profile-form-group">
                        <label class="profile-form-label">Vai trò</label>
                        <div>
                            <span class="role-badge role-badge--{{ $user->role }}">
                                @switch($user->role)
                                    @case('admin') Super Administrator @break
                                    @case('manager') Quản lý @break
                                    @case('staff') Nhân viên @break
                                    @case('technician') Kỹ thuật viên @break
                                    @default {{ ucfirst($user->role) }}
                                @endswitch
                            </span>
                        </div>
                    </div>

                    <div class="profile-form-group">
                        <label class="profile-form-label">Ngày tham gia</label>
                        <input type="text"
                               class="profile-form-input"
                               value="{{ $user->created_at->format('d/m/Y') }}"
                               disabled>
                    </div>
                </div>

                <div class="profile-form-actions">
                    <button type="submit" class="btn-save">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Lưu thay đổi
                    </button>
                    <a href="{{ portal_route('dashboard') }}" class="btn-cancel">Hủy</a>
                </div>
            </form>
        </div>
    </div>

    {{-- ĐỔI MẬT KHẨU --}}
    <div class="profile-card">
        <div class="profile-card__header">
            <div class="profile-card__header-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <h2>Đổi mật khẩu</h2>
        </div>

        <div class="profile-card__body">
            <form action="{{ portal_route('profile.change-password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="password-grid">
                    <div class="profile-form-group">
                        <label class="profile-form-label">Mật khẩu hiện tại <span class="required">*</span></label>
                        <input type="password"
                               name="current_password"
                               class="profile-form-input @error('current_password') input-error @enderror"
                               placeholder="Nhập mật khẩu hiện tại"
                               required>
                        @error('current_password')
                            <span class="profile-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="profile-form-group">
                        <label class="profile-form-label">Mật khẩu mới <span class="required">*</span></label>
                        <input type="password"
                               name="password"
                               class="profile-form-input @error('password') input-error @enderror"
                               placeholder="Tối thiểu 6 ký tự"
                               required>
                        @error('password')
                            <span class="profile-form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="profile-form-group">
                        <label class="profile-form-label">Xác nhận mật khẩu mới <span class="required">*</span></label>
                        <input type="password"
                               name="password_confirmation"
                               class="profile-form-input"
                               placeholder="Nhập lại mật khẩu mới"
                               required>
                    </div>
                </div>

                <div class="profile-form-actions">
                    <button type="submit" class="btn-save">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Cập nhật mật khẩu
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatar-img');
                const placeholder = document.getElementById('avatar-placeholder');
                img.src = e.target.result;
                img.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
@endsection
