@extends('layouts.admin.master')

@section('page_title', 'Cập nhật Nhân sự')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@push('styles')
<style>
    .tabs-nav { display:flex; border-bottom:1px solid #e2e8f0; margin-bottom:24px; gap:24px; }
    .tab-btn { background:none; border:none; padding:12px 0; font-size:14px; font-weight:600; color:#64748b; cursor:pointer; position:relative; }
    .tab-btn.active { color:#0b57d0; }
    .tab-btn.active::after { content:''; position:absolute; bottom:-1px; left:0; width:100%; height:2px; background:#0b57d0; }
    .tab-content { display:none; }
    .tab-content.active { display:block; }
    
    .form-group { margin-bottom:20px; }
    .form-label { display:block; font-size:13px; font-weight:600; color:#0b1c30; margin-bottom:8px; }
    .form-input { width:100%; padding:10px 14px; border:1px solid #cbd5e1; border-radius:8px; outline:none; font-family:inherit; background:#fff; }
    .form-input:focus { border-color:#0b57d0; box-shadow:0 0 0 3px rgba(11,87,208,0.1); }
    .req { color:#ba1a1a; }
</style>
@endpush

@section('content')
<div style="max-width:800px;">
    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.staffs.index') }}" style="font-size:14px;color:#0b57d0;text-decoration:none;display:inline-flex;align-items:center;gap:6px;font-weight:600;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="dashboard-card" style="padding:32px;">
        <h1 style="font-size:20px;font-weight:700;color:#0b1c30;margin-bottom:24px;">Cập nhật hồ sơ nhân sự: {{ $staff->full_name }}</h1>

        @if($errors->any())
            <div style="background:#fee2e2; color:#b91c1c; padding:12px; border-radius:8px; margin-bottom:24px; font-size:13px;">
                <ul style="margin:0; padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.staffs.update', $staff->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="tabs-nav">
                <button type="button" class="tab-btn active" data-target="tab1">Thông tin cá nhân</button>
                <button type="button" class="tab-btn" data-target="tab2">Công việc & Phòng ban</button>
                <button type="button" class="tab-btn" data-target="tab3">Hợp đồng lao động</button>
                <button type="button" class="tab-btn" data-target="tab4">Tài khoản hệ thống</button>
            </div>

            <!-- Tab 1 -->
            <div id="tab1" class="tab-content active">
                <div class="form-group">
                    <label class="form-label">Họ và tên <span class="req">*</span></label>
                    <input type="text" name="full_name" class="form-input" value="{{ old('full_name', $staff->full_name) }}" required>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="phone" class="form-input" value="{{ old('phone', $staff->phone) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CCCD / CMND</label>
                        <input type="text" name="cccd" class="form-input" value="{{ old('cccd', $staff->cccd) }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date" name="dob" class="form-input" value="{{ old('dob', optional($staff->dob)->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" class="form-input" value="{{ old('address', $staff->address) }}">
                </div>
            </div>

            <!-- Tab 2 -->
            <div id="tab2" class="tab-content">
                <div class="form-group">
                    <label class="form-label">Phòng ban</label>
                    <select name="department_id" class="form-input">
                        <option value="">-- Chọn phòng ban --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" @selected(old('department_id', $staff->department_id) == $dept->id)>{{ $dept->code ? $dept->code . ' - ' : '' }}{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Trạng thái làm việc <span class="req">*</span></label>
                    <select name="status" class="form-input" required>
                        <option value="active" @selected(old('status', $staff->status) === 'active')>Đang làm việc</option>
                        <option value="inactive" @selected(old('status', $staff->status) === 'inactive')>Đã nghỉ việc</option>
                    </select>
                </div>
            </div>

            <!-- Tab 3 -->
            <div id="tab3" class="tab-content">
                @if($staff->contracts->count() > 0)
                    <div style="margin-bottom:24px;">
                        <h3 style="font-size:14px; color:#0b1c30; margin-bottom:12px;">Lịch sử hợp đồng hiện tại:</h3>
                        <table style="width:100%; border-collapse:collapse; font-size:13px; text-align:left;">
                            <thead>
                                <tr style="background:#f8f9fa;">
                                    <th style="padding:10px; border:1px solid #e2e8f0;">Số HĐ</th>
                                    <th style="padding:10px; border:1px solid #e2e8f0;">Loại</th>
                                    <th style="padding:10px; border:1px solid #e2e8f0;">Thời hạn</th>
                                    <th style="padding:10px; border:1px solid #e2e8f0;">Lương CB</th>
                                    <th style="padding:10px; border:1px solid #e2e8f0;">Trạng thái</th>
                                    <th style="padding:10px; border:1px solid #e2e8f0;">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staff->contracts as $contract)
                                <tr>
                                    <td style="padding:10px; border:1px solid #e2e8f0;">{{ $contract->contract_number }}</td>
                                    <td style="padding:10px; border:1px solid #e2e8f0;">{{ $contract->type }}</td>
                                    <td style="padding:10px; border:1px solid #e2e8f0;">{{ optional($contract->start_date)->format('d/m/Y') }} - {{ optional($contract->end_date)->format('d/m/Y') ?? 'Vô thời hạn' }}</td>
                                    <td style="padding:10px; border:1px solid #e2e8f0;">{{ number_format($contract->base_salary) }}đ</td>
                                    <td style="padding:10px; border:1px solid #e2e8f0;">
                                        @if($contract->is_expired)
                                            <span style="background:#fee2e2; color:#b91c1c; padding:4px 8px; border-radius:4px; font-weight:600; font-size:12px;">Hết hạn</span>
                                        @else
                                            <span style="background:#e6f4ea; color:#137333; padding:4px 8px; border-radius:4px; font-weight:600; font-size:12px;">Còn hiệu lực</span>
                                        @endif
                                    </td>
                                    <td style="padding:10px; border:1px solid #e2e8f0; display:flex; gap:8px;">
                                        @if($contract->file_path)
                                            <a href="{{ asset('storage/' . $contract->file_path) }}" target="_blank" style="color:#0b57d0; text-decoration:none; font-weight:600;">Xem file</a>
                                        @endif
                                        <button type="button" onclick="document.getElementById('delete-contract-{{ $contract->id }}').submit()" style="background:none; border:none; color:#b91c1c; font-weight:600; cursor:pointer; padding:0;">Xóa</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                
                <h3 style="font-size:14px; color:#0b1c30; margin-bottom:12px;">Thêm hợp đồng mới (Để trống nếu không thêm)</h3>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Số hợp đồng mới</label>
                        <input type="text" name="contract_number" class="form-input" value="{{ old('contract_number') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Loại hợp đồng</label>
                        <input type="text" name="contract_type" class="form-input" value="{{ old('contract_type') }}" placeholder="VD: Full-time, Thử việc...">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Mức lương cơ bản (VNĐ)</label>
                    <input type="number" name="base_salary" class="form-input" value="{{ old('base_salary') }}">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-input" value="{{ old('start_date') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ngày kết thúc (nếu có)</label>
                        <input type="date" name="end_date" class="form-input" value="{{ old('end_date') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">File đính kèm (PDF/Ảnh bản scan)</label>
                    <input type="file" name="contract_file" class="form-input" accept=".pdf, .jpg, .jpeg, .png">
                </div>
            </div>

            <!-- Tab 4 -->
            <div id="tab4" class="tab-content">
                @if($staff->user)
                    <div style="background:#e6f4ea; color:#137333; padding:16px; border-radius:8px; border:1px solid #bce2c7;">
                        <div style="font-weight:700; margin-bottom:8px;"><i class="fa-solid fa-circle-check"></i> Đã cấp tài khoản hệ thống</div>
                        <div>Email đăng nhập: <strong>{{ $staff->user->email }}</strong></div>
                        <div>Vai trò: <strong>{{ $roles[$staff->user->role] ?? $staff->user->role }}</strong></div>
                        <div style="margin-top:12px;">
                            <a href="{{ route('admin.users.edit', $staff->user->id) }}" style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; background:#fff; color:#137333; font-weight:600; text-decoration:none; border-radius:6px; font-size:13px; border:1px solid #bce2c7;">
                                <i class="fa-solid fa-pen-to-square"></i> Quản lý tài khoản (Đổi mật khẩu/Khóa)
                            </a>
                        </div>
                    </div>
                @else
                    <div style="background:#f1f5f9; color:#475569; padding:16px; border-radius:8px; border:1px solid #e2e8f0;">
                        <p style="margin-bottom:12px;">Nhân sự này chưa được cấp tài khoản hệ thống.</p>
                        <p>Để cấp quyền đăng nhập, vui lòng lưu thông tin hiện tại, sau đó quay lại tính năng "Phân quyền người dùng" và tạo tài khoản thủ công, chọn Role và liên kết thông tin nếu cần thiết. Hoặc tính năng này đã được tách ra để tránh xung đột.</p>
                    </div>
                @endif
            </div>

            <div style="display:flex;gap:12px;margin-top:24px;border-top:1px solid #e2e8f0;padding-top:24px;">
                <button type="submit" class="btn-new-broadcast" style="width:auto; margin:0;"><i class="fa-solid fa-check"></i> Lưu thay đổi</button>
                <a href="{{ route('admin.staffs.index') }}" style="display:inline-flex;align-items:center;padding:10px 24px;border-radius:8px;background:#f1f5f9;color:#475569;font-weight:600;text-decoration:none;">Hủy</a>
            </div>
        </form>
        
        <form method="POST" action="{{ route('admin.staffs.destroy', $staff->id) }}" style="margin-top:40px; border-top:1px dashed #e2e8f0; padding-top:24px;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hồ sơ nhân sự này? Thao tác không thể hoàn tác!');">
            @csrf
            @method('DELETE')
            <button type="submit" style="background:#fee2e2; color:#b91c1c; border:none; padding:10px 20px; border-radius:8px; font-weight:600; cursor:pointer;"><i class="fa-solid fa-trash-can"></i> Xóa hồ sơ nhân sự</button>
        </form>

        @foreach($staff->contracts as $contract)
        <form id="delete-contract-{{ $contract->id }}" action="{{ route('admin.contracts.destroy', $contract->id) }}" method="POST" onsubmit="return confirm('Xóa hợp đồng này?');" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Tab switching
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.target;
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById(target).classList.add('active');
            });
        });
    });
</script>
@endpush
