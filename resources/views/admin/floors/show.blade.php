@extends('layouts.admin.master')

@section('page_title', 'Chi tiết Tầng')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@section('content')

    <div class="dashboard-content" style="padding: 24px; max-width: 1400px; margin: 0 auto; background: #f8fafc; min-height: calc(100vh - 64px);">

        {{-- Breadcrumb & Actions --}}
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <div>
                <nav style="display: flex; gap: 8px; font-size: 13px; color: #64748b; margin-bottom: 8px;">
                    <a href="{{ portal_route('dashboard') }}" style="color: #64748b; text-decoration: none;">Trang chủ</a>
                    <span style="color: #cbd5e1;">/</span>
                    <a href="{{ portal_route('blocks.index') }}" style="color: #64748b; text-decoration: none;">Quản lý Hạ tầng</a>
                    <span style="color: #cbd5e1;">/</span>
                    <a href="{{ portal_route('blocks.show', $floor->block_id) }}" style="color: #64748b; text-decoration: none;">{{ $floor->block->name }}</a>
                    <span style="color: #cbd5e1;">/</span>
                    <span style="color: #0f172a; font-weight: 600;">{{ $floor->name ?? 'Tầng ' . $floor->floor_number }}</span>
                </nav>
                <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 12px;">
                    {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                    @if (($floor->status ?? 'active') === 'active')
                        <span style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 600;">Hoạt động</span>
                    @elseif(($floor->status ?? 'active') === 'maintenance')
                        <span style="background: #fef3c7; color: #b45309; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 600;">Bảo trì</span>
                    @else
                        <span style="background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 600;">Ngưng hoạt động</span>
                    @endif
                </h1>
            </div>

            <div style="display: flex; gap: 12px;">
                <a href="{{ portal_route('blocks.show', $floor->block_id) }}" style="background: white; border: 1px solid #e2e8f0; color: #475569; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='white'">← Về Tòa nhà</a>
                <a href="{{ portal_route('floors.edit', $floor) }}" style="background: #1d4ed8; border: 1px solid #1d4ed8; color: white; padding: 10px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s; display: flex; align-items: center; gap: 8px;" onmouseover="this.style.background='#1e40af'" onmouseout="this.style.background='#1d4ed8'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    Sửa tầng
                </a>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert-success">
                {{ $message }}
            </div>
        @endif

        <div class="dashboard-grid">

            {{-- Thông tin chung --}}
            <article class="dashboard-card">
                <div class="card-header">
                    <h2>Thông tin chung</h2>
                </div>

                <div class="card-body">
                    <div class="detail-row">
                        <span class="detail-label">Tòa nhà</span>
                        <span class="detail-value"
                            style="font-weight: 700; color: #082b7a;">{{ $floor->block->name }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tên tầng / Số tầng</span>
                        <span class="detail-value"
                            style="font-weight: 600;">{{ $floor->name ?? 'Tầng ' . $floor->floor_number }} (Tầng
                            {{ $floor->floor_number }})</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Loại tầng</span>
                        <span>
                            @if ($floor->floor_type === 'above_ground')
                                <span class="badge badge-success" style="background-color: #dbeafe; color: #1e40af;">Tầng nổi</span>
                            @elseif($floor->floor_type === 'basement')
                                <span class="badge badge-warning" style="background-color: #f1f5f9; color: #475569;">Tầng hầm</span>
                            @else
                                <span class="badge badge-success" style="background-color: #dbeafe; color: #1e40af;">Tầng nổi</span>
                            @endif
                        </span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Trạng thái tầng</span>
                        <span>
                            @if (($floor->status ?? 'active') === 'active')
                                <span class="badge badge-success">Hoạt động</span>
                            @elseif(($floor->status ?? 'active') === 'maintenance')
                                <span class="badge badge-warning">Bảo trì</span>
                            @else
                                <span class="badge badge-danger">Ngưng hoạt động</span>
                            @endif
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Căn hộ đã khai báo</span>
                        <span class="detail-value" style="font-weight: 700;">{{ $floor->apartments->count() }} căn</span>
                    </div>

                    <div class="detail-row" style="flex-direction: column; align-items: flex-start; gap: 8px;">
                        <span class="detail-label">Ghi chú / Mô tả</span>
                        <span class="detail-value"
                            style="color: #475569; font-weight: 400; line-height: 1.6;">{{ $floor->description ?? 'Không có mô tả chi tiết' }}</span>
                    </div>
                </div>
            </article>

            {{-- Thống kê căn hộ thuộc tầng --}}
            <article class="dashboard-card">
                <div class="card-header">
                    <h2>Thống kê phòng</h2>
                </div>

                <div class="card-body stats-container">
                    <div class="premium-stat-card">
                        <span class="premium-stat-label">Tổng phòng</span>
                        <span class="premium-stat-value">{{ $stats['total'] }}</span>
                    </div>
                    <div class="premium-stat-card">
                        <span class="premium-stat-label">Tỷ lệ lấp đầy</span>
                        <span class="premium-stat-value" style="color: #082b7a;">{{ $stats['occupancy_rate'] }}%</span>
                    </div>
                    <div class="premium-stat-card">
                        <span class="premium-stat-label">Đang ở</span>
                        <span class="premium-stat-value" style="color: #16a34a;">{{ $stats['occupied'] }}</span>
                    </div>
                    <div class="premium-stat-card">
                        <span class="premium-stat-label">Trống</span>
                        <span class="premium-stat-value" style="color: #d97706;">{{ $stats['vacant'] }}</span>
                    </div>
                    <div class="premium-stat-card" style="grid-column: span 2;">
                        <span class="premium-stat-label">Bảo trì</span>
                        <span class="premium-stat-value" style="color: #dc2626;">{{ $stats['maintenance'] }}</span>
                    </div>
                </div>
            </article>

        </div>

        {{-- Sơ đồ phòng dạng lưới --}}
        <article class="dashboard-card" style="margin-top: 28px;">
            <div class="card-header"
                style="border-bottom: 1px solid #f1f5f9; padding-bottom: 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h2>Danh sách căn hộ</h2>
                </div>
                <a href="{{ portal_route('apartments.create', ['floor_id' => $floor->id]) }}" class="btn btn-primary">
                    + Thêm căn hộ mới
                </a>
            </div>

            <div class="card-body" style="padding: 0; overflow-x: auto; border-radius: 8px;">
                @if ($floor->apartments->count() > 0)
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>Tên căn hộ</th>
                                <th>Loại căn hộ</th>
                                <th>Diện tích</th>
                                <th>Tên chủ hộ</th>
                                <th>Số điện thoại</th>
                                <th>Trạng thái</th>
                                <th style="text-align: right;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($floor->apartments as $apartment)
                                @php
                                    $type = $apartment->apartmentType->name ?? 'Chưa xác định';

                                    $owner = $apartment->residents()->where('relationship', 'owner')->first() ?? $apartment->residents()->first();
                                    $ownerName = $owner ? ($owner->user->name ?? '-') : '-';
                                    $ownerPhone = $owner ? ($owner->user->phone ?? '-') : '-';
                                @endphp
                                <tr>
                                    <td style="font-weight: 700; color: #082b7a;">
                                        <a href="{{ portal_route('apartments.show', $apartment->id) }}" style="text-decoration: none; color: inherit;">
                                            {{ $apartment->apartment_number }}
                                        </a>
                                    </td>
                                    <td>{{ $type }}</td>
                                    <td>{{ number_format($apartment->area, 0, ',', '.') }} m²</td>
                                    <td>{{ $ownerName }}</td>
                                    <td>{{ $ownerPhone }}</td>
                                    <td>
                                        <span class="badge-status badge-status--{{ $apartment->status }}">
                                            @if ($apartment->status == 'occupied')
                                                Đang ở
                                            @elseif($apartment->status == 'vacant')
                                                Trống
                                            @else
                                                Bảo trì
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons" style="justify-content: flex-end;">
                                            <a href="{{ portal_route('apartments.show', $apartment->id) }}" class="btn-action btn-action--view" title="Chi tiết">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                            <a href="{{ portal_route('apartments.edit', $apartment->id) }}" class="btn-action btn-action--edit" title="Sửa">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </a>
                                            <form action="{{ portal_route('apartments.destroy', $apartment->id) }}" method="POST" style="display:contents;">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn-action btn-action--delete delete-apt-btn" title="Xóa" style="border:none; background:none;">
                                                    <i class="fa-regular fa-trash-can" style="color: #ef4444;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 40px; color: #64748b; font-weight: 500;">
                                        Chưa có căn hộ nào được khai báo ở tầng này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <div class="empty-state"
                        style="padding: 50px; text-align: center; color: #64748b; font-size: 15px; font-weight: 500;">
                        Chưa có căn hộ nào được khai báo ở tầng này.
                    </div>
                @endif
            </div>
        </article>

    </div>

@endsection

@push('styles')
    @vite(['resources/css/pages/admin/floors/show.css'])
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-apt-btn');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Xác nhận xóa?',
                        text: "Bạn có chắc chắn muốn xóa căn hộ này không? Thao tác này không thể hoàn tác!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Đồng ý xóa',
                        cancelButtonText: 'Hủy bỏ',
                        heightAuto: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
