<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Đăng nhập Kế toán</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/auth/admin.css'])
</head>
<body>

<div class="card">

    <div class="logo">
        <div class="logo-icon"><i class="fa-solid fa-calculator"></i></div>
        DomusHub Kế toán
    </div>

    <h2>Xin chào, Kế toán</h2>
    <p class="sub">Đăng nhập để quản lý hệ thống DomusHub.</p>

    @if(session('error'))
        <div class="alert-error">
            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('staff.login.submit') }}">
        @csrf

        <div class="group">
            <label>Email</label>
            <div class="input-box">
                <i class="fa-regular fa-envelope icon"></i>
                <input type="email" name="email" placeholder="" value="{{ old('email') }}" required>
            </div>
            @error('email')<span class="error-msg">{{ $message }}</span>@enderror
        </div>

        <div class="group">
            <label>Mật khẩu</label>
            <div class="input-box">
                <i class="fa-solid fa-lock icon"></i>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
                <i class="fa-regular fa-eye toggle-pw" id="toggleIcon" onclick="togglePassword()"></i>
            </div>
        </div>

        <div class="form-row">
            <div>
                <label class="remember">
                    <input type="checkbox" name="remember"> Ghi nhớ đăng nhập
                </label>
            </div>
        </div>

        <button type="submit" class="login-btn">
            <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
        </button>
    </form>

    <div class="info-box">
        <i class="fa-solid fa-lock"></i>
        <span>Phiên đăng nhập được mã hóa và ghi lại nhật ký. Nếu cần hỗ trợ, liên hệ IT <strong>1900-XXXX</strong></span>
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
