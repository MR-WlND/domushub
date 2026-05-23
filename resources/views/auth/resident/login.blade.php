<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident - DomusHub Login</title>
    @vite([
        'resources/css/auth/login.css'
    ])
</head>

<body>
    <div class="container">
        <div class="left">
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab">
            <div class="overlay"></div>
            <div class="content">
                <h1>
                    Nâng tầm cuộc sống
                    <br>
                    tại DomusHub
                </h1>
                <p>
                    Trải nghiệm dịch vụ quản lý cư dân minh bạch,
                    an toàn và hiện đại dành riêng cho bạn.
                </p>
            </div>
        </div>

        <div class="right">
            <div class="login-box">
                <div class="logo">
                    🏠 Cư dân
                </div>

                <h2 class="title">
                    Đăng nhập Cư dân
                </h2>

                <p class="sub">
                    Vui lòng nhập thông tin để truy cập tài khoản.
                </p>

                @if (session('status'))
                    <div class="success-message" style="background:#ecfdf5; border:1px solid #86efac; color:#166534; padding:12px 14px; border-radius:10px; margin-bottom:16px; font-size:14px;">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('resident.login.submit') }}">
                    @csrf

                    <div class="group">
                        <label>
                            Email hoặc SĐT
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="example@gmail.com" required>
                        @error('email')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label>
                            Mật khẩu
                        </label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>

                    <div class="remember">
                        <div>
                            <input type="checkbox" name="remember">
                            <span>
                                Ghi nhớ
                            </span>
                        </div>

                        <a href="{{ route('resident.forgot-password') }}">
                            Quên mật khẩu?
                        </a>
                    </div>

                    <button class="login-btn">
                        Đăng nhập →
                    </button>

                </form>

                <div class="support">
                    ℹ Cần hỗ trợ kỹ thuật
                    <br>
                    Liên hệ quản lý để được hỗ trợ.
                </div>

                <div class="footer">
                    <a href="{{ route('admin.login') }}">Admin</a> |
                    <a href="{{ route('security.login') }}">Bảo vệ</a> |
                    <a href="{{ route('resident.register') }}">Đăng ký</a>
                </div>

            </div>

        </div>

    </div>

</body>

</html>
