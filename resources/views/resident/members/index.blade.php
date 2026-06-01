@extends('layouts.resident.master')

@section('title', 'Quản lý thành viên')

@section('content')
@push('styles')
    @vite(['resources/css/pages/resident/members.css'])
@endpush
<div class="page-header">
    <h1>Quản lý thành viên</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
</div>

<div class="member-card">
    <h2>Danh sách nhân khẩu căn hộ</h2>
    <p>Căn hộ: {{ optional($ownerMember->apartment->floor->block)->name ?? '—' }} / Căn {{ optional($ownerMember->apartment)->apartment_number ?? '—' }}</p>

    <div class="member-actions">
        <h3>Thêm nhân khẩu</h3>
        <form method="POST" action="{{ route('resident.members.store') }}" class="add-member-form">
            @csrf
            <div class="form-row">
                <input type="text" name="name" placeholder="Họ và tên" required>
                <input type="date" name="date_of_birth" placeholder="Ngày sinh">
                <input type="text" name="relationship" placeholder="Quan hệ" required>
                <input type="text" name="phone" placeholder="Số điện thoại">
                <input type="email" name="email" placeholder="Email">
                <button type="submit" class="btn btn-secondary">Thêm</button>
            </div>
            @error('name')<span class="error-msg">{{ $message }}</span>@enderror
            @error('relationship')<span class="error-msg">{{ $message }}</span>@enderror
        </form>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Họ tên</th>
                <th>Quan hệ</th>
                    <th>Ngày sinh</th>
                    <th>Liên hệ</th>
                <th>Trạng thái</th>
                <th>Mã mời</th>
                <th>Tài khoản</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->relationship }}</td>
                    <td>{{ optional($member->date_of_birth)->format('d/m/Y') ?? ($member->birth_year ?? '—') }}</td>
                    <td>
                        @if($member->phone) <div>{{ $member->phone }}</div> @endif
                        @if($member->email) <div>{{ $member->email }}</div> @endif
                    </td>
                    <td>{{ ucfirst($member->status) }}</td>
                    <td>{{ optional($member->invitation)->code ?? '—' }}</td>
                    <td>{{ $member->user ? 'Đã có tài khoản' : 'Chưa có tài khoản' }}</td>
                    <td>
                        @if(!$member->user && $member->status === 'verified')
                            <form method="POST" action="{{ route('resident.members.invite', $member->id) }}" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-primary">Tạo mã mời</button>
                            </form>
                            @if(!$member->user)
                                <form method="POST" action="{{ route('resident.members.destroy', $member->id) }}" style="display:inline-block;" onsubmit="return confirm('Bạn có chắc muốn xóa nhân khẩu này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Xóa</button>
                                </form>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($memberInvites->isNotEmpty())
<div class="member-card">
    <h2>Mã mời thành viên đã tạo</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Mã</th>
                <th>Thành viên</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($memberInvites as $invite)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $invite->code }}</td>
                    <td>{{ optional($invite->apartmentMember)->name ?? '—' }}</td>
                    <td>{{ ucfirst($invite->status) }}</td>
                    <td>{{ $invite->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
