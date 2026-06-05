@extends('layouts.admin.master')

@section('page_title', 'Quản lý Hạ tầng')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', 'admin')

@push('styles')
    @vite(['resources/css/pages/admin/blocks/index.css'])
@endpush

@section('content')

<div class="blocks-page">

    {{-- Header --}}
    <div class="blocks-page__header">
        <div>
            <h1>Quản lý Hạ tầng</h1>
            <p class="blocks-page__subtitle">Quản lý danh sách các tòa nhà, tầng và phân bổ căn hộ trong hệ thống.</p>
        </div>

        <div class="blocks-page__actions" style="display: flex; gap: 10px;">
            <button id="btn-import-csv" class="blocks-button" style="background: #eff6ff; color: #0b57d0; border: 1px solid #bfdbfe; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px; min-height: 40px; padding: 0 18px; border-radius: 10px; font-size: 14px; font-weight: 700;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Nhập từ CSV
            </button>
            <a href="{{ route('admin.blocks.create') }}" class="blocks-button blocks-button--primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Thêm tòa nhà mới
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="blocks-top-stats">
        <div class="blocks-top-stat-card" style="border-left: 4px solid #0b57d0;">
            <span class="blocks-top-stat-card__label">Tổng số tòa</span>
            <span class="blocks-top-stat-card__value" style="color: #0b57d0;">{{ $totalBlocks }}</span>
        </div>
        <div class="blocks-top-stat-card" style="border-left: 4px solid #16a34a;">
            <span class="blocks-top-stat-card__label">Tổng số tầng</span>
            <span class="blocks-top-stat-card__value" style="color: #16a34a;">{{ $totalFloors }}</span>
        </div>
        <div class="blocks-top-stat-card" style="border-left: 4px solid #4f46e5;">
            <span class="blocks-top-stat-card__label">Căn hộ</span>
            <span class="blocks-top-stat-card__value" style="color: #4f46e5;">{{ number_format($totalApartments) }}</span>
        </div>
        <div class="blocks-top-stat-card" style="border-left: 4px solid #475569;">
            <span class="blocks-top-stat-card__label">Tỷ lệ lấp đầy</span>
            <span class="blocks-top-stat-card__value" style="color: #475569;">{{ $occupancyRate }}%</span>
        </div>
    </div>

    {{-- Alerts --}}
    @if ($message = Session::get('success'))
        <div class="blocks-alert blocks-alert--success" style="margin-bottom: 20px;">{{ $message }}</div>
    @endif
    @if ($message = Session::get('error'))
        <div class="blocks-alert blocks-alert--danger" style="margin-bottom: 20px;">{!! $message !!}</div>
    @endif

    {{-- Main Layout: 2 Columns --}}
    <div class="infra-layout">

        {{-- Left Column: Featured Block --}}
        <div class="infra-featured">
            @if($featuredBlock)
                <div class="infra-featured-card">
                    
                    {{-- Featured Header --}}
                    <div class="infra-featured__header">
                        <div class="infra-featured__info">
                            <div class="infra-featured__icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h2 class="infra-featured__name">{{ $featuredBlock->name }}</h2>
                                <p class="infra-featured__desc">{{ $featuredBlock->description ?? 'Khu vực quản lý' }}</p>
                            </div>
                        </div>

                        <div class="infra-featured__actions">
                            <span class="infra-status infra-status--{{ $featuredBlock->status }}">
                                @if($featuredBlock->status == 'active') Đang vận hành
                                @elseif($featuredBlock->status == 'maintenance') Bảo trì
                                @else Ngưng hoạt động @endif
                            </span>
                            <div class="infra-dropdown-wrap" style="position: relative;">
                                <button class="infra-icon-btn infra-dropdown-toggle" title="Tùy chọn" style="background: none; border: none; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                </button>
                                <div class="infra-dropdown-menu" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 4px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 10; min-width: 200px; padding: 6px 0;">
                                    <a href="{{ route('admin.floors.create', ['block_id' => $featuredBlock->id]) }}" class="infra-dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Thêm tầng mới
                                    </a>
                                    <a href="{{ route('admin.blocks.edit', $featuredBlock) }}" class="infra-dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        Chỉnh sửa tòa nhà
                                    </a>
                                    <form action="{{ route('admin.blocks.destroy', $featuredBlock) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa tòa nhà này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="infra-dropdown-item infra-dropdown-item--danger">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Xóa tòa nhà
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Featured Stats --}}
                    <div class="infra-featured__stats">
                        <div class="infra-stat-item">
                            <span class="infra-stat-label">SỐ TẦNG</span>
                            <span class="infra-stat-value">{{ $featuredBlock->floors_count }}</span>
                        </div>
                        <div class="infra-stat-item">
                            <span class="infra-stat-label">SỐ CĂN HỘ</span>
                            <span class="infra-stat-value">{{ $featuredBlock->apartments_count }}</span>
                        </div>
                        <div class="infra-stat-item">
                            <span class="infra-stat-label">THANG MÁY</span>
                            <span class="infra-stat-value">8</span> <!-- Placeholder data for Thang máy -->
                        </div>
                    </div>

                    {{-- Featured Floors List --}}
                    <div class="infra-featured__list">
                        <table class="infra-table">
                            <thead>
                                <tr>
                                    <th>Tầng</th>
                                    <th>Số căn hộ</th>
                                    <th>Trạng thái</th>
                                    <th class="text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($featuredFloors as $floor)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.floors.show', $floor->id) }}" class="infra-table-link">
                                                {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                                            </a>
                                        </td>
                                        <td class="text-muted">{{ $floor->apartments_count }} căn</td>
                                        <td>
                                            <span class="infra-table-status infra-table-status--{{ $floor->status }}">
                                                @if($floor->status == 'active') Hoàn tất
                                                @elseif($floor->status == 'maintenance') Bảo trì
                                                @else Ngưng @endif
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <div class="infra-table-actions">
                                                <a href="{{ route('admin.floors.edit', $floor->id) }}" class="infra-table-btn infra-table-btn--edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </a>
                                                <form action="{{ route('admin.floors.destroy', $floor->id) }}" method="POST" onsubmit="return confirm('Xóa tầng này?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="infra-table-btn infra-table-btn--delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted" style="padding: 30px;">
                                            Chưa có tầng nào cho tòa nhà này
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                </div>
            @else
                <div class="infra-featured-card empty">
                    <p class="text-muted">Chưa có dữ liệu tòa nhà.</p>
                </div>
            @endif
        </div>

        {{-- Right Column: Other Blocks --}}
        <div class="infra-sidebar">
            @forelse($otherBlocks as $block)
                <div class="infra-block-card">
                    <div class="infra-block-card__top">
                        <div class="infra-block-card__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="infra-block-card__info">
                            <h3>{{ $block->name }}</h3>
                            <p>{{ Str::limit($block->description, 30) }}</p>
                        </div>
                        <span class="infra-status-bubble infra-status-bubble--{{ $block->status }}">
                            @if($block->status == 'active') Hoạt động
                            @elseif($block->status == 'maintenance') Bảo trì
                            @else Ngưng @endif
                        </span>
                    </div>

                    <div class="infra-block-card__stats">
                        <div class="infra-block-stat">
                            <span class="infra-block-stat-label">Tầng</span>
                            <span class="infra-block-stat-value">{{ $block->floors_count }}</span>
                        </div>
                        <div class="infra-block-stat">
                            <span class="infra-block-stat-label">Căn hộ</span>
                            <span class="infra-block-stat-value">{{ $block->apartments_count }}</span>
                        </div>
                    </div>

                    <a href="{{ route('admin.blocks.index', ['featured_block_id' => $block->id]) }}" class="infra-block-card__btn">
                        Chi tiết tòa nhà
                    </a>
                </div>
            @empty
                <div class="infra-block-card empty">
                    <p class="text-muted text-center">Không có tòa nhà nào khác.</p>
                </div>
            @endforelse

            {{-- Map Banner (mock) --}}
            <div class="infra-map-card">
                <div class="infra-map-overlay">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Xem vị trí trên bản đồ tổng thể
                </div>
            </div>
            
            {{-- Pagination for blocks if needed --}}
            @if($otherBlocks->hasPages())
                <div class="infra-pagination" style="margin-top: 15px;">
                    {{ $otherBlocks->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- Modal Nhập CSV --}}
    <div id="import-csv-modal" class="custom-modal" style="display: none;">
        <div class="custom-modal-backdrop"></div>
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h3>Nhập căn hộ từ file CSV</h3>
                <button type="button" class="close-modal-btn">&times;</button>
            </div>
            <form action="{{ route('admin.apartments.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="custom-modal-body">
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 15px; line-height: 1.5;">
                        Vui lòng sử dụng file mẫu CSV để điền danh sách căn hộ. Hệ thống sẽ tự động tìm kiếm hoặc khởi tạo Tòa nhà & Tầng tương ứng nếu chưa tồn tại.
                    </p>
                    
                    <div style="margin-bottom: 20px;">
                        <a href="{{ route('admin.apartments.import.template') }}" class="blocks-button" style="font-size: 13px; min-height: 36px; padding: 0 12px; display: inline-flex; align-items: center; background: #eff6ff; color: #0b57d0; border: 1px solid #bfdbfe; border-radius: 8px; text-decoration: none; font-weight: 700; gap: 4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Tải file mẫu CSV (.csv)
                        </a>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <label style="font-size: 13px; font-weight: 700; color: #475569;">Chọn file CSV từ máy tính<span style="color: #dc2626; margin-left: 2px;">*</span></label>
                        <input type="file" name="file" accept=".csv" required style="width: 100%; min-height: 44px; padding: 9px 14px; border: 1.5px solid #d9e2f2; border-radius: 10px; background: #ffffff; color: #0f172a; font-size: 14px; outline: none;">
                    </div>
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="blocks-button close-modal-btn" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; cursor: pointer; border-radius: 8px; min-height: 38px; padding: 0 16px; font-size: 14px; font-weight: 700;">Hủy bỏ</button>
                    <button type="submit" class="blocks-button blocks-button--primary" style="cursor: pointer; border-radius: 8px; min-height: 38px; padding: 0 16px; font-size: 14px; font-weight: 700; border: none;">Bắt đầu nhập</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.querySelector('.infra-dropdown-toggle');
        const menu = document.querySelector('.infra-dropdown-menu');
        
        if (toggleBtn && menu) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
            });
            
            document.addEventListener('click', function(e) {
                if (!menu.contains(e.target) && e.target !== toggleBtn) {
                    menu.style.display = 'none';
                }
            });
        }

        // Modal Nhập CSV Logic
        const btnImportCsv = document.getElementById('btn-import-csv');
        const importModal = document.getElementById('import-csv-modal');
        const closeModalBtns = document.querySelectorAll('.close-modal-btn');

        if (btnImportCsv && importModal) {
            btnImportCsv.addEventListener('click', function(e) {
                e.preventDefault();
                importModal.style.display = 'flex';
            });

            closeModalBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    importModal.style.display = 'none';
                });
            });

            const backdrop = importModal.querySelector('.custom-modal-backdrop');
            if (backdrop) {
                backdrop.addEventListener('click', function(e) {
                    e.preventDefault();
                    importModal.style.display = 'none';
                });
            }
        }
    });
</script>
@endpush