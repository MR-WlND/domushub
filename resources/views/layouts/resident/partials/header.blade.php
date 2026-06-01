<header class="resident-header">
    <div class="resident-header__brand">
        <a href="{{ route('resident.dashboard') }}" class="resident-header__brand-link">DomusHub</a>
    </div>

    <nav class="resident-header__nav" aria-label="Resident navigation">
        <a href="{{ route('resident.dashboard') }}" class="resident-header__link {{ request()->routeIs('resident.dashboard') ? 'resident-header__link--active' : '' }}">
            Home
        </a>
        <a href="#" class="resident-header__link">
            Support
        </a>
        <a href="#" class="resident-header__link">
            FAQs
        </a>
        <a href="#" class="resident-header__link">
            Contact
        </a>
    </nav>

    <div class="resident-header__actions">
        @php
            $ownerMember = null;
            $pendingMembers = collect();
            if (auth()->check()) {
                $ownerMember = \App\Models\ApartmentMember::where('user_id', auth()->id())
                    ->where('relationship', 'owner')
                    ->first();

                if ($ownerMember) {
                    $pendingMembers = \App\Models\ApartmentMember::where('apartment_id', $ownerMember->apartment_id)
                        ->where('status', 'pending')
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            }
        @endphp

        @if($pendingMembers->isNotEmpty())
            <div class="header-icon-wrapper resident-header__pending-members">
                <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5s-3 1.34-3 3 1.34 3 3 3z"></path>
                    <path d="M6 11c1.66 0 2.99-1.34 2.99-3S7.66 5 6 5 3 6.34 3 8s1.34 3 3 3z"></path>
                    <path d="M6 14c-2.33 0-7 1.17-7 3.5V20h20v-2.5C19 15.17 14.33 14 12 14"></path>
                </svg>
                <span class="header-badge">{{ $pendingMembers->count() }}</span>
                <div class="resident-pending-dropdown">
                    <div class="pending-list">
                        @foreach($pendingMembers as $pm)
                            <div class="pending-item">
                                <div class="pending-item__main">
                                    <strong>{{ $pm->name }}</strong>
                                    <span class="pending-item__rel">{{ $pm->relationship }}</span>
                                </div>
                                <div class="pending-item__sub">
                                    @if($pm->phone) <span>{{ $pm->phone }}</span> @endif
                                    @if($pm->email) <span>• {{ $pm->email }}</span> @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="pending-actions">
                        <a href="{{ route('resident.members.index') }}">Quản lý nhân khẩu</a>
                    </div>
                </div>
            </div>
        @endif
        <div class="header-icon-wrapper">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
        </div>

        <div class="header-icon-wrapper">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </div>

        <div class="resident-header__user-menu">
            <div class="resident-header__user-avatar-container">
                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Avatar" class="resident-header__user-avatar">
            </div>
            <div class="resident-header__dropdown">
                <div class="dropdown-info">
                    <strong>{{ auth()->user()->name ?? 'Cư dân' }}</strong>
                    <span>Cư dân</span>
                </div>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item">Hồ sơ</a>
                <a href="#" class="dropdown-item">Cài đặt</a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('resident-logout-form').submit();" class="dropdown-item dropdown-item--logout">Đăng xuất</a>
                <form id="resident-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</header>
