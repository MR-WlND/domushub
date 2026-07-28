@extends('layouts.admin.master')

@section('page_title', 'Nhiệm vụ của tôi')
@section('page_kicker', 'Kỹ thuật viên')
@section('role_title', 'Kỹ thuật viên')
@section('home_route', portal_route('tickets.my-tasks'))
@section('user_name', auth()->user()->name ?? 'KTV')
@section('user_role', 'technician')

@push('styles')
    @vite(['resources/css/pages/admin/tickets/technician.css'])
@endpush

@section('content')
<div class="ktv-page">

    {{-- Page Header --}}
    <div class="ktv-header">
        <div class="ktv-header__left">
            <h1 class="ktv-header__title">Nhiệm vụ của tôi</h1>
            <p class="ktv-header__sub">Quản lý và theo dõi tiến độ các yêu cầu kỹ thuật được phân công.</p>
        </div>
        <div class="ktv-header__right">
            <a href="{{ portal_route('tickets.index') }}" class="ktv-btn ktv-btn--ghost">
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

    {{-- 4 Stat Cards --}}
    <div class="ktv-stats-grid">
        {{-- Card 1: Tổng nhiệm vụ --}}
        <div class="ktv-stat-card">
            <div class="ktv-stat-card__icon ktv-stat-card__icon--blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div class="ktv-stat-card__content">
                <span class="ktv-stat-card__label">TỔNG NHIỆM VỤ</span>
                <span class="ktv-stat-card__value">{{ $stats['total'] }}</span>
            </div>
        </div>

        {{-- Card 2: Đang thực hiện --}}
        <div class="ktv-stat-card">
            <div class="ktv-stat-card__icon ktv-stat-card__icon--orange">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="ktv-stat-card__content">
                <span class="ktv-stat-card__label">ĐANG THỰC HIỆN</span>
                <span class="ktv-stat-card__value">{{ $stats['active'] }}</span>
            </div>
        </div>

        {{-- Card 3: Chờ xử lý --}}
        <div class="ktv-stat-card">
            <div class="ktv-stat-card__icon ktv-stat-card__icon--cyan">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ktv-stat-card__content">
                <span class="ktv-stat-card__label">CHỜ XỬ LÝ</span>
                <span class="ktv-stat-card__value">{{ $stats['new'] }}</span>
            </div>
        </div>

        {{-- Card 4: Hoàn thành tháng này --}}
        <div class="ktv-stat-card">
            <div class="ktv-stat-card__icon ktv-stat-card__icon--green">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ktv-stat-card__content">
                <span class="ktv-stat-card__label">HOÀN THÀNH THÁNG NÀY</span>
                <span class="ktv-stat-card__value">{{ $stats['completed_this_month'] }}</span>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="ktv-filter-card">
        <form action="{{ portal_route('tickets.my-tasks') }}" method="GET" class="ktv-filter-form" id="ktvFilterForm">
            {{-- Search Box --}}
            <div class="ktv-search-box">
                <svg class="ktv-search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm theo mã căn hộ hoặc nội dung..." class="ktv-search-input" onchange="this.form.submit()">
            </div>

            {{-- Building Filter (Tòa nhà) --}}
            <div class="ktv-select-wrapper">
                <select name="block_id" class="ktv-filter-select" onchange="this.form.submit()">
                    <option value="">Tòa nhà: Tất cả</option>
                    @foreach($blocks as $block)
                        <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>
                            {{ $block->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="ktv-select-wrapper">
                <select name="status" class="ktv-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>Trạng thái: Tất cả</option>
                    <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Đang thực hiện</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã xong</option>
                    <option value="recheck" {{ request('status') == 'recheck' ? 'selected' : '' }}>Cần kiểm tra lại</option>
                </select>
            </div>

            {{-- Priority Filter --}}
            <div class="ktv-select-wrapper">
                <select name="priority" class="ktv-filter-select" onchange="this.form.submit()">
                    <option value="all" {{ request('priority', 'all') == 'all' ? 'selected' : '' }}>Ưu tiên: Tất cả</option>
                    <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Bình thường</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
                </select>
            </div>

            {{-- Month / Time Filter --}}
            <div class="ktv-select-wrapper">
                <select name="month" class="ktv-filter-select" onchange="this.form.submit()">
                    <option value="">📅 Thời gian: Tất cả</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                    @endfor
                </select>
            </div>

            @if(request()->anyFilled(['search', 'block_id', 'status', 'priority', 'month']))
                <a href="{{ portal_route('tickets.my-tasks') }}" class="ktv-filter-reset">Đặt lại</a>
            @endif
        </form>
    </div>

    {{-- Main Data Table Card --}}
    <div class="ktv-table-card">
        <div class="table-responsive">
            <table class="ktv-table">
                <thead>
                    <tr>
                        <th>MÃ YÊU CẦU</th>
                        <th>CĂN HỘ</th>
                        <th>LOẠI SỰ CỐ</th>
                        <th>MỨC ĐỘ ƯU TIÊN</th>
                        <th>TRẠNG THÁI</th>
                        <th>NGÀY GỬI</th>
                        <th style="text-align: center;">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tickets as $ticket)
                        <tr>
                            {{-- Mã yêu cầu --}}
                            <td class="ktv-table__req-code">
                                <a href="{{ portal_route('tickets.show', $ticket->id) }}">
                                    #REQ-{{ $ticket->created_at ? $ticket->created_at->format('Y') : date('Y') }}-{{ sprintf('%03d', $ticket->id) }}
                                </a>
                            </td>

                            {{-- Căn hộ --}}
                            <td class="ktv-table__apt">
                                @if($ticket->apartment)
                                    Căn {{ $ticket->apartment->floor && $ticket->apartment->floor->block ? $ticket->apartment->floor->block->name . '-' : '' }}{{ $ticket->apartment->apartment_number }}
                                @else
                                    N/A
                                @endif
                            </td>

                            {{-- Loại sự cố / Tiêu đề --}}
                            <td class="ktv-table__title">
                                {{ $ticket->title }}
                            </td>

                            {{-- Mức độ ưu tiên --}}
                            <td>
                                @if($ticket->priority === 'urgent')
                                    <span class="ktv-pill ktv-pill--urgent">Khẩn cấp</span>
                                @elseif($ticket->priority === 'high')
                                    <span class="ktv-pill ktv-pill--high">Cao</span>
                                @elseif($ticket->priority === 'medium')
                                    <span class="ktv-pill ktv-pill--medium">Bình thường</span>
                                @else
                                    <span class="ktv-pill ktv-pill--low">Thấp</span>
                                @endif
                            </td>

                            {{-- Trạng thái --}}
                            <td>
                                @if($ticket->status === 'assigned')
                                    <span class="ktv-pill ktv-pill--status-assigned">Chờ xử lý</span>
                                @elseif($ticket->status === 'in_progress')
                                    @if($ticket->reopened_count > 0)
                                        <span class="ktv-pill ktv-pill--status-recheck">Cần kiểm tra lại</span>
                                    @else
                                        <span class="ktv-pill ktv-pill--status-active">Đang thực hiện</span>
                                    @endif
                                @elseif($ticket->status === 'completed')
                                    <span class="ktv-pill ktv-pill--status-completed">Đã xong</span>
                                @else
                                    <span class="ktv-pill ktv-pill--status-assigned">{{ $ticket->statusLabel() }}</span>
                                @endif
                            </td>

                            {{-- Ngày gửi --}}
                            <td class="ktv-table__date">
                                {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y') : '-' }}
                            </td>

                            {{-- Thao tác --}}
                            <td class="ktv-table__actions">
                                @if($ticket->status === 'assigned')
                                    <button type="button" class="ktv-action-btn ktv-action-btn--primary ktv-accept-btn"
                                            data-url="{{ portal_route('tickets.accept', $ticket->id) }}">
                                        Nhận nhiệm vụ
                                    </button>
                                @elseif($ticket->status === 'in_progress')
                                    <button type="button" class="ktv-action-btn ktv-action-btn--warning"
                                            onclick="openProgressModal(this)"
                                            data-url="{{ portal_route('tickets.update-progress', $ticket->id) }}"
                                            data-title="{{ $ticket->title }}"
                                            data-apt="Căn {{ $ticket->apartment->apartment_number ?? '' }}"
                                            data-block="{{ $ticket->apartment->floor->block->name ?? '' }}"
                                            data-priority="{{ $ticket->priority }}"
                                            data-status="{{ $ticket->status }}">
                                        Cập nhật
                                    </button>
                                @endif
                                <a href="{{ portal_route('tickets.show', $ticket->id) }}" class="ktv-action-btn ktv-action-btn--ghost">
                                    Chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="ktv-table__empty">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p>Không tìm thấy nhiệm vụ nào phù hợp</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer / Pagination --}}
        <div class="ktv-table-footer">
            <div class="ktv-table-footer__info">
                Hiển thị {{ $tickets->firstItem() ?? 0 }}-{{ $tickets->lastItem() ?? 0 }} trong số {{ $tickets->total() }} kết quả
            </div>
            <div class="ktv-table-footer__pagination">
                {{ $tickets->onEachSide(1)->links('pagination::bootstrap-4') }}
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

        const url     = this.dataset.url;
        const origTxt = this.innerHTML;

        this.disabled = true;
        this.innerHTML = '<svg class="ktv-spin" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Đang nhận...';

        try {
            const res  = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            });
            const data = await res.json();

            if (data.success) {
                showKtvToast('✅ ' + data.message, 'success');
                setTimeout(() => location.reload(), 600);
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
    if (!proofNote || !commentNote) return;

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
    document.getElementById('imgProofInput').click();
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
            alert('Không thể kết nối camera máy tính. Bạn có thể tải ảnh lên thủ công.');
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
    if (video) video.srcObject = null;
}

function captureCameraPhoto() {
    const video = document.getElementById('cameraVideo');
    if (!video || !video.videoWidth) {
        alert('Camera chưa sẵn sàng. Vui lòng thử lại.');
        return;
    }

    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    canvas.toBlob(blob => {
        if (!blob) return;
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

function handleFileSelect(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('previewImg').src = e.target.result;
        document.getElementById('previewName').textContent = file.name;
        document.getElementById('uploadPlaceholder').style.display = 'none';
        document.getElementById('uploadPreview').style.display = 'flex';
    };
    reader.readAsDataURL(file);
}

function getPriorityLabel(p) {
    return { urgent: '🔴 Khẩn cấp', high: '🟠 Cao', medium: '🟡 Trung bình', low: '🟢 Thấp' }[p] || p;
}

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

@endsection
