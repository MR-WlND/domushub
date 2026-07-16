@extends('layouts.admin.master')

@section('page_title', 'Quản lý Phản ánh')
@section('page_kicker', 'Dịch vụ cư dân')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', auth()->user()->role)

@push('styles')
    @vite(['resources/css/pages/admin/tickets/index.css'])
@endpush

@section('content')
<div class="tickets-page">

    {{-- Header --}}
    <div class="tickets-page__header">
        <div>
            <h1>Quản lý Phản ánh</h1>
            <p class="tickets-page__subtitle">Tiếp nhận và xử lý phản ánh sự cố từ cư dân.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="tickets-alert tickets-alert--success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="tickets-alert tickets-alert--danger">{{ $errors->first() }}</div>
    @endif

    {{-- Stats --}}
    <div class="tickets-stats-grid">
        <div class="tk-stat-card" style="border-left:4px solid #7c3aed;">
            <span class="tk-stat-card__label">Tổng</span>
            <span class="tk-stat-card__value" style="color:#7c3aed;">{{ number_format($stats['total']) }}</span>
        </div>
        <div class="tk-stat-card" style="border-left:4px solid #f59e0b;">
            <span class="tk-stat-card__label">Chờ xử lý</span>
            <span class="tk-stat-card__value" style="color:#f59e0b;">{{ number_format($stats['pending']) }}</span>
        </div>
        <div class="tk-stat-card" style="border-left:4px solid #8b5cf6;">
            <span class="tk-stat-card__label">Đã phân công</span>
            <span class="tk-stat-card__value" style="color:#8b5cf6;">{{ number_format($stats['assigned']) }}</span>
        </div>
        <div class="tk-stat-card" style="border-left:4px solid #2563eb;">
            <span class="tk-stat-card__label">Đang xử lý</span>
            <span class="tk-stat-card__value" style="color:#2563eb;">{{ number_format($stats['in_progress']) }}</span>
        </div>
        <div class="tk-stat-card" style="border-left:4px solid #16a34a;">
            <span class="tk-stat-card__label">Hoàn thành</span>
            <span class="tk-stat-card__value" style="color:#16a34a;">{{ number_format($stats['completed']) }}</span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="tickets-filter-card">
        <form method="GET" id="ticket-filter-form">
            <div class="tickets-filter-grid">
                <div>
                    <label>Tòa nhà</label>
                    <select name="block_id" onchange="this.form.submit()">
                        <option value="">Tất cả tòa</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tiêu đề, căn hộ..." onchange="this.form.submit()">
                </div>
                <div>
                    <label>Trạng thái</label>
                    <select name="status" onchange="this.form.submit()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending"     {{ request('status')==='pending'     ?'selected':'' }}>Chờ xử lý</option>
                        <option value="assigned"    {{ request('status')==='assigned'    ?'selected':'' }}>Đã phân công</option>
                        <option value="in_progress" {{ request('status')==='in_progress' ?'selected':'' }}>Đang xử lý</option>
                        <option value="completed"   {{ request('status')==='completed'   ?'selected':'' }}>Hoàn thành</option>
                        <option value="cancelled"   {{ request('status')==='cancelled'   ?'selected':'' }}>Đã hủy</option>
                    </select>
                </div>
                <div>
                    <label>Ưu tiên</label>
                    <select name="priority" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <option value="urgent" {{ request('priority')==='urgent' ?'selected':'' }}>🔴 Khẩn cấp</option>
                        <option value="high"   {{ request('priority')==='high'   ?'selected':'' }}>🟠 Cao</option>
                        <option value="medium" {{ request('priority')==='medium' ?'selected':'' }}>🟡 Trung bình</option>
                        <option value="low"    {{ request('priority')==='low'    ?'selected':'' }}>🟢 Thấp</option>
                    </select>
                </div>
                <div>
                    <label>Loại</label>
                    <select name="ticket_type" onchange="this.form.submit()">
                        <option value="">Tất cả</option>
                        <option value="complaint" {{ request('ticket_type')==='complaint' ?'selected':'' }}>📋 Phản ánh</option>
                        <option value="report"    {{ request('ticket_type')==='report'    ?'selected':'' }}>⚠️ Tố cáo</option>
                    </select>
                </div>
            </div>
            @if(request()->hasAny(['block_id','search','status','priority','ticket_type']))
                <div style="margin-top:10px;">
                    <a href="{{ route('admin.tickets.index') }}" style="font-size:.82rem;color:#ef4444;text-decoration:none;font-weight:600;">× Xóa bộ lọc</a>
                </div>
            @endif
        </form>
    </div>

    {{-- Group by block --}}
    @php
        $grouped    = $tickets->getCollection()->groupBy(fn($t) => $t->apartment?->floor?->block?->name ?? 'Không xác định');
        $blockOrder = $blocks->pluck('name')->toArray();
        $grouped    = $grouped->sortBy(fn($items, $key) => array_search($key, $blockOrder) !== false ? array_search($key, $blockOrder) : 999);
    @endphp

    @if($tickets->isEmpty())
        <div class="tickets-table-card" style="text-align:center;padding:48px 20px;color:#94a3b8;">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.5" style="margin-bottom:10px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <br>Không có phản ánh nào
            @if(request()->hasAny(['block_id','search','status','priority']))
                <br><a href="{{ route('admin.tickets.index') }}" style="color:#7c3aed;font-weight:600;font-size:.88rem;">Xóa bộ lọc để xem tất cả</a>
            @endif
        </div>
    @else
        @foreach($grouped as $blockName => $blockTickets)
        <div class="tickets-block-group">
            <div class="tickets-block-group__header">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="18" rx="1"/><path d="M9 3v18"/><path d="M15 3v18"/><path d="M2 9h20"/><path d="M2 15h20"/></svg>
                <span>Tòa {{ $blockName }}</span>
                <span class="tickets-block-group__count">{{ $blockTickets->count() }} phản ánh</span>
            </div>
            <div class="tickets-table-card">
                <div class="tickets-table-wrap">
                    <table class="tickets-table">
                        <thead>
                            <tr>
                                <th style="width:36px;"></th>
                                <th>Phản ánh</th>
                                <th>Căn hộ</th>
                                <th>Trạng thái</th>
                                <th>KTV</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($blockTickets as $ticket)
                            @php
                                $ageHours = $ticket->created_at->diffInHours(now());
                                $slaOver  = match($ticket->priority) {
                                    'urgent' => $ageHours >= 2,
                                    'high'   => $ageHours >= 8,
                                    'medium' => $ageHours >= 24,
                                    'low'    => $ageHours >= 72,
                                    default  => false,
                                };
                                $isActive = !in_array($ticket->status, ['completed','cancelled']);
                                $overdue  = $slaOver && $isActive;
                            @endphp
                            <tr class="tk-row {{ $overdue ? 'tk-row--overdue' : '' }}" data-id="{{ $ticket->id }}"
                                data-title="{{ $ticket->title }}"
                                data-desc="{{ $ticket->description }}"
                                data-status="{{ $ticket->status }}"
                                data-status-label="{{ $ticket->statusLabel() }}"
                                data-priority="{{ $ticket->priority }}"
                                data-priority-label="{{ $ticket->priorityLabel() }}"
                                data-apartment="{{ $ticket->apartment->apartment_number ?? 'N/A' }}"
                                data-block="{{ $blockName }}"
                                data-floor="{{ $ticket->apartment?->floor?->floor_number ?? '' }}"
                                data-sender="{{ $ticket->sender->name ?? 'N/A' }}"
                                data-handler="{{ $ticket->handler->name ?? '' }}"
                                data-handler-id="{{ $ticket->handler_id ?? '' }}"
                                data-created="{{ $ticket->created_at->diffForHumans() }}"
                                data-created-full="{{ $ticket->created_at->format('d/m/Y H:i') }}"
                                data-assign-url="{{ route('admin.tickets.assign', $ticket->id) }}"
                                data-progress-url="{{ route('admin.tickets.update-progress', $ticket->id) }}"
                                data-detail-url="{{ route('admin.tickets.show', $ticket->id) }}"
                                data-can-assign="{{ in_array($ticket->status, ['pending','assigned']) && in_array(auth()->user()->role, ['admin','manager']) ? '1' : '0' }}"
                                data-can-progress="{{ in_array($ticket->status, ['assigned','in_progress']) ? '1' : '0' }}"
                                data-overdue="{{ $overdue ? '1' : '0' }}"
                            >
                                <td>
                                    <span class="tk-priority-dot tk-priority-dot--{{ $ticket->priority }}" title="{{ $ticket->priorityLabel() }}"></span>
                                </td>
                                <td>
                                    <div class="tk-title-cell">
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            @if($ticket->ticket_type === 'report')
                                                <span style="display:inline-flex;padding:1px 6px;background:#fef2f2;color:#dc2626;border-radius:4px;font-size:0.65rem;font-weight:700;border:1px solid #fecaca;white-space:nowrap;">⚠️ Tố cáo</span>
                                            @endif
                                            <span class="tk-title-cell__title">{{ $ticket->title }}</span>
                                        </div>
                                        <span class="tk-title-cell__desc">{{ Str::limit($ticket->description, 55) }}</span>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $ticket->apartment->apartment_number ?? 'N/A' }}</strong>
                                    @if($ticket->apartment?->floor)
                                        <div style="font-size:.73rem;color:#94a3b8;">Tầng {{ $ticket->apartment->floor->floor_number }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="tk-status tk-status--{{ $ticket->status }}">{{ $ticket->statusLabel() }}</span>
                                </td>
                                <td>
                                    @if($ticket->handler)
                                        <span style="font-weight:600;font-size:.85rem;">{{ $ticket->handler->name }}</span>
                                    @else
                                        <span class="tk-unassigned">Chưa phân công</span>
                                    @endif
                                </td>
                                <td class="tk-time" title="{{ $ticket->created_at->format('d/m/Y H:i') }}">
                                    {{ $ticket->created_at->diffForHumans() }}
                                    @if($overdue)
                                        <div class="tk-overdue-badge">⚠ Trễ SLA</div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    @endif

    @if($tickets->hasPages())
        <div class="tickets-pagination">{{ $tickets->links() }}</div>
    @endif

</div>

{{-- Slide Panel --}}
<div class="tk-panel-overlay" id="tkOverlay" onclick="closePanel()"></div>
<div class="tk-panel" id="tkPanel">
    <div class="tk-panel__header">
        <div class="tk-panel__header-left">
            <span class="tk-priority-dot" id="panelDot"></span>
            <div>
                <p class="tk-panel__eyebrow" id="panelEyebrow"></p>
                <h2 class="tk-panel__title" id="panelTitle"></h2>
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <a id="panelDetailLink" href="#" class="tk-panel__detail-btn" target="_blank">Chi tiết đầy đủ ↗</a>
            <button class="tk-panel__close" onclick="closePanel()">×</button>
        </div>
    </div>

    <div class="tk-panel__body">

        {{-- Overdue warning --}}
        <div class="tk-panel__overdue" id="panelOverdueWarn" style="display:none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span>Ticket này đã vượt SLA — cần xử lý ngay!</span>
        </div>

        {{-- Info --}}
        <div class="tk-panel__section">
            <div class="tk-panel__info-grid">
                <div><span class="tk-panel__lbl">Căn hộ</span><span class="tk-panel__val" id="panelApartment"></span></div>
                <div><span class="tk-panel__lbl">Tòa nhà</span><span class="tk-panel__val" id="panelBlock"></span></div>
                <div><span class="tk-panel__lbl">Người gửi</span><span class="tk-panel__val" id="panelSender"></span></div>
                <div><span class="tk-panel__lbl">Gửi lúc</span><span class="tk-panel__val" id="panelCreated"></span></div>
            </div>
            <p class="tk-panel__desc" id="panelDesc"></p>
        </div>

        {{-- Assign form --}}
        <div class="tk-panel__section" id="panelAssignSection" style="display:none;">
            <p class="tk-panel__section-title">Phân công kỹ thuật viên</p>
            <form id="panelAssignForm" method="POST">
                @csrf
                <div style="display:flex;gap:8px;align-items:center;">
                    <select name="handler_id" id="panelTechSelect" class="tk-panel__select" required>
                        <option value="" disabled selected>-- Chọn KTV --</option>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="tk-panel__btn tk-panel__btn--primary">Phân công</button>
                </div>
            </form>
        </div>



    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content
          || '{{ csrf_token() }}';

// ── Row click → open panel ───────────────────────────────────────────
document.querySelectorAll('.tk-row').forEach(row => {
    row.addEventListener('click', () => openPanel(row.dataset, row));
});

let activeRow = null;

function openPanel(d, row) {
    activeRow = row;

    document.getElementById('panelDot').className = 'tk-priority-dot tk-priority-dot--' + d.priority;
    document.getElementById('panelEyebrow').textContent = d.priorityLabel + ' · ' + d.statusLabel;
    document.getElementById('panelTitle').textContent   = d.title;
    document.getElementById('panelDetailLink').href     = d.detailUrl;

    document.getElementById('panelApartment').textContent = d.apartment + (d.floor ? ' · Tầng ' + d.floor : '');
    document.getElementById('panelBlock').textContent     = 'Tòa ' + d.block;
    document.getElementById('panelSender').textContent    = d.sender;
    document.getElementById('panelCreated').textContent   = d.createdFull + ' (' + d.created + ')';
    document.getElementById('panelDesc').textContent      = d.desc;

    // overdue warning
    const warn = document.getElementById('panelOverdueWarn');
    warn.style.display = d.overdue === '1' ? 'flex' : 'none';

    // assign section
    const assignSec = document.getElementById('panelAssignSection');
    assignSec.style.display = d.canAssign === '1' ? 'block' : 'none';
    if (d.canAssign === '1') {
        document.getElementById('panelAssignForm').dataset.url = d.assignUrl;
        document.getElementById('panelTechSelect').value = d.handlerId || '';
    }

    document.getElementById('tkPanel').classList.add('tk-panel--open');
    document.getElementById('tkOverlay').classList.add('tk-panel-overlay--visible');
    document.body.style.overflow = 'hidden';
}

function closePanel() {
    document.getElementById('tkPanel').classList.remove('tk-panel--open');
    document.getElementById('tkOverlay').classList.remove('tk-panel-overlay--visible');
    document.body.style.overflow = '';
    activeRow = null;
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closePanel(); });

// ── AJAX: Assign ─────────────────────────────────────────────────────
document.getElementById('panelAssignForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const url       = this.dataset.url;
    const handlerId = document.getElementById('panelTechSelect').value;
    if (!handlerId) return;

    const btn = this.querySelector('button[type=submit]');
    setLoading(btn, true);

    try {
        const res  = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json',
                       'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ handler_id: handlerId }),
        });
        const data = await res.json();

        if (data.success) {
            // Cập nhật row tại chỗ
            if (activeRow) {
                activeRow.dataset.status      = data.status;
                activeRow.dataset.statusLabel = data.statusLabel;
                activeRow.dataset.handlerId   = handlerId;
                const techName = document.getElementById('panelTechSelect').selectedOptions[0]?.text || '';
                activeRow.dataset.handler = techName;
                activeRow.querySelector('.tk-status').className   = 'tk-status tk-status--' + data.status;
                activeRow.querySelector('.tk-status').textContent  = data.statusLabel;
                activeRow.querySelector('.tk-unassigned, [data-ktv]') &&
                    (activeRow.querySelector('td:nth-child(5)').innerHTML =
                        `<span style="font-weight:600;font-size:.85rem;">${techName}</span>`);
            }
            showToast('✅ ' + data.message, 'success');
            closePanel();
        } else {
            showToast('❌ ' + (data.message || 'Có lỗi xảy ra'), 'error');
        }
    } catch {
        showToast('❌ Lỗi kết nối', 'error');
    } finally {
        setLoading(btn, false);
    }
});

// ── Toast ─────────────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = 'tk-toast tk-toast--' + type;
    t.textContent = msg;
    document.body.appendChild(t);
    requestAnimationFrame(() => t.classList.add('tk-toast--show'));
    setTimeout(() => {
        t.classList.remove('tk-toast--show');
        setTimeout(() => t.remove(), 300);
    }, 3000);
}

// ── Loading state ────────────────────────────────────────────────────
function setLoading(btn, loading) {
    btn.disabled    = loading;
    btn.textContent = loading ? 'Đang xử lý...' : btn.dataset.orig || btn.textContent;
    if (!btn.dataset.orig && !loading) return;
    if (!btn.dataset.orig) btn.dataset.orig = btn.textContent;
}
</script>
@endsection
