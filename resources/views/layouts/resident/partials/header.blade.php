<header class="resident-header">
    <div class="resident-header__brand">
        <span class="resident-header__logo">🏠</span>
        <div>
            <p class="resident-header__label">DomusHub</p>
            <h1 class="resident-header__title">Khu dân cư thông minh</h1>
        </div>
    </div>

    <nav class="resident-header__nav" aria-label="Resident navigation">
        <a href="{{ route('resident.dashboard') }}" class="resident-header__link resident-header__link--active">
            Tổng quan
        </a>
        <a href="#" class="resident-header__link">
            Hồ sơ
        </a>
        <a href="#" class="resident-header__link">
            Cài đặt
        </a>
    </nav>

    <div class="resident-header__actions">
        <div class="resident-header__user">
            <strong>{{ auth()->user()->name ?? 'Cư dân' }}</strong>
            <span>Cư dân</span>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="resident-header__logout">
                Đăng xuất
            </button>
        </form>
    </div>
</header>
