@extends('layouts.cleaning.master')

@section('page_title', 'Bảng điều khiển – DomusHub')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cleaning-tasks.css') }}">
<style>
    .greeting{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
    .greeting h1{font-size:24px;font-weight:800;color:#1B2559;letter-spacing:-.3px;}
    .greeting p{font-size:14px;color:#707EAE;margin-top:4px;}
    .btn-report{display:inline-flex;align-items:center;gap:8px;background:#3652D9;color:white;padding:12px 20px;border-radius:10px;border:none;font-size:13px;font-weight:600;cursor:pointer;transition:.2s;text-decoration:none;font-family:inherit;}
    .btn-report:hover{background:#2a43b8;transform:translateY(-1px);box-shadow:0 6px 16px rgba(54,82,217,.3);}
    .kpi-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
    .kpi-card{background:white;border-radius:14px;padding:20px;box-shadow:0 2px 8px rgba(54,82,217,.04);display:flex;align-items:center;gap:14px;}
    .kpi-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
    .kpi-icon--blue{background:#EEF2FF;color:#3652D9;}
    .kpi-icon--orange{background:#FFF4E5;color:#FF9B05;}
    .kpi-icon--green{background:#E6F9F0;color:#05CD99;}
    .kpi-icon--red{background:#FFF0F0;color:#EE5D50;}
    .kpi-value{font-size:28px;font-weight:800;color:#1B2559;line-height:1;}
    .kpi-label{font-size:11.5px;color:#A3AED0;margin-top:2px;}
    .section-title{font-size:16px;font-weight:700;color:#1B2559;margin-bottom:14px;display:flex;align-items:center;gap:8px;}
    .section-title span{font-size:12px;color:#A3AED0;font-weight:500;}
    @media(max-width:768px){.kpi-row{grid-template-columns:1fr 1fr;}.greeting{flex-direction:column;}}
</style>
@endpush

@section('topbar_left')
<div class="topbar-search">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" placeholder="Tìm kiếm công việc, khu vực..." aria-label="Tìm kiếm">
</div>
@endsection

@section('content')

<!-- GREETING -->
<div class="greeting">
    <div>
        @php
            $hour = now()->hour;
            $greet = $hour < 12 ? 'Chào buổi sáng' : ($hour < 18 ? 'Chào buổi chiều' : 'Chào buổi tối');
        @endphp
        <h1>{{ $greet }}, {{ explode(' ', auth()->user()->name)[0] }}</h1>
        <p>Hôm nay bạn có <strong>{{ $pending + $progress }}</strong> công việc cần xử lý.</p>
    </div>
    <a href="{{ route('cleaning.report') }}" class="btn-report">
        <i class="fa-solid fa-plus"></i> Báo cáo sự cố mới
    </a>
</div>

<!-- KPI -->
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--blue"><i class="fa-solid fa-list-check"></i></div>
        <div><div class="kpi-value">{{ str_pad($total, 2, '0', STR_PAD_LEFT) }}</div><div class="kpi-label">Tổng công việc</div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--orange"><i class="fa-solid fa-spinner"></i></div>
        <div><div class="kpi-value">{{ str_pad($progress, 2, '0', STR_PAD_LEFT) }}</div><div class="kpi-label">Đang thực hiện</div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--green"><i class="fa-solid fa-circle-check"></i></div>
        <div><div class="kpi-value">{{ str_pad($done, 2, '0', STR_PAD_LEFT) }}</div><div class="kpi-label">Đã hoàn thành</div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--red"><i class="fa-solid fa-clock"></i></div>
        <div><div class="kpi-value">{{ str_pad($pending, 2, '0', STR_PAD_LEFT) }}</div><div class="kpi-label">Chờ xử lý</div></div>
    </div>
</div>

<!-- CHƯA HOÀN THÀNH -->
<div class="section-title" style="color:#EE5D50;">Chưa hoàn thành <span>({{ $activeTasks->count() }})</span></div>

@if($activeTasks->count() > 0)
<div class="task-list" style="margin-bottom:28px;">
    @foreach($activeTasks as $task)
    @php
        $doneSteps = collect($task->checklist ?? [])->where('done', true)->count();
        $totalSteps = count($task->checklist ?? []);
        $endTime = \Carbon\Carbon::parse($task->task_date->format('Y-m-d') . ' ' . $task->end_time);
        $minutesLeft = now()->diffInMinutes($endTime, false);
        $isUrgent = $minutesLeft > 0 && $minutesLeft <= 90;
    @endphp
    <div class="task-item" data-id="{{ $task->id }}" data-status="{{ $task->status }}" tabindex="0">
        <div class="task-item__indicator task-item__indicator--{{ $task->priority }}" aria-hidden="true"></div>
        <div class="task-item__body" onclick="window.location='{{ route('cleaning.tasks.show', $task->id) }}'" role="link" style="cursor:pointer;">
            <div class="task-item__title">{{ $task->title }}</div>
            <div class="task-item__desc">{{ $task->description }}</div>
            <div class="task-item__meta">
                <span class="task-meta-tag"><i class="fa-solid fa-location-dot"></i> {{ $task->area }}</span>
                <span class="task-meta-tag"><i class="fa-solid fa-clock"></i> {{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($task->end_time)->format('H:i') }}</span>
                <span class="task-meta-tag"><i class="fa-solid fa-check-double"></i> {{ $doneSteps }}/{{ $totalSteps }} bước</span>
                @if($isUrgent)
                <span class="deadline-warn"><i class="fa-solid fa-bolt"></i> Còn {{ $minutesLeft }} phút</span>
                @endif
            </div>
        </div>
        <div class="task-item__right">
            <span class="priority-badge priority-badge--{{ $task->priority }}">{{ $task->priority === 'high' ? 'Cao' : ($task->priority === 'medium' ? 'TB' : 'Thấp') }}</span>
            <span class="status-badge status-badge--{{ $task->status }}">{{ $task->status === 'progress' ? 'Đang làm' : 'Chờ xử lý' }}</span>
            <button class="quick-action" aria-pressed="false" aria-label="Đánh dấu hoàn thành" data-task-id="{{ $task->id }}">
                <i class="fa-solid fa-check"></i>
            </button>
        </div>
    </div>
    @endforeach
</div>
@endif

<!-- ĐÃ HOÀN THÀNH -->
<div class="section-title" style="color:#05CD99;">Đã hoàn thành <span>({{ $doneTasks->count() }})</span></div>

@if($doneTasks->count() > 0)
<div class="task-list">
    @foreach($doneTasks as $task)
    @php
        $doneSteps = collect($task->checklist ?? [])->where('done', true)->count();
        $totalSteps = count($task->checklist ?? []);
    @endphp
    <div class="task-item" style="opacity:.7;" tabindex="0">
        <div class="task-item__indicator" style="background:#05CD99;" aria-hidden="true"></div>
        <div class="task-item__body" onclick="window.location='{{ route('cleaning.tasks.show', $task->id) }}'" role="link" style="cursor:pointer;">
            <div class="task-item__title" style="text-decoration:line-through;color:#A3AED0;">{{ $task->title }}</div>
            <div class="task-item__desc">{{ $task->description }}</div>
            <div class="task-item__meta">
                <span class="task-meta-tag"><i class="fa-solid fa-location-dot"></i> {{ $task->area }}</span>
                <span class="task-meta-tag"><i class="fa-solid fa-clock"></i> {{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($task->end_time)->format('H:i') }}</span>
                <span class="task-meta-tag" style="color:#05CD99;"><i class="fa-solid fa-circle-check"></i> Xong lúc {{ $task->completed_at?->format('H:i') }}</span>
            </div>
        </div>
        <div class="task-item__right">
            <span class="status-badge status-badge--done">Hoàn thành</span>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

document.querySelectorAll('.quick-action').forEach(btn => {
    btn.addEventListener('click', async function(e){
        e.stopPropagation();
        const taskId = this.dataset.taskId;
        const item = this.closest('.task-item');
        try {
            const res = await fetch(`/cleaning/tasks/${taskId}/status`, {
                method:'PATCH',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
                body:JSON.stringify({status:'done'})
            });
            if(!res.ok) throw new Error();
            item.style.opacity = '0.5';
            item.style.transform = 'translateX(20px)';
            setTimeout(() => { item.remove(); updateKPI(); }, 300);
        } catch(err){ alert('Không thể cập nhật.'); }
    });
});

function updateKPI(){
    // Reload to get fresh data
    location.reload();
}

document.querySelectorAll('.task-item').forEach(item => {
    item.addEventListener('keydown', e => { if(e.key === 'Enter') item.querySelector('.task-item__body').click(); });
});
</script>
@endpush
