@extends('layouts.admin.master')

@section('page_title', 'Báo cáo & Đánh giá')
@section('page_kicker', 'Dịch vụ cư dân')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', auth()->user()->role)

@section('content')
<div class="rpt-page">

    {{-- Header --}}
    <div class="rpt-page__header">
        <div>
            <p class="rpt-page__eyebrow">Dịch vụ cư dân</p>
            <h1 class="rpt-page__title">Báo cáo & Đánh giá</h1>
            <p class="rpt-page__subtitle">Tổng hợp kết quả xử lý sự cố và đánh giá của cư dân.</p>
        </div>
        <button class="rpt-btn rpt-btn--ghost" onclick="window.print()">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            In báo cáo
        </button>
    </div>

    {{-- ══════ SECTION: THỐNG KÊ ĐÁNH GIÁ ══════ --}}
    <div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 60%, #fde68a 100%); border: 1.5px solid #fcd34d; border-radius: 20px; padding: 1.5rem 2rem;">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 1.5rem;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="#f59e0b" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            <h2 style="font-size: 1.1rem; font-weight: 800; color: #92400e; margin: 0;">Đánh giá của cư dân</h2>
        </div>

        {{-- Stats tổng --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: white; border-radius: 14px; padding: 1rem 1.25rem; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
                <div style="font-size: 2.5rem; font-weight: 900; color: #f59e0b; line-height: 1;">
                    {{ number_format($avgRating ?? 0, 1) }}
                </div>
                <div style="display: flex; justify-content: center; gap: 2px; margin: 6px 0;">
                    @for($i = 1; $i <= 5; $i++)
                        <span style="font-size: 1.2rem; color: {{ $i <= round($avgRating ?? 0) ? '#f59e0b' : '#d1d5db' }};">★</span>
                    @endfor
                </div>
                <div style="font-size: 0.78rem; color: #92400e; font-weight: 600;">Trung bình đánh giá</div>
            </div>
            <div style="background: white; border-radius: 14px; padding: 1rem 1.25rem; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
                <div style="font-size: 2.5rem; font-weight: 900; color: #16a34a; line-height: 1;">{{ $totalRated }}</div>
                <div style="font-size: 0.78rem; color: #166534; font-weight: 600; margin-top: 6px;">Lượt đánh giá</div>
            </div>
            <div style="background: white; border-radius: 14px; padding: 1rem 1.25rem; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
                <div style="font-size: 2.5rem; font-weight: 900; color: #7c3aed; line-height: 1;">{{ $totalCompleted }}</div>
                <div style="font-size: 0.78rem; color: #5b21b6; font-weight: 600; margin-top: 6px;">Ticket hoàn thành</div>
            </div>
            <div style="background: white; border-radius: 14px; padding: 1rem 1.25rem; text-align: center; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
                <div style="font-size: 2.5rem; font-weight: 900; color: #0284c7; line-height: 1;">
                    {{ $totalCompleted > 0 ? round($totalRated / $totalCompleted * 100) : 0 }}%
                </div>
                <div style="font-size: 0.78rem; color: #075985; font-weight: 600; margin-top: 6px;">Tỷ lệ có đánh giá</div>
            </div>
        </div>

        {{-- Phân bố sao --}}
        <div style="background: white; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 1.5rem;">
            <h3 style="font-size: 0.82rem; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.08em; margin: 0 0 1rem;">Phân bố đánh giá</h3>
            @foreach(array_reverse([1,2,3,4,5], true) as $star)
                @php $data = $ratingDistribution[$star] ?? ['count' => 0, 'percent' => 0]; @endphp
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                    <span style="font-size: 0.85rem; color: #f59e0b; font-weight: 700; width: 55px; flex-shrink: 0;">
                        {{ $star }} ★
                    </span>
                    <div style="flex: 1; background: #f1f5f9; border-radius: 20px; height: 10px; overflow: hidden;">
                        <div style="width: {{ $data['percent'] }}%; height: 100%; background: linear-gradient(90deg, #f59e0b, #fbbf24); border-radius: 20px; transition: width 0.5s;"></div>
                    </div>
                    <span style="font-size: 0.82rem; color: #64748b; font-weight: 600; width: 55px; text-align: right; flex-shrink: 0;">
                        {{ $data['count'] }} ({{ $data['percent'] }}%)
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Top KTV --}}
        @if($topTechnicians->isNotEmpty())
        <div style="background: white; border-radius: 14px; padding: 1.25rem; box-shadow: 0 2px 10px rgba(0,0,0,0.06); margin-bottom: 1.5rem;">
            <h3 style="font-size: 0.82rem; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.08em; margin: 0 0 1rem;">🏆 Top kỹ thuật viên được đánh giá cao</h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @foreach($topTechnicians as $idx => $ktv)
                <div style="display: flex; align-items: center; gap: 14px; padding: 10px 14px; border-radius: 12px; background: {{ $idx === 0 ? '#fffbeb' : '#f8fafc' }}; border: 1px solid {{ $idx === 0 ? '#fde68a' : '#e2e8f0' }};">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: {{ $idx === 0 ? 'linear-gradient(135deg,#f59e0b,#d97706)' : ($idx === 1 ? 'linear-gradient(135deg,#94a3b8,#64748b)' : 'linear-gradient(135deg,#f97316,#ea580c)') }}; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 900; flex-shrink: 0;">
                        {{ $idx + 1 }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-weight: 700; color: #0f172a; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $ktv->name }}</div>
                        <div style="font-size: 0.75rem; color: #64748b;">{{ $ktv->rated_count }} đánh giá</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 4px; flex-shrink: 0;">
                        <span style="font-size: 1.2rem; color: #f59e0b;">★</span>
                        <span style="font-size: 1.1rem; font-weight: 900; color: {{ $idx === 0 ? '#d97706' : '#1e293b' }};">{{ number_format($ktv->avg_rating, 1) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Danh sách đánh giá gần đây --}}
        @if($recentRatings->isNotEmpty())
        <div style="background: white; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #f1f5f9;">
                <h3 style="font-size: 0.82rem; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.08em; margin: 0;">Đánh giá gần đây</h3>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.86rem;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 10px 16px; text-align: left; font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; white-space: nowrap;">Mã</th>
                            <th style="padding: 10px 16px; text-align: left; font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Tiêu đề</th>
                            <th style="padding: 10px 16px; text-align: left; font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; white-space: nowrap;">KTV</th>
                            <th style="padding: 10px 16px; text-align: center; font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Đánh giá</th>
                            <th style="padding: 10px 16px; text-align: left; font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Nhận xét</th>
                            <th style="padding: 10px 16px; text-align: left; font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; white-space: nowrap;">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRatings as $r)
                        <tr style="border-top: 1px solid #f1f5f9;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                            <td style="padding: 12px 16px;">
                                <a href="{{ route('admin.tickets.show', $r->id) }}" style="font-family: monospace; font-size: 0.82rem; font-weight: 800; color: #7c3aed; text-decoration: none;">#{{ $r->id }}</a>
                            </td>
                            <td style="padding: 12px 16px; color: #1e293b; font-weight: 600; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $r->title }}</td>
                            <td style="padding: 12px 16px; color: #475569; white-space: nowrap;">{{ $r->handler->name ?? '—' }}</td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <div style="display: flex; justify-content: center; gap: 2px; align-items: center;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span style="font-size: 1rem; color: {{ $i <= $r->rating ? '#f59e0b' : '#d1d5db' }};">★</span>
                                    @endfor
                                    <span style="font-size: 0.82rem; font-weight: 700; color: #f59e0b; margin-left: 4px;">{{ $r->rating }}</span>
                                </div>
                            </td>
                            <td style="padding: 12px 16px; color: #64748b; font-style: italic; font-size: 0.82rem; max-width: 200px;">
                                @if($r->feedback_comment)
                                    <span title="{{ $r->feedback_comment }}" style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                        "{{ $r->feedback_comment }}"
                                    </span>
                                @else
                                    <span style="color: #d1d5db;">—</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px; color: #94a3b8; font-size: 0.78rem; white-space: nowrap;">{{ $r->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div style="background: white; border-radius: 14px; padding: 3rem; text-align: center; color: #94a3b8; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
            <div style="font-size: 2.5rem; margin-bottom: 8px;">⭐</div>
            <p style="margin: 0; font-size: 0.9rem;">Chưa có đánh giá nào từ cư dân</p>
        </div>
        @endif
    </div>

    {{-- Stats tổng (cũ) --}}
    <div class="rpt-stats">
        <div class="rpt-stat rpt-stat--blue">
            <div class="rpt-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
            <div>
                <p class="rpt-stat__val">—</p>
                <p class="rpt-stat__lbl">Tổng báo cáo</p>
            </div>
        </div>
        <div class="rpt-stat rpt-stat--yellow">
            <div class="rpt-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <p class="rpt-stat__val">—</p>
                <p class="rpt-stat__lbl">Chờ nghiệm thu</p>
            </div>
        </div>
        <div class="rpt-stat rpt-stat--green">
            <div class="rpt-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <p class="rpt-stat__val">—</p>
                <p class="rpt-stat__lbl">Đã nghiệm thu</p>
            </div>
        </div>
        <div class="rpt-stat rpt-stat--red">
            <div class="rpt-stat__icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.57"/></svg>
            </div>
            <div>
                <p class="rpt-stat__val">—</p>
                <p class="rpt-stat__lbl">Yêu cầu làm lại</p>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="rpt-filter">
        <div class="rpt-filter__search">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Tìm mã, tiêu đề, căn hộ, KTV...">
        </div>
        <select class="rpt-filter__select">
            <option>Tất cả tòa</option>
        </select>
        <select class="rpt-filter__select">
            <option>Tất cả trạng thái</option>
            <option>Chờ nghiệm thu</option>
            <option>Đã nghiệm thu</option>
            <option>Yêu cầu làm lại</option>
        </select>
        <select class="rpt-filter__select">
            <option>Tất cả KTV</option>
        </select>
        <input type="date" class="rpt-filter__date" title="Từ ngày">
        <input type="date" class="rpt-filter__date" title="Đến ngày">
        <button class="rpt-btn rpt-btn--primary">Lọc</button>
    </div>

    {{-- ── SECTION: Chờ nghiệm thu ── --}}
    <div class="rpt-section">
        <div class="rpt-section__header rpt-section__header--yellow">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <span>Chờ nghiệm thu</span>
            <span class="rpt-section__count">0 báo cáo</span>
        </div>

        {{-- Empty state --}}
        <div class="rpt-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            <p>Chưa có báo cáo nào chờ nghiệm thu</p>
        </div>

        {{-- Khi có data, mỗi báo cáo render theo cấu trúc này --}}
        {{--
        <div class="rpt-card rpt-card--pending">
            <div class="rpt-card__left">
                <div class="rpt-card__head">
                    <span class="rpt-card__id">#ID</span>
                    <span class="rpt-badge rpt-badge--urgent">Khẩn cấp</span>
                </div>
                <h3 class="rpt-card__title">Tiêu đề sự cố</h3>
                <div class="rpt-card__meta">
                    <span>🏢 Tòa · Tầng</span>
                    <span>👤 KTV: Tên KTV</span>
                    <span>⏱ Xử lý trong: X giờ Y phút</span>
                    <span>🕐 Hoàn thành: X giờ trước</span>
                </div>

                <div class="rpt-card__report">
                    <p class="rpt-card__report-label">📋 Báo cáo của KTV</p>
                    <p class="rpt-card__report-text">Nội dung báo cáo...</p>
                </div>

                <div class="rpt-card__images">
                    <div class="rpt-card__image-group">
                        <p class="rpt-card__image-label">Trước xử lý</p>
                        <img src="..." class="rpt-card__thumb" onclick="openLightbox(this.src, 'Trước xử lý')">
                    </div>
                    <div class="rpt-card__image-group">
                        <p class="rpt-card__image-label">Sau xử lý</p>
                        <img src="..." class="rpt-card__thumb" onclick="openLightbox(this.src, 'Sau xử lý')">
                    </div>
                </div>

                <div class="rpt-card__timeline">
                    <div class="rpt-card__tl-item">
                        <span class="rpt-card__tl-dot rpt-card__tl-dot--blue"></span>
                        <span class="rpt-card__tl-lbl">Tiếp nhận</span>
                        <span class="rpt-card__tl-val">HH:mm · dd/mm/yyyy</span>
                    </div>
                    <div class="rpt-card__tl-item">
                        <span class="rpt-card__tl-dot rpt-card__tl-dot--orange"></span>
                        <span class="rpt-card__tl-lbl">Bắt đầu xử lý</span>
                        <span class="rpt-card__tl-val">HH:mm · dd/mm/yyyy</span>
                    </div>
                    <div class="rpt-card__tl-item">
                        <span class="rpt-card__tl-dot rpt-card__tl-dot--green"></span>
                        <span class="rpt-card__tl-lbl">KTV hoàn thành</span>
                        <span class="rpt-card__tl-val">HH:mm · dd/mm/yyyy</span>
                    </div>
                </div>
            </div>

            <div class="rpt-card__right">
                <button class="rpt-btn rpt-btn--success rpt-btn--full"
                        onclick="confirmApprove(ID)">
                    ✅ Nghiệm thu đạt
                </button>
                <button class="rpt-btn rpt-btn--danger rpt-btn--full"
                        onclick="openRejectModal(ID)">
                    🔄 Yêu cầu làm lại
                </button>
                <a href="#" class="rpt-btn rpt-btn--ghost rpt-btn--full">
                    Xem chi tiết ↗
                </a>
            </div>
        </div>
        --}}
    </div>

    {{-- ── SECTION: Đã nghiệm thu ── --}}
    <div class="rpt-section">
        <div class="rpt-section__header rpt-section__header--green">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>Đã nghiệm thu</span>
            <span class="rpt-section__count">0 báo cáo</span>
        </div>

        <div class="rpt-table-wrap">
            <table class="rpt-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tiêu đề sự cố</th>
                        <th>Căn hộ</th>
                        <th>KTV xử lý</th>
                        <th>Thời gian xử lý</th>
                        <th>Ngày nghiệm thu</th>
                        <th>Người nghiệm thu</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="rpt-table__empty">Chưa có báo cáo nào được nghiệm thu</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── SECTION: Yêu cầu làm lại ── --}}
    <div class="rpt-section">
        <div class="rpt-section__header rpt-section__header--red">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.57"/></svg>
            <span>Yêu cầu làm lại</span>
            <span class="rpt-section__count">0 báo cáo</span>
        </div>

        <div class="rpt-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.57"/></svg>
            <p>Không có báo cáo nào yêu cầu làm lại</p>
        </div>
    </div>

</div>

{{-- ── Modal: Xác nhận nghiệm thu đạt ── --}}
<div class="rpt-modal-overlay" id="approveOverlay">
    <div class="rpt-modal">
        <div class="rpt-modal__icon rpt-modal__icon--green">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <h3 class="rpt-modal__title">Xác nhận nghiệm thu đạt?</h3>
        <p class="rpt-modal__desc">Thao tác này sẽ đánh dấu sự cố đã xử lý xong và thông báo đến cư dân.</p>
        <div class="rpt-modal__actions">
            <button class="rpt-btn rpt-btn--ghost" onclick="closeModals()">Hủy</button>
            <button class="rpt-btn rpt-btn--success" onclick="submitApprove()">✅ Xác nhận đạt</button>
        </div>
    </div>
</div>

{{-- ── Modal: Yêu cầu làm lại ── --}}
<div class="rpt-modal-overlay" id="rejectOverlay">
    <div class="rpt-modal">
        <div class="rpt-modal__icon rpt-modal__icon--red">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.57"/></svg>
        </div>
        <h3 class="rpt-modal__title">Yêu cầu làm lại</h3>
        <p class="rpt-modal__desc">Nhập lý do để KTV biết cần sửa gì. Lý do này sẽ được gửi đến KTV phụ trách.</p>
        <div class="rpt-modal__field">
            <label class="rpt-modal__label">Lý do yêu cầu làm lại <span style="color:#ef4444">*</span></label>
            <textarea id="rejectReason" class="rpt-modal__textarea" placeholder="VD: Vẫn còn rò rỉ sau khi sửa, cần kiểm tra lại đoạn ống phía trên..." rows="4"></textarea>
            <p class="rpt-modal__error" id="rejectError" style="display:none;">Vui lòng nhập lý do trước khi gửi.</p>
        </div>
        <div class="rpt-modal__actions">
            <button class="rpt-btn rpt-btn--ghost" onclick="closeModals()">Hủy</button>
            <button class="rpt-btn rpt-btn--danger" onclick="submitReject()">🔄 Gửi yêu cầu làm lại</button>
        </div>
    </div>
</div>

{{-- ── Lightbox ── --}}
<div class="rpt-lightbox" id="lightbox" onclick="closeLightbox()">
    <div class="rpt-lightbox__inner" onclick="event.stopPropagation()">
        <div class="rpt-lightbox__header">
            <span id="lightboxCaption"></span>
            <button class="rpt-lightbox__close" onclick="closeLightbox()">×</button>
        </div>
        <img id="lightboxImg" src="" alt="">
    </div>
</div>

<script>
let pendingId = null;

// ── Approve ────────────────────────────────────────────────────────
function confirmApprove(id) {
    pendingId = id;
    document.getElementById('approveOverlay').classList.add('rpt-modal-overlay--visible');
}
function submitApprove() {
    // TODO: gọi API approve khi có data
    console.log('Approve ticket:', pendingId);
    closeModals();
}

// ── Reject ─────────────────────────────────────────────────────────
function openRejectModal(id) {
    pendingId = id;
    document.getElementById('rejectReason').value = '';
    document.getElementById('rejectError').style.display = 'none';
    document.getElementById('rejectOverlay').classList.add('rpt-modal-overlay--visible');
    setTimeout(() => document.getElementById('rejectReason').focus(), 100);
}
function submitReject() {
    const reason = document.getElementById('rejectReason').value.trim();
    if (!reason) {
        document.getElementById('rejectError').style.display = 'block';
        document.getElementById('rejectReason').focus();
        return;
    }
    // TODO: gọi API reject khi có data
    console.log('Reject ticket:', pendingId, '| Lý do:', reason);
    closeModals();
}

// ── Close modals ───────────────────────────────────────────────────
function closeModals() {
    document.getElementById('approveOverlay').classList.remove('rpt-modal-overlay--visible');
    document.getElementById('rejectOverlay').classList.remove('rpt-modal-overlay--visible');
    pendingId = null;
}

// ── Lightbox ───────────────────────────────────────────────────────
function openLightbox(src, caption) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxCaption').textContent = caption || '';
    document.getElementById('lightbox').classList.add('rpt-lightbox--visible');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('rpt-lightbox--visible');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeModals(); closeLightbox(); }
});
</script>

<style>
/* ── Page ── */
.rpt-page { max-width: 1200px; margin: 0 auto; padding: 24px 20px; display: flex; flex-direction: column; gap: 24px; }
.rpt-page__header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; }
.rpt-page__eyebrow { font-size: .75rem; text-transform: uppercase; letter-spacing: .06em; color: #64748b; font-weight: 700; margin: 0 0 4px; }
.rpt-page__title { font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
.rpt-page__subtitle { font-size: .88rem; color: #64748b; margin: 0; }

/* ── Stats ── */
.rpt-stats { display: grid; grid-template-columns: repeat(4,1fr); gap: 12px; }
.rpt-stat { background: #fff; border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.06); border: 1px solid #f1f5f9; }
.rpt-stat__icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.rpt-stat__val { font-size: 1.6rem; font-weight: 800; line-height: 1; margin: 0 0 3px; }
.rpt-stat__lbl { font-size: .72rem; font-weight: 600; color: #64748b; margin: 0; text-transform: uppercase; letter-spacing: .04em; }
.rpt-stat--blue   .rpt-stat__icon { background: #eff6ff; color: #2563eb; } .rpt-stat--blue   .rpt-stat__val { color: #2563eb; }
.rpt-stat--yellow .rpt-stat__icon { background: #fffbeb; color: #d97706; } .rpt-stat--yellow .rpt-stat__val { color: #d97706; }
.rpt-stat--green  .rpt-stat__icon { background: #f0fdf4; color: #16a34a; } .rpt-stat--green  .rpt-stat__val { color: #16a34a; }
.rpt-stat--red    .rpt-stat__icon { background: #fef2f2; color: #dc2626; } .rpt-stat--red    .rpt-stat__val { color: #dc2626; }

/* ── Filter ── */
.rpt-filter { background: #fff; border-radius: 12px; padding: 14px 18px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; box-shadow: 0 1px 3px rgba(0,0,0,.06); border: 1px solid #f1f5f9; }
.rpt-filter__search { display: flex; align-items: center; gap: 8px; flex: 1; min-width: 200px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 9px; padding: 8px 12px; }
.rpt-filter__search input { border: none; background: none; outline: none; font-size: .88rem; color: #0f172a; width: 100%; }
.rpt-filter__select, .rpt-filter__date { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 9px; font-size: .85rem; background: #f8fafc; color: #0f172a; outline: none; cursor: pointer; }

/* ── Buttons ── */
.rpt-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 18px; border-radius: 9px; font-size: .85rem; font-weight: 700; cursor: pointer; border: none; transition: all .15s; white-space: nowrap; text-decoration: none; }
.rpt-btn--full { width: 100%; }
.rpt-btn--primary { background: linear-gradient(135deg,#7c3aed,#6d28d9); color: #fff; box-shadow: 0 3px 10px rgba(124,58,237,.25); }
.rpt-btn--success { background: #16a34a; color: #fff; } .rpt-btn--success:hover { background: #15803d; }
.rpt-btn--danger  { background: #fee2e2; color: #b91c1c; } .rpt-btn--danger:hover { background: #fecaca; }
.rpt-btn--ghost   { background: #f1f5f9; color: #475569; } .rpt-btn--ghost:hover { background: #e2e8f0; }
.rpt-btn--sm { padding: 5px 12px; font-size: .78rem; }

/* ── Section ── */
.rpt-section { display: flex; flex-direction: column; gap: 12px; }
.rpt-section__header { display: flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 10px; font-size: .88rem; font-weight: 700; border-left: 4px solid; }
.rpt-section__count { margin-left: auto; font-size: .75rem; font-weight: 700; padding: 2px 10px; border-radius: 20px; }
.rpt-section__header--yellow { background: #fffbeb; color: #92400e; border-color: #f59e0b; }
.rpt-section__header--yellow .rpt-section__count { background: #fef3c7; color: #92400e; }
.rpt-section__header--green  { background: #f0fdf4; color: #166534; border-color: #22c55e; }
.rpt-section__header--green  .rpt-section__count { background: #dcfce7; color: #166534; }
.rpt-section__header--red    { background: #fef2f2; color: #991b1b; border-color: #ef4444; }
.rpt-section__header--red    .rpt-section__count { background: #fee2e2; color: #991b1b; }

/* ── Empty state ── */
.rpt-empty { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 36px 20px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; color: #94a3b8; font-size: .88rem; }

/* ── Card layout 2 cột ── */
.rpt-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,.07); display: grid; grid-template-columns: 1fr 180px; gap: 0; overflow: hidden; }
.rpt-card--pending { border-top: 3px solid #f59e0b; }
.rpt-card--redo    { border-top: 3px solid #ef4444; }

/* Left: thông tin */
.rpt-card__left { padding: 18px; display: flex; flex-direction: column; gap: 12px; border-right: 1px solid #f1f5f9; }

/* Right: actions luôn visible */
.rpt-card__right { padding: 16px 14px; display: flex; flex-direction: column; gap: 8px; justify-content: flex-start; background: #fafafa; }

.rpt-card__head  { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.rpt-card__id    { font-family: monospace; font-size: .82rem; font-weight: 800; color: #7c3aed; background: #f5f3ff; padding: 2px 8px; border-radius: 6px; }
.rpt-card__title { font-size: .95rem; font-weight: 700; color: #0f172a; margin: 0; }
.rpt-card__meta  { display: flex; gap: 10px; flex-wrap: wrap; font-size: .8rem; color: #64748b; }

.rpt-card__report { background: #f8fafc; border-radius: 9px; padding: 11px 13px; border: 1px solid #e2e8f0; }
.rpt-card__report-label { font-size: .72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .05em; margin: 0 0 5px; }
.rpt-card__report-text  { font-size: .87rem; color: #334155; line-height: 1.6; margin: 0; }

.rpt-card__reject-reason { background: #fff5f5; border-radius: 9px; padding: 11px 13px; border: 1px solid #fecaca; }

/* Thumbnails → lightbox */
.rpt-card__images { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.rpt-card__image-group { display: flex; flex-direction: column; gap: 4px; }
.rpt-card__image-label { font-size: .7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: .04em; margin: 0; }
.rpt-card__thumb { width: 100%; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; cursor: zoom-in; transition: opacity .15s; }
.rpt-card__thumb:hover { opacity: .85; }

/* Timeline */
.rpt-card__timeline { display: flex; flex-direction: column; gap: 5px; }
.rpt-card__tl-item  { display: flex; align-items: center; gap: 8px; font-size: .78rem; }
.rpt-card__tl-dot   { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.rpt-card__tl-dot--blue   { background: #3b82f6; }
.rpt-card__tl-dot--orange { background: #f97316; }
.rpt-card__tl-dot--green  { background: #22c55e; }
.rpt-card__tl-lbl { color: #64748b; font-weight: 500; width: 120px; flex-shrink: 0; }
.rpt-card__tl-val { color: #0f172a; font-weight: 600; }
.rpt-card__duration { font-size: .8rem; color: #475569; background: #f0fdf4; padding: 5px 10px; border-radius: 7px; border: 1px solid #bbf7d0; align-self: flex-start; }

/* Badges */
.rpt-badge { font-size: .7rem; font-weight: 700; padding: 2px 8px; border-radius: 5px; }
.rpt-badge--urgent  { background: #fee2e2; color: #991b1b; }
.rpt-badge--high    { background: #ffedd5; color: #c2410c; }
.rpt-badge--medium  { background: #fef9c3; color: #854d0e; }
.rpt-badge--low     { background: #f0fdf4; color: #15803d; }
.rpt-badge--pending { background: #fef3c7; color: #92400e; }
.rpt-badge--redo    { background: #fee2e2; color: #b91c1c; }

/* Table */
.rpt-table-wrap { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.06); border: 1px solid #e2e8f0; overflow-x: auto; }
.rpt-table { width: 100%; border-collapse: collapse; font-size: .86rem; }
.rpt-table th { background: #f8fafc; padding: 11px 16px; text-align: left; font-size: .72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .05em; border-bottom: 1px solid #e2e8f0; white-space: nowrap; }
.rpt-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; color: #1e293b; vertical-align: middle; }
.rpt-table tr:hover td { background: #faf5ff; }
.rpt-table__id    { font-family: monospace; font-weight: 800; color: #7c3aed; font-size: .8rem; }
.rpt-table__empty { text-align: center; color: #94a3b8; padding: 32px !important; }

/* ── Modal ── */
.rpt-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(3px); }
.rpt-modal-overlay--visible { display: flex; }
.rpt-modal { background: #fff; border-radius: 16px; padding: 28px; width: 460px; max-width: calc(100vw - 32px); box-shadow: 0 20px 60px rgba(0,0,0,.2); display: flex; flex-direction: column; gap: 14px; }
.rpt-modal__icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin: 0 auto; }
.rpt-modal__icon--green { background: #f0fdf4; color: #16a34a; }
.rpt-modal__icon--red   { background: #fef2f2; color: #dc2626; }
.rpt-modal__title { font-size: 1.1rem; font-weight: 800; color: #0f172a; text-align: center; margin: 0; }
.rpt-modal__desc  { font-size: .88rem; color: #64748b; text-align: center; margin: 0; line-height: 1.5; }
.rpt-modal__field { display: flex; flex-direction: column; gap: 6px; }
.rpt-modal__label { font-size: .85rem; font-weight: 700; color: #334155; }
.rpt-modal__textarea { padding: 10px 12px; border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: .88rem; font-family: inherit; resize: vertical; outline: none; transition: border-color .2s; }
.rpt-modal__textarea:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.1); }
.rpt-modal__error { font-size: .8rem; color: #dc2626; font-weight: 600; margin: 0; }
.rpt-modal__actions { display: flex; gap: 10px; justify-content: flex-end; padding-top: 4px; border-top: 1px solid #f1f5f9; }

/* ── Lightbox ── */
.rpt-lightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.88); z-index: 2000; align-items: center; justify-content: center; }
.rpt-lightbox--visible { display: flex; }
.rpt-lightbox__inner { display: flex; flex-direction: column; max-width: 90vw; max-height: 90vh; }
.rpt-lightbox__header { display: flex; justify-content: space-between; align-items: center; padding: 8px 4px 10px; color: #e2e8f0; font-size: .9rem; font-weight: 600; }
.rpt-lightbox__close { background: rgba(255,255,255,.15); border: none; color: #fff; width: 32px; height: 32px; border-radius: 8px; font-size: 1.2rem; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.rpt-lightbox__close:hover { background: rgba(255,255,255,.25); }
.rpt-lightbox img { max-width: 90vw; max-height: 80vh; border-radius: 10px; object-fit: contain; }

/* ── Responsive ── */
@media (max-width: 768px) {
    .rpt-stats { grid-template-columns: repeat(2,1fr); }
    .rpt-card  { grid-template-columns: 1fr; }
    .rpt-card__right { border-top: 1px solid #f1f5f9; border-right: none; flex-direction: row; flex-wrap: wrap; background: #fff; }
    .rpt-card__right .rpt-btn { flex: 1; }
}
@media (max-width: 480px) {
    .rpt-page__header { flex-direction: column; }
    .rpt-stats { grid-template-columns: repeat(2,1fr); }
}

/* ── Print ── */
@media print {
    .rpt-filter, .rpt-card__right, .rpt-page__header button { display: none !important; }
    .rpt-card { grid-template-columns: 1fr; break-inside: avoid; }
}
</style>
@endsection
