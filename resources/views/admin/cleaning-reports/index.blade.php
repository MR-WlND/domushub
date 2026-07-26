@extends('layouts.admin.master')

@section('page_title', 'Báo cáo sự cố vệ sinh – DomusHub')

@push('styles')
<style>
.cr-page{max-width:1100px;}
.cr-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
.cr-header h1{font-size:24px;font-weight:800;color:#0f172a;}
.cr-header p{font-size:13px;color:#64748b;margin-top:2px;}
.cr-filters{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.cr-filters select{height:40px;border:1.5px solid #e2e8f0;border-radius:8px;padding:0 12px;font-size:13px;font-family:inherit;color:#334155;background:white;}
.cr-filters select:focus{outline:none;border-color:#082B7A;}
.cr-table{width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.04);}
.cr-table th{text-align:left;padding:12px 16px;font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;background:#f8fafc;border-bottom:1px solid #e2e8f0;}
.cr-table td{padding:14px 16px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#334155;vertical-align:middle;}
.cr-table tr:hover td{background:#f8fafc;}
.cr-title{font-weight:700;color:#0f172a;}
.cr-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:6px;font-size:10.5px;font-weight:700;}
.cr-badge--high{background:#fee2e2;color:#dc2626;}
.cr-badge--medium{background:#fef3c7;color:#d97706;}
.cr-badge--low{background:#d1fae5;color:#059669;}
.cr-badge--pending{background:#fef3c7;color:#d97706;}
.cr-badge--processing{background:#dbeafe;color:#2563eb;}
.cr-badge--resolved{background:#d1fae5;color:#059669;}
.cr-status-form select{height:32px;border:1px solid #e2e8f0;border-radius:6px;padding:0 8px;font-size:11.5px;font-family:inherit;}
.cr-imgs{display:flex;gap:4px;}
.cr-imgs img{width:36px;height:36px;border-radius:6px;object-fit:cover;border:1px solid #e2e8f0;}
.alert-box{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;}
</style>
@endpush

@section('content')
<div class="cr-page">
    <div class="cr-header">
        <div>
            <h1>Báo cáo sự cố vệ sinh</h1>
            <p>Danh sách sự cố do nhân viên vệ sinh báo cáo</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert-box"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <form class="cr-filters" method="GET" action="{{ portal_route('cleaning-reports.index') }}">
        <select name="status" onchange="this.form.submit()">
            <option value="">Tất cả trạng thái</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Đã xử lý</option>
        </select>
        <select name="priority" onchange="this.form.submit()">
            <option value="">Tất cả ưu tiên</option>
            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Cao</option>
            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Trung bình</option>
            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
        </select>
    </form>

    <table class="cr-table">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Người báo</th>
                <th>Khu vực</th>
                <th>Ưu tiên</th>
                <th>Ảnh</th>
                <th>Trạng thái</th>
                <th>Ngày</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
            <tr>
                <td><span class="cr-title">{{ $report->title }}</span></td>
                <td>{{ $report->reporter->name ?? '—' }}</td>
                <td>{{ $report->location }}</td>
                <td><span class="cr-badge cr-badge--{{ $report->priority }}">{{ $report->priority === 'high' ? 'Cao' : ($report->priority === 'medium' ? 'TB' : 'Thấp') }}</span></td>
                <td>
                    <div class="cr-imgs">
                        @foreach(($report->images ?? []) as $img)
                        <img src="{{ asset('storage/' . $img) }}" alt="evidence">
                        @endforeach
                        @if(empty($report->images)) — @endif
                    </div>
                </td>
                <td>
                    <form method="POST" action="{{ portal_route('cleaning-reports.update-status', $report->id) }}" class="cr-status-form">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()">
                            <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="processing" {{ $report->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Đã xử lý</option>
                        </select>
                    </form>
                </td>
                <td style="font-size:12px;color:#94a3b8;">{{ $report->created_at->format('H:i d/m') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:32px;">Chưa có báo cáo sự cố nào.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $reports->links() }}</div>
</div>
@endsection
