@extends('layouts.admin.master')

@section('page_title', 'Phân quyền người dùng')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@push('styles')
    @vite(['resources/css/pages/admin/users/index.css'])
    <style>
        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .role-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }
        .role-card__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .role-card__title {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
        }
        .role-card__count {
            background: #f1f5f9;
            color: #475569;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }
        .role-card__desc {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
            margin: 0;
        }

        /* Action Buttons */
        .btn-action-edit {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 34px;
            padding: 0 14px;
            font-size: 13px;
            font-weight: 600;
            color: #2563eb;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 1px 2px rgba(37,99,235,0.05);
        }
        .btn-action-edit:hover {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 4px 12px rgba(37,99,235,0.25);
            transform: translateY(-1px);
        }
        .btn-action-edit svg {
            transition: transform 0.2s ease;
        }
        .btn-action-edit:hover svg {
            transform: scale(1.1);
        }

        .btn-action-faceid {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 34px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-action-faceid--active {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .btn-action-faceid--active:hover {
            background: #10b981;
            color: #fff;
            box-shadow: 0 4px 12px rgba(16,185,129,0.25);
            transform: translateY(-1px);
        }
        .btn-action-faceid--pending {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .btn-action-faceid--pending:hover {
            background: #f59e0b;
            color: #fff;
            box-shadow: 0 4px 12px rgba(245,158,11,0.25);
            transform: translateY(-1px);
        }

        .btn-action-delete {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            height: 34px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 600;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-action-delete:hover {
            background: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
            box-shadow: 0 4px 12px rgba(220,38,38,0.25);
            transform: translateY(-1px);
        }

        /* Modal */
        .role-modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15,23,42,0.4);
            backdrop-filter: blur(2px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: all 0.2s ease;
        }
        .role-modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .role-modal {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
            transform: translateY(20px) scale(0.95);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .role-modal-overlay.active .role-modal {
            transform: translateY(0) scale(1);
        }
        .role-modal__header {
            padding: 20px 24px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .role-modal__header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
        }
        .role-modal__close {
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .role-modal__close:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .role-modal__body {
            padding: 24px;
        }
        .role-modal__field {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
        }
        .role-modal__field label {
            font-size: 14px;
            font-weight: 500;
            color: #334155;
        }
        .role-modal__field select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: #0f172a;
            outline: none;
        }
        .role-modal__field select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        .role-modal__footer {
            padding: 16px 24px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            border-radius: 0 0 16px 16px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
    </style>
@endpush

@php
    $roleLabels = [
        'admin'      => 'Quản trị viên',
        'manager'    => 'Quản lý',
        'staff'      => 'Kế toán',
        'technician' => 'Kỹ thuật',
        'security'   => 'An ninh',
        'cleaning'   => 'Vệ sinh',
    ];

    $statusLabels = [
        'pending' => 'Chờ kích hoạt',
        'active'  => 'Đang hoạt động',
        'banned'  => 'Đã khóa',
    ];
@endphp

@section('content')
    <div class="users-page">
        <div class="users-page__header">
            <div>
                <p class="users-page__eyebrow">Cấu hình hệ thống</p>
                <h1>Phân quyền người dùng</h1>
            </div>
            <div class="users-page__actions">
                <a href="{{ portal_route('system-logs.index') }}" class="users-button users-button--secondary" style="background:#fff; color:#475569; border:1px solid #cbd5e1; display:inline-flex; align-items:center; gap:6px;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <polyline points="1 20 1 14 7 14"></polyline>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                    </svg>
                    Lịch sử hệ thống
                </a>
                <a href="{{ portal_route('users.create') }}" class="users-button users-button--primary">+ Thêm nhân sự</a>
            </div>
        </div>

        @if (session('success'))
            <div class="users-alert users-alert--success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="users-alert users-alert--danger">{{ $errors->first() }}</div>
        @endif

        {{-- 1. Danh sách Roles (Cards) --}}
        <div class="roles-grid">
            @foreach($roleDescriptions as $roleKey => $desc)
                <div class="role-card">
                    <div class="role-card__header">
                        <h3 class="role-card__title">{{ $roleLabels[$roleKey] ?? $roleKey }}</h3>
                        <span class="role-card__count">{{ $roleCounts[$roleKey] ?? 0 }} tài khoản</span>
                    </div>
                    <p class="role-card__desc">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

        {{-- 2. Danh sách nhân sự & form lọc --}}
        <form class="users-filter" method="GET" action="{{ portal_route('roles.index') }}">
            <label class="users-filter__field">
                <span>Tìm kiếm</span>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Tên hoặc email">
            </label>

            <label class="users-filter__field">
                <span>Vai trò</span>
                <select name="role">
                    <option value="">Tất cả vai trò</option>
                    @foreach ($roleLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <div class="users-filter__actions">
                <button type="submit" class="users-button users-button--primary">Lọc</button>
                <a href="{{ portal_route('roles.index') }}" class="users-button users-button--secondary">Xóa lọc</a>
            </div>
        </form>

        <div class="users-table-card">
            <div class="users-table-wrap">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Nhân viên</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="users-identity">
                                        @if ($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="users-avatar-img">
                                        @else
                                            <span class="users-avatar-initial">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</span>
                                        @endif
                                        <div class="users-identity__details">
                                            <span class="users-identity__name">{{ $user->name }}</span>
                                            <span class="users-identity__email">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="users-role-text" style="font-weight: 500;">{{ $roleLabels[$user->role] ?? $user->role }}</span>
                                </td>
                                <td>
                                    <span class="status-pill status-pill--{{ $user->status }}">
                                        <span class="status-dot"></span>
                                        {{ $statusLabels[$user->status] ?? $user->status }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                        <a href="{{ portal_route('users.edit', $user->id) }}" class="btn-action-edit">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="3"></circle>
                                                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                            </svg>
                                            Sửa hồ sơ
                                        </a>



                                        @if((int)$user->id !== (int)auth()->id())
                                            <form action="{{ portal_route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản {{ $user->name }} không?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-action-delete">
                                                    🗑️ Xóa
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <div class="users-empty">Không tìm thấy tài khoản phù hợp.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="users-table-footer">
                <div class="users-table-footer__stats">
                    Hiển thị {{ $users->count() }} trên {{ $users->total() }} nhân sự
                </div>
                <div>
                    {{ $users->appends(request()->query())->links('admin.users.pagination') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Sửa Hồ Sơ & Phân Quyền --}}
    <div class="role-modal-overlay" id="roleModalOverlay">
        <div class="role-modal" onclick="event.stopPropagation()" style="max-width:480px;">
            <div class="role-modal__header">
                <h3 style="margin:0; font-size:18px; font-weight:800; color:#0f172a;">Sửa Hồ Sơ & Phân Quyền</h3>
                <button type="button" class="role-modal__close" onclick="closeRoleModal()">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form id="roleModalForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="role-modal__body">
                    <div class="role-modal__field">
                        <label style="font-weight:700;">Họ và tên (*)</label>
                        <input type="text" name="name" id="modalUserName" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                    </div>

                    <div style="display:flex; gap:12px;" class="role-modal__field">
                        <div style="flex:1;">
                            <label style="font-weight:700;">Số điện thoại</label>
                            <input type="text" name="phone" id="modalUserPhone" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                        </div>
                        <div style="flex:1;">
                            <label style="font-weight:700;">Email (Tài khoản)</label>
                            <input type="email" id="modalUserEmail" readonly disabled style="width:100%; padding:8px 12px; border:1px solid #e2e8f0; background:#f8fafc; border-radius:8px; color:#64748b;">
                        </div>
                    </div>

                    <div style="display:flex; gap:12px;" class="role-modal__field">
                        <div style="flex:1;">
                            <label style="font-weight:700;">Vai trò hệ thống</label>
                            <select name="role" id="modalUserRole" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                                @foreach ($roleLabels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="flex:1;">
                            <label style="font-weight:700;">Trạng thái tài khoản</label>
                            <select name="status" id="modalUserStatus" required style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:8px;">
                                @foreach ($statusLabels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div style="margin-top:12px; background:#f8fafc; padding:12px; border-radius:8px; border:1px solid #e2e8f0;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; font-weight:700; color:#334155; cursor:pointer; margin:0;">
                            <input type="checkbox" name="reset_password" value="1">
                            <span>🔑 Đặt lại mật khẩu mặc định (Chungcu@2026)</span>
                        </label>
                    </div>
                </div>
                <div class="role-modal__footer">
                    <button type="button" class="users-button users-button--secondary" onclick="closeRoleModal()">Hủy</button>
                    <button type="submit" class="users-button users-button--primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const overlay = document.getElementById('roleModalOverlay');
        const form = document.getElementById('roleModalForm');

        function openRoleModal(user) {
            document.getElementById('modalUserName').value = user.name || '';
            document.getElementById('modalUserPhone').value = user.phone || '';
            document.getElementById('modalUserEmail').value = user.email || '';
            document.getElementById('modalUserRole').value = user.role || 'staff';
            document.getElementById('modalUserStatus').value = user.status || 'active';
            
            form.action = `/admin/roles/${user.id}/status`;
            overlay.classList.add('active');
        }

        function closeRoleModal() {
            overlay.classList.remove('active');
        }

        overlay.addEventListener('click', closeRoleModal);
    </script>
    @endpush

@endsection
