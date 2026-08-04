<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - DomusHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite([
        'resources/css/auth/login.css',
        'resources/css/auth/reset-password.css'
    ])
</head>

<body>
    <div class="container">
        <div class="left">
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab">
            <div class="overlay"></div>
            <div class="hero">
                <h1>
                    Đặt lại mật khẩu
                    <br>
                    an toàn
                </h1>
                <p>
                    Nhập mã xác nhận gồm 6 số được gửi về email để tạo mật khẩu mới cho tài khoản của bạn.
                </p>
            </div>
        </div>

        <div class="right">
            <div class="login-box">
                <div class="logo">
                    <i class="fa-solid fa-building"></i> DomusHub Portal
                </div>
                <h2>Nhập mã xác nhận</h2>
                <p class="sub">Nhập email, mã xác nhận và mật khẩu mới để hoàn tất khôi phục tài khoản.</p>

                @if (session('status'))
                    <div class="alert-success">
                        <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif

                <p class="helper">
                    Mã xác nhận có hiệu lực trong 15 phút. Nếu bạn chưa nhận được email, vui lòng yêu cầu gửi lại từ trang quên mật khẩu.
                </p>

                <form method="POST" action="{{ route('resident.reset-password.submit') }}">
                    @csrf

                    <div class="group">
                        <label for="email">Email</label>
                        <div class="input-box">
                            <i class="fa-regular fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="example@gmail.com" required>
                        </div>
                        @error('email')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="group">
                        <label for="code">Mã xác nhận</label>
                        <div class="input-box">
                            <i class="fa-solid fa-key"></i>
                            <input id="code" class="otp-input" type="text" name="code" value="{{ old('code') }}" placeholder="123456" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" required>
                        </div>
                        @error('code')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="group">
                        <label for="password">Mật khẩu mới</label>
                        <div class="input-box">
                            <i class="fa-solid fa-lock"></i>
                            <input id="password" type="password" name="password" placeholder="Tối thiểu 8 ký tự" minlength="8" required>
                        </div>
                        @error('password')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="group">
                        <label for="password_confirmation">Xác nhận mật khẩu mới</label>
                        <div class="input-box">
                            <i class="fa-solid fa-lock"></i>
                            <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu mới" minlength="8" required>
                        </div>
                    </div>

                    <button class="login-btn" type="submit">
                        Đặt lại mật khẩu
                    </button>
                </form>

                <div class="bottom">
                    <p><a href="{{ route('resident.forgot-password') }}"><i class="fa-solid fa-paper-plane"></i> Gửi lại mã xác nhận</a></p>
                    <p><a href="{{ route('resident.login') }}"><i class="fa-solid fa-arrow-left"></i> Quay lại đăng nhập</a></p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
