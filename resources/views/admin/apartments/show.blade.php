@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Căn hộ')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')

<div class="dashboard-content">

    <div class="page-header">

        <div>
            <h1 class="page-title">Căn hộ {{ $apartment->apartment_number }}</h1>
            <p class="page-subtitle">
                {{ $apartment->floor->block->name }} - {{ $apartment->floor->name ?? 'Tầng ' . $apartment->floor->floor_number }}
            </p>
        </div>

        <div class="page-header-actions" style="display: flex; gap: 10px; align-items: center;">
            <a href="{{ route('admin.apartments.edit', $apartment) }}" class="btn btn-light">
                Sửa
            </a>
            <form action="{{ route('admin.apartments.destroy', $apartment) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa căn hộ này?')" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Xóa
                </button>
            </form>
            <a href="{{ route('admin.apartments.index') }}" class="btn btn-secondary">
                Quay lại
            </a>
        </div>

    </div>

    @if ($message = Session::get('success'))
        <div class="alert-success">
            {{ $message }}
        </div>
    @endif

    <div class="dashboard-grid">

        <article class="dashboard-card">
            <div class="card-header">
                <h2>Thông tin chung</h2>
            </div>

            <div class="card-body">
                <div class="detail-row">
                    <span class="detail-label">Số căn hộ</span>
                    <span>{{ $apartment->apartment_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tòa nhà</span>
                    <span>{{ $apartment->floor->block->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tầng</span>
                    <span>{{ $apartment->floor->name ?? 'Tầng ' . $apartment->floor->floor_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Diện tích</span>
                    <span>{{ $apartment->area }} m²</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Trạng thái</span>
                    <span>
                        @if($apartment->status == 'occupied')
                            <span class="badge badge-success">Đang ở</span>
                        @elseif($apartment->status == 'vacant')
                            <span class="badge badge-warning">Trống</span>
                        @else
                            <span class="badge badge-danger">Bảo trì</span>
                        @endif
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Mô tả</span>
                    <span>{{ $apartment->description ?? 'Không có mô tả' }}</span>
                </div>
            </div>
        </article>

        <article class="dashboard-card">
            <div class="card-header">
                <h2>Danh sách cư dân</h2>
            </div>

            <div class="card-body">
                @if($apartment->residents->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Điện thoại</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($apartment->residents as $resident)
                                <tr>
                                    <td>{{ $resident->name }}</td>
                                    <td>{{ $resident->email }}</td>
                                    <td>{{ $resident->phone ?? '-' }}</td>
                                    <td>
                                        @if($resident->status == 'active')
                                            <span class="badge badge-success">Hoạt động</span>
                                        @else
                                            <span class="badge badge-danger">Vô hiệu</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        Chưa có cư dân nào trong căn hộ này.
                    </div>
                @endif
            </div>
        </article>

    </div>

</div>

@endsection
