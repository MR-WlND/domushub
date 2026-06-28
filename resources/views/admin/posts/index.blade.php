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
        'marketplace' => 'Thanh lý',
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
            <a href="{{ route('admin.posts.index', ['tab' => 'banned_users']) }}" class="posts-page__tab {{ $activeTab === 'banned_users' ? 'posts-page__tab--active' : '' }}">
                <i class="fa-solid fa-user-slash"></i> Cư dân bị khóa
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
                                            
                                            @if($post->user && $post->user->role === 'resident')
                                                <div class="ban-controls" style="display: flex; gap: 0.5rem; margin-top: 0.4rem; flex-wrap: wrap;">
                                                    {{-- Form khóa/mở đăng bài --}}
                                                    <form action="{{ route('admin.users.ban-posting', $post->user->id) }}" method="POST" style="display: inline-flex; align-items: center; position: relative;">
                                                        @csrf
                                                        <i class="fa-regular fa-pen-to-square" style="position: absolute; left: 6px; color: #64748b; pointer-events: none; font-size: 0.75rem;"></i>
                                                        <select name="duration" onchange="if(confirm('Bạn có chắc chắn muốn thực hiện hành động này?')) this.form.submit(); else this.selectedIndex=0;" style="font-size: 0.725rem; padding: 3px 6px 3px 20px; border-radius: 4px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; color: var(--color-text-secondary); font-weight: 600;" title="Khóa quyền đăng bài viết">
                                                            <option value="" disabled selected>Đăng bài: {{ $post->user->isBannedPosting() ? 'Bị khóa' : 'Mở' }}</option>
                                                            @if($post->user->isBannedPosting())
                                                                <option value="unban">Mở khóa đăng bài</option>
                                                            @else
                                                                <option value="1">Khóa đăng bài 1 ngày</option>
                                                                <option value="3">Khóa đăng bài 3 ngày</option>
                                                                <option value="7">Khóa đăng bài 7 ngày</option>
                                                                <option value="30">Khóa đăng bài 30 ngày</option>
                                                                <option value="permanent">Khóa đăng bài vĩnh viễn</option>
                                                            @endif
                                                        </select>
                                                    </form>

                                                    {{-- Form khóa/mở bình luận --}}
                                                    <form action="{{ route('admin.users.ban-commenting', $post->user->id) }}" method="POST" style="display: inline-flex; align-items: center; position: relative;">
                                                        @csrf
                                                        <i class="fa-regular fa-comment" style="position: absolute; left: 6px; color: #64748b; pointer-events: none; font-size: 0.75rem;"></i>
                                                        <select name="duration" onchange="if(confirm('Bạn có chắc chắn muốn thực hiện hành động này?')) this.form.submit(); else this.selectedIndex=0;" style="font-size: 0.725rem; padding: 3px 6px 3px 20px; border-radius: 4px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; color: var(--color-text-secondary); font-weight: 600;" title="Khóa quyền bình luận">
                                                            <option value="" disabled selected>Bình luận: {{ $post->user->isBannedCommenting() ? 'Bị khóa' : 'Mở' }}</option>
                                                            @if($post->user->isBannedCommenting())
                                                                <option value="unban">Mở khóa bình luận</option>
                                                            @else
                                                                <option value="1">Khóa bình luận 1 ngày</option>
                                                                <option value="3">Khóa bình luận 3 ngày</option>
                                                                <option value="7">Khóa bình luận 7 ngày</option>
                                                                <option value="30">Khóa bình luận 30 ngày</option>
                                                                <option value="permanent">Khóa bình luận vĩnh viễn</option>
                                                            @endif
                                                        </select>
                                                    </form>
                                                </div>
                                            @endif
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
                                        @if($post->trashed())
                                            <span class="status-pill status-pill--hidden" style="background-color: #fee2e2; color: #991b1b;">
                                                <span class="status-dot" style="background-color: #991b1b;"></span>
                                                Đã bị xóa
                                            </span>
                                        @else
                                            <span class="status-pill status-pill--{{ $post->status }}">
                                                <span class="status-dot"></span>
                                                {{ $statusLabels[$post->status] ?? $post->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="admin-actions">
                                            @if($post->trashed())
                                                {{-- Nút Khôi phục bài viết --}}
                                                <form action="{{ route('admin.posts.restore', $post->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="admin-action-btn admin-action-btn--toggle" style="color: #00236f; border-color: #b3c5eb;">
                                                        <i class="fa-solid fa-rotate-left"></i> Khôi phục bài viết
                                                    </button>
                                                </form>
                                            @else
                                                {{-- Nút Ẩn/Hiện bài viết --}}
                                                <form action="{{ route('admin.posts.toggle-status', $post->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái hiển thị của bài đăng này?')">
                                                    @csrf
                                                    @if ($post->status === 'published')
                                                        <button type="submit" class="admin-action-btn admin-action-btn--toggle">
                                                            <i class="fa-solid fa-eye-slash"></i> Ẩn bài viết
                                                        </button>
                                                    @else
                                                        <button type="submit" class="admin-action-btn admin-action-btn--toggle" style="color: #00236f; border-color: #b3c5eb;">
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

                                                {{-- Nút Xóa bài viết --}}
                                                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Hành động này sẽ ẩn bài đăng khỏi phía cư dân nhưng vẫn giữ lại lịch sử báo cáo cho Admin. Bạn có chắc chắn muốn tiếp tục?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="admin-action-btn admin-action-btn--delete">
                                                        <i class="fa-solid fa-trash-can"></i> Xóa bài viết
                                                    </button>
                                                </form>
                                            @endif
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
        @elseif($activeTab === 'comments')
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
                            
                            <div class="violator-content" @if($comment->trashed()) style="background: #f8fafc; color: #64748b; text-decoration: line-through; border-left: 3px solid #cbd5e1;" @endif>
                                @if($comment->trashed())
                                    <span style="color: #dc2626; font-weight: 700; font-size: 0.725rem; background: #fee2e2; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 0.25rem; margin-right: 0.5rem; text-decoration: none !important;">
                                        <i class="fa-solid fa-trash-can"></i> Đã bị xóa (Vi phạm)
                                    </span>
                                @endif
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
                            <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-action-btn admin-action-btn--delete" style="padding: 0.5rem 1rem;">
                                    <i class="fa-solid fa-trash-can"></i> Xóa bình luận
                                </button>
                            </form>

                            @if($comment->user && $comment->user->role === 'resident')
                                {{-- Form khóa/mở đăng bài --}}
                                <form action="{{ route('admin.users.ban-posting', $comment->user->id) }}" method="POST" style="display: inline-flex; align-items: center; position: relative;">
                                    @csrf
                                    <i class="fa-regular fa-pen-to-square" style="position: absolute; left: 10px; color: #64748b; pointer-events: none; font-size: 0.875rem;"></i>
                                    <select name="duration" onchange="if(confirm('Bạn có chắc chắn muốn thực hiện hành động này?')) this.form.submit(); else this.selectedIndex=0;" style="font-size: 0.825rem; padding: 0.5rem 0.5rem 0.5rem 28px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; height: 38px; color: var(--color-text-secondary); font-weight: 600;" title="Khóa quyền đăng bài viết">
                                        <option value="" disabled selected>Đăng bài: {{ $comment->user->isBannedPosting() ? 'Bị khóa' : 'Mở' }}</option>
                                        @if($comment->user->isBannedPosting())
                                            <option value="unban">Mở khóa đăng bài</option>
                                        @else
                                            <option value="1">Khóa đăng bài 1 ngày</option>
                                            <option value="3">Khóa đăng bài 3 ngày</option>
                                            <option value="7">Khóa đăng bài 7 ngày</option>
                                            <option value="30">Khóa đăng bài 30 ngày</option>
                                            <option value="permanent">Khóa đăng bài vĩnh viễn</option>
                                        @endif
                                    </select>
                                </form>

                                {{-- Form khóa/mở bình luận --}}
                                <form action="{{ route('admin.users.ban-commenting', $comment->user->id) }}" method="POST" style="display: inline-flex; align-items: center; position: relative;">
                                    @csrf
                                    <i class="fa-regular fa-comment" style="position: absolute; left: 10px; color: #64748b; pointer-events: none; font-size: 0.875rem;"></i>
                                    <select name="duration" onchange="if(confirm('Bạn có chắc chắn muốn thực hiện hành động này?')) this.form.submit(); else this.selectedIndex=0;" style="font-size: 0.825rem; padding: 0.5rem 0.5rem 0.5rem 28px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; height: 38px; color: var(--color-text-secondary); font-weight: 600;" title="Khóa quyền bình luận">
                                        <option value="" disabled selected>Bình luận: {{ $comment->user->isBannedCommenting() ? 'Bị khóa' : 'Mở' }}</option>
                                        @if($comment->user->isBannedCommenting())
                                            <option value="unban">Mở khóa bình luận</option>
                                        @else
                                            <option value="1">Khóa bình luận 1 ngày</option>
                                            <option value="3">Khóa bình luận 3 ngày</option>
                                            <option value="7">Khóa bình luận 7 ngày</option>
                                            <option value="30">Khóa bình luận 30 ngày</option>
                                            <option value="permanent">Khóa bình luận vĩnh viễn</option>
                                        @endif
                                    </select>
                                </form>
                            @endif
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
        @elseif($activeTab === 'banned_users')
            {{-- BỘ LỌC TÌM KIẾM CƯ DÂN BỊ KHÓA --}}
            <form class="posts-filter" method="GET" action="{{ route('admin.posts.index') }}">
                <input type="hidden" name="tab" value="banned_users">
                <label class="posts-filter__field" style="flex: 2;">
                    <span>Tìm kiếm cư dân bị khóa</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Tên cư dân, email hoặc số điện thoại...">
                </label>
                <div class="posts-filter__actions" style="margin-top: auto; align-self: flex-end;">
                    <button type="submit" class="posts-button posts-button--primary">Lọc</button>
                    <a href="{{ route('admin.posts.index', ['tab' => 'banned_users']) }}" class="posts-button posts-button--secondary">Xóa lọc</a>
                </div>
            </form>

            {{-- BẢNG DANH SÁCH CƯ DÂN BỊ KHÓA --}}
            <div class="posts-table-card">
                <div class="posts-table-wrap">
                    <table class="posts-table">
                        <thead>
                            <tr>
                                <th>Cư dân</th>
                                <th>Khóa đăng bài</th>
                                <th>Khóa bình luận</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($bannedUsers as $user)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            @if($user->avatar)
                                                <img src="/storage/{{ $user->avatar }}" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1.5px solid #e2e8f0;">
                                            @else
                                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #475569; border: 1.5px solid #e2e8f0;">
                                                    {{ mb_substr($user->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div style="font-weight: 700; color: #0f172a;">{{ $user->name }}</div>
                                                <div style="font-size: 0.775rem; color: #64748b; margin-top: 0.1rem;">
                                                    @if($user->apartment)
                                                        Căn: <strong>{{ $user->apartment->apartment_number }}</strong>
                                                    @else
                                                        Căn: <em>Chưa nhận</em>
                                                    @endif
                                                </div>
                                                <div style="font-size: 0.725rem; color: #94a3b8; margin-top: 0.1rem;">
                                                    {{ $user->email }} | SĐT: {{ $user->phone ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->isBannedPosting())
                                            @php
                                                $now = now();
                                                $postingUntil = \Carbon\Carbon::parse($user->banned_posting_until);
                                                $diffDays = $now->diffInDays($postingUntil);
                                                
                                                if ($diffDays > 30000) {
                                                    $postingRemaining = 'Bị khóa vĩnh viễn';
                                                    $postingBadgeClass = 'status-pill--hidden';
                                                } elseif ($diffDays >= 1) {
                                                    $postingRemaining = 'Bị khóa (Còn ' . $diffDays . ' ngày)';
                                                    $postingBadgeClass = 'status-pill--hidden';
                                                } else {
                                                    $diffHours = $now->diffInHours($postingUntil);
                                                    if ($diffHours >= 1) {
                                                        $postingRemaining = 'Bị khóa (Còn ' . $diffHours . ' giờ)';
                                                        $postingBadgeClass = 'status-pill--hidden';
                                                    } else {
                                                        $diffMins = $now->diffInMinutes($postingUntil);
                                                        $postingRemaining = 'Bị khóa (Còn ' . ($diffMins > 0 ? $diffMins : 1) . ' phút)';
                                                        $postingBadgeClass = 'status-pill--hidden';
                                                    }
                                                }
                                            @endphp
                                            <span class="status-pill {{ $postingBadgeClass }}">
                                                <span class="status-dot"></span>
                                                {{ $postingRemaining }}
                                            </span>
                                            <div style="font-size: 0.725rem; color: #64748b; margin-top: 0.25rem;">
                                                Đến: {{ $postingUntil->format('H:i d/m/Y') }}
                                            </div>
                                        @else
                                            <span class="status-pill status-pill--published">
                                                <span class="status-dot"></span>
                                                Hoạt động
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->isBannedCommenting())
                                            @php
                                                $now = now();
                                                $commentingUntil = \Carbon\Carbon::parse($user->banned_commenting_until);
                                                $diffDays = $now->diffInDays($commentingUntil);
                                                
                                                if ($diffDays > 30000) {
                                                    $commentingRemaining = 'Bị khóa vĩnh viễn';
                                                    $commentingBadgeClass = 'status-pill--hidden';
                                                } elseif ($diffDays >= 1) {
                                                    $commentingRemaining = 'Bị khóa (Còn ' . $diffDays . ' ngày)';
                                                    $commentingBadgeClass = 'status-pill--hidden';
                                                } else {
                                                    $diffHours = $now->diffInHours($commentingUntil);
                                                    if ($diffHours >= 1) {
                                                        $commentingRemaining = 'Bị khóa (Còn ' . $diffHours . ' giờ)';
                                                        $commentingBadgeClass = 'status-pill--hidden';
                                                    } else {
                                                        $diffMins = $now->diffInMinutes($commentingUntil);
                                                        $commentingRemaining = 'Bị khóa (Còn ' . ($diffMins > 0 ? $diffMins : 1) . ' phút)';
                                                        $commentingBadgeClass = 'status-pill--hidden';
                                                    }
                                                }
                                            @endphp
                                            <span class="status-pill {{ $commentingBadgeClass }}">
                                                <span class="status-dot"></span>
                                                {{ $commentingRemaining }}
                                            </span>
                                            <div style="font-size: 0.725rem; color: #64748b; margin-top: 0.25rem;">
                                                Đến: {{ $commentingUntil->format('H:i d/m/Y') }}
                                            </div>
                                        @else
                                            <span class="status-pill status-pill--published">
                                                <span class="status-dot"></span>
                                                Hoạt động
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="ban-controls" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            {{-- Form thay đổi hoặc mở khóa đăng bài --}}
                                            <form action="{{ route('admin.users.ban-posting', $user->id) }}" method="POST" style="display: inline-flex; align-items: center; position: relative;">
                                                @csrf
                                                <i class="fa-regular fa-pen-to-square" style="position: absolute; left: 10px; color: #64748b; pointer-events: none; font-size: 0.875rem;"></i>
                                                <select name="duration" onchange="if(confirm('Bạn có chắc chắn muốn thực hiện hành động này?')) this.form.submit(); else this.selectedIndex=0;" style="font-size: 0.825rem; padding: 0.4rem 0.5rem 0.4rem 28px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; color: var(--color-text-secondary); font-weight: 600;" title="Quản lý quyền đăng bài">
                                                    <option value="" disabled selected>Đăng bài: {{ $user->isBannedPosting() ? 'Bị khóa' : 'Mở' }}</option>
                                                    @if($user->isBannedPosting())
                                                        <option value="unban">Mở khóa đăng bài</option>
                                                        <option value="1">Gia hạn khóa 1 ngày</option>
                                                        <option value="3">Gia hạn khóa 3 ngày</option>
                                                        <option value="7">Gia hạn khóa 7 ngày</option>
                                                        <option value="30">Gia hạn khóa 30 ngày</option>
                                                        <option value="permanent">Gia hạn khóa vĩnh viễn</option>
                                                    @else
                                                        <option value="1">Khóa đăng bài 1 ngày</option>
                                                        <option value="3">Khóa đăng bài 3 ngày</option>
                                                        <option value="7">Khóa đăng bài 7 ngày</option>
                                                        <option value="30">Khóa đăng bài 30 ngày</option>
                                                        <option value="permanent">Khóa đăng bài vĩnh viễn</option>
                                                    @endif
                                                </select>
                                            </form>

                                            {{-- Form thay đổi hoặc mở khóa bình luận --}}
                                            <form action="{{ route('admin.users.ban-commenting', $user->id) }}" method="POST" style="display: inline-flex; align-items: center; position: relative;">
                                                @csrf
                                                <i class="fa-regular fa-comment" style="position: absolute; left: 10px; color: #64748b; pointer-events: none; font-size: 0.875rem;"></i>
                                                <select name="duration" onchange="if(confirm('Bạn có chắc chắn muốn thực hiện hành động này?')) this.form.submit(); else this.selectedIndex=0;" style="font-size: 0.825rem; padding: 0.4rem 0.5rem 0.4rem 28px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; color: var(--color-text-secondary); font-weight: 600;" title="Quản lý quyền bình luận">
                                                    <option value="" disabled selected>Bình luận: {{ $user->isBannedCommenting() ? 'Bị khóa' : 'Mở' }}</option>
                                                    @if($user->isBannedCommenting())
                                                        <option value="unban">Mở khóa bình luận</option>
                                                        <option value="1">Gia hạn khóa 1 ngày</option>
                                                        <option value="3">Gia hạn khóa 3 ngày</option>
                                                        <option value="7">Gia hạn khóa 7 ngày</option>
                                                        <option value="30">Gia hạn khóa 30 ngày</option>
                                                        <option value="permanent">Gia hạn khóa vĩnh viễn</option>
                                                    @else
                                                        <option value="1">Khóa bình luận 1 ngày</option>
                                                        <option value="3">Khóa bình luận 3 ngày</option>
                                                        <option value="7">Khóa bình luận 7 ngày</option>
                                                        <option value="30">Khóa bình luận 30 ngày</option>
                                                        <option value="permanent">Khóa bình luận vĩnh viễn</option>
                                                    @endif
                                                </select>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="posts-empty">Hiện tại không có cư dân nào bị khóa quyền tương tác.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PHÂN TRANG CƯ DÂN BỊ KHÓA --}}
                @if($bannedUsers->total() > 0)
                    <div class="posts-table-footer">
                        <div class="posts-table-footer__stats">
                            Hiển thị {{ $bannedUsers->count() }} trên {{ $bannedUsers->total() }} cư dân
                        </div>
                        <div>
                            {{ $bannedUsers->appends(request()->query())->links('admin.users.pagination') }}
                        </div>
                    </div>
                @endif
            </div>
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

                        if (post.user && post.user.role === 'resident' && showActions) {
                            // Tạo form ban đăng bài
                            const banPostingForm = document.createElement('form');
                            banPostingForm.action = `/admin/users/${post.user.id}/ban-posting`;
                            banPostingForm.method = 'POST';
                            banPostingForm.style.display = 'inline-flex';
                            banPostingForm.style.alignItems = 'center';
                            banPostingForm.style.position = 'relative';
                            
                            const isBannedPosting = data.is_banned_posting;
                            
                            banPostingForm.innerHTML = `
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <i class="fa-regular fa-pen-to-square" style="position: absolute; left: 10px; color: #64748b; pointer-events: none; font-size: 0.875rem;"></i>
                                <select name="duration" onchange="if(confirm('Bạn có chắc chắn muốn thực hiện hành động này?')) this.form.submit(); else this.selectedIndex=0;" style="font-size: 0.825rem; padding: 0 0.5rem 0 28px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; height: 36px; color: var(--color-text-secondary); font-weight: 600;" title="Khóa quyền đăng bài viết">
                                    <option value="" disabled selected>Đăng bài: ${isBannedPosting ? 'Bị khóa' : 'Mở'}</option>
                                    ${isBannedPosting 
                                        ? '<option value="unban">Mở khóa đăng bài</option>' 
                                        : '<option value="1">Khóa đăng bài 1 ngày</option><option value="3">Khóa đăng bài 3 ngày</option><option value="7">Khóa đăng bài 7 ngày</option><option value="30">Khóa đăng bài 30 ngày</option><option value="permanent">Khóa đăng bài vĩnh viễn</option>'
                                    }
                                </select>
                            `;
                            actionsContainer.appendChild(banPostingForm);

                            // Tạo form ban bình luận
                            const banCommentingForm = document.createElement('form');
                            banCommentingForm.action = `/admin/users/${post.user.id}/ban-commenting`;
                            banCommentingForm.method = 'POST';
                            banCommentingForm.style.display = 'inline-flex';
                            banCommentingForm.style.alignItems = 'center';
                            banCommentingForm.style.position = 'relative';
                            
                            const isBannedCommenting = data.is_banned_commenting;
                            
                            banCommentingForm.innerHTML = `
                                <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                <i class="fa-regular fa-comment" style="position: absolute; left: 10px; color: #64748b; pointer-events: none; font-size: 0.875rem;"></i>
                                <select name="duration" onchange="if(confirm('Bạn có chắc chắn muốn thực hiện hành động này?')) this.form.submit(); else this.selectedIndex=0;" style="font-size: 0.825rem; padding: 0 0.5rem 0 28px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; height: 36px; color: var(--color-text-secondary); font-weight: 600;" title="Khóa quyền bình luận">
                                    <option value="" disabled selected>Bình luận: ${isBannedCommenting ? 'Bị khóa' : 'Mở'}</option>
                                    ${isBannedCommenting 
                                        ? '<option value="unban">Mở khóa bình luận</option>' 
                                        : '<option value="1">Khóa bình luận 1 ngày</option><option value="3">Khóa bình luận 3 ngày</option><option value="7">Khóa bình luận 7 ngày</option><option value="30">Khóa bình luận 30 ngày</option><option value="permanent">Khóa bình luận vĩnh viễn</option>'
                                    }
                                </select>
                            `;
                            actionsContainer.appendChild(banCommentingForm);
                        }
                        
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
                        
                        // Nút Ẩn/Hiện/Khôi phục & Xóa bài viết (chỉ nếu được cho phép hành động)
                        if (showActions) {
                            if (post.deleted_at !== null) {
                                // Nút Khôi phục bài viết
                                const restoreForm = document.createElement('form');
                                restoreForm.action = `/admin/posts/${post.id}/restore`;
                                restoreForm.method = 'POST';
                                restoreForm.style.display = 'inline';
                                restoreForm.innerHTML = `
                                    <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                    <button type="submit" class="admin-action-btn admin-action-btn--toggle" style="height:36px; padding: 0 1rem; border-radius:6px; color:#00236f; border-color:#b3c5eb; margin: 0;">
                                        <i class="fa-solid fa-rotate-left"></i> Khôi phục bài viết
                                    </button>
                                `;
                                actionsContainer.appendChild(restoreForm);
                            } else {
                                // Nút Ẩn/Hiện bài viết
                                const toggleForm = document.createElement('form');
                                toggleForm.action = `/admin/posts/${post.id}/toggle-status`;
                                toggleForm.method = 'POST';
                                toggleForm.style.display = 'inline';
                                toggleForm.onsubmit = function() {
                                    return confirm('Bạn có chắc chắn muốn thay đổi trạng thái hiển thị của bài đăng này?');
                                };
                                
                                const toggleBtnHtml = post.status === 'published' 
                                    ? `<button type="submit" class="admin-action-btn admin-action-btn--toggle" style="height:36px; padding: 0 1rem; border-radius:6px; margin: 0;"><i class="fa-solid fa-eye-slash"></i> Ẩn bài viết</button>`
                                    : `<button type="submit" class="admin-action-btn admin-action-btn--toggle" style="height:36px; padding: 0 1rem; border-radius:6px; color:#00236f; border-color:#b3c5eb; margin: 0;"><i class="fa-solid fa-eye"></i> Hiện bài viết</button>`;
                                    
                                toggleForm.innerHTML = `
                                    <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                    ${toggleBtnHtml}
                                `;
                                actionsContainer.appendChild(toggleForm);

                                // Nút Xóa bài viết (soft delete)
                                const deleteForm = document.createElement('form');
                                deleteForm.action = `/admin/posts/${post.id}`;
                                deleteForm.method = 'POST';
                                deleteForm.style.display = 'inline';
                                deleteForm.onsubmit = function() {
                                    return confirm('Hành động này sẽ ẩn bài đăng khỏi phía cư dân nhưng vẫn giữ lại lịch sử báo cáo cho Admin. Bạn có chắc chắn muốn tiếp tục?');
                                };
                                
                                deleteForm.innerHTML = `
                                    <input type="hidden" name="_token" value="${document.querySelector('input[name="_token"]').value}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="admin-action-btn admin-action-btn--delete" style="height:36px; padding: 0 1rem; border-radius:6px; margin: 0;">
                                        <i class="fa-solid fa-trash-can"></i> Xóa bài viết
                                    </button>
                                `;
                                actionsContainer.appendChild(deleteForm);
                            }
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
