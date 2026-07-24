@extends('layouts.cleaning.master')

@section('page_title', $task->title . ' – DomusHub')

@push('styles')
<style>
    .content{display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;}

    /* TOAST NOTIFICATION */
    .toast{position:fixed;top:20px;right:20px;z-index:200;background:#05CD99;color:white;padding:14px 20px;border-radius:10px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(5,205,153,.3);transform:translateX(120%);transition:transform .3s ease;max-width:320px;}
    .toast.show{transform:translateX(0);}
    .toast i{font-size:16px;}

    /* TASK DETAIL LEFT */
    .task-detail{display:flex;flex-direction:column;gap:20px;}
    .task-breadcrumb{font-size:12px;color:#A3AED0;}
    .task-breadcrumb a{color:#3652D9;font-weight:600;text-decoration:none;}
    .task-breadcrumb a:hover{text-decoration:underline;}
    .task-title-row{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;}
    .task-title{font-size:22px;font-weight:800;color:#1B2559;letter-spacing:-.3px;}
    .task-actions{display:flex;gap:10px;flex-shrink:0;}
    .btn-action{display:inline-flex;align-items:center;gap:8px;padding:11px 20px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;transition:.2s;font-family:inherit;border:none;}
    .btn-action:focus-visible{outline:2px solid #3652D9;outline-offset:2px;}
    .btn-start{background:#3652D9;color:white;}
    .btn-start:hover{background:#2a43b8;transform:translateY(-1px);box-shadow:0 6px 16px rgba(54,82,217,.3);}
    .btn-complete{background:white;color:#05CD99;border:1.5px solid #05CD99;}
    .btn-complete:hover{background:#E6F9F0;transform:translateY(-1px);}
    .btn-reopen{background:white;color:#FF9B05;border:1.5px solid #FF9B05;}
    .btn-reopen:hover{background:#FFF4E5;transform:translateY(-1px);}
    .task-meta{display:flex;align-items:center;gap:16px;flex-wrap:wrap;}
    .status-pill{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:11.5px;font-weight:700;}
    .status-pill--done{background:#E6F9F0;color:#05CD99;}
    .status-pill--progress{background:#EEF2FF;color:#3652D9;}
    .status-pill--pending{background:#F4F7FE;color:#A3AED0;}
    .meta-text{font-size:12px;color:#A3AED0;display:flex;align-items:center;gap:5px;}

    /* CARDS */
    .detail-card{background:white;border-radius:14px;padding:24px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
    .detail-card__title{font-size:15px;font-weight:700;color:#1B2559;margin-bottom:12px;display:flex;align-items:center;gap:8px;}
    .detail-card__title i{color:#3652D9;font-size:14px;}
    .detail-card__text{font-size:13.5px;color:#4A5568;line-height:1.8;}
    .detail-card__info{display:flex;gap:24px;margin-top:16px;padding-top:14px;border-top:1px solid #E9EDF7;}
    .info-item{display:flex;align-items:center;gap:10px;}
    .info-item__icon{width:34px;height:34px;border-radius:9px;background:#F4F7FE;display:flex;align-items:center;justify-content:center;color:#3652D9;font-size:13px;}
    .info-item__label{font-size:10.5px;color:#A3AED0;font-weight:600;text-transform:uppercase;}
    .info-item__value{font-size:13px;color:#1B2559;font-weight:700;}

    /* CHECKLIST */
    .checklist-card{background:white;border-radius:14px;padding:24px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
    .checklist-card.disabled{opacity:.7;pointer-events:none;}
    .checklist-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;}
    .checklist-header h3{font-size:15px;font-weight:700;color:#1B2559;display:flex;align-items:center;gap:8px;}
    .checklist-header h3 i{color:#3652D9;font-size:14px;}
    .checklist-progress{padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;background:#E6F9F0;color:#05CD99;}
    .checklist{list-style:none;padding:0;margin:0;}
    .checklist li{display:flex;align-items:center;gap:12px;padding:12px 14px;border-bottom:1px solid #F4F7FE;font-size:13.5px;color:#1B2559;border-radius:8px;transition:.15s;}
    .checklist li:last-child{border-bottom:none;}
    .checklist li:hover{background:#FAFCFE;}
    .checklist li.done{color:#A3AED0;text-decoration:line-through;}
    .check-box{width:20px;height:20px;border-radius:6px;border:2px solid #D8E0F0;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:.2s;flex-shrink:0;background:white;}
    .check-box:hover{border-color:#3652D9;}
    .check-box:focus-visible{outline:2px solid #3652D9;outline-offset:2px;}
    .check-box.checked{background:#3652D9;border-color:#3652D9;}
    .check-box.checked i{color:white;font-size:9px;}

    /* TIMELINE */
    .timeline-card{background:white;border-radius:14px;padding:24px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
    .timeline-card__title{font-size:15px;font-weight:700;color:#1B2559;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
    .timeline-card__title i{color:#3652D9;font-size:14px;}
    .timeline{list-style:none;padding:0;margin:0;}
    .timeline li{display:flex;gap:12px;padding-bottom:14px;position:relative;}
    .timeline li:last-child{padding-bottom:0;}
    .timeline li::before{content:"";position:absolute;left:9px;top:22px;bottom:0;width:2px;background:#E9EDF7;}
    .timeline li:last-child::before{display:none;}
    .timeline__dot{width:20px;height:20px;border-radius:50%;background:#F4F7FE;border:2px solid #E9EDF7;display:flex;align-items:center;justify-content:center;flex-shrink:0;z-index:1;}
    .timeline__dot i{font-size:8px;color:#3652D9;}
    .timeline__dot--done{background:#E6F9F0;border-color:#05CD99;}
    .timeline__dot--done i{color:#05CD99;}
    .timeline__content{font-size:12px;color:#4A5568;line-height:1.5;}
    .timeline__time{font-size:10.5px;color:#A3AED0;margin-top:2px;}

    /* RIGHT COLUMN */
    .right-col{display:flex;flex-direction:column;gap:16px;}
    .info-card{background:white;border-radius:14px;padding:20px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
    .info-card__header{font-size:10.5px;font-weight:700;color:#A3AED0;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;}
    .info-card__row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #F4F7FE;font-size:12px;}
    .info-card__row:last-child{border-bottom:none;}
    .info-card__label{color:#A3AED0;}
    .info-card__value{color:#1B2559;font-weight:700;text-align:right;}
    .notes-card{background:linear-gradient(135deg,#F4F7FE,#EEF2FF);border-radius:14px;padding:20px;border:1px solid #D8E0F0;}
    .notes-card__header{font-size:10.5px;font-weight:700;color:#A3AED0;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;}
    .notes-card textarea{width:100%;min-height:70px;border:1.5px solid #D8E0F0;border-radius:10px;padding:10px 12px;font-size:12.5px;color:#1B2559;font-family:inherit;background:white;resize:vertical;}
    .notes-card textarea:focus{outline:none;border-color:#3652D9;box-shadow:0 0 0 3px rgba(54,82,217,.08);}
    .notes-card textarea:read-only{background:#F4F7FE;cursor:default;}
    .btn-note{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;background:white;border:1.5px solid #E9EDF7;color:#1B2559;padding:9px;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;margin-top:10px;font-family:inherit;transition:.2s;}
    .btn-note:hover{border-color:#3652D9;color:#3652D9;}
    .btn-note.saved{border-color:#05CD99;color:#05CD99;pointer-events:none;}

    /* CONFIRM DIALOG */
    .confirm-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:100;align-items:center;justify-content:center;}
    .confirm-overlay.open{display:flex;}
    .confirm-dialog{background:white;border-radius:14px;padding:28px;max-width:380px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.15);}
    .confirm-dialog h3{font-size:16px;font-weight:700;color:#1B2559;margin-bottom:8px;}
    .confirm-dialog p{font-size:13px;color:#707EAE;margin-bottom:20px;line-height:1.5;}
    .confirm-dialog__actions{display:flex;gap:10px;justify-content:center;}
    .confirm-dialog__actions button{padding:10px 20px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;border:none;transition:.2s;}
    .btn-confirm-yes{background:#05CD99;color:white;}
    .btn-confirm-yes:hover{background:#04B085;}
    .btn-confirm-no{background:#F4F7FE;color:#707EAE;}
    .btn-confirm-no:hover{background:#E9EDF7;}

    @media(max-width:1024px){.content{grid-template-columns:1fr;}.right-col{order:-1;display:grid;grid-template-columns:1fr 1fr;gap:16px;}}
    @media(max-width:768px){.task-title-row{flex-direction:column;}.task-actions{width:100%;display:flex;}.task-actions .btn-action{flex:1;justify-content:center;}.detail-card__info{flex-direction:column;gap:12px;}.right-col{grid-template-columns:1fr;}}
</style>
@endpush

@section('topbar_left')
<a href="{{ route('cleaning.tasks') }}" style="width:36px;height:36px;border-radius:10px;border:1px solid #E9EDF7;display:inline-flex;align-items:center;justify-content:center;color:#707EAE;text-decoration:none;" aria-label="Quay lại danh sách">
    <i class="fa-solid fa-arrow-left"></i>
</a>
@endsection

@section('content')

<div class="task-detail">

    <!-- TOAST (shown when task is done) -->
    @if($task->status === 'done')
    <div class="toast show" id="toast">
        <i class="fa-solid fa-circle-check"></i> Hoàn thành lúc {{ $task->completed_at?->format('H:i') }}
    </div>
    @endif
    <div class="toast" id="toastNotify"></div>

    <!-- BREADCRUMB & TITLE -->
    <div>
        <div class="task-breadcrumb"><a href="{{ route('cleaning.tasks') }}">Công việc hàng ngày</a> &gt; <span style="color:#3652D9;font-weight:600;">#TSK-{{ $task->id }}</span></div>
        <div class="task-title-row">
            <h1 class="task-title">{{ $task->title }}</h1>
            <div class="task-actions">
                @if($task->status === 'pending')
                <button class="btn-action btn-start" id="btnStart"><i class="fa-solid fa-play"></i> Bắt đầu</button>
                @endif
                @if($task->status !== 'done')
                <button class="btn-action btn-complete" id="btnComplete"><i class="fa-solid fa-circle-check"></i> Hoàn thành</button>
                @endif
            </div>
        </div>
        <div class="task-meta">
            <span class="status-pill status-pill--{{ $task->status }}" id="statusPill">
                @if($task->status === 'done') ✓ Hoàn thành
                @elseif($task->status === 'progress') ↻ Đang thực hiện
                @else ○ Chờ xử lý @endif
            </span>
            <span class="meta-text"><i class="fa-solid fa-clock"></i> Cập nhật {{ $task->updated_at->diffForHumans() }}</span>
        </div>
    </div>

    <!-- 2. DESCRIPTION + EQUIPMENT -->
    <div class="detail-card">
        <h2 class="detail-card__title"><i class="fa-solid fa-file-lines"></i> Mô tả công việc</h2>
        <p class="detail-card__text">{{ $task->description }}</p>
        <div class="detail-card__info">
            <div class="info-item">
                <div class="info-item__icon"><i class="fa-solid fa-location-dot"></i></div>
                <div><div class="info-item__label">Khu vực</div><div class="info-item__value">{{ $task->area }}</div></div>
            </div>
            <div class="info-item">
                <div class="info-item__icon"><i class="fa-solid fa-clock"></i></div>
                <div><div class="info-item__label">Thời gian</div><div class="info-item__value">{{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($task->end_time)->format('H:i') }}</div></div>
            </div>
            <div class="info-item">
                <div class="info-item__icon"><i class="fa-solid fa-gauge-high"></i></div>
                <div><div class="info-item__label">Ưu tiên</div><div class="info-item__value" style="color:{{ $task->priority === 'high' ? '#EE5D50' : ($task->priority === 'medium' ? '#FF9B05' : '#05CD99') }}">{{ $task->priority === 'high' ? 'Cao' : ($task->priority === 'medium' ? 'Trung bình' : 'Thấp') }}</div></div>
            </div>
        </div>
    </div>

    <!-- 1. CHECKLIST (disabled when done) -->
    @if($task->checklist)
    @php
        $checklist = $task->checklist;
        $doneCount = collect($checklist)->where('done', true)->count();
        $totalCount = count($checklist);
    @endphp
    <div class="checklist-card" id="checklistCard">
        <div class="checklist-header">
            <h3><i class="fa-solid fa-list-check"></i> Danh sách kiểm tra</h3>
            <span class="checklist-progress" id="checkProgress">{{ $doneCount }}/{{ $totalCount }} Hoàn thành</span>
        </div>
        <ul class="checklist" id="checklist">
            @foreach($checklist as $index => $item)
            <li class="{{ $item['done'] ? 'done' : '' }}">
                <div class="check-box {{ $item['done'] ? 'checked' : '' }}" data-index="{{ $index }}" tabindex="0" role="checkbox" aria-checked="{{ $item['done'] ? 'true' : 'false' }}" aria-label="{{ $item['text'] }}">
                    @if($item['done'])<i class="fa-solid fa-check"></i>@endif
                </div>
                {{ $item['text'] }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- 7. TIMELINE -->
    <div class="timeline-card">
        <h3 class="timeline-card__title"><i class="fa-solid fa-timeline"></i> Lịch sử hoạt động</h3>
        <ul class="timeline">
            <li>
                <div class="timeline__dot"><i class="fa-solid fa-plus"></i></div>
                <div><div class="timeline__content">Công việc được tạo bởi {{ $task->assigner?->name ?? 'Quản lý' }}</div><div class="timeline__time">{{ $task->created_at->format('H:i – d/m/Y') }}</div></div>
            </li>
            @if($task->status === 'progress' || $task->status === 'done')
            <li>
                <div class="timeline__dot timeline__dot--done"><i class="fa-solid fa-play"></i></div>
                <div><div class="timeline__content">Bắt đầu thực hiện</div><div class="timeline__time">{{ $task->created_at->addMinutes(5)->format('H:i – d/m/Y') }}</div></div>
            </li>
            @endif
            @if($task->status === 'done' && $task->completed_at)
            <li>
                <div class="timeline__dot timeline__dot--done"><i class="fa-solid fa-check"></i></div>
                <div><div class="timeline__content">Đánh dấu hoàn thành</div><div class="timeline__time">{{ $task->completed_at->format('H:i – d/m/Y') }}</div></div>
            </li>
            @endif
        </ul>
    </div>
</div>

<!-- RIGHT COLUMN -->
<div class="right-col">
    <!-- INFO CARD -->
    <div class="info-card">
        <div class="info-card__header">Thông tin chi tiết</div>
        <div class="info-card__row"><span class="info-card__label">Người giao</span><span class="info-card__value">{{ $task->assigner?->name ?? 'Quản lý' }}</span></div>
        <div class="info-card__row"><span class="info-card__label">Ngày tạo</span><span class="info-card__value">{{ $task->created_at->format('d/m/Y') }}</span></div>
        <div class="info-card__row"><span class="info-card__label">Trạng thái</span><span class="info-card__value" style="color:{{ $task->status === 'done' ? '#05CD99' : ($task->status === 'progress' ? '#3652D9' : '#A3AED0') }}">{{ $task->status === 'done' ? 'Hoàn thành' : ($task->status === 'progress' ? 'Đang làm' : 'Chờ xử lý') }}</span></div>
        <div class="info-card__row"><span class="info-card__label">Khu vực</span><span class="info-card__value">{{ $task->area_group }}</span></div>
        @if($task->completed_at)
        <div class="info-card__row"><span class="info-card__label">Hoàn thành lúc</span><span class="info-card__value" style="color:#05CD99;">{{ $task->completed_at->format('H:i') }}</span></div>
        @endif
    </div>
</div>

<!-- 9. CONFIRM DIALOG -->
<div class="confirm-overlay" id="confirmOverlay" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
    <div class="confirm-dialog">
        <h3 id="confirmTitle">Xác nhận hoàn thành?</h3>
        <p id="confirmText">Bạn có chắc muốn đánh dấu công việc này là hoàn thành? Hành động này có thể hoàn tác.</p>
        <div class="confirm-dialog__actions">
            <button class="btn-confirm-no" id="confirmNo">Hủy</button>
            <button class="btn-confirm-yes" id="confirmYes">Xác nhận</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const taskId = {{ $task->id }};
let pendingAction = null;

// Confirm dialog
function showConfirm(title, text, callback){
    document.getElementById('confirmTitle').textContent = title;
    document.getElementById('confirmText').textContent = text;
    document.getElementById('confirmOverlay').classList.add('open');
    pendingAction = callback;
}
document.getElementById('confirmNo').addEventListener('click', () => {
    document.getElementById('confirmOverlay').classList.remove('open');
    pendingAction = null;
});
document.getElementById('confirmYes').addEventListener('click', () => {
    document.getElementById('confirmOverlay').classList.remove('open');
    if(pendingAction) pendingAction();
    pendingAction = null;
});

// Start task
document.getElementById('btnStart')?.addEventListener('click', async function(){
    await updateStatus('progress');
    location.reload();
});

// Complete task (with confirm)
document.getElementById('btnComplete')?.addEventListener('click', function(){
    showConfirm('Xác nhận hoàn thành?', 'Bạn có chắc muốn đánh dấu công việc này là hoàn thành?', async () => {
        await updateStatus('done');
        showToast('Công việc đã hoàn thành!');
        setTimeout(() => location.reload(), 1500);
    });
});

function showToast(msg){
    const t = document.getElementById('toastNotify');
    t.innerHTML = '<i class="fa-solid fa-circle-check"></i> ' + msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

async function updateStatus(status){
    await fetch(`/cleaning/tasks/${taskId}/status`, {
        method:'PATCH',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
        body:JSON.stringify({status})
    });
}

// Checklist toggle (only if not disabled)
document.querySelectorAll('#checklist .check-box').forEach(box => {
    const handler = async function(){
        const li = this.closest('li');
        const isChecked = this.classList.toggle('checked');
        li.classList.toggle('done');
        this.setAttribute('aria-checked', isChecked);
        this.innerHTML = isChecked ? '<i class="fa-solid fa-check"></i>' : '';
        const total = document.querySelectorAll('#checklist li').length;
        const done = document.querySelectorAll('#checklist li.done').length;
        document.getElementById('checkProgress').textContent = `${done}/${total} Hoàn thành`;
        const checklist = [];
        document.querySelectorAll('#checklist li').forEach(item => {
            checklist.push({text: item.textContent.trim(), done: item.classList.contains('done')});
        });
        const res = await fetch(`/cleaning/tasks/${taskId}/checklist`, {
            method:'PATCH',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
            body:JSON.stringify({checklist})
        });
        const data = await res.json();
        // If all items done, server auto-marks as done — reload to reflect
        if(data.status === 'done' && document.getElementById('btnComplete')){
            showToast('Tất cả bước hoàn thành — công việc đã xong!');
            setTimeout(() => location.reload(), 1500);
        }
        // If unchecked and was done, reverts to progress
        if(data.status === 'progress'){
            location.reload();
        }
    };
    box.addEventListener('click', handler);
    box.addEventListener('keydown', e => { if(e.key==='Enter'||e.key===' '){e.preventDefault();handler.call(box);} });
});

// Auto-hide toast on page load
const initToast = document.getElementById('toast');
if(initToast && initToast.classList.contains('show')) setTimeout(() => initToast.classList.remove('show'), 3000);

// Save note
document.getElementById('btnSaveNote')?.addEventListener('click', async function(){
    const note = document.getElementById('noteInput').value;
    await fetch(`/cleaning/tasks/${taskId}/status`, {
        method:'PATCH',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
        body:JSON.stringify({status:'{{ $task->status }}', note})
    });
    this.classList.add('saved');
    this.innerHTML = '<i class="fa-solid fa-check"></i> Đã lưu';
    setTimeout(() => { this.classList.remove('saved'); this.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu ghi chú'; }, 2000);
});
</script>
@endpush
