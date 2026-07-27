@extends('layouts.admin.master')

@section('page_title', 'Quản lý Hạ tầng')
@section('page_kicker', 'Quản trị hệ thống')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
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

        <div class="blocks-page__actions">
            <button type="button" class="blocks-button blocks-button--secondary" onclick="openImportModal()" style="margin-right: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Nhập từ Excel
            </button>
            <a href="{{ portal_route('blocks.create') }}" class="blocks-button blocks-button--primary">
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
        <div class="blocks-alert blocks-alert--danger" style="margin-bottom: 20px;">{{ $message }}</div>
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
                                    <a href="{{ portal_route('floors.create', ['block_id' => $featuredBlock->id]) }}" class="infra-dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Thêm tầng mới
                                    </a>
                                    <a href="{{ portal_route('blocks.edit', $featuredBlock) }}" class="infra-dropdown-item">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        Chỉnh sửa tòa nhà
                                    </a>
                                    <form action="{{ portal_route('blocks.destroy', $featuredBlock) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn xóa tòa nhà này?')">
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
                                            <a href="{{ portal_route('floors.show', $floor->id) }}" class="infra-table-link">
                                                {{ $floor->name ?? 'Tầng ' . $floor->floor_number }}
                                            </a>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                                Loại: {{ $floor->floor_type_label }}
                                            </div>
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
                                                <a href="{{ portal_route('floors.edit', $floor->id) }}" class="infra-table-btn infra-table-btn--edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </a>
                                                <form action="{{ portal_route('floors.destroy', $floor->id) }}" method="POST" onsubmit="return confirm('Xóa tầng này?')">
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

                    <a href="{{ portal_route('blocks.index', ['featured_block_id' => $block->id]) }}" class="infra-block-card__btn">
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
</div>

{{-- ── Import Modal ────────────────────────────────────── --}}
<div class="util-modal-backdrop" id="importModal">
    <div class="util-modal">
        <div class="util-modal-header">
            <h3>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:6px;">
                    <path d="M12 10v6m0 0-3-3m3 3 3-3M3 17V7a2 2 0 0 1 2-2h6l2 2h6a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                Nhập Tòa/Tầng/Căn Hộ từ Excel
            </h3>
            <button class="util-modal-close" onclick="closeImportModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="util-modal-body">
            {{-- 1. Download template option --}}
            <div class="util-template-box">
                <div class="util-template-title">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;">
                        <path d="M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1m-4-4-4 4m0 0-4-4m4 4V4"/>
                    </svg>
                    Tải File Mẫu Sơ Đồ Căn Hộ
                </div>
                <div class="util-template-desc">
                    Tải file cấu trúc chuẩn để điền thông tin Tòa nhà, Tầng, Số phòng và Diện tích tương ứng.
                </div>
                <div class="util-template-select-row" style="justify-content: flex-start;">
                    <a href="{{ portal_route('apartments.import-template') }}" class="util-template-btn">
                        Tải file mẫu (.xlsx)
                    </a>
                </div>
            </div>

            {{-- 2. Upload Form --}}
            <form action="{{ portal_route('apartments.import') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <input type="file" name="csv_file" id="csv_file" accept=".xlsx,.xls" style="display: none;" onchange="handleFileSelect(this)">

                <div class="util-drag-zone" id="dropZone" onclick="document.getElementById('csv_file').click()">
                    <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M12 16v-8m0 8-4-4m4 4 4-4M3 15v3a3 3 0 0 0 3 3h12a3 3 0 0 0 3-3v-3"/>
                    </svg>
                    <div class="util-drag-text">Kéo thả file Excel vào đây hoặc <span>chọn từ máy tính</span></div>
                    <div class="util-drag-sub">Hỗ trợ định dạng .xlsx, .xls tối đa 4MB</div>
                </div>

                <div class="util-file-preview" id="filePreview">
                    <div class="util-file-info">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="color: #10b981; display:inline-block; vertical-align:middle; margin-right:4px;">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                        </svg>
                        <span id="fileNameDisplay">file_name.xlsx</span>
                    </div>
                    <span class="util-file-remove" onclick="removeSelectedFile(event)">Xóa</span>
                </div>

                <div class="util-form-actions" style="margin-top: 24px; padding-top: 18px;">
                    <button type="submit" class="blocks-button blocks-button--primary" id="btnSubmitImport" disabled style="width: 100%; justify-content: center; border: none; cursor: pointer;">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px;">
                            <path d="M5 13l4 4L19 7"/>
                        </svg>
                        Bắt đầu nhập dữ liệu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Modal toggling
    function openImportModal() {
        document.getElementById('importModal').classList.add('active');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.remove('active');
        removeSelectedFile(null);
    }

    // File preview and selection
    function handleFileSelect(input) {
        const file = input.files[0];
        if (file) {
            document.getElementById('fileNameDisplay').textContent = file.name;
            document.getElementById('filePreview').style.display = 'flex';
            document.getElementById('btnSubmitImport').disabled = false;
        }
    }

    function removeSelectedFile(e) {
        if (e) e.stopPropagation();
        document.getElementById('csv_file').value = '';
        document.getElementById('filePreview').style.display = 'none';
        document.getElementById('btnSubmitImport').disabled = true;
    }

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

        // Drag and drop events setup
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('csv_file');

        if (dropZone && fileInput) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('dragover');
                }, false);
            });

            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length) {
                    fileInput.files = files;
                    handleFileSelect(fileInput);
                }
            }, false);
        }
    });
</script>
@endpush
