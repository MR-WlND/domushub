<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Đăng nhập Cư dân</title>
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
            <h1>Nâng tầm cuộc sống<br>tại DomusHub</h1>
            <p>Trải nghiệm dịch vụ quản lý cư dân minh bạch, an toàn và hiện đại dành riêng cho bạn.</p>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="right">
        <div class="login-box">

            <div class="logo">
                <i class="fa-solid fa-building"></i> DomusHub Portal
            </div>

            <h2>Chào mừng trở lại</h2>
            <p class="sub">Vui lòng nhập thông tin để truy cập tài khoản của bạn.</p>

            <form method="POST" action="{{ route('resident.login.submit') }}">
                @csrf

                <div class="group">
                    <label>Email hoặc Số điện thoại</label>
                    <div class="input-box">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="email" placeholder="ten@vidu.com" value="{{ old('email') }}" required>
                    </div>
                    @error('email')<span class="error-msg">{{ $message }}</span>@enderror
                </div>

                <div class="group">
                    <label>Mã căn hộ</label>
                    <div class="input-box">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Nhập mã căn hộ" required>
                    </div>
                </div>

                <div class="forgot">
                    <a href="#">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="login-btn">
                    Đăng nhập <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="divider"><span>HOẶC TIẾP TỤC VỚI</span></div>

            <div class="support">
                <i class="fa-solid fa-circle-info"></i>
                <div>
                    <strong>Cần hỗ trợ kỹ thuật?</strong>
                    <p>Liên hệ Ban quản lý hoặc hotline 1900-XXXX</p>
                </div>
            </div>

            <div class="bottom">
                <p>Bạn chưa có tài khoản? <a href="#">Đăng ký ngay</a></p>
                <small>
                    <span>Chính sách bảo mật</span>
                    <span>Điều khoản dịch vụ</span>
                </small>
            </div>

        </div>
    </div>

</div>
</body>
</html>
