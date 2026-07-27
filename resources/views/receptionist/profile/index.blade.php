@extends('layouts.receptionist.master')

@section('page_title', 'Trang cá nhân – Lễ tân DomusHub')

@push('styles')
<style>
    .profile-grid { display: grid; grid-template-columns: 300px 1fr; gap: 24px; align-items: start; }
    /* ── Card ── */
    .card { background: white; border-radius: 16px; padding: 28px; box-shadow: 0 2px 12px rgba(123,63,228,.07); margin-bottom: 24px; }
    .card h2 { font-size: 16px; font-weight: 700; color: #1B2559; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid #F4F7FE; display: flex; align-items: center; gap: 8px; }
    /* ── Avatar card ── */
    .avatar-card { text-align: center; }
    .avatar-wrap { position: relative; display: inline-block; margin-bottom: 16px; }
    .avatar-img { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid #F5F0FF; }
    .avatar-placeholder { width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg,#7B3FE4,#A67BF8); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px; font-weight: 800; border: 4px solid #F5F0FF; margin: 0 auto; }
    .avatar-edit-btn { position: absolute; bottom: 4px; right: 4px; width: 32px; height: 32px; border-radius: 50%; background: #7B3FE4; color: white; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 13px; border: 2px solid white; transition: .2s; }
    .avatar-edit-btn:hover { background: #6930cc; }
    .profile-name { font-size: 18px; font-weight: 800; color: #1B2559; }
    .profile-role { font-size: 12px; color: white; background: linear-gradient(135deg,#7B3FE4,#A67BF8); display: inline-block; padding: 3px 14px; border-radius: 20px; font-weight: 600; margin-top: 6px; }
    .profile-email { font-size: 13px; color: #A3AED0; margin-top: 8px; }
    /* ── Stats ── */
    .stat-row { display: flex; justify-content: space-around; margin-top: 20px; padding-top: 18px; border-top: 1px solid #F4F7FE; }
    .stat-item { text-align: center; }
    .stat-value { font-size: 24px; font-weight: 800; color: #7B3FE4; }
    .stat-label { font-size: 11px; color: #A3AED0; font-weight: 500; margin-top: 2px; }
    /* ── Form ── */
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #1B2559; margin-bottom: 7px; }
    .form-group label .req { color: #EE5D50; }
    .form-control { width: 100%; padding: 11px 14px; border: 1.5px solid #E9EDF7; border-radius: 10px; font-size: 13.5px; font-family: inherit; color: #1B2559; outline: none; transition: .2s; background: white; }
    .form-control:focus { border-color: #7B3FE4; box-shadow: 0 0 0 3px rgba(123,63,228,.1); }
    .form-control.is-invalid { border-color: #EE5D50; }
    .invalid-feedback { font-size: 12px; color: #EE5D50; margin-top: 5px; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    /* ── Buttons ── */
    .btn-save { padding: 11px 28px; border-radius: 10px; background: #7B3FE4; color: white; font-size: 14px; font-weight: 700; border: none; cursor: pointer; font-family: inherit; transition: .2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-save:hover { background: #6930cc; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(123,63,228,.3); }
    /* ── Password section ── */
    .pwd-hint { font-size: 12px; color: #A3AED0; margin-top: 5px; }
    .strength-bar { height: 4px; border-radius: 4px; background: #F4F7FE; margin-top: 6px; overflow: hidden; }
    .strength-bar-fill { height: 100%; border-radius: 4px; transition: width .3s, background .3s; width: 0; }
    @media(max-width: 900px) { .profile-grid { grid-template-columns: 1fr; } .form-row-2 { grid-template-columns: 1fr; } }
</style>
@endpush

@section('topbar_left')
<nav style="font-size:13px;color:#A3AED0;display:flex;align-items:center;gap:6px;">
    <a href="{{ route('receptionist.dashboard') }}" style="color:#7B3FE4;text-decoration:none;">Dashboard</a>
    <i class="fa-solid fa-chevron-right" style="font-size:10px;"></i>
    <span>Trang cá nhân</span>
</nav>
@endsection

@section('content')
<div class="profile-grid">

    {{-- ── Cột trái: Avatar + thống kê ── --}}
    <div>
        <div class="card avatar-card">
            {{-- Avatar --}}
            <form method="POST" action="{{ route('receptionist.profile.update') }}" enctype="multipart/form-data" id="avatarForm">
                @csrf @method('PUT')
                <div class="avatar-wrap">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" class="avatar-img" alt="{{ $user->name }}" id="avatarPreview">
                    @else
                        <div class="avatar-placeholder" id="avatarPlaceholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    @endif
                    <label for="avatarInput" class="avatar-edit-btn" title="Đổi ảnh đại diện">
                        <i class="fa-solid fa-camera"></i>
                    </label>
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none" onchange="previewAvatar(this)">
                    {{-- hidden fields để giữ lại các giá trị khi submit chỉ avatar --}}
                    <input type="hidden" name="name"  value="{{ $user->name }}">
                    <input type="hidden" name="phone" value="{{ $user->phone }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                </div>
            </form>

            <div class="profile-name">{{ $user->name }}</div>
            <div class="profile-role">Lễ tân</div>
            <div class="profile-email"><i class="fa-regular fa-envelope"></i> {{ $user->email }}</div>
            @if($user->phone)
            <div class="profile-email" style="margin-top:4px;"><i class="fa-solid fa-phone"></i> {{ $user->phone }}</div>
            @endif

            <div class="stat-row">
                <div class="stat-item">
                    <div class="stat-value">{{ $totalParcelsHandled }}</div>
                    <div class="stat-label">Bưu phẩm đã nhận</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $parcelsToday }}</div>
                    <div class="stat-label">Hôm nay</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Cột phải: Form cập nhật ── --}}
    <div>
        {{-- Thông tin cá nhân --}}
        <div class="card">
            <h2><i class="fa-solid fa-user" style="color:#7B3FE4;"></i> Thông tin cá nhân</h2>

            <form method="POST" action="{{ route('receptionist.profile.update') }}" enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="form-row-2">
                    <div class="form-group">
                        <label>Họ và tên <span class="req">*</span></label>
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại <span class="req">*</span></label>
                        <input type="text" name="phone" class="form-control {{ $errors->has('phone') ? 'is-invalid' : '' }}"
                               value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Email <span class="req">*</span></label>
                    <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu thông tin
                </button>
            </form>
        </div>

        {{-- Đổi mật khẩu --}}
        <div class="card">
            <h2><i class="fa-solid fa-lock" style="color:#7B3FE4;"></i> Đổi mật khẩu</h2>

            <form method="POST" action="{{ route('receptionist.profile.change-password') }}">
                @csrf @method('PUT')

                <div class="form-group">
                    <label>Mật khẩu hiện tại <span class="req">*</span></label>
                    <input type="password" name="current_password"
                           class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                           placeholder="••••••••" required>
                    @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label>Mật khẩu mới <span class="req">*</span></label>
                        <input type="password" name="password" id="newPwd"
                               class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               placeholder="Tối thiểu 8 ký tự" required oninput="checkStrength(this.value)">
                        <div class="strength-bar"><div class="strength-bar-fill" id="strengthBar"></div></div>
                        <div class="pwd-hint" id="strengthLabel"></div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới <span class="req">*</span></label>
                        <input type="password" name="password_confirmation"
                               class="form-control"
                               placeholder="Nhập lại mật khẩu mới" required>
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fa-solid fa-key"></i> Đổi mật khẩu
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Preview avatar trước khi upload
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('avatarPreview');
            const placeholder = document.getElementById('avatarPlaceholder');
            if (preview) {
                preview.src = e.target.result;
            } else if (placeholder) {
                placeholder.style.display = 'none';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'avatar-img';
                img.id = 'avatarPreview';
                placeholder.parentNode.insertBefore(img, placeholder);
            }
        };
        reader.readAsDataURL(input.files[0]);
        // Auto submit form avatar
        document.getElementById('avatarForm').submit();
    }
}

// Kiểm tra độ mạnh mật khẩu
function checkStrength(val) {
    const bar = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { width: '25%', color: '#EE5D50', text: 'Rất yếu' },
        { width: '50%', color: '#FF9B05', text: 'Yếu' },
        { width: '75%', color: '#3652D9', text: 'Trung bình' },
        { width: '100%',color: '#05CD99', text: 'Mạnh' },
    ];
    const lv = levels[score - 1] || levels[0];
    if (val.length === 0) { bar.style.width = '0'; label.textContent = ''; return; }
    bar.style.width = lv.width;
    bar.style.background = lv.color;
    label.textContent = lv.text;
    label.style.color = lv.color;
}
</script>
@endpush
