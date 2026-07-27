@extends('layouts.admin.master')

@section('page_title', 'Phân ca làm việc')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
    .schedule-grid { width: 100%; border-collapse: collapse; text-align: left; table-layout: fixed; }
    .schedule-grid th { background: #f8f9fa; padding: 12px; border: 1px solid #e2e8f0; font-size: 13px; color: #475569; text-align: center; }
    .schedule-grid td { border: 1px solid #e2e8f0; vertical-align: top; padding: 8px; }
    .schedule-cell { display: flex; flex-direction: column; gap: 12px; height: 100%; }
    
    .dept-group { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
    .dept-header { background: #f1f5f9; padding: 6px 10px; font-size: 12px; font-weight: 700; color: #334155; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e2e8f0; }
    .dept-header span.count { font-size: 11px; font-weight: 500; color: #64748b; background: #e2e8f0; padding: 2px 6px; border-radius: 12px; }
    .dept-header span.warning { font-size: 11px; font-weight: 600; color: #b91c1c; background: #fee2e2; padding: 2px 6px; border-radius: 12px; }
    
    .schedule-list { padding: 6px; display: flex; flex-direction: column; gap: 4px; }
    .schedule-item { display: flex; justify-content: space-between; align-items: center; background: #eff6ff; color: #1e3a8a; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; border: 1px solid #bfdbfe; transition: all 0.2s; }
    .schedule-item.leader { background: #fffbeb; color: #92400e; border-color: #fde68a; }
    
    .schedule-actions { display: flex; gap: 4px; }
    .action-btn { cursor: pointer; background: none; border: none; padding: 2px 4px; display: inline-flex; align-items: center; justify-content: center; border-radius: 4px; opacity: 0.7; }
    .action-btn:hover { opacity: 1; background: rgba(0,0,0,0.05); }
    .action-btn.del { color: #ef4444; }
    .action-btn.del:hover { background: #fee2e2; }
    .action-btn.star { color: #fbbf24; }
    .action-btn.star:hover { background: #fef3c7; }
    
    .schedule-add { margin: 4px 6px 6px 6px; display: flex; justify-content: center; align-items: center; background: none; border: 1px dashed #cbd5e1; color: #64748b; padding: 4px; border-radius: 4px; cursor: pointer; font-size: 11px; font-weight: 600; transition: all 0.2s; }
    .schedule-add:hover { background: #f8fafc; border-color: #94a3b8; color: #0f172a; }

    .ts-control { border-radius: 8px; border-color: #cbd5e1; padding: 8px 12px; }
</style>
@endpush

@section('content')
<div class="dashboard-card" style="padding:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
        <h1 style="font-size:20px; font-weight:700; color:#0b1c30; margin:0;">Bảng phân ca làm việc</h1>
        
        <div style="display:flex; gap:12px; align-items:center;">
            <button onclick="openCopyModal()" class="btn-new-broadcast" style="width:auto; margin:0; background:#fff; color:#0b57d0; border:1px solid #0b57d0;"><i class="fa-regular fa-copy"></i> Sao chép lịch</button>
            <a href="{{ route('admin.schedules.index', ['date' => $startOfWeek->copy()->subWeek()->format('Y-m-d')]) }}" class="btn-new-broadcast" style="width:auto; margin:0; background:#f1f5f9; color:#475569;"><i class="fa-solid fa-chevron-left"></i> Tuần trước</a>
            <span style="font-weight:600; font-size:14px; color:#0b1c30; padding:0 12px;">Tuần {{ $startOfWeek->format('d/m') }} - {{ $endOfWeek->format('d/m/Y') }}</span>
            <a href="{{ route('admin.schedules.index', ['date' => $startOfWeek->copy()->addWeek()->format('Y-m-d')]) }}" class="btn-new-broadcast" style="width:auto; margin:0; background:#f1f5f9; color:#475569;">Tuần sau <i class="fa-solid fa-chevron-right"></i></a>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="schedule-grid">
            <thead>
                <tr>
                    <th style="width: 120px;">Ca trực</th>
                    @foreach($days as $day)
                        <th style="width: 250px;">
                            <div style="font-weight:700; font-size:14px;">{{ ['Chủ nhật','Thứ hai','Thứ ba','Thứ tư','Thứ năm','Thứ sáu','Thứ bảy'][$day->dayOfWeek] }}</div>
                            <div style="font-weight:400; font-size:12px; color:#64748b;">{{ $day->format('d/m/Y') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($shifts as $shift)
                <tr>
                    <td style="background:#f8f9fa; font-weight:700; text-align:center; vertical-align:middle; color:#0b1c30;">
                        @php
                            $shiftReqs = $shift->requirements->pluck('required_staff', 'department_id')->toJson();
                        @endphp
                        <div style="display:flex; justify-content:center; align-items:center; gap:8px;">
                            {{ $shift->name }} 
                            <button onclick="openReqModal({{ $shift->id }}, '{{ $shift->name }}', {{ $shiftReqs }})" style="background:none; border:none; color:#64748b; cursor:pointer; font-size:14px;" title="Cài đặt định mức nhân sự"><i class="fa-solid fa-gear"></i></button>
                        </div>
                        <div style="font-size:11px; color:#64748b; font-weight:400; margin-top:4px;">{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</div>
                    </td>
                    
                    @foreach($days as $day)
                        @php
                            $dayStr = $day->format('Y-m-d');
                            $cellSchedules = $scheduleMatrix[$dayStr][$shift->id] ?? [];
                            // Group schedules by department
                            $grouped = [];
                            foreach ($cellSchedules as $sc) {
                                $deptId = $sc->staff->department_id;
                                if (!isset($grouped[$deptId])) $grouped[$deptId] = [];
                                $grouped[$deptId][] = $sc;
                            }
                        @endphp
                        <td>
                            <div class="schedule-cell" id="cell-{{ $dayStr }}-{{ $shift->id }}">
                                @foreach($departments as $dept)
                                    @php
                                        // Get requirements
                                        $req = $shift->requirements->where('department_id', $dept->id)->first();
                                        $reqStaff = $req ? $req->required_staff : 0;
                                        $assigned = isset($grouped[$dept->id]) ? count($grouped[$dept->id]) : 0;
                                        
                                        $icon = '🏢';
                                        if($dept->code == 'SEC') $icon = '👮';
                                        if($dept->code == 'TECH') $icon = '🛠';
                                        if($dept->code == 'CLEAN') $icon = '🧹';
                                        if($dept->code == 'REC') $icon = '💁';
                                    @endphp
                                    
                                    @if($reqStaff > 0 || $assigned > 0)
                                    <div class="dept-group">
                                        <div class="dept-header">
                                            <span>{{ $icon }} {{ $dept->name }}</span>
                                            @if($assigned < $reqStaff)
                                                <span class="warning" title="Yêu cầu {{ $reqStaff }} người">⚠ Thiếu {{ $reqStaff - $assigned }}</span>
                                            @else
                                                <span class="count">{{ $assigned }}/{{ $reqStaff }}</span>
                                            @endif
                                        </div>
                                        <div class="schedule-list" id="list-{{ $dayStr }}-{{ $shift->id }}-{{ $dept->id }}">
                                            @if(isset($grouped[$dept->id]))
                                                @foreach($grouped[$dept->id] as $sc)
                                                    <div class="schedule-item {{ $sc->is_leader ? 'leader' : '' }}" id="schedule-item-{{ $sc->id }}">
                                                        <span title="{{ $sc->staff->full_name }}">
                                                            @if($sc->is_leader) ⭐ @endif
                                                            {{ mb_strimwidth($sc->staff->full_name, 0, 18, '...') }}
                                                        </span>
                                                        <div class="schedule-actions">
                                                            <button class="action-btn star" onclick="toggleLeader({{ $sc->id }})" title="Đánh dấu trưởng ca"><i class="fa-solid fa-star"></i></button>
                                                            <button class="action-btn del" onclick="deleteSchedule({{ $sc->id }})" title="Xóa"><i class="fa-solid fa-xmark"></i></button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <button class="schedule-add" onclick="openAddModal('{{ $dayStr }}', {{ $shift->id }}, '{{ $shift->name }}', '{{ $day->format('d/m/Y') }}', {{ $dept->id }}, '{{ $dept->name }}')">
                                            <i class="fa-solid fa-plus" style="margin-right:4px;"></i> Thêm nhân viên
                                        </button>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add Schedule Modal -->
<div id="addModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:100%; max-width:400px; border-radius:12px; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
        <h2 style="font-size:18px; font-weight:700; color:#0b1c30; margin-bottom:4px;">Phân ca làm việc</h2>
        <p id="modalSubtitle" style="font-size:13px; color:#64748b; margin-bottom:20px;"></p>
        
        <form id="addForm" onsubmit="submitAddForm(event)">
            @csrf
            <input type="hidden" name="work_date" id="inputWorkDate">
            <input type="hidden" name="shift_id" id="inputShiftId">
            <input type="hidden" name="department_id" id="inputDeptId">
            
            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px;">Chọn nhân sự <span style="color:#ba1a1a;">*</span></label>
                <select name="staff_id" id="inputStaffId" required placeholder="Gõ tên để tìm kiếm..."></select>
            </div>

            <div id="modalError" style="display:none; color:#ba1a1a; font-size:13px; margin-bottom:16px; padding:10px; background:#fef2f2; border-radius:6px; border:1px solid #fee2e2;"></div>

            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" onclick="closeAddModal()" style="padding:10px 20px; background:#f1f5f9; color:#475569; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Hủy</button>
                <button type="submit" id="btnSubmitAdd" class="btn-new-broadcast" style="width:auto; margin:0;">Gán ca</button>
            </div>
        </form>
    </div>
</div>

<!-- Copy Schedule Modal -->
<div id="copyModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:100%; max-width:400px; border-radius:12px; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
        <h2 style="font-size:18px; font-weight:700; color:#0b1c30; margin-bottom:4px;">Sao chép lịch</h2>
        <p style="font-size:13px; color:#64748b; margin-bottom:20px;">Hệ thống sẽ nhân bản lịch của một ngày sang một ngày khác.</p>
        
        <form id="copyForm" onsubmit="submitCopyForm(event)">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px;">Copy TỪ NGÀY (Nguồn)</label>
                <input type="date" id="copyFrom" required class="form-control" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px;">
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px;">ĐẾN NGÀY (Đích)</label>
                <input type="date" id="copyTo" required class="form-control" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:8px;">
            </div>

            <div id="copyError" style="display:none; color:#ba1a1a; font-size:13px; margin-bottom:16px; padding:10px; background:#fef2f2; border-radius:6px;"></div>
            <div id="copySuccess" style="display:none; color:#166534; font-size:13px; margin-bottom:16px; padding:10px; background:#f0fdf4; border-radius:6px;"></div>

            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" onclick="closeCopyModal()" style="padding:10px 20px; background:#f1f5f9; color:#475569; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Hủy</button>
                <button type="submit" id="btnSubmitCopy" class="btn-new-broadcast" style="width:auto; margin:0;">Sao chép ngay</button>
            </div>
        </form>
    </div>
</div>

<!-- Requiremnt Settings Modal -->
<div id="reqModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:100%; max-width:400px; border-radius:12px; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
        <h2 style="font-size:18px; font-weight:700; color:#0b1c30; margin-bottom:4px;">Định mức nhân sự</h2>
        <p id="reqModalSubtitle" style="font-size:13px; color:#64748b; margin-bottom:20px;"></p>
        
        <form id="reqForm" onsubmit="submitReqForm(event)">
            @csrf
            <input type="hidden" id="reqShiftId">
            
            <div style="max-height: 300px; overflow-y: auto; margin-bottom: 24px; padding-right:8px;">
                @foreach($departments as $dept)
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #f1f5f9;">
                        <label style="font-size:13px; font-weight:600; color:#334155;">{{ $dept->name }}</label>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <input type="number" id="req-dept-{{ $dept->id }}" class="req-dept-input" data-dept-id="{{ $dept->id }}" min="0" value="0" style="width:60px; padding:6px; border:1px solid #cbd5e1; border-radius:6px; text-align:center;">
                            <span style="font-size:12px; color:#64748b;">người</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="reqError" style="display:none; color:#ba1a1a; font-size:13px; margin-bottom:16px; padding:10px; background:#fef2f2; border-radius:6px;"></div>

            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" onclick="closeReqModal()" style="padding:10px 20px; background:#f1f5f9; color:#475569; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Hủy</button>
                <button type="submit" id="btnSubmitReq" class="btn-new-broadcast" style="width:auto; margin:0;">Lưu định mức</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    const token = document.querySelector('input[name="_token"]').value;
    let tomSelectInstance = null;

    // Khởi tạo Tom Select
    document.addEventListener("DOMContentLoaded", function() {
        tomSelectInstance = new TomSelect("#inputStaffId",{
            valueField: 'id',
            labelField: 'text',
            searchField: 'text',
            placeholder: 'Gõ tên nhân viên...',
            load: function(query, callback) {
                const deptId = document.getElementById('inputDeptId').value;
                if(!deptId) return callback();
                
                fetch(`{{ route('admin.api.staffs') }}?department_id=${deptId}`)
                    .then(response => response.json())
                    .then(json => {
                        callback(json);
                    }).catch(()=>{
                        callback();
                    });
            },
            render: {
                option: function(item, escape) {
                    return `<div><span class="name">${escape(item.text)}</span></div>`;
                },
                item: function(item, escape) {
                    return `<div><span class="name">${escape(item.text)}</span></div>`;
                }
            }
        });
    });

    function openAddModal(dateStr, shiftId, shiftName, dateFormatted, deptId, deptName) {
        document.getElementById('inputWorkDate').value = dateStr;
        document.getElementById('inputShiftId').value = shiftId;
        document.getElementById('inputDeptId').value = deptId;
        document.getElementById('modalSubtitle').innerText = `${shiftName} - ${dateFormatted} | Bộ phận: ${deptName}`;
        document.getElementById('modalError').style.display = 'none';
        
        // Load lại staffs cho TomSelect
        tomSelectInstance.clear();
        tomSelectInstance.clearOptions();
        tomSelectInstance.load("");

        document.getElementById('addModal').style.display = 'flex';
    }

    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }

    async function submitAddForm(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitAdd');
        const err = document.getElementById('modalError');
        btn.disabled = true;
        btn.innerText = 'Đang lưu...';
        err.style.display = 'none';

        const data = {
            work_date: document.getElementById('inputWorkDate').value,
            shift_id: document.getElementById('inputShiftId').value,
            staff_id: document.getElementById('inputStaffId').value,
        };

        try {
            const res = await fetch("{{ route('admin.staff-schedules.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            });
            const json = await res.json();
            
            if (json.success) {
                // Thành công -> Reload trang để đơn giản hóa logic đếm sĩ số và render
                // Nếu muốn pure AJAX DOM manipulation thì phức tạp hơn do phải tính lại sĩ số.
                window.location.reload();
            } else {
                err.innerText = json.message || 'Có lỗi xảy ra.';
                err.style.display = 'block';
                btn.disabled = false;
                btn.innerText = 'Gán ca';
            }
        } catch (error) {
            err.innerText = 'Lỗi kết nối máy chủ.';
            err.style.display = 'block';
            btn.disabled = false;
            btn.innerText = 'Gán ca';
        }
    }

    async function deleteSchedule(id) {
        if(!confirm('Bạn có chắc muốn gỡ nhân sự khỏi ca này?')) return;
        
        try {
            const res = await fetch(`{{ url('admin/staff-schedules') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            });
            const json = await res.json();
            if(json.success) {
                // Tải lại để update số lượng
                window.location.reload();
            } else {
                alert(json.message || 'Không thể xóa');
            }
        } catch(e) {
            alert('Lỗi kết nối');
        }
    }

    async function toggleLeader(id) {
        try {
            const res = await fetch(`{{ url('admin/staff-schedules') }}/${id}/leader`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            });
            const json = await res.json();
            if(json.success) {
                window.location.reload();
            } else {
                alert(json.message || 'Lỗi');
            }
        } catch(e) {
            alert('Lỗi kết nối');
        }
    }

    // Sao chép lịch
    function openCopyModal() {
        document.getElementById('copyError').style.display = 'none';
        document.getElementById('copySuccess').style.display = 'none';
        document.getElementById('copyModal').style.display = 'flex';
    }

    function closeCopyModal() {
        document.getElementById('copyModal').style.display = 'none';
    }

    async function submitCopyForm(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitCopy');
        const err = document.getElementById('copyError');
        const suc = document.getElementById('copySuccess');
        
        btn.disabled = true;
        btn.innerText = 'Đang copy...';
        err.style.display = 'none';
        suc.style.display = 'none';

        const data = {
            from_date: document.getElementById('copyFrom').value,
            to_date: document.getElementById('copyTo').value,
        };

        try {
            const res = await fetch("{{ route('admin.staff-schedules.copy') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(data)
            });
            const json = await res.json();
            
            if (json.success) {
                suc.innerText = json.message;
                suc.style.display = 'block';
                setTimeout(() => window.location.reload(), 1500);
            } else {
                err.innerText = json.message || 'Có lỗi xảy ra.';
                err.style.display = 'block';
                btn.disabled = false;
                btn.innerText = 'Sao chép ngay';
            }
        } catch (error) {
            err.innerText = 'Lỗi kết nối máy chủ.';
            err.style.display = 'block';
            btn.disabled = false;
            btn.innerText = 'Sao chép ngay';
        }
    }

    // Modal Định mức
    function openReqModal(shiftId, shiftName, reqs) {
        document.getElementById('reqShiftId').value = shiftId;
        document.getElementById('reqModalSubtitle').innerText = `Thiết lập số lượng nhân sự cần thiết cho ${shiftName}`;
        document.getElementById('reqError').style.display = 'none';

        // Reset all to 0
        document.querySelectorAll('.req-dept-input').forEach(el => el.value = 0);

        // Fill existing
        for (const [deptId, count] of Object.entries(reqs)) {
            const input = document.getElementById(`req-dept-${deptId}`);
            if (input) input.value = count;
        }

        document.getElementById('reqModal').style.display = 'flex';
    }

    function closeReqModal() {
        document.getElementById('reqModal').style.display = 'none';
    }

    async function submitReqForm(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitReq');
        const err = document.getElementById('reqError');
        const shiftId = document.getElementById('reqShiftId').value;
        
        btn.disabled = true;
        btn.innerText = 'Đang lưu...';
        err.style.display = 'none';

        const requirements = {};
        document.querySelectorAll('.req-dept-input').forEach(el => {
            requirements[el.dataset.deptId] = el.value;
        });

        try {
            const res = await fetch(`{{ url('admin/schedules') }}/${shiftId}/requirements`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ requirements })
            });
            const json = await res.json();
            
            if (json.success) {
                window.location.reload();
            } else {
                err.innerText = json.message || 'Có lỗi xảy ra.';
                err.style.display = 'block';
                btn.disabled = false;
                btn.innerText = 'Lưu định mức';
            }
        } catch (error) {
            err.innerText = 'Lỗi kết nối máy chủ.';
            err.style.display = 'block';
            btn.disabled = false;
            btn.innerText = 'Lưu định mức';
        }
    }
</script>
@endpush
