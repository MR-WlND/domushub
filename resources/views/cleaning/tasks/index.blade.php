@extends('layouts.cleaning.master')

@section('page_title', 'Công việc hàng ngày – DomusHub')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cleaning-tasks.css') }}">
<style>
@import url('{{ asset("css/cleaning-tasks.css") }}');
</style>
@endpush

@section('topbar_left')
<div class="topbar-search">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input type="text" placeholder="Tìm kiếm công việc, khu vực..." id="searchInput" aria-label="Tìm kiếm công việc">
</div>
@endsection

@section('content')

<!-- SHIFT BANNER -->
<div class="shift-banner" role="banner">
    <div class="shift-banner__left">
        <div class="shift-banner__icon" aria-hidden="true"><i class="fa-solid fa-sun"></i></div>
        <div>
            <div class="shift-banner__title">Ca sáng</div>
            <div class="shift-banner__sub">06:00 – 14:00 • {{ auth()->user()->name }}</div>
        </div>
    </div>
    <div class="shift-banner__right">
        <div class="shift-banner__time" id="currentTime">{{ now()->format('H:i') }}</div>
        <div class="shift-banner__date">{{ now()->isoFormat('dddd, D/MM/Y') }}</div>
    </div>
</div>

<!-- PROGRESS -->
<div class="day-progress" role="progressbar" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100" aria-label="Tiến độ hôm nay">
    <div class="day-progress__header">
        <span class="day-progress__title">Tiến độ hôm nay</span>
        <span class="day-progress__count" id="progressCount">{{ $done }}/{{ $total }} hoàn thành</span>
    </div>
    <div class="day-progress__bar">
        <div class="day-progress__fill" id="progressFill" style="width:{{ $percentage }}%;"></div>
    </div>
    <div class="day-progress__text" id="progressText">Bạn đã hoàn thành {{ $percentage }}% công việc trong ca. Còn {{ $total - $done }} việc cần xử lý.</div>
</div>

<!-- CONTROLS -->
<div class="controls-row">
    <div class="tabs" role="tablist" aria-label="Lọc theo trạng thái">
        <button class="tab" role="tab" aria-selected="true" data-filter="all">Tất cả <span class="badge">{{ $total }}</span></button>
        <button class="tab" role="tab" aria-selected="false" data-filter="progress">Đang làm <span class="badge">{{ $progress }}</span></button>
        <button class="tab" role="tab" aria-selected="false" data-filter="pending">Chờ xử lý <span class="badge">{{ $pending }}</span></button>
        <button class="tab" role="tab" aria-selected="false" data-filter="done">Hoàn thành <span class="badge">{{ $done }}</span></button>
    </div>
</div>

<!-- TASK LIST GROUPED BY AREA -->
@forelse($grouped as $areaName => $areaTasks)
<div class="area-group" data-area="{{ Str::slug($areaName) }}">
    <div class="area-group__header">
        <span class="area-group__label">{{ $areaName }}</span>
        <span class="area-group__count">{{ $areaTasks->count() }} việc</span>
    </div>
    <div class="task-list">
        @foreach($areaTasks as $task)
        @php
            $doneSteps = collect($task->checklist ?? [])->where('done', true)->count();
            $totalSteps = count($task->checklist ?? []);
            $endTime = \Carbon\Carbon::parse($task->task_date->format('Y-m-d') . ' ' . $task->end_time);
            $minutesLeft = now()->diffInMinutes($endTime, false);
            $isUrgent = $task->status !== 'done' && $minutesLeft > 0 && $minutesLeft <= 90;
        @endphp
        <div class="task-item" data-id="{{ $task->id }}" data-status="{{ $task->status }}" data-time="{{ str_replace(':', '', $task->start_time) }}" tabindex="0"
             role="article" aria-label="{{ $task->title }}">
            <div class="task-item__indicator task-item__indicator--{{ $task->priority }}" aria-hidden="true"></div>
            <div class="task-item__body" onclick="window.location='{{ route('cleaning.tasks.show', $task->id) }}'" role="link">
                <div class="task-item__title">{{ $task->title }}</div>
                <div class="task-item__desc">{{ $task->description }}</div>
                <div class="task-item__meta">
                    <span class="task-meta-tag"><i class="fa-solid fa-clock" aria-hidden="true"></i> {{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($task->end_time)->format('H:i') }}</span>
                    <span class="task-meta-tag"><i class="fa-solid fa-check-double" aria-hidden="true"></i> {{ $doneSteps }}/{{ $totalSteps }} bước</span>
                    @if($task->status === 'done' && $task->completed_at)
                    <span class="task-meta-tag" style="color:#05CD99;"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Xong lúc {{ $task->completed_at->format('H:i') }}</span>
                    @elseif($isUrgent)
                    <span class="deadline-warn" aria-live="polite"><i class="fa-solid fa-bolt" aria-hidden="true"></i> Còn {{ $minutesLeft }} phút</span>
                    @endif
                </div>
            </div>
            <div class="task-item__right">
                <span class="priority-badge priority-badge--{{ $task->priority }}">{{ $task->priority === 'high' ? 'Cao' : ($task->priority === 'medium' ? 'TB' : 'Thấp') }}</span>
                <span class="status-badge status-badge--{{ $task->status }}">{{ $task->status === 'done' ? 'Hoàn thành' : ($task->status === 'progress' ? 'Đang làm' : 'Chờ xử lý') }}</span>
                <button class="quick-action" 
                        aria-pressed="{{ $task->status === 'done' ? 'true' : 'false' }}" 
                        aria-label="{{ $task->status === 'done' ? 'Đã hoàn thành' : 'Đánh dấu hoàn thành' }}"
                        data-task-id="{{ $task->id }}"
                        {{ $task->status === 'done' ? 'disabled' : '' }}>
                    <i class="fa-solid fa-check"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
<div class="empty-state" role="status">
    <i class="fa-solid fa-clipboard-check" aria-hidden="true"></i>
    <p>Không có công việc nào hôm nay.</p>
</div>
@endforelse

<!-- EMPTY STATE FOR FILTERS -->
<div class="empty-state" id="emptyFilter" style="display:none;" role="status">
    <i class="fa-solid fa-filter-circle-xmark" aria-hidden="true"></i>
    <p>Không có công việc nào trong nhóm này.</p>
</div>

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// Tab filtering with keyboard support
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', handleTabClick);
    tab.addEventListener('keydown', e => { if(e.key === 'Enter' || e.key === ' '){e.preventDefault();handleTabClick.call(tab);} });
});

function handleTabClick() {
    document.querySelectorAll('.tab').forEach(t => t.setAttribute('aria-selected', 'false'));
    this.setAttribute('aria-selected', 'true');
    const filter = this.dataset.filter;
    let visibleCount = 0;
    document.querySelectorAll('.task-item').forEach(item => {
        const show = filter === 'all' || item.dataset.status === filter;
        item.style.display = show ? '' : 'none';
        if(show) visibleCount++;
    });
    document.querySelectorAll('.area-group').forEach(group => {
        let hasVisible = false;
        group.querySelectorAll('.task-item').forEach(i => { if(i.style.display !== 'none') hasVisible = true; });
        group.style.display = hasVisible ? '' : 'none';
    });
    document.getElementById('emptyFilter').style.display = visibleCount === 0 ? '' : 'none';
}

// Quick action - update status via API
document.querySelectorAll('.quick-action:not([disabled])').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.stopPropagation();
        const taskId = this.dataset.taskId;
        const item = this.closest('.task-item');
        
        try {
            const res = await fetch(`/cleaning/tasks/${taskId}/status`, {
                method: 'PATCH',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
                body: JSON.stringify({status: 'done'})
            });
            if(!res.ok) throw new Error('Failed');
            
            // Update UI
            this.setAttribute('aria-pressed', 'true');
            this.setAttribute('disabled', '');
            this.setAttribute('aria-label', 'Đã hoàn thành');
            item.dataset.status = 'done';
            const badge = item.querySelector('.status-badge');
            if(badge){badge.className='status-badge status-badge--done';badge.textContent='Hoàn thành';}
            const warn = item.querySelector('.deadline-warn');
            if(warn) warn.remove();
            
            // Add completion time
            const meta = item.querySelector('.task-item__meta');
            const timeTag = document.createElement('span');
            timeTag.className = 'task-meta-tag';
            timeTag.style.color = '#05CD99';
            const now = new Date();
            timeTag.innerHTML = `<i class="fa-solid fa-circle-check"></i> Xong lúc ${now.getHours().toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')}`;
            meta.appendChild(timeTag);
            
            updateProgress();
            checkCelebration();
        } catch(err) {
            alert('Không thể cập nhật. Vui lòng thử lại.');
        }
    });
});

function updateProgress(){
    const total = document.querySelectorAll('.task-item').length;
    const done = document.querySelectorAll('.task-item[data-status="done"]').length;
    const progress = document.querySelectorAll('.task-item[data-status="progress"]').length;
    const pending = document.querySelectorAll('.task-item[data-status="pending"]').length;
    const pct = total > 0 ? Math.round((done/total)*100) : 0;
    
    document.getElementById('progressCount').textContent = `${done}/${total} hoàn thành`;
    document.getElementById('progressFill').style.width = `${pct}%`;
    document.getElementById('progressText').textContent = `Bạn đã hoàn thành ${pct}% công việc trong ca. Còn ${total-done} việc cần xử lý.`;
    document.querySelector('[role="progressbar"]').setAttribute('aria-valuenow', pct);
    
    // Update tab badges
    const tabs = document.querySelectorAll('.tab');
    tabs[0].querySelector('.badge').textContent = total;
    tabs[1].querySelector('.badge').textContent = progress;
    tabs[2].querySelector('.badge').textContent = pending;
    tabs[3].querySelector('.badge').textContent = done;
}

function checkCelebration(){
    // disabled
}

// Search
document.getElementById('searchInput').addEventListener('input', function(){
    const q = this.value.toLowerCase();
    document.querySelectorAll('.task-item').forEach(item => {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(q) ? '' : 'none';
    });
    document.querySelectorAll('.area-group').forEach(group => {
        let hasVisible = false;
        group.querySelectorAll('.task-item').forEach(i => { if(i.style.display !== 'none') hasVisible = true; });
        group.style.display = hasVisible ? '' : 'none';
    });
});

// Keyboard nav on task items
document.querySelectorAll('.task-item').forEach(item => {
    item.addEventListener('keydown', e => {
        if(e.key === 'Enter') item.querySelector('.task-item__body').click();
    });
});

// Live clock
setInterval(() => {
    const now = new Date();
    const el = document.getElementById('currentTime');
    if(el) el.textContent = now.getHours().toString().padStart(2,'0')+':'+now.getMinutes().toString().padStart(2,'0');
}, 60000);
</script>
@endpush
