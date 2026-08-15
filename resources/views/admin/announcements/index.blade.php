@extends('layouts.admin.master')

@section('page_title', 'Quản lý thông báo BQL')

@push('styles')
    @vite(['resources/css/pages/admin/announcements/index.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom Modal Styles */
        .announcement-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(4px);
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .announcement-modal-content {
            background-color: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            transform: scale(0.95);
            animation: scaleIn 0.2s ease-out forwards;
        }

        @keyframes scaleIn {
            to { transform: scale(1); }
        }

        .announcement-modal-header {
            padding: 32px 24px 16px;
            text-align: center;
        }

        .modal-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
        }

        .modal-icon.warning {
            background-color: #fef2f2;
            color: #ef4444;
        }

        .announcement-modal-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }

        .announcement-modal-body {
            padding: 0 32px 24px;
            text-align: center;
        }

        .announcement-modal-body p {
            margin: 0;
            font-size: 15px;
            color: #475569;
            line-height: 1.6;
        }

        .announcement-modal-footer {
            padding: 20px 24px;
            background-color: #f8fafc;
            display: flex;
            gap: 12px;
            justify-content: center;
            border-top: 1px solid #e2e8f0;
        }

        .announcement-modal-footer .announcements-btn {
            flex: 1;
            justify-content: center;
            font-size: 14px;
            padding: 10px 16px;
        }

        .announcements-btn--danger {
            background-color: #ef4444;
            color: white;
            border: none;
            transition: all 0.2s ease;
        }

        .announcements-btn--danger:hover {
            background-color: #dc2626;
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('content')
    <div class="announcements-page">
        {{-- Header --}}
        <div class="announcements-page__header">
            <div>
                <p class="announcements-page__eyebrow">Tương tác & Bảng tin</p>
                <h1>Bảng tin chung cư (Thông báo BQL)</h1>
            </div>
            <div>
                <a href="{{ portal_route('announcements.create') }}" class="announcements-btn announcements-btn--primary">
                    <i class="fa-solid fa-plus"></i> Soạn thông báo mới
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('success'))
            <div class="announcements-alert announcements-alert--success">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Quick Stats --}}
        <div class="announcements-stats">
            <div class="announcements-stat-card">
                <div class="announcements-stat-icon announcements-stat-icon--total">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>
                <div class="announcements-stat-details">
                    <span class="announcements-stat-count">{{ $totalCount }}</span>
                    <span class="announcements-stat-label">Tổng thông báo</span>
                </div>
            </div>
            <div class="announcements-stat-card">
                <div class="announcements-stat-icon announcements-stat-icon--published">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>
                <div class="announcements-stat-details">
                    <span class="announcements-stat-count">{{ $publishedCount }}</span>
                    <span class="announcements-stat-label">Đang xuất bản</span>
                </div>
            </div>
            <div class="announcements-stat-card">
                <div class="announcements-stat-icon announcements-stat-icon--pinned">
                    <i class="fa-solid fa-thumbtack"></i>
                </div>
                <div class="announcements-stat-details">
                    <span class="announcements-stat-count">{{ $pinnedCount }}</span>
                    <span class="announcements-stat-label">Đang ghim</span>
                </div>
            </div>
            <div class="announcements-stat-card">
                <div class="announcements-stat-icon announcements-stat-icon--maintenance">
                    <i class="fa-solid fa-wrench"></i>
                </div>
                <div class="announcements-stat-details">
                    <span class="announcements-stat-count">{{ $maintenanceCount }}</span>
                    <span class="announcements-stat-label">Lịch bảo trì</span>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <form class="announcements-filter" method="GET" action="{{ portal_route('announcements.index') }}">
            <div class="announcements-filter__field">
                <span>Tìm kiếm</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập tiêu đề hoặc nội dung...">
            </div>

            <div class="announcements-filter__field">
                <span>Phân loại</span>
                <select name="category">
                    <option value="">Tất cả phân loại</option>
                    <option value="maintenance" @selected(request('category') === 'maintenance')>Bảo trì kỹ thuật</option>
                    <option value="warning" @selected(request('category') === 'warning')>Cảnh báo khẩn cấp</option>
                    <option value="general" @selected(request('category') === 'general')>Tin tức chung</option>
                    <option value="event" @selected(request('category') === 'event')>Sự kiện chung cư</option>
                </select>
            </div>

            <div class="announcements-filter__field">
                <span>Trạng thái</span>
                <select name="status">
                    <option value="">Tất cả trạng thái</option>
                    <option value="published" @selected(request('status') === 'published')>Đang hoạt động</option>
                    <option value="draft" @selected(request('status') === 'draft')>Bản nháp</option>
                    <option value="archived" @selected(request('status') === 'archived')>Đã lưu trữ</option>
                </select>
            </div>

            <div class="announcements-filter__actions">
                <button type="submit" class="announcements-btn announcements-btn--primary">Lọc</button>
                <a href="{{ portal_route('announcements.index') }}" class="announcements-btn">Xóa lọc</a>
            </div>
        </form>

        {{-- Table Card --}}
        <div class="announcements-table-card">
            <div class="announcements-table-wrap">
                <table class="announcements-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">Ghim</th>
                            <th>Thông báo</th>
                            <th>Phân loại</th>
                            <th>Trạng thái</th>
                            <th>Đăng bởi</th>
                            <th>Ngày tạo</th>
                            <th style="text-align: center; width: 120px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($announcements as $announcement)
                            <tr>
                                <td style="text-align: center;">
                                    <button type="button" 
                                            class="pin-action-btn {{ $announcement->pinned ? 'pin-action-btn--active' : 'pin-action-btn--inactive' }}" 
                                            onclick="togglePin({{ $announcement->id }}, this)" 
                                            title="{{ $announcement->pinned ? 'Bỏ ghim thông báo' : 'Ghim thông báo lên đầu' }}">
                                        <i class="fa-solid fa-thumbtack"></i>
                                    </button>
                                </td>
                                <td>
                                    <div class="announcement-title-cell">
                                        @if ($announcement->image_path)
                                            <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="Banner" class="announcement-banner-thumb">
                                        @else
                                            <div class="announcement-banner-thumb" style="display: flex; align-items: center; justify-content: center; background: #e2e8f0; color: #94a3b8;">
                                                <i class="fa-regular fa-image" style="font-size: 14px;"></i>
                                            </div>
                                        @endif
                                        <div class="announcement-info-wrap">
                                            <a href="{{ portal_route('announcements.edit', $announcement->id) }}" class="announcement-title-link">
                                                {{ $announcement->title }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge category-badge--{{ $announcement->category }}">
                                        @if($announcement->category === 'maintenance')
                                            <i class="fa-solid fa-wrench"></i> Bảo trì
                                        @elseif($announcement->category === 'warning')
                                            <i class="fa-solid fa-triangle-exclamation"></i> Cảnh báo
                                        @elseif($announcement->category === 'event')
                                            <i class="fa-solid fa-calendar-days"></i> Sự kiện
                                        @else
                                            <i class="fa-solid fa-circle-info"></i> Tin chung
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="status-pill status-pill--{{ $announcement->status }}">
                                        <span class="status-dot"></span>
                                        @if($announcement->status === 'published')
                                            Công bố
                                        @elseif($announcement->status === 'draft')
                                            Bản nháp
                                        @else
                                            Lưu trữ
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $announcement->user->name ?? 'BQL' }}</td>
                                <td>{{ $announcement->created_at->format('H:i d/m/Y') }}</td>
                                <td style="text-align: center;">
                                    <div class="announcements-actions-cell">
                                        <a href="{{ portal_route('announcements.edit', $announcement->id) }}" class="action-btn action-btn--edit" title="Chỉnh sửa">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ portal_route('announcements.destroy', $announcement->id) }}" method="POST" onsubmit="confirmDelete(event, this)" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn action-btn--delete" title="Xóa">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="announcements-empty">
                                        <i class="fa-regular fa-folder-open" style="font-size: 32px; color: #cbd5e1; margin-bottom: 8px; display: block;"></i>
                                        Không tìm thấy thông báo nào phù hợp.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="announcements-table-footer">
                <div class="announcements-table-footer__stats">
                    Hiển thị {{ $announcements->count() }} trên {{ $announcements->total() }} thông báo
                </div>
                <div>
                    {{ $announcements->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Custom Delete Modal --}}
    <div id="deleteModal" class="announcement-modal">
        <div class="announcement-modal-content">
            <div class="announcement-modal-header">
                <div class="modal-icon warning">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3>Xác nhận xóa</h3>
            </div>
            <div class="announcement-modal-body">
                <p>Bạn có chắc chắn muốn xóa thông báo này? Hành động này không thể hoàn tác!</p>
            </div>
            <div class="announcement-modal-footer">
                <button type="button" class="announcements-btn" onclick="closeDeleteModal()">Hủy bỏ</button>
                <button type="button" class="announcements-btn announcements-btn--danger" id="confirmDeleteBtn">Xóa thông báo</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePin(id, button) {
            fetch(`/admin/announcements/${id}/toggle-pin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.pinned) {
                        button.classList.remove('pin-action-btn--inactive');
                        button.classList.add('pin-action-btn--active');
                        button.setAttribute('title', 'Bỏ ghim thông báo');
                    } else {
                        button.classList.remove('pin-action-btn--active');
                        button.classList.add('pin-action-btn--inactive');
                        button.setAttribute('title', 'Ghim thông báo lên đầu');
                    }
                }
            })
            .catch(err => {
                console.error('Error toggling pin status:', err);
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            });
        }

        // Delete Modal Logic
        let currentFormToSubmit = null;

        function confirmDelete(event, formElement) {
            event.preventDefault(); // Prevent native form submission
            currentFormToSubmit = formElement;
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'flex';
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.style.display = 'none';
            currentFormToSubmit = null;
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentFormToSubmit) {
                currentFormToSubmit.submit();
            }
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target === modal) {
                closeDeleteModal();
            }
        });
    </script>
@endpush
