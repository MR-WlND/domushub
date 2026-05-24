<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Đăng ký - DomusHub Portal</title>
    @vite(['resources/css/auth/register.css'])
</head>

<body>

    {{-- ===== HEADER ===== --}}
    <header class="header">
        <div class="header__logo">DomusHub Portal</div>
        <a href="#" class="header__support">Hỗ trợ</a>
    </header>

    {{-- ===== MAIN ===== --}}
    <main class="main">

        {{-- Left panel --}}
        <div class="left">
            <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=1400" alt="DomusHub">
            <div class="overlay"></div>
            <div class="left__content">
                <h1>
                    Chào mừng bạn đến với
                    <br>
                    cộng đồng thông minh.
                </h1>
                <p>
                    Trải nghiệm sự tiện nghi tối ưu trong quản lý căn hộ.
                    Từ thanh toán hóa đơn đến đăng ký dịch vụ, mọi thứ đều nằm trong tầm tay bạn.
                </p>
                <ul class="feature-list">
                    <li>
                        <span class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                        </span>
                        <div>
                            <strong>An ninh tối đa</strong>
                            <p>Hệ thống xác thực mã căn hộ độc quyền đảm bảo tính minh bạch.</p>
                        </div>
                    </li>
                    <li>
                        <span class="feature-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                            </svg>
                        </span>
                        <div>
                            <strong>Xử lý nhanh chóng</strong>
                            <p>Yêu cầu bảo trì và hỗ trợ được phản hồi tức thì.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Right panel --}}
        <div class="right">
            <div class="form-box">

                <h2 class="form-title">Đăng ký tài khoản</h2>
                <p class="form-sub">Điền thông tin bên dưới để bắt đầu sử dụng DomusHub.</p>

                {{-- Validation errors --}}
                <x-validation-errors />

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Họ và tên --}}
                    <div class="group">
                        <label for="name">Họ và tên</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </span>
                            <input id="name" type="text" name="name" value="{{ old('name') }}"
                                placeholder="Nguyễn Văn A"
                                class="{{ $errors->has('name') ? 'input-error' : '' }}"
                                required autofocus>
                        </div>
                    </div>

                    {{-- Số điện thoại + Mã mời (2 cột) --}}
                    <div class="form-row">

                        <div class="group">
                            <label for="phone">Số điện thoại</label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.42 2 2 0 0 1 3.6 1h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.56a16 16 0 0 0 6.29 6.29l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                                    </svg>
                                </span>
                                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}"
                                    placeholder="0901 234 567"
                                    class="{{ $errors->has('phone') ? 'input-error' : '' }}"
                                    required>
                            </div>
                        </div>

                        <div class="group">
                            <label for="invite_code">Mã mời cư dân</label>
                            <div class="input-wrapper">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                    </svg>
                                </span>
                                <input id="invite_code" type="text" name="invite_code"
                                    value="{{ old('invite_code') }}"
                                    placeholder="VIN-B1-1205"
                                    class="input-invite {{ $errors->has('invite_code') ? 'input-error' : '' }}"
                                    required autocomplete="off">
                            </div>
                        </div>

                    </div>

                    {{-- Email --}}
                    <div class="group">
                        <label for="email">Địa chỉ Email</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                    <polyline points="22,6 12,13 2,6" />
                                </svg>
                            </span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="email@example.com"
                                class="{{ $errors->has('email') ? 'input-error' : '' }}"
                                required>
                        </div>
                    </div>

                    {{-- Mật khẩu --}}
                    <div class="group">
                        <label for="password">Mật khẩu</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                            <input id="password" type="password" name="password"
                                placeholder="••••••••"
                                class="{{ $errors->has('password') ? 'input-error' : '' }}"
                                required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password', this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Xác nhận mật khẩu --}}
                    <div class="group">
                        <label for="password_confirmation">Xác nhận mật khẩu</label>
                        <div class="input-wrapper">
                            <span class="input-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0 1 12 2.944a11.955 11.955 0 0 1-8.618 3.04A12.02 12.02 0 0 0 3 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </span>
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                placeholder="••••••••"
                                required>
                            <button type="button" class="toggle-password"
                                onclick="togglePassword('password_confirmation', this)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Điều khoản --}}
                    <div class="terms">
                        <input id="terms" type="checkbox" name="terms" required>
                        <label for="terms">
                            Tôi đồng ý với
                            <a href="#">Điều khoản</a>
                            &amp;
                            <a href="#">Chính sách bảo mật</a>
                        </label>
                    </div>

                    <button type="submit" class="submit-btn">
                        Đăng ký ngay →
                    </button>

                </form>

                <p class="footer-link">
                    Bạn đã có tài khoản?
                    <a href="{{ route('login') }}">Đăng nhập</a>
                </p>

            </div>
        </div>

    </main>

    {{-- ===== FOOTER ===== --}}
    <footer class="footer">
        <span class="footer__brand">DomusHub Portal &copy; {{ date('Y') }} DomusHub Management Solutions. All rights reserved.</span>
        <nav class="footer__nav">
            <a href="#">Chính sách</a>
            <a href="#">Điều khoản</a>
            <a href="#">Trợ năng</a>
            <a href="#">Liên hệ hỗ trợ</a>
        </nav>
    </footer>

    <script>
        function togglePassword(id, btn) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.classList.toggle('active');
        }

        document.getElementById('invite_code').addEventListener('input', function () {
            const pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });
    </script>

</body>
</html>
