<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Đăng nhập Nhân viên Vệ sinh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/auth/login.css'])
    <style>
        body {
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 24px 16px;
        }
        .login-wrapper {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 40px 32px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }
        .login-wrapper .logo {
            margin-bottom: 28px;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-box">

        <div class="logo">
            <i class="fa-solid fa-broom"></i> DomusHub Portal
        </div>

        <h2>Chào mừng, Nhân Viên Vệ Sinh</h2>
        <p class="sub">Vui lòng nhập thông tin để truy cập tài khoản Vệ sinh.</p>

        @if(session('error'))
            <div class="alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('cleaning.login.submit') }}">
            @csrf

            <div class="group">
                <label>Email hoặc Số điện thoại</label>
                <div class="input-box">
                    <i class="fa-regular fa-envelope"></i>
                    <input type="email" name="email" placeholder="vesinh@demo.com" value="{{ old('email') }}" required>
                </div>
                @error('email')<span class="error-msg">{{ $message }}</span>@enderror
            </div>

            <div class="group">
                <label>Mật khẩu</label>
                <div class="input-box">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" required>
                    <i class="fa-solid fa-eye toggle-pw" id="togglePassword"></i>
                </div>
            </div>

            <div class="resident-login-options">
                <label class="resident-remember">
                    <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                    <span>Ghi nhớ đăng nhập</span>
                </label>

                <div class="forgot">
                    <a href="#">Quên mật khẩu?</a>
                </div>
            </div>

            <button type="submit" class="login-btn">
                Đăng nhập <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <div class="divider"><span>HOẶC TIẾP TỤC VỚI</span></div>

        <div class="support">
            <i class="fa-solid fa-circle-info"></i>
            <div>
                <strong>Cần hỗ trợ?</strong>
                <p>Liên hệ Ban quản lý hoặc hotline 1900-XXXX</p>
            </div>
        </div>

        <div class="bottom">
            <small>
                <span>Chính sách bảo mật</span>
                <span>Điều khoản dịch vụ</span>
            </small>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        
        if (togglePassword && password) {
            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    });
</script>
</body>
</html>


