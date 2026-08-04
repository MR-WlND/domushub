@extends('layouts.admin.master')

@section('page_title', 'Quản lý Cư dân')

@push('styles')
    @vite(['resources/css/pages/admin/residents/index.css'])
@endpush

@section('content')
<div class="residents-page">

    {{-- Header --}}
    <div class="residents-page__header">
        <div>
            <h1>Quản lý Cư dân</h1>
            <p class="residents-page__subtitle">Danh sách toàn bộ cư dân đã đăng ký trong hệ thống chung cư.</p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="residents-alert residents-alert--success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="residents-alert residents-alert--danger">{{ $errors->first() }}</div>
    @endif

    {{-- Stats --}}
    <div class="residents-stats-grid" id="stats-grid">
        <div class="res-stat-card" style="border-left: 4px solid #0b57d0;">
            <span class="res-stat-card__label">Tổng cư dân</span>
            <span class="res-stat-card__value" id="stat-total" style="color: #0b57d0;">{{ number_format($stats['total']) }}</span>
        </div>
        <div class="res-stat-card" style="border-left: 4px solid #16a34a;">
            <span class="res-stat-card__label">Đang hoạt động</span>
            <span class="res-stat-card__value" id="stat-active" style="color: #16a34a;">{{ number_format($stats['active']) }}</span>
        </div>
        <div class="res-stat-card" style="border-left: 4px solid #f59e0b;">
            <span class="res-stat-card__label">Ngừng hoạt động</span>
            <span class="res-stat-card__value" id="stat-inactive" style="color: #f59e0b;">{{ number_format($stats['inactive']) }}</span>
        </div>
        <div class="res-stat-card" style="border-left: 4px solid #dc2626;">
            <span class="res-stat-card__label">Bị khóa</span>
            <span class="res-stat-card__value" id="stat-banned" style="color: #dc2626;">{{ number_format($stats['banned']) }}</span>
        </div>
    </div>

    {{-- Filters --}}
    <div class="residents-filter-card">
        <form id="resident-filter-form">
            <div class="residents-filter-grid">
                <div>
                    <label>Tòa nhà</label>
                    <select name="block_id" id="filter-block">
                        <option value="">Tất cả tòa</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Tầng</label>
                    <select name="floor_id" id="filter-floor">
                        <option value="">Tất cả tầng</option>
                        @foreach($floors as $floor)
                            <option value="{{ $floor->id }}" data-block="{{ $floor->block_id }}" {{ request('floor_id') == $floor->id ? 'selected' : '' }}>
                                {{ $floor->display_name }} ({{ $floor->block->name ?? '—' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Tìm kiếm</label>
                    <input type="text" name="search" id="filter-search" value="{{ request('search') }}" placeholder="Tên, SĐT, email, CCCD, căn hộ...">
                </div>
            </div>
            <div class="mt-2.5 hidden" id="clear-filter-wrap">
                <a href="#" id="clear-filter-btn" class="text-xs text-red-600 no-underline font-semibold">× Xóa bộ lọc</a>
            </div>
        </form>
    </div>

    {{-- Table Container (AJAX) --}}
    <div id="resident-table-container">
        @include('admin.residents._table', ['residents' => $residents])
    </div>

</div>

<script>
const RESIDENT_INDEX_URL = '{{ portal_route('residents.index') }}';
let filterTimeout = null;

function getFilterParams() {
    const params = new URLSearchParams();
    const blockId = document.getElementById('filter-block').value;
    const floorId = document.getElementById('filter-floor').value;
    const search = document.getElementById('filter-search').value.trim();

    if (blockId) params.set('block_id', blockId);
    if (floorId) params.set('floor_id', floorId);
    if (search) params.set('search', search);

    return params;
}

function hasActiveFilters() {
    return document.getElementById('filter-block').value ||
           document.getElementById('filter-floor').value ||
           document.getElementById('filter-search').value.trim();
}

function toggleClearButton() {
    document.getElementById('clear-filter-wrap').classList.toggle('hidden', !hasActiveFilters());
}

async function fetchResidents() {
    const params = getFilterParams();
    const container = document.getElementById('resident-table-container');

    container.style.opacity = '0.5';
    container.style.pointerEvents = 'none';

    try {
        const url = RESIDENT_INDEX_URL + (params.toString() ? '?' + params.toString() : '');
        const res = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.success) {
            container.innerHTML = data.html;
            if (data.stats) {
                document.getElementById('stat-total').textContent = Number(data.stats.total).toLocaleString();
                document.getElementById('stat-active').textContent = Number(data.stats.active).toLocaleString();
                document.getElementById('stat-inactive').textContent = Number(data.stats.inactive).toLocaleString();
                document.getElementById('stat-banned').textContent = Number(data.stats.banned).toLocaleString();
            }
        }
    } catch (err) {
        console.error('Filter error:', err);
    } finally {
        container.style.opacity = '1';
        container.style.pointerEvents = '';
    }

    toggleClearButton();
    const newUrl = RESIDENT_INDEX_URL + (params.toString() ? '?' + params.toString() : '');
    window.history.replaceState({}, '', newUrl);
}

function filterFloorOptions() {
    const blockId = document.getElementById('filter-block').value;
    const floorSelect = document.getElementById('filter-floor');
    const floorOptions = floorSelect.querySelectorAll('option');
    
    let selectedFloorOption = floorSelect.options[floorSelect.selectedIndex];
    let selectedFloorBlockId = selectedFloorOption ? selectedFloorOption.getAttribute('data-block') : null;
    
    if (blockId && selectedFloorBlockId && selectedFloorBlockId !== blockId) {
        floorSelect.value = "";
    }
    
    floorOptions.forEach(option => {
        const optionBlockId = option.getAttribute('data-block');
        if (!optionBlockId) {
            // "Tất cả tầng" option
            option.style.display = '';
            return;
        }
        if (!blockId || optionBlockId === blockId) {
            option.style.display = '';
            option.disabled = false;
        } else {
            option.style.display = 'none';
            option.disabled = true;
        }
    });
}

// Select filters: immediate
document.getElementById('filter-block').addEventListener('change', function() {
    filterFloorOptions();
    fetchResidents();
});
document.getElementById('filter-floor').addEventListener('change', fetchResidents);

// Search: debounce
document.getElementById('filter-search').addEventListener('input', function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(fetchResidents, 400);
});

// Clear
document.getElementById('clear-filter-btn').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('filter-block').value = '';
    document.getElementById('filter-floor').value = '';
    document.getElementById('filter-search').value = '';
    filterFloorOptions();
    fetchResidents();
});

// Pagination via AJAX
document.getElementById('resident-table-container').addEventListener('click', async function(e) {
    const link = e.target.closest('.residents-pagination a');
    if (!link) return;
    e.preventDefault();

    const container = document.getElementById('resident-table-container');
    container.style.opacity = '0.5';
    container.style.pointerEvents = 'none';

    try {
        const res = await fetch(link.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.success) {
            container.innerHTML = data.html;
            if (data.stats) {
                document.getElementById('stat-total').textContent = Number(data.stats.total).toLocaleString();
                document.getElementById('stat-active').textContent = Number(data.stats.active).toLocaleString();
                document.getElementById('stat-inactive').textContent = Number(data.stats.inactive).toLocaleString();
                document.getElementById('stat-banned').textContent = Number(data.stats.banned).toLocaleString();
            }
        }
    } catch (err) {
        console.error('Pagination error:', err);
    } finally {
        container.style.opacity = '1';
        container.style.pointerEvents = '';
    }

    window.history.replaceState({}, '', link.href);
});

filterFloorOptions();
toggleClearButton();
</script>
@endsection
