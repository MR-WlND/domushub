<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Quên mật khẩu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/auth/login.css'])
</head>
<body>
<div class="container">

    <!-- LEFT -->
    <div class="left">
        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=768&h=1024&fit=crop" alt="building">
        <div class="overlay"></div>
        <div class="hero">
            <h1>Khôi phục<br>tài khoản</h1>
            <p>Nhập email để nhận mã xác nhận và đặt lại mật khẩu của bạn.</p>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <div class="login-box">

            <div class="logo">
                <i class="fa-solid fa-building"></i> DomusHub Portal
            </div>

            <h2>Quên mật khẩu?</h2>
            <p class="sub">Nhập email đã đăng ký, chúng tôi sẽ gửi mã xác nhận trong vài phút.</p>

            @if(session('status'))
                <div class="alert-success">
                    <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('resident.forgot-password.submit') }}">
                @csrf

                <div class="group">
                    <label>Email</label>
                    <div class="input-box">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="email" placeholder="ten@vidu.com" value="{{ old('email') }}" required>
                    </div>
                    @error('email')<span class="error-msg">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="login-btn">
                    Gửi mã xác nhận <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>

            <div class="bottom">
                <p><a href="{{ route('resident.login') }}"><i class="fa-solid fa-arrow-left"></i> Quay lại đăng nhập</a></p>
            </div>

        </div>
    </div>

</div>
</body>
</html>
