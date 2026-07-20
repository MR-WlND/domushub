@extends('layouts.admin.master')

@section('page_title', 'Lịch sử Ghi số Nước – DomusHub')
@section('user_name', auth()->user()->name ?? 'Admin')

@section('content')
<div class="db-page">

    {{-- ===================== HEADER ===================== --}}
    <div class="db-header">
        <div>
            <h1 class="db-header__title">Lịch sử Ghi số Nước</h1>
            <p class="db-header__sub">Tra cứu toàn bộ lịch sử ghi nhận chỉ số nước trong hệ thống.</p>
        </div>
        <div>
            <a href="{{ route('admin.utility-readings.index') }}" class="al-btn-filter" style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></svg>
                Chốt số tháng này
            </a>
        </div>
    </div>

    {{-- ===================== STAT CARDS ===================== --}}
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap:14px; margin-top:24px;">
        <div class="chart-card" style="padding:16px 20px; text-align:center; border-top:3px solid #00236f;">
            <div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em;">Tổng bản ghi</div>
            <div style="font-size:26px; font-weight:800; color:#00236f; margin-top:6px;">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="chart-card" style="padding:16px 20px; text-align:center; border-top:3px solid #3b82f6;">
            <div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em;">Ghi nước</div>
            <div style="font-size:26px; font-weight:800; color:#1d4ed8; margin-top:6px;">{{ number_format($stats['water']) }}</div>
        </div>
        <div class="chart-card" style="padding:16px 20px; text-align:center; border-top:3px solid #10b981;">
            <div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em;">Đã duyệt</div>
            <div style="font-size:26px; font-weight:800; color:#059669; margin-top:6px;">{{ number_format($stats['approved']) }}</div>
        </div>
        <div class="chart-card" style="padding:16px 20px; text-align:center; border-top:3px solid #f97316;">
            <div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em;">Chờ duyệt</div>
            <div style="font-size:26px; font-weight:800; color:#c2410c; margin-top:6px;">{{ number_format($stats['pending']) }}</div>
        </div>
        <div class="chart-card" style="padding:16px 20px; text-align:center; border-top:3px solid #ef4444;">
            <div style="font-size:11px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:.04em;">Từ chối</div>
            <div style="font-size:26px; font-weight:800; color:#dc2626; margin-top:6px;">{{ number_format($stats['rejected']) }}</div>
        </div>
    </div>

    {{-- ===================== BỘ LỌC ===================== --}}
    <div class="chart-card" style="margin-top: 20px;">
        <form method="GET" action="{{ request()->url() }}" class="al-filter-form" style="flex-wrap:wrap;">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="al-filter-group">
                <label class="al-filter-label">Số căn hộ</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="al-filter-input" placeholder="VD: 101, 202...">
            </div>

            @if($tab !== 'rejected')
            <div class="al-filter-group">
                <label class="al-filter-label">Trạng thái</label>
                <select name="status" class="al-filter-input">
                    <option value="">Tất cả</option>
                    <option value="approved"  {{ request('status') == 'approved'  ? 'selected' : '' }}>✅ Đã duyệt</option>
                    <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>⏳ Chờ duyệt</option>
                    <option value="rejected"  {{ request('status') == 'rejected'  ? 'selected' : '' }}>❌ Bị từ chối</option>
                </select>
            </div>
            @endif

            <div class="al-filter-group">
                <label class="al-filter-label">Tháng</label>
                <input type="number" name="month" value="{{ request('month') }}" min="1" max="12"
                       class="al-filter-input" placeholder="1–12" style="width:80px;">
            </div>

            <div class="al-filter-group">
                <label class="al-filter-label">Năm</label>
                <input type="number" name="year" value="{{ request('year', date('Y')) }}" min="2020" max="2099"
                       class="al-filter-input" placeholder="{{ date('Y') }}" style="width:90px;">
            </div>

            <div class="al-filter-group">
                <label class="al-filter-label">Từ ngày</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="al-filter-input">
            </div>

            <div class="al-filter-group">
                <label class="al-filter-label">Đến ngày</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="al-filter-input">
            </div>

            <div class="al-filter-actions">
                <button type="submit" class="al-btn-filter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Lọc
                </button>
                @if(request()->anyFilled(['search', 'type', 'status', 'month', 'year', 'date_from', 'date_to']))
                <a href="{{ route('admin.utility-logs.index', ['tab' => $tab]) }}" class="al-btn-reset">Xóa lọc</a>
                @endif
            </div>
        </form>
    </div>

    {{-- ===================== TABS ===================== --}}
    <div style="display: flex; gap: 4px; margin-top: 24px; border-bottom: 2px solid #e2e8f0; padding-bottom: 0;">
        <a href="{{ route('admin.utility-logs.index', array_merge(request()->except('page'), ['tab' => 'all'])) }}" 
           style="text-decoration: none; padding: 10px 20px; font-weight: 700; font-size: 14px; border-radius: 8px 8px 0 0; border: 1px solid transparent; margin-bottom: -2px; transition: all 0.2s; {{ $tab === 'all' ? 'background: #ffffff; border-color: #e2e8f0 #e2e8f0 #ffffff; border-bottom-color: #ffffff; color: #00236f; position: relative; z-index: 1;' : 'color: #64748b; background: transparent;' }}">
            Tất cả lịch sử ({{ number_format($stats['total']) }})
        </a>
        <a href="{{ route('admin.utility-logs.index', array_merge(request()->except('page'), ['tab' => 'rejected'])) }}" 
           style="text-decoration: none; padding: 10px 20px; font-weight: 700; font-size: 14px; border-radius: 8px 8px 0 0; border: 1px solid transparent; margin-bottom: -2px; transition: all 0.2s; {{ $tab === 'rejected' ? 'background: #ffffff; border-color: #e2e8f0 #e2e8f0 #ffffff; border-bottom-color: #ffffff; color: #dc2626; position: relative; z-index: 1;' : 'color: #64748b; background: transparent;' }}">
            Lý do từ chối ({{ number_format($stats['rejected']) }})
        </a>
    </div>

    {{-- ===================== DATA TABLE ===================== --}}
    <div class="table-card">
        <div style="padding: 10px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: flex-end; align-items: center; background: #f8fafc; gap: 8px;">
            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: #475569; cursor: pointer; user-select: none; margin: 0;">
                <input type="checkbox" id="toggleProofImages" style="width: 16px; height: 16px; accent-color: #00236f; cursor: pointer;">
                Hiện ảnh công tơ thu nhỏ
            </label>
        </div>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kỳ ghi</th>
                        <th>Căn hộ</th>
                        <th>Tòa / Tầng</th>
                        <th>Loại</th>
                        @if($tab === 'rejected')
                            <th>Người ghi</th>
                            <th>Người từ chối</th>
                            <th>Ngày từ chối</th>
                            <th>Lý do từ chối</th>
                        @else
                            <th>Chỉ số cũ</th>
                            <th>Chỉ số mới</th>
                            <th>Tiêu thụ</th>
                            <th>Người ghi</th>
                            <th>Ngày ghi</th>
                            <th style="text-align:center">Trạng thái</th>
                        @endif
                        <th style="text-align:center">Ảnh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="white-space:nowrap; font-weight:600; color:#00236f;">
                            T{{ $log->record_month }}/{{ $log->record_year }}
                        </td>
                        <td>
                            <strong style="color:#0b1c30;">{{ $log->apartment->apartment_number ?? '—' }}</strong>
                        </td>
                        <td style="font-size:12px; color:#64748b;">
                            {{ $log->apartment->floor->block->name ?? '—' }} /
                            Tầng {{ $log->apartment->floor->floor_number ?? '—' }}
                        </td>
                        <td>
                            @if($log->type === 'electricity')
                                <span class="db-badge" style="background:#fef3c7; color:#b45309; font-size:11px;">Điện</span>
                            @else
                                <span class="db-badge" style="background:#dbeafe; color:#1d4ed8; font-size:11px;">Nước</span>
                            @endif
                        </td>
                        @if($tab === 'rejected')
                            <td style="font-size:12px; color:#475569;">{{ $log->recorder->name ?? '—' }}</td>
                            <td style="font-size:12px; color:#64748b; font-weight: 500;">
                                {{ $log->rejecter->name ?? 'Kế toán viên' }}
                            </td>
                            <td style="font-size:11px; color:#94a3b8; font-weight: 400; white-space:nowrap;">
                                <div>{{ $log->updated_at ? $log->updated_at->format('d/m/Y') : '' }}</div>
                                <div>{{ $log->updated_at ? $log->updated_at->format('H:i') : '' }}</div>
                            </td>
                            <td>
                                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 6px; background-color: #fff5f5; border: 1px solid #feb2b2; color: #e53e3e; font-weight: 700; font-size: 11px;">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 10px;"></i>
                                    <span>{{ $log->reject_reason ?? 'Không rõ lý do' }}</span>
                                </div>
                            </td>
                        @else
                            <td style="color:#64748b; font-size:13px;">{{ number_format($log->old_value) }}</td>
                            <td style="font-weight:700; color:#0b1c30;">{{ number_format($log->new_value) }}</td>
                            <td>
                                <strong style="color:#059669;">{{ number_format($log->usage_amount) }}</strong>
                                <small style="color:#94a3b8;">{{ $log->type === 'electricity' ? 'kWh' : 'm³' }}</small>
                            </td>
                            <td style="font-size:12px; color:#475569;">{{ $log->recorder->name ?? '—' }}</td>
                            <td style="font-size:11px; color:#64748b; white-space:nowrap;">
                                <div>{{ $log->created_at->format('d/m/Y') }}</div>
                                <div>{{ $log->created_at->format('H:i') }}</div>
                            </td>
                            <td style="text-align:center;">
                                @if($log->status === 'approved')
                                    <span class="db-badge" style="background:#dcfce7; color:#15803d; font-size:11px;">Đã duyệt</span>
                                @elseif($log->status === 'rejected')
                                    <span class="db-badge" style="background:#fee2e2; color:#dc2626; font-size:11px;" title="{{ $log->reject_reason }}">
                                        Từ chối
                                    </span>
                                @else
                                    <span class="db-badge" style="background:#fef3c7; color:#b45309; font-size:11px;">Chờ duyệt</span>
                                @endif
                            </td>
                        @endif
                        <td style="text-align:center;">
                            @php
                                $imgs = [];
                                if ($log->image_proof) $imgs[] = asset('storage/' . $log->image_proof);
                                if ($log->images) {
                                    foreach ($log->images as $img) $imgs[] = asset('storage/' . $img);
                                }
                            @endphp
                            @if(count($imgs) > 0)
                                <span class="proof-photo-btn" data-img="{{ $imgs[0] }}" style="cursor:pointer; color:#0b57d0; display:inline-flex; align-items:center; vertical-align:middle;" title="Xem minh chứng công tơ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="vertical-align:middle;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </span>
                                <div class="proof-thumbnail-container">
                                    <div class="proof-photo-wrapper">
                                        <img src="{{ $imgs[0] }}" class="proof-thumbnail" data-img="{{ $imgs[0] }}" data-info="Phòng {{ $log->apartment->apartment_number ?? 'N/A' }} - {{ $log->type === 'electricity' ? 'Điện' : 'Nước' }}" alt="Proof">
                                    </div>
                                </div>
                            @else
                                <span style="color:#cbd5e1; font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ $tab === 'rejected' ? 10 : 11 }}" class="empty-row">Chưa có dữ liệu ghi số điện nước nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="al-pagination">
            {{ $logs->links('admin.users.pagination') }}
        </div>
    </div>

</div>

{{-- ── Proof Modal ────────────────────────────────────── --}}
<div class="util-modal-backdrop" id="proofModal">
    <div class="util-modal" style="max-width: 540px;">
        <div class="util-modal-header">
            <h3>
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:6px; color:#0b57d0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                </svg>
                Ảnh chụp công tơ minh chứng
            </h3>
            <button class="util-modal-close" onclick="closeProofModal()">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="util-modal-body" style="text-align: center; padding: 20px;">
            <img id="proofModalImg" src="" alt="Ảnh minh chứng" style="max-width: 100%; max-height: 60vh; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
        </div>
    </div>
</div>

{{-- ── Global Proof Preview tooltip ─────────────────── --}}
<div id="globalProofPreview">
    <div class="proof-hover-header"></div>
    <img src="" alt="Xem trước">
</div>

@push('styles')
    @vite(['resources/css/pages/admin/statistics.css', 'resources/css/pages/admin/dashboard.css', 'resources/css/pages/admin/activity-logs.css'])
    <style>
        .db-badge {
            display: inline-block;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 9999px;
        }
        .al-filter-form {
            flex-wrap: wrap;
        }

        /* ---- PROOF IMAGE THUMBNAIL & HOVER PREVIEW ---- */
        .proof-thumbnail-container {
            display: none;
            vertical-align: middle;
        }

        .show-proof-thumbnails .proof-thumbnail-container {
            display: inline-flex;
        }

        .show-proof-thumbnails .proof-photo-btn {
            display: none !important;
        }

        .proof-photo-wrapper {
            position: relative;
            display: inline-block;
            line-height: 0;
        }

        .proof-thumbnail {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            background-color: #f8fafc;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .proof-thumbnail:hover {
            border-color: #0b57d0;
            transform: scale(1.08);
            box-shadow: 0 4px 12px rgba(11, 87, 208, 0.18);
        }

        /* Global Floating Proof Preview */
        #globalProofPreview {
            position: fixed;
            display: none;
            z-index: 9999;
            pointer-events: none;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.12);
            width: 250px;
            box-sizing: border-box;
            opacity: 0;
            transition: opacity 0.15s ease-out, transform 0.15s ease-out;
            transform: scale(0.95);
        }

        #globalProofPreview.active {
            display: block;
            opacity: 1;
            transform: scale(1);
        }

        .proof-hover-header {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            line-height: 1.2;
            text-align: left;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 4px;
        }

        #globalProofPreview img {
            width: 100%;
            height: auto;
            max-height: 240px;
            object-fit: contain;
            border-radius: 8px;
            display: block;
            background: #f8fafc;
        }

        /* Modal Styles */
        .util-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }

        .util-modal-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .util-modal {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 580px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            transform: scale(0.95);
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            overflow: hidden;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .util-modal-backdrop.active .util-modal {
            transform: scale(1);
        }

        .util-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .util-modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #00236f;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .util-modal-close {
            background: transparent;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            padding: 4px;
            border-radius: 6px;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .util-modal-close:hover {
            background: #e2e8f0;
            color: #334155;
        }

        .util-modal-body {
            padding: 24px;
            overflow-y: auto;
            flex: 1;
        }
    </style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Proof image popup modal
    window.closeProofModal = function() {
        document.getElementById('proofModal').classList.remove('active');
    }

    const previewEl = document.getElementById('globalProofPreview');
    const previewImg = previewEl ? previewEl.querySelector('img') : null;
    const previewHeader = previewEl ? previewEl.querySelector('.proof-hover-header') : null;

    function positionPreview(target, preview) {
        const rect = target.getBoundingClientRect();
        const previewWidth = 250; // width of preview element
        const previewHeight = 280; // approximate max height including padding & header

        let left = rect.left + (rect.width / 2) - (previewWidth / 2);
        let top = rect.top - previewHeight - 10;

        // Check window boundaries
        if (left < 10) left = 10;
        if (left + previewWidth > window.innerWidth - 10) {
            left = window.innerWidth - previewWidth - 10;
        }
        if (top < 10) {
            // Show below target if it goes off top of screen
            top = rect.bottom + 10;
        }

        preview.style.left = left + 'px';
        preview.style.top = top + 'px';
    }

    document.querySelectorAll('.proof-photo-btn, .proof-thumbnail').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const imgUrl = this.dataset.img;
            if (imgUrl) {
                document.getElementById('proofModalImg').src = imgUrl;
                document.getElementById('proofModal').classList.add('active');
            }
        });

        // Add hover preview to thumbnails
        if (btn.classList.contains('proof-thumbnail') && previewEl && previewImg && previewHeader) {
            btn.addEventListener('mouseenter', function(e) {
                const imgUrl = this.dataset.img;
                const info = this.dataset.info || 'Minh chứng công tơ';
                previewImg.src = imgUrl;
                previewHeader.textContent = info;
                
                positionPreview(this, previewEl);
                previewEl.classList.add('active');
            });

            btn.addEventListener('mousemove', function(e) {
                positionPreview(this, previewEl);
            });

            btn.addEventListener('mouseleave', function() {
                previewEl.classList.remove('active');
            });
        }
    });

    // Toggle showing proof thumbnails
    const toggleProofImages = document.getElementById('toggleProofImages');
    const tableWrap = document.querySelector('.table-wrap');

    if (toggleProofImages && tableWrap) {
        // Load state from localStorage, default to true for convenience
        const showImagesState = localStorage.getItem('domushub_show_proof_images_logs');
        if (showImagesState === 'false') {
            toggleProofImages.checked = false;
            tableWrap.classList.remove('show-proof-thumbnails');
        } else {
            toggleProofImages.checked = true;
            tableWrap.classList.add('show-proof-thumbnails');
        }

        toggleProofImages.addEventListener('change', function() {
            if (this.checked) {
                tableWrap.classList.add('show-proof-thumbnails');
                localStorage.setItem('domushub_show_proof_images_logs', 'true');
            } else {
                tableWrap.classList.remove('show-proof-thumbnails');
                localStorage.setItem('domushub_show_proof_images_logs', 'false');
            }
        });
    }

    // Close modals when clicking on the backdrop
    document.querySelectorAll('.util-modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.util-modal-backdrop.active').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

/* Style overrides to prevent layout overflow */
.dashboard-main,
.dashboard-content {
    min-width: 0 !important;
}
</style>
@endpush
@endsection
