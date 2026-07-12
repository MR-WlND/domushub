@extends('layouts.resident.master')

@section('title', 'Trang chủ - DomusHub')

@push('styles')
    @vite(['resources/css/pages/resident/home/index.css'])
@endpush

@section('content')
<div class="rh">

    {{-- Welcome --}}
    <div class="rh-welcome">
        <h1>Xin chào, {{ $user->name }}</h1>
        <p>Chào mừng bạn quay trở lại căn hộ {{ $apartment->apartment_number ?? '' }}.</p>
    </div>

    {{-- Hero: Debt + Announce --}}
    <div class="rh-hero">
        <div class="rh-debt-card">
            <div>
                <span class="rh-debt__label">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    Số dư hiện tại
                </span>
                <div class="rh-debt__amount">{{ number_format($totalUnpaidAmount, 0, ',', '.') }} <small>VNĐ</small></div>
                @if($dueDate)
                    <span class="rh-debt__due">Hạn thanh toán: {{ \Carbon\Carbon::parse($dueDate)->format('d/m/Y') }}</span>
                @endif
            </div>
            <a href="{{ route('resident.invoices.index') }}" class="rh-debt__btn" aria-label="Thanh toán hoá đơn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Thanh toán ngay
            </a>
        </div>

        <div class="rh-announce-card" role="region" aria-label="Thông báo quan trọng">
            @if($announcements->isNotEmpty())
                <div class="rh-announce-slider" id="announceSlider">
                    @foreach($announcements as $i => $ann)
                    <div class="rh-announce-slide" role="group" aria-label="Thông báo {{ $i + 1 }}">
                        <div class="rh-announce__visual">
                            @if($ann->image_path)
                                <img src="{{ asset('storage/' . $ann->image_path) }}" class="rh-announce__img" alt="{{ $ann->title }}" loading="lazy">
                            @else
                                <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=600&q=80" class="rh-announce__img" alt="Chung cư" loading="lazy">
                            @endif
                            @if($i === 0 && $ann->pinned)
                                <span class="rh-announce__badge">Quan trọng</span>
                            @endif
                        </div>
                        <div class="rh-announce__body">
                            <h4 class="rh-announce__title">{{ Str::limit($ann->title, 50) }}</h4>
                            <p class="rh-announce__time">{{ $ann->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($announcements->count() > 1)
                <div class="rh-announce-dots" id="announceDots" role="tablist">
                    @foreach($announcements as $i => $ann)
                        <span class="rh-dot {{ $i === 0 ? 'rh-dot--active' : '' }}" data-index="{{ $i }}" role="tab" tabindex="0" aria-label="Thông báo {{ $i + 1 }}"></span>
                    @endforeach
                </div>
                @endif
            @else
                <div class="rh-announce__empty">Không có thông báo</div>
            @endif
        </div>
    </div>

    {{-- Phím tắt nhanh (ngay dưới hero) --}}
    <div class="rh-shortcuts">
        <a href="{{ route('resident.invoices.index') }}" class="rh-shortcut" aria-label="Thanh toán">
            <div class="rh-shortcut__icon rh-shortcut__icon--pay" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div><span class="rh-shortcut__label">Thanh toán</span><span class="rh-shortcut__desc">Xem lịch sử & phí</span></div>
        </a>
        <a href="{{ route('resident.tickets.create') }}" class="rh-shortcut" aria-label="Báo hỏng">
            <div class="rh-shortcut__icon rh-shortcut__icon--report" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div><span class="rh-shortcut__label">Báo hỏng</span><span class="rh-shortcut__desc">Gửi yêu cầu kỹ thuật</span></div>
        </a>
        <a href="{{ route('resident.visitors.create') }}" class="rh-shortcut" aria-label="Đăng ký khách">
            <div class="rh-shortcut__icon rh-shortcut__icon--visitor" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            </div>
            <div><span class="rh-shortcut__label">Đăng ký khách</span><span class="rh-shortcut__desc">Cấp mã QR ra vào</span></div>
        </a>
        <a href="{{ route('resident.posts.create') }}" class="rh-shortcut" aria-label="Bảng tin">
            <div class="rh-shortcut__icon rh-shortcut__icon--post" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div><span class="rh-shortcut__label">Bảng tin</span><span class="rh-shortcut__desc">Xem & đăng bài viết</span></div>
        </a>
    </div>

    {{-- Bài viết cư dân (FB-style) --}}
    <div class="rh-section">
        <h3 class="rh-section__title">Bài viết từ cư dân</h3>
        <div class="rh-section__actions">
            <a href="{{ route('resident.posts.create') }}" class="rh-section__btn" aria-label="Đăng bài viết mới">+ Đăng bài</a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:12px 18px;background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;border-radius:10px;font-size:0.85rem;font-weight:600;margin-bottom:16px;">{{ session('success') }}</div>
    @endif

    <div style="display:flex;gap:16px;margin-bottom:16px;border-bottom:1px solid #f1f5f9;padding-bottom:12px;">
        <a href="{{ route('resident.dashboard') }}" style="font-size:0.85rem;font-weight:{{ !request()->filled('type') ? '700' : '500' }};color:{{ !request()->filled('type') ? '#0f172a' : '#64748b' }};text-decoration:none;">Tất cả</a>
        <a href="{{ route('resident.dashboard', ['type' => 'mine']) }}" style="font-size:0.85rem;font-weight:{{ request('type')==='mine' ? '700' : '500' }};color:{{ request('type')==='mine' ? '#0f172a' : '#64748b' }};text-decoration:none;">Của tôi</a>
    </div>

    <div class="rh-fb-feed">
            @forelse($posts as $post)
            <div class="rh-fb-card">
                {{-- Header: avatar + name + time --}}
                <div class="rh-fb-card__header">
                    <div class="rh-fb-card__avatar">
                        @if($post->user->avatar)
                            <img src="{{ asset('storage/' . $post->user->avatar) }}" alt="" loading="lazy">
                        @else
                            <span>{{ mb_substr($post->user->name ?? '?', 0, 1) }}</span>
                        @endif
                    </div>
                    <div>
                        <span class="rh-fb-card__name">{{ $post->user->name ?? 'Cư dân' }}</span>
                        <span class="rh-fb-card__time">{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                {{-- Content text --}}
                @if($post->content)
                <div class="rh-fb-card__text">
                    {{ Str::limit(strip_tags($post->content), 250) }}
                    @if(strlen(strip_tags($post->content)) > 250)
                        <a href="{{ route('resident.posts.show', $post->id) }}" class="rh-fb-card__more-text">Xem thêm</a>
                    @endif
                </div>
                @endif

                {{-- Media --}}
                @if($post->images->isNotEmpty())
                @php $imgCount = $post->images->count(); @endphp
                @if($imgCount === 1 && ($post->images[0]->type ?? 'image') === 'video')
                <div class="rh-fb-card__media" style="margin-top:12px;">
                    <video src="{{ asset('storage/' . $post->images[0]->image_path) }}" class="rh-fb-card__img-single" controls preload="metadata" style="max-height:500px;width:100%;object-fit:cover;display:block;"></video>
                </div>
                @else
                <a href="{{ route('resident.posts.show', $post->id) }}" class="rh-fb-card__media">
                    @if($imgCount === 1)
                        <img src="{{ asset('storage/' . $post->images[0]->image_path) }}" class="rh-fb-card__img-single" alt="" loading="lazy">
                    @elseif($imgCount === 2)
                        <div class="rh-fb-card__img-grid rh-fb-card__img-grid--2">
                            @foreach($post->images->take(2) as $img)
                                @if(($img->type ?? 'image') === 'video')
                                    <video src="{{ asset('storage/' . $img->image_path) }}" controls preload="metadata" style="width:100%;height:300px;object-fit:cover;"></video>
                                @else
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="" loading="lazy">
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="rh-fb-card__img-grid rh-fb-card__img-grid--3plus">
                            @if(($post->images[0]->type ?? 'image') === 'video')
                                <video src="{{ asset('storage/' . $post->images[0]->image_path) }}" class="rh-fb-card__img-main" controls preload="metadata" style="height:320px;width:100%;object-fit:cover;"></video>
                            @else
                                <img src="{{ asset('storage/' . $post->images[0]->image_path) }}" class="rh-fb-card__img-main" alt="" loading="lazy">
                            @endif
                            <div class="rh-fb-card__img-side">
                                @foreach($post->images->slice(1, 2) as $img)
                                    @if(($img->type ?? 'image') === 'video')
                                        <video src="{{ asset('storage/' . $img->image_path) }}" controls preload="metadata" style="width:100%;height:159px;object-fit:cover;"></video>
                                    @else
                                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="" loading="lazy">
                                    @endif
                                @endforeach
                                @if($imgCount > 3)
                                    <div class="rh-fb-card__img-overlay">+{{ $imgCount - 3 }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </a>
                @endif
                @endif

                {{-- Stats row --}}
                <div class="rh-fb-card__stats">
                    <span>{{ $post->likes_count ?? 0 }} lượt thích</span>
                    <span>{{ $post->comments_count ?? 0 }} bình luận</span>
                </div>

                {{-- Action bar --}}
                <div class="rh-fb-card__actions">
                    <button type="button" class="rh-fb-card__action rh-fb-like-btn {{ $post->likedByCurrentUser->isNotEmpty() ? 'rh-fb-like-btn--active' : '' }}" data-post-id="{{ $post->id }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                        Thích
                    </button>
                    <a href="{{ route('resident.posts.show', $post->id) }}" class="rh-fb-card__action">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Bình luận
                    </a>
                    <button type="button" class="rh-fb-card__action rh-fb-share-btn" data-url="{{ route('resident.posts.show', $post->id) }}">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                        Chia sẻ
                    </button>
                </div>
            </div>
            @empty
            <div class="rh-empty">
                <p class="rh-empty__text">Chưa có bài viết nào</p>
                <a href="{{ route('resident.posts.create') }}" class="rh-empty__btn">Đăng bài đầu tiên</a>
            </div>
            @endforelse

        @if($posts->hasPages())
        <div style="margin-top:20px;">{{ $posts->links() }}</div>
        @endif
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('announceSlider');
    if (!slider) return;
    const slides = slider.querySelectorAll('.rh-announce-slide');
    const dots = document.querySelectorAll('#announceDots .rh-dot');
    if (slides.length <= 1) return;
    let current = 0, interval;
    function show(i) { slides.forEach(s => s.style.display = 'none'); dots.forEach(d => d.classList.remove('rh-dot--active')); slides[i].style.display = 'flex'; dots[i].classList.add('rh-dot--active'); current = i; }
    function next() { show((current + 1) % slides.length); }
    interval = setInterval(next, 5000);
    dots.forEach(d => { d.addEventListener('click', function() { clearInterval(interval); show(+this.dataset.index); interval = setInterval(next, 5000); }); });
    slider.addEventListener('mouseenter', () => clearInterval(interval));
    slider.addEventListener('mouseleave', () => { interval = setInterval(next, 5000); });
    let sx = 0;
    slider.addEventListener('touchstart', e => sx = e.touches[0].clientX);
    slider.addEventListener('touchend', e => { const d = sx - e.changedTouches[0].clientX; if (Math.abs(d) > 40) { clearInterval(interval); d > 0 ? next() : show((current-1+slides.length)%slides.length); interval = setInterval(next, 5000); }});
});

// Like AJAX
document.querySelectorAll('.rh-fb-like-btn').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        const postId = this.dataset.postId;
        const card = this.closest('.rh-fb-card');
        const statsEl = card.querySelector('.rh-fb-card__stats span:first-child');
        try {
            const res = await fetch('{{ route("resident.posts.like") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: JSON.stringify({ likeable_id: postId, likeable_type: 'post', type: 'like' })
            });
            const data = await res.json();
            if (data.likes_count !== undefined) statsEl.textContent = data.likes_count + ' lượt thích';
            if (data.liked) {
                this.classList.add('rh-fb-like-btn--active');
            } else {
                this.classList.remove('rh-fb-like-btn--active');
            }
        } catch(e) { console.error(e); }
    });
});
// Share - Copy link
document.querySelectorAll('.rh-fb-share-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const url = this.dataset.url;
        navigator.clipboard.writeText(url).then(() => {
            const orig = this.innerHTML;
            this.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Đã sao chép';
            setTimeout(() => { this.innerHTML = orig; }, 2000);
        });
    });
});
</script>
@endpush
@endsection
