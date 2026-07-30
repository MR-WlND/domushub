@extends('layouts.cleaning.master')

@section('page_title', 'Công việc hàng ngày – DomusHub')

@push('styles')
<style>
/* ═══════════════════════════════════════════════════════
   CLEANING TASKS — Timeline Layout v2
   ═══════════════════════════════════════════════════════ */

/* Page Header */
.page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.page-header__left {}
.page-header__title { font-size:22px; font-weight:800; color:#1B2559; }
.page-header__date { font-size:13px; color:#707EAE; margin-top:4px; }
.page-header__right { display:flex; align-items:center; gap:8px; }
.search-inline { padding:10px 14px; border:1px solid #E9EDF7; border-radius:10px; font-size:13px; font-family:inherit; color:#1B2559; background:white; transition:border-color .2s; width:220px; }
.search-inline:focus { outline:none; border-color:#3652D9; box-shadow:0 0 0 3px rgba(54,82,217,.08); }
.search-inline::placeholder { color:#A3AED0; }

/* Stats Strip */
.stats-strip { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:20px; }
.stat-pill { text-align:center; padding:16px 12px; border-radius:12px; cursor:pointer; transition:.2s; background:white; border:1.5px solid #E9EDF7; box-shadow:0 1px 4px rgba(54,82,217,.04); }
.stat-pill:hover { border-color:#3652D9; box-shadow:0 2px 8px rgba(54,82,217,.08); }
.stat-pill.active { background:#3652D9; border-color:#3652D9; box-shadow:0 4px 12px rgba(54,82,217,.2); }
.stat-pill.active .stat-pill__val,
.stat-pill.active .stat-pill__label { color:white; }
.stat-pill__val { font-size:24px; font-weight:800; color:#1B2559; line-height:1; }
.stat-pill__label { font-size:11px; color:#A3AED0; margin-top:6px; font-weight:600; text-transform:uppercase; letter-spacing:.3px; }

/* Progress Bar */
.progress-slim { background:#E9EDF7; height:6px; border-radius:3px; margin-bottom:28px; overflow:hidden; }
.progress-slim__fill { height:100%; border-radius:3px; background:#3652D9; transition:width .5s ease; }

/* Timeline Container */
.timeline { position:relative; padding-left:56px; }
.timeline::before { content:''; position:absolute; left:22px; top:0; bottom:0; width:2px; background:#E9EDF7; }

/* Now Indicator */
.now-indicator { position:relative; margin-bottom:20px; padding-left:0; }
.now-indicator::before { content:''; position:absolute; left:-56px; right:0; top:50%; height:1px; border-top:2px dashed #3652D9; opacity:.4; }
.now-indicator__label { position:absolute; left:-56px; top:50%; transform:translateY(-50%); background:#3652D9; color:white; font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; white-space:nowrap; }
.now-indicator__dot { position:absolute; left:-40px; top:50%; transform:translate(-50%,-50%); width:12px; height:12px; border-radius:50%; background:#3652D9; box-shadow:0 0 0 4px rgba(54,82,217,.2); z-index:2; }

/* Time Slot */
.time-slot { position:relative; margin-bottom:28px; }
.time-slot:last-child { margin-bottom:0; }
.time-slot__marker { position:absolute; left:-56px; top:2px; width:44px; text-align:center; }
.time-slot__time { font-size:12px; font-weight:700; color:#707EAE; }
.time-slot__dot { width:10px; height:10px; border-radius:50%; background:#D8E0F0; margin:6px auto 0; position:relative; z-index:1; }
.time-slot__dot--active { background:#3652D9; box-shadow:0 0 0 4px rgba(54,82,217,.15); }
.time-slot__dot--done { background:#05CD99; }

/* Task Card */
.task-card { background:white; border-radius:12px; padding:16px 20px; margin-bottom:8px; box-shadow:0 1px 4px rgba(54,82,217,.04); border-left:4px solid transparent; transition:box-shadow .2s, border-left-width .15s; cursor:pointer; display:flex; align-items:center; gap:16px; }
.task-card:hover { box-shadow:0 6px 20px rgba(54,82,217,.1); border-left-width:6px; }
.task-card--high { border-left-color:#EE5D50; }
.task-card--medium { border-left-color:#FFB547; }
.task-card--low { border-left-color:#05CD99; }
.task-card--done { border-left-color:#05CD99; }
.task-card--done .task-card__title { text-decoration:line-through; color:#A3AED0; }
.task-card--done .task-card__area { color:#CBD5E1; }

/* Task Card Content */
.task-card__body { flex:1; min-width:0; }
.task-card__title { font-size:14px; font-weight:700; color:#1B2559; margin-bottom:3px; }
.task-card__area { font-size:12px; color:#707EAE; }
.task-card__row { display:flex; align-items:center; gap:10px; margin-top:8px; flex-wrap:wrap; }

/* Micro Progress (checklist) */
.micro-progress { display:flex; gap:3px; align-items:center; }
.micro-progress__bar { width:56px; height:4px; border-radius:2px; background:#E9EDF7; overflow:hidden; }
.micro-progress__fill { height:100%; background:#3652D9; border-radius:2px; transition:width .3s; }
.micro-progress__text { font-size:11px; color:#A3AED0; font-weight:600; }

/* Tags — max 2 visible (status + urgent/time) */
.tag { display:inline-block; padding:4px 10px; border-radius:5px; font-size:11px; font-weight:700; letter-spacing:.2px; }
.tag--progress { background:#EEF2FF; color:#3652D9; }
.tag--pending { background:#F4F7FE; color:#94A3B8; }
.tag--done { background:#E6F9F0; color:#05CD99; }
.tag--urgent { background:#FEF2F2; color:#EE5D50; animation:pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:.6;} }
.tag--time { background:none; padding:0; color:#05CD99; font-size:11px; font-weight:600; }

/* Action Button */
.task-card__action { flex-shrink:0; }
.btn-done { width:34px; height:34px; border-radius:8px; border:2px solid #D8E0F0; background:white; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:.2s; font-size:13px; font-weight:800; color:#D8E0F0; }
.btn-done:hover { border-color:#05CD99; color:#05CD99; background:#E6F9F0; }
.btn-done--checked { border-color:#05CD99; background:#05CD99; color:white; pointer-events:none; }

/* Done section (always visible) */
.done-section { margin-top:32px; }
.done-section__title { font-size:13px; font-weight:700; color:#707EAE; margin-bottom:12px; padding-bottom:8px; border-bottom:1px solid #E9EDF7; }
.done-list { display:flex; flex-direction:column; gap:8px; }
.done-list .task-card { opacity:.55; }
.done-list .task-card:hover { opacity:.8; }

/* Empty */
.empty { text-align:center; padding:48px 20px; color:#A3AED0; font-size:14px; }

/* Toast */
.toast-cleaning { position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(100px); background:#1B2559; color:white; padding:12px 24px; border-radius:10px; font-size:13px; font-weight:600; z-index:9999; transition:transform .3s ease; box-shadow:0 8px 24px rgba(0,0,0,.15); }
.toast-cleaning--visible { transform:translateX(-50%) translateY(0); }

/* Responsive */
@media(max-width:768px) {
    .page-header { flex-direction:column; }
    .page-header__right { width:100%; }
    .search-inline { width:100%; }
    .timeline { padding-left:44px; }
    .timeline::before { left:16px; }
    .time-slot__marker { left:-44px; width:36px; }
    .task-card { padding:14px 16px; gap:12px; }
    .stats-strip { grid-template-columns:repeat(2,1fr); }
    .stat-pill { min-width:auto; }
    .now-indicator::before { left:-44px; }
    .now-indicator__label { left:-44px; }
    .now-indicator__dot { left:-28px; }
}

@media(prefers-reduced-motion:reduce) {
    .tag--urgent { animation:none; }
    .progress-slim__fill { transition:none; }
}
</style>
@endpush

@section('content')

{{-- Header with inline search --}}
<div class="page-header">
    <div class="page-header__left">
        <div class="page-header__title">Công việc hôm nay</div>
        <div class="page-header__date">{{ now()->isoFormat('dddd, D MMMM Y') }}</div>
    </div>
    <div class="page-header__right">
        <input type="text" class="search-inline" id="searchInput" placeholder="Tìm kiếm..." aria-label="Tìm kiếm">
    </div>
</div>

{{-- Stats --}}
<div class="stats-strip" role="tablist">
    <div class="stat-pill active" role="tab" aria-selected="true" data-filter="all">
        <div class="stat-pill__val">{{ $total }}</div>
        <div class="stat-pill__label">Tất cả</div>
    </div>
    <div class="stat-pill" role="tab" aria-selected="false" data-filter="pending">
        <div class="stat-pill__val">{{ $pending }}</div>
        <div class="stat-pill__label">Chờ</div>
    </div>
    <div class="stat-pill" role="tab" aria-selected="false" data-filter="progress">
        <div class="stat-pill__val">{{ $progress }}</div>
        <div class="stat-pill__label">Đang làm</div>
    </div>
    <div class="stat-pill" role="tab" aria-selected="false" data-filter="done">
        <div class="stat-pill__val">{{ $done }}</div>
        <div class="stat-pill__label">Xong</div>
    </div>
</div>

{{-- Progress --}}
<div class="progress-slim" role="progressbar" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
    <div class="progress-slim__fill" id="progressFill" style="width:{{ $percentage }}%"></div>
</div>

{{-- Timeline --}}
@php
    $byTime = $tasks->groupBy(fn($t) => \Carbon\Carbon::parse($t->start_time)->format('H:i'));
    $activeTasks = $tasks->whereIn('status', ['pending', 'progress']);
    $doneTasks = $tasks->where('status', 'done');
    $activeByTime = $activeTasks->groupBy(fn($t) => \Carbon\Carbon::parse($t->start_time)->format('H:i'));
    $nowStr = now()->format('H:i');
    $nowInserted = false;
@endphp

@if($activeTasks->count() > 0)
<div class="timeline" id="timeline">
    @foreach($activeByTime as $time => $timeTasks)
        {{-- Now indicator --}}
        @if(!$nowInserted && $time > $nowStr)
        <div class="now-indicator">
            <span class="now-indicator__label">{{ $nowStr }}</span>
            <span class="now-indicator__dot"></span>
        </div>
        @php $nowInserted = true; @endphp
        @endif

    <div class="time-slot" data-time="{{ $time }}">
        <div class="time-slot__marker">
            <div class="time-slot__time">{{ $time }}</div>
            @php
                $hasProgress = $timeTasks->contains(fn($t) => $t->status === 'progress');
            @endphp
            <div class="time-slot__dot {{ $hasProgress ? 'time-slot__dot--active' : '' }}"></div>
        </div>

        @foreach($timeTasks as $task)
        @php
            $doneSteps = collect($task->checklist ?? [])->where('done', true)->count();
            $totalSteps = count($task->checklist ?? []);
            $checkPct = $totalSteps > 0 ? round(($doneSteps / $totalSteps) * 100) : 0;
            $endTime = \Carbon\Carbon::parse($task->task_date->format('Y-m-d') . ' ' . $task->end_time);
            $minutesLeft = now()->diffInMinutes($endTime, false);
            $isUrgent = $minutesLeft > 0 && $minutesLeft <= 60;
        @endphp
        <div class="task-card task-card--{{ $task->priority }}"
             data-id="{{ $task->id }}" data-status="{{ $task->status }}" tabindex="0"
             onclick="window.location='{{ route('cleaning.tasks.show', $task->id) }}'">
            
            <div class="task-card__body">
                <div class="task-card__title">{{ $task->title }}</div>
                <div class="task-card__area">{{ $task->area }} — {{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($task->end_time)->format('H:i') }}</div>
                <div class="task-card__row">
                    <span class="tag tag--{{ $task->status }}">{{ $task->status === 'progress' ? 'Đang làm' : 'Chờ' }}</span>

                    @if($totalSteps > 0)
                    <div class="micro-progress">
                        <div class="micro-progress__bar"><div class="micro-progress__fill" style="width:{{ $checkPct }}%"></div></div>
                        <span class="micro-progress__text">{{ $doneSteps }}/{{ $totalSteps }}</span>
                    </div>
                    @endif

                    @if($isUrgent)
                    <span class="tag tag--urgent">Còn {{ $minutesLeft }}p</span>
                    @endif
                </div>
            </div>

            <div class="task-card__action" onclick="event.stopPropagation();">
                <button class="btn-done" data-task-id="{{ $task->id }}" title="Hoàn thành">✓</button>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach

    {{-- Now indicator at end if not inserted yet --}}
    @if(!$nowInserted)
    <div class="now-indicator">
        <span class="now-indicator__label">{{ $nowStr }}</span>
        <span class="now-indicator__dot"></span>
    </div>
    @endif
</div>
@elseif($doneTasks->count() === 0)
<div class="empty">Không có công việc nào hôm nay.</div>
@endif

{{-- Done section (always visible) --}}
@if($doneTasks->count() > 0)
<div class="done-section">
    <div class="done-section__title">Đã hoàn thành ({{ $doneTasks->count() }})</div>
    <div class="done-list">
        @foreach($doneTasks as $task)
        <div class="task-card task-card--done" tabindex="0"
             onclick="window.location='{{ route('cleaning.tasks.show', $task->id) }}'">
            <div class="task-card__body">
                <div class="task-card__title">{{ $task->title }}</div>
                <div class="task-card__area">{{ $task->area }}</div>
                <div class="task-card__row">
                    <span class="tag tag--done">Xong</span>
                    @if($task->completed_at)
                    <span class="tag tag--time">Xong lúc {{ $task->completed_at->format('H:i') }}</span>
                    @endif
                </div>
            </div>
            <div class="task-card__action">
                <span class="btn-done btn-done--checked">✓</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Empty filter state --}}
<div class="empty" id="emptyFilter" style="display:none;">Không tìm thấy công việc phù hợp.</div>

{{-- Toast --}}
<div class="toast-cleaning" id="toast"></div>

@endsection

@push('scripts')
<script>
(function(){
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('toast-cleaning--visible');
        setTimeout(() => t.classList.remove('toast-cleaning--visible'), 2500);
    }

    // Filter tabs
    document.querySelectorAll('.stat-pill').forEach(pill => {
        pill.addEventListener('click', function() {
            document.querySelectorAll('.stat-pill').forEach(p => { p.classList.remove('active'); p.setAttribute('aria-selected','false'); });
            this.classList.add('active');
            this.setAttribute('aria-selected','true');
            const filter = this.dataset.filter;

            // Show/hide in active timeline
            let visible = 0;
            document.querySelectorAll('#timeline .task-card').forEach(card => {
                const show = filter === 'all' || filter === 'pending' || filter === 'progress' ? 
                    (filter === 'all' || card.dataset.status === filter) : false;
                card.style.display = show ? '' : 'none';
                if(show) visible++;
            });

            // Show/hide done section
            const doneSection = document.querySelector('.done-section');
            if (doneSection) {
                doneSection.style.display = (filter === 'done' || filter === 'all') ? '' : 'none';
            }

            // Show timeline only for non-done filters
            const timeline = document.getElementById('timeline');
            if (timeline) {
                timeline.style.display = filter === 'done' ? 'none' : '';
            }

            // Hide empty time slots
            document.querySelectorAll('.time-slot').forEach(slot => {
                let has = false;
                slot.querySelectorAll('.task-card').forEach(c => { if(c.style.display !== 'none') has = true; });
                slot.style.display = has ? '' : 'none';
            });

            if (filter === 'done') visible = document.querySelectorAll('.done-list .task-card').length;
            document.getElementById('emptyFilter').style.display = visible === 0 && filter !== 'all' ? '' : 'none';
        });
    });

    // Search
    document.getElementById('searchInput').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.task-card').forEach(card => {
            card.style.display = card.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
        document.querySelectorAll('.time-slot').forEach(slot => {
            let has = false;
            slot.querySelectorAll('.task-card').forEach(c => { if(c.style.display !== 'none') has = true; });
            slot.style.display = has ? '' : 'none';
        });
    });

    // Quick done
    document.querySelectorAll('.btn-done[data-task-id]').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.stopPropagation();
            const id = this.dataset.taskId;
            const card = this.closest('.task-card');
            this.textContent = '·';
            try {
                const res = await fetch(`/cleaning/tasks/${id}/status`, {
                    method:'PATCH',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},
                    body:JSON.stringify({status:'done'})
                });
                if(!res.ok) throw new Error();
                
                // Animate out
                card.style.transition = 'opacity .3s, transform .3s';
                card.style.opacity = '0';
                card.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    card.remove();
                    updateStats();
                    // Check if time-slot is empty
                    document.querySelectorAll('.time-slot').forEach(slot => {
                        if (slot.querySelectorAll('.task-card').length === 0) slot.remove();
                    });
                }, 300);
                showToast('Đã hoàn thành!');
            } catch(e) {
                this.textContent = '✓';
                showToast('Lỗi, thử lại.');
            }
        });
    });

    function updateStats() {
        // We reload stats from remaining DOM — approximate
        const timeline = document.getElementById('timeline');
        const remaining = timeline ? timeline.querySelectorAll('.task-card').length : 0;
        const doneCount = document.querySelectorAll('.done-list .task-card').length + 
            (document.querySelectorAll('#timeline .task-card[data-status="done"]').length);
        // Simple: just reload for accurate counts
        // For now, update progress bar
        const allCards = document.querySelectorAll('.task-card').length;
        const pills = document.querySelectorAll('.stat-pill');
        const pct = allCards > 0 ? Math.round(((allCards - remaining) / allCards) * 100) : 100;
        document.getElementById('progressFill').style.width = pct + '%';
    }

    // Keyboard
    document.querySelectorAll('.task-card').forEach(card => {
        card.addEventListener('keydown', e => { if(e.key === 'Enter') card.click(); });
    });
})();
</script>
@endpush
