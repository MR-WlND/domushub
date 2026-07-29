@extends('layouts.admin.master')

@section('page_title', 'Phân quyền người dùng')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role_label', 'ADMIN')

@push('styles')
    @vite(['resources/css/pages/admin/users/index.css'])
@endpush

@php
    $roleLabels = [
        'admin' => 'Quản trị viên',
        'manager' => 'Quản lý',
        'staff' => 'Nhân viên kế toán',
        'technician' => 'Kỹ thuật',
        'security' => 'An ninh',
        'cleaning' => 'Nhân viên vệ sinh',
        'receptionist' => 'Lễ tân',
    ];

    $statusLabels = [
        'pending' => 'Chờ kích hoạt',
        'active' => 'Đang hoạt động',
        'banned' => 'Đã khóa',
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

        <form class="users-filter" method="GET" action="{{ portal_route('users.index') }}">
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
                <a href="{{ portal_route('users.index') }}" class="users-button users-button--secondary">Xóa lọc</a>
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
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
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
                                    <a href="{{ portal_route('users.edit', $user) }}" class="btn-edit-permissions">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path
                                                d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z">
                                            </path>
                                        </svg>
                                        Sửa quyền
                                    </a>
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

@endsection
