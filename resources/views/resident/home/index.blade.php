@extends('layouts.resident.master')

@section('title', 'Trang chủ - DomusHub')

@push('styles')
    @vite(['resources/css/pages/resident/home/index.css', 'resources/css/resident/posts.css'])
@endpush

@section('content')
<div class="rh">
    @if($apartment)

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
                    <a href="{{ route('resident.announcements.show', $ann->id) }}" class="rh-announce-slide" role="group" aria-label="Thông báo {{ $i + 1 }}" style="text-decoration: none; color: inherit;">
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
                    </a>
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
        <a href="{{ route('resident.vehicles.index') }}" class="rh-shortcut" aria-label="Phương tiện">
            <div class="rh-shortcut__icon rh-shortcut__icon--visitor" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h2m10 0h2M2 9l2-6h16l2 6M2 9h20v8a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V9z"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
            </div>
            <div><span class="rh-shortcut__label">Phương tiện</span><span class="rh-shortcut__desc">Quản lý xe của bạn</span></div>
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

    <div class="rh-fb-feed">
            @forelse($posts as $post)
            <div class="rh-fb-card" id="post-card-{{ $post->id }}">
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

                    {{-- Nút ... tùy chọn --}}
                    <div class="rh-fb-card__menu">
                        <button type="button" class="rh-fb-card__menu-btn" onclick="togglePostMenu(event, {{ $post->id }})" aria-label="Tùy chọn">
                            <i class="fa-solid fa-ellipsis"></i>
                        </button>
                        <div class="rh-fb-card__dropdown" id="post-menu-{{ $post->id }}">
                            @if($post->user_id === auth()->id())
                                <a href="{{ route('resident.posts.edit', $post->id) }}" class="rh-fb-card__dropdown-item">
                                    <i class="fa-regular fa-pen-to-square"></i> Sửa bài viết
                                </a>
                            @endif
                            @if($post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
                                <form action="{{ route('resident.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirmDeletePost(event, this)" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rh-fb-card__dropdown-item rh-fb-card__dropdown-item--danger">
                                        <i class="fa-regular fa-trash-can"></i> Xóa bài viết
                                    </button>
                                </form>
                            @endif
                            @if($post->user_id !== auth()->id())
                                <button type="button" class="rh-fb-card__dropdown-item" onclick="openReportModal(event, {{ $post->id }})">
                                    <i class="fa-regular fa-flag"></i> Báo cáo bài viết
                                </button>
                                <button type="button" class="rh-fb-card__dropdown-item" onclick="hidePost(event, {{ $post->id }})">
                                    <i class="fa-regular fa-eye-slash"></i> Ẩn bài viết
                                </button>
                            @endif
                        </div>
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
    @else
    {{-- Empty State cho User chưa có căn hộ --}}
    <div class="rh-empty-apartment" style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-top: 20px;">
        <div style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;">
            <i class="fa-solid fa-house-circle-xmark"></i>
        </div>
        <h2 style="font-size: 1.5rem; color: #0f172a; margin-bottom: 12px; font-weight: 700;">Bạn chưa tham gia căn hộ nào</h2>
        <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 24px; max-width: 500px; margin-left: auto; margin-right: auto; line-height: 1.6;">
            Tài khoản của bạn hiện không được liên kết với bất kỳ căn hộ nào. Vui lòng liên hệ Ban quản lý, hoặc yêu cầu Chủ hộ cấp <b>Mã mời</b> để gia nhập vào căn hộ.
        </p>
        <a href="{{ route('resident.members.index') }}" class="rh-section__btn" style="padding: 12px 24px; font-size: 1rem;">
            <i class="fa-solid fa-key" style="margin-right: 8px;"></i> Nhập mã mời gia nhập
        </a>
    </div>
    @endif

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
<script>
let pendingHidePostId = null;

// Dropdown control
function togglePostMenu(event, postId) {
    event.stopPropagation();
    // Close all other dropdowns
    document.querySelectorAll('.rh-fb-card__dropdown').forEach(dropdown => {
        if (dropdown.id !== 'post-menu-' + postId) {
            dropdown.style.display = 'none';
        }
    });
    const dropdown = document.getElementById('post-menu-' + postId);
    if (dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
    } else {
        dropdown.style.display = 'block';
    }
}

// Close dropdowns on document click
document.addEventListener('click', function() {
    document.querySelectorAll('.rh-fb-card__dropdown').forEach(dropdown => {
        dropdown.style.display = 'none';
    });
});

// Toast notification helper
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.style.cssText = `padding:0.75rem 1.25rem;border-radius:10px;box-shadow:0 8px 16px rgba(0,0,0,0.1);color:white;font-size:0.875rem;font-weight:600;display:flex;align-items:center;gap:0.65rem;opacity:0;transform:translateY(20px);transition:all 0.3s cubic-bezier(0.4,0,0.2,1);background-color:${type === 'success' ? '#10b981' : '#ef4444'};`;
    const icon = type === 'success' ? '<i class="fa-solid fa-circle-check"></i>' : '<i class="fa-solid fa-triangle-exclamation"></i>';
    toast.innerHTML = `${icon} <span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; }, 10);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(20px)'; setTimeout(() => toast.remove(), 300); }, 3500);
}

// Hide post function
function hidePost(event, postId) {
    event.preventDefault();
    pendingHidePostId = postId;
    const modal = document.getElementById('confirmHideModal');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
}

// Close hide modal
function closeHideModal() {
    const modal = document.getElementById('confirmHideModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
    pendingHidePostId = null;
}

function handleHideOutsideClick(e) {
    if (e.target === document.getElementById('confirmHideModal')) {
        closeHideModal();
    }
}

// Bind confirmation button
document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirm-hide-btn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', async function() {
            if (!pendingHidePostId) return;
            const postId = pendingHidePostId;
            closeHideModal();
            try {
                const res = await fetch(`/resident/posts/${postId}/hide`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Đã ẩn bài viết thành công!');
                    // Fade out and remove post card from DOM
                    const card = document.getElementById('post-card-' + postId);
                    if (card) {
                        card.style.transition = 'all 0.5s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            card.remove();
                            // If no posts left, show empty state
                            const feed = document.querySelector('.rh-fb-feed');
                            if (feed && feed.querySelectorAll('.rh-fb-card').length === 0) {
                                feed.innerHTML = `<div class="rh-empty"><p class="rh-empty__text">Chưa có bài viết nào</p><a href="{{ route('resident.posts.create') }}" class="rh-empty__btn">Đăng bài đầu tiên</a></div>`;
                            }
                        }, 500);
                    }
                } else {
                    showToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại.', 'error');
                }
            } catch(e) {
                console.error(e);
                showToast('Không thể kết nối máy chủ.', 'error');
            }
        });
    }
});

// Report Modal functions
function openReportModal(event, postId) {
    event.preventDefault();
    document.getElementById('report-target-post-id').value = postId;
    document.getElementById('report-post-form').reset();
    document.getElementById('custom-reason-container').style.display = 'none';
    document.getElementById('submit-report-btn').setAttribute('disabled', 'disabled');
    
    const modal = document.getElementById('reportPostModal');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
}

function closeReportModal() {
    const modal = document.getElementById('reportPostModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function handleOutsideModalClick(e) {
    if (e.target === document.getElementById('reportPostModal')) {
        closeReportModal();
    }
}

function toggleCustomReason(show) {
    const customContainer = document.getElementById('custom-reason-container');
    const textarea = document.getElementById('report-reason-custom');
    if (show) {
        customContainer.style.display = 'block';
        textarea.setAttribute('required', 'required');
    } else {
        customContainer.style.display = 'none';
        textarea.removeAttribute('required');
    }
    checkReportFormStatus();
}

function checkReportFormStatus() {
    const submitBtn = document.getElementById('submit-report-btn');
    const presets = document.querySelectorAll('input[name="reason_preset"]:checked');
    if (presets.length === 0) {
        submitBtn.setAttribute('disabled', 'disabled');
        return;
    }
    const val = presets[0].value;
    if (val === 'other') {
        const text = document.getElementById('report-reason-custom').value.trim();
        if (text.length > 0) {
            submitBtn.removeAttribute('disabled');
        } else {
            submitBtn.setAttribute('disabled', 'disabled');
        }
    } else {
        submitBtn.removeAttribute('disabled');
    }
}

// Attach listener to custom reason textarea
document.addEventListener('DOMContentLoaded', function() {
    const customTextarea = document.getElementById('report-reason-custom');
    if (customTextarea) {
        customTextarea.addEventListener('input', checkReportFormStatus);
    }
    
    // Handle report form submit
    const reportForm = document.getElementById('report-post-form');
    if (reportForm) {
        reportForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const postId = document.getElementById('report-target-post-id').value;
            const presets = document.querySelectorAll('input[name="reason_preset"]:checked');
            if (presets.length === 0) return;
            
            let reason = presets[0].value;
            if (reason === 'other') {
                reason = document.getElementById('report-reason-custom').value.trim();
            }
            
            try {
                const res = await fetch(`/resident/posts/${postId}/report`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ reason: reason })
                });
                const data = await res.json();
                if (data.success) {
                    closeReportModal();
                    showToast(data.message || 'Báo cáo bài viết thành công.');
                    
                    // Fade out and remove reported post from feed
                    const card = document.getElementById('post-card-' + postId);
                    if (card) {
                        card.style.transition = 'all 0.5s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            card.remove();
                            // If no posts left, show empty state
                            const feed = document.querySelector('.rh-fb-feed');
                            if (feed && feed.querySelectorAll('.rh-fb-card').length === 0) {
                                feed.innerHTML = `<div class="rh-empty"><p class="rh-empty__text">Chưa có bài viết nào</p><a href="{{ route('resident.posts.create') }}" class="rh-empty__btn">Đăng bài đầu tiên</a></div>`;
                            }
                        }, 500);
                    }
                } else {
                    showToast(data.message || 'Không thể gửi báo cáo.', 'error');
                }
            } catch(err) {
                console.error(err);
                showToast('Có lỗi xảy ra khi kết nối máy chủ.', 'error');
            }
        });
    }
});
</script>

{{-- Modal Báo cáo Bài viết --}}
<div id="reportPostModal" class="rep-modal" onclick="handleOutsideModalClick(event)" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="rep-modal__content" style="background: #fff; padding: 24px; border-radius: 12px; max-width: 450px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.15); position: relative; animation: postDropdownFadeIn 0.3s ease-out; box-sizing: border-box;">
        <div class="rep-modal__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 class="rep-modal__title" style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #00236f; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #dc2626;"></i> Báo cáo bài viết
            </h3>
            <button type="button" class="rep-modal__close" onclick="closeReportModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <form id="report-post-form">
            @csrf
            <input type="hidden" name="post_id" id="report-target-post-id" value="">
            <div class="rep-modal__body" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                <span class="rep-modal__label" style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px;">Tại sao bạn muốn báo cáo bài viết này?</span>
                <label class="rep-option" style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: #334155; cursor: pointer;"><input type="radio" name="reason_preset" value="Spam, quảng cáo rác" onclick="toggleCustomReason(false)"><span class="rep-option__text">Spam, quảng cáo rác</span></label>
                <label class="rep-option" style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: #334155; cursor: pointer;"><input type="radio" name="reason_preset" value="Từ ngữ thô tục, công kích" onclick="toggleCustomReason(false)"><span class="rep-option__text">Từ ngữ thô tục, công kích</span></label>
                <label class="rep-option" style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: #334155; cursor: pointer;"><input type="radio" name="reason_preset" value="Lừa đảo, giả mạo" onclick="toggleCustomReason(false)"><span class="rep-option__text">Lừa đảo, giả mạo thông tin</span></label>
                <label class="rep-option" style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: #334155; cursor: pointer;"><input type="radio" name="reason_preset" value="Nội dung phản cảm, thù địch" onclick="toggleCustomReason(false)"><span class="rep-option__text">Nội dung phản cảm, thù địch</span></label>
                <label class="rep-option" style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: #334155; cursor: pointer;"><input type="radio" name="reason_preset" value="other" onclick="toggleCustomReason(true)"><span class="rep-option__text">Lý do khác...</span></label>
                <div class="rep-modal__custom-reason" id="custom-reason-container" style="display: none; margin-top: 8px;">
                    <textarea name="reason_custom" id="report-reason-custom" class="rep-modal__textarea" placeholder="Nhập lý do cụ thể..." style="width: 100%; padding: 10px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 0.88rem; outline: none; font-family: inherit; resize: vertical; box-sizing: border-box;" rows="3"></textarea>
                </div>
            </div>
            <div class="rep-modal__footer" style="display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 14px;">
                <button type="button" class="rh-section__btn" style="background: #64748b;" onclick="closeReportModal()">Hủy bỏ</button>
                <button type="submit" class="rh-section__btn" id="submit-report-btn" disabled style="background: #dc2626;">Gửi báo cáo</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Xác nhận Ẩn Bài viết --}}
<div id="confirmHideModal" class="rep-modal" onclick="handleHideOutsideClick(event)" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="rep-modal__content" style="background: #fff; padding: 24px; border-radius: 12px; max-width: 400px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.15); position: relative; animation: postDropdownFadeIn 0.3s ease-out; box-sizing: border-box; text-align: center;">
        <div style="font-size: 2.5rem; color: #eab308; margin-bottom: 12px;">
            <i class="fa-solid fa-circle-question"></i>
        </div>
        <h3 style="margin: 0 0 10px; font-size: 1.15rem; font-weight: 700; color: #00236f;">Ẩn bài viết này?</h3>
        <p style="margin: 0 0 20px; font-size: 0.88rem; color: #64748b; line-height: 1.4;">Bài viết này sẽ không còn hiển thị trên bảng tin của bạn nữa. Bạn vẫn muốn tiếp tục ẩn chứ?</p>
        <div style="display: flex; justify-content: center; gap: 12px;">
            <button type="button" class="rh-section__btn" style="background: #64748b; padding: 10px 20px;" onclick="closeHideModal()">Hủy bỏ</button>
            <button type="button" class="rh-section__btn" id="confirm-hide-btn" style="background: #2563eb; padding: 10px 20px;">Đồng ý ẩn</button>
        </div>
    </div>
</div>

@if($popupAnnouncement)
    {{-- Modal Thông báo Khẩn cấp --}}
    <div id="emergencyAnnouncementModal" class="rep-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 20000; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
        <div class="rep-modal__content" style="background: var(--color-card, #fff); padding: 32px; border-radius: 20px; max-width: 550px; width: 90%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid rgba(220, 38, 38, 0.1); position: relative; transform: scale(0.9); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); text-align: center; box-sizing: border-box;">
            
            {{-- Header Icon --}}
            <div style="width: 72px; height: 72px; background: #fee2e2; color: #dc2626; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem; box-shadow: 0 10px 15px -3px rgba(220, 38, 38, 0.2);">
                @if($popupAnnouncement->category === 'warning')
                    <i class="fa-solid fa-triangle-exclamation animate-bounce"></i>
                @elseif($popupAnnouncement->category === 'maintenance')
                    <i class="fa-solid fa-wrench"></i>
                @else
                    <i class="fa-solid fa-bullhorn"></i>
                @endif
            </div>

            {{-- Category badge --}}
            <span style="display: inline-block; padding: 6px 16px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;
                @if($popupAnnouncement->category === 'warning')
                    background: #fee2e2; color: #dc2626;
                @elseif($popupAnnouncement->category === 'maintenance')
                    background: #e0f2fe; color: #0369a1;
                @else
                    background: #f1f5f9; color: #475569;
                @endif
            ">
                @if($popupAnnouncement->category === 'warning')
                    Cảnh báo khẩn cấp
                @elseif($popupAnnouncement->category === 'maintenance')
                    Thông báo bảo trì
                @elseif($popupAnnouncement->category === 'event')
                    Sự kiện chung cư
                @else
                    Thông báo chung
                @endif
            </span>

            {{-- Title --}}
            <h2 style="margin: 0 0 12px; font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1.3;">
                {{ $popupAnnouncement->title }}
            </h2>

            {{-- Time --}}
            <p style="margin: 0 0 20px; font-size: 0.8rem; color: #64748b; font-weight: 500;">
                <i class="fa-regular fa-clock" style="margin-right: 4px;"></i> Đăng {{ $popupAnnouncement->created_at->diffForHumans() }}
            </p>

            {{-- Announcement Image if exists --}}
            @if($popupAnnouncement->image_path)
                <div style="width: 100%; max-height: 200px; border-radius: 12px; overflow: hidden; margin-bottom: 20px; border: 1px solid #f1f5f9;">
                    <img src="{{ asset('storage/' . $popupAnnouncement->image_path) }}" style="width: 100%; height: 100%; object-fit: cover;" alt="Hình ảnh thông báo">
                </div>
            @endif

            {{-- Content --}}
            <div style="font-size: 0.95rem; color: #334155; line-height: 1.6; margin-bottom: 28px; text-align: left; max-height: 250px; overflow-y: auto; padding: 0 10px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; padding-top: 16px; padding-bottom: 16px;">
                {!! $popupAnnouncement->content !!}
            </div>

            {{-- Actions --}}
            <div style="display: flex; justify-content: center; gap: 12px;">
                <button type="button" onclick="dismissEmergencyAnnouncement({{ $popupAnnouncement->id }})" style="background: #0f172a; color: white; border: none; padding: 12px 30px; font-size: 0.9rem; font-weight: 700; border-radius: 12px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.1); width: 100%; max-width: 200px;" onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background='#0f172a'">
                    Đã đọc & Đóng
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const popupId = {{ $popupAnnouncement->id }};
        const dismissed = localStorage.getItem('dismissed_announcement_' + popupId);
        
        if (dismissed !== 'true') {
            const modal = document.getElementById('emergencyAnnouncementModal');
            if (modal) {
                modal.style.display = 'flex';
                // Force reflow
                modal.offsetHeight;
                modal.style.opacity = '1';
                
                const content = modal.querySelector('.rep-modal__content');
                if (content) {
                    content.style.transform = 'scale(1)';
                }
            }
        }
    });

    function dismissEmergencyAnnouncement(popupId) {
        const modal = document.getElementById('emergencyAnnouncementModal');
        if (modal) {
            modal.style.opacity = '0';
            const content = modal.querySelector('.rep-modal__content');
            if (content) {
                content.style.transform = 'scale(0.9)';
            }
            
            setTimeout(() => {
                modal.style.display = 'none';
                localStorage.setItem('dismissed_announcement_' + popupId, 'true');
            }, 300);
        }
    }
    </script>
@endif

{{-- Toast Container --}}
<div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 10001; display: flex; flex-direction: column; gap: 0.5rem; max-width: 350px;"></div>

@endpush
@endsection
