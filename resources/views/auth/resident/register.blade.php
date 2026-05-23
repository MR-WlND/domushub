<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký cư dân - DomusHub</title>
    @vite([
        'resources/css/auth/login.css'
    ])
    <style>
        .error {
            color: #dc2626;
            font-size: 13px;
            margin-top: 8px;
            display: block;
        }

        .muted {
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .footer-link {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
        }

        .footer-link a {
            color: #00236f;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left">
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab">
            <div class="overlay"></div>
            <div class="content">
                <h1>
                    Tạo tài khoản
                    <br>
                    cho cư dân
                </h1>
                <p>
                    Đăng ký nhanh với thông tin cá nhân và mã mời để tham gia hệ thống quản lý DomusHub.
                </p>
            </div>
        </div>

        <div class="right">
            <div class="login-box">
                <div class="logo">
                    🏠 Đăng ký cư dân
                </div>

                <h2 class="title">
                    Đăng ký cư dân
                </h2>

                <p class="sub">
                    Vui lòng nhập đầy đủ thông tin bên dưới.
                </p>

                <p class="muted">
                    Mã mời cư dân được cấp bởi quản lý để xác minh quyền truy cập vào căn hộ.
                </p>

                <form method="POST" action="{{ route('resident.register.submit') }}">
                    @csrf

                    <div class="group">
                        <label for="name">Họ và tên</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nguyễn Văn A" required>
                        @error('name')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="phone">Số điện thoại</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="0901234567" required>
                        @error('phone')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="example@gmail.com" required>
                        @error('email')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="password">Mật khẩu</label>
                        <input id="password" type="password" name="password" placeholder="••••••••" required>
                        @error('password')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="password_confirmation">Nhập lại mật khẩu</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" required>
                    </div>

                    <div class="group">
                        <label for="invite_code">Mã mời cư dân</label>
                        <input id="invite_code" type="text" name="invite_code" value="{{ old('invite_code') }}" placeholder="INVITE-RESIDENT-001" required>
                        @error('invite_code')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button class="login-btn" type="submit">
                        Tạo tài khoản →
                    </button>
                </form>

                <p class="footer-link">
                    Đã có tài khoản? <a href="{{ route('resident.login') }}">Đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
