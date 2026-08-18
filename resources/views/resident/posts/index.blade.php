@extends('layouts.resident.master')

@section('title', 'Bản tin - DomusHub')

@push('styles')
    @vite(['resources/css/resident/posts.css'])
@endpush

@php
    $tab = request('tab', 'all');
    $importantAnnouncements = \App\Models\Announcement::where('status', 'published')->latest()->take(3)->get();
    
    // Lấy 2 hình ảnh mới nhất từ các bài viết
    $latestImages = \App\Models\PostImage::where('type', 'image')
        ->latest()
        ->take(2)
        ->get();
@endphp

@section('content')
<div class="df-layout">

    {{-- LEFT SIDEBAR: DANH MỤC --}}
    <div class="df-sidebar">
        {{-- User Info Snippet --}}
        <div class="df-user-snippet">
            @if(auth()->user() && auth()->user()->avatar)
                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="df-snippet-avatar" alt="">
            @else
                <div class="df-snippet-avatar df-avatar-placeholder">
                    {{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
            @endif
            <div class="df-snippet-info">
                <span class="df-snippet-name">{{ explode(' ', auth()->user()->name)[count(explode(' ', auth()->user()->name))-1] ?? auth()->user()->name }} Nguyen</span>
                <span class="df-snippet-meta">Căn hộ {{ auth()->user()->apartment->apartment_number ?? 'A-1204' }}</span>
            </div>
        </div>

        <div class="df-menu">
            <a href="{{ route('resident.posts.index', ['tab' => 'all']) }}" class="df-menu-item {{ $tab === 'all' ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Bảng tin cư dân
            </a>
            <a href="{{ route('resident.posts.index', ['tab' => 'admin']) }}" class="df-menu-item {{ $tab === 'admin' ? 'active' : '' }}">
                <i class="fa-solid fa-bullhorn"></i> Thông báo ban quản lý
            </a>
            <a href="{{ route('resident.posts.index', ['tab' => 'mine']) }}" class="df-menu-item {{ $tab === 'mine' ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Bài viết của tôi
            </a>
            <a href="{{ route('resident.facilities.index') }}" class="df-menu-item">
                <i class="fa-regular fa-calendar"></i> Sự kiện sắp tới
            </a>
        </div>
    </div>

    {{-- MAIN CONTENT: FEED --}}
    <div class="df-main">

        {{-- Create Post Box --}}
        <div class="df-create-post">
            <div class="df-create-post-top">
                @if(auth()->user() && auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="df-avatar" alt="">
                @else
                    <div class="df-avatar df-avatar-placeholder">
                        {{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                @endif
                <div class="df-create-post-input" onclick="window.location.href='{{ route('resident.posts.create') }}'">
                    {{ explode(' ', auth()->user()->name)[count(explode(' ', auth()->user()->name))-1] ?? auth()->user()->name }}, bạn đang nghĩ gì?
                </div>
            </div>
            <div class="df-create-post-bottom">
                <button class="df-create-btn" onclick="window.location.href='{{ route('resident.posts.create') }}'">
                    <i class="fa-regular fa-image" style="color: #10b981; font-size: 1.1rem;"></i> Hình ảnh/Video
                </button>
                <button class="df-post-submit-btn" onclick="window.location.href='{{ route('resident.posts.create') }}'">Đăng</button>
            </div>
        </div>

        {{-- Filters --}}
        <div class="df-filters">
            <a href="#" class="df-filter-btn active">Nổi bật</a>
            <a href="#" class="df-filter-btn">Gần đây</a>
            <a href="#" class="df-filter-btn">Ban quản lý</a>
        </div>

        {{-- Posts Loop --}}
        @forelse($posts as $post)
        <div class="df-post" id="post-card-{{ $post->id }}">
            <div class="df-post-header">
                <div class="df-post-user">
                    @if($post->user && $post->user->avatar)
                        <img src="{{ asset('storage/' . $post->user->avatar) }}" class="df-avatar" alt="">
                    @else
                        <div class="df-avatar df-avatar-placeholder" style="background:#000;">
                            <i class="fa-solid fa-shield-halved" style="color:#fff; font-size:0.9rem;"></i>
                        </div>
                    @endif
                    <div class="df-post-user-info">
                        <span class="df-post-name">
                            {{ $post->user->name ?? 'Ban Quản Lý Urban Living' }}
                            @if($post->user && $post->user->role === 'admin')
                                <i class="fa-solid fa-circle-check" style="color:#10b981;"></i>
                            @else
                                <i class="fa-solid fa-circle-check" style="color:#10b981;"></i>
                            @endif
                        </span>
                        <span class="df-post-meta">
                            {{ $post->created_at->diffForHumans() }} · Cập nhật tiện ích
                        </span>
                    </div>
                </div>
                <div style="position: relative;">
                    <button type="button" class="df-post-menu-btn" onclick="togglePostMenu(event, {{ $post->id }})">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>
                    <div class="df-post-dropdown" id="post-menu-{{ $post->id }}">
                        @if($post->user_id === auth()->id())
                            <a href="{{ route('resident.posts.edit', $post->id) }}" class="df-post-dropdown-item">
                                <i class="fa-regular fa-pen-to-square"></i> Sửa bài viết
                            </a>
                            <form action="{{ route('resident.posts.destroy', $post->id) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="df-post-dropdown-item df-post-dropdown-item--danger" onclick="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                    <i class="fa-regular fa-trash-can"></i> Xóa bài viết
                                </button>
                            </form>
                        @else
                            <button type="button" class="df-post-dropdown-item" onclick="reportPost(event, {{ $post->id }})">
                                <i class="fa-regular fa-flag"></i> Báo cáo bài viết
                            </button>
                            <button type="button" class="df-post-dropdown-item" onclick="hidePost(event, {{ $post->id }})">
                                <i class="fa-regular fa-eye-slash"></i> Ẩn bài viết
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="df-post-content">
                @if($post->title)
                    <h3 class="df-post-title">{{ $post->title }}</h3>
                @endif
                <p class="df-post-text">
                    @if($post->content)
                        {!! nl2br(e(strip_tags($post->content))) !!}
                    @endif
                </p>
            </div>

            <div class="df-post-images {{ $post->images->count() >= 2 ? 'grid-2' : '' }}">
                @if($post->images->isNotEmpty())
                    @foreach($post->images->take(2) as $img)
                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="">
                    @endforeach
                @endif
            </div>

            <div class="df-post-stats">
                <div class="df-post-stats-left">
                    <span class="df-stat-icon-wrap" style="background:#22c55e;"><i class="fa-solid fa-thumbs-up" style="color:#fff;font-size:0.6rem;"></i></span>
                    <span style="margin-left:6px; font-weight:600; color:#374151;">{{ $post->likes_count ?? 0 }}</span>
                </div>
                <div class="df-post-stats-right">
                    <span>{{ $post->comments_count ?? 0 }} bình luận</span>
                </div>
            </div>

            <div class="df-post-actions">
                <button type="button" class="df-action-btn rh-fb-like-btn {{ $post->likedByCurrentUser->isNotEmpty() ? 'active' : '' }}" data-post-id="{{ $post->id }}">
                    <i class="fa-regular fa-thumbs-up"></i> Thích
                </button>
                <a href="{{ route('resident.posts.show', $post->id) }}" class="df-action-btn" style="text-decoration:none;">
                    <i class="fa-regular fa-comment"></i> Bình luận
                </a>
                <button type="button" class="df-action-btn rh-fb-share-btn" data-url="{{ route('resident.posts.show', $post->id) }}">
                    <i class="fa-solid fa-share-nodes"></i> Chia sẻ
                </button>
            </div>
        </div>
        @empty
        <div class="df-empty">
            <i class="fa-regular fa-folder-open" style="font-size: 3rem; margin-bottom: 16px; opacity: 0.5;"></i>
            <p>Chưa có bài viết nào.</p>
        </div>
        @endforelse

    </div>

    {{-- RIGHT SIDEBAR: WIDGETS --}}
    <div class="df-right">
        {{-- Tiêu điểm cộng đồng --}}
        <div class="df-widget">
            <h4 class="df-widget-title">Tiêu điểm cộng đồng</h4>
            <div class="df-widget-list">
                @forelse($importantAnnouncements as $ann)
                <div class="df-event-item">
                    <span class="df-event-meta">{{ mb_strtoupper($ann->category ?? 'THÔNG BÁO') }}</span>
                    <a href="{{ route('resident.announcements.show', $ann->id) }}" style="text-decoration:none;">
                        <span class="df-event-title" style="color:#000;">{{ Str::limit($ann->title, 50) }}</span>
                    </a>
                    <span class="df-event-meta" style="font-size:0.75rem;">{{ $ann->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <div class="df-event-item">
                    <span class="df-event-title" style="color:#6b7280; font-weight:400;">Không có tiêu điểm nào gần đây.</span>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Hình ảnh nổi bật --}}
        <div class="df-widget">
            <h4 class="df-widget-title">Hình ảnh nổi bật</h4>
            <div class="df-gallery">
                @forelse($latestImages as $img)
                    <div class="df-gallery-img" style="background-image:url('{{ asset('storage/' . $img->image_path) }}');"></div>
                @empty
                    <p style="color:#6b7280; font-size:0.85rem; margin:0; grid-column:span 2;">Chưa có hình ảnh nào.</p>
                @endforelse
            </div>
            <a href="#" class="df-widget-link">Xem tất cả</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Like AJAX
document.querySelectorAll('.rh-fb-like-btn').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        const postId = this.dataset.postId;
        const card = this.closest('.df-post');
        const statsEl = card.querySelector('.df-post-stats-left span:last-child');
        try {
            const res = await fetch('{{ route("resident.posts.like") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: JSON.stringify({ likeable_id: postId, likeable_type: 'post', type: 'like' })
            });
            const data = await res.json();
            if (data.likes_count !== undefined) statsEl.textContent = data.likes_count;
            if (data.liked) {
                this.classList.add('active');
            } else {
                this.classList.remove('active');
            }
        } catch(e) { console.error(e); }
    });
});
// Share - Copy link
document.querySelectorAll('.rh-fb-share-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const url = this.dataset.url;
        navigator.clipboard.writeText(url).then(() => {
            alert('Đã sao chép liên kết bài viết!');
        });
    });
});

// Toggle Post dropdown
function togglePostMenu(e, id) {
    e.stopPropagation();
    document.querySelectorAll('.df-post-dropdown').forEach(d => {
        if (d.id !== 'post-menu-' + id) d.style.display = 'none';
    });
    const menu = document.getElementById('post-menu-' + id);
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
}

window.addEventListener('click', () => {
    document.querySelectorAll('.df-post-dropdown').forEach(d => d.style.display = 'none');
});

function reportPost(e, id) {
    e.stopPropagation();
    document.getElementById('post-menu-' + id).style.display = 'none';
    alert('Đã gửi báo cáo bài viết. Ban quản trị sẽ xem xét.');
}

function hidePost(e, id) {
    e.stopPropagation();
    document.getElementById('post-menu-' + id).style.display = 'none';
    const card = document.getElementById('post-card-' + id);
    card.style.opacity = '0.5';
    card.innerHTML = '<div style="padding:20px;text-align:center;color:#64748b;">Bài viết đã được ẩn.</div>';
}
</script>
@endpush
@endsection
