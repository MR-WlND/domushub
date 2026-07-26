<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Đăng ký Cư dân</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/auth/login.css'])
</head>


<body>
    <div class="container">
        <!-- LEFT -->
        <div class="left">
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=768&h=1024&fit=crop"
                alt="building">
            <div class="overlay"></div>
            <div class="hero">
                <h1>Tham gia cùng<br>DomusHub</h1>
                <p>Đăng ký tài khoản để trải nghiệm dịch vụ quản lý cư dân minh bạch và hiện đại.</p>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="right">
            <div class="login-box">

                <div class="logo">
                    <i class="fa-solid fa-building"></i> DomusHub Portal
                </div>

                <h2>Tạo tài khoản</h2>
                <p class="sub">Vui lòng nhập đầy đủ thông tin để đăng ký.</p>

                @if (session('error'))
                    <div class="alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('resident.register.submit') }}">
                    @csrf

                    <div class="group">
                        <label>Họ và tên</label>
                        <div class="input-box">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="name" placeholder="Nguyễn Văn A" value="{{ old('name') }}"
                                required>
                        </div>
                        @error('name')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label>Số điện thoại</label>
                        <div class="input-box">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" name="phone" placeholder="0901234567" value="{{ old('phone') }}"
                                required>
                        </div>
                        @error('phone')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label>Email</label>
                        <div class="input-box">
                            <i class="fa-regular fa-envelope"></i>
                            <input type="email" name="email" placeholder="ten@vidu.com" value="{{ old('email') }}"
                                required>
                        </div>
                        @error('email')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label>Mật khẩu</label>
                        <div class="input-box">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="password" placeholder="••••••••" required>
                            <i class="fa-regular fa-eye toggle-pw" id="toggleIcon" onclick="togglePassword()"></i>
                        </div>
                        @error('password')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="group">
                        <label>Nhập lại mật khẩu</label>
                        <div class="input-box">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password_confirmation" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="group">
                        <label>Mã mời cư dân (Bắt buộc)</label>
                        <div class="input-box">
                            <i class="fa-solid fa-key"></i>
                            <input type="text" name="invite_code" placeholder="RES-XXXXXXXX"
                                value="{{ old('invite_code') }}" required>
                        </div>
                        @error('invite_code')
                            <span class="error-msg">{{ $message }}</span>
                        @enderror
                        <small class="hint-text">Nhập mã mời do quản trị viên hoặc chủ căn hộ cung cấp.</small>
                    </div>

                    <button type="submit" class="login-btn">
                        Tạo tài khoản <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <div class="bottom">
                    <p>Đã có tài khoản? <a href="{{ route('resident.login') }}">Đăng nhập</a></p>
                    <small>
                        <span>Chính sách bảo mật</span>
                        <span>Điều khoản dịch vụ</span>
                    </small>
                </div>

            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.className = isHidden ? 'fa-regular fa-eye-slash toggle-pw' : 'fa-regular fa-eye toggle-pw';
        }
    </script>

</body>

<body>
<div class="container">

