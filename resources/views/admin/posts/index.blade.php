@extends('layouts.admin.master')

@section('page_title', 'Quản lý bài viết cư dân')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@push('styles')
    @vite(['resources/css/pages/admin/posts/index.css'])
@endpush

@php
    $statusLabels = [
        'published' => 'Đang hiển thị',
        'hidden' => 'Đã ẩn',
    ];
    $typeLabels = [
        'general' => 'Chia sẻ',
        'marketplace' => 'Thanh lý & Chợ',
    ];
@endphp

@section('content')
    <div class="posts-page">
        <div class="posts-page__header">
            <div>
                <p class="posts-page__eyebrow">Tương tác & Bảng tin</p>
                <h1>Quản lý bài viết cư dân</h1>
            </div>
        </div>

        @if (session('success'))
            <div class="posts-alert posts-alert--success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="posts-alert posts-alert--danger">{{ $errors->first() }}</div>
        @endif

        {{-- TABS ĐIỀU HƯỚNG LỚN --}}
        <div class="posts-page__tabs">
            <a href="{{ route('admin.posts.index', ['tab' => 'posts']) }}" class="posts-page__tab {{ $activeTab === 'posts' ? 'posts-page__tab--active' : '' }}">
                <i class="fa-regular fa-file-lines"></i> Bài viết bị báo cáo
            </a>
            <a href="{{ route('admin.posts.index', ['tab' => 'comments']) }}" class="posts-page__tab {{ $activeTab === 'comments' ? 'posts-page__tab--active' : '' }}">
                <i class="fa-regular fa-comment-dots"></i> Bình luận bị báo cáo
            </a>
        </div>

        @if($activeTab === 'posts')
            {{-- BỘ LỌC TÌM KIẾM BÀI VIẾT --}}
            <form class="posts-filter" method="GET" action="{{ route('admin.posts.index') }}">
                <input type="hidden" name="tab" value="posts">
                <label class="posts-filter__field">
                    <span>Tìm kiếm bài đăng</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Tiêu đề, nội dung hoặc người đăng...">
                </label>

                <label class="posts-filter__field">
                    <span>Phân loại</span>
                    <select name="type">
                        <option value="">Tất cả phân loại</option>
                        @foreach ($typeLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="posts-filter__field">
                    <span>Trạng thái</span>
                    <select name="status">
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statusLabels as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="posts-filter__field">
                    <span>Báo cáo</span>
                    <select name="reported">
                        <option value="">Tất cả bài đăng</option>
                        <option value="yes" @selected(request('reported') === 'yes')>Có lượt báo cáo</option>
                        <option value="high" @selected(request('reported') === 'high')>Bị báo cáo nhiều (>= 3)</option>
                    </select>
                </label>

                <div class="posts-filter__actions">
                    <button type="submit" class="posts-button posts-button--primary">Lọc</button>
                    <a href="{{ route('admin.posts.index', ['tab' => 'posts']) }}" class="posts-button posts-button--secondary">Xóa lọc</a>
                </div>
            </form>

            {{-- BẢNG DANH SÁCH BÀI VIẾT --}}
            <div class="posts-table-card">
                <div class="posts-table-wrap">
                    <table class="posts-table">
                        <thead>
                            <tr>
                                <th>Bài viết</th>
                                <th>Phân loại</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($posts as $post)
                                <tr>
                                    <td>
                                        <div class="post-details">
                                            <a href="javascript:void(0)" onclick="openAdminPostModal({{ $post->id }})" class="post-title-link" title="Bấm để xem chi tiết bài đăng và danh sách báo cáo vi phạm">
                                                {{ $post->title }}
                                            </a>
                                            @if($post->reports_count > 0)
                                                <span style="color: #dc2626; font-size: 0.725rem; font-weight: 700; background: #fee2e2; padding: 2px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 0.25rem; margin-left: 0.5rem;" title="Bài viết bị cư dân báo cáo vi phạm">
                                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $post->reports_count }} báo cáo
                                                </span>
                                            @endif
                                            <p class="post-excerpt">{{ Str::limit($post->content, 120, '...') }}</p>
                                            <span class="post-author">
                                                Đăng bởi: <strong>{{ $post->user->name ?? 'Không xác định' }}</strong> 
                                                @if($post->user && $post->user->apartment)
                                                    (Căn: {{ $post->user->apartment->apartment_number }})
                                                @endif
                                                • {{ $post->created_at->format('H:i d/m/Y') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($post->price !== null)
                                            <span class="post-type-badge post-type-badge--marketplace">Thanh lý</span>
                                            <span class="post-price-tag">{{ number_format($post->price, 0, ',', '.') }}đ</span>
                                        @else
                                            <span class="post-type-badge post-type-badge--general">Chia sẻ</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-pill status-pill--{{ $post->status }}">
                                            <span class="status-dot"></span>
                                            {{ $statusLabels[$post->status] ?? $post->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-actions">
                                            {{-- Nút Ẩn/Hiện bài viết --}}
                                            <form action="{{ route('admin.posts.toggle-status', $post->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái hiển thị của bài đăng này?')">
                                                @csrf
                                                @if ($post->status === 'published')
                                                    <button type="submit" class="admin-action-btn admin-action-btn--toggle">
                                                        <i class="fa-solid fa-eye-slash"></i> Ẩn bài viết
                                                    </button>
                                                @else
                                                    <button type="submit" class="admin-action-btn admin-action-btn--toggle" style="color: #4f46e5; border-color: #c7d2fe;">
                                                        <i class="fa-solid fa-eye"></i> Hiện bài viết
                                                    </button>
                                                @endif
                                            </form>

                                            {{-- Nút Bỏ qua báo cáo (chỉ hiện khi có lượt báo cáo) --}}
                                            @if($post->reports_count > 0)
                                                <form action="{{ route('admin.posts.dismiss-reports', $post->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn bỏ qua và gỡ bỏ toàn bộ lượt báo cáo của bài viết này?')">
                                                    @csrf
                                                    <button type="submit" class="admin-action-btn admin-action-btn--dismiss">
                                                        <i class="fa-solid fa-shield-halved"></i> Bỏ qua báo cáo
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Nút Xóa bài viết vĩnh viễn --}}
                                            <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hành động này sẽ xóa vĩnh viễn bài đăng và tất cả các bình luận liên quan. Bạn có chắc chắn muốn tiếp tục?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="admin-action-btn admin-action-btn--delete">
                                                    <i class="fa-solid fa-trash-can"></i> Xóa vĩnh viễn
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="posts-empty">Không tìm thấy bài đăng cư dân nào phù hợp.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PHÂN TRANG BÀI VIẾT --}}
                <div class="posts-table-footer">
                    <div class="posts-table-footer__stats">
                        Hiển thị {{ $posts->count() }} trên {{ $posts->total() }} bài viết
                    </div>
                    <div>
                        {{ $posts->appends(request()->query())->links('admin.users.pagination') }}
                    </div>
                </div>
            </div>
        @else
            {{-- BỘ LỌC TÌM KIẾM BÌNH LUẬN --}}
            <form class="posts-filter" method="GET" action="{{ route('admin.posts.index') }}">
                <input type="hidden" name="tab" value="comments">
                <label class="posts-filter__field" style="flex: 2;">
                    <span>Tìm kiếm bình luận</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Nội dung bình luận hoặc tên cư dân...">
                </label>
                <div class="posts-filter__actions" style="margin-top: auto; align-self: flex-end;">
                    <button type="submit" class="posts-button posts-button--primary">Lọc</button>
                    <a href="{{ route('admin.posts.index', ['tab' => 'comments']) }}" class="posts-button posts-button--secondary">Xóa lọc</a>
                </div>
            </form>

            {{-- DANH SÁCH THẺ BÌNH LUẬN BỊ BÁO CÁO (CARDS LIST) --}}
            <div class="comments-cards-list">
                @forelse($reportedComments as $comment)
                    <div class="comment-card">
                        {{-- Context box (Bài viết gốc) --}}
                        <div class="comment-card__post-context">
                            <span class="context-title"><i class="fa-regular fa-paper-plane"></i> Bài viết gốc:</span>
                            <span class="context-link" onclick="openAdminPostModal({{ $comment->post->id ?? 0 }}, false)">
                                {{ $comment->post->title ?? 'Bài đăng đã bị xóa hoặc không tìm thấy' }}
                            </span>
                            <span class="context-author">
                                (Đăng bởi: <strong>{{ $comment->post->user->name ?? 'N/A' }}</strong>)
                            </span>
                        </div>

                        {{-- Violator box (Bình luận vi phạm) --}}
                        <div class="comment-card__violator">
                            <div class="violator-meta">
                                <div class="violator-author">
                                    <i class="fa-regular fa-user"></i>
                                    <strong>{{ $comment->user->name ?? 'N/A' }}</strong> 
                                    @if($comment->user && $comment->user->apartment)
                                        (Căn: {{ $comment->user->apartment->apartment_number }})
                                    @endif
                                </div>
                                <div class="violator-time">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $comment->created_at->format('H:i d/m/Y') }}
                                </div>
                            </div>
                            
                            <div class="violator-content">
                                {{ $comment->content }}
                            </div>

                            {{-- Nhãn cảnh báo màu đỏ + Tooltip chứa danh sách lý do cụ thể --}}
                            <div class="violator-warning">
                                <span class="warning-badge">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    {{ $comment->reports_count }} báo cáo vi phạm
                                </span>
                                
                                <div class="warning-tooltip">
                                    <div class="tooltip-header">Chi tiết các báo cáo:</div>
                                    <ul class="tooltip-list">
                                        @foreach($comment->reports as $report)
                                            <li>
                                                <strong>{{ $report->user->name ?? 'Ẩn danh' }}</strong>
                                                @if($report->user && $report->user->apartment)
                                                    (Căn: {{ $report->user->apartment->apartment_number }})
                                                @endif:
                                                <span>{{ $report->reason }}</span>
                                                <small>({{ $report->created_at->format('H:i d/m/Y') }})</small>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Action bar (Thao tác nhanh) --}}
                        <div class="comment-card__actions">
                            {{-- Nút Bỏ qua báo cáo --}}
                            <form action="{{ route('admin.comments.dismiss-reports', $comment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn bỏ qua và gỡ bỏ toàn bộ lượt báo cáo của bình luận này?')">
                                @csrf
                                <button type="submit" class="admin-action-btn admin-action-btn--dismiss" style="padding: 0.5rem 1rem;">
                                    <i class="fa-solid fa-shield-halved"></i> Bỏ qua báo cáo
                                </button>
                            </form>

                            {{-- Nút Xóa bình luận --}}
                            <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nội dung bình luận vi phạm này? (Nội dung sẽ được cập nhật thành thông báo vi phạm)')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-action-btn admin-action-btn--delete" style="padding: 0.5rem 1rem;">
                                    <i class="fa-solid fa-trash-can"></i> Xóa bình luận
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="posts-empty">Không tìm thấy bình luận bị báo cáo nào phù hợp.</div>
                @endforelse
            </div>

            {{-- PHÂN TRANG BÌNH LUẬN --}}
            @if($reportedComments->total() > 0)
                <div class="posts-table-footer" style="margin-top: 1.5rem; border-radius: 8px; border: 1px solid #e2e8f0;">
                    <div class="posts-table-footer__stats">
                        Hiển thị {{ $reportedComments->count() }} trên {{ $reportedComments->total() }} bình luận
                    </div>
                    <div>
                        {{ $reportedComments->appends(request()->query())->links('admin.users.pagination') }}
                    </div>
                </div>
            @endif
        @endif
    </div>

    {{-- Modal xem chi tiết bài viết & báo cáo phía Admin --}}
    <div id="adminPostDetailModal" class="adm-modal" onclick="handleAdminOutsideClick(event)">
        <div class="adm-modal__content">
            <div class="adm-modal__header">
                <h3 class="adm-modal__title" id="adm-modal-title-text">Chi tiết bài viết</h3>
                <button type="button" class="adm-modal__close" onclick="closeAdminPostModal()">&times;</button>
            </div>
            <div class="adm-modal__body">
                {{-- Phần 1: Nội dung bài viết --}}
                <div class="adm-post-section">
                    <div class="adm-post-meta">
                        <span>Đăng bởi: <strong id="adm-post-author"></strong></span>
                        <span>•</span>
                        <span id="adm-post-time"></span>
                        <span>•</span>
                        <span id="adm-post-type"></span>
                    </div>
                    <div class="adm-post-content" id="adm-post-content-text"></div>
                    <div class="adm-post-gallery" id="adm-post-gallery-list"></div>
                </div>
                
                {{-- Phần 2: Danh sách các báo cáo vi phạm --}}
                <div class="adm-reports-section" id="adm-reports-section-container">
                    <h4 class="adm-section-title">
                        <i class="fa-solid fa-triangle-exclamation" style="color: #dc2626; margin-right: 0.25rem;"></i>
                        Báo cáo vi phạm (<span id="adm-reports-count">0</span>)
                    </h4>
                    <div class="adm-reports-list" id="adm-reports-list-container"></div>
                </div>
            </div>
            <div class="adm-modal__footer">
                {{-- Các nút hành động nhanh --}}
                <div id="adm-modal-actions" style="display: flex; gap: 0.5rem; width: 100%; justify-content: flex-end; align-items: center;">
                    <!-- Render động qua JS -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAdminPostModal(postId, showActions = true) {
            const modal = document.getElementById('adminPostDetailModal');
            
            // Hiển thị trạng thái loading tạm thời
            document.getElementById('adm-modal-title-text').innerText = 'Đang tải dữ liệu...';
            document.getElementById('adm-post-author').innerText = '...';
            document.getElementById('adm-post-time').innerText = '...';
            document.getElementById('adm-post-type').innerHTML = '...';
            document.getElementById('adm-post-content-text').innerText = 'Đang tải nội dung bài viết...';
            document.getElementById('adm-post-gallery-list').innerHTML = '';
            document.getElementById('adm-reports-count').innerText = '0';
            document.getElementById('adm-reports-list-container').innerHTML = '<p style="color: #64748b; font-size: 0.85rem; margin: 0;">Đang tải danh sách báo cáo...</p>';
            document.getElementById('adm-modal-actions').innerHTML = '';
            
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
            
            // Fetch dữ liệu từ API JSON
            fetch(`/admin/posts/${postId}/json`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Không thể kết nối đến máy chủ.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const post = data.post;
                        
                        // 1. Cập nhật nội dung bài viết
                        document.getElementById('adm-modal-title-text').innerText = post.title || 'Chi tiết bài viết';
                        document.getElementById('adm-post-author').innerText = post.user ? `${post.user.name} (Căn: ${post.user.apartment ? post.user.apartment.apartment_number : 'BQT'})` : 'Không xác định';
                        
                        // Format time
                        const createdDate = new Date(post.created_at);
                        const timeStr = createdDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) + ' ' + createdDate.toLocaleDateString('vi-VN');
                        document.getElementById('adm-post-time').innerText = timeStr;
                        
                        // Phân loại & Giá
                        let typeHtml = '';
                        if (post.price !== null) {
                           const priceFormatted = new Intl.NumberFormat('vi-VN').format(post.price) + 'đ';
                           typeHtml = `<span class="post-type-badge post-type-badge--marketplace">Thanh lý</span> <span class="post-price-tag" style="display:inline; margin-left: 0.25rem;">${priceFormatted}</span>`;
                        } else {
                           typeHtml = `<span class="post-type-badge post-type-badge--general">Chia sẻ</span>`;
                        }
                        document.getElementById('adm-post-type').innerHTML = typeHtml;
                        document.getElementById('adm-post-content-text').innerText = post.content;
                        
                        // Render hình ảnh đính kèm (Lưới ảnh trong Admin popup)
                        const gallery = document.getElementById('adm-post-gallery-list');
                        gallery.innerHTML = '';
                        if (post.images && post.images.length > 0) {
                            post.images.forEach(img => {
                                const imageEl = document.createElement('img');
                                imageEl.src = `/storage/${img.image_path}`;
                                imageEl.alt = 'Ảnh đính kèm';
                                imageEl.onclick = function() {
                                    window.open(this.src, '_blank');
                                };
                                gallery.appendChild(imageEl);
                            });
                        }
                        
                        // 2. Cập nhật danh sách các báo cáo vi phạm
                        const reportsCount = post.reports_count;
                        document.getElementById('adm-reports-count').innerText = reportsCount;
                        
                        const reportsContainer = document.getElementById('adm-reports-list-container');
                        reportsContainer.innerHTML = '';
                        
                        const reportsSection = document.getElementById('adm-reports-section-container');
                        if (reportsCount > 0 && showActions) {
                            reportsSection.style.display = 'flex';
                            post.reports.forEach(rep => {
                                const repDate = new Date(rep.created_at);
                                const repTimeStr = repDate.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) + ' ' + repDate.toLocaleDateString('vi-VN');
                                
                                const item = document.createElement('div');
                                item.className = 'adm-report-item';
                                item.innerHTML = `
                                    <span class="adm-report-reporter"><i class="fa-regular fa-user"></i> Cư dân: <strong>${rep.user ? rep.user.name : 'Ẩn danh'}</strong> (Căn: ${rep.user && rep.user.apartment ? rep.user.apartment.apartment_number : 'N/A'})</span>
                                    <span class="adm-report-reason"><i class="fa-regular fa-comment-dots"></i> Lý do: <strong>${rep.reason}</strong></span>
                                    <span class="adm-report-time"><i class="fa-regular fa-clock"></i> Báo cáo lúc: ${repTimeStr}</span>
                                `;
                                reportsContainer.appendChild(item);
                            });
                        } else {
                            reportsSection.style.display = 'none';
                        }
                        
                        // 3. Render các nút hành động nhanh ở chân modal
                        const actionsContainer = document.getElementById('adm-modal-actions');
                        actionsContainer.innerHTML = '';
                        
                        // Nút Đóng (Hủy)
                        const closeBtn = document.createElement('button');
                        closeBtn.type = 'button';
                        closeBtn.className = 'posts-button posts-button--secondary';
                        closeBtn.style.height = '36px';
                        closeBtn.innerText = 'Đóng';
                        closeBtn.onclick = closeAdminPostModal;
                        
                        // Nút Bỏ qua báo cáo (nếu có báo cáo và được cho phép hành động)
                        if (reportsCount > 0 && showActions) {
                            const dismissForm = document.createElement('form');
                            dismissForm.action = `/admin/posts/${post.id}/dismiss-reports`;
                            dismissForm.method = 'POST';
                            dismissForm.style.display = 'inline';
                            dismissForm.onsubmit = function() {
                                return confirm('Bạn có chắc chắn muốn bỏ qua và gỡ bỏ toàn bộ lượt báo cáo của bài viết này?');
                            };
                            
                            dismissForm.innerHTML = `
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <button type="submit" class="admin-action-btn admin-action-btn--dismiss" style="height:36px; padding: 0 1rem; border-radius:6px;">
                                    <i class="fa-solid fa-shield-halved"></i> Bỏ qua báo cáo
                                </button>
                            `;
                            actionsContainer.appendChild(dismissForm);
                        }
                        
                        // Nút Ẩn/Hiện bài viết (chỉ nếu được cho phép hành động)
                        if (showActions) {
                            const toggleForm = document.createElement('form');
                            toggleForm.action = `/admin/posts/${post.id}/toggle-status`;
                            toggleForm.method = 'POST';
                            toggleForm.style.display = 'inline';
                            toggleForm.onsubmit = function() {
                                return confirm('Bạn có chắc chắn muốn thay đổi trạng thái hiển thị của bài đăng này?');
                            };
                            
                            const toggleBtnHtml = post.status === 'published' 
                                ? `<button type="submit" class="admin-action-btn admin-action-btn--toggle" style="height:36px; padding: 0 1rem; border-radius:6px; margin: 0;"><i class="fa-solid fa-eye-slash"></i> Ẩn bài viết</button>`
                                : `<button type="submit" class="admin-action-btn admin-action-btn--toggle" style="height:36px; padding: 0 1rem; border-radius:6px; color:#4f46e5; border-color:#c7d2fe; margin: 0;"><i class="fa-solid fa-eye"></i> Hiện bài viết</button>`;
                                
                            toggleForm.innerHTML = `
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                ${toggleBtnHtml}
                            `;
                            actionsContainer.appendChild(toggleForm);
                        }
                        
                        // Nút Xóa vĩnh viễn (chỉ nếu được cho phép hành động)
                        if (showActions) {
                            const deleteForm = document.createElement('form');
                            deleteForm.action = `/admin/posts/${post.id}`;
                            deleteForm.method = 'POST';
                            deleteForm.style.display = 'inline';
                            deleteForm.onsubmit = function() {
                                return confirm('Hành động này sẽ xóa vĩnh viễn bài đăng và tất cả các ảnh/bình luận liên quan. Bạn có chắc chắn muốn tiếp tục?');
                            };
                            
                            deleteForm.innerHTML = `
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="admin-action-btn admin-action-btn--delete" style="height:36px; padding: 0 1rem; border-radius:6px; margin: 0;">
                                    <i class="fa-solid fa-trash-can"></i> Xóa vĩnh viễn
                                </button>
                            `;
                            actionsContainer.appendChild(deleteForm);
                        }
                        actionsContainer.appendChild(closeBtn);
                    }
                })
                .catch(error => {
                    document.getElementById('adm-modal-title-text').innerText = 'Lỗi tải dữ liệu';
                    document.getElementById('adm-post-content-text').innerText = 'Không thể lấy được thông tin chi tiết bài viết này. Vui lòng thử lại!';
                    document.getElementById('adm-reports-list-container').innerHTML = '<p style="color: #dc2626; font-size: 0.85rem; margin: 0;">Có lỗi xảy ra khi kết nối tới hệ thống.</p>';
                });
        }

        function closeAdminPostModal() {
            const modal = document.getElementById('adminPostDetailModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }

        function handleAdminOutsideClick(event) {
            if (event.target === document.getElementById('adminPostDetailModal')) {
                closeAdminPostModal();
            }
        }
    </script>
@endsection
