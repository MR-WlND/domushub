@extends('layouts.resident.master')

@section('title', 'Bảng tin Cư dân – DomusHub')

@push('styles')
    @vite(['resources/css/resident/posts.css'])
@endpush

@section('content')
<div class="pc" style="max-width: 800px;">

    {{-- HEADER --}}
    <div class="pc__header">
        <div>
            <p class="pc__eyebrow">Cộng đồng</p>
            <h1 class="pc__title">Bảng tin cư dân</h1>
        </div>
        
        {{-- THANH TÌM KIẾM BÀI VIẾT --}}
        <form action="{{ route('resident.posts.index') }}" method="GET" class="pc-search-form">
            @if(request()->filled('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            <div class="pc-search-wrap">
                <i class="fa-solid fa-magnifying-glass pc-search-icon"></i>
                <input type="text" name="search" class="pc-search-input" placeholder="Tìm kiếm bài viết..." value="{{ request('search') }}">
                @if(request()->filled('search'))
                    <a href="{{ route('resident.posts.index', request()->only('type')) }}" class="pc-search-clear" title="Xóa tìm kiếm">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- THÔNG BÁO THÀNH CÔNG/LỖI --}}
    @if(session('success'))
        <div class="pc-alert pc-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="pc-alert pc-alert--error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                <ul style="margin: 0; padding-left: 1rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="pc-layout">
        
        {{-- CỘT CHÍNH (FORM ĐĂNG BÀI & DANH SÁCH BÀI VIẾT) --}}
        <div class="pc-feed">
            
            {{-- QUY CHẾ BẢNG TIN (Collapsible Banner) --}}
            <div class="pc-alert pc-rules-banner" style="background-color: #f8fafc; border: 1px solid var(--color-outline-soft); color: var(--color-text-secondary); margin-bottom: 1.5rem; display: flex; flex-direction: column; align-items: stretch; gap: 0.75rem; border-radius: var(--radius-lg); padding: 1rem 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <span style="font-weight: 700; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; color: var(--color-text);">
                        <i class="fa-solid fa-handshake-angle" style="color: var(--color-primary);"></i> Quy chế Bảng tin cư dân
                    </span>
                    <button type="button" onclick="toggleRulesBanner()" id="btn-toggle-rules" style="background: transparent; border: none; color: var(--color-primary); font-weight: 700; font-size: 0.825rem; cursor: pointer; outline: none; display: flex; align-items: center; gap: 0.25rem;">
                        <span>Xem chi tiết</span> <i class="fa-solid fa-chevron-down"></i>
                    </button>
                </div>
                <div id="rules-banner-content" style="display: none; font-size: 0.85rem; line-height: 1.6; border-top: 1px dashed var(--color-outline-soft); padding-top: 0.75rem; margin-top: 0.25rem;">
                    <ul style="margin: 0; padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.5rem; color: var(--color-text-secondary);">
                        <li><strong>Tôn trọng:</strong> Giữ thái độ hòa nhã, lịch sự khi thảo luận và bình luận bài viết.</li>
                        <li><strong>Chính xác:</strong> Thông tin thanh lý hoặc chia sẻ phải rõ ràng, trung thực.</li>
                        <li><strong>Không spam:</strong> Không đăng tin quảng cáo, rác hoặc thông tin vi phạm pháp luật.</li>
                        <li><strong>Bảo mật:</strong> Không chia sẻ thông tin cá nhân của người khác khi chưa được phép.</li>
                    </ul>
                </div>
            </div>

            {{-- BỘ SOẠN THẢO BÀI VIẾT MỚI --}}
            <div class="pc-composer">
                <form action="{{ route('resident.posts.store') }}" method="POST" enctype="multipart/form-data" id="post-form">
                    @csrf
                    <div class="pc-composer__header">
                        <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=00236f&color=fff' }}" 
                             alt="Avatar" class="pc-composer__avatar">
                        <div class="pc-composer__author-info">
                            <h4 class="pc-composer__author-name">{{ auth()->user()->name }}</h4>
                            <span class="pc-composer__author-apartment">Cư dân {{ auth()->user()->apartment->apartment_number ?? '' }}</span>
                        </div>
                    </div>

                    <div class="pc-composer__inputs">
                        <input type="text" name="title" class="pc-composer__title-input" placeholder="Nhập tiêu đề bài viết... (không bắt buộc)" value="{{ old('title') }}">
                        <textarea name="content" class="pc-composer__body-input" placeholder="Bạn đang muốn chia sẻ điều gì với cư dân hôm nay..." rows="4" required>{{ old('content') }}</textarea>
                    </div>
                    
                    {{-- Khung Preview nhiều ảnh đính kèm --}}
                    <div class="pc-composer__preview-list" id="image-preview-list"></div>

                    {{-- Ô nhập giá thanh lý ẩn mặc định --}}
                    <div class="pc-composer__price-container" id="price-input-container" style="display: {{ old('price') ? 'block' : 'none' }}; margin-top: 0.75rem;">
                        <div class="pc-composer__price-wrap">
                            <i class="fa-solid fa-tags pc-composer__price-icon"></i>
                            <input type="tel" name="price" id="post-price" class="pc-composer__price-input" placeholder="Nhập giá thanh lý (ví dụ: 150.000)..." value="{{ old('price') }}">
                            <span style="font-weight: 700; color: var(--color-secondary); font-size: 0.9rem; margin-right: 0.5rem;">đ</span>
                        </div>
                        <div id="post-price-preview" style="font-size: 0.825rem; color: var(--color-success); font-weight: 600; margin-top: 0.35rem; margin-left: 1.8rem; display: none;"></div>
                    </div>

                    {{-- Thanh công cụ Toolbar --}}
                    <div class="pc-composer__toolbar">
                        <div class="pc-composer__toolbar-left">
                            {{-- Nút chọn ảnh --}}
                            <label class="pc-composer__file-label">
                                <i class="fa-regular fa-image" style="color: #2563eb;"></i> Đính kèm ảnh
                                <input type="file" name="images[]" id="post-image" class="pc-composer__file-input" accept="image/*" multiple onchange="previewImages(event)">
                            </label>

                            {{-- Nút bật nhập giá thanh lý --}}
                            <button type="button" class="pc-composer__toggle-price-btn {{ old('price') ? 'active' : '' }}" id="toggle-price-btn" onclick="togglePriceField()">
                                <i class="fa-solid fa-tags"></i> Thanh lý đồ
                            </button>
                        </div>

                        <div class="pc-composer__toolbar-right">
                            <button type="submit" class="pc-btn pc-btn--primary-filled">
                                <i class="fa-regular fa-paper-plane"></i> Đăng bài
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ĐIỀU HƯỚNG LỌC BÀI VIẾT (TABS) --}}
            <div class="pc-filter-bar">
                <a href="{{ route('resident.posts.index') }}" class="pc-filter-tab {{ !request()->filled('type') ? 'pc-filter-tab--active' : '' }}">
                    Tất cả
                </a>
                <a href="{{ route('resident.posts.index', ['type' => 'general']) }}" class="pc-filter-tab {{ request()->get('type') === 'general' ? 'pc-filter-tab--active' : '' }}">
                    Chia sẻ
                </a>
                <a href="{{ route('resident.posts.index', ['type' => 'marketplace']) }}" class="pc-filter-tab {{ request()->get('type') === 'marketplace' ? 'pc-filter-tab--active' : '' }}">
                    Thanh lý
                </a>
            </div>

            {{-- DANH SÁCH BÀI VIẾT (FEED) --}}
            <div id="posts-list-container">
            @if($posts->isEmpty())
                <div class="pc-card" id="empty-feed-placeholder" style="text-align: center; padding: 3rem 1.5rem;">
                    <i class="fa-regular fa-comments" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 1rem;"></i>
                    <h3 style="margin: 0 0 0.5rem 0; color: var(--color-text);">Chưa có bài viết nào</h3>
                    <p style="margin: 0; color: var(--color-text-secondary); font-size: 0.9rem;">Hãy là người đầu tiên chia sẻ thông tin gì đó trên bảng tin cư dân!</p>
                </div>
            @else
                @foreach($posts as $post)
                    <div class="pc-card" data-post-id="{{ $post->id }}">
                        
                        {{-- Thông tin tác giả --}}
                        <div class="pc-card__author">
                            <div class="pc-card__author-info">
                                <img src="{{ $post->user->avatar ? asset('storage/' . $post->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=00236f&color=fff' }}" 
                                     alt="Avatar" class="pc-card__avatar">
                                <div>
                                    <h4 class="pc-card__name">{{ $post->user->name }}</h4>
                                    <span class="pc-card__apartment">
                                        @if($post->user->apartment)
                                            Căn hộ: {{ $post->user->apartment->apartment_number }}
                                        @else
                                            Ban Quản Trị
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <span class="pc-card__date" title="{{ $post->created_at }}">
                                {{ $post->created_at->diffForHumans() }}
                            </span>
                        </div>

                        {{-- Phân loại bài viết --}}
                        <div class="pc-card__tags">
                            @if($post->price !== null)
                                <span class="pc-badge pc-badge--marketplace">Thanh lý</span>
                                <span class="pc-card__price">{{ number_format($post->price, 0, ',', '.') }}đ</span>
                            @else
                                <span class="pc-badge pc-badge--general">Chia sẻ</span>
                            @endif
                        </div>

                        {{-- Tiêu đề & Nội dung --}}
                        <h3 class="pc-card__title">
                            <a href="{{ route('resident.posts.show', $post->id) }}" class="pc-card__title-link">
                                {{ $post->title }}
                            </a>
                        </h3>
                        
                        @if(mb_strlen($post->content) > 250)
                            <div class="pc-card__content-wrapper">
                                <p class="pc-card__content" id="content-short-{{ $post->id }}">{!! nl2br(e(Str::limit($post->content, 250, '...'))) !!}</p>
                                <p class="pc-card__content" id="content-full-{{ $post->id }}" style="display: none;">{!! nl2br(e($post->content)) !!}</p>
                                <button type="button" class="pc-card__toggle-content-btn" id="btn-toggle-{{ $post->id }}" onclick="toggleContent({{ $post->id }})">Xem thêm</button>
                            </div>
                        @else
                            <p class="pc-card__content">{!! nl2br(e($post->content)) !!}</p>
                        @endif

                        {{-- Ảnh đính kèm (Lưới ảnh CSS động kiểu Facebook) --}}
                        @if($post->images->isNotEmpty())
                            @php
                                $imgCount = $post->images->count();
                            @endphp
                            <div class="pc-card__image-grid pc-card__image-grid--{{ min($imgCount, 5) }}">
                                @foreach($post->images->take(4) as $index => $img)
                                    <div class="pc-card__grid-item" onclick="openLightbox('{{ asset('storage/' . $img->image_path) }}')">
                                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="Ảnh đính kèm" class="pc-card__grid-img">
                                        @if($index === 3 && $imgCount > 4)
                                            <div class="pc-card__grid-overlay">
                                                +{{ $imgCount - 4 }} ảnh
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Chân bài viết --}}
                        <div class="pc-card__footer">
                            <div class="pc-card__actions" style="gap: 1.25rem;">
                                {{-- Nút Thích --}}
                                <div class="pc-like-container">
                                    <div class="pc-reactions-popup">
                                        <span class="pc-reaction-option" data-label="Thích" onclick="toggleLike(event, {{ $post->id }}, 'post', 'like')">👍</span>
                                        <span class="pc-reaction-option" data-label="Yêu thích" onclick="toggleLike(event, {{ $post->id }}, 'post', 'love')">❤️</span>
                                        <span class="pc-reaction-option" data-label="Haha" onclick="toggleLike(event, {{ $post->id }}, 'post', 'haha')">😆</span>
                                        <span class="pc-reaction-option" data-label="Wow" onclick="toggleLike(event, {{ $post->id }}, 'post', 'wow')">😮</span>
                                        <span class="pc-reaction-option" data-label="Buồn" onclick="toggleLike(event, {{ $post->id }}, 'post', 'sad')">😢</span>
                                        <span class="pc-reaction-option" data-label="Phẫn nộ" onclick="toggleLike(event, {{ $post->id }}, 'post', 'angry')">😡</span>
                                    </div>

                                    @php
                                        $userLike = $post->likedByCurrentUser->first();
                                        $activeReaction = $userLike ? $userLike->type : null;
                                        
                                        $likeBtnClass = 'pc-like-btn';
                                        $likeIconHtml = '<i class="fa-regular fa-thumbs-up"></i>';
                                        $likeText = 'Thích';
                                        
                                        if ($activeReaction) {
                                            $likeBtnClass .= ' pc-like-btn--active reaction-active-' . $activeReaction;
                                            $likeIconHtml = match($activeReaction) {
                                                'love' => '❤️',
                                                'haha' => '😆',
                                                'wow' => '😮',
                                                'sad' => '😢',
                                                'angry' => '😡',
                                                default => '👍',
                                            };
                                            $likeText = match($activeReaction) {
                                                'love' => 'Yêu thích',
                                                'haha' => 'Haha',
                                                'wow' => 'Wow',
                                                'sad' => 'Buồn',
                                                'angry' => 'Phẫn nộ',
                                                default => 'Thích',
                                            };
                                        }
                                    @endphp

                                    <button type="button" class="pc-card__action-btn {{ $likeBtnClass }}" onclick="toggleLike(event, {{ $post->id }}, 'post')" style="border: none; background: transparent; cursor: pointer; font-family: inherit; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; outline: none;">
                                        <span class="reaction-icon-span">{!! $likeIconHtml !!}</span>
                                        <span class="reaction-text-span">{{ $likeText }}</span> (<span class="like-count">{{ $post->likes_count }}</span>)
                                    </button>
                                </div>

                                <a href="{{ route('resident.posts.show', $post->id) }}" class="pc-card__action-btn">
                                    <i class="fa-regular fa-comment"></i> Bình luận ({{ $post->comments->count() }})
                                </a>
                                <button type="button" class="pc-card__action-btn" onclick="openShareModal({{ $post->id }}, '{{ route('resident.posts.show', $post->id) }}')">
                                    <i class="fa-regular fa-share-from-square"></i> Chia sẻ
                                </button>
                                @if($post->user_id !== auth()->id())
                                    <button type="button" class="pc-card__action-btn" style="color: var(--color-error); border: none; background: transparent; cursor: pointer; font-family: inherit; font-size: 0.875rem; font-weight: 600; padding: 0; outline: none;" onclick="openReportModal({{ $post->id }})">
                                        <i class="fa-regular fa-flag"></i> Báo cáo
                                    </button>
                                @endif
                            </div>

                            <div style="display: flex; gap: 0.5rem; align-items: center;">
                                {{-- Nút sửa bài viết (chỉ dành cho tác giả) --}}
                                @if($post->user_id === auth()->id())
                                    <button type="button" class="pc-card__edit-btn" onclick="openEditPostModal({{ $post->id }}, '{{ addslashes($post->title) }}', '{{ addslashes($post->content) }}', '{{ $post->price }}')">
                                        <i class="fa-regular fa-pen-to-square"></i> Sửa bài
                                    </button>
                                @endif

                                {{-- Nút xóa bài viết (Bảo mật IDOR đã check ở Backend) --}}
                                @if($post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
                                    <form action="{{ route('resident.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài đăng này không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="pc-card__delete-btn">
                                            <i class="fa-regular fa-trash-can"></i> Xóa
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Phân trang --}}
                <div style="margin-top: 1rem;">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>

    </div>

</div>

{{-- Lightbox Zoom Ảnh --}}
<div id="pcLightbox" class="pc-modal" onclick="closeLightbox()">
    <span class="pc-modal__close">&times;</span>
    <img class="pc-modal__content" id="pcLightboxTarget">
</div>

{{-- Modal Báo cáo Bài viết --}}
<div id="reportPostModal" class="rep-modal" onclick="handleOutsideModalClick(event)">
    <div class="rep-modal__content">
        <div class="rep-modal__header">
            <h3 class="rep-modal__title">
                <i class="fa-solid fa-triangle-exclamation" style="color: var(--color-error);"></i> Báo cáo bài viết
            </h3>
            <button type="button" class="rep-modal__close" onclick="closeReportModal()">&times;</button>
        </div>
        <form id="report-post-form">
            @csrf
            <input type="hidden" name="post_id" id="report-target-post-id" value="">
            <div class="rep-modal__body">
                <span class="rep-modal__label">Tại sao bạn muốn báo cáo bài viết này?</span>
                
                <label class="rep-option">
                    <input type="radio" name="reason_preset" value="Spam, quảng cáo rác" onclick="toggleCustomReason(false)">
                    <span class="rep-option__text">Spam, quảng cáo rác</span>
                </label>
                
                <label class="rep-option">
                    <input type="radio" name="reason_preset" value="Từ ngữ thô tục, công kích" onclick="toggleCustomReason(false)">
                    <span class="rep-option__text">Từ ngữ thô tục, công kích</span>
                </label>
                
                <label class="rep-option">
                    <input type="radio" name="reason_preset" value="Lừa đảo, giả mạo" onclick="toggleCustomReason(false)">
                    <span class="rep-option__text">Lừa đảo, giả mạo thông tin</span>
                </label>
                
                <label class="rep-option">
                    <input type="radio" name="reason_preset" value="Nội dung phản cảm, thù địch" onclick="toggleCustomReason(false)">
                    <span class="rep-option__text">Nội dung phản cảm, thù địch</span>
                </label>
                
                <label class="rep-option">
                    <input type="radio" name="reason_preset" value="other" onclick="toggleCustomReason(true)">
                    <span class="rep-option__text">Lý do khác...</span>
                </label>

                <div class="rep-modal__custom-reason" id="custom-reason-container">
                    <textarea name="reason_custom" id="report-reason-custom" class="rep-modal__textarea" placeholder="Nhập lý do cụ thể của bạn ở đây..."></textarea>
                </div>
            </div>
            <div class="rep-modal__footer">
                <button type="button" class="pc-btn pc-btn--secondary" onclick="closeReportModal()">Hủy bỏ</button>
                <button type="submit" class="rep-modal__btn-submit" id="submit-report-btn" disabled>Gửi báo cáo</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Chỉnh sửa Bài viết --}}
<div id="editPostModal" class="rep-modal" onclick="handleEditModalClick(event)">
    <div class="rep-modal__content" style="max-width: 580px;">
        <div class="rep-modal__header">
            <h3 class="rep-modal__title">
                <i class="fa-regular fa-pen-to-square" style="color: var(--color-primary);"></i> Chỉnh sửa bài viết
            </h3>
            <button type="button" class="rep-modal__close" onclick="closeEditPostModal()">&times;</button>
        </div>
        <form id="edit-post-form" method="POST">
            @csrf
            @method('PUT')
            <div class="rep-modal__body">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label class="rep-modal__label">Tiêu đề bài viết (không bắt buộc)</label>
                        <input type="text" name="title" id="edit-post-title" class="pc-composer__title-input" placeholder="Nhập tiêu đề bài viết...">
                    </div>
                    <div>
                        <label class="rep-modal__label">Nội dung bài viết</label>
                        <textarea name="content" id="edit-post-content" class="pc-composer__body-input" rows="5" required placeholder="Bạn đang muốn chia sẻ điều gì..."></textarea>
                    </div>
                    
                    {{-- Ô nhập giá thanh lý --}}
                    <div id="edit-price-input-container">
                        <label class="rep-modal__label">Giá thanh lý (để trống nếu là bài chia sẻ)</label>
                        <div class="pc-composer__price-wrap" style="width: 100%; box-sizing: border-box; display: flex;">
                            <i class="fa-solid fa-tags pc-composer__price-icon"></i>
                            <input type="tel" name="price" id="edit-post-price" class="pc-composer__price-input" placeholder="Nhập giá thanh lý (ví dụ: 150.000)..." style="flex: 1;">
                            <span style="font-weight: 700; color: var(--color-secondary); font-size: 0.9rem; margin-right: 0.5rem;">đ</span>
                        </div>
                        <div id="edit-post-price-preview" style="font-size: 0.825rem; color: var(--color-success); font-weight: 600; margin-top: 0.35rem; margin-left: 1.8rem; display: none;"></div>
                    </div>
                </div>
            </div>
            <div class="rep-modal__footer">
                <button type="button" class="pc-btn pc-btn--secondary" onclick="closeEditPostModal()">Hủy bỏ</button>
                <button type="submit" class="pc-btn">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Chia sẻ Bài viết đã được chuyển sang cơ chế Web Share API / Clipboard Fallback trực tiếp --}}

{{-- Toast Container hiển thị thông báo góc màn hình --}}
<div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 10001; display: flex; flex-direction: column; gap: 0.5rem; max-width: 350px;"></div>

<script>
    function openShareModal(postId, postUrl, postTitle = '') {
        if (!postTitle) {
            // Lấy tiêu đề bài viết từ DOM
            const postCard = document.querySelector(`.pc-card[data-post-id="${postId}"]`);
            if (postCard) {
                const titleEl = postCard.querySelector('.pc-card__title-link') || postCard.querySelector('.pc-card__title');
                if (titleEl) {
                    postTitle = titleEl.innerText.trim();
                }
            }
            if (!postTitle) {
                const detailTitleEl = document.querySelector('.pc-detail__title');
                if (detailTitleEl) {
                    postTitle = detailTitleEl.innerText.trim();
                }
            }
        }
        if (!postTitle) {
            postTitle = 'DomusHub';
        }

        // Web Share API cho thiết bị di động
        if (navigator.share) {
            navigator.share({
                title: postTitle,
                text: postTitle,
                url: postUrl
            }).catch(err => {
                console.log('Chia sẻ bị hủy hoặc gặp lỗi:', err);
            });
        } else {
            // Clipboard Fallback cho máy tính
            navigator.clipboard.writeText(postUrl).then(() => {
                showToast('Đã sao chép liên kết bài viết vào bộ nhớ tạm!');
            }).catch(err => {
                console.error('Failed to copy:', err);
                // Fallback cực hạn dùng textarea tạm
                const tempInput = document.createElement('textarea');
                tempInput.value = postUrl;
                document.body.appendChild(tempInput);
                tempInput.select();
                try {
                    document.execCommand('copy');
                    showToast('Đã sao chép liên kết bài viết vào bộ nhớ tạm!');
                } catch (e) {
                    showToast('Không thể sao chép liên kết.', 'error');
                }
                document.body.removeChild(tempInput);
            });
        }
    }

    // Hàm hiển thị thông báo Toast mượt mà
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.style.cssText = `
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-md);
            box-shadow: var(--color-shadow-lg);
            color: white;
            font-size: 0.875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: ${type === 'success' ? '#10b981' : '#ef4444'};
        `;
        
        const icon = type === 'success' 
            ? '<i class="fa-solid fa-circle-check"></i>' 
            : '<i class="fa-solid fa-triangle-exclamation"></i>';
            
        toast.innerHTML = `${icon} <span>${message}</span>`;
        container.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        }, 10);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    // Modal Báo cáo
    function openReportModal(postId) {
        const modal = document.getElementById('reportPostModal');
        document.getElementById('report-target-post-id').value = postId;
        
        // Reset form
        document.getElementById('report-post-form').reset();
        toggleCustomReason(false);
        
        // Disable nút submit lúc mới mở
        document.getElementById('submit-report-btn').setAttribute('disabled', 'disabled');
        
        modal.style.display = 'flex';
        // Kích hoạt animation transition
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    }

    function closeReportModal() {
        const modal = document.getElementById('reportPostModal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    function handleOutsideModalClick(event) {
        if (event.target === document.getElementById('reportPostModal')) {
            closeReportModal();
        }
    }

    function toggleCustomReason(show) {
        const container = document.getElementById('custom-reason-container');
        const textarea = document.getElementById('report-reason-custom');
        if (show) {
            container.style.display = 'block';
            textarea.setAttribute('required', 'required');
            textarea.focus();
        } else {
            container.style.display = 'none';
            textarea.removeAttribute('required');
            textarea.value = '';
        }
        checkReportFormStatus();
    }

    // Kiểm tra trạng thái form báo cáo để enable/disable nút gửi
    function checkReportFormStatus() {
        const submitBtn = document.getElementById('submit-report-btn');
        const checkedRadio = document.querySelector('input[name="reason_preset"]:checked');
        const customText = document.getElementById('report-reason-custom').value.trim();
        
        if (!checkedRadio) {
            submitBtn.setAttribute('disabled', 'disabled');
            return;
        }
        
        if (checkedRadio.value === 'other') {
            if (customText.length > 0) {
                submitBtn.removeAttribute('disabled');
            } else {
                submitBtn.setAttribute('disabled', 'disabled');
            }
        } else {
            submitBtn.removeAttribute('disabled');
        }
    }

    // Gắn sự kiện lắng nghe để toggle trạng thái nút submit
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="reason_preset"]').forEach(radio => {
            radio.addEventListener('change', checkReportFormStatus);
        });
        
        const customReasonInput = document.getElementById('report-reason-custom');
        if (customReasonInput) {
            customReasonInput.addEventListener('input', checkReportFormStatus);
        }
    });

    // Submit báo cáo qua AJAX Fetch
    const reportForm = document.getElementById('report-post-form');
    if (reportForm) {
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const postId = document.getElementById('report-target-post-id').value;
            const presetReason = document.querySelector('input[name="reason_preset"]:checked');
            if (!presetReason) return;
            
            let reason = presetReason.value;
            if (presetReason.value === 'other') {
                reason = document.getElementById('report-reason-custom').value.trim();
            }
            
            if (!reason) {
                showToast('Vui lòng chọn hoặc nhập lý do báo cáo.', 'error');
                return;
            }
            
            // Disable nút gửi để chống spam click
            const submitBtn = document.getElementById('submit-report-btn');
            submitBtn.setAttribute('disabled', 'disabled');
            submitBtn.innerText = 'Đang gửi...';

            fetch(`/resident/posts/${postId}/report`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                closeReportModal();
                showToast(data.message || 'Báo cáo bài viết thành công!', 'success');
                
                // Kích hoạt hiệu ứng biến mất mượt mà của bài viết khỏi feed
                const postCard = document.querySelector(`.pc-card[data-post-id="${postId}"]`);
                if (postCard) {
                    postCard.classList.add('fade-out-slide-up');
                    setTimeout(() => {
                        postCard.remove();
                        // Nếu sau khi remove mà không còn bài đăng nào, hiển thị card trống
                        const remainingFeed = document.querySelector('.pc-feed');
                        const remainingCards = remainingFeed.querySelectorAll('.pc-card');
                        if (remainingCards.length === 0) {
                            // Reload trang để render đúng giao diện trống và phân trang
                            window.location.reload();
                        }
                    }, 600);
                }
            })
            .catch(error => {
                showToast(error.message || 'Có lỗi xảy ra khi gửi báo cáo. Vui lòng thử lại!', 'error');
            })
            .finally(() => {
                submitBtn.removeAttribute('disabled');
                submitBtn.innerText = 'Gửi báo cáo';
            });
        });
    }

    // Hàm ẩn/hiện nội dung bài viết dài
    function toggleContent(postId) {
        const shortContent = document.getElementById('content-short-' + postId);
        const fullContent = document.getElementById('content-full-' + postId);
        const toggleBtn = document.getElementById('btn-toggle-' + postId);

        if (fullContent.style.display === 'none') {
            fullContent.style.display = 'block';
            shortContent.style.display = 'none';
            toggleBtn.innerText = 'Thu gọn';
        } else {
            fullContent.style.display = 'none';
            shortContent.style.display = 'block';
            toggleBtn.innerText = 'Xem thêm';
        }
    }

    // Biến toàn cục lưu trữ DataTransfer để quản lý danh sách file được chọn
    let postFilesTransfer = new DataTransfer();

    // Preview danh sách hình ảnh khi chọn
    function previewImages(event) {
        const input = event.target;
        
        if (input.files) {
            // Duyệt và thêm các file mới vào DataTransfer (giới hạn tối đa 5 ảnh)
            for (let i = 0; i < input.files.length; i++) {
                if (postFilesTransfer.files.length >= 5) {
                    showToast('Bạn chỉ được đính kèm tối đa 5 hình ảnh.', 'error');
                    break;
                }
                postFilesTransfer.items.add(input.files[i]);
            }
            
            // Cập nhật lại danh sách file cho input
            input.files = postFilesTransfer.files;
            
            // Render giao diện preview
            renderPreviews();
        }
    }

    // Render danh sách Thumbnail preview
    function renderPreviews() {
        const previewList = document.getElementById('image-preview-list');
        previewList.innerHTML = '';
        
        if (postFilesTransfer.files.length === 0) {
            previewList.style.display = 'none';
            return;
        }
        
        previewList.style.display = 'flex';
        
        Array.from(postFilesTransfer.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const item = document.createElement('div');
                item.className = 'pc-composer__preview-item';
                item.innerHTML = `
                    <img src="${e.target.result}" alt="Preview thumbnail">
                    <button type="button" class="pc-composer__preview-item-remove" onclick="removePreviewImage(${index})">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                `;
                previewList.appendChild(item);
            };
            reader.readAsDataURL(file);
        });
    }

    // Gỡ bỏ 1 ảnh khỏi danh sách chọn trước khi đăng
    function removePreviewImage(index) {
        const input = document.getElementById('post-image');
        
        // Tạo DataTransfer mới bỏ qua file ở index cần xóa
        const newTransfer = new DataTransfer();
        const files = postFilesTransfer.files;
        for (let i = 0; i < files.length; i++) {
            if (i !== index) {
                newTransfer.items.add(files[i]);
            }
        }
        
        postFilesTransfer = newTransfer;
        input.files = postFilesTransfer.files;
        renderPreviews();
    }

    // Ẩn/Hiện ô nhập giá thanh lý
    function togglePriceField() {
        const priceContainer = document.getElementById('price-input-container');
        const toggleBtn = document.getElementById('toggle-price-btn');
        const priceInput = document.getElementById('post-price');

        if (priceContainer.style.display === 'none') {
            priceContainer.style.display = 'block';
            toggleBtn.classList.add('active');
            priceInput.focus();
        } else {
            priceContainer.style.display = 'none';
            toggleBtn.classList.remove('active');
            priceInput.value = ''; // Xóa giá trị khi tắt
        }
    }

    // Hàm khởi tạo định dạng tiền tệ chuyên nghiệp: gõ số thô để tránh xung đột Unikey, hiển thị preview thời gian thực và format đẹp mắt khi blur
    function initPriceFormatter(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;

        function updatePreview() {
            let digits = input.value.replace(/\D/g, '');
            if (digits) {
                let formatted = parseInt(digits, 10).toLocaleString('vi-VN');
                preview.innerText = `Định dạng: ${formatted}đ`;
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        }

        input.addEventListener('input', function() {
            // Chỉ giữ lại chữ số thô khi đang gõ để tránh xung đột Unikey
            let cursor = this.selectionStart;
            let originalLength = this.value.length;
            
            let clean = this.value.replace(/\D/g, '');
            this.value = clean;
            
            // Khôi phục con trỏ
            let newCursor = cursor - (originalLength - clean.length);
            if (newCursor < 0) newCursor = 0;
            this.setSelectionRange(newCursor, newCursor);
            
            updatePreview();
        });

        input.addEventListener('focus', function() {
            // Khi focus, chuyển về số thô để sửa đổi không bị lỗi
            let digits = this.value.replace(/\D/g, '');
            this.value = digits;
            updatePreview();
        });

        input.addEventListener('blur', function() {
            // Khi blur, định dạng đẹp mắt trong ô nhập
            let digits = this.value.replace(/\D/g, '');
            if (digits) {
                this.value = parseInt(digits, 10).toLocaleString('vi-VN');
            } else {
                this.value = '';
            }
            preview.style.display = 'none'; // Ẩn xem trước khi đã blur
        });
    }

    // Khởi tạo định dạng tiền cho ô nhập đăng bài viết
    initPriceFormatter('post-price', 'post-price-preview');

    // Xử lý trước khi submit form để loại bỏ định dạng hiển thị của giá trước khi gửi lên controller
    const postForm = document.getElementById('post-form');
    if (postForm) {
        postForm.addEventListener('submit', function() {
            if (priceInput && priceInput.value) {
                // Trả về số thô
                priceInput.value = priceInput.value.replace(/[^0-9]/g, '');
            }
        });
    }

    // Lightbox zoom hình ảnh
    function openLightbox(src) {
        const lightbox = document.getElementById('pcLightbox');
        const img = document.getElementById('pcLightboxTarget');
        lightbox.style.display = "flex";
        img.src = src;
    }

    // Đóng lightbox zoom ảnh
    function closeLightbox() {
        document.getElementById('pcLightbox').style.display = "none";
    }

    // Toggle collapsible rules banner
    function toggleRulesBanner() {
        const content = document.getElementById('rules-banner-content');
        const btn = document.getElementById('btn-toggle-rules');
        if (!content || !btn) return;
        
        const label = btn.querySelector('span');
        const icon = btn.querySelector('i');
        
        if (content.style.display === 'none') {
            content.style.display = 'block';
            label.innerText = 'Thu gọn';
            icon.className = 'fa-solid fa-chevron-up';
        } else {
            content.style.display = 'none';
            label.innerText = 'Xem chi tiết';
            icon.className = 'fa-solid fa-chevron-down';
        }
    }
</script>

{{-- NHÚNG THƯ VIỆN BROADCASTING & LARAVEL ECHO --}}
<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.0/dist/echo.iife.js"></script>
<style>
    @keyframes highlight-new-post {
        0% { background-color: rgba(59, 130, 246, 0.15); border-color: var(--color-primary); }
        100% { background-color: var(--color-card-bg, #ffffff); }
    }
    .new-post-highlight {
        animation: highlight-new-post 3s ease-out;
    }
</style>
<script>
    // === Cấu hình Real-time WebSockets (Laravel Echo) ===
    const currentUserId = {{ auth()->id() }};
    const isAdmin = {{ auth()->user()->isAdminPortalUser() ? 'true' : 'false' }};
    const currentFilterType = "{{ request()->get('type', 'all') }}";

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function nl2br(str) {
        if (!str) return '';
        return escapeHtml(str).replace(/\n/g, '<br>');
    }

    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    }

    function createPostCardHtml(post) {
        const avatarUrl = post.user.avatar 
            ? `/storage/${post.user.avatar}` 
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(post.user.name)}&background=00236f&color=fff`;
            
        const apartmentText = post.user.apartment 
            ? `Căn hộ: ${escapeHtml(post.user.apartment.apartment_number)}` 
            : 'Ban Quản Trị';
            
        let tagHtml = '';
        if (post.price !== null) {
            tagHtml = `
                <span class="pc-badge pc-badge--marketplace">Thanh lý</span>
                <span class="pc-card__price">${formatNumber(post.price)}đ</span>
            `;
        } else {
            tagHtml = `
                <span class="pc-badge pc-badge--general">Chia sẻ</span>
            `;
        }
        
        let contentHtml = '';
        if (post.content.length > 250) {
            const shortContent = post.content.substring(0, 250) + '...';
            contentHtml = `
                <div class="pc-card__content-wrapper">
                    <p class="pc-card__content" id="content-short-${post.id}">${nl2br(shortContent)}</p>
                    <p class="pc-card__content" id="content-full-${post.id}" style="display: none;">${nl2br(post.content)}</p>
                    <button type="button" class="pc-card__toggle-content-btn" id="btn-toggle-${post.id}" onclick="toggleContent(${post.id})">Xem thêm</button>
                </div>
            `;
        } else {
            contentHtml = `<p class="pc-card__content">${nl2br(post.content)}</p>`;
        }
        
        let imagesHtml = '';
        if (post.images && post.images.length > 0) {
            const imgCount = post.images.length;
            const displayCount = Math.min(imgCount, 5);
            imagesHtml += `<div class="pc-card__image-grid pc-card__image-grid--${displayCount}">`;
            
            const maxVisible = 4;
            const takeImages = post.images.slice(0, maxVisible);
            takeImages.forEach((img, index) => {
                const storageUrl = `/storage/${img.image_path}`;
                imagesHtml += `
                    <div class="pc-card__grid-item" onclick="openLightbox('${storageUrl}')">
                        <img src="${storageUrl}" alt="Ảnh đính kèm" class="pc-card__grid-img">
                        ${index === 3 && imgCount > 4 ? `<div class="pc-card__grid-overlay">+${imgCount - 4} ảnh</div>` : ''}
                    </div>
                `;
            });
            imagesHtml += `</div>`;
        }
        
        let deleteForm = '';
        if (post.user.id === currentUserId || isAdmin) {
            deleteForm = `
                <form action="/resident/posts/${post.id}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài đăng này không?')">
                    <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="pc-card__delete-btn">
                        <i class="fa-regular fa-trash-can"></i> Xóa
                    </button>
                </form>
            `;
        }

        let editBtn = '';
        if (post.user.id === currentUserId) {
            editBtn = `
                <button type="button" class="pc-card__edit-btn" onclick="openEditPostModal(${post.id}, '${escapeHtml(post.title)}', '${escapeHtml(post.content)}', '${post.price || ''}')">
                    <i class="fa-regular fa-pen-to-square"></i> Sửa bài
                </button>
            `;
        }

        let reportBtn = '';
        if (post.user.id !== currentUserId) {
            reportBtn = `
                <button type="button" class="pc-card__action-btn" style="color: var(--color-error); border: none; background: transparent; cursor: pointer; font-family: inherit; font-size: 0.875rem; font-weight: 600; padding: 0; outline: none;" onclick="openReportModal(${post.id})">
                    <i class="fa-regular fa-flag"></i> Báo cáo
                </button>
            `;
        }

        let activeReaction = null;
        if (post.liked_by_current_user && post.liked_by_current_user.length > 0) {
            activeReaction = post.liked_by_current_user[0].type || 'like';
        }

        let likeBtnClass = 'pc-like-btn';
        let likeIconHtml = '<i class="fa-regular fa-thumbs-up"></i>';
        let likeText = 'Thích';

        if (activeReaction) {
            likeBtnClass += ' pc-like-btn--active reaction-active-' + activeReaction;
            likeIconHtml = getReactionEmoji(activeReaction);
            likeText = getReactionText(activeReaction);
        }
        const likesCount = post.likes_count || 0;
        
        return `
            <div class="pc-card new-post-highlight" data-post-id="${post.id}">
                <div class="pc-card__author">
                    <div class="pc-card__author-info">
                        <img src="${avatarUrl}" alt="Avatar" class="pc-card__avatar">
                        <div>
                            <h4 class="pc-card__name">${escapeHtml(post.user.name)}</h4>
                            <span class="pc-card__apartment">${apartmentText}</span>
                        </div>
                    </div>
                    <span class="pc-card__date" title="${escapeHtml(post.created_at)}">${escapeHtml(post.created_at_human)}</span>
                </div>

                <div class="pc-card__tags">
                    ${tagHtml}
                </div>

                <h3 class="pc-card__title">
                    <a href="/resident/posts/${post.id}" class="pc-card__title-link">
                        ${escapeHtml(post.title)}
                    </a>
                </h3>

                ${contentHtml}
                ${imagesHtml}

                <div class="pc-card__footer">
                    <div class="pc-card__actions" style="gap: 1.25rem;">
                        <div class="pc-like-container">
                            <div class="pc-reactions-popup">
                                <span class="pc-reaction-option" data-label="Thích" onclick="toggleLike(event, ${post.id}, 'post', 'like')">👍</span>
                                <span class="pc-reaction-option" data-label="Yêu thích" onclick="toggleLike(event, ${post.id}, 'post', 'love')">❤️</span>
                                <span class="pc-reaction-option" data-label="Haha" onclick="toggleLike(event, ${post.id}, 'post', 'haha')">😆</span>
                                <span class="pc-reaction-option" data-label="Wow" onclick="toggleLike(event, ${post.id}, 'post', 'wow')">😮</span>
                                <span class="pc-reaction-option" data-label="Buồn" onclick="toggleLike(event, ${post.id}, 'post', 'sad')">😢</span>
                                <span class="pc-reaction-option" data-label="Phẫn nộ" onclick="toggleLike(event, ${post.id}, 'post', 'angry')">😡</span>
                            </div>
                            <button type="button" class="pc-card__action-btn ${likeBtnClass}" onclick="toggleLike(event, ${post.id}, 'post')" style="border: none; background: transparent; cursor: pointer; font-family: inherit; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; outline: none;">
                                <span class="reaction-icon-span">${likeIconHtml}</span>
                                <span class="reaction-text-span">${likeText}</span> (<span class="like-count">${likesCount}</span>)
                            </button>
                        </div>
                        <a href="/resident/posts/${post.id}" class="pc-card__action-btn">
                            <i class="fa-regular fa-comment"></i> Bình luận (0)
                        </a>
                        <button type="button" class="pc-card__action-btn" onclick="openShareModal(${post.id}, '${window.location.origin}/resident/posts/${post.id}')">
                            <i class="fa-regular fa-share-from-square"></i> Chia sẻ
                        </button>
                        ${reportBtn}
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        ${editBtn}
                        ${deleteForm}
                    </div>
                </div>
            </div>
        `;
    }

    function getReactionEmoji(type) {
        switch(type) {
            case 'love': return '❤️';
            case 'haha': return '😆';
            case 'wow': return '😮';
            case 'sad': return '😢';
            case 'angry': return '😡';
            default: return '👍';
        }
    }

    function getReactionText(type) {
        switch(type) {
            case 'love': return 'Yêu thích';
            case 'haha': return 'Haha';
            case 'wow': return 'Wow';
            case 'sad': return 'Buồn';
            case 'angry': return 'Phẫn nộ';
            default: return 'Thích';
        }
    }

    // AJAX Toggle Like / Reaction
    function toggleLike(event, id, type, reactionType = null) {
        event.preventDefault();
        event.stopPropagation();
        
        const target = event.currentTarget;
        let btn = target;
        if (target.classList.contains('pc-reaction-option')) {
            btn = target.closest('.pc-like-container').querySelector('.pc-card__action-btn, .pc-comment-like-btn');
        }
        
        const countEl = btn.querySelector('.like-count');
        const iconSpan = btn.querySelector('.reaction-icon-span');
        const textSpan = btn.querySelector('.reaction-text-span');
        
        const reqReactionType = reactionType || 'like';
        
        fetch('/resident/like', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                likeable_id: id,
                likeable_type: type,
                type: reqReactionType
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                countEl.innerText = data.likes_count;
                
                // Reset classes
                btn.className = type === 'post' ? 'pc-card__action-btn pc-like-btn' : 'pc-comment__action-btn pc-comment-like-btn';
                
                if (data.liked && data.reaction_type) {
                    btn.classList.add(type === 'post' ? 'pc-like-btn--active' : 'pc-comment-like-btn--active');
                    btn.classList.add('reaction-active-' + data.reaction_type);
                    if (iconSpan) iconSpan.innerHTML = getReactionEmoji(data.reaction_type);
                    if (textSpan) textSpan.innerText = getReactionText(data.reaction_type);
                } else {
                    if (iconSpan) iconSpan.innerHTML = type === 'post' ? '<i class="fa-regular fa-thumbs-up"></i>' : '<i class="fa-regular fa-heart"></i>';
                    if (textSpan) textSpan.innerText = 'Thích';
                }
            }
        })
        .catch(err => {
            console.error('Error toggling reaction:', err);
        });
    }

    // Modal Edit Post
    function openEditPostModal(id, title, content, price) {
        const modal = document.getElementById('editPostModal');
        const form = document.getElementById('edit-post-form');
        form.action = `/resident/posts/${id}`;
        
        document.getElementById('edit-post-title').value = title || '';
        document.getElementById('edit-post-content').value = content || '';
        
        const priceInput = document.getElementById('edit-post-price');
        if (price && parseFloat(price) > 0) {
            priceInput.value = parseInt(price, 10).toLocaleString('vi-VN');
        } else {
            priceInput.value = '';
        }
        
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    }

    function closeEditPostModal() {
        const modal = document.getElementById('editPostModal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    function handleEditModalClick(event) {
        if (event.target === document.getElementById('editPostModal')) {
            closeEditPostModal();
        }
    }

    // Khởi tạo định dạng tiền cho ô nhập sửa bài viết
    initPriceFormatter('edit-post-price', 'edit-post-price-preview');

    // Xử lý submit sửa bài viết qua AJAX
    const editPostForm = document.getElementById('edit-post-form');
    if (editPostForm) {
        editPostForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.setAttribute('disabled', 'disabled');
            submitBtn.innerText = 'Đang lưu...';
            
            const priceInput = document.getElementById('edit-post-price');
            let rawPrice = priceInput.value.replace(/[^0-9]/g, '');
            
            const formData = {
                title: document.getElementById('edit-post-title').value.trim(),
                content: document.getElementById('edit-post-content').value.trim(),
                price: rawPrice || null,
            };

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    ...formData,
                    _method: 'PUT'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditPostModal();
                    showToast('Cập nhật bài viết thành công!', 'success');
                    
                    const postId = data.post.id;
                    const postCard = document.querySelector(`.pc-card[data-post-id="${postId}"]`);
                    if (postCard) {
                        const titleLink = postCard.querySelector('.pc-card__title-link');
                        if (titleLink) titleLink.innerText = data.post.title;

                        const contentWrapper = postCard.querySelector('.pc-card__content-wrapper');
                        const contentNormal = postCard.querySelector('.pc-card__content');
                        if (contentWrapper) {
                            const shortEl = document.getElementById('content-short-' + postId);
                            const fullEl = document.getElementById('content-full-' + postId);
                            if (data.post.content.length > 250) {
                                if (shortEl) shortEl.innerHTML = nl2br(data.post.content.substring(0, 250) + '...');
                                if (fullEl) fullEl.innerHTML = nl2br(data.post.content);
                            } else {
                                contentWrapper.outerHTML = `<p class="pc-card__content">${nl2br(data.post.content)}</p>`;
                            }
                        } else if (contentNormal) {
                            if (data.post.content.length > 250) {
                                const shortContent = data.post.content.substring(0, 250) + '...';
                                contentNormal.outerHTML = `
                                    <div class="pc-card__content-wrapper">
                                        <p class="pc-card__content" id="content-short-${postId}">${nl2br(shortContent)}</p>
                                        <p class="pc-card__content" id="content-full-${postId}" style="display: none;">${nl2br(data.post.content)}</p>
                                        <button type="button" class="pc-card__toggle-content-btn" id="btn-toggle-${postId}" onclick="toggleContent(${postId})">Xem thêm</button>
                                    </div>
                                `;
                            } else {
                                contentNormal.innerHTML = nl2br(data.post.content);
                            }
                        }

                        const tagsContainer = postCard.querySelector('.pc-card__tags');
                        if (tagsContainer) {
                            if (data.post.price !== null) {
                                tagsContainer.innerHTML = `
                                    <span class="pc-badge pc-badge--marketplace">Thanh lý</span>
                                    <span class="pc-card__price">${formatNumber(data.post.price)}đ</span>
                                `;
                            } else {
                                tagsContainer.innerHTML = `
                                    <span class="pc-badge pc-badge--general">Chia sẻ</span>
                                `;
                            }
                        }
                    }
                }
            })
            .catch(error => {
                console.error(error);
                showToast('Có lỗi xảy ra khi cập nhật. Vui lòng thử lại!', 'error');
            })
            .finally(() => {
                submitBtn.removeAttribute('disabled');
                submitBtn.innerText = 'Lưu thay đổi';
            });
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.Pusher === 'undefined' && typeof Pusher !== 'undefined') {
            window.Pusher = Pusher;
        }

        const broadcastConnection = "{{ env('BROADCAST_CONNECTION', 'reverb') }}";
        console.log("DomusHub WebSockets: Connecting using driver:", broadcastConnection);

        let echoConfig = {};
        
        if (broadcastConnection === 'reverb') {
            echoConfig = {
                broadcaster: 'reverb',
                key: "{{ env('REVERB_APP_KEY', 'domushub_key') }}",
                wsHost: "{{ env('REVERB_HOST') }}" || window.location.hostname,
                wsPort: parseInt("{{ env('REVERB_PORT', '8080') }}", 10),
                wssPort: parseInt("{{ env('REVERB_PORT', '8080') }}", 10),
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
            };
        } else {
            echoConfig = {
                broadcaster: 'pusher',
                key: "{{ env('PUSHER_APP_KEY') }}",
                cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
                forceTLS: true
            };
        }

        console.log("DomusHub Echo Config:", echoConfig);

        if (typeof window.Echo === 'undefined' && typeof window.LaravelEcho !== 'undefined') {
            window.Echo = new window.LaravelEcho(echoConfig);
            console.log("DomusHub WebSockets: Echo initialized!");
        }

        if (window.Echo) {
            console.log("DomusHub WebSockets: Subscribing to 'community-feed' channel...");
            window.Echo.channel('community-feed')
                .listen('PostCreated', (e) => {
                    console.log("DomusHub WebSockets: Received Event PostCreated:", e);
                    if (currentFilterType === 'marketplace' && e.price === null) return;
                    if (currentFilterType === 'general' && e.price !== null) return;

                    const emptyPlaceholder = document.getElementById('empty-feed-placeholder');
                    if (emptyPlaceholder) {
                        emptyPlaceholder.remove();
                    }

                    if (document.querySelector(`.pc-card[data-post-id="${e.id}"]`)) return;

                    const container = document.getElementById('posts-list-container');
                    if (container) {
                        const postHtml = createPostCardHtml(e);
                        container.insertAdjacentHTML('afterbegin', postHtml);
                        
                        showToast(`Có bài đăng mới từ căn hộ ${e.user.apartment ? e.user.apartment.apartment_number : 'Ban Quản Trị'}!`, 'success');
                    }
                })
                .listen('PostUpdated', (e) => {
                    console.log("DomusHub WebSockets: Received Event PostUpdated:", e);
                    const postCard = document.querySelector(`.pc-card[data-post-id="${e.id}"]`);
                    if (postCard) {
                        const titleLink = postCard.querySelector('.pc-card__title-link');
                        if (titleLink) titleLink.innerText = e.title;

                        const contentWrapper = postCard.querySelector('.pc-card__content-wrapper');
                        const contentNormal = postCard.querySelector('.pc-card__content');
                        if (contentWrapper) {
                            const shortEl = document.getElementById('content-short-' + e.id);
                            const fullEl = document.getElementById('content-full-' + e.id);
                            if (e.content.length > 250) {
                                if (shortEl) shortEl.innerHTML = nl2br(e.content.substring(0, 250) + '...');
                                if (fullEl) fullEl.innerHTML = nl2br(e.content);
                            } else {
                                contentWrapper.outerHTML = `<p class="pc-card__content">${nl2br(e.content)}</p>`;
                            }
                        } else if (contentNormal) {
                            if (e.content.length > 250) {
                                const shortContent = e.content.substring(0, 250) + '...';
                                contentNormal.outerHTML = `
                                    <div class="pc-card__content-wrapper">
                                        <p class="pc-card__content" id="content-short-${e.id}">${nl2br(shortContent)}</p>
                                        <p class="pc-card__content" id="content-full-${e.id}" style="display: none;">${nl2br(e.content)}</p>
                                        <button type="button" class="pc-card__toggle-content-btn" id="btn-toggle-${e.id}" onclick="toggleContent(${e.id})">Xem thêm</button>
                                    </div>
                                `;
                            } else {
                                contentNormal.innerHTML = nl2br(e.content);
                            }
                        }

                        const tagsContainer = postCard.querySelector('.pc-card__tags');
                        if (tagsContainer) {
                            if (e.price !== null) {
                                tagsContainer.innerHTML = `
                                    <span class="pc-badge pc-badge--marketplace">Thanh lý</span>
                                    <span class="pc-card__price">${formatNumber(e.price)}đ</span>
                                `;
                            } else {
                                tagsContainer.innerHTML = `
                                    <span class="pc-badge pc-badge--general">Chia sẻ</span>
                                `;
                            }
                        }
                    }
                })
                .listen('LikeToggled', (e) => {
                    console.log("DomusHub WebSockets: Received Event LikeToggled:", e);
                    if (e.likeable_type === 'post') {
                        const postCard = document.querySelector(`.pc-card[data-post-id="${e.likeable_id}"]`);
                        if (postCard) {
                            const likeCountEl = postCard.querySelector('.like-count');
                            if (likeCountEl) {
                                likeCountEl.innerText = e.likes_count;
                            }
                        }
                    }
                });
        } else {
            console.error("DomusHub WebSockets: Failed to initialize Echo. Please check libraries.");
        }
    });
</script>
@endsection
