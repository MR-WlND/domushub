@extends('layouts.admin.master')

@section('page_title', 'Nhiệm vụ của tôi')
@section('page_kicker', 'Kỹ thuật viên')
@section('role_title', 'Kỹ thuật viên')
@section('home_route', route('admin.tickets.my-tasks'))
@section('user_name', auth()->user()->name ?? 'KTV')
@section('user_role', 'technician')

@push('styles')
    @vite(['resources/css/pages/admin/tickets/index.css', 'resources/css/pages/admin/tickets/technician.css'])
@endpush

@section('content')
<div class="ktv-page">

    {{-- Header --}}
    <div class="ktv-header">
        <div class="ktv-header__left">
            <div class="ktv-header__avatar">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"/></svg>
            </div>
            <div>
                <h1 class="ktv-header__title">Nhiệm vụ của tôi</h1>
                <p class="ktv-header__sub">Xin chào, <strong>{{ auth()->user()->name }}</strong> — Kỹ thuật viên</p>
            </div>
        </div>
        <div class="ktv-header__right">
            <a href="{{ route('admin.tickets.index') }}" class="ktv-btn ktv-btn--ghost">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Tất cả phản ánh
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="ktv-alert ktv-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="ktv-alert ktv-alert--danger">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- Stats --}}
    <div class="ktv-stats">
        <div class="ktv-stat ktv-stat--new">
            <div class="ktv-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div>
                <div class="ktv-stat__num">{{ $stats['new'] }}</div>
                <div class="ktv-stat__lbl">Mới được giao</div>
            </div>
        </div>
        <div class="ktv-stat ktv-stat--active">
            <div class="ktv-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ktv-stat__num">{{ $stats['active'] }}</div>
                <div class="ktv-stat__lbl">Đang xử lý</div>
            </div>
        </div>
        <div class="ktv-stat ktv-stat--done">
            <div class="ktv-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="ktv-stat__num">{{ $stats['completed'] }}</div>
                <div class="ktv-stat__lbl">Hoàn thành</div>
            </div>
        </div>
        <div class="ktv-stat ktv-stat--total">
            <div class="ktv-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <div class="ktv-stat__num">{{ $stats['total'] }}</div>
                <div class="ktv-stat__lbl">Tổng nhiệm vụ</div>
            </div>
        </div>
        @if($stats['recheck'] > 0)
        <div class="ktv-stat ktv-stat--recheck">
            <div class="ktv-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            </div>
            <div>
                <div class="ktv-stat__num">{{ $stats['recheck'] }}</div>
                <div class="ktv-stat__lbl">Cần kiểm tra lại</div>
            </div>
        </div>
        @endif
    </div>

    {{-- Filters --}}
    <form action="{{ route('admin.tickets.my-tasks') }}" method="GET" class="ktv-filters">
        {{-- Tìm kiếm --}}
        <div class="ktv-filters__group">
            <label for="filterSearch" class="ktv-filters__label">Tìm kiếm</label>
            <div class="ktv-filters__search-wrapper">
                <input type="text" name="search" id="filterSearch" value="{{ request('search') }}" placeholder="Mã số, tiêu đề, căn hộ..." class="ktv-filters__input">
                <span class="ktv-filters__search-icon-inside">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
            </div>
        </div>

        {{-- Tòa nhà --}}
        <div class="ktv-filters__group">
            <label for="filterBlock" class="ktv-filters__label">Tòa nhà</label>
            <select name="block_id" id="filterBlock" class="ktv-filters__select">
                <option value="">Tất cả tòa nhà</option>
                @foreach($blocks as $block)
                    <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                        {{ $block->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Độ ưu tiên --}}
        <div class="ktv-filters__group">
            <label for="filterPriority" class="ktv-filters__label">Độ ưu tiên</label>
            <select name="priority" id="filterPriority" class="ktv-filters__select">
                <option value="">Tất cả độ ưu tiên</option>
                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
            </select>
        </div>

        {{-- Loại công việc --}}
        <div class="ktv-filters__group">
            <label for="filterType" class="ktv-filters__label">Loại công việc</label>
            <select name="type" id="filterType" class="ktv-filters__select">
                <option value="">Tất cả loại</option>
                <option value="normal" {{ request('type') == 'normal' ? 'selected' : '' }}>Bình thường</option>
                <option value="recheck" {{ request('type') == 'recheck' ? 'selected' : '' }}>Cần làm lại (Reopen)</option>
            </select>
        </div>

        {{-- Tháng --}}
        <div class="ktv-filters__group">
            <label for="filterMonth" class="ktv-filters__label">Tháng</label>
            <select name="month" id="filterMonth" class="ktv-filters__select" style="min-width: 90px;">
                <option value="">Tất cả</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endfor
            </select>
        </div>

        {{-- Năm --}}
        <div class="ktv-filters__group">
            <label for="filterYear" class="ktv-filters__label">Năm</label>
            <select name="year" id="filterYear" class="ktv-filters__select" style="min-width: 95px;">
                <option value="">Tất cả</option>
                @for($y = date('Y') + 1; $y >= date('Y') - 2; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        {{-- Sắp xếp --}}
        <div class="ktv-filters__group">
            <label for="filterSort" class="ktv-filters__label">Sắp xếp</label>
            <select name="sort" id="filterSort" class="ktv-filters__select">
                <option value="priority_desc" {{ request('sort', 'priority_desc') == 'priority_desc' ? 'selected' : '' }}>Ưu tiên cao trước</option>
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất xếp trước</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất xếp trước</option>
            </select>
        </div>

        {{-- Nút hành động --}}
        <div class="ktv-filters__actions">
            <button type="submit" class="ktv-filters__btn-submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Lọc
            </button>
            <a href="{{ route('admin.tickets.my-tasks') }}" class="ktv-filters__btn-reset">
                Đặt lại
            </a>
        </div>
    </form>

    {{-- Kanban Columns --}}
    <div class="ktv-kanban">

        {{-- Cột 1: Nhiệm vụ mới được giao --}}
        <div class="ktv-col">
            <div class="ktv-col__header ktv-col__header--new">
                <div class="ktv-col__header-left">
                    <span class="ktv-col__dot ktv-col__dot--new"></span>
                    <span class="ktv-col__title">Nhiệm vụ mới được giao</span>
                </div>
                <span class="ktv-col__badge ktv-col__badge--new">{{ $newTasks->count() }}</span>
            </div>
            <div class="ktv-col__body">
                @forelse($newTasks as $ticket)
                    @include('admin.tickets._ktv_card', ['ticket' => $ticket, 'mode' => 'new'])
                @empty
                    <div class="ktv-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p>Không có nhiệm vụ mới</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Cột 2: Đang xử lý --}}
        <div class="ktv-col">
            <div class="ktv-col__header ktv-col__header--active">
                <div class="ktv-col__header-left">
                    <span class="ktv-col__dot ktv-col__dot--active"></span>
                    <span class="ktv-col__title">Đang xử lý</span>
                </div>
                <span class="ktv-col__badge ktv-col__badge--active">{{ $activeTasks->count() }}</span>
            </div>
            <div class="ktv-col__body">
                @forelse($activeTasks as $ticket)
                    @include('admin.tickets._ktv_card', ['ticket' => $ticket, 'mode' => 'active'])
                @empty
                    <div class="ktv-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p>Chưa có việc đang làm</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Cột 3: Hoàn thành gần đây --}}
        <div class="ktv-col">
            <div class="ktv-col__header ktv-col__header--done">
                <div class="ktv-col__header-left">
                    <span class="ktv-col__dot ktv-col__dot--done"></span>
                    <span class="ktv-col__title">Hoàn thành gần đây</span>
                </div>
                <span class="ktv-col__badge ktv-col__badge--done">{{ $completedTasks->count() }}</span>
            </div>
            <div class="ktv-col__body">
                @forelse($completedTasks as $ticket)
                    @include('admin.tickets._ktv_card', ['ticket' => $ticket, 'mode' => 'done'])
                @empty
                    <div class="ktv-empty">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <p>Chưa có nhiệm vụ nào hoàn thành</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

{{-- Modal: Cập nhật tiến độ --}}
<div class="ktv-modal-overlay" id="progressModalOverlay" onclick="closeProgressModal()"></div>
<div class="ktv-modal" id="progressModal">
    <div class="ktv-modal__header">
        <div>
            <p class="ktv-modal__eyebrow" id="modalEyebrow">Cập nhật tiến độ</p>
            <h2 class="ktv-modal__title" id="modalTitle">—</h2>
        </div>
        <button class="ktv-modal__close" onclick="closeProgressModal()">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <form id="progressForm" method="POST" enctype="multipart/form-data" class="ktv-modal__body">
        @csrf
        <div class="ktv-modal__info" id="modalInfo"></div>

        <div class="ktv-modal__field">
            <label for="progressStatus">Trạng thái mới <span class="required">*</span></label>
            <select name="status" id="progressStatus" required>
                <option value="" disabled selected>-- Chọn trạng thái --</option>
                <option value="in_progress">🔄 Đang xử lý (tiếp tục)</option>
                <option value="completed">✅ Hoàn thành</option>
            </select>
        </div>

        <div class="ktv-modal__field">
            <label for="progressComment">Báo cáo hoàn thành <span id="commentFieldNote" class="ktv-field-note">(bắt buộc khi hoàn thành)</span></label>
            <textarea name="comment" id="progressComment" placeholder="Mô tả công việc đã thực hiện, vật tư sử dụng, kết quả..."></textarea>
        </div>

        <div class="ktv-modal__field">
            <label>Ảnh nghiệm thu / ảnh tiến trình <span id="proofFieldNote" class="ktv-field-note">(tùy chọn, có thể chụp ảnh khi đang xử lý)</span></label>
            <div class="ktv-modal__upload" id="uploadZone" onclick="openFilePicker()">
                <input type="file" name="image_proof" id="imgProofInput" accept="image/*" style="display:none" onchange="handleFileSelect(this)">
                <div id="uploadPlaceholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Nhấn để chọn ảnh nghiệm thu</span>
                </div>
                <div id="uploadPreview" style="display:none">
                    <img id="previewImg" src="" alt="preview">
                    <span id="previewName"></span>
                </div>
            </div>
            <button type="button" class="ktv-btn ktv-btn--sm ktv-btn--ghost" style="margin-top:0.75rem;" onclick="openCameraCapture()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 7h-2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h18a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"/><path d="M9 7l1.5-3h3L15 7"/><circle cx="12" cy="15" r="3"/></svg>
                Mở camera máy tính
            </button>
        </div>

        <div class="ktv-modal__footer">
            <button type="button" class="ktv-btn ktv-btn--ghost" onclick="closeProgressModal()">Hủy</button>
            <button type="submit" class="ktv-btn ktv-btn--primary" id="submitProgressBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Cập nhật tiến độ
            </button>
        </div>
    </form>
</div>

<div id="cameraModalOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.65);z-index:10000;cursor:pointer;pointer-events:none;" onclick="closeCameraModal()"></div>
<div id="cameraModal" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%, -50%);width:92vw;max-width:520px;background:#fff;border-radius:16px;box-shadow:0 24px 48px rgba(15,23,42,0.25);z-index:10001;padding:16px;pointer-events:none;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <div style="font-weight:700;font-size:0.95rem;color:#0f172a;">Camera máy tính</div>
        <button type="button" class="ktv-btn ktv-btn--ghost" style="padding:6px 10px;font-size:0.85rem;" onclick="closeCameraModal()">Đóng</button>
    </div>
    <video id="cameraVideo" autoplay playsinline style="width:100%;height:auto;border-radius:14px;background:#000;"></video>
    <div style="display:flex;justify-content:flex-end;gap:0.75rem;margin-top:12px;">
        <button type="button" class="ktv-btn ktv-btn--ghost" onclick="closeCameraModal()">Hủy</button>
        <button type="button" class="ktv-btn ktv-btn--primary" onclick="captureCameraPhoto()">Chụp ảnh</button>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

// ── Nhận nhiệm vụ (AJAX) ─────────────────────────────────────────────
document.querySelectorAll('.ktv-accept-btn').forEach(btn => {
    btn.addEventListener('click', async function (e) {
        e.stopPropagation();

        if (!confirm('Bạn có chắc chắn muốn nhận nhiệm vụ này và bắt đầu thực hiện không?')) {
            return;
        }

        const card    = this.closest('.ktv-card');
        const url     = this.dataset.url;
        const origTxt = this.innerHTML;

        this.disabled = true;
        this.innerHTML = '<svg class="ktv-spin" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Đang nhận...';

        try {
            const res  = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const data = await res.json();

            if (data.success) {
                showKtvToast('✅ ' + data.message, 'success');
                // Animate card removal
                card.style.transition = 'all 0.4s ease';
                card.style.opacity    = '0';
                card.style.transform  = 'translateX(20px)';
                setTimeout(() => {
                    card.remove();
                    updateColCount('new', -1);
                    updateStatNum('new', -1);
                    updateStatNum('active', 1);
                    setTimeout(() => location.reload(), 800);
                }, 400);
            } else {
                showKtvToast('❌ ' + data.message, 'error');
                this.disabled = false;
                this.innerHTML = origTxt;
            }
        } catch {
            showKtvToast('❌ Lỗi kết nối. Vui lòng thử lại.', 'error');
            this.disabled = false;
            this.innerHTML = origTxt;
        }
    });
});

// ── Mở modal cập nhật tiến độ ────────────────────────────────────────
function openProgressModal(btn) {
    const url   = btn.dataset.url;
    const title = btn.dataset.title;
    const apt   = btn.dataset.apt;
    const block = btn.dataset.block;
    const pri   = btn.dataset.priority;
    const status = btn.dataset.status;

    document.getElementById('progressForm').action = url;
    document.getElementById('modalTitle').textContent   = title;
    document.getElementById('modalEyebrow').textContent = 'Cập nhật tiến độ';
    document.getElementById('modalInfo').innerHTML =
        `<span class="ktv-modal__apt">${apt} · ${block}</span>
         <span class="tk-priority tk-priority--${pri}">${getPriorityLabel(pri)}</span>`;

    const statusSelect = document.getElementById('progressStatus');

    // Mặc định giữ lại trạng thái hiện tại nếu đang xử lý, hoặc yêu cầu chọn nếu mới được phân công.
    if (status === 'in_progress') {
        statusSelect.value = 'in_progress';
    } else {
        statusSelect.value = '';
    }

    updateProgressModalNotes(statusSelect.value);
    document.getElementById('progressComment').value = '';
    document.getElementById('imgProofInput').value = '';
    document.getElementById('uploadPreview').style.display = 'none';
    document.getElementById('uploadPlaceholder').style.display = 'flex';

    document.getElementById('progressModalOverlay').classList.add('active');
    document.getElementById('progressModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function updateProgressModalNotes(status) {
    const proofNote = document.getElementById('proofFieldNote');
    const commentNote = document.getElementById('commentFieldNote');
    if (!proofNote || !commentNote) {
        return;
    }

    if (status === 'completed') {
        proofNote.textContent = '(bắt buộc khi hoàn thành)';
        commentNote.textContent = '(bắt buộc khi hoàn thành)';
    } else {
        proofNote.textContent = '(tùy chọn)';
        commentNote.textContent = '(tùy chọn)';
    }
}

function closeProgressModal() {
    document.getElementById('progressModalOverlay').classList.remove('active');
    document.getElementById('progressModal').classList.remove('active');
    document.body.style.overflow = '';
}

let cameraStream = null;

function openFilePicker() {
    const input = document.getElementById('imgProofInput');
    input.click();
}

function openCameraCapture() {
    const overlay = document.getElementById('cameraModalOverlay');
    const modal = document.getElementById('cameraModal');
    const video = document.getElementById('cameraVideo');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Trình duyệt của bạn không hỗ trợ mở camera.');
        return;
    }

    overlay.style.display = 'block';
    overlay.style.pointerEvents = 'auto';
    modal.style.display = 'block';
    modal.style.pointerEvents = 'auto';

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            cameraStream = stream;
            video.srcObject = stream;
            video.play();
        })
        .catch(error => {
            let message = 'Không thể mở camera. Vui lòng kiểm tra quyền truy cập.';
            if (error && error.name) {
                switch (error.name) {
                    case 'NotAllowedError':
                    case 'PermissionDeniedError':
                        message = 'Quyền truy cập camera bị từ chối. Vui lòng cho phép truy cập camera cho trang này và thử lại.';
                        break;
                    case 'NotFoundError':
                    case 'DevicesNotFoundError':
                        message = 'Không tìm thấy thiết bị camera. Vui lòng kiểm tra camera hoặc kết nối thiết bị.';
                        break;
                    case 'NotReadableError':
                    case 'TrackStartError':
                        message = 'Camera đang bị ứng dụng khác sử dụng. Vui lòng đóng ứng dụng khác và thử lại.';
                        break;
                    case 'OverconstrainedError':
                    case 'ConstraintNotSatisfiedError':
                        message = 'Không thể mở camera với cấu hình hiện tại. Vui lòng thử lại.';
                        break;
                }
            }
            alert(message + '\nNếu không được, bạn có thể chọn ảnh nghiệm thu thủ công từ máy tính.');
            closeCameraModal();
        });
}

function closeCameraModal() {
    const overlay = document.getElementById('cameraModalOverlay');
    const modal = document.getElementById('cameraModal');
    const video = document.getElementById('cameraVideo');

    overlay.style.display = 'none';
    overlay.style.pointerEvents = 'none';
    modal.style.display = 'none';
    modal.style.pointerEvents = 'none';

    if (cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }

    if (video) {
        video.srcObject = null;
    }
}

function captureCameraPhoto() {
    const video = document.getElementById('cameraVideo');
    if (!video || !video.videoWidth || !video.videoHeight) {
        alert('Camera chưa sẵn sàng. Vui lòng thử lại.');
        return;
    }

    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(blob => {
        if (!blob) {
            alert('Không thể chụp ảnh. Vui lòng thử lại.');
            return;
        }

        const file = new File([blob], 'camera-capture.jpg', { type: 'image/jpeg' });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        const input = document.getElementById('imgProofInput');
        input.files = dataTransfer.files;
        handleFileSelect(input);
        closeCameraModal();
    }, 'image/jpeg', 0.92);
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeProgressModal(); });

const progressStatusSelect = document.getElementById('progressStatus');
if (progressStatusSelect) {
    progressStatusSelect.addEventListener('change', function () {
        updateProgressModalNotes(this.value);
    });
}

// ── Xác nhận cập nhật tiến độ ─────────────────────────────────────────
const progressForm = document.getElementById('progressForm');
if (progressForm) {
    progressForm.addEventListener('submit', function (e) {
        const statusSelect = document.getElementById('progressStatus');
        if (!statusSelect) return;
        const statusText = statusSelect.value === 'completed' ? 'Hoàn thành' : 'Đang xử lý';
        if (!confirm(`Bạn có chắc chắn muốn cập nhật trạng thái nhiệm vụ này thành "${statusText}" không?`)) {
            e.preventDefault();
        }
    });
}

// ── Upload preview ────────────────────────────────────────────────────
function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src     = e.target.result;
        document.getElementById('previewName').textContent = file.name;
        document.getElementById('uploadPlaceholder').style.display = 'none';
        document.getElementById('uploadPreview').style.display    = 'flex';
    };
    reader.readAsDataURL(file);
}

// ── Helper ────────────────────────────────────────────────────────────
function getPriorityLabel(p) {
    return { urgent: '🔴 Khẩn cấp', high: '🟠 Cao', medium: '🟡 Trung bình', low: '🟢 Thấp' }[p] || p;
}

function updateColCount(type, delta) {
    const badge = document.querySelector(`.ktv-col__badge--${type}`);
    if (badge) badge.textContent = Math.max(0, parseInt(badge.textContent) + delta);
}

function updateStatNum(type, delta) {
    const el = document.querySelector(`.ktv-stat--${type} .ktv-stat__num`);
    if (el) el.textContent = Math.max(0, parseInt(el.textContent) + delta);
}

// ── Toast ─────────────────────────────────────────────────────────────
function showKtvToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = 'ktv-toast ktv-toast--' + type;
    t.textContent = msg;
    document.body.appendChild(t);
    requestAnimationFrame(() => t.classList.add('show'));
    setTimeout(() => {
        t.classList.remove('show');
        setTimeout(() => t.remove(), 300);
    }, 3500);
}
</script>

<style>
.ktv-card--recheck {
    border-color: #f97316 !important;
    box-shadow: 0 0 0 2px rgba(249,115,22,0.15) !important;
}
.ktv-card__recheck-badge {
    font-size: 0.68rem;
    font-weight: 700;
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #fed7aa;
    padding: 2px 8px;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.ktv-card__recheck-notice {
    margin: 8px 0 4px;
    padding: 8px 10px;
    background: #fff7ed;
    border-left: 3px solid #f97316;
    border-radius: 0 6px 6px 0;
    font-size: 0.78rem;
    color: #9a3412;
    font-weight: 500;
    line-height: 1.5;
}
.ktv-card__recheck-reason {
    margin-top: 4px;
    font-style: italic;
    color: #c2410c;
    font-weight: 400;
}
.ktv-stat--recheck {
    border-color: #fed7aa;
    background: #fff7ed;
}
.ktv-stat--recheck .ktv-stat__num { color: #c2410c; }
.ktv-stat--recheck .ktv-stat__icon { background: #ffedd5; color: #ea580c; }
</style>

@endsection
