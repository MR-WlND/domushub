@extends('layouts.admin.master')

@section('page_title', 'Quản lý Chỉ số Điện Nước')
@section('page_kicker', 'Quản lý hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Quản lý Chỉ số Điện Nước</h1>
            <p class="page-subtitle">Danh sách chỉ số theo căn hộ và tháng/năm</p>
        </div>
        <a href="{{ route('admin.utility-readings.create') }}" class="btn btn-primary">
            + Ghi chỉ số mới
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert-success">
            {{ $message }}
        </div>
    @endif

    <form action="{{ route('admin.utility-readings.index') }}" method="GET" class="filter-panel">
        <div class="form-grid-4" style="gap: 12px; align-items: end;">
            <div>
                <label class="form-label-custom">Tòa nhà</label>
                <select name="block_id" class="form-input-custom">
                    <option value="">Tất cả</option>
                    @foreach ($blocks as $block)
                        <option value="{{ $block->id }}" {{ $blockId == $block->id ? 'selected' : '' }}>
                            {{ $block->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label-custom">Tầng</label>
                <select name="floor_id" class="form-input-custom">
                    <option value="">Tất cả</option>
                    @foreach ($floors as $floor)
                        <option value="{{ $floor->id }}" {{ $floorId == $floor->id ? 'selected' : '' }}>
                            {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label-custom">Loại</label>
                <select name="type" class="form-input-custom">
                    <option value="">Tất cả</option>
                    <option value="electricity" {{ $type == 'electricity' ? 'selected' : '' }}>Điện</option>
                    <option value="water" {{ $type == 'water' ? 'selected' : '' }}>Nước</option>
                </select>
            </div>

            <div>
                <label class="form-label-custom">Tháng/Năm</label>
                <div class="form-grid-2" style="gap: 8px;">
                    <input type="number" name="month" value="{{ $month }}" min="1" max="12"
                        class="form-input-custom" placeholder="Tháng" />
                    <input type="number" name="year" value="{{ $year }}" min="2020" max="2100"
                        class="form-input-custom" placeholder="Năm" />
                </div>
            </div>

            <div style="display:flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary">Lọc</button>
                <a href="{{ route('admin.utility-readings.index') }}" class="btn btn-outline">Đặt lại</a>
            </div>
        </div>
    </form>

    @if ($readings->count() > 0)
        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Phòng</th>
                        <th>Tòa</th>
                        <th>Tầng</th>
                        <th>Loại</th>
                        <th>Tháng/Năm</th>
                        <th>Chỉ số cũ</th>
                        <th>Chỉ số mới</th>
                        <th>Tiêu thụ</th>
                        <th>Người ghi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($readings as $reading)
                        <tr>
                            <td>{{ $reading->apartment->apartment_number ?? 'N/A' }}</td>
                            <td>{{ $reading->apartment->floor->block->name ?? 'N/A' }}</td>
                            <td>{{ $reading->apartment->floor->name ?? 'Tầng ' . $reading->apartment->floor->floor_number }}
                            </td>
                            <td>{{ ucfirst($reading->type) }}</td>
                            <td>{{ $reading->record_month }}/{{ $reading->record_year }}</td>
                            <td>{{ $reading->old_value }}</td>
                            <td>{{ $reading->new_value }}</td>
                            <td>{{ $reading->usage_amount }}</td>
                            <td>{{ $reading->recorder->name ?? '---' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $readings->links() }}
        </div>
    @else
        <div class="empty-state">
            <p>Chưa có chỉ số nào được ghi.</p>
        </div>
    @endif
@endsection
