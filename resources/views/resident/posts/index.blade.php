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
                <div class="df-create-post-input" onclick="openCreatePostModal()">
                    {{ explode(' ', auth()->user()->name)[count(explode(' ', auth()->user()->name))-1] ?? auth()->user()->name }}, bạn đang nghĩ gì?
                </div>
            </div>
            <div class="df-create-post-bottom">
                <button class="df-create-btn" onclick="openCreatePostModal()">
                    <i class="fa-regular fa-image" style="color: #10b981; font-size: 1.1rem;"></i> Hình ảnh/Video
                </button>
                <button class="df-post-submit-btn" onclick="openCreatePostModal()">Đăng</button>
            </div>
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
                @php
                    $catRaw = strtolower($ann->category ?? '');
                    $catMap = [
                        'general' => 'Chung',
                        'event' => 'Sự kiện',
                        'maintenance' => 'Bảo trì',
                        'warning' => 'Khẩn cấp',
                        'community' => 'Cộng đồng',
                    ];
                    $catText = $catMap[$catRaw] ?? 'THÔNG BÁO';
                @endphp
                <div class="df-event-item">
                    <span class="df-event-meta">{{ mb_strtoupper($catText) }}</span>
                    <a href="{{ route('resident.announcements.show', $ann->id) }}" style="text-decoration:none; display:block;">
                        <span class="df-event-title" style="color:#111827; display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden; font-weight: 500; font-size: 0.95rem; line-height: 1.5;">{{ $ann->title }}</span>
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
                <button type="submit" style="width: 100%; background: #000000; color: #ffffff; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background 0.2s;">
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
