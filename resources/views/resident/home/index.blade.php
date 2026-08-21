@extends('layouts.resident.master')

@section('title', 'Trang chủ - DomusHub')

@push('styles')
    @vite(['resources/css/pages/resident/home/index.css'])
@endpush

@section('content')
@php
    // Xác định lời chào theo thời gian thực
    $hour = \Carbon\Carbon::now()->setTimezone('Asia/Ho_Chi_Minh')->format('H');
    if ($hour < 12) {
        $greeting = 'Chào buổi sáng';
    } elseif ($hour < 18) {
        $greeting = 'Chào buổi chiều';
    } else {
        $greeting = 'Chào buổi tối';
    }

    // Xác định trạng thái hệ thống / yêu cầu cư dân
    $pendingTickets = \App\Models\Ticket::where('sender_id', auth()->id())
        ->whereIn('status', ['pending', 'in_progress'])
        ->count();
    $statusText = $pendingTickets > 0 ? ($pendingTickets . ' YÊU CẦU ĐANG XỬ LÝ') : 'HỆ THỐNG HOẠT ĐỘNG TỐT';
    $statusColor = $pendingTickets > 0 ? '#fbbf24' : '#4ade80';

    // Dịch category sang Tiếng Việt nếu database lưu tiếng Anh
    $categoryMap = [
        'general' => 'Chung',
        'event' => 'Sự kiện',
        'maintenance' => 'Bảo trì',
        'urgent' => 'Khẩn cấp',
        'warning' => 'Khẩn cấp',
        'community' => 'Cộng đồng',
    ];
@endphp
<div class="rh-container">

    {{-- HERO SECTION --}}
    {{-- You can replace the background image URL with a real one --}}
    <div class="rh-hero" style="background-image: url('https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?q=80&w=2070&auto=format&fit=crop');">
        <div class="rh-hero-overlay"></div>
        <div class="rh-hero-content">
            <span class="rh-hero-status"><span class="dot" style="background-color: {{ $statusColor }};"></span> {{ $statusText }}</span>
            <h1 class="rh-hero-title">{{ $greeting }}, {{ explode(' ', $user->name)[count(explode(' ', $user->name))-1] ?? $user->name }}</h1>
        </div>
    </div>

    {{-- MAIN CONTENT GRID --}}
    <div class="rh-grid">
        
        {{-- LEFT COLUMN --}}
        <div class="rh-col-left">
            {{-- Billing Card --}}
            <div class="rh-card rh-billing">
                <div class="rh-billing-info">
                    <span class="rh-billing-label">PHÍ QUẢN LÝ & TIỆN ÍCH</span>
                    <h2 class="rh-billing-amount">{{ number_format($totalUnpaidAmount, 0, ',', '.') }} VND</h2>
                    <span class="rh-billing-due">
                        <i class="fa-regular fa-calendar"></i> Đến hạn: {{ $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d/m/Y') : 'Không có' }}
                    </span>
                </div>
                <a href="{{ route('resident.invoices.index') }}" class="rh-btn-primary">THANH TOÁN NGAY</a>
            </div>

            {{-- Shortcuts --}}
            <div>
                <h3 class="rh-section-title">Truy cập nhanh</h3>
                <div class="rh-shortcuts-grid">
                    <a href="{{ route('resident.facilities.index') }}" class="rh-shortcut-card">
                        <i class="fa-regular fa-calendar-check"></i>
                        <span>Đặt chỗ tiện ích</span>
                    </a>
                    <a href="{{ route('resident.visitors.index') }}" class="rh-shortcut-card">
                        <i class="fa-solid fa-qrcode"></i>
                        <span>Truy cập khách</span>
                    </a>
                    <a href="{{ route('resident.tickets.create') }}" class="rh-shortcut-card">
                        <i class="fa-solid fa-headset"></i>
                        <span>Phản ánh của cư dân</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="rh-col-right">
            @if($announcements->count() > 0)
                @php 
                    $ann = $announcements->first(); 
                    $catRaw = strtolower($ann->category ?? '');
                    $catText = $categoryMap[$catRaw] ?? ($ann->category ?? 'TIN TỨC');
                @endphp
                <div class="rh-news-card">
                    <div class="rh-news-img-wrap">
                        <span class="rh-news-tag">{{ mb_strtoupper($catText) }}</span>
                        @if($ann->image_path)
                            <img src="{{ asset('storage/' . $ann->image_path) }}" alt="" class="rh-news-img">
                        @else
                            <div class="rh-news-img" style="background:#e2e8f0;display:flex;align-items:center;justify-content:center;color:#94a3b8;"><i class="fa-solid fa-image fa-3x"></i></div>
                        @endif
                    </div>
                    <div class="rh-news-body">
                        <span class="rh-news-date">{{ $ann->created_at->format('d/m/Y') }}</span>
                        <h3 class="rh-news-title">{{ $ann->title }}</h3>
                        <p class="rh-news-excerpt">{{ Str::limit(strip_tags($ann->content), 100) }}</p>
                        <a href="{{ route('resident.announcements.index') }}" class="rh-news-link">XEM TẤT CẢ <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            @endif
        </div>
        
    </div>

</div>
@endsection
