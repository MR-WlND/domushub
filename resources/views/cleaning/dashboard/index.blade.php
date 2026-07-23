<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DomusHub – Trang chủ Vệ sinh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', system-ui, sans-serif; }
        body { background: #F0FDF4; min-height: 100vh; }
        .navbar {
            background: white;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 700;
            color: #166534;
        }
        .navbar-brand i { font-size: 20px; }
        .navbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            color: #334155;
        }
        .logout-btn {
            background: none;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            color: #64748b;
            cursor: pointer;
            transition: .2s;
        }
        .logout-btn:hover { background: #fee2e2; color: #dc2626; border-color: #fecaca; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 24px; }
        .welcome-card {
            background: white;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 6px rgba(0,0,0,.04);
            text-align: center;
        }
        .welcome-card h1 { font-size: 24px; color: #0f172a; margin-bottom: 8px; }
        .welcome-card p { color: #64748b; font-size: 15px; }
        .welcome-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #166534, #16a34a);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            color: white;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <i class="fa-solid fa-broom"></i> DomusHub Vệ sinh
    </div>
    <div class="navbar-user">
        <span>{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </button>
        </form>
    </div>
</nav>

<div class="container">
    <div class="welcome-card">
        <div class="welcome-icon">
            <i class="fa-solid fa-broom"></i>
        </div>
        <h1>Chào mừng, {{ auth()->user()->name }}</h1>
        <p>Hệ thống quản lý công việc vệ sinh chung cư DomusHub.</p>
    </div>
</div>

</body>
</html>
