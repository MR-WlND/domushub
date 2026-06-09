@extends('layouts.resident.master')

@section('title', $post->title . ' – Bảng tin DomusHub')

@push('styles')
    @vite(['resources/css/resident/posts.css'])
@endpush

@section('content')
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

        {{-- Nút xóa bài đăng (chống IDOR) --}}
        @if($post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
            <div style="display: flex; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                <form action="{{ route('resident.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài đăng này không?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="pc-card__delete-btn">
                        <i class="fa-regular fa-trash-can"></i> Xóa bài viết
                    </button>
                </form>
            </div>
        @elseif($post->user_id !== auth()->id())
            <div style="display: flex; justify-content: flex-end; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                <button type="button" class="pc-card__action-btn" style="color: var(--color-error); border: none; background: transparent; cursor: pointer; font-family: inherit; font-size: 0.875rem; font-weight: 600; padding: 0.4rem 0.8rem; outline: none;" onclick="openReportModal({{ $post->id }})">
                    <i class="fa-regular fa-flag"></i> Báo cáo bài viết
                </button>
            </div>
        @endif
    </article>

    {{-- KHU VỰC BÌNH LUẬN --}}
    <section class="pc-comments-sec">
        <h3 class="pc-comments__title">
            <i class="fa-regular fa-comments" style="margin-right: 0.5rem;"></i> Bình luận ({{ $post->comments->count() }})
        </h3>

        {{-- FORM VIẾT BÌNH LUẬN --}}
        <div class="pc-comments__form">
            <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=00236f&color=fff' }}" 
                 alt="Avatar" class="pc-comment__avatar">
            
            <div class="pc-comments__textarea-wrap">
                <form action="{{ route('resident.posts.comments.store', $post->id) }}" method="POST" id="comment-form">
                    @csrf
                    {{-- ID comment cha (nếu có) --}}
                    <input type="hidden" name="parent_id" id="comment-parent-id" value="">

                    {{-- Banner hiển thị đang trả lời ai --}}
                    <div class="pc-reply-indicator" id="reply-indicator" style="display: none; margin-bottom: 0.5rem;">
                        <span><i class="fa-solid fa-reply"></i> Đang trả lời <strong id="reply-target-name"></strong></span>
                        <button type="button" class="pc-reply-indicator__close" onclick="cancelReply()">&times;</button>
                    </div>

                    <textarea name="content" id="comment-content" class="pc-comments__input" placeholder="Viết bình luận của bạn..." required></textarea>
                    
                    <div style="display: flex; justify-content: flex-end; margin-top: 0.5rem;">
                        <button type="submit" class="pc-btn">Gửi bình luận</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- DANH SÁCH CÁC BÌNH LUẬN --}}
        <div class="pc-comments-list">
            @if($post->comments->isEmpty())
                <p id="empty-comments-placeholder" style="text-align: center; color: var(--color-text-secondary); font-size: 0.9rem; margin: 2rem 0;">Chưa có bình luận nào. Hãy bắt đầu cuộc trò chuyện!</p>
            @else
                @foreach($post->comments as $comment)
                    <div class="pc-comment" id="comment-{{ $comment->id }}">
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

                                    {{-- Chỉ báo trả lời --}}
                                    @if($comment->parent_id && $comment->parent)
                                        <span class="pc-comment__reply-tag">
                                            <i class="fa-solid fa-reply"></i> trả lời <strong>{{ $comment->parent->user->name ?? 'Cư dân' }}</strong>
                                        </span>
                                    @endif
                                </div>
                                
                                <span class="pc-comment__time" title="{{ $comment->created_at }}">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </div>
                            
                            <p class="pc-comment__content">{!! nl2br(e($comment->content)) !!}</p>
                            
                            {{-- Các hành động trên bình luận --}}
                            <div class="pc-comment__actions" style="display: flex; width: 100%; align-items: center;">
                                <button type="button" class="pc-comment__action-btn" onclick="replyToComment({{ $comment->id }}, '{{ $comment->user->name }}')">
                                    Trả lời
                                </button>

                                {{-- Nút xóa bình luận (Chống IDOR: Tác giả bình luận, chủ bài viết, hoặc Admin mới xóa được) --}}
                                @if($comment->user_id === auth()->id() || $post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
                                    <form action="{{ route('resident.comments.destroy', $comment->id) }}" method="POST" style="display: inline; margin-left: 0.5rem;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="pc-comment__action-btn pc-comment__action-btn--delete">
                                            Xóa
                                        </button>
                                    </form>
                                @endif

                                {{-- Nút báo cáo bình luận (Chỉ xuất hiện khi bình luận không thuộc người đang đăng nhập) --}}
                                @if($comment->user_id !== auth()->id())
                                    <button type="button" class="pc-comment__action-btn pc-comment__action-btn--report" style="color: var(--color-error); margin-left: auto; display: flex; align-items: center; gap: 0.25rem;" onclick="openCommentReportModal({{ $comment->id }})">
                                        <i class="fa-regular fa-flag"></i> Báo cáo
                                    </button>
                                @endif
                            </div>
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

{{-- Toast Container hiển thị thông báo góc màn hình --}}
<div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 10001; display: flex; flex-direction: column; gap: 0.5rem; max-width: 350px;"></div>

<script>
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
        return escapeHtml(str).replace(/\n/g, '<br>');
    }

    function createCommentHtml(comment) {
        const avatarUrl = comment.user.avatar 
            ? `/storage/${comment.user.avatar}` 
            : `https://ui-avatars.com/api/?name=${encodeURIComponent(comment.user.name)}&background=00236f&color=fff`;
            
        const apartmentText = comment.user.apartment 
            ? `Căn hộ: ${escapeHtml(comment.user.apartment.apartment_number)}` 
            : 'Ban Quản Trị';
            
        let replyTag = '';
        if (comment.parent_id && comment.parent) {
            replyTag = `
                <span class="pc-comment__reply-tag">
                    <i class="fa-solid fa-reply"></i> trả lời <strong>${escapeHtml(comment.parent.user.name)}</strong>
                </span>
            `;
        }
        
        let deleteForm = '';
        if (comment.user.id === currentUserId || isPostOwner || isAdmin) {
            deleteForm = `
                <form action="/resident/comments/${comment.id}" method="POST" style="display: inline; margin-left: 0.5rem;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này không?')">
                    <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="pc-comment__action-btn pc-comment__action-btn--delete">
                        Xóa
                    </button>
                </form>
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
        
        return `
            <div class="pc-comment new-comment-highlight" id="comment-${comment.id}">
                <img src="${avatarUrl}" alt="Avatar" class="pc-comment__avatar">
                
                <div class="pc-comment__body">
                    <div class="pc-comment__header">
                        <div class="pc-comment__user-info">
                            <span class="pc-comment__author-name">${escapeHtml(comment.user.name)}</span>
                            <span class="pc-comment__apartment">${apartmentText}</span>
                            ${replyTag}
                        </div>
                        <span class="pc-comment__time" title="${escapeHtml(comment.created_at_human)}">
                            Vừa xong
                        </span>
                    </div>
                    
                    <p class="pc-comment__content">${nl2br(comment.content)}</p>
                    
                    <div class="pc-comment__actions" style="display: flex; width: 100%; align-items: center;">
                        <button type="button" class="pc-comment__action-btn" onclick="replyToComment(${comment.id}, '${escapeHtml(comment.user.name)}')">
                            Trả lời
                        </button>
                        ${deleteForm}
                        ${reportBtn}
                    </div>
                </div>
            </div>
        `;
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Đảm bảo Pusher khả dụng toàn cục cho Laravel Echo
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

        // Khởi tạo Echo
        if (typeof window.Echo === 'undefined' && typeof window.LaravelEcho !== 'undefined') {
            window.Echo = new window.LaravelEcho(echoConfig);
            console.log("DomusHub WebSockets: Echo initialized!");
        }

        if (window.Echo) {
            console.log(`DomusHub WebSockets: Subscribing to 'post.${currentPostId}' channel...`);
            window.Echo.channel(`post.${currentPostId}`)
                .listen('CommentCreated', (e) => {
                    console.log("DomusHub WebSockets: Received Event CommentCreated:", e);
                    const emptyPlaceholder = document.getElementById('empty-comments-placeholder');
                    if (emptyPlaceholder) {
                        emptyPlaceholder.remove();
                    }

                    if (document.getElementById(`comment-${e.id}`)) return;

                    const container = document.querySelector('.pc-comments-list');
                    if (container) {
                        const commentHtml = createCommentHtml(e);
                        container.insertAdjacentHTML('beforeend', commentHtml);
                        
                        // Cập nhật lại số đếm tiêu đề bình luận
                        const countEl = document.querySelector('.pc-comments__title');
                        if (countEl) {
                            const count = container.querySelectorAll('.pc-comment').length;
                            countEl.innerHTML = `<i class="fa-regular fa-comments" style="margin-right: 0.5rem;"></i> Bình luận (${count})`;
                        }

                        showToast(`Cư dân ${e.user.name} vừa bình luận bài viết!`, 'success');
                    }
                });
        } else {
            console.error("DomusHub WebSockets: Failed to initialize Echo. Please check libraries.");
        }
    });
</script>
@endsection
