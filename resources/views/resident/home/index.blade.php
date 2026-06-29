@extends('layouts.resident.master')

@section('title', 'Bảng điều khiển Cư dân – DomusHub')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .resident-dashboard {
            max-width: 800px;
            margin: 0 auto;
            padding-bottom: 48px;
        }
        
        /* Greeting Header */
        .welcome-header {
            margin-bottom: 24px;
            margin-top: 10px;
        }
        .welcome-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #0b1c30;
            margin: 0 0 6px;
            letter-spacing: -0.02em;
        }
        .welcome-header p {
            font-size: 15px;
            color: #64748b;
            margin: 0;
        }

        /* Premium Slideshow Wrapper */
        .announcements-slider-wrapper {
            position: relative;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            background: #ffffff;
        }
        
        /* Horizontal scroll list with snap */
        .announcements-slider {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none; /* Hide scrollbar for Firefox */
        }
        
        .announcements-slider::-webkit-scrollbar {
            display: none; /* Hide scrollbar for Chrome/Safari */
        }
        
        .announcement-slide {
            flex-shrink: 0;
            width: 100%;
            scroll-snap-align: start;
        }
        
        /* Vertical Slide Card Structure */
        .slide-card {
            display: flex;
            flex-direction: column;
            text-decoration: none;
            color: #1e293b;
            background: #ffffff;
            overflow: hidden;
            transition: background-color 0.2s ease;
        }
        
        .slide-card:hover {
            background-color: #f8fafc;
        }
        
        /* Top Image Container */
        .slide-card__img-container {
            position: relative;
            width: 100%;
            height: 180px;
            overflow: hidden;
            background: #f1f5f9;
        }
        
        .slide-card__img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Dark overlay for text readability */
        .slide-card__overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80%;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.45) 60%, rgba(15, 23, 42, 0) 100%);
            z-index: 1;
        }

        /* Overlaid content on image */
        .slide-card__image-content {
            position: absolute;
            bottom: 16px;
            left: 56px;
            right: 56px;
            z-index: 2;
            color: #ffffff;
        }
        
        /* Category Badge */
        .slide-card__badge {
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
            margin-bottom: 8px;
            width: fit-content;
        }
        .slide-card__badge--maintenance { background: #ea580c; }
        .slide-card__badge--warning { background: #dc2626; }
        .slide-card__badge--event { background: #16a34a; }
        .slide-card__badge--general { background: #2563eb; }
        .slide-card__badge--important { background: #b91c1c; } /* Red for important/pinned */
        
        .slide-card__title {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            line-height: 1.4;
            padding-top: 4px;
            padding-bottom: 2px;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Bottom Detail Area */
        .slide-card__bottom {
            display: flex;
            align-items: center;
            padding: 18px 24px;
            background: #ffffff;
            gap: 16px;
            border-top: 1px solid #f1f5f9;
        }

        /* Icon container on the left */
        .slide-card__icon-box {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .slide-card__icon-box--maintenance { background-color: #eff6ff; color: #2563eb; }
        .slide-card__icon-box--warning { background-color: #fef2f2; color: #dc2626; }
        .slide-card__icon-box--event { background-color: #f0fdf4; color: #16a34a; }
        .slide-card__icon-box--general { background-color: #f5f3ff; color: #7c3aed; }

        .slide-card__icon-box i {
            font-size: 18px;
        }

        /* Details on the right */
        .slide-card__details {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }

        .slide-card__subtitle {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 4px 0;
            line-height: 1.4;
            padding-top: 2px;
            padding-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .slide-card__desc {
            font-size: 13.5px;
            color: #64748b;
            margin: 0;
            line-height: 1.45;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Navigation Arrows */
        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            color: #64748b;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            z-index: 10;
            opacity: 0;
            pointer-events: none;
        }
        .announcements-slider-wrapper:hover .slider-arrow {
            opacity: 1;
            pointer-events: auto;
        }
        .slider-arrow:hover {
            background: #f8fafc;
            color: #0f172a;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .slider-arrow--prev {
            left: 12px;
        }
        .slider-arrow--next {
            right: 12px;
        }
        
        /* Pagination Dots */
        .slider-dots {
            position: absolute;
            bottom: 18px;
            right: 24px;
            display: flex;
            gap: 6px;
            z-index: 10;
        }
        .slider-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #cbd5e1;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .slider-dot--active {
            background: #475569;
            transform: scale(1.2);
        }

        /* Mobile responsiveness */
        @media (max-width: 640px) {
            .slide-card__img-container {
                height: 150px;
            }
            .slide-card__title {
                font-size: 18px;
            }
            .slide-card__bottom {
                padding: 14px 20px;
                gap: 12px;
            }
            .slider-dots {
                bottom: 14px;
                right: 20px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="resident-dashboard">
        {{-- Greeting Header --}}
        <div class="welcome-header">
            <h1>Xin chào, {{ auth()->user()->name ?? 'Cư dân' }}</h1>
            <p>
                @if($apartment)
                    Chào mừng bạn quay trở lại căn hộ {{ $apartment->apartment_number }}.
                @else
                    Chào mừng bạn quay trở lại DomusHub.
                @endif
            </p>
        </div>

        {{-- Announcement Board Card --}}
        @if($recentAnnouncements->count() > 0)
            <div class="announcements-slider-wrapper">
                <div class="announcements-slider" id="announcementsSlider">
                    @foreach($recentAnnouncements as $index => $notice)
                        <div class="announcement-slide" data-index="{{ $index }}">
                            <a href="{{ route('resident.announcements.show', $notice->id) }}" class="slide-card">
                                <div class="slide-card__img-container">
                                    @if($notice->image_path)
                                        <img src="{{ asset('storage/' . $notice->image_path) }}" class="slide-card__img" alt="{{ $notice->title }}">
                                    @else
                                        <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=800&q=80" class="slide-card__img" alt="Default announcement image">
                                    @endif
                                    
                                    {{-- Dark Overlay --}}
                                    <div class="slide-card__overlay"></div>
                                    
                                    {{-- Image Content Overlay --}}
                                    <div class="slide-card__image-content">
                                        <span class="slide-card__badge {{ $notice->pinned ? 'slide-card__badge--important' : 'slide-card__badge--' . $notice->category }}">
                                            @if($notice->pinned)
                                                Quan trọng
                                            @elseif($notice->category === 'maintenance')
                                                Bảo trì
                                            @elseif($notice->category === 'warning')
                                                Cảnh báo
                                            @elseif($notice->category === 'event')
                                                Sự kiện
                                            @else
                                                Tin chung
                                            @endif
                                        </span>
                                        <h3 class="slide-card__title">{{ $notice->title }}</h3>
                                    </div>
                                </div>
                                
                                {{-- Bottom Content Area --}}
                                <div class="slide-card__bottom">
                                    {{-- Category Icon Box --}}
                                    <div class="slide-card__icon-box slide-card__icon-box--{{ $notice->category }}">
                                        @if($notice->category === 'maintenance')
                                            <i class="fa-solid fa-wrench"></i>
                                        @elseif($notice->category === 'warning')
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                        @elseif($notice->category === 'event')
                                            <i class="fa-solid fa-calendar-days"></i>
                                        @else
                                            <i class="fa-solid fa-bullhorn"></i>
                                        @endif
                                    </div>
                                    
                                    {{-- Details --}}
                                    <div class="slide-card__details">
                                        <h4 class="slide-card__subtitle">
                                            @if($notice->category === 'maintenance')
                                                Lịch bảo trì kỹ thuật
                                            @elseif($notice->category === 'warning')
                                                Cảnh báo khẩn cấp
                                            @elseif($notice->category === 'event')
                                                Sự kiện chung cư
                                            @else
                                                Tin tức ban quản lý
                                            @endif
                                        </h4>
                                        <p class="slide-card__desc">
                                            {{ Str::limit(strip_tags($notice->content), 90) }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Interactive controls (only visible if there are multiple slides) --}}
                @if($recentAnnouncements->count() > 1)
                    <button class="slider-arrow slider-arrow--prev" onclick="moveSlider(-1)" aria-label="Previous slide">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="slider-arrow slider-arrow--next" onclick="moveSlider(1)" aria-label="Next slide">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    
                    <div class="slider-dots">
                        @foreach($recentAnnouncements as $index => $notice)
                            <span class="slider-dot {{ $index === 0 ? 'slider-dot--active' : '' }}" onclick="goToSlide({{ $index }})"></span>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            {{-- Empty state when there are no notices --}}
            <div style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 16px; padding: 60px 20px; text-align: center; color: #64748b; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                <i class="fa-regular fa-bell-slash" style="font-size: 36px; color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
                Chưa có thông báo chính thức từ Ban Quản Lý.
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    @if($recentAnnouncements->count() > 1)
        <script>
            let currentSlide = 0;
            const slider = document.getElementById('announcementsSlider');
            const slidesCount = {{ $recentAnnouncements->count() }};
            const dots = document.querySelectorAll('.slider-dot');

            function updateActiveDot(index) {
                dots.forEach((dot, idx) => {
                    if (idx === index) {
                        dot.classList.add('slider-dot--active');
                    } else {
                        dot.classList.remove('slider-dot--active');
                    }
                });
            }

            function moveSlider(direction) {
                currentSlide = (currentSlide + direction + slidesCount) % slidesCount;
                goToSlide(currentSlide);
            }

            function goToSlide(index) {
                currentSlide = index;
                const slideWidth = slider.clientWidth;
                slider.scrollTo({
                    left: slideWidth * index,
                    behavior: 'smooth'
                });
                updateActiveDot(index);
            }

            // Sync dots if resident swipes manually
            let scrollTimeout;
            slider.addEventListener('scroll', () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    const slideWidth = slider.clientWidth;
                    const index = Math.round(slider.scrollLeft / slideWidth);
                    if (index >= 0 && index < slidesCount) {
                        currentSlide = index;
                        updateActiveDot(index);
                    }
                }, 100);
            });



            // Sync on window resize
            window.addEventListener('resize', () => {
                goToSlide(currentSlide);
            });
        </script>
    @endif
@endpush
