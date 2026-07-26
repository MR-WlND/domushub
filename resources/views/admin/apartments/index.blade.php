@extends('layouts.admin.master')

@section('page_title', 'Quản lý Căn hộ')
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
            <h1>Quản lý Căn hộ</h1>
            <p class="apartments-page__subtitle">Hệ thống theo dõi chi tiết tình trạng và thông tin cư dân theo đơn vị căn hộ.</p>
        </div>

        <div class="apartments-page__actions">
            <button type="button" class="apts-button apts-button--secondary" onclick="openImportModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Nhập từ Excel
            </button>
            <a href="{{ portal_route('apartments.create') }}" class="apts-button apts-button--primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Thêm căn hộ mới
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

    {{-- Stats Grid --}}
    <div class="apartments-stats-grid">
        <div class="apts-stat-card" style="border-left: 4px solid #0b57d0;">
            <span class="apts-stat-card__label">Tổng số căn hộ</span>
            <span class="apts-stat-card__value" style="color: #0b57d0;">{{ number_format($stats['total'] ?? 0) }}</span>
        </div>
        <div class="apts-stat-card" style="border-left: 4px solid #16a34a;">
            <span class="apts-stat-card__label">Đang ở (Occupied)</span>
            <span class="apts-stat-card__value" style="color: #16a34a;">{{ number_format($stats['occupied'] ?? 0) }}</span>
        </div>
        <div class="apts-stat-card" style="border-left: 4px solid #f59e0b;">
            <span class="apts-stat-card__label">Trống (Vacant)</span>
            <span class="apts-stat-card__value" style="color: #f59e0b;">{{ number_format($stats['vacant'] ?? 0) }}</span>
        </div>
        <div class="apts-stat-card" style="border-left: 4px solid #dc2626;">
            <span class="apts-stat-card__label">Đang bảo trì</span>
            <span class="apts-stat-card__value" style="color: #dc2626;">{{ number_format($stats['maintenance'] ?? 0) }}</span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="apartments-filter-card">
        <form method="GET">
            <div class="apartments-filter-grid">
                <div>
                    <label>Tòa nhà</label>
                    <select name="block_id">
                        <option value="">Tất cả</option>
                        @foreach ($blocks as $blk)
                            <option value="{{ $blk->id }}"
                                {{ request('block_id') == $blk->id ? 'selected' : '' }}>
                                {{ $blk->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Tầng</label>
                    <select name="floor_id">
                        <option value="">Tất cả</option>
                        @foreach ($floors as $fl)
                            <option value="{{ $fl->id }}"
                                    data-block-id="{{ $fl->block_id }}"
                                {{ request('floor_id') == $fl->id ? 'selected' : '' }}>
                                {{ $fl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Loại căn hộ</label>
                    <select name="apartment_type_id">
                        <option value="">Tất cả</option>
                        @foreach ($apartmentTypes as $type)
                            <option value="{{ $type->id }}"
                                {{ request('apartment_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label>Trạng thái</label>
                    <select name="status">
                        <option value="">Tất cả</option>
                        <option value="occupied"    {{ request('status') == 'occupied'    ? 'selected' : '' }}>Đang ở</option>
                        <option value="vacant"      {{ request('status') == 'vacant'      ? 'selected' : '' }}>Trống</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Đang bảo trì</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    {{-- Apartments Table --}}
    <div class="apartments-table-card">
        <div class="apartments-table-wrap">
            <table class="apartments-table">
                <thead>
                    <tr>
                        <th>Mã căn hộ</th>
                        <th>Loại</th>
                        <th>Tòa / Tầng</th>
                        <th>Diện tích</th>
                        <th>Chủ hộ</th>
                        <th>Trạng thái</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($apartments as $apartment)
                        <tr>
                            {{-- Mã căn hộ --}}
                            <td>
                                <a href="{{ portal_route('apartments.show', $apartment->id) }}" class="apt-code-link">
                                    <span class="apt-code">{{ $apartment->apartment_number }}</span>
                                </a>
                            </td>

                            {{-- Loại --}}
                            <td>
                                <span style="font-weight: 600; color: #475569;">{{ $apartment->apartmentType->name ?? '—' }}</span>
                            </td>

                            {{-- Tòa / Tầng --}}
                            <td>
                                <div class="apt-location-cell">
                                    <span class="apt-location-block">{{ $apartment->floor->block->name ?? '—' }}</span>
                                    <span class="apt-location-floor">{{ $apartment->floor->name ?? '' }}</span>
                                </div>
                            </td>

                            {{-- Diện tích --}}
                            <td>
                                <span class="apt-area">{{ number_format($apartment->area, 1, ',', '.') }} m²</span>
                            </td>

                            {{-- Chủ hộ --}}
                            <td>
                                @php
                                    $owner = $apartment->residents->firstWhere('relationship', 'owner')
                                        ?? $apartment->residents->first();
                                @endphp
                                @if($owner && $owner->user)
                                    <div class="apt-owner-cell">
                                        <span class="apt-owner-name">{{ $owner->user->name }}</span>
                                        <span class="apt-owner-phone">{{ $owner->user->phone ?? '' }}</span>
                                    </div>
                                @else
                                    <span class="apt-owner-empty">—</span>
                                @endif
                            </td>

                            {{-- Trạng thái --}}
                            <td>
                                <span class="apt-status apt-status--{{ $apartment->status }}">
                                    @if ($apartment->status == 'occupied') Đang ở
                                    @elseif($apartment->status == 'vacant') Trống
                                    @else Đang sửa chữa
                                    @endif
                                </span>
                            </td>

                            {{-- Thao tác --}}
                            <td class="text-right">
                                <div class="apt-table-actions">
                                    <a href="{{ portal_route('apartments.show', $apartment->id) }}" class="apt-table-btn apt-table-btn--view" title="Xem chi tiết">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ portal_route('apartments.edit', $apartment->id) }}" class="apt-table-btn apt-table-btn--edit" title="Chỉnh sửa">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    <form action="{{ portal_route('apartments.destroy', $apartment->id) }}" method="POST"
                                           style="display:contents;">
                                        @csrf @method('DELETE')
                                        <button type="button" class="apt-table-btn apt-table-btn--delete delete-apt-btn" title="Xóa">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding: 40px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.5" style="margin-bottom: 10px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <br>Chưa có căn hộ nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($apartments->hasPages())
        <div class="apartments-pagination">
            {{ $apartments->links() }}
        </div>
    @endif

</div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const blockFilter = document.querySelector('select[name="block_id"]');
            const floorFilter = document.querySelector('select[name="floor_id"]');
            const typeFilter = document.querySelector('select[name="apartment_type_id"]');
            const statusFilter = document.querySelector('select[name="status"]');

            const floorOptions = Array.from(floorFilter.querySelectorAll('option[data-block-id]'));

            function updateFloors(resetSelected = false) {
                const selectedBlockId = blockFilter.value;
                const currentFloorValue = floorFilter.value;

                floorFilter.innerHTML = '<option value="">Tất cả</option>';

                if (selectedBlockId) {
                    const filteredOptions = floorOptions.filter(opt => opt.getAttribute('data-block-id') === selectedBlockId);
                    filteredOptions.forEach(opt => floorFilter.appendChild(opt));
                    floorFilter.disabled = false;

                    if (!resetSelected && currentFloorValue) {
                        const selectedOpt = filteredOptions.find(opt => opt.value === currentFloorValue);
                        if (selectedOpt) floorFilter.value = currentFloorValue;
                    }
                } else {
                    floorFilter.disabled = true;
                }
            }

            if (statusFilter) statusFilter.addEventListener('change', function() { this.form.submit(); });
            if (typeFilter) typeFilter.addEventListener('change', function() { this.form.submit(); });
            if (floorFilter) floorFilter.addEventListener('change', function() { this.form.submit(); });
            if (blockFilter) {
                blockFilter.addEventListener('change', function() {
                    floorFilter.value = "";
                    updateFloors(true);
                    this.form.submit();
                });
            }

            updateFloors(false);

            // SweetAlert2 confirm for delete apartment
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
