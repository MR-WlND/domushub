@extends('layouts.resident.master')

@section('title', 'Bảng tin Cư dân – DomusHub')

@push('styles')
    @vite(['resources/css/resident/posts.css'])
@endpush

@section('content')
<div class="pc">

    {{-- HEADER --}}
    <div class="pc__header">
        <div>
            <p class="pc__eyebrow">Cộng đồng</p>
            <h1 class="pc__title">Bảng tin cư dân</h1>
        </div>
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
                            <input type="text" name="price" id="post-price" class="pc-composer__price-input" placeholder="Nhập giá thanh lý (ví dụ: 150.000)..." value="{{ old('price') }}">
                            <span style="font-weight: 700; color: var(--color-secondary); font-size: 0.9rem; margin-right: 0.5rem;">đ</span>
                        </div>
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
                    Thanh lý & Chợ
                </a>
            </div>

            {{-- DANH SÁCH BÀI VIẾT (FEED) --}}
            @if($posts->isEmpty())
                <div class="pc-card" style="text-align: center; padding: 3rem 1.5rem;">
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
                                <a href="{{ route('resident.posts.show', $post->id) }}" class="pc-card__action-btn">
                                    <i class="fa-regular fa-comment"></i> Bình luận ({{ $post->comments->count() }})
                                </a>
                                @if($post->user_id !== auth()->id())
                                    <button type="button" class="pc-card__action-btn" style="color: var(--color-error); border: none; background: transparent; cursor: pointer; font-family: inherit; font-size: 0.875rem; font-weight: 600; padding: 0; outline: none;" onclick="openReportModal({{ $post->id }})">
                                        <i class="fa-regular fa-flag"></i> Báo cáo
                                    </button>
                                @endif
                            </div>

                            {{-- Nút xóa bài viết (Bảo mật IDOR đã check ở Backend) --}}
                            @if($post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
                                <form action="{{ route('resident.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài đăng này không?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pc-card__delete-btn">
                                        <i class="fa-regular fa-trash-can"></i> Xóa bài
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach

                {{-- Phân trang --}}
                <div style="margin-top: 1rem;">
                    {{ $posts->links() }}
                </div>
            @endif

        </div>

        {{-- CỘT PHỤ (SIDEBAR) --}}
        <div class="pc-sidebar">
            
            {{-- Quy chế cộng đồng --}}
            <div class="pc-widget">
                <h3 class="pc-widget__title"><i class="fa-solid fa-handshake-angle" style="color: var(--color-primary); margin-right: 0.5rem;"></i>Quy chế Bảng tin</h3>
                <ul class="pc-rules-list">
                    <li><strong>Tôn trọng:</strong> Giữ thái độ hòa nhã, lịch sự khi thảo luận và bình luận bài viết.</li>
                    <li><strong>Chính xác:</strong> Thông tin thanh lý hoặc chia sẻ phải rõ ràng, trung thực.</li>
                    <li><strong>Không spam:</strong> Không đăng tin quảng cáo, rác hoặc thông tin vi phạm pháp luật.</li>
                    <li><strong>Bảo mật:</strong> Không chia sẻ thông tin cá nhân của người khác khi chưa được phép.</li>
                </ul>
            </div>

            {{-- Sản phẩm thanh lý nổi bật --}}
            <div class="pc-widget">
                <h3 class="pc-widget__title"><i class="fa-solid fa-store" style="color: var(--color-secondary); margin-right: 0.5rem;"></i>Gần đây trong Chợ thanh lý</h3>
                <div class="pc-market-list">
                    @if($featuredSales->isEmpty())
                        <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin: 0; text-align: center;">Chưa có đồ thanh lý nào.</p>
                    @else
                        @foreach($featuredSales as $sale)
                            <a href="{{ route('resident.posts.show', $sale->id) }}" class="pc-market-item">
                                @if($sale->image)
                                    <img src="{{ asset('storage/' . $sale->image) }}" alt="{{ $sale->title }}" class="pc-market-item__thumb">
                                @else
                                    <div class="pc-market-item__fallback">
                                        <i class="fa-solid fa-box"></i>
                                    </div>
                                @endif
                                <div class="pc-market-item__body">
                                    <h4 class="pc-market-item__title">{{ $sale->title }}</h4>
                                    <span class="pc-market-item__price">{{ number_format($sale->price, 0, ',', '.') }}đ</span>
                                </div>
                            </a>
                        @endforeach
                    @endif
                </div>
            </div>

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

    // Định dạng tiền tệ tự động cho input giá tiền
    const priceInput = document.getElementById('post-price');
    if (priceInput) {
        priceInput.addEventListener('input', function(e) {
            // Lấy giá trị thô chỉ chứa số
            let value = this.value.replace(/[^0-9]/g, '');
            
            if (value) {
                // Định dạng thêm dấu chấm phân cách hàng nghìn
                this.value = parseInt(value, 10).toLocaleString('vi-VN');
            } else {
                this.value = '';
            }
        });
    }

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
</script>
@endsection
