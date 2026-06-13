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
    </div>

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
            <label for="progressComment">Ghi chú công việc</label>
            <textarea name="comment" id="progressComment" placeholder="Mô tả công việc đã thực hiện, vật tư sử dụng, kết quả..."></textarea>
        </div>

        <div class="ktv-modal__field">
            <label>Ảnh chứng minh <span class="optional">(tùy chọn)</span></label>
            <div class="ktv-modal__upload" id="uploadZone" onclick="document.getElementById('imgProofInput').click()">
                <input type="file" name="image_proof" id="imgProofInput" accept="image/*" style="display:none" onchange="handleFileSelect(this)">
                <div id="uploadPlaceholder">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#94a3b8" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Nhấn để chọn ảnh</span>
                </div>
                <div id="uploadPreview" style="display:none">
                    <img id="previewImg" src="" alt="preview">
                    <span id="previewName"></span>
                </div>
            </div>
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

    // Ẩn/hiện tùy chọn "Đang xử lý (tiếp tục)" dựa vào trạng thái hiện tại
    const statusSelect = document.getElementById('progressStatus');
    const inProgressOpt = statusSelect.querySelector('option[value="in_progress"]');
    if (status === 'in_progress') {
        if (inProgressOpt) {
            inProgressOpt.style.display = 'none';
            inProgressOpt.disabled = true;
        }
    } else {
        if (inProgressOpt) {
            inProgressOpt.style.display = 'block';
            inProgressOpt.disabled = false;
        }
    }

    // Reset form
    if (status === 'in_progress') {
        statusSelect.value = 'completed';
    } else {
        statusSelect.value = '';
    }
    document.getElementById('progressComment').value = '';
    document.getElementById('imgProofInput').value   = '';
    document.getElementById('uploadPreview').style.display    = 'none';
    document.getElementById('uploadPlaceholder').style.display = 'flex';

    document.getElementById('progressModalOverlay').classList.add('active');
    document.getElementById('progressModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeProgressModal() {
    document.getElementById('progressModalOverlay').classList.remove('active');
    document.getElementById('progressModal').classList.remove('active');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeProgressModal(); });

// ── Xác nhận cập nhật tiến độ ─────────────────────────────────────────
document.getElementById('progressForm').addEventListener('submit', function (e) {
    const statusSelect = document.getElementById('progressStatus');
    const statusText = statusSelect.value === 'completed' ? 'Hoàn thành' : 'Đang xử lý';
    if (!confirm(`Bạn có chắc chắn muốn cập nhật trạng thái nhiệm vụ này thành "${statusText}" không?`)) {
        e.preventDefault();
    }
});

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

@endsection
