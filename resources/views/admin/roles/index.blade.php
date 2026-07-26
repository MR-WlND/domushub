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
                                    <button type="button" class="users-button users-button--secondary"
                                            style="height: 34px; font-size: 13px; padding: 0 12px; border: 1px solid #cbd5e1; background: #fff;"
                                            onclick="openRoleModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->role }}', '{{ $user->status }}')">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:4px;">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                        Sửa quyền
                                    </button>
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

    {{-- Modal Sửa Quyền --}}
    <div class="role-modal-overlay" id="roleModalOverlay">
        <div class="role-modal" onclick="event.stopPropagation()">
            <div class="role-modal__header">
                <h3>Phân quyền: <span id="modalUserName" style="color:#2563eb;"></span></h3>
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
                        <label>Vai trò hệ thống</label>
                        <select name="role" id="modalUserRole" required>
                            @foreach ($roleLabels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="role-modal__field">
                        <label>Trạng thái truy cập</label>
                        <select name="status" id="modalUserStatus" required>
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="role-modal__footer">
                    <button type="button" class="users-button users-button--secondary" onclick="closeRoleModal()">Hủy</button>
                    <button type="submit" class="users-button users-button--primary">Lưu phân quyền</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const overlay = document.getElementById('roleModalOverlay');
        const form = document.getElementById('roleModalForm');
        const nameSpan = document.getElementById('modalUserName');
        const roleSel = document.getElementById('modalUserRole');
        const statusSel = document.getElementById('modalUserStatus');

        function openRoleModal(id, name, role, status) {
            nameSpan.innerText = name;
            roleSel.value = role;
            statusSel.value = status;
            form.action = `/admin/roles/${id}/status`;
            overlay.classList.add('active');
        }

        function closeRoleModal() {
            overlay.classList.remove('active');
        }

        overlay.addEventListener('click', closeRoleModal);
    </script>
    @endpush

@endsection
