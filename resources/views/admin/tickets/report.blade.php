@extends('layouts.admin.master')

@section('page_title', 'Báo cáo & Đánh giá')
@section('page_kicker', 'Dịch vụ cư dân')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
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
                                <a href="{{ portal_route('tickets.show', $r->id) }}" style="font-family: monospace; font-size: 0.82rem; font-weight: 800; color: #7c3aed; text-decoration: none;">#{{ $r->id }}</a>
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
        <div class="rpt-stat rpt-stat--neutral">
            <div class="rpt-stat__icon"></div>
            <div>
                <p class="rpt-stat__val">{{ number_format($totalReports) }}</p>
                <p class="rpt-stat__lbl">Tổng báo cáo</p>
            </div>
        </div>
        <div class="rpt-stat rpt-stat--neutral">
            <div class="rpt-stat__icon"></div>
            <div>
                <p class="rpt-stat__val">{{ number_format($pendingReview->count()) }}</p>
                <p class="rpt-stat__lbl">Chờ nghiệm thu</p>
            </div>
        </div>
        <div class="rpt-stat rpt-stat--neutral">
            <div class="rpt-stat__icon"></div>
            <div>
                <p class="rpt-stat__val">{{ number_format($approvedReports->count()) }}</p>
                <p class="rpt-stat__lbl">Đã nghiệm thu</p>
            </div>
        </div>
        <div class="rpt-stat rpt-stat--neutral">
            <div class="rpt-stat__icon"></div>
            <div>
                <p class="rpt-stat__val">{{ number_format($reworkReports->count()) }}</p>
                <p class="rpt-stat__lbl">Yêu cầu làm lại</p>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ portal_route('tickets.report') }}" class="rpt-filter">
        <div class="rpt-filter__search">
            <input type="text" name="search" placeholder="Tìm mã, tiêu đề, căn hộ, KTV..." value="{{ request('search') }}">
        </div>
        <select name="block_id" class="rpt-filter__select">
            <option value="">Tất cả tòa</option>
            @foreach($blocks as $block)
                <option value="{{ $block->id }}" {{ request('block_id') == $block->id ? 'selected' : '' }}>{{ $block->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rpt-filter__select">
            <option value="">Tất cả trạng thái</option>
            <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Chờ nghiệm thu</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Đã nghiệm thu</option>
            <option value="rework" {{ request('status') === 'rework' ? 'selected' : '' }}>Yêu cầu làm lại</option>
        </select>
        <select name="technician_id" class="rpt-filter__select">
            <option value="">Tất cả KTV</option>
            @foreach($technicians as $tech)
                <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
            @endforeach
        </select>
        <input type="date" name="from" class="rpt-filter__date" title="Từ ngày" value="{{ request('from') }}">
        <input type="date" name="to" class="rpt-filter__date" title="Đến ngày" value="{{ request('to') }}">
        <button class="rpt-btn rpt-btn--primary">Lọc</button>
    </form>

    {{-- ── SECTION: Chờ nghiệm thu ── --}}
    <div class="rpt-section">
        <div class="rpt-section__header rpt-section__header--neutral">
            <span>Chờ nghiệm thu</span>
            <span class="rpt-section__count">{{ $pendingReview->count() }} báo cáo</span>
        </div>

        @if($pendingReview->isEmpty())
            <div class="rpt-empty">
                <p>Chưa có báo cáo nào chờ nghiệm thu</p>
            </div>
        @else
            <div class="rpt-table-wrap">
                <table class="rpt-table">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Tiêu đề sự cố</th>
                            <th>Căn hộ</th>
                            <th>KTV xử lý</th>
                            <th>Ngày hoàn thành</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                            @foreach($pendingReview as $ticket)
                            @php
                                $lastProgress = $ticket->progress->last();
                                $reportText = $lastProgress?->comment ?? 'Không có báo cáo chi tiết.';
                                $proofImages = !empty($lastProgress?->image_proof) && is_array($lastProgress->image_proof) ? $lastProgress->image_proof : [];
                            @endphp
                            <!-- Dòng chính -->
                            <tr class="rpt-table__main-row" data-ticket-id="{{ $ticket->id }}">
                                <td class="rpt-table__id">#{{ $ticket->id }}</td>
                                <td>
                                    <span style="font-weight: 700;">{{ Str::limit($ticket->title, 40) }}</span>
                                    @if($ticket->priority === 'urgent')
                                        <span class="rpt-badge rpt-badge--urgent" style="margin-left: 6px;">Khẩn cấp</span>
                                    @elseif($ticket->priority === 'high')
                                        <span class="rpt-badge rpt-badge--high" style="margin-left: 6px;">Cao</span>
                                    @endif
                                </td>
                                <td>{{ $ticket->apartment?->apartment_number ?? 'N/A' }}</td>
                                <td>{{ $ticket->handler?->name ?? 'N/A' }}</td>
                                <td>{{ $lastProgress?->created_at?->format('d/m/Y H:i') ?? $ticket->updated_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button class="rpt-btn rpt-btn--ghost rpt-btn--sm btn-toggle-detail" onclick="toggleTableDetail({{ $ticket->id }})">
                                        Chi tiết
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Dòng chi tiết inline -->
                            <tr class="rpt-table__detail-row" id="detail-row-{{ $ticket->id }}" style="display: none;">
                                <td colspan="6" class="rpt-table__detail-cell">
                                    <div class="rpt-table__detail-content">
                                        <div class="rpt-table__detail-grid">
                                            {{-- Left Side: Report content --}}
                                            <div class="rpt-table__detail-left">
                                                <div class="rpt-table__detail-meta">
                                                    <span>Tòa nhà: <strong>{{ $ticket->apartment?->floor?->block?->name ?? 'Chưa có tòa' }}</strong></span>
                                                    <span>Tầng: <strong>{{ $ticket->apartment?->floor?->floor_number ?? 'N/A' }}</strong></span>
                                                </div>
                                                
                                                <div class="rpt-table__detail-body">
                                                    <div class="rpt-table__detail-report">
                                                        <div class="rpt-table__detail-report-body">
                                                            <p class="rpt-table__detail-label">Báo cáo của KTV</p>
                                                            <p class="rpt-table__detail-text">{{ $reportText }}</p>
                                                        </div>

                                                        @if(count($proofImages) > 0)
                                                            <div class="rpt-table__detail-image-box">
                                                                <p class="rpt-table__detail-label rpt-table__detail-label--thumb">Ảnh</p>
                                                                @foreach($proofImages as $idx => $img)
                                                                    <img data-src="{{ asset('storage/' . $img) }}" src="" class="rpt-table__detail-thumb rpt-table__detail-thumb--lazy" onclick="openLightbox(this.src, 'Ảnh nghiệm thu')" style="{{ $idx > 0 ? 'display:none;' : '' }}">
                                                                    @if($idx === 0 && count($proofImages) > 1)
                                                                        <div style="position:absolute; bottom:5px; right:5px; background:rgba(0,0,0,0.6); color:#fff; font-size:0.7rem; padding:2px 6px; border-radius:10px; pointer-events:none;">+{{ count($proofImages)-1 }}</div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Right Side: Actions --}}
                                            <div class="rpt-table__detail-right">
                                                <button class="rpt-btn rpt-btn--success rpt-btn--full" data-approve-ticket="{{ $ticket->id }}">
                                                    Xác nhận nghiệm thu
                                                </button>
                                                <button class="rpt-btn rpt-btn--danger rpt-btn--full" data-reject-ticket="{{ $ticket->id }}">
                                                    Yêu cầu làm lại
                                                </button>
                                                <a href="{{ portal_route('tickets.show', $ticket->id) }}" class="rpt-btn rpt-btn--ghost rpt-btn--full">
                                                    Xem chi tiết
                                                </a>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── SECTION: Đã nghiệm thu ── --}}
    <div class="rpt-section">
        <div class="rpt-section__header rpt-section__header--neutral">
            <span>Đã nghiệm thu</span>
            <span class="rpt-section__count">{{ $approvedReports->count() }} báo cáo</span>
        </div>

        <div class="rpt-table-wrap">
            <table class="rpt-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tiêu đề sự cố</th>
                        <th>Căn hộ</th>
                        <th>KTV xử lý</th>
                        <th>Ngày nghiệm thu</th>
                        <th>Người nghiệm thu</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($approvedReports as $ticket)
                        @php
                            $lastProgress = $ticket->progress->last();
                        @endphp
                        <tr>
                            <td>#{{ $ticket->id }}</td>
                            <td>{{ Str::limit($ticket->title, 40) }}</td>
                            <td>{{ $ticket->apartment?->apartment_number ?? 'N/A' }}</td>
                            <td>{{ $ticket->handler?->name ?? 'N/A' }}</td>
                            <td>{{ optional($lastProgress?->created_at)->format('d/m/Y H:i') }}</td>
                            <td>{{ $lastProgress?->updatedBy?->name ?? auth()->user()->name }}</td>
                            <td><a href="{{ portal_route('tickets.show', $ticket->id) }}" class="rpt-btn rpt-btn--ghost rpt-btn--sm">Chi tiết</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="rpt-table__empty">Chưa có báo cáo nào được nghiệm thu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── SECTION: Yêu cầu làm lại ── --}}
    <div class="rpt-section">
        <div class="rpt-section__header rpt-section__header--neutral">
            <span>Yêu cầu làm lại</span>
            <span class="rpt-section__count">{{ $reworkReports->count() }} báo cáo</span>
        </div>

        @if($reworkReports->isEmpty())
            <div class="rpt-empty">
                <p>Không có báo cáo nào yêu cầu làm lại</p>
            </div>
        @else
            <div class="rpt-table-wrap">
                <table class="rpt-table">
                    <thead>
                        <tr>
                            <th>Mã</th>
                            <th>Tiêu đề</th>
                            <th>Căn hộ</th>
                            <th>KTV</th>
                            <th>Lần yêu cầu</th>
                            <th>Ghi chú yêu cầu</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reworkReports as $ticket)
                            @php $lastProgress = $ticket->progress->last(); @endphp
                            <tr>
                                <td>#{{ $ticket->id }}</td>
                                <td>{{ Str::limit($ticket->title, 40) }}</td>
                                <td>{{ $ticket->apartment?->apartment_number ?? 'N/A' }}</td>
                                <td>{{ $ticket->handler?->name ?? 'N/A' }}</td>
                                <td>{{ $ticket->reopened_count }}</td>
                                <td>{{ Str::limit($lastProgress?->comment ?? 'Không có lý do.', 80) }}</td>
                                <td><a href="{{ portal_route('tickets.show', $ticket->id) }}" class="rpt-btn rpt-btn--ghost rpt-btn--sm">Chi tiết</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

{{-- ── Modal: Xác nhận nghiệm thu đạt ── --}}
<div class="rpt-modal-overlay" id="approveOverlay">
    <div class="rpt-modal">
        <div class="rpt-modal__icon rpt-modal__icon--green"></div>
        <h3 class="rpt-modal__title">Xác nhận nghiệm thu đạt?</h3>
        <p class="rpt-modal__desc">Thao tác này sẽ đánh dấu sự cố đã xử lý xong và thông báo đến cư dân.</p>
        <div class="rpt-modal__actions">
            <button class="rpt-btn rpt-btn--ghost" onclick="closeModals()">Hủy</button>
            <button class="rpt-btn rpt-btn--success" onclick="submitApprove()">Xác nhận đạt</button>
        </div>
    </div>
</div>

{{-- ── Modal: Yêu cầu làm lại ── --}}
<div class="rpt-modal-overlay" id="rejectOverlay">
    <div class="rpt-modal">
        <div class="rpt-modal__icon rpt-modal__icon--red"></div>
        <h3 class="rpt-modal__title">Yêu cầu làm lại</h3>
        <p class="rpt-modal__desc">Nhập lý do để KTV biết cần sửa gì. Lý do này sẽ được gửi đến KTV phụ trách.</p>
        <div class="rpt-modal__field">
            <label class="rpt-modal__label">Lý do yêu cầu làm lại <span style="color:#ef4444">*</span></label>
            <textarea id="rejectReason" class="rpt-modal__textarea" placeholder="VD: Vẫn còn rò rỉ sau khi sửa, cần kiểm tra lại đoạn ống phía trên..." rows="4"></textarea>
            <p class="rpt-modal__error" id="rejectError" style="display:none;">Vui lòng nhập lý do trước khi gửi.</p>
        </div>
        <div class="rpt-modal__actions">
            <button class="rpt-btn rpt-btn--ghost" onclick="closeModals()">Hủy</button>
            <button class="rpt-btn rpt-btn--danger" onclick="submitReject()">Gửi yêu cầu làm lại</button>
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

// ── Toggle Accordion Details ─────────────────────────────────────────
function toggleTableDetail(id) {
    const row = document.getElementById(`detail-row-${id}`);
    if (!row) return;

    const isHidden = row.style.display === 'none' || row.style.display === '';
    row.style.display = isHidden ? 'table-row' : 'none';

    // Lazy load the proof image if it exists
    const lazyImg = row.querySelector('.rpt-table__detail-thumb--lazy');
    if (lazyImg && lazyImg.getAttribute('data-src')) {
        lazyImg.setAttribute('src', lazyImg.getAttribute('data-src'));
        lazyImg.removeAttribute('data-src');
    }
}

// Backward compatibility (if other templates call the old name)
function toggleReportDetails(id) {
    toggleTableDetail(id);
}

// Ensure print mode loads all images
window.addEventListener('beforeprint', () => {
    document.querySelectorAll('.rpt-card__thumb--lazy').forEach(img => {
        if (img.getAttribute('data-src')) {
            img.setAttribute('src', img.getAttribute('data-src'));
            img.removeAttribute('data-src');
        }
    });
});

// ── Approve ────────────────────────────────────────────────────────
function confirmApprove(id) {
    pendingId = id;
    document.getElementById('approveOverlay').classList.add('rpt-modal-overlay--visible');
}
function submitApprove() {
    if (!pendingId) {
        closeModals();
        return;
    }
    const url = `/admin/tickets/${pendingId}/review/approve`;
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({}),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Không thể xác nhận nghiệm thu.');
            closeModals();
        }
    })
    .catch(() => {
        alert('Có lỗi khi gửi yêu cầu nghiệm thu. Vui lòng thử lại.');
        closeModals();
    });
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

    const url = `/admin/tickets/${pendingId}/review/reject`;
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ reject_reason: reason }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Không thể gửi yêu cầu làm lại.');
            closeModals();
        }
    })
    .catch(() => {
        alert('Có lỗi khi gửi yêu cầu làm lại. Vui lòng thử lại.');
        closeModals();
    });
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

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-approve-ticket]').forEach(el => {
        el.addEventListener('click', () => {
            confirmApprove(el.getAttribute('data-approve-ticket'));
        });
    });

    document.querySelectorAll('[data-reject-ticket]').forEach(el => {
        el.addEventListener('click', () => {
            openRejectModal(el.getAttribute('data-reject-ticket'));
        });
    });
});

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
.rpt-stat--neutral { border-color: #e2e8f0; }
.rpt-stat--neutral .rpt-stat__icon { background: #f8fafc; color: #64748b; }
.rpt-stat--neutral .rpt-stat__val { color: #111827; }

/* ── Filter ── */
.rpt-filter { background: #fff; border-radius: 12px; padding: 14px 18px; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; box-shadow: 0 1px 3px rgba(0,0,0,.06); border: 1px solid #f1f5f9; }
.rpt-filter__search { display: flex; align-items: center; gap: 8px; flex: 1; min-width: 200px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 9px; padding: 8px 12px; }
.rpt-filter__search input { border: none; background: none; outline: none; font-size: .88rem; color: #0f172a; width: 100%; }
.rpt-filter__select, .rpt-filter__date { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 9px; font-size: .85rem; background: #f8fafc; color: #0f172a; outline: none; cursor: pointer; }

/* ── Buttons ── */
.rpt-btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 18px; border-radius: 9px; font-size: .85rem; font-weight: 700; cursor: pointer; border: none; transition: all .15s; white-space: nowrap; text-decoration: none; }
.rpt-btn--full { width: 100%; }
.rpt-btn--primary { background: #1f2937; color: #fff; box-shadow: 0 8px 20px rgba(15,23,42,.08); }
.rpt-btn--primary:hover { background: #111827; }
.rpt-btn--success { background: #0f5132; color: #fff; } .rpt-btn--success:hover { background: #0d452d; }
.rpt-btn--danger  { background: #7f1d1d; color: #fff; } .rpt-btn--danger:hover { background: #6b1818; }
.rpt-btn--ghost   { background: #f8fafc; color: #334155; } .rpt-btn--ghost:hover { background: #e2e8f0; }
.rpt-btn--sm { padding: 5px 12px; font-size: .78rem; }

/* ── Section ── */
.rpt-section { display: flex; flex-direction: column; gap: 12px; }
.rpt-section__header { display: flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 10px; font-size: .88rem; font-weight: 700; border-left: 4px solid; }
.rpt-section__count { margin-left: auto; font-size: .75rem; font-weight: 700; padding: 2px 10px; border-radius: 20px; }
.rpt-section__header--neutral { background: #f8fafc; color: #1f2937; border-color: #cbd5e1; }
.rpt-section__header--neutral .rpt-section__count { background: #eef2ff; color: #1f2937; }

/* ── Empty state ── */
.rpt-empty { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 36px 20px; background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; color: #94a3b8; font-size: .88rem; }

/* ── Bảng chi tiết inline ── */
.rpt-table__main-row {
    transition: background-color 0.15s ease;
}
.rpt-table__main-row:hover td {
    background-color: #faf5ff !important;
}

.rpt-table__detail-row td {
    padding: 0 !important;
    background-color: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0;
}

.rpt-table__detail-content {
    padding: 20px 24px;
    background-color: #f8fafc;
    border-top: 1px solid #f1f5f9;
}

.rpt-table__detail-grid {
    display: grid;
    grid-template-columns: 1fr 200px;
    gap: 24px;
}

.rpt-table__detail-left {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.rpt-table__detail-meta {
    display: flex;
    gap: 16px;
    font-size: 0.8rem;
    color: #64748b;
}

.rpt-table__detail-body {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}

.rpt-table__detail-report {
    flex: 1;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 9px;
    padding: 14px 16px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    position: relative;
    min-height: 96px;
}

.rpt-table__detail-report-body {
    padding-right: 92px; /* leave space for the corner thumbnail */
}

.rpt-table__detail-label--thumb {
    margin: 0 0 4px;
    display: none; /* keep the corner clean */
}


.rpt-table__detail-label {
    font-size: .7rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin: 0 0 6px;
}

.rpt-table__detail-text {
    font-size: .88rem;
    color: #334155;
    line-height: 1.6;
    margin: 0;
}

/* Small square image box */
.rpt-table__detail-image-box {
    position: absolute;
    top: 12px;
    right: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex-shrink: 0;
    width: 60px;
}


.rpt-table__detail-thumb {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    cursor: zoom-in;
    transition: transform 0.2s, opacity 0.15s;
}

.rpt-table__detail-thumb:hover {
    opacity: 0.85;
    transform: scale(1.02);
}

.rpt-table__detail-right {
    display: flex;
    flex-direction: column;
    gap: 10px;
    justify-content: flex-start;
}

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
    .rpt-card__body-grid { grid-template-columns: 1fr; }
    .rpt-card__right { border-top: 1px solid #f1f5f9; border-right: none; flex-direction: row; flex-wrap: wrap; background: #fff; }
    .rpt-card__right .rpt-btn { flex: 1; }
    
    .rpt-card__header-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    .rpt-card__header-right {
        width: 100%;
        justify-content: space-between;
    }
    .rpt-card__meta-collapsed {
        flex-wrap: wrap;
        gap: 8px;
    }
}
@media (max-width: 480px) {
    .rpt-page__header { flex-direction: column; }
    .rpt-stats { grid-template-columns: repeat(2,1fr); }
}

/* ── Print ── */
@media print {
    .rpt-filter, .rpt-card__right, .rpt-page__header button { display: none !important; }
    .rpt-card { grid-template-columns: 1fr; break-inside: avoid; }
    .rpt-card__collapse-content { max-height: none !important; opacity: 1 !important; display: block !important; }
    .rpt-card__body-grid { grid-template-columns: 1fr !important; }
    .rpt-card__toggle-icon { display: none !important; }
}
</style>
@endsection
