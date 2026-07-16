@extends('layouts.resident.master')

@section('title', 'Trang chủ - DomusHub')

@push('styles')
    @vite(['resources/css/pages/resident/home/index.css', 'resources/css/resident/posts.css'])
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
                                <button type="button" class="rh-fb-card__dropdown-item" 
                                        data-id="{{ $post->id }}"
                                        data-title="{{ $post->title }}"
                                        data-content="{{ $post->content }}"
                                        data-price="{{ $post->price }}"
                                        onclick="initiateEditPost(this)">
                                    <i class="fa-regular fa-pen-to-square"></i> Sửa bài viết
                                </button>
                            @endif
                            @if($post->user_id === auth()->id() || auth()->user()->isAdminPortalUser())
                                <form action="{{ route('resident.posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Xóa bài đăng này?')" style="margin: 0;">
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

</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
let editEditorInstance = null;

// Initiate Edit Post
function initiateEditPost(button) {
    const id = button.getAttribute('data-id');
    const title = button.getAttribute('data-title');
    const content = button.getAttribute('data-content');
    const price = button.getAttribute('data-price');
    openEditPostModal(id, title, content, price);
}

// Open Edit Modal
function openEditPostModal(id, title, content, price) {
    const form = document.getElementById('edit-post-form');
    form.action = `/resident/posts/${id}`;
    document.getElementById('edit-post-title').value = title || '';
    if (editEditorInstance) {
        editEditorInstance.setData(content || '');
    } else {
        document.getElementById('edit-post-content').value = content || '';
    }
    const priceInput = document.getElementById('edit-post-price');
    priceInput.value = (price && parseFloat(price) > 0) ? parseInt(price, 10).toLocaleString('vi-VN') : '';
    
    // Trigger preview update if exists
    const preview = document.getElementById('edit-post-price-preview');
    if (preview) {
        if (price && parseFloat(price) > 0) {
            preview.innerText = `Định dạng: ${parseInt(price, 10).toLocaleString('vi-VN')}đ`;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }

    const modal = document.getElementById('editPostModal');
    modal.style.display = 'flex';
}

function closeEditPostModal() {
    const modal = document.getElementById('editPostModal');
    modal.style.display = 'none';
}

function handleEditModalClick(e) {
    if (e.target === document.getElementById('editPostModal')) {
        closeEditPostModal();
    }
}

// Price Formatter Helper
function initPriceFormatter(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;
    function updatePreview() {
        let digits = input.value.replace(/\D/g, '');
        if (digits) {
            preview.innerText = `Định dạng: ${parseInt(digits, 10).toLocaleString('vi-VN')}đ`;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }
    input.addEventListener('input', function() {
        let cursor = this.selectionStart, orig = this.value.length;
        let clean = this.value.replace(/\D/g, '');
        this.value = clean;
        let nc = cursor - (orig - clean.length);
        if (nc < 0) nc = 0;
        this.setSelectionRange(nc, nc);
        updatePreview();
    });
    input.addEventListener('blur', function() {
        let d = this.value.replace(/\D/g, '');
        this.value = d ? parseInt(d, 10).toLocaleString('vi-VN') : '';
        preview.style.display = 'none';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize price formatter
    initPriceFormatter('edit-post-price', 'edit-post-price-preview');

    // Initialize CKEditor for edit post
    const editTA = document.getElementById('edit-post-content');
    if (editTA) {
        ClassicEditor.create(editTA, {
            toolbar: ['bold', 'italic', 'underline', 'bulletedList', 'numberedList', 'undo', 'redo'],
            placeholder: 'Nội dung bài viết...'
        })
        .then(editor => {
            editEditorInstance = editor;
        })
        .catch(err => console.error(err));
    }

    // Handle Edit Post Form Submit
    const editPostForm = document.getElementById('edit-post-form');
    if (editPostForm) {
        editPostForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            let content = editEditorInstance ? editEditorInstance.getData().trim() : document.getElementById('edit-post-content').value.trim();
            if (!content) {
                showToast('Nhập nội dung bài viết.', 'error');
                return;
            }
            btn.setAttribute('disabled', 'disabled');
            btn.innerText = 'Đang lưu...';
            let rawPrice = document.getElementById('edit-post-price').value.replace(/[^0-9]/g, '');
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    title: document.getElementById('edit-post-title').value.trim(),
                    content: content,
                    price: rawPrice || null,
                    _method: 'PUT'
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    closeEditPostModal();
                    showToast('Cập nhật bài viết thành công!');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(data.message || 'Lỗi cập nhật.', 'error');
                }
            })
            .catch(() => showToast('Lỗi cập nhật.', 'error'))
            .finally(() => {
                btn.removeAttribute('disabled');
                btn.innerText = 'Lưu thay đổi';
            });
        });
    }

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

{{-- Modal Chỉnh sửa Bài viết --}}
<div id="editPostModal" class="rep-modal" onclick="handleEditModalClick(event)" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div class="rep-modal__content" style="background: #fff; padding: 24px; border-radius: 12px; max-width: 580px; width: 90%; box-shadow: 0 10px 25px rgba(0,0,0,0.15); position: relative; animation: postDropdownFadeIn 0.3s ease-out; box-sizing: border-box;">
        <div class="rep-modal__header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 class="rep-modal__title" style="margin: 0; font-size: 1.1rem; font-weight: 700; color: #00236f; display: flex; align-items: center; gap: 8px;">
                <i class="fa-regular fa-pen-to-square" style="color: #2563eb;"></i> Chỉnh sửa bài viết
            </h3>
            <button type="button" class="rep-modal__close" onclick="closeEditPostModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #64748b;">&times;</button>
        </div>
        <form id="edit-post-form" method="POST">
            @csrf
            @method('PUT')
            <div class="rep-modal__body" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                <input type="hidden" name="title" id="edit-post-title">
                <div style="margin-bottom: 10px;">
                    <label class="rep-modal__label" style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">Nội dung bài viết</label>
                    <textarea name="content" id="edit-post-content" rows="5" placeholder="Bạn đang muốn chia sẻ điều gì..."></textarea>
                </div>
                <div id="edit-price-input-container" style="margin-bottom: 10px;">
                    <label class="rep-modal__label" style="font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 6px; display: block;">Giá thanh lý (nếu có)</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="fa-solid fa-tags" style="position: absolute; left: 12px; color: #94a3b8;"></i>
                        <input type="tel" name="price" id="edit-post-price" placeholder="Nhập giá..." style="width: 100%; padding: 10px 30px 10px 36px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 0.88rem; outline: none; box-sizing: border-box;">
                        <span style="position: absolute; right: 12px; font-weight: 700; color: #64748b; font-size: 0.9rem;">đ</span>
                    </div>
                    <div id="edit-post-price-preview" style="font-size: 0.825rem; color: #10b981; font-weight: 600; margin-top: 0.35rem; margin-left: 4px; display: none;"></div>
                </div>
            </div>
            <div class="rep-modal__footer" style="display: flex; justify-content: flex-end; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 14px;">
                <button type="button" class="rh-section__btn" style="background: #64748b;" onclick="closeEditPostModal()">Hủy bỏ</button>
                <button type="submit" class="rh-section__btn" style="background: #2563eb;">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

{{-- Toast Container --}}
<div id="toast-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 10001; display: flex; flex-direction: column; gap: 0.5rem; max-width: 350px;"></div>

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
}

function closeHideModal() {
    const modal = document.getElementById('confirmHideModal');
    modal.style.display = 'none';
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
}

function closeReportModal() {
    const modal = document.getElementById('reportPostModal');
    modal.style.display = 'none';
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
@endpush
@endsection
