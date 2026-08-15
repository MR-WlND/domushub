@extends('layouts.admin.master')

@section('page_title', 'Báo cáo sự cố vận hành – DomusHub')

@push('styles')
<style>
.cr-page{max-width:1100px;}
.cr-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;}
.cr-header h1{font-size:24px;font-weight:800;color:#0f172a;}
.cr-header p{font-size:13px;color:#64748b;margin-top:2px;}
.cr-filters{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.cr-filters select{height:40px;border:1.5px solid #e2e8f0;border-radius:8px;padding:0 12px;font-size:13px;font-family:inherit;color:#334155;background:white;}
.cr-filters select:focus{outline:none;border-color:#082B7A;}
.cr-table{width:100%;border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.04);}
.cr-table th{text-align:left;padding:12px 16px;font-size:11.5px;font-weight:700;color:#64748b;text-transform:uppercase;background:#f8fafc;border-bottom:1px solid #e2e8f0;}
.cr-table td{padding:14px 16px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#334155;vertical-align:middle;}
.cr-table tr:hover td{background:#f8fafc;}
.cr-title{font-weight:700;color:#0f172a;}
.cr-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border-radius:6px;font-size:10.5px;font-weight:700;}
.cr-badge--high{background:#fee2e2;color:#dc2626;}
.cr-badge--medium{background:#fef3c7;color:#d97706;}
.cr-badge--low{background:#d1fae5;color:#059669;}
.cr-badge--pending{background:#fef3c7;color:#d97706;}
.cr-badge--processing{background:#dbeafe;color:#2563eb;}
.cr-badge--resolved{background:#d1fae5;color:#059669;}
.cr-badge--role{background:#f1f5f9;color:#475569;}
.cr-status-form select{height:32px;border:1px solid #e2e8f0;border-radius:6px;padding:0 8px;font-size:11.5px;font-family:inherit;}
.cr-imgs{display:flex;gap:4px;}
.cr-imgs img{width:36px;height:36px;border-radius:6px;object-fit:cover;border:1px solid #e2e8f0;cursor:pointer;transition:opacity .2s;}
.cr-imgs img:hover{opacity:.75;}
.alert-box{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px;background:#d1fae5;color:#065f46;border:1px solid #a7f3d0;}

/* Lightbox */
.cr-lightbox{position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.85);display:none;align-items:center;justify-content:center;padding:24px;}
.cr-lightbox.active{display:flex;}
.cr-lightbox__img{max-width:90vw;max-height:85vh;border-radius:10px;box-shadow:0 8px 32px rgba(0,0,0,.4);object-fit:contain;}
.cr-lightbox__close{position:absolute;top:16px;right:20px;width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:white;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;}
.cr-lightbox__close:hover{background:rgba(255,255,255,.3);}
.cr-lightbox__nav{position:absolute;top:50%;transform:translateY(-50%);width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,.15);border:none;color:white;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;}
.cr-lightbox__nav:hover{background:rgba(255,255,255,.3);}
.cr-lightbox__nav--prev{left:16px;}
.cr-lightbox__nav--next{right:16px;}
.cr-lightbox__counter{position:absolute;bottom:20px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,.7);font-size:13px;font-weight:600;}

/* Detail Popup */
.cr-detail-overlay{position:fixed;inset:0;z-index:9990;background:rgba(15,23,42,.5);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center;padding:24px;}
.cr-detail-overlay.active{display:flex;}
.cr-detail-popup{background:white;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.15);width:100%;max-width:580px;max-height:85vh;overflow-y:auto;animation:crPopupIn .25s ease;}
@keyframes crPopupIn{from{opacity:0;transform:translateY(12px) scale(.97);}to{opacity:1;transform:translateY(0) scale(1);}}
.cr-detail-popup__header{display:flex;align-items:flex-start;justify-content:space-between;padding:24px 24px 0;gap:12px;}
.cr-detail-popup__title{font-size:18px;font-weight:800;color:#0f172a;line-height:1.3;}
.cr-detail-popup__close{flex-shrink:0;width:32px;height:32px;border-radius:8px;border:none;background:#f1f5f9;color:#64748b;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;}
.cr-detail-popup__close:hover{background:#e2e8f0;color:#0f172a;}
.cr-detail-popup__body{padding:20px 24px 24px;}
.cr-detail-popup__meta{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;}
.cr-detail-popup__meta-item{background:#f8fafc;border-radius:10px;padding:12px 14px;}
.cr-detail-popup__meta-label{font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.3px;margin-bottom:4px;}
.cr-detail-popup__meta-value{font-size:13.5px;font-weight:600;color:#1e293b;}
.cr-detail-popup__desc{margin-bottom:20px;}
.cr-detail-popup__desc-label{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:6px;}
.cr-detail-popup__desc-text{font-size:13.5px;color:#334155;line-height:1.6;background:#f8fafc;border-radius:10px;padding:14px 16px;border:1px solid #f1f5f9;}
.cr-detail-popup__images{margin-bottom:4px;}
.cr-detail-popup__images-label{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:8px;}
.cr-detail-popup__images-grid{display:grid;grid-template-columns:repeat(auto-fill, minmax(100px, 1fr));gap:8px;}
.cr-detail-popup__images-grid img{width:100%;aspect-ratio:1;object-fit:cover;border-radius:10px;border:1px solid #e2e8f0;cursor:pointer;transition:opacity .2s;}
.cr-detail-popup__images-grid img:hover{opacity:.8;}
.cr-detail-popup__status-select{width:100%;height:36px;border:1.5px solid #e2e8f0;border-radius:8px;padding:0 10px;font-size:13px;font-weight:600;font-family:inherit;color:#1e293b;background:white;cursor:pointer;transition:border-color .2s;}
.cr-detail-popup__status-select:focus{outline:none;border-color:#082B7A;}
.cr-detail-popup__status-msg{font-size:11.5px;margin-top:6px;font-weight:600;display:none;}
.cr-detail-popup__status-msg--success{color:#059669;display:block;}
.cr-detail-popup__status-msg--error{color:#dc2626;display:block;}
</style>
@endpush

@section('content')
<div class="cr-page">
    <div class="cr-header">
        <div>
            <h1>Báo cáo sự cố vận hành</h1>
            <p>Tổng hợp sự cố từ tất cả nhân viên (vệ sinh, bảo vệ, kỹ thuật, lễ tân...)</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert-box"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div>
    @endif

    <form class="cr-filters" method="GET" action="{{ portal_route('cleaning-reports.index') }}">
        <select name="status" onchange="this.form.submit()">
            <option value="">Tất cả trạng thái</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Đã xử lý</option>
        </select>
        <select name="priority" onchange="this.form.submit()">
            <option value="">Tất cả ưu tiên</option>
            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Cao</option>
            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Trung bình</option>
            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Thấp</option>
        </select>
        <select name="role" onchange="this.form.submit()">
            <option value="">Tất cả bộ phận</option>
            <option value="cleaning" {{ request('role') == 'cleaning' ? 'selected' : '' }}>Vệ sinh</option>
            <option value="security" {{ request('role') == 'security' ? 'selected' : '' }}>Bảo vệ</option>
            <option value="technician" {{ request('role') == 'technician' ? 'selected' : '' }}>Kỹ thuật</option>
            <option value="receptionist" {{ request('role') == 'receptionist' ? 'selected' : '' }}>Lễ tân</option>
            <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Nhân viên</option>
            <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Quản lý</option>
        </select>
    </form>

    <table class="cr-table">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Người báo</th>
                <th>Bộ phận</th>
                <th>Khu vực</th>
                <th>Ưu tiên</th>
                <th>Ảnh</th>
                <th>Trạng thái</th>
                <th>Ngày</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
            @php
                $roleLabels = [
                    'cleaning' => 'Vệ sinh',
                    'security' => 'Bảo vệ',
                    'technician' => 'Kỹ thuật',
                    'receptionist' => 'Lễ tân',
                    'staff' => 'Nhân viên',
                    'manager' => 'Quản lý',
                    'admin' => 'Admin',
                ];
                $reporterRole = $report->reporter->role ?? '';
                $roleLabel = $roleLabels[$reporterRole] ?? $reporterRole;
            @endphp
            <tr class="cr-row" 
                data-id="{{ $report->id }}"
                data-title="{{ $report->title }}"
                data-reporter="{{ $report->reporter->name ?? '—' }}"
                data-role="{{ $roleLabel }}"
                data-location="{{ $report->location }}"
                data-priority="{{ $report->priority }}"
                data-status="{{ $report->status }}"
                data-description="{{ $report->description ?? 'Không có mô tả' }}"
                data-images='@json($report->images ?? [])'
                data-date="{{ $report->created_at->format('H:i d/m/Y') }}"
            >
                <td><span class="cr-title" style="cursor:pointer;text-decoration:underline;text-decoration-color:#cbd5e1;text-underline-offset:2px;">{{ $report->title }}</span></td>
                <td>{{ $report->reporter->name ?? '—' }}</td>
                <td><span class="cr-badge cr-badge--role">{{ $roleLabel }}</span></td>
                <td>{{ $report->location }}</td>
                <td><span class="cr-badge cr-badge--{{ $report->priority }}">{{ $report->priority === 'high' ? 'Cao' : ($report->priority === 'medium' ? 'TB' : 'Thấp') }}</span></td>
                <td>
                    <div class="cr-imgs">
                        @foreach(($report->images ?? []) as $img)
                        @php
                            $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
                            $isVideo = in_array($ext, ['mp4','mov','avi','webm']);
                        @endphp
                        @if($isVideo)
                        <div style="width:36px;height:36px;border-radius:6px;background:#1e293b;display:flex;align-items:center;justify-content:center;cursor:pointer;" onclick="event.stopPropagation();openMedia('{{ asset('storage/' . $img) }}','video')">
                            <i class="fa-solid fa-play" style="color:white;font-size:10px;"></i>
                        </div>
                        @else
                        <img src="{{ asset('storage/' . $img) }}" alt="evidence">
                        @endif
                        @endforeach
                        @if(empty($report->images)) — @endif
                    </div>
                </td>
                <td>
                    <form method="POST" action="{{ portal_route('cleaning-reports.update-status', $report->id) }}" class="cr-status-form">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()">
                            <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="processing" {{ $report->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Đã xử lý</option>
                        </select>
                    </form>
                </td>
                <td style="font-size:12px;color:#94a3b8;">{{ $report->created_at->format('H:i d/m') }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:32px;">Chưa có báo cáo sự cố nào.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $reports->links() }}</div>
</div>

<!-- Detail Popup -->
<div class="cr-detail-overlay" id="crDetailOverlay">
    <div class="cr-detail-popup">
        <div class="cr-detail-popup__header">
            <h2 class="cr-detail-popup__title" id="crDetailTitle"></h2>
            <button class="cr-detail-popup__close" id="crDetailClose" aria-label="Đóng">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <div class="cr-detail-popup__body">
            <div class="cr-detail-popup__meta">
                <div class="cr-detail-popup__meta-item">
                    <div class="cr-detail-popup__meta-label">Người báo cáo</div>
                    <div class="cr-detail-popup__meta-value" id="crDetailReporter"></div>
                </div>
                <div class="cr-detail-popup__meta-item">
                    <div class="cr-detail-popup__meta-label">Bộ phận</div>
                    <div class="cr-detail-popup__meta-value" id="crDetailRole"></div>
                </div>
                <div class="cr-detail-popup__meta-item">
                    <div class="cr-detail-popup__meta-label">Khu vực</div>
                    <div class="cr-detail-popup__meta-value" id="crDetailLocation"></div>
                </div>
                <div class="cr-detail-popup__meta-item">
                    <div class="cr-detail-popup__meta-label">Mức ưu tiên</div>
                    <div class="cr-detail-popup__meta-value" id="crDetailPriority"></div>
                </div>
                <div class="cr-detail-popup__meta-item">
                    <div class="cr-detail-popup__meta-label">Trạng thái</div>
                    <div class="cr-detail-popup__meta-value">
                        <select class="cr-detail-popup__status-select" id="crDetailStatusSelect">
                            <option value="pending">Chờ xử lý</option>
                            <option value="processing">Đang xử lý</option>
                            <option value="resolved">Đã xử lý</option>
                        </select>
                        <div class="cr-detail-popup__status-msg" id="crDetailStatusMsg"></div>
                    </div>
                </div>
                <div class="cr-detail-popup__meta-item">
                    <div class="cr-detail-popup__meta-label">Thời gian báo cáo</div>
                    <div class="cr-detail-popup__meta-value" id="crDetailDate"></div>
                </div>
            </div>
            <div class="cr-detail-popup__desc">
                <div class="cr-detail-popup__desc-label">Mô tả chi tiết</div>
                <div class="cr-detail-popup__desc-text" id="crDetailDesc"></div>
            </div>
            <div class="cr-detail-popup__images" id="crDetailImagesSection" style="display:none;">
                <div class="cr-detail-popup__images-label">Ảnh / Video đính kèm</div>
                <div class="cr-detail-popup__images-grid" id="crDetailImagesGrid"></div>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div class="cr-lightbox" id="crLightbox">
    <button class="cr-lightbox__close" id="crLightboxClose" aria-label="Đóng">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
    <button class="cr-lightbox__nav cr-lightbox__nav--prev" id="crLightboxPrev" aria-label="Ảnh trước">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </button>
    <button class="cr-lightbox__nav cr-lightbox__nav--next" id="crLightboxNext" aria-label="Ảnh tiếp">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
    <img class="cr-lightbox__img" id="crLightboxImg" src="" alt="Ảnh sự cố">
    <div class="cr-lightbox__counter" id="crLightboxCounter"></div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    // === Detail Popup ===
    const detailOverlay = document.getElementById('crDetailOverlay');
    const detailClose = document.getElementById('crDetailClose');
    const detailTitle = document.getElementById('crDetailTitle');
    const detailReporter = document.getElementById('crDetailReporter');
    const detailRole = document.getElementById('crDetailRole');
    const detailLocation = document.getElementById('crDetailLocation');
    const detailPriority = document.getElementById('crDetailPriority');
    const detailStatusSelect = document.getElementById('crDetailStatusSelect');
    const detailStatusMsg = document.getElementById('crDetailStatusMsg');
    const detailDate = document.getElementById('crDetailDate');
    const detailDesc = document.getElementById('crDetailDesc');
    const detailImagesSection = document.getElementById('crDetailImagesSection');
    const detailImagesGrid = document.getElementById('crDetailImagesGrid');

    const priorityMap = { high: 'Cao', medium: 'Trung bình', low: 'Thấp' };

    let currentReportId = null;
    let currentRow = null;

    document.querySelectorAll('.cr-row .cr-title').forEach(titleEl => {
        titleEl.addEventListener('click', function(e) {
            e.stopPropagation();
            const row = this.closest('.cr-row');
            currentRow = row;
            currentReportId = row.dataset.id;

            detailTitle.textContent = row.dataset.title;
            detailReporter.textContent = row.dataset.reporter;
            detailRole.textContent = row.dataset.role || '—';
            detailLocation.textContent = row.dataset.location;
            detailPriority.textContent = priorityMap[row.dataset.priority] || row.dataset.priority;
            detailStatusSelect.value = row.dataset.status;
            detailStatusMsg.className = 'cr-detail-popup__status-msg';
            detailStatusMsg.textContent = '';
            detailDate.textContent = row.dataset.date;
            detailDesc.textContent = row.dataset.description;

            // Images & Videos
            const images = JSON.parse(row.dataset.images || '[]');
            detailImagesGrid.innerHTML = '';
            if (images.length > 0) {
                detailImagesSection.style.display = 'block';
                images.forEach(img => {
                    const ext = img.split('.').pop().toLowerCase();
                    const isVideo = ['mp4','mov','avi','webm'].includes(ext);
                    const url = '/storage/' + img;

                    if (isVideo) {
                        const div = document.createElement('div');
                        div.style.cssText = 'width:100%;aspect-ratio:1;border-radius:10px;background:#1e293b;display:flex;align-items:center;justify-content:center;cursor:pointer;border:1px solid #e2e8f0;';
                        div.innerHTML = '<i class="fa-solid fa-play" style="color:white;font-size:20px;"></i>';
                        div.addEventListener('click', function() { openMedia(url, 'video'); });
                        detailImagesGrid.appendChild(div);
                    } else {
                        const imgEl = document.createElement('img');
                        imgEl.src = url;
                        imgEl.alt = 'Ảnh sự cố';
                        imgEl.addEventListener('click', function() {
                            currentImages = images.filter(i => !['mp4','mov','avi','webm'].includes(i.split('.').pop().toLowerCase())).map(i => '/storage/' + i);
                            currentIndex = currentImages.indexOf(this.src);
                            if (currentIndex < 0) currentIndex = 0;
                            showImage();
                            lightbox.classList.add('active');
                        });
                        detailImagesGrid.appendChild(imgEl);
                    }
                });
            } else {
                detailImagesSection.style.display = 'none';
            }

            detailOverlay.classList.add('active');
        });
    });

    // Update status from popup
    detailStatusSelect.addEventListener('change', function() {
        if (!currentReportId) return;
        const newStatus = this.value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="_token"]')?.value;

        detailStatusMsg.className = 'cr-detail-popup__status-msg';
        detailStatusMsg.textContent = 'Đang cập nhật...';
        detailStatusMsg.style.display = 'block';
        detailStatusMsg.style.color = '#64748b';

        fetch('/{{ request()->segment(1) }}/cleaning-reports/' + currentReportId + '/status', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status: newStatus })
        }).then(response => {
            if (response.ok || response.status === 302) {
                detailStatusMsg.className = 'cr-detail-popup__status-msg cr-detail-popup__status-msg--success';
                detailStatusMsg.textContent = '✓ Đã cập nhật trạng thái';

                // Update the row data and table select
                if (currentRow) {
                    currentRow.dataset.status = newStatus;
                    const tableSelect = currentRow.querySelector('.cr-status-form select');
                    if (tableSelect) tableSelect.value = newStatus;
                }

                setTimeout(() => {
                    detailStatusMsg.style.display = 'none';
                }, 2000);
            } else {
                throw new Error('Update failed');
            }
        }).catch(() => {
            detailStatusMsg.className = 'cr-detail-popup__status-msg cr-detail-popup__status-msg--error';
            detailStatusMsg.textContent = '✗ Lỗi cập nhật, thử lại';
        });
    });

    function closeDetail() {
        detailOverlay.classList.remove('active');
    }

    detailClose.addEventListener('click', closeDetail);
    detailOverlay.addEventListener('click', function(e) {
        if (e.target === detailOverlay) closeDetail();
    });

    // === Lightbox ===
    const lightbox = document.getElementById('crLightbox');
    const lightboxImg = document.getElementById('crLightboxImg');
    const lightboxCounter = document.getElementById('crLightboxCounter');
    const btnClose = document.getElementById('crLightboxClose');
    const btnPrev = document.getElementById('crLightboxPrev');
    const btnNext = document.getElementById('crLightboxNext');

    let currentImages = [];
    let currentIndex = 0;

    // Open lightbox when clicking on table thumbnail images
    document.querySelectorAll('.cr-imgs img').forEach(img => {
        img.addEventListener('click', function(e) {
            e.stopPropagation();
            const container = this.closest('.cr-imgs');
            currentImages = Array.from(container.querySelectorAll('img')).map(i => i.src);
            currentIndex = currentImages.indexOf(this.src);
            showImage();
            lightbox.classList.add('active');
        });
    });

    function showImage() {
        lightboxImg.src = currentImages[currentIndex];
        lightboxCounter.textContent = currentImages.length > 1
            ? (currentIndex + 1) + ' / ' + currentImages.length
            : '';
        btnPrev.style.display = currentImages.length > 1 ? 'flex' : 'none';
        btnNext.style.display = currentImages.length > 1 ? 'flex' : 'none';
    }

    function closeLightbox() {
        lightbox.classList.remove('active');
        lightboxImg.src = '';
    }

    btnClose.addEventListener('click', closeLightbox);
    btnPrev.addEventListener('click', function() {
        currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
        showImage();
    });
    btnNext.addEventListener('click', function() {
        currentIndex = (currentIndex + 1) % currentImages.length;
        showImage();
    });

    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) closeLightbox();
    });

    document.addEventListener('keydown', function(e) {
        if (lightbox.classList.contains('active')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') btnPrev.click();
            if (e.key === 'ArrowRight') btnNext.click();
        } else if (detailOverlay.classList.contains('active')) {
            if (e.key === 'Escape') closeDetail();
        }
    });

    // === Video Lightbox ===
    window.openMedia = function(url, type) {
        if (type === 'video') {
            lightboxImg.style.display = 'none';
            let videoEl = document.getElementById('crLightboxVideo');
            if (!videoEl) {
                videoEl = document.createElement('video');
                videoEl.id = 'crLightboxVideo';
                videoEl.controls = true;
                videoEl.autoplay = true;
                videoEl.style.cssText = 'max-width:90vw;max-height:85vh;border-radius:10px;box-shadow:0 8px 32px rgba(0,0,0,.4);';
                lightbox.appendChild(videoEl);
            }
            videoEl.src = url;
            videoEl.style.display = 'block';
            btnPrev.style.display = 'none';
            btnNext.style.display = 'none';
            lightboxCounter.textContent = '';
            lightbox.classList.add('active');
        } else {
            currentImages = [url];
            currentIndex = 0;
            showImage();
            lightbox.classList.add('active');
        }
    };

    const origCloseLightbox = closeLightbox;
    closeLightbox = function() {
        lightbox.classList.remove('active');
        lightboxImg.src = '';
        lightboxImg.style.display = '';
        const videoEl = document.getElementById('crLightboxVideo');
        if (videoEl) { videoEl.pause(); videoEl.style.display = 'none'; videoEl.src = ''; }
    };
    btnClose.removeEventListener('click', origCloseLightbox);
    btnClose.addEventListener('click', closeLightbox);
    lightbox.removeEventListener('click', function(){});
    lightbox.addEventListener('click', function(e) { if (e.target === lightbox) closeLightbox(); });
})();
</script>
@endpush
