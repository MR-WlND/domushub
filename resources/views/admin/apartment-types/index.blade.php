@extends('layouts.admin.master')

@section('page_title', 'Quản lý Loại căn hộ')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/apartments/index.css'])
@endpush

@section('content')
<div class="apartments-page">

    {{-- Header --}}
    <div class="apartments-page__header">
        <div>
            <h1>Quản lý Loại căn hộ</h1>
            <p class="apartments-page__subtitle">Cấu hình các loại căn hộ, số phòng ngủ, phòng tắm và đơn giá dịch vụ cơ bản.</p>
        </div>

        <div class="apartments-page__actions">
            <a href="{{ portal_route('apartment-types.create') }}" class="apts-button apts-button--primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Thêm loại căn hộ
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="apartments-alert apartments-alert--success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="apartments-alert apartments-alert--danger">{{ session('error') }}</div>
    @endif

    {{-- Filters & Search --}}
    <div class="apartments-filter-card">
        <form method="GET" action="{{ portal_route('apartment-types.index') }}">
            <div style="display: flex; gap: 16px; align-items: center; width: 100%;">
                <div style="flex: 1; position: relative;">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Tìm kiếm loại căn hộ theo tên hoặc mô tả..."
                        style="width: 100%; height: 42px; padding: 10px 16px; border: 1.5px solid #d9e2f2; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box;"
                    >
                </div>
                <button type="submit" class="apts-button apts-button--primary" style="height: 42px;">
                    Tìm kiếm
                </button>
                @if($search)
                    <a href="{{ portal_route('apartment-types.index') }}" class="apts-button apts-button--edit" style="height: 42px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                        Xóa lọc
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Apartment Types Table --}}
    <div class="apartments-table-card">
        <div class="apartments-table-wrap">
            <table class="apartments-table">
                <thead>
                    <tr>
                        <th>Tên loại</th>
                        <th>Thông số phòng</th>
                        <th>Đơn giá dịch vụ (đ/m²)</th>
                        <th>Số lượng căn hộ</th>
                        <th>Mô tả</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apartmentTypes as $type)
                        <tr>
                            {{-- Tên loại --}}
                            <td style="font-weight: 700; color: #0b57d0;">
                                {{ $type->name }}
                            </td>

                            {{-- Thông số phòng --}}
                            <td>
                                <span style="font-weight: 600;">{{ $type->bedroom_count }} PN / {{ $type->bathroom_count }} WC</span>
                            </td>

                            {{-- Đơn giá dịch vụ --}}
                            <td style="font-weight: 600; color: #16a34a;">
                                {{ number_format($type->base_service_fee, 0, ',', '.') }} VND
                            </td>

                            {{-- Số lượng căn hộ liên kết --}}
                            <td>
                                <span class="count-pill" style="background-color: #eff6ff; color: #0b57d0; font-weight: 700; padding: 4px 10px; border-radius: 12px; font-size: 13px;">
                                    {{ $type->apartments_count }} căn hộ
                                </span>
                            </td>

                            {{-- Mô tả --}}
                            <td style="color: #64748b; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $type->description ?? '—' }}
                            </td>

                            {{-- Thao tác --}}
                            <td class="text-right">
                                <div class="apt-table-actions">
                                    <a href="{{ portal_route('apartment-types.show', $type->id) }}" class="apt-table-btn apt-table-btn--view" title="Xem chi tiết" style="color: #64748b; border-color: #e2e8f0; background: #fff;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="{{ portal_route('apartment-types.edit', $type->id) }}" class="apt-table-btn apt-table-btn--edit" title="Chỉnh sửa">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    @if($type->apartments_count == 0)
                                        <form action="{{ portal_route('apartment-types.destroy', $type->id) }}" method="POST"
                                              style="display:contents;">
                                            @csrf @method('DELETE')
                                            <button type="button" class="apt-table-btn apt-table-btn--delete delete-type-btn" title="Xóa">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <button class="apt-table-btn" style="color: #cbd5e1; border-color: #cbd5e1; cursor: not-allowed; opacity: 0.6;" title="Đang chứa căn hộ, không thể xóa">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted" style="padding: 40px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.5" style="margin-bottom: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <br>Chưa có loại căn hộ nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($apartmentTypes->hasPages())
        <div class="apartments-pagination">
            {{ $apartmentTypes->links() }}
        </div>
    @endif

</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-type-btn');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function (e) {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Xác nhận xóa?',
                        text: "Bạn có chắc chắn muốn xóa loại căn hộ này không? Thao tác này không thể hoàn tác!",
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
