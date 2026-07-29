@extends('layouts.cleaning.master')

@section('page_title', $task->title . ' – DomusHub')

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════
   TASK DETAIL — Single Column, Text-driven, No Icons
   ═══════════════════════════════════════════════════════ */

.task-page { max-width:680px; margin:0 auto; }

/* Back link */
.back-link { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#707EAE; text-decoration:none; margin-bottom:20px; transition:color .15s; }
.back-link:hover { color:#3652D9; }

/* Header */
.task-header { margin-bottom:28px; }
.task-header__title { font-size:22px; font-weight:800; color:#1B2559; margin-bottom:8px; line-height:1.3; }
.task-header__meta { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
.meta-pill { padding:4px 12px; border-radius:6px; font-size:11px; font-weight:700; }
.meta-pill--done { background:#E6F9F0; color:#05CD99; }
.meta-pill--progress { background:#EEF2FF; color:#3652D9; }
.meta-pill--pending { background:#F4F7FE; color:#94A3B8; }
.meta-pill--high { background:#FFF0F0; color:#EE5D50; }
.meta-pill--medium { background:#FFF4E5; color:#FF9B05; }
.meta-pill--low { background:#E6F9F0; color:#05CD99; }
.meta-text { font-size:12px; color:#A3AED0; }

/* Action bar */
.action-bar { margin-bottom:28px; }
.btn-primary { display:inline-flex; align-items:center; gap:8px; padding:12px 24px; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; transition:.2s; font-family:inherit; border:none; }
.btn-primary:hover { transform:translateY(-1px); }
.btn-primary--start { background:#3652D9; color:white; box-shadow:0 4px 12px rgba(54,82,217,.25); }
.btn-primary--start:hover { background:#2a43b8; }
.btn-primary--complete { background:#05CD99; color:white; box-shadow:0 4px 12px rgba(5,205,153,.25); }
.btn-primary--complete:hover { background:#04B085; }

/* Sections */
.section { background:white; border-radius:14px; padding:24px; margin-bottom:16px; box-shadow:0 1px 6px rgba(54,82,217,.04); }
.section__title { font-size:11px; font-weight:700; color:#A3AED0; text-transform:uppercase; letter-spacing:.5px; margin-bottom:14px; }

/* Info Grid */
.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.info-item { }
.info-item__label { font-size:11px; color:#A3AED0; font-weight:600; margin-bottom:2px; }
.info-item__value { font-size:14px; color:#1B2559; font-weight:700; }

/* Description */
.desc-text { font-size:14px; color:#4A5568; line-height:1.8; }

/* Checklist */
.checklist { list-style:none; padding:0; margin:0; }
.checklist__item { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid #F4F7FE; font-size:14px; color:#1B2559; transition:.15s; }
.checklist__item:last-child { border-bottom:none; }
.checklist__item:hover { background:#FAFCFE; margin:0 -24px; padding-left:24px; padding-right:24px; }
.checklist__item--done { color:#A3AED0; text-decoration:line-through; }
.checkbox { width:22px; height:22px; border-radius:6px; border:2px solid #D8E0F0; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:.2s; flex-shrink:0; background:white; font-size:11px; font-weight:800; color:transparent; user-select:none; }
.checkbox:hover { border-color:#3652D9; }
.checkbox--checked { background:#3652D9; border-color:#3652D9; color:white; }
.checklist-footer { display:flex; justify-content:space-between; align-items:center; margin-top:14px; padding-top:14px; border-top:1px solid #F4F7FE; }
.checklist-footer__progress { font-size:12px; color:#A3AED0; font-weight:600; }
.checklist-footer__bar { width:120px; height:5px; border-radius:3px; background:#E9EDF7; overflow:hidden; }
.checklist-footer__fill { height:100%; background:#3652D9; border-radius:3px; transition:width .3s; }

/* Timeline */
.activity-list { list-style:none; padding:0; margin:0; }
.activity-item { display:flex; gap:12px; padding:10px 0; border-bottom:1px solid #F4F7FE; }
.activity-item:last-child { border-bottom:none; }
.activity-dot { width:8px; height:8px; border-radius:50%; background:#D8E0F0; margin-top:5px; flex-shrink:0; }
.activity-dot--done { background:#05CD99; }
.activity-dot--active { background:#3652D9; }
.activity-content { font-size:13px; color:#4A5568; }
.activity-time { font-size:11px; color:#A3AED0; margin-top:2px; }

/* Toast */
.toast-msg { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(100px); background:#1B2559; color:white; padding:12px 24px; border-radius:10px; font-size:13px; font-weight:600; z-index:9999; transition:transform .3s ease; box-shadow:0 8px 24px rgba(0,0,0,.15); }
.toast-msg--visible { transform:translateX(-50%) translateY(0); }

/* Confirm */
.confirm-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:100; align-items:center; justify-content:center; }
.confirm-overlay--open { display:flex; }
.confirm-box { background:white; border-radius:14px; padding:28px; max-width:360px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,.15); }
.confirm-box__title { font-size:16px; font-weight:700; color:#1B2559; margin-bottom:8px; }
.confirm-box__text { font-size:13px; color:#707EAE; margin-bottom:20px; line-height:1.5; }
.confirm-box__actions { display:flex; gap:10px; justify-content:center; }
.confirm-box__actions button { padding:10px 20px; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; border:none; transition:.15s; }
.btn-yes { background:#05CD99; color:white; }
.btn-yes:hover { background:#04B085; }
.btn-no { background:#F4F7FE; color:#707EAE; }
.btn-no:hover { background:#E9EDF7; }

/* Responsive */
@media(max-width:768px) {
    .task-page { padding:0; }
    .info-grid { grid-template-columns:1fr; gap:12px; }
    .section { padding:20px 16px; }
}
</style>
@endpush

@section('content')
<div class="task-page">

    {{-- Back --}}
    <a href="{{ route('cleaning.tasks') }}" class="back-link">← Quay lại danh sách</a>

    {{-- Header --}}
    <div class="task-header">
        <h1 class="task-header__title">{{ $task->title }}</h1>
        <div class="task-header__meta">
            <span class="meta-pill meta-pill--{{ $task->status }}">
                @if($task->status === 'done') Hoàn thành
                @elseif($task->status === 'progress') Đang thực hiện
                @else Chờ xử lý @endif
            </span>
            <span class="meta-pill meta-pill--{{ $task->priority }}">
                {{ $task->priority === 'high' ? 'Ưu tiên cao' : ($task->priority === 'medium' ? 'Trung bình' : 'Thấp') }}
            </span>
            <span class="meta-text">Cập nhật {{ $task->updated_at->diffForHumans() }}</span>
        </div>
    </div>

    {{-- Action --}}
    @if($task->status !== 'done')
    <div class="action-bar">
        @if($task->status === 'pending')
            <button class="btn-primary btn-primary--start" id="btnStart">Bắt đầu làm →</button>
        @else
            <button class="btn-primary btn-primary--complete" id="btnComplete">Đánh dấu hoàn thành ✓</button>
        @endif
    </div>
    @endif

    {{-- Info --}}
    <div class="section">
        <div class="section__title">Thông tin</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-item__label">Khu vực</div>
                <div class="info-item__value">{{ $task->area }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Thời gian</div>
                <div class="info-item__value">{{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($task->end_time)->format('H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Người giao</div>
                <div class="info-item__value">{{ $task->assigner?->name ?? 'Quản lý' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item__label">Ngày</div>
                <div class="info-item__value">{{ $task->task_date->format('d/m/Y') }}</div>
            </div>
            @if($task->completed_at)
            <div class="info-item">
                <div class="info-item__label">Hoàn thành lúc</div>
                <div class="info-item__value" style="color:#05CD99;">{{ $task->completed_at->format('H:i') }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Description --}}
    @if($task->description)
    <div class="section">
        <div class="section__title">Mô tả</div>
        <p class="desc-text">{{ $task->description }}</p>
    </div>
    @endif

    {{-- Checklist --}}
    @if($task->checklist)
    @php
        $checklist = $task->checklist;
        $doneCount = collect($checklist)->where('done', true)->count();
        $totalCount = count($checklist);
        $pct = $totalCount > 0 ? round(($doneCount / $totalCount) * 100) : 0;
    @endphp
    <div class="section" id="checklistSection">
        <div class="section__title">Checklist</div>
        <ul class="checklist" id="checklist">
            @foreach($checklist as $index => $item)
            <li class="checklist__item {{ $item['done'] ? 'checklist__item--done' : '' }}">
                <div class="checkbox {{ $item['done'] ? 'checkbox--checked' : '' }}" 
                     data-index="{{ $index }}" tabindex="0" role="checkbox" 
                     aria-checked="{{ $item['done'] ? 'true' : 'false' }}">{{ $item['done'] ? '✓' : '' }}</div>
                <span>{{ $item['text'] }}</span>
            </li>
            @endforeach
        </ul>
        <div class="checklist-footer">
            <span class="checklist-footer__progress" id="checkProgress">{{ $doneCount }}/{{ $totalCount }} hoàn thành</span>
            <div class="checklist-footer__bar"><div class="checklist-footer__fill" id="checkFill" style="width:{{ $pct }}%"></div></div>
        </div>
    </div>
    @endif

    {{-- Activity --}}
    <div class="section">
        <div class="section__title">Hoạt động</div>
        <ul class="activity-list">
            <li class="activity-item">
                <span class="activity-dot"></span>
                <div>
                    <div class="activity-content">Được tạo bởi {{ $task->assigner?->name ?? 'Quản lý' }}</div>
                    <div class="activity-time">{{ $task->created_at->format('H:i – d/m/Y') }}</div>
                </div>
            </li>
            @if($task->status === 'progress' || $task->status === 'done')
            <li class="activity-item">
                <span class="activity-dot activity-dot--active"></span>
                <div>
                    <div class="activity-content">Bắt đầu thực hiện</div>
                    <div class="activity-time">{{ $task->updated_at->format('H:i – d/m/Y') }}</div>
                </div>
            </li>
            @endif
            @if($task->status === 'done' && $task->completed_at)
            <li class="activity-item">
                <span class="activity-dot activity-dot--done"></span>
                <div>
                    <div class="activity-content">Hoàn thành</div>
                    <div class="activity-time">{{ $task->completed_at->format('H:i – d/m/Y') }}</div>
                </div>
            </li>
            @endif
        </ul>
    </div>

</div>

{{-- Toast --}}
<div class="toast-msg" id="toast"></div>

{{-- Confirm --}}
<div class="confirm-overlay" id="confirmOverlay" role="dialog" aria-modal="true">
    <div class="confirm-box">
        <div class="confirm-box__title" id="confirmTitle"></div>
        <div class="confirm-box__text" id="confirmText"></div>
        <div class="confirm-box__actions">
            <button class="btn-no" id="confirmNo">Hủy</button>
            <button class="btn-yes" id="confirmYes">Xác nhận</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function(){
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const taskId = {{ $task->id }};
    let confirmCallback = null;

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('toast-msg--visible');
        setTimeout(() => t.classList.remove('toast-msg--visible'), 2500);
    }

    function showConfirm(title, text, cb) {
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmText').textContent = text;
        document.getElementById('confirmOverlay').classList.add('confirm-overlay--open');
        confirmCallback = cb;
    }

    document.getElementById('confirmNo').addEventListener('click', () => {
        document.getElementById('confirmOverlay').classList.remove('confirm-overlay--open');
        confirmCallback = null;
    });
    document.getElementById('confirmYes').addEventListener('click', () => {
        document.getElementById('confirmOverlay').classList.remove('confirm-overlay--open');
        if (confirmCallback) confirmCallback();
        confirmCallback = null;
    });

    async function updateStatus(status) {
        const res = await fetch(`/cleaning/tasks/${taskId}/status`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ status })
        });
        return res.ok;
    }

    // Start button
    const btnStart = document.getElementById('btnStart');
    if (btnStart) {
        btnStart.addEventListener('click', async function() {
            this.textContent = 'Đang xử lý...';
            this.disabled = true;
            if (await updateStatus('progress')) {
                showToast('Đã bắt đầu!');
                setTimeout(() => location.reload(), 800);
            } else {
                this.textContent = 'Bắt đầu làm →';
                this.disabled = false;
                showToast('Lỗi, thử lại.');
            }
        });
    }

    // Complete button
    const btnComplete = document.getElementById('btnComplete');
    if (btnComplete) {
        btnComplete.addEventListener('click', function() {
            showConfirm('Hoàn thành công việc?', 'Xác nhận đánh dấu công việc này đã xong.', async () => {
                btnComplete.textContent = 'Đang xử lý...';
                btnComplete.disabled = true;
                if (await updateStatus('done')) {
                    showToast('Đã hoàn thành!');
                    setTimeout(() => location.reload(), 800);
                } else {
                    btnComplete.textContent = 'Đánh dấu hoàn thành ✓';
                    btnComplete.disabled = false;
                    showToast('Lỗi, thử lại.');
                }
            });
        });
    }

    // Checklist
    document.querySelectorAll('#checklist .checkbox').forEach(box => {
        function toggle() {
            const li = box.closest('.checklist__item');
            const isChecked = !box.classList.contains('checkbox--checked');
            box.classList.toggle('checkbox--checked', isChecked);
            box.textContent = isChecked ? '✓' : '';
            box.setAttribute('aria-checked', isChecked);
            li.classList.toggle('checklist__item--done', isChecked);

            // Gather state
            const items = [];
            document.querySelectorAll('#checklist .checklist__item').forEach(item => {
                items.push({ text: item.querySelector('span').textContent, done: item.classList.contains('checklist__item--done') });
            });
            const doneCount = items.filter(i => i.done).length;
            const total = items.length;
            const pct = Math.round((doneCount / total) * 100);
            document.getElementById('checkProgress').textContent = `${doneCount}/${total} hoàn thành`;
            document.getElementById('checkFill').style.width = pct + '%';

            // Send to server
            fetch(`/cleaning/tasks/${taskId}/checklist`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ checklist: items })
            }).then(r => r.json()).then(data => {
                if (data.status === 'done') {
                    showToast('Tất cả hoàn thành!');
                    setTimeout(() => location.reload(), 1000);
                } else if (data.status === 'progress' && '{{ $task->status }}' === 'done') {
                    setTimeout(() => location.reload(), 500);
                }
            });
        }
        box.addEventListener('click', toggle);
        box.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); toggle(); } });
    });
})();
</script>
@endpush
