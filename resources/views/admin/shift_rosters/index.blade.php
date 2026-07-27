@extends('layouts.admin.master')

@section('title', 'Lịch Phân Ca - Bảng Matrix 7 Ngày')

@push('styles')
<style>
    .roster-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
    }
    .roster-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .filters {
        display: flex;
        gap: 12px;
        align-items: center;
        background: #f8fafc;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    .matrix-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }
    .matrix-table th {
        background: #0f172a;
        color: #fff;
        font-weight: 700;
        font-size: 13px;
        padding: 10px 8px;
        text-align: center;
        border: 1px solid #1e293b;
    }
    .matrix-table th.staff-col {
        width: 200px;
        text-align: left;
        padding-left: 14px;
    }
    .matrix-table td {
        border: 1px solid #cbd5e1;
        height: 70px;
        vertical-align: top;
        padding: 6px;
        position: relative;
        background: #fff;
        transition: background 0.15s;
    }
    .matrix-table td:hover {
        background: #f1f5f9;
        cursor: pointer;
    }
    .staff-info {
        display: flex;
        flex-direction: column;
    }
    .staff-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 13px;
    }
    .staff-dept {
        font-size: 11px;
        color: #64748b;
    }
    .shift-badge {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
        text-align: center;
    }
    .block-badge {
        background: #fef3c7;
        border: 1px solid #fde68a;
        color: #b45309;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        display: inline-block;
    }
    .add-shift-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #94a3b8;
        font-size: 18px;
        font-weight: bold;
    }
    .add-shift-btn:hover {
        color: #2563eb;
    }
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
        max-width: 450px;
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

    <div class="roster-card">
        <div class="roster-header">
            <div>
                <p style="margin:0 0 4px; font-size:13px; font-weight:700; color:#2563eb; text-transform:uppercase; letter-spacing:.05em;">Nhân sự</p>
                <h1 style="margin:0; font-size:24px; font-weight:800; color:#0f172a;">Lịch Phân Ca - Bảng Matrix</h1>
            </div>
            <form action="{{ portal_route('shift-rosters.copy-week') }}" method="POST" style="display:inline;" onsubmit="return confirm('Hệ thống sẽ sao chép toàn bộ lịch tuần trước sang tuần này (nếu nhân viên chưa có lịch). Tiếp tục?');">
                @csrf
                <input type="hidden" name="target_week" value="{{ $startOfWeek->toDateString() }}">
                <button type="submit" class="btn-action btn-secondary" style="background:#fff; border:1px solid #cbd5e1; color:#0f172a;">
                    📋 Sao Chép Lịch Tuần Trước
                </button>
            </form>
        </div>

        {{-- Filters --}}
        <div class="filters">
            <form action="{{ portal_route('shift-rosters.index') }}" method="GET" style="display:flex; gap:12px; width:100%; align-items:center;">
                <div class="form-group" style="margin:0;">
                    <label style="margin-bottom:4px; font-size:11px;">Chọn Tuần</label>
                    <input type="date" name="week" value="{{ $startOfWeek->toDateString() }}" class="form-control" style="padding:6px 10px; height:36px;" onchange="this.form.submit()">
                </div>

                <div class="form-group" style="margin:0;">
                    <label style="margin-bottom:4px; font-size:11px;">Phòng Ban</label>
                    <select name="department_id" class="form-control" style="padding:6px 10px; height:36px;" onchange="this.form.submit()">
                        <option value="">-- Tất cả phòng ban --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $departmentId == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin:0;">
                    <label style="margin-bottom:4px; font-size:11px;">Khu Vực / Block</label>
                    <select name="block_id" class="form-control" style="padding:6px 10px; height:36px;" onchange="this.form.submit()">
                        <option value="">-- Tất cả khu vực --</option>
                        @foreach($blocks as $b)
                            <option value="{{ $b->id }}" {{ $blockId == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin:0; flex:1;">
                    <label style="margin-bottom:4px; font-size:11px;">Tìm Tên Nhân Viên</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Nhập tên..." class="form-control" style="padding:6px 10px; height:36px;">
                </div>

                <button type="submit" class="btn-action" style="background:#2563eb; color:#fff; height:36px; margin-top:16px;">Lọc</button>
            </form>
        </div>

        {{-- Matrix Table --}}
        <div style="overflow-x:auto;">
            <table class="matrix-table">
                <thead>
                    <tr>
                        <th class="staff-col">Nhân Viên</th>
                        @foreach($weekDays as $day)
                            <th style="{{ $day->isToday() ? 'background:#2563eb;' : '' }}">
                                {{ $day->translatedFormat('D') }}<br>
                                <span style="font-size:11px; font-weight:normal;">{{ $day->format('d/m') }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffs as $staff)
                        <tr>
                            <td class="staff-col" style="background:#f8fafc; border:1px solid #cbd5e1;">
                                <div class="staff-info">
                                    <span class="staff-name">{{ $staff->full_name }}</span>
                                    <span class="staff-dept">{{ $staff->department->name ?? 'Chưa gắn PB' }}</span>
                                </div>
                            </td>

                            @foreach($weekDays as $day)
                                @php
                                    $dateStr = $day->toDateString();
                                    $dayRosters = $rosterMap[$staff->id][$dateStr] ?? [];
                                    $roster = $dayRosters[0] ?? null;
                                @endphp
                                <td onclick="openRosterModal('{{ $staff->id }}', '{{ $staff->full_name }}', '{{ $dateStr }}', {{ json_encode($roster) }})">
                                    @if($roster)
                                        <span class="shift-badge">
                                            {{ $roster->shift->name }}
                                        </span>
                                        @if($roster->block)
                                            <span class="block-badge">📍 {{ $roster->block->name }}</span>
                                        @endif
                                        @if($roster->status === 'cancelled')
                                            <span style="font-size:10px; color:#dc2626; display:block; text-align:center;">(Đã hủy)</span>
                                        @endif
                                    @else
                                        <div class="add-shift-btn">+</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; color:#94a3b8; padding:30px;">
                                Không có nhân viên nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

{{-- Modal Gán/Sửa Ca --}}
<div id="rosterModal" class="modal-overlay">
    <div class="modal-content">
        <form id="rosterForm" method="POST" action="{{ portal_route('shift-rosters.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="staff_id" id="rosterStaffId">
            <input type="hidden" name="date" id="rosterDate">
            
            <h3 style="margin:0 0 4px; font-size:18px; font-weight:800; color:#0f172a;">Xếp Lịch Phân Ca</h3>
            <p id="modalSubTitle" style="margin:0 0 16px; font-size:13px; color:#64748b;"></p>
            
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px;">Chọn Ca Làm Việc (*)</label>
                <select name="shift_id" id="rosterShiftId" class="form-control" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                    <option value="">-- Chọn ca --</option>
                    @foreach($shifts as $shift)
                        <option value="{{ $shift->id }}">{{ $shift->name }} ({{ substr($shift->start_time, 0, 5) }} - {{ substr($shift->end_time, 0, 5) }})</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px;">Khu Vực / Block Phân Công</label>
                <select name="block_id" id="rosterBlockId" class="form-control" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                    <option value="">-- Không cố định Block --</option>
                    @foreach($blocks as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block; font-size:13px; font-weight:700; color:#334155; margin-bottom:6px;">Ghi chú trực</label>
                <textarea name="notes" id="rosterNotes" rows="2" class="form-control" placeholder="Ghi chú nhiệm vụ cụ thể..." style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;"></textarea>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div id="deleteContainer"></div>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="btn-action" style="background:#f1f5f9; color:#475569;" onclick="closeRosterModal()">Hủy</button>
                    <button type="submit" class="btn-action" style="background:#2563eb; color:#fff;">Lưu Phân Ca</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openRosterModal(staffId, staffName, dateStr, rosterObj = null) {
        const form = document.getElementById('rosterForm');
        const methodInput = document.getElementById('formMethod');
        const deleteContainer = document.getElementById('deleteContainer');

        document.getElementById('rosterStaffId').value = staffId;
        document.getElementById('rosterDate').value = dateStr;
        document.getElementById('modalSubTitle').textContent = `${staffName} - Ngày: ${dateStr}`;

        if (rosterObj) {
            // Sửa
            form.action = "{{ portal_route('shift-rosters.index') }}/" + rosterObj.id;
            methodInput.value = 'PUT';
            document.getElementById('rosterShiftId').value = rosterObj.shift_id;
            document.getElementById('rosterBlockId').value = rosterObj.block_id || '';
            document.getElementById('rosterNotes').value = rosterObj.notes || '';
            
            // Render delete button with form
            deleteContainer.innerHTML = `
                <button type="button" class="btn-action" style="background:#fef2f2; color:#dc2626; border:1px solid #fecaca;" 
                        onclick="if(confirm('Bạn có chắc muốn gỡ ca làm việc này?')) { document.getElementById('deleteForm_${rosterObj.id}').submit(); }">
                    Gỡ ca làm việc
                </button>
            `;
            
            // Add hidden delete form to body (temporary)
            const oldForm = document.getElementById(`deleteForm_${rosterObj.id}`);
            if (oldForm) oldForm.remove();
            
            const delForm = document.createElement('form');
            delForm.id = `deleteForm_${rosterObj.id}`;
            delForm.method = 'POST';
            delForm.action = "{{ portal_route('shift-rosters.index') }}/" + rosterObj.id;
            delForm.style.display = 'none';
            delForm.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(delForm);
            
        } else {
            // Thêm mới
            form.action = "{{ portal_route('shift-rosters.store') }}";
            methodInput.value = 'POST';
            document.getElementById('rosterShiftId').value = '';
            document.getElementById('rosterBlockId').value = '';
            document.getElementById('rosterNotes').value = '';
            deleteContainer.innerHTML = '';
        }
        
        document.getElementById('rosterModal').style.display = 'flex';
    }

    function closeRosterModal() {
        document.getElementById('rosterModal').style.display = 'none';
    }
</script>
@endpush
@endsection
