@extends('layouts.admin.master')

@section('page_title', 'Duyệt nhân khẩu')

@section('content')
<div class="admin-page-header">
    <h1>Duyệt nhân khẩu</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
</div>

<div class="admin-table">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Căn hộ</th>
                <th>Họ tên</th>
                <th>Quan hệ</th>
                <th>Năm sinh</th>
                <th>Trạng thái</th>
                <th>Tài khoản</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ optional($member->apartment->floor->block)->name ?? '—' }} / 
                        {{ optional($member->apartment)->apartment_number ?? '—' }}
                    </td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->relationship }}</td>
                    <td>{{ $member->birth_year ?? '—' }}</td>
                    <td>{{ ucfirst($member->status) }}</td>
                    <td>{{ $member->user ? 'Đã có' : 'Chưa có' }}</td>
                    <td>
                        @if($member->status === 'pending')
                            <form style="display:inline-block" method="POST" action="{{ route('admin.apartment-members.verify', $member->id) }}">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success">Duyệt</button>
                            </form>
                            <form style="display:inline-block" method="POST" action="{{ route('admin.apartment-members.reject', $member->id) }}">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-danger">Từ chối</button>
                            </form>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Chưa có nhân khẩu nào để duyệt.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
