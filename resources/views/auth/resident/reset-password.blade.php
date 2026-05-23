<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - DomusHub</title>
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

        .helper {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .link-row {
            margin-top: 18px;
            text-align: center;
            font-size: 14px;
        }

        .link-row a {
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
                    Đặt lại mật khẩu
                    <br>
                    cho cư dân
                </h1>
                <p>
                    Nhập email, mã xác nhận và mật khẩu mới để hoàn tất quá trình khôi phục.
                </p>
            </div>
        </div>

        <div class="right">
            <div class="login-box">
                <div class="logo">🏠 Cư dân</div>
                <h2 class="title">Nhập mã xác nhận</h2>
                <p class="sub">Vui lòng hoàn tất các bước bên dưới.</p>

                @if (session('status'))
                    <div class="success-message" style="background:#ecfdf5; border:1px solid #86efac; color:#166534; padding:12px 14px; border-radius:10px; margin-bottom:16px; font-size:14px;">
                        {{ session('status') }}
                    </div>
                @endif

                <p class="helper">
                    Mã xác nhận có hiệu lực trong 15 phút. Nếu bạn chưa nhận được email, vui lòng yêu cầu gửi lại từ trang quên mật khẩu.
                </p>

                <form method="POST" action="{{ route('resident.reset-password.submit') }}">
                    @csrf

                    <div class="group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="example@gmail.com" required>
                        @error('email')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="code">Mã xác nhận</label>
                        <input id="code" type="text" name="code" value="{{ old('code') }}" placeholder="123456" maxlength="6" required>
                        @error('code')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="password">Mật khẩu mới</label>
                        <input id="password" type="password" name="password" placeholder="••••••••" required>
                        @error('password')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label for="password_confirmation">Nhập lại mật khẩu mới</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" required>
                    </div>

                    <button class="login-btn" type="submit">
                        Đặt lại mật khẩu →
                    </button>
                </form>

                <p class="link-row">
                    <a href="{{ route('resident.login') }}">← Quay lại đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
