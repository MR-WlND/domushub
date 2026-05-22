<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security - DomusHub Login</title>
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
                    Bảo vệ toàn diện
                    <br>
                    cho khu chung cư
                </h1>
                <p>
                    Hệ thống an ninh hiện đại với giám sát toàn thời gian
                    và quản lý truy cập đơn giản, hiệu quả.
                </p>
            </div>
        </div>

        <div class="right">
            <div class="login-box">
                <div class="logo">
                    🛡️ Bảo vệ
                </div>

                <h2 class="title">
                    Đăng nhập Bảo vệ
                </h2>

                <p class="sub">
                    Nhập thông tin bảo vệ để tiếp tục.
                </p>

                <form method="POST" action="{{ route('security.login.submit') }}">
                    @csrf

                    <div class="group">
                        <label>
                            Email hoặc SĐT
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="security@example.com" required>
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

                        <a href="#">
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
                    Liên hệ Quản lý để được hỗ trợ.
                </div>

                <div class="footer">
                    <a href="{{ route('admin.login') }}">Admin</a> | 
                    <a href="{{ route('resident.login') }}">Cư dân</a>
                </div>

            </div>

        </div>

    </div>

</body>

</html>