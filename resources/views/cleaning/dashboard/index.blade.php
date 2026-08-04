@extends('layouts.cleaning.master')

@section('page_title', 'Bảng điều khiển – DomusHub')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/cleaning-tasks.css') }}">
<style>
    .greeting { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .greeting h1 { font-size:22px; font-weight:800; color:#1B2559; letter-spacing:-.3px; }
    .greeting p { font-size:14px; color:#707EAE; margin-top:4px; }
    .greeting p strong { color:#1B2559; }
    .btn-report { display:inline-flex; align-items:center; gap:8px; background:#3652D9; color:white; padding:11px 18px; border-radius:10px; border:none; font-size:13px; font-weight:600; cursor:pointer; transition:.2s; text-decoration:none; font-family:inherit; }
    .btn-report:hover { background:#2a43b8; transform:translateY(-1px); box-shadow:0 6px 16px rgba(54,82,217,.3); }

    /* KPI Row */
    .kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:28px; }
    .kpi-card { background:white; border-radius:14px; padding:18px 20px; box-shadow:0 2px 8px rgba(54,82,217,.04); display:flex; align-items:center; gap:14px; transition:box-shadow .2s; }
    .kpi-card:hover { box-shadow:0 4px 16px rgba(54,82,217,.08); }
    .kpi-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:17px; flex-shrink:0; }
    .kpi-icon--blue { background:#EEF2FF; color:#3652D9; }
    .kpi-icon--orange { background:#FFF4E5; color:#FF9B05; }
    .kpi-icon--green { background:#E6F9F0; color:#05CD99; }
    .kpi-icon--red { background:#FFF0F0; color:#EE5D50; }
    .kpi-value { font-size:26px; font-weight:800; color:#1B2559; line-height:1; }
    .kpi-label { font-size:11.5px; color:#A3AED0; margin-top:3px; font-weight:500; }

    /* Dashboard Grid */
    .dash-grid { display:grid; grid-template-columns:1fr 340px; gap:20px; }
    .dash-main { min-width:0; }
    .dash-side { display:flex; flex-direction:column; gap:16px; }

    /* Card wrapper */
    .dash-card { background:white; border-radius:14px; padding:20px; box-shadow:0 2px 8px rgba(54,82,217,.04); }
    .dash-card__header { display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .dash-card__title { font-size:15px; font-weight:700; color:#1B2559; }
    .dash-card__link { font-size:12px; font-weight:600; color:#3652D9; text-decoration:none; }
    .dash-card__link:hover { text-decoration:underline; }

    /* Priority Table */
    .priority-table { width:100%; border-collapse:collapse; }
    .priority-table th { font-size:11px; color:#A3AED0; font-weight:600; text-transform:uppercase; letter-spacing:.5px; text-align:left; padding:8px 12px; border-bottom:1px solid #F4F7FE; }
    .priority-table td { padding:12px; border-bottom:1px solid #F4F7FE; font-size:13px; vertical-align:middle; }
    .priority-table tr:last-child td { border-bottom:none; }
    .priority-table tr:hover td { background:#FAFBFF; }
    .td-location__name { font-weight:600; color:#1B2559; }
    .td-location__sub { font-size:11px; color:#A3AED0; margin-top:2px; }
    .td-status { display:inline-block; padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700; text-transform:uppercase; }
    .td-status--high { background:#FFF0F0; color:#EE5D50; }
    .td-status--progress { background:#E6F9F0; color:#05CD99; }
    .td-status--pending { background:#FFF4E5; color:#FF9B05; }
    .td-staff { display:flex; align-items:center; gap:8px; }
    .td-staff__avatar { width:28px; height:28px; border-radius:50%; background:#EEF2FF; color:#3652D9; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:700; flex-shrink:0; }
    .td-staff__name { font-size:12px; font-weight:600; color:#1B2559; }

    /* Progress Card */
    .progress-card { text-align:center; }
    .progress-card__header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .progress-card__pct { font-size:13px; font-weight:700; color:#05CD99; }
    .progress-ring { position:relative; width:140px; height:140px; margin:0 auto 16px; }
    .progress-ring svg { transform:rotate(-90deg); }
    .progress-ring__bg { fill:none; stroke:#F4F7FE; stroke-width:12; }
    .progress-ring__fill { fill:none; stroke:#05CD99; stroke-width:12; stroke-linecap:round; transition:stroke-dashoffset .6s ease; }
    .progress-ring__text { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:28px; font-weight:800; color:#1B2559; }
    .progress-ring__text small { font-size:12px; color:#A3AED0; font-weight:500; display:block; }
    .progress-stats { display:flex; justify-content:center; gap:20px; margin-top:12px; }
    .progress-stat { text-align:center; }
    .progress-stat__val { font-size:16px; font-weight:700; color:#1B2559; }
    .progress-stat__label { font-size:11px; color:#A3AED0; }

    /* Report list */
    .report-item { display:flex; gap:12px; padding:12px 0; border-bottom:1px solid #F4F7FE; }
    .report-item:last-child { border-bottom:none; }
    .report-item__icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; }
    .report-item__icon--urgent { background:#FFF0F0; color:#EE5D50; }
    .report-item__icon--normal { background:#FFF4E5; color:#FF9B05; }
    .report-item__body { flex:1; min-width:0; }
    .report-item__title { font-size:13px; font-weight:600; color:#1B2559; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .report-item__desc { font-size:11.5px; color:#A3AED0; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .report-item__time { font-size:10px; color:#A3AED0; flex-shrink:0; white-space:nowrap; }
    .report-item__action { font-size:11px; font-weight:600; color:#3652D9; margin-top:4px; cursor:pointer; }

    .empty-state { text-align:center; padding:24px; color:#A3AED0; font-size:13px; }

    @media(max-width:1024px) { .dash-grid { grid-template-columns:1fr; } .dash-side { flex-direction:row; flex-wrap:wrap; } .dash-side > * { flex:1; min-width:280px; } }
    @media(max-width:768px) { .kpi-row { grid-template-columns:1fr 1fr; } .greeting { flex-direction:column; } }
    @media(max-width:480px) { .kpi-row { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

{{-- Greeting --}}
<div class="greeting">
    <div>
        @php
            $hour = now()->hour;
            $greet = $hour < 12 ? 'Chào buổi sáng' : ($hour < 18 ? 'Chào buổi chiều' : 'Chào buổi tối');
        @endphp
        <h1>{{ $greet }}, {{ explode(' ', auth()->user()->name)[0] }}!</h1>
        <p>Hôm nay bạn có <strong>{{ $pending + $progress }}</strong> công việc cần xử lý.</p>
    </div>
    <a href="{{ route('cleaning.report') }}" class="btn-report">
        <i class="fa-solid fa-triangle-exclamation"></i> Báo cáo sự cố
    </a>
</div>

{{-- KPI --}}
<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--blue"><i class="fa-solid fa-list-check"></i></div>
        <div><div class="kpi-value">{{ $total }}</div><div class="kpi-label">Tổng công việc</div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--orange"><i class="fa-solid fa-spinner"></i></div>
        <div><div class="kpi-value">{{ $progress }}</div><div class="kpi-label">Đang thực hiện</div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--green"><i class="fa-solid fa-circle-check"></i></div>
        <div><div class="kpi-value">{{ $done }}</div><div class="kpi-label">Đã hoàn thành</div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon--red"><i class="fa-solid fa-clock"></i></div>
        <div><div class="kpi-value">{{ $pending }}</div><div class="kpi-label">Chờ xử lý</div></div>
    </div>
</div>

{{-- Main Grid --}}
<div class="dash-grid">
    {{-- Left: Priority Tasks --}}
    <div class="dash-main">
        <div class="dash-card">
            <div class="dash-card__header">
                <span class="dash-card__title">Công việc ưu tiên</span>
                <a href="{{ route('cleaning.tasks') }}" class="dash-card__link">Xem tất cả</a>
            </div>

            @if($activeTasks->count() > 0)
            <table class="priority-table">
                <thead>
                    <tr>
                        <th>Vị trí</th>
                        <th>Trạng thái</th>
                        <th>Thời gian</th>
                        <th>Ưu tiên</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeTasks->take(5) as $task)
                    <tr onclick="window.location='{{ route('cleaning.tasks.show', $task->id) }}'" style="cursor:pointer;">
                        <td>
                            <div class="td-location__name">{{ $task->title }}</div>
                            <div class="td-location__sub">{{ $task->area }}</div>
                        </td>
                        <td>
                            @if($task->status === 'progress')
                                <span class="td-status td-status--progress">Đang làm</span>
                            @else
                                <span class="td-status td-status--pending">Chờ</span>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#707EAE;white-space:nowrap;">
                            {{ \Carbon\Carbon::parse($task->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($task->end_time)->format('H:i') }}
                        </td>
                        <td>
                            <span class="td-status td-status--{{ $task->priority === 'high' ? 'high' : ($task->priority === 'medium' ? 'pending' : 'progress') }}">
                                {{ $task->priority === 'high' ? 'Gấp' : ($task->priority === 'medium' ? 'TB' : 'Thấp') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <i class="fa-solid fa-party-horn" style="font-size:24px;margin-bottom:8px;display:block;"></i>
                Không có công việc nào cần xử lý!
            </div>
            @endif
        </div>

        {{-- Done tasks --}}
        @if($doneTasks->count() > 0)
        <div class="dash-card" style="margin-top:16px;">
            <div class="dash-card__header">
                <span class="dash-card__title" style="color:#05CD99;">Đã hoàn thành hôm nay</span>
                <span style="font-size:12px;color:#A3AED0;">{{ $doneTasks->count() }} task</span>
            </div>
            <table class="priority-table">
                <tbody>
                    @foreach($doneTasks->take(3) as $task)
                    <tr style="opacity:.7;">
                        <td>
                            <div class="td-location__name" style="text-decoration:line-through;color:#A3AED0;">{{ $task->title }}</div>
                            <div class="td-location__sub">{{ $task->area }}</div>
                        </td>
                        <td><span class="td-status td-status--progress">Xong</span></td>
                        <td style="font-size:12px;color:#A3AED0;">{{ $task->completed_at?->format('H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Right: Progress + Reports --}}
    <div class="dash-side">
        {{-- Progress Ring --}}
        <div class="dash-card progress-card">
            <div class="dash-card__header">
                <span class="dash-card__title">Tiến độ trong ngày</span>
                @php $pct = $total > 0 ? round(($done / $total) * 100) : 0; @endphp
                <span class="progress-card__pct">{{ $pct }}% Đã xong</span>
            </div>
            
            @php
                $radius = 54;
                $circumference = 2 * 3.14159 * $radius;
                $offset = $circumference - ($pct / 100) * $circumference;
            @endphp
            <div class="progress-ring">
                <svg width="140" height="140">
                    <circle class="progress-ring__bg" cx="70" cy="70" r="{{ $radius }}"/>
                    <circle class="progress-ring__fill" cx="70" cy="70" r="{{ $radius }}" 
                        stroke-dasharray="{{ $circumference }}" 
                        stroke-dashoffset="{{ $offset }}"/>
                </svg>
                <div class="progress-ring__text">
                    {{ $pct }}%
                    <small>hoàn thành</small>
                </div>
            </div>

            <div class="progress-stats">
                <div class="progress-stat">
                    <div class="progress-stat__val" style="color:#05CD99;">{{ $done }}</div>
                    <div class="progress-stat__label">Xong</div>
                </div>
                <div class="progress-stat">
                    <div class="progress-stat__val" style="color:#FF9B05;">{{ $progress }}</div>
                    <div class="progress-stat__label">Đang làm</div>
                </div>
                <div class="progress-stat">
                    <div class="progress-stat__val" style="color:#EE5D50;">{{ $pending }}</div>
                    <div class="progress-stat__label">Chờ</div>
                </div>
            </div>
        </div>

        {{-- Recent Reports --}}
        <div class="dash-card">
            <div class="dash-card__header">
                <span class="dash-card__title">Báo cáo sự cố</span>
                <a href="{{ route('cleaning.report') }}" class="dash-card__link">Gửi mới</a>
            </div>

            @php
                $reports = \App\Models\CleaningReport::where('reported_by', auth()->id())
                    ->latest()
                    ->take(3)
                    ->get();
            @endphp

            @if($reports->count() > 0)
                @foreach($reports as $report)
                <div class="report-item">
                    <div class="report-item__icon {{ $report->priority === 'high' ? 'report-item__icon--urgent' : 'report-item__icon--normal' }}">
                        <i class="fa-solid {{ $report->priority === 'high' ? 'fa-circle-exclamation' : 'fa-wrench' }}"></i>
                    </div>
                    <div class="report-item__body">
                        <div class="report-item__title">{{ $report->title }}</div>
                        <div class="report-item__desc">{{ $report->location }}</div>
                    </div>
                    <span class="report-item__time">{{ $report->created_at->diffForHumans(null, true) }}</span>
                </div>
                @endforeach
            @else
                <div class="empty-state">Chưa có báo cáo nào</div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.priority-table tr[onclick]').forEach(row => {
    row.addEventListener('keydown', e => { if(e.key === 'Enter') row.click(); });
    row.setAttribute('tabindex', '0');
});
</script>
@endpush
