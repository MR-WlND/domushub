@extends('layouts.cleaning.master')

@section('page_title', $task->title . ' – DomusHub')

@push('styles')
<style>
    .content{display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start;}
    .task-detail{display:flex;flex-direction:column;gap:20px;}
    .task-breadcrumb{font-size:12px;color:#A3AED0;}
    .task-breadcrumb a{color:#3652D9;font-weight:600;text-decoration:none;}
    .task-breadcrumb a:hover{text-decoration:underline;}
    .task-title-row{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;}
    .task-title{font-size:24px;font-weight:800;color:#1B2559;letter-spacing:-.3px;}
    .task-actions{display:flex;gap:10px;flex-shrink:0;}
    .btn-start{display:inline-flex;align-items:center;gap:8px;background:#3652D9;color:white;border:none;padding:11px 20px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;transition:.2s;font-family:inherit;}
    .btn-start:hover{background:#2a43b8;transform:translateY(-1px);box-shadow:0 6px 16px rgba(54,82,217,.3);}
    .btn-start:focus-visible{outline:2px solid #3652D9;outline-offset:2px;}
    .btn-complete{display:inline-flex;align-items:center;gap:8px;background:white;color:#05CD99;border:1.5px solid #05CD99;padding:11px 20px;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;transition:.2s;font-family:inherit;}
    .btn-complete:hover{background:#E6F9F0;transform:translateY(-1px);}
    .btn-complete:focus-visible{outline:2px solid #05CD99;outline-offset:2px;}
    .task-meta{display:flex;align-items:center;gap:16px;}
    .status-pill{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:11.5px;font-weight:700;background:#EEF2FF;color:#3652D9;}
    .meta-text{font-size:12px;color:#A3AED0;display:flex;align-items:center;gap:5px;}
    .detail-card{background:white;border-radius:14px;padding:28px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
    .detail-card__title{font-size:16px;font-weight:700;color:#1B2559;margin-bottom:14px;}
    .detail-card__text{font-size:13.5px;color:#4A5568;line-height:1.8;}
    .detail-card__info{display:flex;gap:32px;margin-top:20px;padding-top:18px;border-top:1px solid #E9EDF7;}
    .info-item{display:flex;align-items:center;gap:10px;}
    .info-item__icon{width:36px;height:36px;border-radius:10px;background:#F4F7FE;display:flex;align-items:center;justify-content:center;color:#3652D9;font-size:14px;}
    .info-item__label{font-size:11px;color:#A3AED0;font-weight:600;text-transform:uppercase;}
    .info-item__value{font-size:13px;color:#1B2559;font-weight:700;}
    .checklist-card{background:white;border-radius:14px;padding:28px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
    .checklist-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;}
    .checklist-header h3{font-size:16px;font-weight:700;color:#1B2559;}
    .checklist-progress{padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:700;background:#E6F9F0;color:#05CD99;}
    .checklist{list-style:none;padding:0;margin:0;}
    .checklist li{display:flex;align-items:center;gap:14px;padding:14px 16px;border-bottom:1px solid #F4F7FE;font-size:14px;color:#1B2559;border-radius:8px;transition:.15s;}
    .checklist li:last-child{border-bottom:none;}
    .checklist li:hover{background:#FAFCFE;}
    .checklist li.done{color:#A3AED0;text-decoration:line-through;}
    .check-box{width:22px;height:22px;border-radius:6px;border:2px solid #D8E0F0;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:.2s;flex-shrink:0;background:white;}
    .check-box:hover{border-color:#3652D9;}
    .check-box:focus-visible{outline:2px solid #3652D9;outline-offset:2px;}
    .check-box.checked{background:#3652D9;border-color:#3652D9;}
    .check-box.checked i{color:white;font-size:10px;}
    .right-col{display:flex;flex-direction:column;gap:20px;}
    .info-card{background:white;border-radius:14px;padding:22px;box-shadow:0 2px 12px rgba(54,82,217,.04);}
    .info-card__header{font-size:11px;font-weight:700;color:#A3AED0;text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;}
    .info-card__row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #F4F7FE;font-size:12.5px;}
    .info-card__row:last-child{border-bottom:none;}
    .info-card__label{color:#A3AED0;}
    .info-card__value{color:#1B2559;font-weight:700;}
    .notes-card{background:linear-gradient(135deg,#F4F7FE,#EEF2FF);border-radius:14px;padding:22px;border:1px solid #D8E0F0;}
    .notes-card__header{font-size:11px;font-weight:700;color:#A3AED0;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;}
    .notes-card textarea{width:100%;min-height:80px;border:1.5px solid #D8E0F0;border-radius:10px;padding:12px 14px;font-size:13px;color:#1B2559;font-family:inherit;background:white;resize:vertical;}
    .notes-card textarea:focus{outline:none;border-color:#3652D9;box-shadow:0 0 0 3px rgba(54,82,217,.08);}
    .btn-note{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;background:white;border:1.5px solid #E9EDF7;color:#1B2559;padding:10px;border-radius:10px;font-size:12.5px;font-weight:700;cursor:pointer;margin-top:12px;font-family:inherit;transition:.2s;}
    .btn-note:hover{border-color:#3652D9;color:#3652D9;}
    @media(max-width:1100px){.content{grid-template-columns:1fr;}}
    @media(max-width:768px){.task-title-row{flex-direction:column;}.task-actions{width:100%;}.detail-card__info{flex-direction:column;gap:14px;}}
</style>
@endpush

@section('topbar_left')
<a href="{{ route('cleaning.tasks') }}" style="width:36px;height:36px;border-radius:10px;border:1px solid #E9EDF7;display:flex;align-items:center;justify-content:center;color:#707EAE;text-decoration:none;" aria-label="Quay lại">
    <i class="fa-solid fa-arrow-left"></i>
</a>
<div class="topbar-search">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" placeholder="Tìm kiếm..." aria-label="Tìm kiếm">
</div>
@endsection

@section('content')
<div class="task-detail">
    <div>
        <div class="task-breadcrumb"><a href="{{ route('cleaning.tasks') }}">Công việc hàng ngày</a> &gt; <span style="color:#3652D9;font-weight:600;">#TSK-{{ $task->id }}</span></div>
        <div class="task-title-row">
            <h1 class="task-title">{{ $task->title }}</h1>
            <div class="task-actions">
                @if($task->status === 'pending')
                <button class="btn-start" id="btnStart" data-task-id="{{ $task->id }}"><i class="fa-solid fa-play"></i> Bắt đầu công việc</button>
                @endif
                @if($task->status !== 'done')
                <button class="btn-complete" id="btnComplete" data-task-id="{{ $task->id }}"><i class="fa-solid fa-circle-check"></i> Đánh dấu hoàn thành</button>
                @endif
            </div>
        </div>
        <div class="task-meta">
            <span class="status-pill" id="statusPill">
                @if($task->status === 'done') ✓ Hoàn thành
                @elseif($task->status === 'progress') ↻ Đang thực hiện
                @else ○ Chờ xử lý @endif
            </span>
            <span class="meta-text"><i class="fa-solid fa-clock"></i> Cập nhật: {{ $task->updated_at->diffForHumans() }}</span>
        </div>
    </div>

    <div class="detail-card">
        <h2 class="detail-card__title">Mô tả công việc</h2>
        <p class="detail-card__text">{{ $task->description }}</p>
        <div class="detail-card__info">
            <div class="info-item">
                <div class="info-item__icon"><i class="fa-solid fa-location-dot"></i></div>
                <div><div class="info-item__label">Khu vực</div><div class="info-item__value">{{ $task->area }}</div></div>
            </div>
            <div class="info-item">
                <div class="info-item__icon"><i class="fa-solid fa-clock"></i></div>
                <div><div class="info-item__label">Thời gian</div><div class="info-item__value">{{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($task->end_time)->format('H:i') }}</div></div>
            </div>
        </div>
    </div>

    @if($task->checklist)
    @php
        $checklist = $task->checklist;
        $doneCount = collect($checklist)->where('done', true)->count();
        $totalCount = count($checklist);
    @endphp
    <div class="checklist-card">
        <div class="checklist-header">
            <h3>Danh sách kiểm tra</h3>
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
</div>

<div class="right-col">
    <div class="info-card">
        <div class="info-card__header">Thông tin</div>
        <div class="info-card__row"><span class="info-card__label">Người giao</span><span class="info-card__value">{{ $task->assigner?->name ?? 'Quản lý' }}</span></div>
        <div class="info-card__row"><span class="info-card__label">Ngày tạo</span><span class="info-card__value">{{ $task->created_at->format('d/m/Y') }}</span></div>
        <div class="info-card__row"><span class="info-card__label">Độ ưu tiên</span><span class="info-card__value" style="color:{{ $task->priority === 'high' ? '#EE5D50' : ($task->priority === 'medium' ? '#FF9B05' : '#05CD99') }}">{{ $task->priority === 'high' ? 'Cao' : ($task->priority === 'medium' ? 'Trung bình' : 'Thấp') }}</span></div>
        <div class="info-card__row"><span class="info-card__label">Khu vực</span><span class="info-card__value">{{ $task->area_group }}</span></div>
    </div>
    <div class="notes-card">
        <div class="notes-card__header">Ghi chú nhanh</div>
        <textarea placeholder="Nhập ghi chú hoặc vấn đề phát sinh..." aria-label="Ghi chú"></textarea>
        <button class="btn-note"><i class="fa-solid fa-floppy-disk"></i> Lưu ghi chú</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const taskId = {{ $task->id }};

// Start task
document.getElementById('btnStart')?.addEventListener('click', async function(){
    await updateTaskStatus('progress');
    document.getElementById('statusPill').innerHTML = '↻ Đang thực hiện';
    this.remove();
});

// Complete task
document.getElementById('btnComplete')?.addEventListener('click', async function(){
    await updateTaskStatus('done');
    document.getElementById('statusPill').innerHTML = '✓ Hoàn thành';
    document.querySelector('.task-actions').innerHTML = '';
});

async function updateTaskStatus(status){
    await fetch(`/cleaning/tasks/${taskId}/status`, {
        method:'PATCH',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
        body:JSON.stringify({status})
    });
}

// Checklist toggle with save
document.querySelectorAll('.check-box').forEach(box => {
    const handler = async function(){
        const li = this.closest('li');
        const isChecked = this.classList.toggle('checked');
        li.classList.toggle('done');
        this.setAttribute('aria-checked', isChecked);
        this.innerHTML = isChecked ? '<i class="fa-solid fa-check"></i>' : '';

        // Update progress display
        const total = document.querySelectorAll('.checklist li').length;
        const done = document.querySelectorAll('.checklist li.done').length;
        document.getElementById('checkProgress').textContent = `${done}/${total} Hoàn thành`;

        // Save to DB
        const checklist = [];
        document.querySelectorAll('.checklist li').forEach(item => {
            checklist.push({text: item.textContent.trim(), done: item.classList.contains('done')});
        });
        await fetch(`/cleaning/tasks/${taskId}/checklist`, {
            method:'PATCH',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
            body:JSON.stringify({checklist})
        });
    };
    box.addEventListener('click', handler);
    box.addEventListener('keydown', e => { if(e.key === 'Enter' || e.key === ' '){e.preventDefault();handler.call(box);} });
});
</script>
@endpush
