@extends('layouts.admin.master')

@section('title', 'Quản Lý Ca Làm Việc')

@push('styles')
<style>
    .shift-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
    }
    .shift-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .shift-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .shift-table th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 13px;
        padding: 12px 16px;
        border-bottom: 2px solid #e2e8f0;
        text-align: left;
    }
    .shift-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        font-size: 14px;
        vertical-align: middle;
    }
    .badge-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-active { background: #dcfce7; color: #15803d; }
    .badge-inactive { background: #f1f5f9; color: #64748b; }
    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
    }
    .btn-primary-custom { background: #2563eb; color: #fff; }
    .btn-primary-custom:hover { background: #1d4ed8; }
    .btn-edit { background: #e0f2fe; color: #0369a1; }
    .btn-edit:hover { background: #bae6fd; }
    .btn-delete { background: #fef2f2; color: #dc2626; }
    .btn-delete:hover { background: #fecaca; }
    
    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .modal-content {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 500px;
        padding: 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid padding-horizontal-lg padding-vertical-md">

    @if(session('success'))
        <div style="background:#dcfce7; color:#15803d; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-weight:600;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#fef2f2; color:#dc2626; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-weight:600;">
            {{ session('error') }}
        </div>
    @endif

    <div class="shift-card">
        <div class="shift-header">
            <div>
                <h1 style="margin:0; font-size:22px; font-weight:800; color:#0f172a;">Danh Mục Ca Làm Việc</h1>
                <p style="margin:4px 0 0; color:#64748b; font-size:14px;">Quản lý các giờ ca trực cố định, thời gian gia hạn và hệ số lương.</p>
            </div>
            <button class="btn-action btn-primary-custom" onclick="openModal('add')">
                + Thêm Ca Mới
            </button>
        </div>

        <table class="shift-table">
            <thead>
                <tr>
                    <th>Tên Ca</th>
                    <th>Giờ Bắt Đầu</th>
                    <th>Giờ Kết Thúc</th>
                    <th>Đi Trễ Cho Phép</th>
                    <th>Hệ Số Lương</th>
                    <th>Trạng Thái</th>
                    <th style="text-align:right;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($shifts as $shift)
                    <tr>
                        <td style="font-weight:700;">{{ $shift->name }}</td>
                        <td><span style="background:#f1f5f9; padding:4px 8px; border-radius:6px; font-family:monospace; font-weight:700;">{{ substr($shift->start_time, 0, 5) }}</span></td>
                        <td><span style="background:#f1f5f9; padding:4px 8px; border-radius:6px; font-family:monospace; font-weight:700;">{{ substr($shift->end_time, 0, 5) }}</span></td>
                        <td>{{ $shift->grace_period_minutes }} phút</td>
                        <td>x{{ number_format($shift->shift_rate, 2) }}</td>
                        <td>
                            <form action="{{ portal_route('shifts.toggle-status', $shift) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:none; border:none; padding:0; cursor:pointer;" title="Nhấn để đổi trạng thái">
                                    @if($shift->status === 'active')
                                        <span class="badge-status badge-active">Hoạt động</span>
                                    @else
                                        <span class="badge-status badge-inactive">Ngưng hoạt động</span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td style="text-align:right;">
                            <button class="btn-action btn-edit" onclick="openModal('edit', {{ json_encode($shift) }})">Sửa</button>
                            <form action="{{ portal_route('shifts.destroy', $shift) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xóa ca làm việc này không?');">
                                @csrf @method('DELETE')
                                <button class="btn-action btn-delete">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color:#94a3b8; padding:30px;">
                            Chưa có ca làm việc nào. Hãy nhấn "+ Thêm Ca Mới" để tạo!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Thêm/Sửa Ca --}}
<div id="shiftModal" class="modal-overlay">
    <div class="modal-content">
        <form id="shiftForm" method="POST" action="{{ portal_route('shifts.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            
            <h3 id="modalTitle" style="margin:0 0 20px; font-size:18px; font-weight:800; color:#0f172a;">Thêm Ca Làm Việc</h3>
            
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px;">Tên Ca trực (*)</label>
                <input type="text" name="name" id="shiftName" class="form-control" placeholder="VD: Ca Sáng, Ca Chiều, Ca Đêm..." required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
            </div>

            <div style="display:flex; gap:12px; margin-bottom:16px;">
                <div style="flex:1;">
                    <label style="display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px;">Giờ Bắt Đầu (*)</label>
                    <input type="time" name="start_time" id="startTime" class="form-control" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                </div>
                <div style="flex:1;">
                    <label style="display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px;">Giờ Kết Thúc (*)</label>
                    <input type="time" name="end_time" id="endTime" class="form-control" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                </div>
            </div>

            <div style="display:flex; gap:12px; margin-bottom:16px;">
                <div style="flex:1;">
                    <label style="display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px;">Cho Phép Đi Trễ (phút)</label>
                    <input type="number" name="grace_period_minutes" id="gracePeriod" class="form-control" value="15" min="0" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                </div>
                <div style="flex:1;">
                    <label style="display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px;">Hệ Số Lương (Ca)</label>
                    <input type="number" step="0.1" name="shift_rate" id="shiftRate" class="form-control" value="1.0" min="0.5" max="3.0" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px;">Mô tả / Ghi chú</label>
                <textarea name="description" id="shiftDesc" rows="2" class="form-control" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px;">
                <button type="button" class="btn-action" style="background:#f1f5f9; color:#475569;" onclick="closeModal()">Hủy</button>
                <button type="submit" class="btn-action btn-primary-custom">Lưu Ca Làm Việc</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal(type, shift = null) {
        const form = document.getElementById('shiftForm');
        const modalTitle = document.getElementById('modalTitle');
        const methodInput = document.getElementById('formMethod');

        if (type === 'add') {
            modalTitle.textContent = 'Thêm Ca Làm Việc';
            form.action = "{{ portal_route('shifts.store') }}";
            methodInput.value = 'POST';
            
            document.getElementById('shiftName').value = '';
            document.getElementById('startTime').value = '';
            document.getElementById('endTime').value = '';
            document.getElementById('gracePeriod').value = '15';
            document.getElementById('shiftRate').value = '1.0';
            document.getElementById('shiftDesc').value = '';
        } else {
            modalTitle.textContent = 'Cập Nhật Ca Làm Việc';
            form.action = "{{ portal_route('shifts.index') }}/" + shift.id;
            methodInput.value = 'PUT';
            
            document.getElementById('shiftName').value = shift.name;
            document.getElementById('startTime').value = shift.start_time.substring(0, 5);
            document.getElementById('endTime').value = shift.end_time.substring(0, 5);
            document.getElementById('gracePeriod').value = shift.grace_period_minutes;
            document.getElementById('shiftRate').value = shift.shift_rate;
            document.getElementById('shiftDesc').value = shift.description || '';
        }

        document.getElementById('shiftModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('shiftModal').style.display = 'none';
    }
</script>
@endpush
@endsection
