<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Đăng nhập Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/auth/login.css'])
</head>
<body>
<div class="container">

    <!-- LEFT -->
    <div class="left">
        <img src="{{ asset('images/admin_login_bg.jpg') }}" alt="admin office">
        <div class="overlay"></div>
        <div class="hero">
            <h1>Quản trị & Điều hành<br>tại DomusHub</h1>
            <p>Hệ thống quản lý trung tâm giúp bạn điều hành và giám sát mọi hoạt động của tòa nhà.</p>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <div class="login-box">

            <div class="logo">
                <i class="fa-solid fa-user-tie"></i> DomusHub Admin
            </div>

            <h2>Xin chào, Admin</h2>
            <p class="sub">Đăng nhập để quản lý hệ thống DomusHub.</p>

            @if(session('error'))
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="group">
                    <label>Email</label>
                    <div class="input-box">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="email" placeholder="admin@example.com" value="{{ old('email') }}" required>
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

            <div class="support" style="margin-bottom: 24px;">
                <i class="fa-solid fa-circle-info"></i>
                <div>
                    <strong>Bảo mật hệ thống</strong>
                    <p>Phiên đăng nhập được mã hóa và ghi lại nhật ký. Nếu cần hỗ trợ, liên hệ IT <strong>1900-XXXX</strong></p>
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
