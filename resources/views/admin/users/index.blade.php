@extends('layouts.admin.master')

@section('page_title', 'Phân quyền người dùng')
@section('home_route', route('home'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@php
    $roleLabels = [
        'admin' => 'Quản trị viên',
        'manager' => 'Quản lý',
        'staff' => 'Nhân viên',
        'technician' => 'Kỹ thuật',
        'security' => 'An ninh',
        'resident' => 'Cư dân',
    ];

    $statusLabels = [
        'active' => 'Đang hoạt động',
        'pending' => 'Chờ duyệt',
        'inactive' => 'Tạm khóa',
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
                <button type="button" class="users-button users-button--primary" onclick="openCreateModal()">Thêm nhân
                    sự</button>
            </div>
        </div>

        @if (session('success'))
            <div class="users-alert users-alert--success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="users-alert users-alert--danger">{{ $errors->first() }}</div>
        @endif

        <form class="users-filter" method="GET" action="{{ route('admin.users.index') }}">
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

            <label class="users-filter__field">
                <span>Trạng thái</span>
                <select name="status">
                    <option value="">Tất cả trạng thái</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>

            <div class="users-filter__actions">
                <button type="submit" class="users-button users-button--primary">Lọc</button>
                <a href="{{ route('admin.users.index') }}" class="users-button users-button--secondary">Xóa lọc</a>
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
                            @php
                                // Logic mapping hiển thị vai trò nên được đồng bộ từ Controller hoặc Model
                                $displayRole = $roleLabels[$user->role] ?? $user->role;
                            @endphp
                            <tr>
                                <td>
                                    <div class="users-identity">
                                        @if ($user->avatar)
                                            <img src="{{ $user->avatar }}" alt="{{ $user->name }}"
                                                class="users-avatar-img">
                                        @else
                                            <span
                                                class="users-avatar-initial">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</span>
                                        @endif
                                        <div class="users-identity__details">
                                            <span class="users-identity__name">{{ $user->name }}</span>
                                            <span class="users-identity__email">{{ $user->email }}</span>
                                            <span class="users-identity__joined">Ngày tham gia:
                                                {{ optional($user->created_at)->format('d/m/Y') ?? 'Chưa có' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="users-role-text">{{ $displayRole }}</span>
                                </td>
                                <td>
                                    <span class="status-pill status-pill--{{ $user->status }}">
                                        <span class="status-dot"></span>
                                        {{ $statusLabels[$user->status] ?? $user->status }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn-edit-permissions"
                                        onclick="openEditModal({{ json_encode($user) }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path
                                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z">
                                            </path>
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

    <div id="createStaffModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Thêm nhân sự</h3>
                <button type="button" class="modal-close" onclick="closeCreateModal()">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="modal-form-group">
                        <label for="createUserName">Tên nhân sự</label>
                        <input type="text" id="createUserName" name="name" value="{{ old('name') }}" required>
                    </div>

                    <div class="modal-form-group">
                        <label for="createUserEmail">Email</label>
                        <input type="email" id="createUserEmail" name="email" value="{{ old('email') }}" required>
                    </div>

                    <div class="modal-form-group">
                        <label for="createRoleSelect">Vai trò</label>
                        <select name="role" id="createRoleSelect" required>
                            @foreach ($roleLabels as $value => $label)
                                <option value="{{ $value }}" @selected(old('role', 'staff') === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-note">
                        Mật khẩu mặc định: <strong>Chungcu@2026</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" onclick="closeCreateModal()">Hủy</button>
                    <button type="submit" class="btn-modal-save">Tạo tài khoản</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editPermissionsModal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3 id="modalTitle">Sửa quyền người dùng</h3>
                <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>

            <form id="editPermissionsForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="modal-form-group">
                        <label for="modalUserName">Người dùng</label>
                        <input type="text" id="modalUserName" disabled>
                    </div>

                    <div class="modal-form-group">
                        <label for="modalRoleSelect">Vai trò</label>
                        <select name="role" id="modalRoleSelect">
                            @foreach ($roleLabels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-form-group">
                        <label for="modalStatusSelect">Trạng thái</label>
                        <select name="status" id="modalStatusSelect">
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="resetPasswordForm" class="btn-modal-reset">Đặt lại mật khẩu</button>
                    <button type="button" class="btn-modal-cancel" onclick="closeEditModal()">Hủy</button>
                    <button type="submit" class="btn-modal-save">Lưu thay đổi</button>
                </div>
            </form>

            <form id="resetPasswordForm" method="POST" action=""
                onsubmit="return confirm('Đặt lại mật khẩu tài khoản này về Chungcu@2026?')">
                @csrf
                @method('PUT')
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const modal = document.getElementById('editPermissionsModal');
        const createModal = document.getElementById('createStaffModal');
        const form = document.getElementById('editPermissionsForm');
        const resetForm = document.getElementById('resetPasswordForm');
        const userNameInput = document.getElementById('modalUserName');
        const roleSelect = document.getElementById('modalRoleSelect');
        const statusSelect = document.getElementById('modalStatusSelect');

        function openCreateModal() {
            createModal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeCreateModal() {
            createModal.classList.remove('open');
            document.body.style.overflow = '';
        }

        function openEditModal(user) {
            form.action = `/users/${user.id}/update-status`;
            resetForm.action = `/users/${user.id}/reset-password`;

            userNameInput.value = user.name;
            roleSelect.value = user.role;
            statusSelect.value = user.status;

            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            modal.classList.remove('open');
            document.body.style.overflow = '';
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeEditModal();
        });

        createModal.addEventListener('click', function(e) {
            if (e.target === createModal) closeCreateModal();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.classList.contains('open')) closeEditModal();
            if (e.key === 'Escape' && createModal.classList.contains('open')) closeCreateModal();
        });
    </script>
@endpush
