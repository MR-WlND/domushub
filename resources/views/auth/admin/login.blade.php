<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - DomusHub Login</title>
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
                    Quản lý hiệu quả
                    <br>
                    với DomusHub
                </h1>
                <p>
                    Hệ thống quản lý cư dân toàn diện cho các quản lý viên
                    với tính năng mạnh mẽ và hiệu suất cao.
                </p>
            </div>
        </div>

        <div class="right">
            <div class="login-box">
                <div class="logo">
                    👨‍💼 Admin Portal
                </div>

                <h2 class="title">
                    Đăng nhập Admin
                </h2>

                <p class="sub">
                    Nhập thông tin quản trị viên để tiếp tục.
                </p>

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <div class="group">
                        <label>
                            Email hoặc SĐT
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required>
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
                    Liên hệ IT để được hỗ trợ.
                </div>

                <div class="footer">
                    <a href="{{ route('resident.login') }}">Cư dân</a> | 
                    <a href="{{ route('security.login') }}">Bảo vệ</a>
                </div>

            </div>

        </div>

    </div>

</body>

</html>