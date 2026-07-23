<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Đăng nhập Vệ sinh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/auth/cleaning.css'])
</head>
<body>

<div class="card">

    <div class="badge">
        <i class="fa-solid fa-broom"></i> Nhân viên Vệ sinh
    </div>

    <h2>Xin chào, Vệ sinh</h2>
    <p class="sub">Đăng nhập để truy cập hệ thống quản lý vệ sinh DomusHub.</p>

    @if(session('error'))
        <div class="alert-error">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('cleaning.login.submit') }}">
        @csrf

        <div class="group">
            <label>Email</label>
            <div class="input-box">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" name="email" placeholder="" value="{{ old('email') }}" required>
            </div>
            @error('email')<span class="error-msg">{{ $message }}</span>@enderror
        </div>

        <div class="group">
            <label>Mật khẩu</label>
            <div class="input-box">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
                <i class="fa-regular fa-eye toggle-pw" id="toggleIcon" onclick="togglePassword()"></i>
            </div>
        </div>

        <div class="row">
            <label class="remember">
                <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
            </label>
        </div>

        <button type="submit" class="login-btn">
            <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
        </button>
    </form>

    <div class="info-box">
        <i class="fa-solid fa-lock"></i>
        <span>Phiên đăng nhập được ghi lại. Cần hỗ trợ liên hệ Quản lý <strong>1900-XXXX</strong></span>
    </div>

</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.className = isHidden ? 'fa-regular fa-eye-slash toggle-pw' : 'fa-regular fa-eye toggle-pw';
}
</script>

</body>
</html>
