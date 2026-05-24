<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DomusHub - Quên mật khẩu</title>

    @vite([
        'resources/css/auth/forgot-password.css'
    ])

</head>

<body>

    <div class="container">

        {{-- Left panel --}}
        <div class="left">

            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1200" alt="DomusHub">

            <div class="overlay"></div>

            <div class="content">

                <h1>
                    Khôi phục
                    <br>
                    tài khoản dễ dàng
                </h1>

                <p>
                    Nhập email của bạn, chúng tôi sẽ gửi
                    hướng dẫn đặt lại mật khẩu ngay lập tức.
                </p>

            </div>

        </div>

        {{-- Right panel --}}
        <div class="right">

            <div class="forgot-box">

                <div class="logo">DomusHub Portal</div>

                {{-- Step: Enter email --}}
                <div class="step" id="step-email">

                    <div class="icon-wrap">

                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>

                    </div>

                    <h2 class="title">Quên mật khẩu?</h2>

                    <p class="sub">
                        Nhập địa chỉ email đã đăng ký. Chúng tôi sẽ gửi
                        liên kết đặt lại mật khẩu vào hộp thư của bạn.
                    </p>

                    {{-- Validation errors --}}
                    <x-validation-errors />

                    {{-- Session status --}}
                    @if (session('status'))

                        <div class="alert-success">

                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>

                            <span>{{ session('status') }}</span>

                        </div>

                    @endif

                    <form method="POST" action="{{ route('password.email') }}">

                        @csrf

                        <div class="group">

                            <label for="email">Địa chỉ email</label>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="example@gmail.com"
                                class="{{ $errors->has('email') ? 'input-error' : '' }}"
                                required
                                autofocus>

                        </div>

                        <button type="submit" class="submit-btn">

                            Gửi liên kết đặt lại →

                        </button>

                    </form>

                </div>

                <div class="footer">

                    <a href="{{ route('login') }}" class="back-link">

                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12" />
                            <polyline points="12 19 5 12 12 5" />
                        </svg>

                        Quay lại đăng nhập

                    </a>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
