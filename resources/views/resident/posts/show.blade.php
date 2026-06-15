@extends('layouts.resident.master')

@section('title', $post->title . ' – Bảng tin DomusHub')

@push('styles')
    @vite(['resources/css/resident/posts.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tributejs/5.1.3/tribute.css">
@endpush

@section('content')
@php
    if (!function_exists('formatCommentContentBlade')) {
        function formatCommentContentBlade($content) {
            $escaped = e($content);
            // Match zero-width space tagged mentions (\x{200B})
            $escaped = preg_replace('/@([^@\x{200B}]+)\x{200B}/u', '<span class="comment-mention">@$1</span>', $escaped);
            return nl2br($escaped);
        }
    }
@endphp
<div class="pc" style="max-width: 800px;">
    
    {{-- NÚT QUAY LẠI --}}
    <a href="{{ route('resident.posts.index') }}" class="pc-detail__back">
        <i class="fa-solid fa-arrow-left"></i> Quay lại bảng tin
    </a>

    {{-- THÔNG BÁO THÀNH CÔNG/LỖI --}}
    @if(session('success'))
        <div class="pc-alert pc-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- CHI TIẾT BÀI VIẾT --}}
    <article class="pc-detail">
        {{-- Tiêu đề --}}
        <h1 class="pc-detail__title">{{ $post->title }}</h1>

        {{-- Meta & Author --}}
        <div class="pc-detail__meta">
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
            
            <div style="text-align: right; display: flex; flex-direction: column; gap: 0.25rem;">
                <span class="pc-comment__time">{{ $post->created_at->format('H:i d/m/Y') }}</span>
                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center;">
                    @if($post->price !== null)
                        <span class="pc-badge pc-badge--marketplace">Thanh lý</span>
                        <span class="pc-card__price">{{ number_format($post->price, 0, ',', '.') }}đ</span>
                    @else
                        <span class="pc-badge pc-badge--general">Chia sẻ</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Nội dung chữ --}}
        <div class="pc-detail__content">{!! nl2br(e($post->content)) !!}</div>

        {{-- Ảnh đính kèm (Lưới ảnh CSS động kiểu Facebook) --}}
        @if($post->images->isNotEmpty())
            @php
                $imgCount = $post->images->count();
            @endphp
            <div class="pc-card__image-grid pc-card__image-grid--{{ min($imgCount, 5) }}" style="margin-bottom: 2.5rem;">
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

        <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; padding-top: 1rem; margin-top: 1.5rem;">
            <div style="display: flex; gap: 1.25rem; align-items: center;">
                {{-- Nút Thích bài viết --}}
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

                {{-- Nút Chia sẻ --}}
                <button type="button" class="pc-card__action-btn" onclick="openShareModal({{ $post->id }}, '{{ route('resident.posts.show', $post->id) }}')">
                    <i class="fa-regular fa-share-from-square"></i> Chia sẻ
                </button>
            </div>

            <div style="display: flex; gap: 0.5rem; align-items: center;">
                {{-- Nút sửa bài viết (chỉ dành cho tác giả) --}}
                @if($post->user_id === auth()->id())
                    <button type="button" class="pc-card__edit-btn" onclick="openEditPostModal({{ $post->id }}, '{{ addslashes($post->title) }}', '{{ addslashes($post->content) }}', '{{ $post->price }}')" style="color: var(--color-primary); background: transparent; border: none; cursor: pointer; font-size: 0.85rem; font-weight: 600; padding: 0.4rem 0.8rem; border-radius: var(--radius); transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i class="fa-regular fa-pen-to-square"></i> Sửa bài viết
                    </button>
                @endif

                {{-- Nút xóa bài đăng (chống IDOR) --}}
                @if($post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
                    <form action="{{ route('resident.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài đăng này không?')" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="pc-card__delete-btn">
                            <i class="fa-regular fa-trash-can"></i> Xóa bài viết
                        </button>
                    </form>
                @elseif($post->user_id !== auth()->id())
                    <button type="button" class="pc-card__action-btn" style="color: var(--color-error); border: none; background: transparent; cursor: pointer; font-family: inherit; font-size: 0.875rem; font-weight: 600; padding: 0.4rem 0.8rem; outline: none;" onclick="openReportModal({{ $post->id }})">
                        <i class="fa-regular fa-flag"></i> Báo cáo bài viết
                    </button>
                @endif
            </div>
        </div>
    </article>

    {{-- KHU VỰC BÌNH LUẬN --}}
    <section class="pc-comments-sec">
        <h3 class="pc-comments__title">
            <i class="fa-regular fa-comments" style="margin-right: 0.5rem;"></i> Bình luận (<span id="total-comments-count">{{ $totalComments }}</span>)
        </h3>

        {{-- FORM VIẾT BÌNH LUẬN --}}
        <div class="pc-comments__form">
            <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=00236f&color=fff' }}" 
                 alt="Avatar" class="pc-comment__avatar">
            
            <div class="pc-comments__textarea-wrap">
                <form action="{{ route('resident.posts.comments.store', $post->id) }}" method="POST" id="comment-form" enctype="multipart/form-data">
                    @csrf
                    {{-- ID comment cha (nếu có) --}}
                    <input type="hidden" name="parent_id" id="comment-parent-id" value="">

                    {{-- Banner hiển thị đang trả lời ai --}}
                    <div class="pc-reply-indicator" id="reply-indicator" style="display: none; margin-bottom: 0.5rem;">
                        <span><i class="fa-solid fa-reply"></i> Đang trả lời <strong id="reply-target-name"></strong></span>
                        <button type="button" class="pc-reply-indicator__close" onclick="cancelReply()">&times;</button>
                    </div>

                    <textarea name="content" id="comment-content" class="pc-comments__input" placeholder="Viết bình luận của bạn..." required></textarea>
                    
                    {{-- Khung Preview ảnh bình luận --}}
                    <div class="pc-comment__image-preview-wrap" id="comment-image-preview-wrap" style="display: none; margin-top: 0.5rem; position: relative; width: 80px; height: 80px; border-radius: var(--radius-md); overflow: hidden; border: 1.5px solid #e2e8f0; box-shadow: var(--color-shadow);">
                        <img id="comment-image-preview" src="" style="width: 100%; height: 100%; object-fit: cover;">
                        <button type="button" class="pc-comment__image-preview-remove" onclick="removeCommentImage()" style="position: absolute; top: 2px; right: 2px; background: rgba(15, 23, 42, 0.75); color: #ffffff; border: none; width: 18px; height: 18px; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 0.6rem;">&times;</button>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                        {{-- Nút đính kèm ảnh bình luận --}}
                        <label class="pc-composer__file-label" style="margin: 0; padding: 0.4rem 0.85rem; font-size: 0.8rem;">
                            <i class="fa-regular fa-image" style="color: #2563eb;"></i> Thêm ảnh
                            <input type="file" name="image" id="comment-image-input" accept="image/*" style="display: none;" onchange="previewCommentImage(event)">
                        </label>
                        <button type="submit" class="pc-btn">Gửi bình luận</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- DANH SÁCH CÁC BÌNH LUẬN --}}
        <div class="pc-comments-list">
            {{-- Nút tải thêm bình luận cũ --}}
            @if($totalComments > 10)
                <div style="text-align: center; margin-bottom: 1.5rem;" id="load-older-container">
                    <button type="button" class="pc-load-older-btn" id="load-older-btn" data-offset="10" data-total="{{ $totalComments }}" onclick="loadOlderComments()">
                        <i class="fa-solid fa-clock-rotate-left"></i> Xem thêm bình luận cũ hơn (<span id="older-count">{{ $totalComments - 10 }}</span>)
                    </button>
                </div>
            @endif

            @if($comments->isEmpty())
                <p id="empty-comments-placeholder" style="text-align: center; color: var(--color-text-secondary); font-size: 0.9rem; margin: 2rem 0;">Chưa có bình luận nào. Hãy bắt đầu cuộc trò chuyện!</p>
            @else
                @foreach($comments as $comment)
                    <div class="pc-comment-thread" id="comment-thread-{{ $comment->id }}">
                        {{-- BÌNH LUẬN CHA --}}
                        <div class="pc-comment {{ $comment->is_pinned ? 'pc-comment--pinned' : '' }}" id="comment-{{ $comment->id }}">
                            <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=00236f&color=fff' }}" 
                                 alt="Avatar" class="pc-comment__avatar">
                            
                            <div class="pc-comment__body">
                                <div class="pc-comment__header">
                                    <div class="pc-comment__user-info">
                                        <span class="pc-comment__author-name">{{ $comment->user->name }}</span>
                                        
                                        <span class="pc-comment__apartment">
                                            @if($comment->user->apartment)
                                                Căn hộ: {{ $comment->user->apartment->apartment_number }}
                                            @else
                                                Ban Quản Trị
                                            @endif
                                        </span>
                                        <span class="pc-comment__pinned-badge" id="pinned-badge-{{ $comment->id }}" style="display: {{ $comment->is_pinned ? 'inline-flex' : 'none' }}; align-items: center; gap: 0.25rem; background-color: #fef3c7; color: #d97706; padding: 0.15rem 0.4rem; border-radius: var(--radius-sm); font-size: 0.7rem; font-weight: 700; border: 1px solid rgba(217, 119, 6, 0.15); margin-left: 0.5rem;">
                                            <i class="fa-solid fa-thumbtack"></i> Đã ghim
                                        </span>
                                    </div>
                                    
                                    <span class="pc-comment__time" title="{{ $comment->created_at }}">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                
                                <div class="comment-content-wrap" id="comment-content-wrap-{{ $comment->id }}">
                                    <p class="pc-comment__content" id="comment-text-{{ $comment->id }}">{!! formatCommentContentBlade($comment->content) !!}</p>
                                    @if($comment->image_path && $comment->content !== '[Bình luận này đã bị xóa bởi Ban quản trị do vi phạm quy chuẩn cộng đồng]' && $comment->content !== '[Bình luận này đã bị ẩn tạm thời do nhận nhiều báo cáo vi phạm]')
                                        <div class="pc-comment__image-attach" id="comment-img-attach-{{ $comment->id }}" style="margin-top: 0.5rem; max-width: 200px; border-radius: var(--radius-md); overflow: hidden; border: 1px solid #e2e8f0; cursor: zoom-in;" onclick="openLightbox('{{ asset('storage/' . $comment->image_path) }}')">
                                            <img src="{{ asset('storage/' . $comment->image_path) }}" style="width: 100%; max-height: 150px; object-fit: cover; display: block;">
                                        </div>
                                    @endif
                                </div>

                                {{-- Form chỉnh sửa inline ẩn --}}
                                @if($comment->user_id === auth()->id())
                                    <div class="comment-edit-inline-wrap" id="comment-edit-wrap-{{ $comment->id }}" style="display: none; margin: 0.5rem 0;">
                                        <textarea class="pc-comments__input" id="comment-edit-input-{{ $comment->id }}" rows="2" style="min-height: 45px; resize: vertical;"></textarea>
                                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.4rem;">
                                            <button type="button" class="pc-btn pc-btn--secondary" style="padding: 0.35rem 0.75rem; font-size: 0.775rem;" onclick="cancelEditComment({{ $comment->id }})">Hủy</button>
                                            <button type="button" class="pc-btn" style="padding: 0.35rem 0.75rem; font-size: 0.775rem;" onclick="saveEditComment({{ $comment->id }})">Lưu</button>
                                        </div>
                                    </div>
                                @endif
                                
                                {{-- Các hành động trên bình luận --}}
                                <div class="pc-comment__actions" style="display: flex; width: 100%; align-items: center; gap: 1rem;">
                                    {{-- Nút Thích bình luận --}}
                                    <div class="pc-like-container">
                                        <div class="pc-reactions-popup">
                                            <span class="pc-reaction-option" data-label="Thích" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'like')">👍</span>
                                            <span class="pc-reaction-option" data-label="Yêu thích" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'love')">❤️</span>
                                            <span class="pc-reaction-option" data-label="Haha" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'haha')">😆</span>
                                            <span class="pc-reaction-option" data-label="Wow" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'wow')">😮</span>
                                            <span class="pc-reaction-option" data-label="Buồn" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'sad')">😢</span>
                                            <span class="pc-reaction-option" data-label="Phẫn nộ" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'angry')">😡</span>
                                        </div>

                                        @php
                                            $userLike = $comment->likedByCurrentUser->first();
                                            $activeReaction = $userLike ? $userLike->type : null;
                                            
                                            $likeBtnClass = 'pc-comment-like-btn';
                                            $likeIconHtml = '<i class="fa-regular fa-heart"></i>';
                                            $likeText = 'Thích';
                                            
                                            if ($activeReaction) {
                                                $likeBtnClass .= ' pc-comment-like-btn--active reaction-active-' . $activeReaction;
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

                                        <button type="button" class="pc-comment__action-btn {{ $likeBtnClass }}" onclick="toggleLike(event, {{ $comment->id }}, 'comment')">
                                            <span class="reaction-icon-span">{!! $likeIconHtml !!}</span>
                                            <span class="reaction-text-span">{{ $likeText }}</span> (<span class="like-count">{{ $comment->likes_count }}</span>)
                                        </button>
                                    </div>

                                    <button type="button" class="pc-comment__action-btn" onclick="replyToComment({{ $comment->id }}, '{{ $comment->user->name }}')">
                                        Trả lời
                                    </button>

                                    {{-- Nút Ghim/Bỏ ghim bình luận --}}
                                    @if(($post->user_id === auth()->id() || auth()->user()->isAdminPortalUser()) && $comment->parent_id === null)
                                        <button type="button" class="pc-comment__action-btn pc-comment__action-btn--pin" id="pin-btn-{{ $comment->id }}" onclick="togglePinComment({{ $comment->id }})" style="color: #d97706; font-weight: 700;">
                                            {{ $comment->is_pinned ? 'Bỏ ghim' : 'Ghim' }}
                                        </button>
                                    @endif

                                    {{-- Nút Sửa bình luận --}}
                                    @if($comment->user_id === auth()->id())
                                        <button type="button" class="pc-comment__action-btn" onclick="startEditComment({{ $comment->id }})">
                                            Sửa
                                        </button>
                                    @endif

                                    {{-- Nút xóa bình luận --}}
                                    @if($comment->user_id === auth()->id() || $post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
                                        <form action="{{ route('resident.comments.destroy', $comment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này không?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="pc-comment__action-btn pc-comment__action-btn--delete">
                                                Xóa
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Nút báo cáo bình luận --}}
                                    @if($comment->user_id !== auth()->id())
                                        <button type="button" class="pc-comment__action-btn pc-comment__action-btn--report" style="color: var(--color-error); margin-left: auto; display: flex; align-items: center; gap: 0.25rem;" onclick="openCommentReportModal({{ $comment->id }})">
                                            <i class="fa-regular fa-flag"></i> Báo cáo
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- NÚT XEM THÊM CÂU TRẢ LỜI --}}
                        @if($comment->replies->count() > 0)
                            <div class="pc-comment__replies-toggle-wrapper" id="replies-toggle-wrapper-{{ $comment->id }}">
                                <button type="button" class="pc-comment__view-replies-btn" id="view-replies-btn-{{ $comment->id }}" onclick="toggleReplies({{ $comment->id }})">
                                    <i class="fa-solid fa-share-nodes"></i> Xem <span class="reply-count">{{ $comment->replies->count() }}</span> câu trả lời
                                </button>
                            </div>
                        @endif

                        {{-- DANH SÁCH CÁC PHẢN HỒI CON --}}
                        <div class="pc-comment-replies" id="replies-container-{{ $comment->id }}" style="display: none;">
                            @foreach($comment->replies as $reply)
                                <div class="pc-comment pc-comment--reply" id="comment-{{ $reply->id }}">
                                    <img src="{{ $reply->user->avatar ? asset('storage/' . $reply->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($reply->user->name) . '&background=00236f&color=fff' }}" 
                                         alt="Avatar" class="pc-comment__avatar pc-comment__avatar--reply">
                                    
                                    <div class="pc-comment__body">
                                        <div class="pc-comment__header">
                                            <div class="pc-comment__user-info">
                                                <span class="pc-comment__author-name">{{ $reply->user->name }}</span>
                                                <span class="pc-comment__apartment">
                                                    @if($reply->user->apartment)
                                                        Căn hộ: {{ $reply->user->apartment->apartment_number }}
                                                    @else
                                                        Ban Quản Trị
                                                    @endif
                                                </span>
                                            </div>
                                            <span class="pc-comment__time" title="{{ $reply->created_at }}">
                                                {{ $reply->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        
                                        <div class="comment-content-wrap" id="comment-content-wrap-{{ $reply->id }}">
                                            <p class="pc-comment__content" id="comment-text-{{ $reply->id }}">{!! formatCommentContentBlade($reply->content) !!}</p>
                                            @if($reply->image_path && $reply->content !== '[Bình luận này đã bị xóa bởi Ban quản trị do vi phạm quy chuẩn cộng đồng]' && $reply->content !== '[Bình luận này đã bị ẩn tạm thời do nhận nhiều báo cáo vi phạm]')
                                                <div class="pc-comment__image-attach" id="comment-img-attach-{{ $reply->id }}" style="margin-top: 0.5rem; max-width: 160px; border-radius: var(--radius-md); overflow: hidden; border: 1px solid #e2e8f0; cursor: zoom-in;" onclick="openLightbox('{{ asset('storage/' . $reply->image_path) }}')">
                                                    <img src="{{ asset('storage/' . $reply->image_path) }}" style="width: 100%; max-height: 120px; object-fit: cover; display: block;">
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Form chỉnh sửa inline ẩn --}}
                                        @if($reply->user_id === auth()->id())
                                            <div class="comment-edit-inline-wrap" id="comment-edit-wrap-{{ $reply->id }}" style="display: none; margin: 0.5rem 0;">
                                                <textarea class="pc-comments__input" id="comment-edit-input-{{ $reply->id }}" rows="2" style="min-height: 45px; resize: vertical;"></textarea>
                                                <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.4rem;">
                                                    <button type="button" class="pc-btn pc-btn--secondary" style="padding: 0.35rem 0.75rem; font-size: 0.775rem;" onclick="cancelEditComment({{ $reply->id }})">Hủy</button>
                                                    <button type="button" class="pc-btn" style="padding: 0.35rem 0.75rem; font-size: 0.775rem;" onclick="saveEditComment({{ $reply->id }})">Lưu</button>
                                                </div>
                                            </div>
                                        @endif
                                        
                                            <div class="pc-like-container">
                                                <div class="pc-reactions-popup">
                                                    <span class="pc-reaction-option" data-label="Thích" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'like')">👍</span>
                                                    <span class="pc-reaction-option" data-label="Yêu thích" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'love')">❤️</span>
                                                    <span class="pc-reaction-option" data-label="Haha" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'haha')">😆</span>
                                                    <span class="pc-reaction-option" data-label="Wow" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'wow')">😮</span>
                                                    <span class="pc-reaction-option" data-label="Buồn" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'sad')">😢</span>
                                                    <span class="pc-reaction-option" data-label="Phẫn nộ" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'angry')">😡</span>
                                                </div>

                                                @php
                                                    $userLike = $reply->likedByCurrentUser->first();
                                                    $activeReaction = $userLike ? $userLike->type : null;
                                                    
                                                    $likeBtnClass = 'pc-comment-like-btn';
                                                    $likeIconHtml = '<i class="fa-regular fa-heart"></i>';
                                                    $likeText = 'Thích';
                                                    
                                                    if ($activeReaction) {
                                                        $likeBtnClass .= ' pc-comment-like-btn--active reaction-active-' . $activeReaction;
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

                                                <button type="button" class="pc-comment__action-btn {{ $likeBtnClass }}" onclick="toggleLike(event, {{ $reply->id }}, 'comment')">
                                                    <span class="reaction-icon-span">{!! $likeIconHtml !!}</span>
                                                    <span class="reaction-text-span">{{ $likeText }}</span> (<span class="like-count">{{ $reply->likes_count }}</span>)
                                                </button>
                                            </div>

                                            <button type="button" class="pc-comment__action-btn" onclick="replyToComment({{ $comment->id }}, '{{ $reply->user->name }}')">
                                                Trả lời
                                            </button>

                                            @if($reply->user_id === auth()->id())
                                                <button type="button" class="pc-comment__action-btn" onclick="startEditComment({{ $reply->id }})">
                                                    Sửa
                                                </button>
                                            @endif

                                            @if($reply->user_id === auth()->id() || $post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
                                                <form action="{{ route('resident.comments.destroy', $reply->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này không?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="pc-comment__action-btn pc-comment__action-btn--delete">
                                                        Xóa
                                                    </button>
                                                </form>
                                            @endif

                                            @if($reply->user_id !== auth()->id())
                                                <button type="button" class="pc-comment__action-btn pc-comment__action-btn--report" style="color: var(--color-error); margin-left: auto; display: flex; align-items: center; gap: 0.25rem;" onclick="openCommentReportModal({{ $reply->id }})">
                                                    <i class="fa-regular fa-flag"></i> Báo cáo
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </section>

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

{{-- Modal Báo cáo Bình luận --}}
<div id="reportCommentModal" class="rep-modal" onclick="handleCommentOutsideModalClick(event)">
    <div class="rep-modal__content">
        <div class="rep-modal__header">
            <h3 class="rep-modal__title">
                <i class="fa-solid fa-triangle-exclamation" style="color: var(--color-error);"></i> Báo cáo bình luận
            </h3>
            <button type="button" class="rep-modal__close" onclick="closeCommentReportModal()">&times;</button>
        </div>
        <form id="report-comment-form">
            @csrf
            <input type="hidden" name="comment_id" id="report-target-comment-id" value="">
            <div class="rep-modal__body">
                <span class="rep-modal__label">Tại sao bạn muốn báo cáo bình luận này?</span>
                
                <label class="rep-option">
                    <input type="radio" name="comment_reason_preset" value="Spam, quảng cáo rác" onclick="toggleCommentCustomReason(false)">
                    <span class="rep-option__text">Spam, quảng cáo rác</span>
                </label>
                
                <label class="rep-option">
                    <input type="radio" name="comment_reason_preset" value="Từ ngữ thô tục, công kích" onclick="toggleCommentCustomReason(false)">
                    <span class="rep-option__text">Từ ngữ thô tục, công kích</span>
                </label>
                
                <label class="rep-option">
                    <input type="radio" name="comment_reason_preset" value="Lừa đảo, giả mạo" onclick="toggleCommentCustomReason(false)">
                    <span class="rep-option__text">Lừa đảo, giả mạo thông tin</span>
                </label>
                
                <label class="rep-option">
                    <input type="radio" name="comment_reason_preset" value="Nội dung phản cảm, thù địch" onclick="toggleCommentCustomReason(false)">
                    <span class="rep-option__text">Nội dung phản cảm, thù địch</span>
                </label>
                
                <label class="rep-option">
                    <input type="radio" name="comment_reason_preset" value="other" onclick="toggleCommentCustomReason(true)">
                    <span class="rep-option__text">Lý do khác...</span>
                </label>

                <div class="rep-modal__custom-reason" id="comment-custom-reason-container">
                    <textarea name="comment_reason_custom" id="report-comment-reason-custom" class="rep-modal__textarea" placeholder="Nhập lý do cụ thể của bạn ở đây..."></textarea>
                </div>
            </div>
            <div class="rep-modal__footer">
                <button type="button" class="pc-btn pc-btn--secondary" onclick="closeCommentReportModal()">Hủy bỏ</button>
                <button type="submit" class="rep-modal__btn-submit" id="submit-comment-report-btn" disabled>Gửi báo cáo</button>
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

{{-- Modal Xem Danh Sách Cảm Xúc --}}
<div id="reactionsListModal" class="rep-modal" onclick="handleReactionsOutsideClick(event)" style="display: none; align-items: center; justify-content: center; z-index: 10002;">
    <div class="rep-modal__content" style="max-width: 450px; border-radius: var(--radius-lg); overflow: hidden; box-shadow: var(--color-shadow-lg);">
        <div class="rep-modal__header" style="border-bottom: 1px solid #f1f5f9; padding: 1.25rem 1.5rem;">
            <h3 class="rep-modal__title" style="font-size: 1.1rem; font-weight: 700; color: var(--color-text); display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                <i class="fa-regular fa-face-smile" style="color: var(--color-primary);"></i> Người đã bày tỏ cảm xúc
            </h3>
            <button type="button" class="rep-modal__close" onclick="closeReactionsModal()" style="font-size: 1.5rem; color: var(--color-outline); background: transparent; border: none; cursor: pointer; line-height: 1;">&times;</button>
        </div>
        <div class="rep-modal__body" style="padding: 0;">
            {{-- Tabs cảm xúc --}}
            <div class="react-tabs" id="reactions-modal-tabs" style="display: flex; border-bottom: 1px solid #f1f5f9; overflow-x: auto; white-space: nowrap; padding: 0.5rem 1rem; gap: 0.25rem; scrollbar-width: none;">
                <button type="button" class="react-tab react-tab--active" data-type="all" onclick="filterReactions('all')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-weight: 700; font-size: 0.825rem; cursor: pointer; color: var(--color-primary); border-bottom: 3px solid var(--color-primary); outline: none; transition: all 0.2s;">Tất cả</button>
                <button type="button" class="react-tab" data-type="like" onclick="filterReactions('like')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-weight: 600; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); outline: none; transition: all 0.2s; display: none;">👍 <span class="react-count-tab" style="font-size: 0.75rem; font-weight: 700; margin-left: 0.15rem;">0</span></button>
                <button type="button" class="react-tab" data-type="love" onclick="filterReactions('love')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-weight: 600; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); outline: none; transition: all 0.2s; display: none;">❤️ <span class="react-count-tab" style="font-size: 0.75rem; font-weight: 700; margin-left: 0.15rem;">0</span></button>
                <button type="button" class="react-tab" data-type="haha" onclick="filterReactions('haha')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-weight: 600; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); outline: none; transition: all 0.2s; display: none;">😆 <span class="react-count-tab" style="font-size: 0.75rem; font-weight: 700; margin-left: 0.15rem;">0</span></button>
                <button type="button" class="react-tab" data-type="wow" onclick="filterReactions('wow')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-weight: 600; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); outline: none; transition: all 0.2s; display: none;">😮 <span class="react-count-tab" style="font-size: 0.75rem; font-weight: 700; margin-left: 0.15rem;">0</span></button>
                <button type="button" class="react-tab" data-type="sad" onclick="filterReactions('sad')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-weight: 600; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); outline: none; transition: all 0.2s; display: none;">😢 <span class="react-count-tab" style="font-size: 0.75rem; font-weight: 700; margin-left: 0.15rem;">0</span></button>
                <button type="button" class="react-tab" data-type="angry" onclick="filterReactions('angry')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-weight: 600; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); outline: none; transition: all 0.2s; display: none;">😡 <span class="react-count-tab" style="font-size: 0.75rem; font-weight: 700; margin-left: 0.15rem;">0</span></button>
            </div>
            {{-- Danh sách thành viên --}}
            <div id="reactions-list-container" style="max-height: 320px; overflow-y: auto; padding: 1rem 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                <div style="text-align: center; color: var(--color-outline); padding: 1rem;">Đang tải...</div>
            </div>
        </div>
    </div>
</div>

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
                
                // Tự động chuyển hướng về trang index sau 1.5s vì bài viết đã bị ẩn đối với user
                setTimeout(() => {
                    window.location.href = "{{ route('resident.posts.index') }}";
                }, 1500);
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

    // Phóng to ảnh
    function openLightbox(src) {
        const lightbox = document.getElementById('pcLightbox');
        const img = document.getElementById('pcLightboxTarget');
        lightbox.style.display = "flex";
        img.src = src;
    }

    function closeLightbox() {
        document.getElementById('pcLightbox').style.display = "none";
    }

    // Xử lý khi nhấn "Trả lời" một bình luận khác
    function replyToComment(commentId, authorName) {
        const parentInput = document.getElementById('comment-parent-id');
        const indicator = document.getElementById('reply-indicator');
        const targetNameSpan = document.getElementById('reply-target-name');
        const textarea = document.getElementById('comment-content');

        parentInput.value = commentId;
        targetNameSpan.innerText = authorName;
        indicator.style.display = 'flex';
        
        textarea.placeholder = `Trả lời bình luận của ${authorName}...`;
        textarea.focus();

        // Cuộn mượt tới khung soạn thảo bình luận
        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Hủy trạng thái trả lời
    function cancelReply() {
        const parentInput = document.getElementById('comment-parent-id');
        const indicator = document.getElementById('reply-indicator');
        const textarea = document.getElementById('comment-content');

        parentInput.value = '';
        indicator.style.display = 'none';
        textarea.placeholder = "Viết bình luận của bạn...";
    }

    // Modal Báo cáo Bình luận
    function openCommentReportModal(commentId) {
        const modal = document.getElementById('reportCommentModal');
        document.getElementById('report-target-comment-id').value = commentId;
        
        // Reset form
        document.getElementById('report-comment-form').reset();
        toggleCommentCustomReason(false);
        
        // Disable nút submit lúc mới mở
        document.getElementById('submit-comment-report-btn').setAttribute('disabled', 'disabled');
        
        modal.style.display = 'flex';
        // Kích hoạt animation transition
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    }

    function closeCommentReportModal() {
        const modal = document.getElementById('reportCommentModal');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    function handleCommentOutsideModalClick(event) {
        if (event.target === document.getElementById('reportCommentModal')) {
            closeCommentReportModal();
        }
    }

    function toggleCommentCustomReason(show) {
        const container = document.getElementById('comment-custom-reason-container');
        const textarea = document.getElementById('report-comment-reason-custom');
        if (show) {
            container.style.display = 'block';
            textarea.setAttribute('required', 'required');
            textarea.focus();
        } else {
            container.style.display = 'none';
            textarea.removeAttribute('required');
            textarea.value = '';
        }
        checkCommentReportFormStatus();
    }

    function checkCommentReportFormStatus() {
        const submitBtn = document.getElementById('submit-comment-report-btn');
        const checkedRadio = document.querySelector('input[name="comment_reason_preset"]:checked');
        const customText = document.getElementById('report-comment-reason-custom').value.trim();
        
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

    // Submit báo cáo bình luận qua AJAX Fetch
    const reportCommentForm = document.getElementById('report-comment-form');
    if (reportCommentForm) {
        reportCommentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const commentId = document.getElementById('report-target-comment-id').value;
            const presetReason = document.querySelector('input[name="comment_reason_preset"]:checked');
            if (!presetReason) return;
            
            let reason = presetReason.value;
            if (presetReason.value === 'other') {
                reason = document.getElementById('report-comment-reason-custom').value.trim();
            }
            
            if (!reason) {
                showToast('Vui lòng chọn hoặc nhập lý do báo cáo.', 'error');
                return;
            }
            
            const submitBtn = document.getElementById('submit-comment-report-btn');
            submitBtn.setAttribute('disabled', 'disabled');
            submitBtn.innerText = 'Đang gửi...';

            fetch(`/resident/comments/${commentId}/report`, {
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
                closeCommentReportModal();
                showToast(data.message || 'Báo cáo bình luận thành công!', 'success');
                
                // Ẩn bình luận đối với người báo cáo (hiệu ứng mượt mà)
                const commentEl = document.getElementById(`comment-${commentId}`);
                if (commentEl) {
                    commentEl.classList.add('fade-out-slide-up');
                    setTimeout(() => {
                        commentEl.remove();
                        // Nếu không còn bình luận nào hiển thị, show dòng trống
                        const commentList = document.querySelector('.pc-comments-list');
                        if (commentList && commentList.querySelectorAll('.pc-comment').length === 0) {
                            commentList.innerHTML = '<p style="text-align: center; color: var(--color-text-secondary); font-size: 0.9rem; margin: 2rem 0;">Chưa có bình luận nào. Hãy bắt đầu cuộc trò chuyện!</p>';
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

    // Gắn sự kiện lắng nghe cho comment report preset
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="comment_reason_preset"]').forEach(radio => {
            radio.addEventListener('change', checkCommentReportFormStatus);
        });
        
        const customReasonInput = document.getElementById('report-comment-reason-custom');
        if (customReasonInput) {
            customReasonInput.addEventListener('input', checkCommentReportFormStatus);
        }
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tributejs/5.1.3/tribute.min.js"></script>

{{-- NHÚNG THƯ VIỆN BROADCASTING & LARAVEL ECHO --}}
<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.0/dist/echo.iife.js"></script>
<style>
    @keyframes highlight-new-comment {
        0% { background-color: rgba(59, 130, 246, 0.15); border-left: 3px solid var(--color-primary); }
        100% { background-color: transparent; }
    }
    .new-comment-highlight {
        animation: highlight-new-comment 3s ease-out;
    }
</style>
<script>
    // === Cấu hình Real-time WebSockets cho Bình luận (Laravel Echo) ===
    const currentUserId = {{ auth()->id() }};
    const isPostOwner = {{ $post->user_id === auth()->id() ? 'true' : 'false' }};
    const isAdmin = {{ auth()->user()->isAdminPortalUser() ? 'true' : 'false' }};
    const currentPostId = {{ $post->id }};

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
        let escaped = escapeHtml(str);
        // Format mentions: bôi xanh mỗi phần @
        escaped = escaped.replace(/@([^@\u200B]+)\u200B/gu, '<span class="comment-mention">@$1</span>');
        return escaped.replace(/\n/g, '<br>');
    }

    function createCommentHtml(comment) {
        const avatarUrl = comment.user.avatar 
            ? `/storage/${comment.user.avatar}` 
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&background=00236f&color=fff`;
            
        const apartmentText = comment.user.apartment 
            ? `Căn hộ: ${escapeHtml(comment.user.apartment.apartment_number)}` 
            : 'Ban Quản Trị';
            
        let deleteForm = '';
        if (comment.user.id === currentUserId || isPostOwner || isAdmin) {
            deleteForm = `
                <form action="/resident/comments/${comment.id}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này không?')">
                    <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="pc-comment__action-btn pc-comment__action-btn--delete">
                        Xóa
                    </button>
                </form>
            `;
        }

        let editBtn = '';
        let editForm = '';
        if (comment.user.id === currentUserId) {
            editBtn = `
                <button type="button" class="pc-comment__action-btn" onclick="startEditComment(${comment.id})">
                    Sửa
                </button>
            `;
            editForm = `
                <div class="comment-edit-inline-wrap" id="comment-edit-wrap-${comment.id}" style="display: none; margin: 0.5rem 0;">
                    <textarea class="pc-comments__input" id="comment-edit-input-${comment.id}" rows="2" style="min-height: 45px; resize: vertical;"></textarea>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 0.4rem;">
                        <button type="button" class="pc-btn pc-btn--secondary" style="padding: 0.35rem 0.75rem; font-size: 0.775rem;" onclick="cancelEditComment(${comment.id})">Hủy</button>
                        <button type="button" class="pc-btn" style="padding: 0.35rem 0.75rem; font-size: 0.775rem;" onclick="saveEditComment(${comment.id})">Lưu</button>
                    </div>
                </div>
            `;
        }
        
        let reportBtn = '';
        if (comment.user.id !== currentUserId) {
            reportBtn = `
                <button type="button" class="pc-comment__action-btn pc-comment__action-btn--report" style="color: var(--color-error); margin-left: auto; display: flex; align-items: center; gap: 0.25rem;" onclick="openCommentReportModal(${comment.id})">
                    <i class="fa-regular fa-flag"></i> Báo cáo
                </button>
            `;
        }

        // Reactions logic
        const isLiked = comment.liked || false;
        let activeReaction = comment.reaction_type || null;
        if (isLiked && !activeReaction) activeReaction = 'like';
        
        let dynamicLikeBtnClass = 'pc-comment-like-btn';
        let likeIconHtml = '<i class="fa-regular fa-heart"></i>';
        let likeText = 'Thích';
        
        if (activeReaction) {
            dynamicLikeBtnClass += ' pc-comment-like-btn--active reaction-active-' + activeReaction;
            likeIconHtml = getReactionEmoji(activeReaction);
            likeText = getReactionText(activeReaction);
        }
        const likesCount = comment.likes_count || 0;

        const likeBtnHtml = `
            <div class="pc-like-container">
                <div class="pc-reactions-popup">
                    <span class="pc-reaction-option" data-label="Thích" onclick="toggleLike(event, ${comment.id}, 'comment', 'like')">👍</span>
                    <span class="pc-reaction-option" data-label="Yêu thích" onclick="toggleLike(event, ${comment.id}, 'comment', 'love')">❤️</span>
                    <span class="pc-reaction-option" data-label="Haha" onclick="toggleLike(event, ${comment.id}, 'comment', 'haha')">😆</span>
                    <span class="pc-reaction-option" data-label="Wow" onclick="toggleLike(event, ${comment.id}, 'comment', 'wow')">😮</span>
                    <span class="pc-reaction-option" data-label="Buồn" onclick="toggleLike(event, ${comment.id}, 'comment', 'sad')">😢</span>
                    <span class="pc-reaction-option" data-label="Phẫn nộ" onclick="toggleLike(event, ${comment.id}, 'comment', 'angry')">😡</span>
                </div>
                <button type="button" class="pc-comment__action-btn ${dynamicLikeBtnClass}" onclick="toggleLike(event, ${comment.id}, 'comment')">
                    <span class="reaction-icon-span">${likeIconHtml}</span>
                    <span class="reaction-text-span">${likeText}</span> (<span class="like-count">${likesCount}</span>)
                </button>
            </div>
        `;
        
        const isChild = comment.parent_id !== null;

        // Image attachment logic
        let imageAttachHtml = '';
        if (comment.image_path && 
            comment.content !== '[Bình luận này đã bị xóa bởi Ban quản trị do vi phạm quy chuẩn cộng đồng]' && 
            comment.content !== '[Bình luận này đã bị ẩn tạm thời do nhận nhiều báo cáo vi phạm]') {
            const imageUrl = comment.image_path.startsWith('http') ? comment.image_path : `/storage/${comment.image_path}`;
            imageAttachHtml = `
                <div class="pc-comment__image-attach" id="comment-img-attach-${comment.id}" style="margin-top: 0.5rem; max-width: ${isChild ? '160px' : '200px'}; border-radius: var(--radius-md); overflow: hidden; border: 1px solid #e2e8f0; cursor: zoom-in;" onclick="openLightbox('${imageUrl}')">
                    <img src="${imageUrl}" style="width: 100%; max-height: ${isChild ? '120px' : '150px'}; object-fit: cover; display: block;">
                </div>
            `;
        }

        // Pinned badge logic
        const pinnedBadgeHtml = `
            <span class="pc-comment__pinned-badge" id="pinned-badge-${comment.id}" style="display: ${comment.is_pinned ? 'inline-flex' : 'none'}; align-items: center; gap: 0.25rem; background-color: #fef3c7; color: #d97706; padding: 0.15rem 0.4rem; border-radius: var(--radius-sm); font-size: 0.7rem; font-weight: 700; border: 1px solid rgba(217, 119, 6, 0.15); margin-left: 0.5rem;">
                <i class="fa-solid fa-thumbtack"></i> Đã ghim
            </span>
        `;

        // Pin action button logic (only parent comments)
        let pinBtn = '';
        if (!isChild && (isPostOwner || isAdmin)) {
            pinBtn = `
                <button type="button" class="pc-comment__action-btn pc-comment__action-btn--pin" id="pin-btn-${comment.id}" onclick="togglePinComment(${comment.id})" style="color: #d97706; font-weight: 700;">
                    ${comment.is_pinned ? 'Bỏ ghim' : 'Ghim'}
                </button>
            `;
        }
        
        if (isChild) {
            return `
                <div class="pc-comment pc-comment--reply new-comment-highlight" id="comment-${comment.id}">
                    <img src="${avatarUrl}" alt="Avatar" class="pc-comment__avatar pc-comment__avatar--reply">
                    
                    <div class="pc-comment__body">
                        <div class="pc-comment__header">
                            <div class="pc-comment__user-info">
                                <span class="pc-comment__author-name">${escapeHtml(comment.user.name)}</span>
                                <span class="pc-comment__apartment">${apartmentText}</span>
                            </div>
                            <span class="pc-comment__time" title="${escapeHtml(comment.created_at_human)}">
                                Vừa xong
                            </span>
                        </div>
                        
                        <div class="comment-content-wrap" id="comment-content-wrap-${comment.id}">
                            <p class="pc-comment__content" id="comment-text-${comment.id}">${nl2br(comment.content)}</p>
                            ${imageAttachHtml}
                        </div>
                        ${editForm}
                        
                        <div class="pc-comment__actions" style="display: flex; width: 100%; align-items: center; gap: 1rem;">
                            ${likeBtnHtml}
                            <button type="button" class="pc-comment__action-btn" onclick="replyToComment(${comment.parent_id}, '${escapeHtml(comment.user.name)}')">
                                Trả lời
                            </button>
                            ${editBtn}
                            ${deleteForm}
                            ${reportBtn}
                        </div>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="pc-comment-thread" id="comment-thread-${comment.id}">
                    <div class="pc-comment ${comment.is_pinned ? 'pc-comment--pinned' : ''} new-comment-highlight" id="comment-${comment.id}">
                        <img src="${avatarUrl}" alt="Avatar" class="pc-comment__avatar">
                        
                        <div class="pc-comment__body">
                            <div class="pc-comment__header">
                                <div class="pc-comment__user-info">
                                    <span class="pc-comment__author-name">${escapeHtml(comment.user.name)}</span>
                                    <span class="pc-comment__apartment">${apartmentText}</span>
                                    ${pinnedBadgeHtml}
                                </div>
                                <span class="pc-comment__time" title="${escapeHtml(comment.created_at_human)}">
                                    Vừa xong
                                </span>
                            </div>
                            
                            <div class="comment-content-wrap" id="comment-content-wrap-${comment.id}">
                                <p class="pc-comment__content" id="comment-text-${comment.id}">${nl2br(comment.content)}</p>
                                ${imageAttachHtml}
                            </div>
                            ${editForm}
                            
                            <div class="pc-comment__actions" style="display: flex; width: 100%; align-items: center; gap: 1rem;">
                                ${likeBtnHtml}
                                <button type="button" class="pc-comment__action-btn" onclick="replyToComment(${comment.id}, '${escapeHtml(comment.user.name)}')">
                                    Trả lời
                                </button>
                                ${pinBtn}
                                ${editBtn}
                                ${deleteForm}
                                ${reportBtn}
                            </div>
                        </div>
                    </div>
                    
                    ${comment.replies && comment.replies.length > 0 ? `
                        <div class="pc-comment__replies-toggle-wrapper" id="replies-toggle-wrapper-${comment.id}">
                            <button type="button" class="pc-comment__view-replies-btn" id="view-replies-btn-${comment.id}" onclick="toggleReplies(${comment.id})">
                                <i class="fa-solid fa-share-nodes"></i> Xem <span class="reply-count">${comment.replies.length}</span> câu trả lời
                            </button>
                        </div>
                    ` : ''}
                    
                    <div class="pc-comment-replies" id="replies-container-${comment.id}" style="display: none;">
                        ${comment.replies ? comment.replies.map(reply => createCommentHtml(reply)).join('') : ''}
                    </div>
                </div>
            `;
        }
    }

    function toggleReplies(parentId) {
        const container = document.getElementById(`replies-container-${parentId}`);
        const btn = document.getElementById(`view-replies-btn-${parentId}`);
        const thread = document.getElementById(`comment-thread-${parentId}`);
        
        if (!container || !btn) return;
        
        if (container.style.display === 'none') {
            container.style.display = 'flex';
            if (thread) thread.classList.add('has-replies-expanded');
            
            const countEl = btn.querySelector('.reply-count');
            const count = countEl ? countEl.innerText : '';
            
            btn.innerHTML = `<i class="fa-solid fa-angle-up"></i> Ẩn câu trả lời<span class="reply-count" style="display:none;">${count}</span>`;
        } else {
            container.style.display = 'none';
            if (thread) thread.classList.remove('has-replies-expanded');
            
            const countEl = btn.querySelector('.reply-count');
            const count = countEl ? countEl.innerText : '';
            
            btn.innerHTML = `<i class="fa-solid fa-share-nodes"></i> Xem <span class="reply-count">${count}</span> câu trả lời`;
        }
    }

    function ensureToggleRepliesButton(parentId) {
        let btn = document.getElementById(`view-replies-btn-${parentId}`);
        if (!btn) {
            const parentComment = document.getElementById(`comment-${parentId}`);
            if (parentComment) {
                const wrapper = document.createElement('div');
                wrapper.className = 'pc-comment__replies-toggle-wrapper';
                wrapper.id = `replies-toggle-wrapper-${parentId}`;
                wrapper.innerHTML = `
                    <button type="button" class="pc-comment__view-replies-btn" id="view-replies-btn-${parentId}" onclick="toggleReplies(${parentId})">
                        <i class="fa-solid fa-angle-up"></i> Ẩn câu trả lời<span class="reply-count" style="display:none;">0</span>
                    </button>
                `;
                parentComment.insertAdjacentElement('afterend', wrapper);
            }
        }
    }

    function handleNewComment(comment) {
        const emptyPlaceholder = document.getElementById('empty-comments-placeholder');
        if (emptyPlaceholder) {
            emptyPlaceholder.remove();
        }

        if (document.getElementById(`comment-${comment.id}`)) return;

        const container = document.querySelector('.pc-comments-list');
        if (!container) return;

        const isChild = comment.parent_id !== null;
        if (isChild) {
            const parentId = comment.parent_id;
            const repliesContainer = document.getElementById(`replies-container-${parentId}`);
            if (repliesContainer) {
                const childHtml = createCommentHtml(comment);
                repliesContainer.insertAdjacentHTML('beforeend', childHtml);
                
                ensureToggleRepliesButton(parentId);
                const toggleBtn = document.getElementById(`view-replies-btn-${parentId}`);
                if (toggleBtn) {
                    const countEls = toggleBtn.querySelectorAll('.reply-count');
                    countEls.forEach(el => {
                        const currentCount = parseInt(el.innerText, 10) || 0;
                        el.innerText = currentCount + 1;
                    });
                }
                
                if (repliesContainer.style.display === 'none') {
                    repliesContainer.style.display = 'flex';
                    const thread = document.getElementById(`comment-thread-${parentId}`);
                    if (thread) thread.classList.add('has-replies-expanded');
                    if (toggleBtn) {
                        const countEl = toggleBtn.querySelector('.reply-count');
                        const count = countEl ? countEl.innerText : '';
                        toggleBtn.innerHTML = `<i class="fa-solid fa-angle-up"></i> Ẩn câu trả lời<span class="reply-count" style="display:none;">${count}</span>`;
                    }
                }
            }
        } else {
            const parentHtml = createCommentHtml(comment);
            container.insertAdjacentHTML('beforeend', parentHtml);
        }

        const countEl = document.getElementById('total-comments-count');
        if (countEl) {
            const count = container.querySelectorAll('.pc-comment-thread').length;
            countEl.innerText = count;
        }
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
                    
                    // Cập nhật giao diện tại chi tiết bài viết
                    const titleEl = document.querySelector('.pc-detail__title');
                    if (titleEl) titleEl.innerText = data.post.title;

                    const contentEl = document.querySelector('.pc-detail__content');
                    if (contentEl) contentEl.innerHTML = nl2br(data.post.content);

                    const priceEl = document.querySelector('.pc-card__price');
                    if (priceEl && data.post.price !== null) {
                        priceEl.innerText = formatNumber(data.post.price) + 'đ';
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

    // Chỉnh sửa bình luận Inline
    function startEditComment(id) {
        const textEl = document.getElementById('comment-text-' + id);
        const editWrap = document.getElementById('comment-edit-wrap-' + id);
        const editInput = document.getElementById('comment-edit-input-' + id);
        const contentWrap = document.getElementById('comment-content-wrap-' + id);

        let plainText = textEl.innerHTML.replace(/<br\s*\/?>/gi, '\n');
        const txt = document.createElement("textarea");
        txt.innerHTML = plainText;
        plainText = txt.value;

        editInput.value = plainText;
        contentWrap.style.display = 'none';
        editWrap.style.display = 'block';
        editInput.focus();
    }

    function cancelEditComment(id) {
        const editWrap = document.getElementById('comment-edit-wrap-' + id);
        const contentWrap = document.getElementById('comment-content-wrap-' + id);
        
        editWrap.style.display = 'none';
        contentWrap.style.display = 'block';
    }

    function saveEditComment(id) {
        const editInput = document.getElementById('comment-edit-input-' + id);
        const content = editInput.value.trim();
        if (!content) return;

        const saveBtn = document.querySelector(`#comment-edit-wrap-${id} button:last-child`);
        saveBtn.setAttribute('disabled', 'disabled');
        saveBtn.innerText = 'Đang lưu...';

        fetch(`/resident/comments/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                content: content,
                _method: 'PUT'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const textEl = document.getElementById('comment-text-' + id);
                textEl.innerHTML = nl2br(data.comment.content);
                cancelEditComment(id);
                showToast('Chỉnh sửa bình luận thành công!', 'success');
            }
        })
        .catch(err => {
            console.error('Error saving comment:', err);
            showToast('Không thể lưu bình luận. Vui lòng thử lại!', 'error');
        })
        .finally(() => {
            saveBtn.removeAttribute('disabled');
            saveBtn.innerText = 'Lưu';
        });
    }

    // AJAX load older comments
    function loadOlderComments() {
        const btn = document.getElementById('load-older-btn');
        if (!btn) return;

        const offset = parseInt(btn.getAttribute('data-offset'), 10);
        const total = parseInt(btn.getAttribute('data-total'), 10);
        
        btn.setAttribute('disabled', 'disabled');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tải...';

        fetch(`/resident/posts/${currentPostId}/comments?offset=${offset}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.comments && data.comments.length > 0) {
                const container = document.querySelector('.pc-comments-list');
                const emptyPlaceholder = document.getElementById('empty-comments-placeholder');
                if (emptyPlaceholder) {
                    emptyPlaceholder.remove();
                }

                // Đo chiều cao trước khi prepend
                const oldHeight = container.scrollHeight;

                // Prepend older comments in reverse loop to preserve chronological order
                const loadContainer = document.getElementById('load-older-container');
                for (let i = data.comments.length - 1; i >= 0; i--) {
                    const comment = data.comments[i];
                    if (document.getElementById(`comment-${comment.id}`)) continue;
                    
                    const commentHtml = createCommentHtml(comment);
                    if (loadContainer) {
                        loadContainer.insertAdjacentHTML('afterend', commentHtml);
                    } else {
                        container.insertAdjacentHTML('afterbegin', commentHtml);
                    }
                }

                // Khôi phục scroll position tránh bị nhảy trang
                const newHeight = container.scrollHeight;
                window.scrollBy(0, newHeight - oldHeight);

                const newOffset = offset + data.comments.length;
                btn.setAttribute('data-offset', newOffset);
                
                const remaining = total - newOffset;
                if (remaining <= 0) {
                    document.getElementById('load-older-container').remove();
                } else {
                    btn.removeAttribute('disabled');
                    btn.innerHTML = `<i class="fa-solid fa-clock-rotate-left"></i> Xem thêm bình luận cũ hơn (<span id="older-count">${remaining}</span>)`;
                }
            } else {
                const loadContainer = document.getElementById('load-older-container');
                if (loadContainer) loadContainer.remove();
            }
        })
        .catch(err => {
            console.error('Error loading comments:', err);
            btn.removeAttribute('disabled');
            btn.innerHTML = `<i class="fa-solid fa-clock-rotate-left"></i> Xem thêm bình luận cũ hơn`;
            showToast('Không thể tải bình luận cũ. Vui lòng thử lại!', 'error');
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        if (typeof window.Pusher === 'undefined' && typeof Pusher !== 'undefined') {
            window.Pusher = Pusher;
        }

        // AJAX comment form submission
        const commentForm = document.getElementById('comment-form');
        if (commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const textarea = document.getElementById('comment-content');
                const content = textarea.value.trim();
                if (!content) return;
                
                const parentIdInput = document.getElementById('comment-parent-id');
                const parentId = parentIdInput.value;
                
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.setAttribute('disabled', 'disabled');
                submitBtn.innerText = 'Đang gửi...';
                
                const formData = new FormData(this);
                if (!parentId) {
                    formData.delete('parent_id');
                } else {
                    formData.set('parent_id', parentId);
                }

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Có lỗi xảy ra khi gửi bình luận.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        textarea.value = '';
                        removeCommentImage();
                        cancelReply();
                        
                        if (!document.getElementById(`comment-${data.comment.id}`)) {
                            handleNewComment(data.comment);
                        }
                    }
                })
                .catch(error => {
                    console.error(error);
                    showToast('Không thể gửi bình luận. Vui lòng thử lại!', 'error');
                })
                .finally(() => {
                    submitBtn.removeAttribute('disabled');
                    submitBtn.innerText = 'Gửi bình luận';
                });
            });
        }

        // Khởi tạo Tribute.js gợi ý tag tên cư dân khi gõ @
        fetch('/resident/search-members')
            .then(res => res.json())
            .then(users => {
                if (typeof Tribute !== 'undefined') {
                    const tribute = new Tribute({
                        values: users,
                        selectTemplate: function (item) {
                            return item.original.value + '\u200B';
                        },
                        menuItemTemplate: function (item) {
                            return `
                                <div style="display:flex; align-items:center; gap:0.5rem; padding:0.25rem 0.5rem;">
                                    <img src="${item.original.avatar}" style="width:28px; height:28px; border-radius:50%; object-fit:cover; border: 1px solid #e2e8f0;">
                                    <span>${item.original.key}</span>
                                </div>
                            `;
                        }
                    });
                    const commentContent = document.getElementById('comment-content');
                    if (commentContent) {
                        tribute.attach(commentContent);
                    }
                }
            })
            .catch(err => console.error("Error loading members for Tribute:", err));

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
            console.log(`DomusHub WebSockets: Subscribing to 'post.${currentPostId}' channel...`);
            window.Echo.channel(`post.${currentPostId}`)
                .listen('CommentCreated', (e) => {
                    console.log("DomusHub WebSockets: Received Event CommentCreated:", e);
                    handleNewComment(e);
                    showToast(`Cư dân ${e.user.name} vừa bình luận bài viết!`, 'success');
                })
                .listen('PostUpdated', (e) => {
                    console.log("DomusHub WebSockets: Received Event PostUpdated (Detail):", e);
                    const titleEl = document.querySelector('.pc-detail__title');
                    if (titleEl) titleEl.innerText = e.title;

                    const contentEl = document.querySelector('.pc-detail__content');
                    if (contentEl) contentEl.innerHTML = nl2br(e.content);

                    const priceEl = document.querySelector('.pc-card__price');
                    if (priceEl && e.price !== null) {
                        priceEl.innerText = formatNumber(e.price) + 'đ';
                    }
                    
                    showToast('Nội dung bài viết vừa được cập nhật!', 'success');
                })
                .listen('CommentUpdated', (e) => {
                    console.log("DomusHub WebSockets: Received Event CommentUpdated:", e);
                    const textEl = document.getElementById('comment-text-' + e.id);
                    if (textEl) {
                        textEl.innerHTML = nl2br(e.content);
                        const commentCard = document.getElementById('comment-' + e.id);
                        if (commentCard) {
                            commentCard.classList.add('new-comment-highlight');
                            setTimeout(() => commentCard.classList.remove('new-comment-highlight'), 3000);
                        }
                    }

                    // Đồng bộ trạng thái ghim bình luận thời gian thực qua Echo
                    if (e.is_pinned !== undefined) {
                        const commentCard = document.getElementById('comment-' + e.id);
                        if (commentCard) {
                            if (e.is_pinned) {
                                document.querySelectorAll('.pc-comment--pinned').forEach(el => {
                                    el.classList.remove('pc-comment--pinned');
                                    const badge = el.querySelector('.pc-comment__pinned-badge');
                                    if (badge) badge.style.display = 'none';
                                    const pBtn = el.querySelector('.pc-comment__action-btn--pin');
                                    if (pBtn) pBtn.innerText = 'Ghim';
                                });
                                
                                commentCard.classList.add('pc-comment--pinned');
                                const badge = document.getElementById('pinned-badge-' + e.id);
                                if (badge) badge.style.display = 'inline-flex';
                                const pBtn = document.getElementById('pin-btn-' + e.id);
                                if (pBtn) pBtn.innerText = 'Bỏ ghim';
                            } else {
                                commentCard.classList.remove('pc-comment--pinned');
                                const badge = document.getElementById('pinned-badge-' + e.id);
                                if (badge) badge.style.display = 'none';
                                const pBtn = document.getElementById('pin-btn-' + e.id);
                                if (pBtn) pBtn.innerText = 'Ghim';
                            }
                        }
                    }
                })
                .listen('LikeToggled', (e) => {
                    console.log("DomusHub WebSockets: Received Event LikeToggled (Detail):", e);
                    if (e.likeable_type === 'post' && e.likeable_id == currentPostId) {
                        const postLikeCountEl = document.querySelector('.pc-like-btn .like-count');
                        if (postLikeCountEl) {
                            postLikeCountEl.innerText = e.likes_count;
                        }
                    } else if (e.likeable_type === 'comment') {
                        const commentEl = document.getElementById('comment-' + e.likeable_id);
                        if (commentEl) {
                            const commentLikeCountEl = commentEl.querySelector('.pc-comment-like-btn .like-count');
                            if (commentLikeCountEl) {
                                commentLikeCountEl.innerText = e.likes_count;
                            }
                        }
                    }
                });
        } else {
            console.error("DomusHub WebSockets: Failed to initialize Echo. Please check libraries.");
        }
    });

    // Helper functions cho ảnh xem trước bình luận
    function previewCommentImage(event) {
        const input = event.target;
        const previewWrap = document.getElementById('comment-image-preview-wrap');
        const previewImg = document.getElementById('comment-image-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewWrap.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removeCommentImage() {
        const input = document.getElementById('comment-image-input');
        const previewWrap = document.getElementById('comment-image-preview-wrap');
        const previewImg = document.getElementById('comment-image-preview');
        
        if (input) input.value = '';
        if (previewImg) previewImg.src = '';
        if (previewWrap) previewWrap.style.display = 'none';
    }

    // Toggle Ghim bình luận
    function togglePinComment(commentId) {
        const pinBtn = document.getElementById('pin-btn-' + commentId);
        if (pinBtn) pinBtn.setAttribute('disabled', 'disabled');
        
        fetch(`/resident/comments/${commentId}/pin`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                const commentCard = document.getElementById('comment-' + commentId);
                if (commentCard) {
                    if (data.is_pinned) {
                        document.querySelectorAll('.pc-comment--pinned').forEach(el => {
                            el.classList.remove('pc-comment--pinned');
                            const badge = el.querySelector('.pc-comment__pinned-badge');
                            if (badge) badge.style.display = 'none';
                            const pBtn = el.querySelector('.pc-comment__action-btn--pin');
                            if (pBtn) pBtn.innerText = 'Ghim';
                        });
                        
                        commentCard.classList.add('pc-comment--pinned');
                        const badge = document.getElementById('pinned-badge-' + commentId);
                        if (badge) badge.style.display = 'inline-flex';
                        if (pinBtn) pinBtn.innerText = 'Bỏ ghim';
                    } else {
                        commentCard.classList.remove('pc-comment--pinned');
                        const badge = document.getElementById('pinned-badge-' + commentId);
                        if (badge) badge.style.display = 'none';
                        if (pinBtn) pinBtn.innerText = 'Ghim';
                    }
                }
            } else {
                showToast(data.message || 'Không thể thực hiện.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Có lỗi xảy ra. Vui lòng thử lại!', 'error');
        })
        .finally(() => {
            if (pinBtn) pinBtn.removeAttribute('disabled');
        });
    }

    // Script xử lý Modal Cảm xúc cư dân
    let currentReactions = [];

    function openReactionsListModal(likeableId, likeableType) {
        const modal = document.getElementById('reactionsListModal');
        const container = document.getElementById('reactions-list-container');
        
        container.innerHTML = '<div style="text-align: center; color: var(--color-outline); padding: 1rem;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>';
        
        document.querySelectorAll('.react-tab').forEach(tab => {
            tab.classList.remove('react-tab--active');
            tab.style.borderBottom = 'none';
            tab.style.color = 'var(--color-text-secondary)';
            tab.style.display = 'none';
        });
        
        const allTab = document.querySelector('.react-tab[data-type="all"]');
        allTab.classList.add('react-tab--active');
        allTab.style.borderBottom = '3px solid var(--color-primary)';
        allTab.style.color = 'var(--color-primary)';
        allTab.style.display = 'inline-block';

        modal.style.display = 'flex';
        
        fetch(`/resident/reactions/${likeableType}/${likeableId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    currentReactions = data.reactions;
                    populateReactionsList('all');
                    updateReactionsTabsSummary();
                } else {
                    container.innerHTML = '<div style="text-align: center; color: var(--color-error); padding: 1rem;">Không thể tải danh sách.</div>';
                }
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = '<div style="text-align: center; color: var(--color-error); padding: 1rem;">Có lỗi xảy ra khi tải dữ liệu.</div>';
            });
    }

    function populateReactionsList(type) {
        const container = document.getElementById('reactions-list-container');
        container.innerHTML = '';
        
        const filtered = type === 'all' 
            ? currentReactions 
            : currentReactions.filter(r => r.type === type);
            
        if (filtered.length === 0) {
            container.innerHTML = '<div style="text-align: center; color: var(--color-outline); padding: 1.5rem 0; font-size: 0.9rem;">Chưa có cảm xúc nào loại này.</div>';
            return;
        }
        
        filtered.forEach(r => {
            const apartmentNo = r.apartment ? `Căn hộ: ${r.apartment.apartment_number}` : 'Ban Quản Trị';
            const emoji = getReactionEmoji(r.type);
            
            const row = document.createElement('div');
            row.style.cssText = `
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.75rem;
                padding-bottom: 0.75rem;
                border-bottom: 1px solid #f1f5f9;
            `;
            
            row.innerHTML = `
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <img src="${r.avatar}" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0;">
                    <div style="display: flex; flex-direction: column; gap: 0.15rem;">
                        <span style="font-weight: 700; font-size: 0.9rem; color: var(--color-text);">${escapeHtml(r.name)}</span>
                        <span style="font-size: 0.75rem; color: var(--color-outline); font-weight: 500;">${apartmentNo}</span>
                    </div>
                </div>
                <span style="font-size: 1.25rem;">${emoji}</span>
            `;
            container.appendChild(row);
        });
    }

    function updateReactionsTabsSummary() {
        const counts = {};
        currentReactions.forEach(r => {
            counts[r.type] = (counts[r.type] || 0) + 1;
        });
        
        Object.keys(counts).forEach(type => {
            const tab = document.querySelector(`.react-tab[data-type="${type}"]`);
            if (tab) {
                tab.style.display = 'inline-block';
                const countEl = tab.querySelector('.react-count-tab');
                if (countEl) countEl.innerText = counts[type];
            }
        });
    }

    function filterReactions(type) {
        document.querySelectorAll('.react-tab').forEach(tab => {
            tab.classList.remove('react-tab--active');
            tab.style.borderBottom = 'none';
            tab.style.color = 'var(--color-text-secondary)';
        });
        
        const tab = document.querySelector(`.react-tab[data-type="${type}"]`);
        if (tab) {
            tab.classList.add('react-tab--active');
            tab.style.borderBottom = '3px solid var(--color-primary)';
            tab.style.color = 'var(--color-primary)';
        }
        
        populateReactionsList(type);
    }

    function closeReactionsModal() {
        const modal = document.getElementById('reactionsListModal');
        modal.style.display = 'none';
    }

    function handleReactionsOutsideClick(event) {
        if (event.target === document.getElementById('reactionsListModal')) {
            closeReactionsModal();
        }
    }
    
    // Intercept click on .like-count to open reactions modal
    const originalToggleLike = toggleLike;
    toggleLike = function(event, id, type, reactionType = null) {
        if (event.target.classList.contains('like-count')) {
            event.preventDefault();
            event.stopPropagation();
            openReactionsListModal(id, type);
            return;
        }
        originalToggleLike(event, id, type, reactionType);
    };
</script>
@endsection
