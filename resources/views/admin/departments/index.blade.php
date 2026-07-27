@extends('layouts.admin.master')

@section('page_title', 'Danh mục Phòng ban')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@section('content')
<div style="display:flex; gap:24px; flex-wrap:wrap; align-items:flex-start;">
    
    <!-- Cột trái: Form thêm mới -->
    <div class="dashboard-card" style="flex:1; min-width:300px; max-width:400px; padding:24px;">
        <h2 style="font-size:16px; font-weight:700; color:#0b1c30; margin-bottom:20px;">Thêm phòng ban mới</h2>
        
        @if ($errors->any())
            <div style="background:#fee2e2; color:#b91c1c; padding:12px; border-radius:8px; margin-bottom:16px; font-size:13px;">
                <ul style="margin:0; padding-left:20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.departments.store') }}">
            @csrf
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px;">Mã phòng ban <span style="color:#ba1a1a;">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" required placeholder="VD: TECH, SEC, MGT" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
            </div>
            
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px;">Tên phòng ban <span style="color:#ba1a1a;">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="VD: Kỹ thuật, An ninh" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px;">Mô tả</label>
                <textarea name="description" rows="3" placeholder="Chức năng của phòng ban..." style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit; resize:vertical;">{{ old('description') }}</textarea>
            </div>

            <div style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="is_shift" id="is_shift" value="1" {{ old('is_shift') ? 'checked' : '' }}>
                <label for="is_shift" style="font-size:13px; font-weight:500; color:#0b1c30; cursor:pointer;">Có tham gia phân ca trực</label>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px;">Trạng thái</label>
                <select name="status" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Ngừng sử dụng</option>
                </select>
            </div>

            <button type="submit" class="btn-new-broadcast" style="width:100%; margin:0; justify-content:center;">
                <i class="fa-solid fa-plus"></i> Thêm phòng ban
            </button>
        </form>
    </div>

    <!-- Cột phải: Danh sách -->
    <div class="dashboard-card" style="flex:2; min-width:400px; padding:0;">
        <div style="padding:24px; border-bottom:1px solid #e2e8f0;">
            <h2 style="font-size:16px; font-weight:700; color:#0b1c30; margin:0;">Danh sách phòng ban</h2>
        </div>

        @if (session('success'))
            <div style="background:#e6f4ea; color:#137333; padding:12px; margin:24px 24px 0 24px; border-radius:8px; font-size:14px; font-weight:600;">
                {{ session('success') }}
            </div>
        @endif
        
        @if ($errors->has('error'))
            <div style="background:#fee2e2; color:#b91c1c; padding:12px; margin:24px 24px 0 24px; border-radius:8px; font-size:14px; font-weight:600;">
                {{ $errors->first('error') }}
            </div>
        @endif

        <div style="overflow-x:auto; padding:24px;">
            <table style="width:100%; border-collapse:collapse; text-align:left;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="padding:14px; border-bottom:1px solid #e2e8f0; font-size:12px; font-weight:700; color:#475569;">Mã PB</th>
                        <th style="padding:14px; border-bottom:1px solid #e2e8f0; font-size:12px; font-weight:700; color:#475569;">Tên phòng ban</th>
                        <th style="padding:14px; border-bottom:1px solid #e2e8f0; font-size:12px; font-weight:700; color:#475569;">Số lượng</th>
                        <th style="padding:14px; border-bottom:1px solid #e2e8f0; font-size:12px; font-weight:700; color:#475569;">Thuộc tính</th>
                        <th style="padding:14px; border-bottom:1px solid #e2e8f0; font-size:12px; font-weight:700; color:#475569;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $dept)
                    <tr>
                        <td style="padding:14px; border-bottom:1px solid #e2e8f0; font-weight:700; color:#0b57d0;">{{ $dept->code }}</td>
                        <td style="padding:14px; border-bottom:1px solid #e2e8f0;">
                            <div style="font-weight:600; color:#0b1c30;">{{ $dept->name }}</div>
                            @if($dept->description)
                            <div style="font-size:12px; color:#64748b; margin-top:4px;">{{ mb_strimwidth($dept->description, 0, 40, '...') }}</div>
                            @endif
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #e2e8f0;">
                            <a href="{{ route('admin.staffs.index', ['department_id' => $dept->id]) }}" style="color:#0b57d0; font-weight:600; text-decoration:none; background:#eff6ff; padding:4px 8px; border-radius:6px; font-size:13px;">
                                {{ $dept->staffs_count }} người
                            </a>
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #e2e8f0;">
                            <div style="display:flex; flex-direction:column; gap:4px; font-size:12px;">
                                @if($dept->is_shift)
                                    <span style="color:#059669; font-weight:600;">✅ Có phân ca</span>
                                @else
                                    <span style="color:#64748b;">❌ Không phân ca</span>
                                @endif
                                
                                @if($dept->status === 'active')
                                    <span style="color:#059669; font-weight:600;">🟢 Hoạt động</span>
                                @else
                                    <span style="color:#b91c1c; font-weight:600;">🔴 Ngừng sử dụng</span>
                                @endif
                            </div>
                        </td>
                        <td style="padding:14px; border-bottom:1px solid #e2e8f0;">
                            <div style="display:flex; gap:8px;">
                                <button type="button" onclick="openEditModal({{ $dept->id }}, '{{ $dept->code }}', '{{ $dept->name }}', '{{ htmlspecialchars($dept->description, ENT_QUOTES) }}', {{ $dept->is_shift ? 'true' : 'false' }}, '{{ $dept->status }}')" style="background:#f1f5f9; color:#0b57d0; border:none; padding:6px 12px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;">Sửa</button>
                                
                                @if($dept->staffs_count > 0)
                                    <button type="button" title="Không thể xóa do còn nhân sự" style="background:#f1f5f9; color:#94a3b8; border:none; padding:6px 12px; border-radius:6px; font-size:13px; font-weight:600; cursor:not-allowed;">Xóa</button>
                                @else
                                    <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa phòng ban này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background:#fee2e2; color:#b91c1c; border:none; padding:6px 12px; border-radius:6px; font-size:13px; font-weight:600; cursor:pointer;">Xóa</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding:30px; text-align:center; color:#64748b;">Chưa có dữ liệu phòng ban.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div style="margin-top:20px;">
                {{ $departments->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:100%; max-width:400px; border-radius:12px; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.1);">
        <h2 style="font-size:18px; font-weight:700; color:#0b1c30; margin-bottom:20px;">Cập nhật phòng ban</h2>
        
        <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px;">Mã phòng ban <span style="color:#ba1a1a;">*</span></label>
                <input type="text" name="code" id="editCode" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
            </div>
            
            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px;">Tên phòng ban <span style="color:#ba1a1a;">*</span></label>
                <input type="text" name="name" id="editName" required style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
            </div>

            <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px;">Mô tả</label>
                <textarea name="description" id="editDesc" rows="3" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit; resize:vertical;"></textarea>
            </div>

            <div style="margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="is_shift" id="editIsShift" value="1">
                <label for="editIsShift" style="font-size:13px; font-weight:500; color:#0b1c30; cursor:pointer;">Có tham gia phân ca trực</label>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px;">Trạng thái</label>
                <select name="status" id="editStatus" style="width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit;">
                    <option value="active">Hoạt động</option>
                    <option value="inactive">Ngừng sử dụng</option>
                </select>
            </div>

            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button type="button" onclick="closeEditModal()" style="padding:10px 20px; background:#f1f5f9; color:#475569; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Hủy</button>
                <button type="submit" class="btn-new-broadcast" style="width:auto; margin:0;">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, code, name, desc, isShift, status) {
        document.getElementById('editForm').action = "{{ url('admin/departments') }}/" + id;
        document.getElementById('editCode').value = code;
        document.getElementById('editName').value = name;
        document.getElementById('editDesc').value = desc;
        document.getElementById('editIsShift').checked = isShift;
        document.getElementById('editStatus').value = status;
        document.getElementById('editModal').style.display = 'flex';
    }
    
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
@endsection
