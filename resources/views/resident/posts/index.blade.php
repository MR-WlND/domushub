@extends('layouts.resident.master')

@section('title', 'Bản tin - DomusHub')

@push('styles')
    @vite(['resources/css/resident/posts.css'])
@endpush

@php
    $tab = request('tab', 'all');
    $importantAnnouncements = \App\Models\Announcement::where('status', 'published')->latest()->take(3)->get();
    
    // Lấy 30 hình ảnh mới nhất từ các bài viết cho Gallery
    $galleryImages = \App\Models\PostImage::where('type', 'image')
        ->latest()
        ->take(30)
        ->get();
        
    // Lấy 2 hình ảnh mới nhất để hiển thị ở Widget
    $featuredImages = $galleryImages->take(2);

    $userPostsCount = 0;
    $userLikesCount = 0;
    if ($tab === 'mine' && auth()->check()) {
        $userPostsCount = auth()->user()->posts()->count();
        $userLikesCount = auth()->user()->posts()->withCount('likes')->get()->sum('likes_count');
    }
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
            <a href="{{ route('resident.posts.index') }}" class="df-menu-item {{ request()->routeIs('resident.posts.index') && request('tab') !== 'mine' ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Bảng tin cư dân
            </a>
            <a href="{{ route('resident.announcements.index') }}" class="df-menu-item {{ request()->routeIs('resident.announcements.*') && request('tab') !== 'event' ? 'active' : '' }}">
                <i class="fa-solid fa-bullhorn"></i> Thông báo ban quản lý
            </a>
            <a href="{{ route('resident.posts.index', ['tab' => 'mine']) }}" class="df-menu-item {{ request('tab') === 'mine' ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Bài viết của tôi
            </a>
            <a href="{{ route('resident.announcements.index', ['tab' => 'event']) }}" class="df-menu-item {{ request('tab') === 'event' ? 'active' : '' }}">
                <i class="fa-regular fa-calendar"></i> Sự kiện sắp tới
            </a>
        </div>
    </div>

    {{-- MAIN CONTENT: FEED --}}
    <div class="df-main">

        @if($tab === 'mine')
        {{-- Profile Header Card (User Stats) --}}
        <div class="df-profile-header-card">
            <div class="df-phc-left">
                @if(auth()->user() && auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="df-phc-avatar" alt="">
                @else
                    <div class="df-phc-avatar df-avatar-placeholder">
                        {{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                @endif
                <div class="df-phc-info">
                    <div class="df-phc-name">{{ auth()->user()->name }}</div>
                    <div class="df-phc-apartment">Căn hộ {{ auth()->user()->apartment->apartment_number ?? 'A-1204' }}</div>
                </div>
            </div>
            <div class="df-phc-right">
                <div class="df-phc-stat">
                    <span class="df-phc-stat-num">{{ $userPostsCount ?? 0 }}</span>
                    <span class="df-phc-stat-label">BÀI VIẾT</span>
                </div>
                <div class="df-phc-stat">
                    <span class="df-phc-stat-num">
                        @php
                            $likes = $userLikesCount ?? 0;
                            echo $likes > 999 ? number_format($likes/1000, 1) . 'k' : $likes;
                        @endphp
                    </span>
                    <span class="df-phc-stat-label">LƯỢT THÍCH</span>
                </div>
            </div>
        </div>
        @endif

        {{-- Create Post Box --}}
        <div class="df-create-post">
            <div class="df-create-post-top">
                <a href="{{ route('resident.posts.index', ['tab' => 'mine']) }}" style="text-decoration: none; flex-shrink: 0; display: block;">
                    @if(auth()->user() && auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="df-avatar" alt="">
                    @else
                        <div class="df-avatar df-avatar-placeholder">
                            {{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                </a>
                <div class="df-create-post-input" onclick="openCreatePostModal()">
                    {{ explode(' ', auth()->user()->name)[count(explode(' ', auth()->user()->name))-1] ?? auth()->user()->name }}, bạn đang nghĩ gì?
                </div>
            </div>
            <div class="df-create-post-bottom">
                <div style="display: flex; gap: 8px;">
                    <button class="df-create-btn" onclick="openCreatePostModal()">
                        <i class="fa-regular fa-image" style="color: #10b981; font-size: 1.1rem;"></i> Ảnh/Video
                    </button>
                    <button class="df-create-btn" onclick="openCreatePostModal()">
                        <i class="fa-regular fa-calendar" style="color: #6366f1; font-size: 1.1rem;"></i> Sự kiện
                    </button>
                </div>
                <button class="df-post-submit-btn" onclick="openCreatePostModal()">Đăng</button>
            </div>
        </div>

        {{-- Mobile Nav (Hiển thị tab trên điện thoại) --}}
        <div class="df-mobile-nav">
            <a href="{{ route('resident.posts.index') }}" class="df-post-submit-btn" style="text-decoration: none; {{ request()->routeIs('resident.posts.index') && request('tab') !== 'mine' ? '' : 'background: #f1f5f9; color: #475569;' }}">Tất cả</a>
            <a href="{{ route('resident.posts.index', ['tab' => 'mine']) }}" class="df-post-submit-btn" style="text-decoration: none; {{ request('tab') === 'mine' ? '' : 'background: #f1f5f9; color: #475569;' }}">Của tôi</a>
        </div>

        {{-- Removed Filters --}}

        {{-- Posts Loop --}}
        @forelse($posts as $post)
        <div class="df-post" id="post-card-{{ $post->id }}">
            <div class="df-post-header">
                <div class="df-post-user">
                    @if($post->user && $post->user->avatar)
                        <img src="{{ asset('storage/' . $post->user->avatar) }}" class="df-avatar" alt="">
                    @else
                        <div class="df-avatar df-avatar-placeholder" style="background:#00236f;">
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
                <div class="df-post-text">
                    @if($post->content)
                        @php
                            // Cho phép các thẻ định dạng văn bản cơ bản
                            $allowed = '<p><br><strong><b><i><em><ul><ol><li><u>';
                            $safe = strip_tags($post->content, $allowed);
                            // Xóa tất cả các thuộc tính (như onclick, style, class...) để chống XSS
                            $safe = preg_replace('/<([a-z][a-z0-9]*)[^>]*?(\/?)>/si', '<$1$2>', $safe);
                        @endphp
                        {!! nl2br($safe) !!}
                    @endif
                </div>
            </div>

            <div class="df-post-images layout-count-{{ min($post->images->count(), 5) }}">
                @if($post->images->isNotEmpty())
                    @foreach($post->images->take(5) as $img)
                        @if($img->type === 'video')
                            <video src="{{ asset('storage/' . $img->image_path) }}" controls style="max-height: 400px; width: 100%; object-fit: cover;"></video>
                        @else
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="" style="max-height: 400px; width: 100%; object-fit: cover; cursor: pointer;" onclick="openSingleImageModal('{{ asset('storage/' . $img->image_path) }}')">
                        @endif
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
                <button type="button" class="df-action-btn" onclick="openPostDetailModal({{ $post->id }})" style="text-decoration:none;">
                    <i class="fa-regular fa-comment"></i> Bình luận
                </button>
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
        {{-- Thông báo căn hộ --}}
        <div class="df-widget df-announcement-widget">
            <h4 class="df-widget-title df-announcement-title">
                <i class="fa-solid fa-bullhorn" style="margin-right: 8px;"></i> Thông báo căn hộ
            </h4>
            <div class="df-announcement-list">
                @forelse($importantAnnouncements as $ann)
                @php
                    $catRaw = strtolower($ann->category ?? '');
                    $catMap = [
                        'general' => ['text' => 'CHUNG', 'color' => '#6b7280'],
                        'event' => ['text' => 'SỰ KIỆN', 'color' => '#10b981'],
                        'maintenance' => ['text' => 'BẢO TRÌ', 'color' => '#6b7280'],
                        'warning' => ['text' => 'QUAN TRỌNG', 'color' => '#dc2626', 'icon' => true],
                        'community' => ['text' => 'CỘNG ĐỒNG', 'color' => '#3b82f6'],
                    ];
                    $catData = $catMap[$catRaw] ?? ['text' => 'THÔNG BÁO', 'color' => '#6b7280'];
                @endphp
                <div class="df-announcement-item">
                    <div class="df-announcement-header">
                        <span class="df-announcement-badge" style="color: {{ $catData['color'] }}">
                            @if(isset($catData['icon']) && $catData['icon'])
                                <i class="fa-solid fa-circle" style="font-size: 0.5rem; margin-right: 6px; position: relative; top: -1px;"></i>
                            @endif
                            {{ $catData['text'] }}
                        </span>
                        <span class="df-announcement-date">
                            @if($ann->created_at->isToday())
                                Hôm nay
                            @elseif($ann->created_at->isYesterday())
                                Hôm qua
                            @else
                                {{ $ann->created_at->format('d/m') }}
                            @endif
                        </span>
                    </div>
                    <a href="{{ route('resident.announcements.show', $ann->id) }}" class="df-announcement-link">
                        {{ $ann->title }}
                    </a>
                </div>
                @empty
                <div class="df-announcement-item" style="border: none;">
                    <span style="color:#6b7280; font-size: 0.9rem;">Không có thông báo nào.</span>
                </div>
                @endforelse
            </div>
            <a href="{{ route('resident.announcements.index') }}" class="df-announcement-view-all">XEM TẤT CẢ</a>
        </div>

        {{-- Hình ảnh nổi bật --}}
        <div class="df-widget">
            <h4 class="df-widget-title">Hình ảnh nổi bật</h4>
            <div class="df-gallery">
                @forelse($featuredImages as $index => $img)
                    <div class="df-gallery-img" style="background-image:url('{{ asset('storage/' . $img->image_path) }}'); cursor:pointer;" onclick="openGalleryModal({{ $index }})"></div>
                @empty
                    <p style="color:#6b7280; font-size:0.85rem; margin:0; grid-column:span 2;">Chưa có hình ảnh nào.</p>
                @endforelse
            </div>
            @if($galleryImages->isNotEmpty())
                <a href="javascript:void(0)" class="df-widget-link" onclick="openGalleryModal(0)">Xem tất cả</a>
            @else
                <a href="#" class="df-widget-link" style="opacity: 0.5; pointer-events: none;">Xem tất cả</a>
            @endif
        </div>
    </div>
</div>

{{-- GALLERY MODAL --}}
@if($galleryImages->isNotEmpty())
<div id="gallery-modal" class="df-gallery-modal">
    <div class="df-gallery-modal-overlay" onclick="closeGalleryModal()"></div>
    
    <button class="df-gallery-modal-close" onclick="closeGalleryModal()">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <div class="df-gallery-modal-counter">
        <span id="gallery-current">1</span> / {{ $galleryImages->count() }}
    </div>

    <button class="df-gallery-modal-prev" onclick="prevGalleryImage()">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    
    <button class="df-gallery-modal-next" onclick="nextGalleryImage()">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    <div class="df-gallery-modal-content">
        <img id="gallery-modal-img" src="" alt="">
    </div>
</div>
@endif

{{-- CREATE POST MODAL --}}
<style>
    /* Loại bỏ viền mặc định của CKEditor để liền mạch với Modal */
    .ck-editor__editable_inline {
        border: none !important;
        box-shadow: none !important;
        padding: 0 4px !important;
        min-height: 100px;
        font-family: inherit !important;
        font-size: 1.05rem !important;
    }
    .ck-editor__editable.ck-focused:not(.ck-editor__nested-editable) {
        border: none !important;
        box-shadow: none !important;
    }
    .ck.ck-toolbar {
        border: none !important;
        border-bottom: 1px solid #e5e7eb !important;
        background: transparent !important;
        padding: 0 0 8px 0 !important;
        margin-bottom: 12px !important;
    }
</style>
<div id="create-post-modal" class="df-gallery-modal">
    <div class="df-gallery-modal-overlay" onclick="closeCreatePostModal()"></div>
    
    <div style="position: relative; z-index: 10; background: #ffffff; width: 100%; max-width: 500px; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
        <div style="display: flex; align-items: center; justify-content: center; padding: 16px; border-bottom: 1px solid #e5e7eb; position: relative;">
            <h2 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #111827;">Tạo bài viết</h2>
            <button onclick="closeCreatePostModal()" style="position: absolute; right: 16px; background: #f3f4f6; border: none; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6b7280; font-size: 1.2rem; transition: background 0.2s;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        
        <form action="{{ route('resident.posts.store') }}" method="POST" enctype="multipart/form-data" style="margin: 0;">
            @csrf
            <div style="padding: 16px;">
                @if($errors->any())
                <div style="padding:12px; background:#fef2f2; color:#991b1b; border:1px solid #fecaca; border-radius:8px; font-size:0.85rem; margin-bottom:16px;">
                    @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
                </div>
                @endif
                
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1px solid #e5e7eb;">
                    @else
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-weight: 600; color: #4b5563;">
                            {{ mb_substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <span style="display: block; font-weight: 600; font-size: 0.95rem; color: #111827;">{{ auth()->user()->name }}</span>
                        <span style="display: block; font-size: 0.75rem; color: #6b7280;">Cư dân</span>
                    </div>
                </div>

                <textarea name="content" id="editor-post-content" rows="4" placeholder="Bạn đang nghĩ gì?" style="width: 100%; border: none; outline: none; resize: none; font-size: 1.05rem; font-family: inherit; color: #111827; padding: 0;">{{ old('content') }}</textarea>
                
                <div style="margin-top: 16px; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px;">
                    <label style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; margin: 0;">
                        <span style="font-weight: 600; font-size: 0.95rem; color: #111827;">Thêm vào bài viết</span>
                        <div style="display: flex; gap: 16px;">
                            <i class="fa-regular fa-image" style="color: #10b981; font-size: 1.4rem;"></i>
                        </div>
                        <input type="file" name="media[]" multiple accept="image/*,video/*" style="display: none;" onchange="previewMedia(this)">
                    </label>
                    <div id="modal-media-previews" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;"></div>
                </div>
            </div>
            
            <div style="padding: 16px; padding-top: 0;">
                <button type="submit" style="width: 100%; background: #00236f; color: #ffffff; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s;">
                    Đăng
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
@vite(['resources/js/pages/resident/posts/create.js'])
<script>
// CREATE POST MODAL LOGIC
const createPostModal = document.getElementById('create-post-modal');
function openCreatePostModal() {
    createPostModal.classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeCreatePostModal() {
    createPostModal.classList.remove('active');
    document.body.style.overflow = '';
}

@if($errors->any())
    // Tự động mở modal nếu có lỗi validation
    document.addEventListener("DOMContentLoaded", function() {
        openCreatePostModal();
    });
@endif

// Preview Media in Modal
function previewMedia(input) {
    const previewContainer = document.getElementById('modal-media-previews');
    previewContainer.innerHTML = ''; // Clear previous
    if (input.files && input.files.length > 0) {
        // Tối đa 5 file
        const maxFiles = 5;
        const filesToProcess = Array.from(input.files).slice(0, maxFiles);
        
        filesToProcess.forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const el = document.createElement('div');
                el.style.width = '60px';
                el.style.height = '60px';
                el.style.borderRadius = '6px';
                el.style.overflow = 'hidden';
                el.style.border = '1px solid #e2e8f0';
                
                if (file.type.startsWith('image/')) {
                    el.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
                } else if (file.type.startsWith('video/')) {
                    el.innerHTML = `<video src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;" muted></video>`;
                }
                
                previewContainer.appendChild(el);
            }
            reader.readAsDataURL(file);
        });
    }
}

// GALLERY MODAL LOGIC
@if($galleryImages->isNotEmpty())
    const galleryImages = [
        @foreach($galleryImages as $img)
            "{{ asset('storage/' . $img->image_path) }}",
        @endforeach
    ];
    let currentGalleryIndex = 0;
    const modal = document.getElementById('gallery-modal');
    const modalImg = document.getElementById('gallery-modal-img');
    const modalCounter = document.getElementById('gallery-current');

    function openGalleryModal(index) {
        currentGalleryIndex = index;
        updateGalleryModal();
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Ngăn cuộn trang
    }

    function closeGalleryModal() {
        modal.classList.remove('active');
        document.body.style.overflow = ''; 
    }

    function updateGalleryModal() {
        modalImg.src = galleryImages[currentGalleryIndex];
        modalCounter.innerText = currentGalleryIndex + 1;
    }

    function nextGalleryImage() {
        currentGalleryIndex = (currentGalleryIndex + 1) % galleryImages.length;
        updateGalleryModal();
    }

    function prevGalleryImage() {
        currentGalleryIndex = (currentGalleryIndex - 1 + galleryImages.length) % galleryImages.length;
        updateGalleryModal();
    }

    // Keyboard support
    document.addEventListener('keydown', function(e) {
        if (!modal.classList.contains('active')) return;
        if (e.key === 'ArrowRight') nextGalleryImage();
        if (e.key === 'ArrowLeft') prevGalleryImage();
        if (e.key === 'Escape') closeGalleryModal();
    });
@endif

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
    document.getElementById('report-target-post-id-index').value = id;
    const modal = document.getElementById('reportPostModalIndex');
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
}

function closeReportModalIndex() {
    const modal = document.getElementById('reportPostModalIndex');
    modal.classList.remove('show');
    setTimeout(() => { modal.style.display = 'none'; document.getElementById('report-post-form-index').reset(); }, 300);
}

function handleOutsideReportModalClick(e) {
    if (e.target.id === 'reportPostModalIndex') closeReportModalIndex();
}

function handleShowCustomReasonIndex(show) {
    const c = document.getElementById('custom-reason-container-index');
    const t = document.getElementById('report-reason-custom-index');
    if(c) c.style.display = show ? 'block' : 'none';
    if(show && t) { t.setAttribute('required', 'required'); t.focus(); }
    else if (t) { t.removeAttribute('required'); t.value = ''; }
    
    const btn = document.getElementById('submit-report-btn-index');
    if(btn) btn.removeAttribute('disabled');
}

function hidePost(e, id) {
    e.stopPropagation();
    document.getElementById('post-menu-' + id).style.display = 'none';
    const card = document.getElementById('post-card-' + id);
    card.style.opacity = '0.5';
    card.innerHTML = '<div style="padding:20px;text-align:center;color:#64748b;">Bài viết đã được ẩn.</div>';
}

// POST DETAIL IFRAME MODAL
function openPostDetailModal(postId) {
    const modal = document.getElementById('postDetailModal');
    const iframe = document.getElementById('postDetailIframe');
    iframe.src = '/resident/posts/' + postId + '?modal=1';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closePostDetailModal() {
    const modal = document.getElementById('postDetailModal');
    const iframe = document.getElementById('postDetailIframe');
    modal.style.display = 'none';
    iframe.src = ''; // Clear iframe
    document.body.style.overflow = 'auto'; // Restore scrolling
}
</script>

<!-- POST DETAIL IFRAME MODAL -->
<div class="df-modal" id="postDetailModal" style="display: none; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 10000; overflow: hidden;">
    <div class="df-modal-overlay" onclick="closePostDetailModal()" style="position: absolute; inset: 0;"></div>
    <div class="df-modal-content" style="position: relative; width: 750px; max-width: 95%; height: 85vh; background: #fff; border-radius: 12px; display: flex; flex-direction: column; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <button class="df-modal-close" onclick="closePostDetailModal()" style="position: absolute; top: 12px; right: 12px; width: 32px; height: 32px; border: none; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 20; color: #333; font-size: 1.2rem; transition: background 0.2s;">
            &times;
        </button>
        <iframe id="postDetailIframe" src="" style="width: 100%; height: 100%; border: none; flex: 1;"></iframe>
    </div>
</div>

<style>
.rep-modal { display: none; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.6); z-index: 10000; opacity: 0; transition: opacity 0.3s ease; }
.rep-modal.show { opacity: 1; }
.rep-modal__content { background: #fff; width: 90%; max-width: 500px; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2); transform: translateY(20px); transition: transform 0.3s ease; }
.rep-modal.show .rep-modal__content { transform: translateY(0); }
.rep-modal__header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.5rem; border-bottom: 1px solid #e5e7eb; }
.rep-modal__title { font-size: 1.1rem; font-weight: 600; margin: 0; }
.rep-modal__close { background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #6b7280; }
.rep-modal__body { padding: 1.5rem; }
.rep-modal__label { display: block; margin-bottom: 1rem; font-weight: 500; }
.rep-option { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; cursor: pointer; font-size: 0.9rem; }
.rep-modal__custom-reason { display: none; margin-top: 1rem; }
.rep-modal__textarea { width: 100%; min-height: 80px; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 8px; resize: vertical; font-family: inherit; }
.rep-modal__footer { padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 1rem; }
.rep-modal__btn-submit { background: #ef4444; color: #fff; border: none; padding: 0.5rem 1.5rem; border-radius: 8px; font-weight: 600; cursor: pointer; }
.rep-modal__btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }
</style>
<div id="reportPostModalIndex" class="rep-modal" onclick="handleOutsideReportModalClick(event)">
    <div class="rep-modal__content" onclick="event.stopPropagation()">
        <div class="rep-modal__header">
            <h3 class="rep-modal__title"><i class="fa-solid fa-triangle-exclamation" style="color: var(--color-error);"></i> Báo cáo bài viết</h3>
            <button type="button" class="rep-modal__close" onclick="closeReportModalIndex()">&times;</button>
        </div>
        <form id="report-post-form-index">
            @csrf
            <input type="hidden" name="post_id" id="report-target-post-id-index" value="">
            <div class="rep-modal__body">
                <span class="rep-modal__label">Tại sao bạn muốn báo cáo bài viết này?</span>
                <label class="rep-option"><input type="radio" name="reason_preset" value="Spam, quảng cáo rác" onclick="handleShowCustomReasonIndex(false)"><span class="rep-option__text">Spam, quảng cáo rác</span></label>
                <label class="rep-option"><input type="radio" name="reason_preset" value="Từ ngữ thô tục, công kích" onclick="handleShowCustomReasonIndex(false)"><span class="rep-option__text">Từ ngữ thô tục, công kích</span></label>
                <label class="rep-option"><input type="radio" name="reason_preset" value="Lừa đảo, giả mạo" onclick="handleShowCustomReasonIndex(false)"><span class="rep-option__text">Lừa đảo, giả mạo thông tin</span></label>
                <label class="rep-option"><input type="radio" name="reason_preset" value="Nội dung phản cảm, thù địch" onclick="handleShowCustomReasonIndex(false)"><span class="rep-option__text">Nội dung phản cảm, thù địch</span></label>
                <label class="rep-option"><input type="radio" name="reason_preset" value="other" onclick="handleShowCustomReasonIndex(true)"><span class="rep-option__text">Lý do khác...</span></label>
                <div class="rep-modal__custom-reason" id="custom-reason-container-index">
                    <textarea name="reason_custom" id="report-reason-custom-index" class="rep-modal__textarea" placeholder="Nhập lý do cụ thể..."></textarea>
                </div>
            </div>
            <div class="rep-modal__footer">
                <button type="button" class="pc-btn pc-btn--secondary" onclick="closeReportModalIndex()" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #d1d5db; background: #fff; cursor: pointer;">Hủy bỏ</button>
                <button type="submit" class="rep-modal__btn-submit" id="submit-report-btn-index" disabled>Gửi báo cáo</button>
            </div>
        </form>
    </div>
</div>
<script>
document.getElementById('report-post-form-index')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-report-btn-index');
    btn.innerHTML = 'Đang gửi...'; btn.disabled = true;
    const formData = new FormData(this);
    fetch('/resident/posts/report', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        closeReportModalIndex();
        btn.innerHTML = 'Gửi báo cáo';
        if(data.success) toastr.success(data.message);
        else toastr.error(data.message || 'Lỗi');
    }).catch(e => {
        closeReportModalIndex();
        btn.innerHTML = 'Gửi báo cáo';
        toastr.error('Lỗi kết nối');
    });
});
</script>
<style>
    .df-post-images {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        border-radius: 8px;
        overflow: hidden;
    }
    .df-post-images > * {
        flex: 1 1 calc(50% - 4px);
        min-width: 30%;
        max-height: 300px;
        object-fit: cover;
        cursor: pointer;
    }
    .df-post-images.layout-count-1 > * {
        flex: 1 1 100%;
        max-height: 500px;
    }
    .df-post-images.layout-count-2 > * {
        flex: 1 1 calc(50% - 4px);
        height: 300px;
    }
    .df-post-images.layout-count-3 > * {
        flex: 1 1 calc(33.333% - 4px);
        height: 250px;
    }
    .df-post-images.layout-count-4 > * {
        flex: 1 1 calc(50% - 4px);
        height: 200px;
    }
    .df-post-images.layout-count-5 > * {
        flex: 1 1 calc(33.333% - 4px);
        height: 200px;
    }
    .df-post-images.layout-count-5 > *:nth-child(4),
    .df-post-images.layout-count-5 > *:nth-child(5) {
        flex: 1 1 calc(50% - 4px);
    }
</style>

<script>
    function openSingleImageModal(src) {
        document.getElementById('single-modal-img').src = src;
        document.getElementById('single-image-modal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSingleImageModal() {
        document.getElementById('single-image-modal').classList.remove('active');
        document.body.style.overflow = '';
    }
</script>

<div id="single-image-modal" class="df-gallery-modal">
    <div class="df-gallery-modal-overlay" onclick="closeSingleImageModal()"></div>
    <button class="df-gallery-modal-close" onclick="closeSingleImageModal()">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <div class="df-gallery-modal-content">
        <img id="single-modal-img" src="" alt="" style="max-height: 90vh; max-width: 90vw; object-fit: contain;">
    </div>
</div>

@endpush
@endsection
