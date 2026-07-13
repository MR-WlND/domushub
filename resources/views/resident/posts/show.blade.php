@extends('layouts.resident.master')

@section('title', $post->title . ' – Bảng tin DomusHub')

@push('styles')
    @vite(['resources/css/resident/posts.css', 'resources/css/pages/resident/home/index.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tributejs/5.1.3/tribute.css">
    <style>
        /* === Facebook-style Comment Bubble === */
        .fb-comments-sec {
            background: var(--color-card);
            border: 1px solid var(--color-outline-soft);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-top: 1.5rem;
            box-shadow: var(--color-shadow);
        }
        .fb-comments__title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--color-text);
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }
        /* Comment form */
        .fb-comment-form {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }
        .fb-comment-form__avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }
        .fb-comment-form__body {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .fb-comment-form__input {
            width: 100%;
            min-height: 40px;
            max-height: 120px;
            padding: 0.6rem 0.85rem;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            background: #f0f2f5;
            font-size: 0.875rem;
            resize: none;
            outline: none;
            transition: border-color 0.2s, background 0.2s;
            font-family: inherit;
        }
        .fb-comment-form__input:focus {
            border-color: var(--color-primary);
            background: #fff;
        }
        .fb-comment-form__actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        /* Reply indicator */
        .fb-reply-indicator {
            display: none;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
            color: var(--color-primary);
            background: #eef2ff;
            padding: 0.35rem 0.75rem;
            border-radius: 12px;
            font-weight: 600;
        }
        .fb-reply-indicator__close {
            background: none;
            border: none;
            font-size: 1.1rem;
            cursor: pointer;
            color: var(--color-outline);
            line-height: 1;
        }
        /* Comments list */
        .fb-comments-list {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        /* Single comment thread */
        .fb-comment-thread {
            display: flex;
            flex-direction: column;
        }
        /* Single comment */
        .fb-comment {
            display: flex;
            gap: 0.6rem;
            align-items: flex-start;
            padding: 0.35rem 0;
        }
        .fb-comment__avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }
        .fb-comment__avatar--reply {
            width: 28px;
            height: 28px;
        }
        .fb-comment__main {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            max-width: 85%;
        }
        /* The grey bubble */
        .fb-comment__bubble {
            background: #f0f2f5;
            border-radius: 18px;
            padding: 0.55rem 0.85rem;
            position: relative;
        }
        .fb-comment--pinned .fb-comment__bubble {
            background: #fffbeb;
            border: 1px solid rgba(217, 119, 6, 0.2);
        }
        .fb-comment__name {
            font-weight: 700;
            font-size: 0.825rem;
            color: var(--color-text);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .fb-comment__role {
            font-size: 0.65rem;
            color: var(--color-outline);
            font-weight: 500;
            background: #e2e8f0;
            padding: 0.1rem 0.4rem;
            border-radius: 8px;
        }
        .fb-comment__pinned-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            background: #fef3c7;
            color: #d97706;
            padding: 0.1rem 0.4rem;
            border-radius: 8px;
            font-size: 0.65rem;
            font-weight: 700;
        }
        .fb-comment__text {
            font-size: 0.875rem;
            color: var(--color-text-secondary);
            line-height: 1.45;
            margin: 0.1rem 0 0;
            word-break: break-word;
        }
        .fb-comment__image {
            margin-top: 0.4rem;
            max-width: 200px;
            border-radius: 12px;
            overflow: hidden;
            cursor: zoom-in;
        }
        .fb-comment__image img {
            width: 100%;
            max-height: 150px;
            object-fit: cover;
            display: block;
        }
        /* Actions row under bubble */
        .fb-comment__meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-left: 0.85rem;
            margin-top: 0.15rem;
        }
        .fb-comment__time {
            font-size: 0.725rem;
            color: var(--color-outline);
            font-weight: 500;
        }
        .fb-comment__action-link {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--color-outline);
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            font-family: inherit;
            transition: color 0.15s;
        }
        .fb-comment__action-link:hover {
            color: var(--color-primary);
            text-decoration: underline;
        }
        .fb-comment__action-link--like {
            color: var(--color-outline);
        }
        .fb-comment__action-link--liked {
            color: var(--color-primary) !important;
        }
        .fb-comment__action-link--delete {
            color: var(--color-error);
        }
        .fb-comment__action-link--delete:hover {
            color: #991b1b;
        }
        .fb-comment__action-link--pin {
            color: #d97706;
        }
        .fb-comment__like-count {
            font-size: 0.7rem;
            color: var(--color-outline);
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.1rem 0.4rem;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        /* Replies container */
        .fb-comment-replies {
            display: none;
            flex-direction: column;
            gap: 0.2rem;
            margin-left: 2.75rem;
            padding-left: 0.75rem;
            border-left: 2px solid #e2e8f0;
        }
        .fb-comment-replies--visible {
            display: flex;
        }
        .fb-view-replies-btn {
            background: none;
            border: none;
            color: var(--color-primary);
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            padding: 0.3rem 0;
            margin-left: 3.5rem;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .fb-view-replies-btn:hover {
            text-decoration: underline;
        }
        /* Edit inline */
        .fb-comment-edit-wrap {
            display: none;
            margin-top: 0.4rem;
        }
        .fb-comment-edit-wrap textarea {
            width: 100%;
            min-height: 38px;
            padding: 0.5rem 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            background: #fff;
            font-size: 0.85rem;
            resize: none;
            outline: none;
            font-family: inherit;
        }
        .fb-comment-edit-wrap textarea:focus {
            border-color: var(--color-primary);
        }
        .fb-comment-edit-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.4rem;
            margin-top: 0.35rem;
        }
        /* Load older */
        .fb-load-older {
            text-align: center;
            margin-bottom: 1rem;
        }
        .fb-load-older__btn {
            background: none;
            border: none;
            color: var(--color-primary);
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .fb-load-older__btn:hover {
            text-decoration: underline;
        }
        /* Reactions popup on comment */
        .fb-reactions-popup {
            position: absolute;
            bottom: 100%;
            left: 0;
            display: none;
            background: #fff;
            border-radius: 24px;
            padding: 0.35rem 0.5rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
            gap: 0.15rem;
            z-index: 100;
        }
        .fb-comment__action-link--like:hover .fb-reactions-popup,
        .fb-like-wrapper:hover .fb-reactions-popup {
            display: flex;
        }
        .fb-like-wrapper {
            position: relative;
            display: inline-flex;
        }
        .fb-reaction-option {
            font-size: 1.3rem;
            cursor: pointer;
            transition: transform 0.15s;
            padding: 0.15rem 0.2rem;
        }
        .fb-reaction-option:hover {
            transform: scale(1.35);
        }
        /* Mention tag */
        .comment-mention {
            color: var(--color-primary);
            font-weight: 600;
            background: rgba(0, 35, 111, 0.06);
            padding: 0.05rem 0.25rem;
            border-radius: 4px;
        }
        /* New comment animation */
        @keyframes fb-comment-in {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fb-comment--new {
            animation: fb-comment-in 0.3s ease-out;
        }
        /* Image preview for form */
        .fb-img-preview {
            display: none;
            position: relative;
            width: 70px;
            height: 70px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .fb-img-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .fb-img-preview__remove {
            position: absolute;
            top: 3px;
            right: 3px;
            width: 18px;
            height: 18px;
            background: rgba(0,0,0,0.7);
            color: #fff;
            border: none;
            border-radius: 50%;
            font-size: 0.6rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
@endpush

@section('content')
@php
    if (!function_exists('cleanPostContentBlade')) {
        function cleanPostContentBlade($content) {
            $allowed = '<p><a><strong><em><u><ul><ol><li><br><blockquote><sub><sup>';
            $stripped = strip_tags($content, $allowed);
            $stripped = preg_replace('/on\w+\s*=\s*(["\'])(.*?)\1/is', '', $stripped);
            $stripped = preg_replace('/on\w+\s*=\s*([^\s>]+)/is', '', $stripped);
            $stripped = preg_replace('/href\s*=\s*(["\'])\s*javascript:(.*?)\1/is', '', $stripped);
            return $stripped;
        }
    }

    if (!function_exists('formatCommentContentBlade')) {
        function formatCommentContentBlade($content) {
            $escaped = e($content);
            $escaped = preg_replace('/@([^@\x{200B}]+)\x{200B}/u', '<span class="comment-mention">@$1</span>', $escaped);
            return nl2br($escaped);
        }
    }
@endphp
<div class="rh" style="max-width:720px;">
<div class="rh-fb-feed" style="max-width: 100%;">

    {{-- NÚT QUAY LẠI --}}
    <a href="{{ route('resident.dashboard') }}" style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; font-weight: 600; color: #2563eb; text-decoration: none; margin-bottom: 0.5rem;">
        <i class="fa-solid fa-arrow-left"></i> Quay lại bảng tin
    </a>

    {{-- THÔNG BÁO --}}
    @if(session('success'))
        <div style="padding: 12px 18px; background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 10px; font-size: 0.85rem; font-weight: 600;">{{ session('success') }}</div>
    @endif

    {{-- BÀI VIẾT DẠNG FB CARD --}}
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
                <span class="rh-fb-card__name">{{ $post->user->name }}</span>
                <span class="rh-fb-card__time">{{ $post->created_at->diffForHumans() }} · {{ $post->user->apartment ? 'Cư dân' : 'Ban Quản Trị' }}</span>
            </div>
        </div>

        {{-- Title --}}
        @if($post->title)
            <div style="padding: 8px 16px 0; font-weight: 700; font-size: 1.1rem; color: #050505; display: none;" class="pc-detail__title">{{ $post->title }}</div>
        @endif

        {{-- Content --}}
        <div class="rh-fb-card__text pc-detail__content post-text-content" id="post-content-{{ $post->id }}" style="padding: 8px 16px 0;">
            {!! cleanPostContentBlade($post->content) !!}
        </div>

        {{-- Badge giá / loại --}}
        @if($post->price !== null)
            <div style="padding: 8px 16px 0; display: flex; gap: 0.5rem; align-items: center;">
                <span style="background: #dbeafe; color: #1d4ed8; padding: 2px 10px; border-radius: 6px; font-size: 0.72rem; font-weight: 700;">Thanh lý</span>
                <span class="pc-card__price" style="font-weight: 700; color: #dc2626; font-size: 0.95rem;">{{ number_format($post->price, 0, ',', '.') }}đ</span>
            </div>
        @endif

        {{-- Media đính kèm (FB-style grid) --}}
        @if($post->images->isNotEmpty())
            @php $imgCount = $post->images->count(); @endphp
            <div class="rh-fb-card__media" style="margin-top: 12px; cursor: pointer;">
                @if($imgCount === 1)
                    @if(($post->images[0]->type ?? 'image') === 'video')
                        <video src="{{ asset('storage/' . $post->images[0]->image_path) }}" class="rh-fb-card__img-single" controls preload="metadata" style="max-height:500px;width:100%;border-radius:0;"></video>
                    @else
                        <img src="{{ asset('storage/' . $post->images[0]->image_path) }}" class="rh-fb-card__img-single" alt="" loading="lazy" onclick="openLightbox('{{ asset('storage/' . $post->images[0]->image_path) }}')">
                    @endif
                @elseif($imgCount === 2)
                    <div class="rh-fb-card__img-grid rh-fb-card__img-grid--2">
                        @foreach($post->images->take(2) as $img)
                            @if(($img->type ?? 'image') === 'video')
                                <video src="{{ asset('storage/' . $img->image_path) }}" controls preload="metadata" style="width:100%;height:300px;object-fit:cover;"></video>
                            @else
                                <img src="{{ asset('storage/' . $img->image_path) }}" alt="" loading="lazy" onclick="openLightbox('{{ asset('storage/' . $img->image_path) }}')">
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="rh-fb-card__img-grid rh-fb-card__img-grid--3plus">
                        @if(($post->images[0]->type ?? 'image') === 'video')
                            <video src="{{ asset('storage/' . $post->images[0]->image_path) }}" class="rh-fb-card__img-main" controls preload="metadata" style="height:320px;width:100%;object-fit:cover;"></video>
                        @else
                            <img src="{{ asset('storage/' . $post->images[0]->image_path) }}" class="rh-fb-card__img-main" alt="" loading="lazy" onclick="openLightbox('{{ asset('storage/' . $post->images[0]->image_path) }}')">
                        @endif
                        <div class="rh-fb-card__img-side">
                            @foreach($post->images->slice(1, 2) as $img)
                                @if(($img->type ?? 'image') === 'video')
                                    <video src="{{ asset('storage/' . $img->image_path) }}" controls preload="metadata" style="width:100%;height:159px;object-fit:cover;"></video>
                                @else
                                    <img src="{{ asset('storage/' . $img->image_path) }}" alt="" loading="lazy" onclick="openLightbox('{{ asset('storage/' . $img->image_path) }}')">
                                @endif
                            @endforeach
                            @if($imgCount > 3)
                                <div class="rh-fb-card__img-overlay">+{{ $imgCount - 3 }}</div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Stats row --}}
        <div class="rh-fb-card__stats">
            <span><span class="like-count">{{ $post->likes_count }}</span> lượt thích</span>
            <span>{{ $totalComments }} bình luận</span>
        </div>

        {{-- Action bar (giống index) --}}
        <div class="rh-fb-card__actions">
            {{-- Thích --}}
            <div class="pc-like-container" style="display: flex; align-items: center; justify-content: center;">
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
                @endphp

                <button type="button" class="rh-fb-card__action pc-like-btn {{ $activeReaction ? 'rh-fb-like-btn--active' : '' }}" onclick="toggleLike(event, {{ $post->id }}, 'post')" style="width:100%;">
                    <svg class="like-icon-svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                    <span class="reaction-icon-span">{{ $activeReaction ? match($activeReaction) { 'love' => '❤️', 'haha' => '😆', 'wow' => '😮', 'sad' => '😢', 'angry' => '😡', default => '' } : '' }}</span>
                    <span class="reaction-text-span">{{ $activeReaction ? match($activeReaction) { 'love' => 'Yêu thích', 'haha' => 'Haha', 'wow' => 'Wow', 'sad' => 'Buồn', 'angry' => 'Phẫn nộ', default => 'Thích' } : 'Thích' }}</span>
                </button>
            </div>

            {{-- Bình luận --}}
            <button type="button" class="rh-fb-card__action" onclick="document.getElementById('comment-content').focus()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Bình luận
            </button>

            {{-- Chia sẻ --}}
            <button type="button" class="rh-fb-card__action rh-fb-share-btn" data-url="{{ route('resident.posts.show', $post->id) }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                Chia sẻ
            </button>
        </div>

        {{-- Owner actions (sửa, xóa, báo cáo) --}}
        <div style="display: flex; justify-content: flex-end; gap: 0.5rem; padding: 8px 16px 12px; align-items: center;">
            @if($post->user_id === auth()->id())
                <button type="button" onclick="openEditPostModal({{ $post->id }}, '{{ addslashes($post->title) }}', '{{ addslashes($post->content) }}', '{{ $post->price }}')" style="color: #2563eb; background: transparent; border: none; cursor: pointer; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem; font-family: inherit;">
                    <i class="fa-regular fa-pen-to-square"></i> Sửa
                </button>
            @endif

            @if($post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
                <form action="{{ route('resident.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Xóa bài đăng này?')" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color: #dc2626; background: transparent; border: none; cursor: pointer; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem; font-family: inherit;">
                        <i class="fa-regular fa-trash-can"></i> Xóa
                    </button>
                </form>
            @elseif($post->user_id !== auth()->id())
                <button type="button" onclick="openReportModal({{ $post->id }})" style="color: #dc2626; background: transparent; border: none; cursor: pointer; font-size: 0.8rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.3rem; font-family: inherit;">
                    <i class="fa-regular fa-flag"></i> Báo cáo
                </button>
            @endif
        </div>
    </div>

    {{-- KHU VỰC BÌNH LUẬN KIỂU FACEBOOK --}}
    <section class="fb-comments-sec" style="border-radius: 10px;">
        <h3 class="fb-comments__title">
            <i class="fa-regular fa-comments"></i> Bình luận (<span id="total-comments-count">{{ $totalComments }}</span>)
        </h3>

        {{-- FORM VIẾT BÌNH LUẬN --}}
        <div class="fb-comment-form">
            <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=00236f&color=fff' }}" 
                 alt="Avatar" class="fb-comment-form__avatar">
            
            <div class="fb-comment-form__body">
                <form action="{{ route('resident.posts.comments.store', $post->id) }}" method="POST" id="comment-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="parent_id" id="comment-parent-id" value="">

                    <div class="fb-reply-indicator" id="reply-indicator">
                        <span><i class="fa-solid fa-reply"></i> Đang trả lời <strong id="reply-target-name"></strong></span>
                        <button type="button" class="fb-reply-indicator__close" onclick="cancelReply()">&times;</button>
                    </div>

                    <textarea name="content" id="comment-content" class="fb-comment-form__input" placeholder="Viết bình luận..." required></textarea>
                    
                    <div class="fb-img-preview" id="comment-image-preview-wrap">
                        <img id="comment-image-preview" src="">
                        <button type="button" class="fb-img-preview__remove" onclick="removeCommentImage()">&times;</button>
                    </div>

                    <div class="fb-comment-form__actions">
                        <label style="cursor: pointer; display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.8rem; color: #64748b;">
                            <i class="fa-regular fa-image" style="color: #2563eb; font-size: 1rem;"></i> Ảnh
                            <input type="file" name="image" id="comment-image-input" accept="image/*" style="display: none;" onchange="previewCommentImage(event)">
                        </label>
                        <button type="submit" class="pc-btn" style="padding: 0.4rem 1rem; font-size: 0.8rem; border-radius: 16px;">Gửi</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- DANH SÁCH BÌNH LUẬN --}}
        <div class="fb-comments-list">
            @if($totalComments > 10)
                <div class="fb-load-older" id="load-older-container">
                    <button type="button" class="fb-load-older__btn" id="load-older-btn" data-offset="10" data-total="{{ $totalComments }}" onclick="loadOlderComments()">
                        <i class="fa-solid fa-clock-rotate-left"></i> Xem thêm bình luận cũ hơn (<span id="older-count">{{ $totalComments - 10 }}</span>)
                    </button>
                </div>
            @endif

            @if($comments->isEmpty())
                <p id="empty-comments-placeholder" style="text-align: center; color: var(--color-text-secondary); font-size: 0.9rem; margin: 2rem 0;">Chưa có bình luận nào. Hãy bắt đầu cuộc trò chuyện!</p>
            @else
                @foreach($comments as $comment)
                    <div class="fb-comment-thread" id="comment-thread-{{ $comment->id }}">
                        {{-- BÌNH LUẬN CHA --}}
                        <div class="fb-comment {{ $comment->is_pinned ? 'fb-comment--pinned' : '' }}" id="comment-{{ $comment->id }}">
                            <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=00236f&color=fff' }}" 
                                 alt="Avatar" class="fb-comment__avatar">
                            
                            <div class="fb-comment__main">
                                <div class="fb-comment__bubble">
                                    <span class="fb-comment__name">
                                        {{ $comment->user->name }}
                                        <span class="fb-comment__role">{{ $comment->user->apartment ? 'Cư dân' : 'BQT' }}</span>
                                        @if($comment->is_pinned)
                                            <span class="fb-comment__pinned-badge" id="pinned-badge-{{ $comment->id }}"><i class="fa-solid fa-thumbtack"></i> Ghim</span>
                                        @else
                                            <span class="fb-comment__pinned-badge" id="pinned-badge-{{ $comment->id }}" style="display:none;"><i class="fa-solid fa-thumbtack"></i> Ghim</span>
                                        @endif
                                    </span>
                                    <div class="comment-content-wrap" id="comment-content-wrap-{{ $comment->id }}">
                                        <p class="fb-comment__text" id="comment-text-{{ $comment->id }}">{!! formatCommentContentBlade($comment->content) !!}</p>
                                    </div>
                                </div>

                                {{-- Ảnh đính kèm bình luận --}}
                                @if($comment->image_path && $comment->content !== '[Bình luận này đã bị xóa bởi Ban quản trị do vi phạm quy chuẩn cộng đồng]' && $comment->content !== '[Bình luận này đã bị ẩn tạm thời do nhận nhiều báo cáo vi phạm]')
                                    <div class="fb-comment__image" id="comment-img-attach-{{ $comment->id }}" onclick="openLightbox('{{ asset('storage/' . $comment->image_path) }}')">
                                        <img src="{{ asset('storage/' . $comment->image_path) }}" alt="Ảnh bình luận">
                                    </div>
                                @endif

                                {{-- Form chỉnh sửa inline --}}
                                @if($comment->user_id === auth()->id())
                                    <div class="fb-comment-edit-wrap" id="comment-edit-wrap-{{ $comment->id }}">
                                        <textarea id="comment-edit-input-{{ $comment->id }}" rows="2"></textarea>
                                        <div class="fb-comment-edit-actions">
                                            <button type="button" class="pc-btn pc-btn--secondary" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; border-radius: 12px;" onclick="cancelEditComment({{ $comment->id }})">Hủy</button>
                                            <button type="button" class="pc-btn" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; border-radius: 12px;" onclick="saveEditComment({{ $comment->id }})">Lưu</button>
                                        </div>
                                    </div>
                                @endif

                                {{-- Meta: thời gian + hành động --}}
                                <div class="fb-comment__meta">
                                    <span class="fb-comment__time" title="{{ $comment->created_at }}">{{ $comment->created_at->diffForHumans() }}</span>

                                    {{-- Like --}}
                                    <div class="fb-like-wrapper">
                                        <div class="fb-reactions-popup">
                                            <span class="fb-reaction-option" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'like')">👍</span>
                                            <span class="fb-reaction-option" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'love')">❤️</span>
                                            <span class="fb-reaction-option" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'haha')">😆</span>
                                            <span class="fb-reaction-option" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'wow')">😮</span>
                                            <span class="fb-reaction-option" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'sad')">😢</span>
                                            <span class="fb-reaction-option" onclick="toggleLike(event, {{ $comment->id }}, 'comment', 'angry')">😡</span>
                                        </div>
                                        @php
                                            $cLike = $comment->likedByCurrentUser->first();
                                            $cReaction = $cLike ? $cLike->type : null;
                                        @endphp
                                        <button type="button" class="fb-comment__action-link fb-comment__action-link--like {{ $cReaction ? 'fb-comment__action-link--liked reaction-active-'.$cReaction : '' }}" onclick="toggleLike(event, {{ $comment->id }}, 'comment')">
                                            <span class="reaction-text-span">{{ $cReaction ? match($cReaction) { 'love' => 'Yêu thích', 'haha' => 'Haha', 'wow' => 'Wow', 'sad' => 'Buồn', 'angry' => 'Phẫn nộ', default => 'Thích' } : 'Thích' }}</span>
                                        </button>
                                    </div>

                                    @if($comment->likes_count > 0)
                                        <span class="fb-comment__like-count"><span class="like-count">{{ $comment->likes_count }}</span> 👍</span>
                                    @else
                                        <span class="fb-comment__like-count" style="display:none;"><span class="like-count">0</span> 👍</span>
                                    @endif

                                    <button type="button" class="fb-comment__action-link" onclick="replyToComment({{ $comment->id }}, '{{ $comment->user->name }}')">Trả lời</button>

                                    @if(($post->user_id === auth()->id() || auth()->user()->isAdminPortalUser()) && $comment->parent_id === null)
                                        <button type="button" class="fb-comment__action-link fb-comment__action-link--pin" id="pin-btn-{{ $comment->id }}" onclick="togglePinComment({{ $comment->id }})">
                                            {{ $comment->is_pinned ? 'Bỏ ghim' : 'Ghim' }}
                                        </button>
                                    @endif

                                    @if($comment->user_id === auth()->id())
                                        <button type="button" class="fb-comment__action-link" onclick="startEditComment({{ $comment->id }})">Sửa</button>
                                    @endif

                                    @if($comment->user_id === auth()->id() || $post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
                                        <form action="{{ route('resident.comments.destroy', $comment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="fb-comment__action-link fb-comment__action-link--delete">Xóa</button>
                                        </form>
                                    @endif

                                    @if($comment->user_id !== auth()->id())
                                        <button type="button" class="fb-comment__action-link fb-comment__action-link--delete" onclick="openCommentReportModal({{ $comment->id }})">
                                            <i class="fa-regular fa-flag"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Nút xem thêm câu trả lời --}}
                        @if($comment->replies->count() > 0)
                            <button type="button" class="fb-view-replies-btn" id="view-replies-btn-{{ $comment->id }}" onclick="toggleReplies({{ $comment->id }})">
                                <i class="fa-solid fa-share-nodes"></i> <span class="reply-count">{{ $comment->replies->count() }}</span> phản hồi
                            </button>
                        @endif

                        {{-- Danh sách phản hồi con --}}
                        <div class="fb-comment-replies" id="replies-container-{{ $comment->id }}">
                            @foreach($comment->replies as $reply)
                                <div class="fb-comment" id="comment-{{ $reply->id }}">
                                    <img src="{{ $reply->user->avatar ? asset('storage/' . $reply->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($reply->user->name) . '&background=00236f&color=fff' }}" 
                                         alt="Avatar" class="fb-comment__avatar fb-comment__avatar--reply">
                                    
                                    <div class="fb-comment__main">
                                        <div class="fb-comment__bubble">
                                            <span class="fb-comment__name">
                                                {{ $reply->user->name }}
                                                <span class="fb-comment__role">{{ $reply->user->apartment ? 'Cư dân' : 'BQT' }}</span>
                                            </span>
                                            <div class="comment-content-wrap" id="comment-content-wrap-{{ $reply->id }}">
                                                <p class="fb-comment__text" id="comment-text-{{ $reply->id }}">{!! formatCommentContentBlade($reply->content) !!}</p>
                                            </div>
                                        </div>

                                        @if($reply->image_path && $reply->content !== '[Bình luận này đã bị xóa bởi Ban quản trị do vi phạm quy chuẩn cộng đồng]' && $reply->content !== '[Bình luận này đã bị ẩn tạm thời do nhận nhiều báo cáo vi phạm]')
                                            <div class="fb-comment__image" id="comment-img-attach-{{ $reply->id }}" onclick="openLightbox('{{ asset('storage/' . $reply->image_path) }}')" style="max-width: 160px;">
                                                <img src="{{ asset('storage/' . $reply->image_path) }}" alt="Ảnh bình luận" style="max-height: 120px;">
                                            </div>
                                        @endif

                                        @if($reply->user_id === auth()->id())
                                            <div class="fb-comment-edit-wrap" id="comment-edit-wrap-{{ $reply->id }}">
                                                <textarea id="comment-edit-input-{{ $reply->id }}" rows="2"></textarea>
                                                <div class="fb-comment-edit-actions">
                                                    <button type="button" class="pc-btn pc-btn--secondary" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; border-radius: 12px;" onclick="cancelEditComment({{ $reply->id }})">Hủy</button>
                                                    <button type="button" class="pc-btn" style="padding: 0.3rem 0.65rem; font-size: 0.75rem; border-radius: 12px;" onclick="saveEditComment({{ $reply->id }})">Lưu</button>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="fb-comment__meta">
                                            <span class="fb-comment__time" title="{{ $reply->created_at }}">{{ $reply->created_at->diffForHumans() }}</span>

                                            <div class="fb-like-wrapper">
                                                <div class="fb-reactions-popup">
                                                    <span class="fb-reaction-option" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'like')">👍</span>
                                                    <span class="fb-reaction-option" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'love')">❤️</span>
                                                    <span class="fb-reaction-option" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'haha')">😆</span>
                                                    <span class="fb-reaction-option" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'wow')">😮</span>
                                                    <span class="fb-reaction-option" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'sad')">😢</span>
                                                    <span class="fb-reaction-option" onclick="toggleLike(event, {{ $reply->id }}, 'comment', 'angry')">😡</span>
                                                </div>
                                                @php
                                                    $rLike = $reply->likedByCurrentUser->first();
                                                    $rReaction = $rLike ? $rLike->type : null;
                                                @endphp
                                                <button type="button" class="fb-comment__action-link fb-comment__action-link--like {{ $rReaction ? 'fb-comment__action-link--liked reaction-active-'.$rReaction : '' }}" onclick="toggleLike(event, {{ $reply->id }}, 'comment')">
                                                    <span class="reaction-text-span">{{ $rReaction ? match($rReaction) { 'love' => 'Yêu thích', 'haha' => 'Haha', 'wow' => 'Wow', 'sad' => 'Buồn', 'angry' => 'Phẫn nộ', default => 'Thích' } : 'Thích' }}</span>
                                                </button>
                                            </div>

                                            @if($reply->likes_count > 0)
                                                <span class="fb-comment__like-count"><span class="like-count">{{ $reply->likes_count }}</span> 👍</span>
                                            @else
                                                <span class="fb-comment__like-count" style="display:none;"><span class="like-count">0</span> 👍</span>
                                            @endif

                                            <button type="button" class="fb-comment__action-link" onclick="replyToComment({{ $comment->id }}, '{{ $reply->user->name }}')">Trả lời</button>

                                            @if($reply->user_id === auth()->id())
                                                <button type="button" class="fb-comment__action-link" onclick="startEditComment({{ $reply->id }})">Sửa</button>
                                            @endif

                                            @if($reply->user_id === auth()->id() || $post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
                                                <form action="{{ route('resident.comments.destroy', $reply->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="fb-comment__action-link fb-comment__action-link--delete">Xóa</button>
                                                </form>
                                            @endif

                                            @if($reply->user_id !== auth()->id())
                                                <button type="button" class="fb-comment__action-link fb-comment__action-link--delete" onclick="openCommentReportModal({{ $reply->id }})">
                                                    <i class="fa-regular fa-flag"></i>
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
</div>

{{-- Lightbox --}}
<div id="pcLightbox" class="pc-modal" onclick="closeLightbox()">
    <span class="pc-modal__close">&times;</span>
    <img class="pc-modal__content" id="pcLightboxTarget">
</div>

{{-- Modal Báo cáo Bài viết --}}
<div id="reportPostModal" class="rep-modal" onclick="handleOutsideModalClick(event)">
    <div class="rep-modal__content">
        <div class="rep-modal__header">
            <h3 class="rep-modal__title"><i class="fa-solid fa-triangle-exclamation" style="color: var(--color-error);"></i> Báo cáo bài viết</h3>
            <button type="button" class="rep-modal__close" onclick="closeReportModal()">&times;</button>
        </div>
        <form id="report-post-form">
            @csrf
            <input type="hidden" name="post_id" id="report-target-post-id" value="">
            <div class="rep-modal__body">
                <span class="rep-modal__label">Tại sao bạn muốn báo cáo bài viết này?</span>
                <label class="rep-option"><input type="radio" name="reason_preset" value="Spam, quảng cáo rác" onclick="toggleCustomReason(false)"><span class="rep-option__text">Spam, quảng cáo rác</span></label>
                <label class="rep-option"><input type="radio" name="reason_preset" value="Từ ngữ thô tục, công kích" onclick="toggleCustomReason(false)"><span class="rep-option__text">Từ ngữ thô tục, công kích</span></label>
                <label class="rep-option"><input type="radio" name="reason_preset" value="Lừa đảo, giả mạo" onclick="toggleCustomReason(false)"><span class="rep-option__text">Lừa đảo, giả mạo thông tin</span></label>
                <label class="rep-option"><input type="radio" name="reason_preset" value="Nội dung phản cảm, thù địch" onclick="toggleCustomReason(false)"><span class="rep-option__text">Nội dung phản cảm, thù địch</span></label>
                <label class="rep-option"><input type="radio" name="reason_preset" value="other" onclick="toggleCustomReason(true)"><span class="rep-option__text">Lý do khác...</span></label>
                <div class="rep-modal__custom-reason" id="custom-reason-container">
                    <textarea name="reason_custom" id="report-reason-custom" class="rep-modal__textarea" placeholder="Nhập lý do cụ thể..."></textarea>
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
            <h3 class="rep-modal__title"><i class="fa-solid fa-triangle-exclamation" style="color: var(--color-error);"></i> Báo cáo bình luận</h3>
            <button type="button" class="rep-modal__close" onclick="closeCommentReportModal()">&times;</button>
        </div>
        <form id="report-comment-form">
            @csrf
            <input type="hidden" name="comment_id" id="report-target-comment-id" value="">
            <div class="rep-modal__body">
                <span class="rep-modal__label">Tại sao bạn muốn báo cáo bình luận này?</span>
                <label class="rep-option"><input type="radio" name="comment_reason_preset" value="Spam, quảng cáo rác" onclick="toggleCommentCustomReason(false)"><span class="rep-option__text">Spam, quảng cáo rác</span></label>
                <label class="rep-option"><input type="radio" name="comment_reason_preset" value="Từ ngữ thô tục, công kích" onclick="toggleCommentCustomReason(false)"><span class="rep-option__text">Từ ngữ thô tục, công kích</span></label>
                <label class="rep-option"><input type="radio" name="comment_reason_preset" value="Lừa đảo, giả mạo" onclick="toggleCommentCustomReason(false)"><span class="rep-option__text">Lừa đảo, giả mạo thông tin</span></label>
                <label class="rep-option"><input type="radio" name="comment_reason_preset" value="Nội dung phản cảm, thù địch" onclick="toggleCommentCustomReason(false)"><span class="rep-option__text">Nội dung phản cảm, thù địch</span></label>
                <label class="rep-option"><input type="radio" name="comment_reason_preset" value="other" onclick="toggleCommentCustomReason(true)"><span class="rep-option__text">Lý do khác...</span></label>
                <div class="rep-modal__custom-reason" id="comment-custom-reason-container">
                    <textarea name="comment_reason_custom" id="report-comment-reason-custom" class="rep-modal__textarea" placeholder="Nhập lý do cụ thể..."></textarea>
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
            <h3 class="rep-modal__title"><i class="fa-regular fa-pen-to-square" style="color: var(--color-primary);"></i> Chỉnh sửa bài viết</h3>
            <button type="button" class="rep-modal__close" onclick="closeEditPostModal()">&times;</button>
        </div>
        <form id="edit-post-form" method="POST">
            @csrf
            @method('PUT')
            <div class="rep-modal__body">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <input type="hidden" name="title" id="edit-post-title">
                    <div>
                        <label class="rep-modal__label">Nội dung bài viết</label>
                        <textarea name="content" id="edit-post-content" class="pc-composer__body-input" rows="5" placeholder="Bạn đang muốn chia sẻ điều gì..."></textarea>
                    </div>
                    <div id="edit-price-input-container">
                        <label class="rep-modal__label">Giá thanh lý</label>
                        <div class="pc-composer__price-wrap" style="width: 100%; box-sizing: border-box; display: flex;">
                            <i class="fa-solid fa-tags pc-composer__price-icon"></i>
                            <input type="tel" name="price" id="edit-post-price" class="pc-composer__price-input" placeholder="Nhập giá..." style="flex: 1;">
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

{{-- Modal Cảm xúc --}}
<div id="reactionsListModal" class="rep-modal" onclick="handleReactionsOutsideClick(event)" style="display: none; align-items: center; justify-content: center; z-index: 10002;">
    <div class="rep-modal__content" style="max-width: 450px; border-radius: var(--radius-lg); overflow: hidden;">
        <div class="rep-modal__header" style="border-bottom: 1px solid #f1f5f9; padding: 1.25rem 1.5rem;">
            <h3 class="rep-modal__title" style="font-size: 1.1rem;"><i class="fa-regular fa-face-smile" style="color: var(--color-primary);"></i> Cảm xúc</h3>
            <button type="button" class="rep-modal__close" onclick="closeReactionsModal()">&times;</button>
        </div>
        <div class="rep-modal__body" style="padding: 0;">
            <div class="react-tabs" id="reactions-modal-tabs" style="display: flex; border-bottom: 1px solid #f1f5f9; overflow-x: auto; padding: 0.5rem 1rem; gap: 0.25rem;">
                <button type="button" class="react-tab react-tab--active" data-type="all" onclick="filterReactions('all')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-weight: 700; font-size: 0.825rem; cursor: pointer; color: var(--color-primary); border-bottom: 3px solid var(--color-primary);">Tất cả</button>
                <button type="button" class="react-tab" data-type="like" onclick="filterReactions('like')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); display: none;">👍 <span class="react-count-tab">0</span></button>
                <button type="button" class="react-tab" data-type="love" onclick="filterReactions('love')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); display: none;">❤️ <span class="react-count-tab">0</span></button>
                <button type="button" class="react-tab" data-type="haha" onclick="filterReactions('haha')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); display: none;">😆 <span class="react-count-tab">0</span></button>
                <button type="button" class="react-tab" data-type="wow" onclick="filterReactions('wow')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); display: none;">😮 <span class="react-count-tab">0</span></button>
                <button type="button" class="react-tab" data-type="sad" onclick="filterReactions('sad')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); display: none;">😢 <span class="react-count-tab">0</span></button>
                <button type="button" class="react-tab" data-type="angry" onclick="filterReactions('angry')" style="border: none; background: transparent; padding: 0.6rem 0.85rem; font-size: 0.825rem; cursor: pointer; color: var(--color-text-secondary); display: none;">😡 <span class="react-count-tab">0</span></button>
            </div>
            <div id="reactions-list-container" style="max-height: 320px; overflow-y: auto; padding: 1rem 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                <div style="text-align: center; color: var(--color-outline); padding: 1rem;">Đang tải...</div>
            </div>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 10001; display: flex; flex-direction: column; gap: 0.5rem; max-width: 350px;"></div>

<script>
    let editEditorInstance;
    const currentUserId = {{ auth()->id() }};
    const isPostOwner = {{ $post->user_id === auth()->id() ? 'true' : 'false' }};
    const isAdmin = {{ auth()->user()->isAdminPortalUser() ? 'true' : 'false' }};
    const currentPostId = {{ $post->id }};

    function cleanHtmlJS(html) {
        if (!html) return '';
        let clean = html;
        clean = clean.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
        clean = clean.replace(/on\w+\s*=\s*(["'])(.*?)\1/gi, '');
        return clean;
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    function nl2br(str) {
        if (!str) return '';
        let escaped = escapeHtml(str);
        escaped = escaped.replace(/@([^@\u200B]+)\u200B/gu, '<span class="comment-mention">@$1</span>');
        return escaped.replace(/\n/g, '<br>');
    }

    function formatNumber(n) {
        return parseInt(n, 10).toLocaleString('vi-VN');
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.style.cssText = `padding:0.75rem 1.25rem;border-radius:var(--radius-md);box-shadow:var(--color-shadow-lg);color:white;font-size:0.875rem;font-weight:600;display:flex;align-items:center;gap:0.65rem;opacity:0;transform:translateY(20px);transition:all 0.3s cubic-bezier(0.4,0,0.2,1);background-color:${type === 'success' ? '#10b981' : '#ef4444'};`;
        const icon = type === 'success' ? '<i class="fa-solid fa-circle-check"></i>' : '<i class="fa-solid fa-triangle-exclamation"></i>';
        toast.innerHTML = `${icon} <span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '1'; toast.style.transform = 'translateY(0)'; }, 10);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateY(20px)'; setTimeout(() => toast.remove(), 300); }, 3500);
    }

    function getReactionEmoji(type) {
        switch(type) { case 'love': return '❤️'; case 'haha': return '😆'; case 'wow': return '😮'; case 'sad': return '😢'; case 'angry': return '😡'; default: return '👍'; }
    }
    function getReactionText(type) {
        switch(type) { case 'love': return 'Yêu thích'; case 'haha': return 'Haha'; case 'wow': return 'Wow'; case 'sad': return 'Buồn'; case 'angry': return 'Phẫn nộ'; default: return 'Thích'; }
    }
</script>

<script>
    // === Share - Copy link ===
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

    // === Lightbox ===
    function openLightbox(src) { const lb = document.getElementById('pcLightbox'); lb.style.display = "flex"; document.getElementById('pcLightboxTarget').src = src; }
    function closeLightbox() { document.getElementById('pcLightbox').style.display = "none"; }

    // === Reply ===
    function replyToComment(commentId, authorName) {
        document.getElementById('comment-parent-id').value = commentId;
        document.getElementById('reply-target-name').innerText = authorName;
        const indicator = document.getElementById('reply-indicator');
        indicator.style.display = 'flex';
        const textarea = document.getElementById('comment-content');
        textarea.placeholder = `Trả lời ${authorName}...`;
        textarea.focus();
        textarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    function cancelReply() {
        document.getElementById('comment-parent-id').value = '';
        document.getElementById('reply-indicator').style.display = 'none';
        document.getElementById('comment-content').placeholder = 'Viết bình luận...';
    }

    // === Image Preview ===
    function previewCommentImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { document.getElementById('comment-image-preview').src = e.target.result; document.getElementById('comment-image-preview-wrap').style.display = 'block'; };
            reader.readAsDataURL(input.files[0]);
        }
    }
    function removeCommentImage() {
        const input = document.getElementById('comment-image-input');
        if (input) input.value = '';
        document.getElementById('comment-image-preview').src = '';
        document.getElementById('comment-image-preview-wrap').style.display = 'none';
    }
</script>

<script>
    // === Toggle Replies ===
    function toggleReplies(parentId) {
        const container = document.getElementById(`replies-container-${parentId}`);
        const btn = document.getElementById(`view-replies-btn-${parentId}`);
        if (!container || !btn) return;
        if (!container.classList.contains('fb-comment-replies--visible')) {
            container.classList.add('fb-comment-replies--visible');
            const count = btn.querySelector('.reply-count').innerText;
            btn.innerHTML = `<i class="fa-solid fa-angle-up"></i> Ẩn phản hồi`;
        } else {
            container.classList.remove('fb-comment-replies--visible');
            const count = container.querySelectorAll('.fb-comment').length;
            btn.innerHTML = `<i class="fa-solid fa-share-nodes"></i> <span class="reply-count">${count}</span> phản hồi`;
        }
    }

    // === Toggle Like / Reaction ===
    function toggleLike(event, id, type, reactionType = null) {
        event.preventDefault();
        event.stopPropagation();

        // If clicking on like-count, open reactions list modal
        if (event.target.classList.contains('like-count')) {
            openReactionsListModal(id, type);
            return;
        }

        const reqReactionType = reactionType || 'like';
        fetch('/resident/like', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ likeable_id: id, likeable_type: type, type: reqReactionType })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            if (type === 'post') {
                const btn = document.querySelector('.pc-like-btn');
                if (!btn) return;
                const countEl = document.querySelector('.rh-fb-card__stats span:first-child .like-count') || document.querySelector('.rh-fb-card__stats span:first-child');
                const textSpan = btn.querySelector('.reaction-text-span');
                // Update stats row
                const statsFirstSpan = document.querySelector('.rh-fb-card__stats span:first-child');
                if (statsFirstSpan) statsFirstSpan.innerHTML = `<span class="like-count">${data.likes_count}</span> lượt thích`;
                // Update button
                btn.className = 'rh-fb-card__action pc-like-btn';
                if (data.liked && data.reaction_type) {
                    btn.classList.add('rh-fb-like-btn--active');
                    if (textSpan) textSpan.innerText = getReactionText(data.reaction_type);
                } else {
                    if (textSpan) textSpan.innerText = 'Thích';
                }
            } else {
                // Comment like
                const commentEl = document.getElementById(`comment-${id}`);
                if (!commentEl) return;
                const likeWrapper = commentEl.querySelector('.fb-like-wrapper');
                const btn = likeWrapper.querySelector('.fb-comment__action-link--like');
                const textSpan = btn.querySelector('.reaction-text-span');
                const likeCountSpan = commentEl.querySelector('.fb-comment__like-count');

                btn.className = 'fb-comment__action-link fb-comment__action-link--like';
                if (data.liked && data.reaction_type) {
                    btn.classList.add('fb-comment__action-link--liked', 'reaction-active-' + data.reaction_type);
                    textSpan.innerText = getReactionText(data.reaction_type);
                } else {
                    textSpan.innerText = 'Thích';
                }
                if (likeCountSpan) {
                    likeCountSpan.querySelector('.like-count').innerText = data.likes_count;
                    likeCountSpan.style.display = data.likes_count > 0 ? 'inline-flex' : 'none';
                }
            }
        })
        .catch(err => console.error('Error toggling reaction:', err));
    }
</script>

<script>
    // === Edit Comment Inline ===
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
        document.getElementById('comment-edit-wrap-' + id).style.display = 'none';
        document.getElementById('comment-content-wrap-' + id).style.display = 'block';
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
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ content: content, _method: 'PUT' })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('comment-text-' + id).innerHTML = nl2br(data.comment.content);
                cancelEditComment(id);
                showToast('Đã cập nhật bình luận!');
            }
        })
        .catch(() => showToast('Lỗi khi lưu bình luận.', 'error'))
        .finally(() => { saveBtn.removeAttribute('disabled'); saveBtn.innerText = 'Lưu'; });
    }

    // === Pin Comment ===
    function togglePinComment(commentId) {
        const pinBtn = document.getElementById('pin-btn-' + commentId);
        if (pinBtn) pinBtn.setAttribute('disabled', 'disabled');
        fetch(`/resident/comments/${commentId}/pin`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message);
                const card = document.getElementById('comment-' + commentId);
                if (data.is_pinned) {
                    document.querySelectorAll('.fb-comment--pinned').forEach(el => {
                        el.classList.remove('fb-comment--pinned');
                        const b = el.querySelector('.fb-comment__pinned-badge'); if (b) b.style.display = 'none';
                        const p = el.querySelector('[id^="pin-btn-"]'); if (p) p.innerText = 'Ghim';
                    });
                    card.classList.add('fb-comment--pinned');
                    const badge = document.getElementById('pinned-badge-' + commentId); if (badge) badge.style.display = 'inline-flex';
                    if (pinBtn) pinBtn.innerText = 'Bỏ ghim';
                } else {
                    card.classList.remove('fb-comment--pinned');
                    const badge = document.getElementById('pinned-badge-' + commentId); if (badge) badge.style.display = 'none';
                    if (pinBtn) pinBtn.innerText = 'Ghim';
                }
            }
        })
        .catch(() => showToast('Có lỗi xảy ra.', 'error'))
        .finally(() => { if (pinBtn) pinBtn.removeAttribute('disabled'); });
    }
</script>

<script>
    // === Report Post Modal ===
    function openReportModal(postId) {
        document.getElementById('report-target-post-id').value = postId;
        document.getElementById('report-post-form').reset();
        toggleCustomReason(false);
        document.getElementById('submit-report-btn').setAttribute('disabled', 'disabled');
        const modal = document.getElementById('reportPostModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }
    function closeReportModal() { const m = document.getElementById('reportPostModal'); m.classList.remove('show'); setTimeout(() => m.style.display = 'none', 300); }
    function handleOutsideModalClick(e) { if (e.target === document.getElementById('reportPostModal')) closeReportModal(); }
    function toggleCustomReason(show) {
        const c = document.getElementById('custom-reason-container');
        const t = document.getElementById('report-reason-custom');
        c.style.display = show ? 'block' : 'none';
        show ? (t.setAttribute('required', 'required'), t.focus()) : (t.removeAttribute('required'), t.value = '');
        checkReportFormStatus();
    }
    function checkReportFormStatus() {
        const btn = document.getElementById('submit-report-btn');
        const checked = document.querySelector('input[name="reason_preset"]:checked');
        if (!checked) { btn.setAttribute('disabled', 'disabled'); return; }
        if (checked.value === 'other') { btn[document.getElementById('report-reason-custom').value.trim() ? 'removeAttribute' : 'setAttribute']('disabled', 'disabled'); }
        else { btn.removeAttribute('disabled'); }
    }

    // === Report Comment Modal ===
    function openCommentReportModal(commentId) {
        document.getElementById('report-target-comment-id').value = commentId;
        document.getElementById('report-comment-form').reset();
        toggleCommentCustomReason(false);
        document.getElementById('submit-comment-report-btn').setAttribute('disabled', 'disabled');
        const modal = document.getElementById('reportCommentModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }
    function closeCommentReportModal() { const m = document.getElementById('reportCommentModal'); m.classList.remove('show'); setTimeout(() => m.style.display = 'none', 300); }
    function handleCommentOutsideModalClick(e) { if (e.target === document.getElementById('reportCommentModal')) closeCommentReportModal(); }
    function toggleCommentCustomReason(show) {
        const c = document.getElementById('comment-custom-reason-container');
        const t = document.getElementById('report-comment-reason-custom');
        c.style.display = show ? 'block' : 'none';
        show ? (t.setAttribute('required', 'required'), t.focus()) : (t.removeAttribute('required'), t.value = '');
        checkCommentReportFormStatus();
    }
    function checkCommentReportFormStatus() {
        const btn = document.getElementById('submit-comment-report-btn');
        const checked = document.querySelector('input[name="comment_reason_preset"]:checked');
        if (!checked) { btn.setAttribute('disabled', 'disabled'); return; }
        if (checked.value === 'other') { btn[document.getElementById('report-comment-reason-custom').value.trim() ? 'removeAttribute' : 'setAttribute']('disabled', 'disabled'); }
        else { btn.removeAttribute('disabled'); }
    }

    // === Edit Post Modal ===
    function openEditPostModal(id, title, content, price) {
        const form = document.getElementById('edit-post-form');
        form.action = `/resident/posts/${id}`;
        document.getElementById('edit-post-title').value = title || '';
        if (editEditorInstance) editEditorInstance.setData(content || '');
        else document.getElementById('edit-post-content').value = content || '';
        const priceInput = document.getElementById('edit-post-price');
        priceInput.value = (price && parseFloat(price) > 0) ? parseInt(price, 10).toLocaleString('vi-VN') : '';
        const modal = document.getElementById('editPostModal');
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    }
    function closeEditPostModal() { const m = document.getElementById('editPostModal'); m.classList.remove('show'); setTimeout(() => m.style.display = 'none', 300); }
    function handleEditModalClick(e) { if (e.target === document.getElementById('editPostModal')) closeEditPostModal(); }
</script>

<script>
    // === Create Comment HTML (for AJAX/WebSocket) ===
    function createCommentHtml(comment) {
        const avatarUrl = comment.user.avatar ? `/storage/${comment.user.avatar}` : `https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&background=00236f&color=fff`;
        const roleText = comment.user.apartment ? 'Cư dân' : 'BQT';
        const isChild = comment.parent_id !== null;

        let imageHtml = '';
        if (comment.image_path && comment.content !== '[Bình luận này đã bị xóa bởi Ban quản trị do vi phạm quy chuẩn cộng đồng]' && comment.content !== '[Bình luận này đã bị ẩn tạm thời do nhận nhiều báo cáo vi phạm]') {
            const imgUrl = comment.image_path.startsWith('http') ? comment.image_path : `/storage/${comment.image_path}`;
            imageHtml = `<div class="fb-comment__image" onclick="openLightbox('${imgUrl}')" style="max-width:${isChild?'160':'200'}px;"><img src="${imgUrl}" style="max-height:${isChild?'120':'150'}px;"></div>`;
        }

        let editBtn = '', editForm = '';
        if (comment.user.id === currentUserId) {
            editBtn = `<button type="button" class="fb-comment__action-link" onclick="startEditComment(${comment.id})">Sửa</button>`;
            editForm = `<div class="fb-comment-edit-wrap" id="comment-edit-wrap-${comment.id}"><textarea id="comment-edit-input-${comment.id}" rows="2"></textarea><div class="fb-comment-edit-actions"><button type="button" class="pc-btn pc-btn--secondary" style="padding:0.3rem 0.65rem;font-size:0.75rem;border-radius:12px;" onclick="cancelEditComment(${comment.id})">Hủy</button><button type="button" class="pc-btn" style="padding:0.3rem 0.65rem;font-size:0.75rem;border-radius:12px;" onclick="saveEditComment(${comment.id})">Lưu</button></div></div>`;
        }

        let deleteBtn = '';
        if (comment.user.id === currentUserId || isPostOwner || isAdmin) {
            deleteBtn = `<form action="/resident/comments/${comment.id}" method="POST" style="display:inline;" onsubmit="return confirm('Xóa bình luận này?')"><input type="hidden" name="_token" value="${document.querySelector('input[name=_token]').value}"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="fb-comment__action-link fb-comment__action-link--delete">Xóa</button></form>`;
        }

        let reportBtn = '';
        if (comment.user.id !== currentUserId) {
            reportBtn = `<button type="button" class="fb-comment__action-link fb-comment__action-link--delete" onclick="openCommentReportModal(${comment.id})"><i class="fa-regular fa-flag"></i></button>`;
        }

        let pinBtn = '';
        if (!isChild && (isPostOwner || isAdmin)) {
            pinBtn = `<button type="button" class="fb-comment__action-link fb-comment__action-link--pin" id="pin-btn-${comment.id}" onclick="togglePinComment(${comment.id})">${comment.is_pinned ? 'Bỏ ghim' : 'Ghim'}</button>`;
        }

        const pinnedBadge = `<span class="fb-comment__pinned-badge" id="pinned-badge-${comment.id}" style="display:${comment.is_pinned?'inline-flex':'none'};"><i class="fa-solid fa-thumbtack"></i> Ghim</span>`;
        const replyToId = isChild ? comment.parent_id : comment.id;
        const likesCount = comment.likes_count || 0;
        const likeCountHtml = `<span class="fb-comment__like-count" style="display:${likesCount>0?'inline-flex':'none'};"><span class="like-count">${likesCount}</span> 👍</span>`;

        const reactionsPopup = `<div class="fb-reactions-popup"><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','like')">👍</span><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','love')">❤️</span><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','haha')">😆</span><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','wow')">😮</span><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','sad')">😢</span><span class="fb-reaction-option" onclick="toggleLike(event,${comment.id},'comment','angry')">😡</span></div>`;

        const html = `
            <div class="fb-comment fb-comment--new" id="comment-${comment.id}">
                <img src="${avatarUrl}" alt="Avatar" class="fb-comment__avatar ${isChild?'fb-comment__avatar--reply':''}">
                <div class="fb-comment__main">
                    <div class="fb-comment__bubble">
                        <span class="fb-comment__name">${escapeHtml(comment.user.name)} <span class="fb-comment__role">${roleText}</span>${!isChild?pinnedBadge:''}</span>
                        <div class="comment-content-wrap" id="comment-content-wrap-${comment.id}">
                            <p class="fb-comment__text" id="comment-text-${comment.id}">${nl2br(comment.content)}</p>
                        </div>
                    </div>
                    ${imageHtml}${editForm}
                    <div class="fb-comment__meta">
                        <span class="fb-comment__time">Vừa xong</span>
                        <div class="fb-like-wrapper">${reactionsPopup}<button type="button" class="fb-comment__action-link fb-comment__action-link--like" onclick="toggleLike(event,${comment.id},'comment')"><span class="reaction-text-span">Thích</span></button></div>
                        ${likeCountHtml}
                        <button type="button" class="fb-comment__action-link" onclick="replyToComment(${replyToId},'${escapeHtml(comment.user.name)}')">Trả lời</button>
                        ${pinBtn}${editBtn}${deleteBtn}${reportBtn}
                    </div>
                </div>
            </div>`;

        if (isChild) return html;

        return `<div class="fb-comment-thread" id="comment-thread-${comment.id}">${html}<div class="fb-comment-replies" id="replies-container-${comment.id}"></div></div>`;
    }
</script>

<script>
    // === Handle New Comment (real-time or AJAX) ===
    function handleNewComment(comment) {
        const emptyPlaceholder = document.getElementById('empty-comments-placeholder');
        if (emptyPlaceholder) emptyPlaceholder.remove();
        if (document.getElementById(`comment-${comment.id}`)) return;

        const container = document.querySelector('.fb-comments-list');
        if (!container) return;

        if (comment.parent_id !== null) {
            const repliesContainer = document.getElementById(`replies-container-${comment.parent_id}`);
            if (repliesContainer) {
                repliesContainer.insertAdjacentHTML('beforeend', createCommentHtml(comment));
                repliesContainer.classList.add('fb-comment-replies--visible');
                // Update or create toggle button
                let btn = document.getElementById(`view-replies-btn-${comment.parent_id}`);
                if (btn) {
                    const count = repliesContainer.querySelectorAll('.fb-comment').length;
                    btn.innerHTML = `<i class="fa-solid fa-angle-up"></i> Ẩn phản hồi`;
                } else {
                    const thread = document.getElementById(`comment-thread-${comment.parent_id}`);
                    if (thread) {
                        const count = repliesContainer.querySelectorAll('.fb-comment').length;
                        const newBtn = document.createElement('button');
                        newBtn.type = 'button';
                        newBtn.className = 'fb-view-replies-btn';
                        newBtn.id = `view-replies-btn-${comment.parent_id}`;
                        newBtn.onclick = () => toggleReplies(comment.parent_id);
                        newBtn.innerHTML = `<i class="fa-solid fa-angle-up"></i> Ẩn phản hồi`;
                        thread.insertBefore(newBtn, repliesContainer);
                    }
                }
            }
        } else {
            container.insertAdjacentHTML('beforeend', createCommentHtml(comment));
        }

        const countEl = document.getElementById('total-comments-count');
        if (countEl) {
            const count = container.querySelectorAll('.fb-comment-thread').length;
            countEl.innerText = count;
        }
    }

    // === Load Older Comments ===
    function loadOlderComments() {
        const btn = document.getElementById('load-older-btn');
        if (!btn) return;
        const offset = parseInt(btn.getAttribute('data-offset'), 10);
        const total = parseInt(btn.getAttribute('data-total'), 10);
        btn.setAttribute('disabled', 'disabled');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tải...';

        fetch(`/resident/posts/${currentPostId}/comments?offset=${offset}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.comments && data.comments.length > 0) {
                const container = document.querySelector('.fb-comments-list');
                const loadContainer = document.getElementById('load-older-container');
                const oldHeight = container.scrollHeight;
                for (let i = data.comments.length - 1; i >= 0; i--) {
                    const c = data.comments[i];
                    if (document.getElementById(`comment-${c.id}`)) continue;
                    const html = createCommentHtml(c);
                    if (loadContainer) loadContainer.insertAdjacentHTML('afterend', html);
                    else container.insertAdjacentHTML('afterbegin', html);
                }
                window.scrollBy(0, container.scrollHeight - oldHeight);
                const newOffset = offset + data.comments.length;
                btn.setAttribute('data-offset', newOffset);
                const remaining = total - newOffset;
                if (remaining <= 0) document.getElementById('load-older-container').remove();
                else { btn.removeAttribute('disabled'); btn.innerHTML = `<i class="fa-solid fa-clock-rotate-left"></i> Xem thêm bình luận cũ hơn (<span id="older-count">${remaining}</span>)`; }
            } else {
                const lc = document.getElementById('load-older-container'); if (lc) lc.remove();
            }
        })
        .catch(() => { btn.removeAttribute('disabled'); btn.innerHTML = `<i class="fa-solid fa-clock-rotate-left"></i> Xem thêm`; showToast('Lỗi tải bình luận.', 'error'); });
    }
</script>

<script>
    // === Reactions List Modal ===
    let currentReactions = [];
    function openReactionsListModal(likeableId, likeableType) {
        const modal = document.getElementById('reactionsListModal');
        const container = document.getElementById('reactions-list-container');
        container.innerHTML = '<div style="text-align:center;color:var(--color-outline);padding:1rem;"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải...</div>';
        document.querySelectorAll('.react-tab').forEach(t => { t.classList.remove('react-tab--active'); t.style.borderBottom = 'none'; t.style.color = 'var(--color-text-secondary)'; t.style.display = 'none'; });
        const allTab = document.querySelector('.react-tab[data-type="all"]');
        allTab.classList.add('react-tab--active'); allTab.style.borderBottom = '3px solid var(--color-primary)'; allTab.style.color = 'var(--color-primary)'; allTab.style.display = 'inline-block';
        modal.style.display = 'flex';
        fetch(`/resident/reactions/${likeableType}/${likeableId}`).then(r => r.json()).then(data => {
            if (data.success) { currentReactions = data.reactions; populateReactionsList('all'); updateReactionsTabsSummary(); }
            else container.innerHTML = '<div style="text-align:center;color:var(--color-error);padding:1rem;">Không thể tải.</div>';
        }).catch(() => container.innerHTML = '<div style="text-align:center;color:var(--color-error);padding:1rem;">Lỗi tải dữ liệu.</div>');
    }
    function populateReactionsList(type) {
        const container = document.getElementById('reactions-list-container');
        container.innerHTML = '';
        const filtered = type === 'all' ? currentReactions : currentReactions.filter(r => r.type === type);
        if (!filtered.length) { container.innerHTML = '<div style="text-align:center;color:var(--color-outline);padding:1.5rem 0;font-size:0.9rem;">Chưa có cảm xúc nào.</div>'; return; }
        filtered.forEach(r => {
            const emoji = getReactionEmoji(r.type);
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:0.75rem;padding-bottom:0.75rem;border-bottom:1px solid #f1f5f9;';
            row.innerHTML = `<div style="display:flex;align-items:center;gap:0.75rem;"><img src="${r.avatar}" style="width:38px;height:38px;border-radius:50%;object-fit:cover;"><div><span style="font-weight:700;font-size:0.9rem;">${escapeHtml(r.name)}</span><br><span style="font-size:0.75rem;color:var(--color-outline);">${r.apartment?'Cư dân':'BQT'}</span></div></div><span style="font-size:1.25rem;">${emoji}</span>`;
            container.appendChild(row);
        });
    }
    function updateReactionsTabsSummary() {
        const counts = {};
        currentReactions.forEach(r => { counts[r.type] = (counts[r.type] || 0) + 1; });
        Object.keys(counts).forEach(type => {
            const tab = document.querySelector(`.react-tab[data-type="${type}"]`);
            if (tab) { tab.style.display = 'inline-block'; const el = tab.querySelector('.react-count-tab'); if (el) el.innerText = counts[type]; }
        });
    }
    function filterReactions(type) {
        document.querySelectorAll('.react-tab').forEach(t => { t.classList.remove('react-tab--active'); t.style.borderBottom = 'none'; t.style.color = 'var(--color-text-secondary)'; });
        const tab = document.querySelector(`.react-tab[data-type="${type}"]`);
        if (tab) { tab.classList.add('react-tab--active'); tab.style.borderBottom = '3px solid var(--color-primary)'; tab.style.color = 'var(--color-primary)'; }
        populateReactionsList(type);
    }
    function closeReactionsModal() { document.getElementById('reactionsListModal').style.display = 'none'; }
    function handleReactionsOutsideClick(e) { if (e.target === document.getElementById('reactionsListModal')) closeReactionsModal(); }
</script>

<script>
    // === Price Formatter ===
    function initPriceFormatter(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        if (!input || !preview) return;
        function updatePreview() {
            let digits = input.value.replace(/\D/g, '');
            if (digits) { preview.innerText = `Định dạng: ${parseInt(digits,10).toLocaleString('vi-VN')}đ`; preview.style.display = 'block'; }
            else preview.style.display = 'none';
        }
        input.addEventListener('input', function() {
            let cursor = this.selectionStart, orig = this.value.length;
            let clean = this.value.replace(/\D/g, '');
            this.value = clean;
            let nc = cursor - (orig - clean.length); if (nc < 0) nc = 0;
            this.setSelectionRange(nc, nc);
            updatePreview();
        });
        input.addEventListener('blur', function() {
            let d = this.value.replace(/\D/g, '');
            this.value = d ? parseInt(d, 10).toLocaleString('vi-VN') : '';
            preview.style.display = 'none';
        });
    }

    // === DOMContentLoaded ===
    document.addEventListener("DOMContentLoaded", function() {
        initPriceFormatter('edit-post-price', 'edit-post-price-preview');

        // Report form listeners
        document.querySelectorAll('input[name="reason_preset"]').forEach(r => r.addEventListener('change', checkReportFormStatus));
        const cri = document.getElementById('report-reason-custom'); if (cri) cri.addEventListener('input', checkReportFormStatus);
        document.querySelectorAll('input[name="comment_reason_preset"]').forEach(r => r.addEventListener('change', checkCommentReportFormStatus));
        const ccri = document.getElementById('report-comment-reason-custom'); if (ccri) ccri.addEventListener('input', checkCommentReportFormStatus);

        // Report post submit
        const rpf = document.getElementById('report-post-form');
        if (rpf) rpf.addEventListener('submit', function(e) {
            e.preventDefault();
            const postId = document.getElementById('report-target-post-id').value;
            const preset = document.querySelector('input[name="reason_preset"]:checked');
            if (!preset) return;
            let reason = preset.value === 'other' ? document.getElementById('report-reason-custom').value.trim() : preset.value;
            if (!reason) { showToast('Chọn lý do báo cáo.', 'error'); return; }
            const btn = document.getElementById('submit-report-btn');
            btn.setAttribute('disabled','disabled'); btn.innerText = 'Đang gửi...';
            fetch(`/resident/posts/${postId}/report`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ reason }) })
            .then(r => { if (!r.ok) return r.json().then(e => { throw e; }); return r.json(); })
            .then(data => { closeReportModal(); showToast(data.message || 'Báo cáo thành công!'); setTimeout(() => window.location.href = "{{ route('resident.dashboard') }}", 1500); })
            .catch(err => showToast(err.message || 'Lỗi báo cáo.', 'error'))
            .finally(() => { btn.removeAttribute('disabled'); btn.innerText = 'Gửi báo cáo'; });
        });

        // Report comment submit
        const rcf = document.getElementById('report-comment-form');
        if (rcf) rcf.addEventListener('submit', function(e) {
            e.preventDefault();
            const cId = document.getElementById('report-target-comment-id').value;
            const preset = document.querySelector('input[name="comment_reason_preset"]:checked');
            if (!preset) return;
            let reason = preset.value === 'other' ? document.getElementById('report-comment-reason-custom').value.trim() : preset.value;
            if (!reason) { showToast('Chọn lý do.', 'error'); return; }
            const btn = document.getElementById('submit-comment-report-btn');
            btn.setAttribute('disabled','disabled'); btn.innerText = 'Đang gửi...';
            fetch(`/resident/comments/${cId}/report`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' }, body: JSON.stringify({ reason }) })
            .then(r => { if (!r.ok) return r.json().then(e => { throw e; }); return r.json(); })
            .then(data => { closeCommentReportModal(); showToast(data.message || 'Báo cáo thành công!'); const el = document.getElementById(`comment-${cId}`); if (el) { el.style.opacity = '0'; setTimeout(() => el.remove(), 500); } })
            .catch(err => showToast(err.message || 'Lỗi báo cáo.', 'error'))
            .finally(() => { btn.removeAttribute('disabled'); btn.innerText = 'Gửi báo cáo'; });
        });

        // Edit Post submit
        const epf = document.getElementById('edit-post-form');
        if (epf) epf.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            let content = editEditorInstance ? editEditorInstance.getData().trim() : document.getElementById('edit-post-content').value.trim();
            if (!content) { showToast('Nhập nội dung bài viết.', 'error'); return; }
            btn.setAttribute('disabled','disabled'); btn.innerText = 'Đang lưu...';
            let rawPrice = document.getElementById('edit-post-price').value.replace(/[^0-9]/g, '');
            fetch(this.action, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ title: document.getElementById('edit-post-title').value.trim(), content, price: rawPrice || null, _method: 'PUT' })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    closeEditPostModal();
                    showToast('Cập nhật thành công!');
                    const titleEl = document.querySelector('.pc-detail__title'); if (titleEl) titleEl.innerText = data.post.title;
                    const contentEl = document.querySelector('.pc-detail__content'); if (contentEl) contentEl.innerHTML = cleanHtmlJS(data.post.content);
                    const priceEl = document.querySelector('.pc-card__price'); if (priceEl && data.post.price !== null) priceEl.innerText = formatNumber(data.post.price) + 'đ';
                }
            })
            .catch(() => showToast('Lỗi cập nhật.', 'error'))
            .finally(() => { btn.removeAttribute('disabled'); btn.innerText = 'Lưu thay đổi'; });
        });

        // CKEditor for edit modal
        const editTA = document.getElementById('edit-post-content');
        if (editTA) {
            ClassicEditor.create(editTA, { toolbar: ['bold','italic','underline','bulletedList','numberedList','undo','redo'], placeholder: 'Nội dung bài viết...' })
            .then(editor => { editEditorInstance = editor; }).catch(err => console.error(err));
        }

        // AJAX comment form
        const commentForm = document.getElementById('comment-form');
        if (commentForm) commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const textarea = document.getElementById('comment-content');
            const content = textarea.value.trim();
            if (!content) return;
            const parentId = document.getElementById('comment-parent-id').value;
            const btn = this.querySelector('button[type="submit"]');
            btn.setAttribute('disabled','disabled'); btn.innerText = 'Đang gửi...';
            const formData = new FormData(this);
            if (!parentId) formData.delete('parent_id'); else formData.set('parent_id', parentId);
            fetch(this.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value, 'X-Requested-With': 'XMLHttpRequest' }, body: formData })
            .then(r => { if (!r.ok) return r.json().then(err => { throw err; }); return r.json(); })
            .then(data => { if (data.success) { textarea.value = ''; removeCommentImage(); cancelReply(); if (!document.getElementById(`comment-${data.comment.id}`)) handleNewComment(data.comment); } })
            .catch(err => { let msg = 'Lỗi gửi bình luận.'; if (err && err.message) msg = err.message; else if (err && err.errors) msg = Object.values(err.errors).flat().join(', '); showToast(msg, 'error'); })
            .finally(() => { btn.removeAttribute('disabled'); btn.innerText = 'Gửi'; });
        });

        // Tribute.js mentions
        fetch('/resident/search-members').then(res => res.json()).then(users => {
            if (typeof Tribute !== 'undefined') {
                const tribute = new Tribute({
                    values: users,
                    selectTemplate: item => item.original.value + '\u200B',
                    menuItemTemplate: item => `<div style="display:flex;align-items:center;gap:0.5rem;padding:0.25rem 0.5rem;"><img src="${item.original.avatar}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;"><span>${item.original.key}</span></div>`
                });
                const cc = document.getElementById('comment-content');
                if (cc) tribute.attach(cc);
            }
        }).catch(err => console.error("Tribute error:", err));

        // WebSockets (Laravel Echo)
        const broadcastConnection = "{{ env('BROADCAST_CONNECTION', 'reverb') }}";
        let echoConfig = {};
        if (broadcastConnection === 'reverb') {
            echoConfig = { broadcaster: 'reverb', key: "{{ env('REVERB_APP_KEY', 'domushub_key') }}", wsHost: "{{ env('REVERB_HOST') }}" || window.location.hostname, wsPort: parseInt("{{ env('REVERB_PORT', '8080') }}", 10), wssPort: parseInt("{{ env('REVERB_PORT', '8080') }}", 10), forceTLS: false, enabledTransports: ['ws', 'wss'] };
        } else {
            echoConfig = { broadcaster: 'pusher', key: "{{ env('PUSHER_APP_KEY') }}", cluster: "{{ env('PUSHER_APP_CLUSTER') }}", forceTLS: true };
        }
        if (typeof window.Echo === 'undefined' && typeof window.LaravelEcho !== 'undefined') {
            window.Echo = new window.LaravelEcho(echoConfig);
        }
        if (window.Echo) {
            window.Echo.channel(`post.${currentPostId}`)
                .listen('CommentCreated', e => { handleNewComment(e); showToast(`${e.user.name} vừa bình luận!`); })
                .listen('PostUpdated', e => {
                    const t = document.querySelector('.pc-detail__title'); if (t) t.innerText = e.title;
                    const c = document.querySelector('.pc-detail__content'); if (c) c.innerHTML = nl2br(e.content);
                    showToast('Bài viết vừa được cập nhật!');
                })
                .listen('CommentUpdated', e => {
                    const el = document.getElementById('comment-text-' + e.id);
                    if (el) el.innerHTML = nl2br(e.content);
                    if (e.is_pinned !== undefined) {
                        const card = document.getElementById('comment-' + e.id);
                        if (card) {
                            if (e.is_pinned) {
                                document.querySelectorAll('.fb-comment--pinned').forEach(x => { x.classList.remove('fb-comment--pinned'); const b = x.querySelector('.fb-comment__pinned-badge'); if(b) b.style.display='none'; });
                                card.classList.add('fb-comment--pinned');
                                const badge = document.getElementById('pinned-badge-' + e.id); if(badge) badge.style.display='inline-flex';
                            } else {
                                card.classList.remove('fb-comment--pinned');
                                const badge = document.getElementById('pinned-badge-' + e.id); if(badge) badge.style.display='none';
                            }
                        }
                    }
                })
                .listen('LikeToggled', e => {
                    if (e.likeable_type === 'post' && e.likeable_id == currentPostId) {
                        const statsFirstSpan = document.querySelector('.rh-fb-card__stats span:first-child');
                        if (statsFirstSpan) statsFirstSpan.innerHTML = `<span class="like-count">${e.likes_count}</span> lượt thích`;
                    } else if (e.likeable_type === 'comment') {
                        const cel = document.getElementById('comment-' + e.likeable_id);
                        if (cel) { const lc = cel.querySelector('.fb-comment__like-count .like-count'); if (lc) { lc.innerText = e.likes_count; lc.closest('.fb-comment__like-count').style.display = e.likes_count > 0 ? 'inline-flex' : 'none'; } }
                    }
                });
        }
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/tributejs/5.1.3/tribute.min.js"></script>
<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.0/dist/echo.iife.js"></script>
@endsection
