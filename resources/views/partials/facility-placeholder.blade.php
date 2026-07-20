@php
    $nameLower = strtolower($name);
@endphp

@if(str_contains($nameLower, 'bơi') || str_contains($nameLower, 'pool'))
    <!-- Pool Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" class="{{ $class ?? 'icon-svg' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M2 6c.6.5 1.2 1 2.5 1C6 7 7 6 8.5 6c1.5 0 2.5 1 4 1s2.5-1 4-1 2.5 1 4 1 1.9-.5 2.5-1"/>
        <path d="M2 12c.6.5 1.2 1 2.5 1 1.5 0 2.5-1 4-1s2.5 1 4 1 2.5-1 4-1 2.5 1 4 1 1.9-.5 2.5-1"/>
        <path d="M2 18c.6.5 1.2 1 2.5 1 1.5 0 2.5-1 4-1s2.5 1 4 1 2.5-1 4-1 2.5 1 4 1 1.9-.5 2.5-1"/>
    </svg>
@elseif(str_contains($nameLower, 'gym') || str_contains($nameLower, 'tập') || str_contains($nameLower, 'thể hình'))
    <!-- Gym Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" class="{{ $class ?? 'icon-svg' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="9" width="3" height="6" rx="1"/>
        <rect x="19" y="9" width="3" height="6" rx="1"/>
        <rect x="5" y="5" width="3" height="14" rx="1"/>
        <rect x="16" y="5" width="3" height="14" rx="1"/>
        <line x1="8" y1="12" x2="16" y2="12"/>
    </svg>
@elseif(str_contains($nameLower, 'bbq') || str_contains($nameLower, 'nướng'))
    <!-- BBQ Icon (Flame) -->
    <svg xmlns="http://www.w3.org/2000/svg" class="{{ $class ?? 'icon-svg' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
    </svg>
@elseif(str_contains($nameLower, 'tennis'))
    <!-- Tennis Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" class="{{ $class ?? 'icon-svg' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="10" cy="10" r="7"/>
        <path d="M10 3v14"/>
        <path d="M3 10h14"/>
        <path d="m15 15 6 6"/>
        <path d="m19 15 2 2"/>
    </svg>
@elseif(str_contains($nameLower, 'đọc') || str_contains($nameLower, 'sách') || str_contains($nameLower, 'thư viện'))
    <!-- Book Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" class="{{ $class ?? 'icon-svg' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
        <path d="M6 6h10"/>
        <path d="M6 10h10"/>
        <path d="M6 14h10"/>
    </svg>
@elseif(str_contains($nameLower, 'sinh hoạt') || str_contains($nameLower, 'hội') || str_contains($nameLower, 'cộng đồng'))
    <!-- Community/Users Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" class="{{ $class ?? 'icon-svg' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
    </svg>
@else
    <!-- Building Icon -->
    <svg xmlns="http://www.w3.org/2000/svg" class="{{ $class ?? 'icon-svg' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="3" y="3" width="18" height="18" rx="2"/>
        <path d="M9 3v18"/>
        <path d="M15 3v18"/>
        <path d="M3 9h18"/>
        <path d="M3 15h18"/>
    </svg>
@endif
