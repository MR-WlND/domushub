@extends('layouts.resident.master')

@section('title', 'Chi tiết phản ánh #' . $ticket->id . ' – DomusHub')

@push('styles')
    @vite(['resources/css/resident/tickets.css'])
@endpush

@section('content')
<div class="tk">

    {{-- HEADER --}}
    <div class="tk__header {{ $ticket->status === 'completed' ? 'tk__header--completed' : '' }}">
        <div>
            <p class="tk__eyebrow">Chi tiết phản ánh #{{ $ticket->id }}</p>
            <h1 class="tk__title">{{ $ticket->title }}</h1>
        </div>

        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            @if($ticket->status === 'completed')
                <span style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#dcfce7;color:#166534;border-radius:12px;font-size:0.88rem;font-weight:700;border:1px solid #86efac;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    Đã xử lý xong
                </span>
            @endif
            <a href="{{ route('resident.tickets.index') }}"
               class="tk-btn tk-btn--outline" style="display: inline-flex; align-items: center; gap: 6px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Quay lại
            </a>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="tk-alert tk-alert--success">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="tk-alert tk-alert--error">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    {{-- BANNER: Bạn bị tố cáo trong vụ việc này --}}
    @if(isset($isAccused) && $isAccused)
        @if(!$ticket->hasAccusedResponse())
            {{-- Chưa phản hồi --}}
            <div style="background: linear-gradient(135deg, #fef2f2, #fff1f2); border: 1.5px solid #fca5a5; border-radius: 16px; padding: 20px; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <strong style="color: #dc2626; font-size: 1.05rem;">Bạn đã bị tố cáo trong vụ việc này</strong>
                </div>
                <p style="color: #991b1b; font-size: 0.88rem; margin: 0 0 16px 0; line-height: 1.6;">
                    Vui lòng xem nội dung tố cáo và bằng chứng bên dưới, sau đó phản hồi bằng cách chọn <strong>"Tôi xác nhận"</strong> hoặc <strong>"Tôi phản đối"</strong>.
                </p>

                <form method="POST" action="{{ route('resident.tickets.respond-accusation', $ticket->id) }}"
                      style="display: flex; flex-direction: column; gap: 12px;">
                    @csrf

                    <div>
                        <label style="font-weight: 600; font-size: 0.85rem; color: #374151; margin-bottom: 4px; display: block;">Lý do (không bắt buộc)</label>
                        <textarea name="accused_response_comment"
                                  style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.88rem; resize: vertical; min-height: 60px; box-sizing: border-box;"
                                  placeholder="Giải thích lý do xác nhận hoặc phản đối...">{{ old('accused_response_comment') }}</textarea>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="accused_response" value="confirmed"
                                style="flex: 1; padding: 12px; border: none; border-radius: 10px; background: #16a34a; color: #fff; font-weight: 700; font-size: 0.88rem; cursor: pointer;"
                                onclick="return confirm('Bạn xác nhận sự việc trong tố cáo là đúng?')">
                            Tôi xác nhận
                        </button>
                        <button type="submit" name="accused_response" value="denied"
                                style="flex: 1; padding: 12px; border: none; border-radius: 10px; background: #dc2626; color: #fff; font-weight: 700; font-size: 0.88rem; cursor: pointer;"
                                onclick="return confirm('Bạn phản đối tố cáo này?')">
                            Tôi phản đối
                        </button>
                    </div>
                </form>
            </div>
        @else
            {{-- Đã phản hồi --}}
            <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-radius:16px;margin-bottom:1rem;
                @if($ticket->accused_response === 'confirmed')
                    background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;
                @else
                    background:linear-gradient(135deg,#fef2f2,#fecaca);border:1.5px solid #fca5a5;
                @endif
            ">
                <div>
                    <div style="font-weight:700;font-size:0.95rem;
                        @if($ticket->accused_response === 'confirmed') color:#166534; @else color:#991b1b; @endif
                    ">
                        Bạn đã {{ $ticket->accused_response === 'confirmed' ? 'xác nhận' : 'phản đối' }} tố cáo này
                    </div>
                    @if($ticket->accused_response_comment)
                        <div style="font-size:0.82rem;color:#475569;margin-top:4px;font-style:italic;">
                            "{{ $ticket->accused_response_comment }}"
                        </div>
                    @endif
                    <div style="font-size:0.75rem;color:#94a3b8;margin-top:4px;">
                        {{ $ticket->accused_responded_at?->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- BANNER: Phản ánh đã được xử lý xong - yêu cầu đánh giá --}}
    @if($ticket->canFeedback())
        <div class="tk-completion-banner" id="completionBanner">
            <div class="tk-completion-banner__icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div class="tk-completion-banner__text">
                <div class="tk-completion-banner__title">✅ Phản ánh đã được xử lý thành công!</div>
                <div class="tk-completion-banner__sub">Hãy dành 1 phút đánh giá chất lượng phục vụ để giúp chúng tôi cải thiện dịch vụ.</div>
            </div>
            <a href="#feedbackSection" class="tk-btn" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;box-shadow:0 4px 14px rgba(245,158,11,.3);flex-shrink:0;" onclick="scrollToFeedback()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#fff" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                Đánh giá ngay
            </a>
        </div>
    @elseif($ticket->status === 'completed' && $ticket->rating)
        <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;border-radius:16px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div>
                <div style="font-weight:700;color:#166534;font-size:0.95rem;">✅ Phản ánh đã hoàn thành &amp; đã được đánh giá</div>
                <div style="font-size:0.82rem;color:#15803d;margin-top:2px;">
                    Bạn đã đánh giá {{ $ticket->rating }}/5 sao — {{ ['','Rất tệ','Chưa hài lòng','Bình thường','Hài lòng','Xuất sắc'][$ticket->rating] }}
                </div>
            </div>
        </div>
    @endif

    {{-- DETAIL GRID --}}
    <div class="tk-detail">

        {{-- MAIN COLUMN --}}
        <div class="tk-detail__main">

            {{-- TICKET CONTENT --}}
            <div class="tk-info-card">
                <h3 class="tk-info-card__title">Nội dung phản ánh</h3>

                <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 1rem;">
                    <span class="tk-badge badge--{{ $ticket->priority }}">{{ $ticket->priorityLabel() }}</span>
                    <span class="tk-badge badge--{{ $ticket->status }}">{{ $ticket->statusLabel() }}</span>
                    @if($ticket->rating)
                        <div style="display: inline-flex; align-items: center; gap: 3px; margin-left: 4px;">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="tk-star {{ $i <= $ticket->rating ? 'tk-star--filled' : 'tk-star--empty' }}" style="font-size: 1.1rem; line-height: 1;">★</span>
                            @endfor
                            <span style="font-weight: 800; color: #f59e0b; font-size: 0.85rem; margin-left: 4px;">{{ $ticket->rating }}/5</span>
                        </div>
                    @endif
                </div>

                <p style="font-size: 0.95rem; color: #334155; line-height: 1.7; margin: 0;">
                    {{ $ticket->description }}
                </p>

                @if($ticket->images && count($ticket->images) > 0)
                    @php
                        $videoExts = ['mp4', 'mov', 'avi', 'webm'];
                        $imageFiles = [];
                        $videoFiles = [];
                        foreach ($ticket->images as $file) {
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            if (in_array($ext, $videoExts)) {
                                $videoFiles[] = $file;
                            } else {
                                $imageFiles[] = $file;
                            }
                        }
                    @endphp
                    <div style="margin-top: 1.25rem;">
                        <p style="font-size: 0.82rem; color: #94a3b8; font-weight: 600; margin-bottom: 8px;">ĐÍNH KÈM ({{ count($ticket->images) }} file)</p>

                        @if(count($imageFiles) > 0)
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; margin-bottom: 10px;">
                                @foreach($imageFiles as $img)
                                    <img src="{{ asset('storage/' . $img) }}"
                                         alt="Ảnh phản ánh"
                                         style="width: 100%; height: 150px; border-radius: 10px; object-fit: cover; cursor: pointer; border: 1px solid #e2e8f0; transition: transform 0.2s;"
                                         onmouseover="this.style.transform='scale(1.03)'"
                                         onmouseout="this.style.transform='scale(1)'"
                                         onclick="openImgModal(this.src)">
                                @endforeach
                            </div>
                        @endif

                        @if(count($videoFiles) > 0)
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                @foreach($videoFiles as $vid)
                                    <video controls
                                           src="{{ asset('storage/' . $vid) }}"
                                           style="width: 100%; max-height: 350px; border-radius: 10px; border: 1px solid #e2e8f0; background: #0f172a;"
                                           preload="metadata"
                                           playsinline>
                                        Trình duyệt không hỗ trợ video.
                                    </video>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- TIMELINE --}}
            <div class="tk-info-card">
                <h3 class="tk-info-card__title">Tiến trình xử lý</h3>

                @if($ticket->progress->isEmpty())
                    <p style="color: #94a3b8; font-size: 0.9rem; text-align: center; padding: 2rem 0;">Chưa có cập nhật tiến trình.</p>
                @else
                    <div class="tk-timeline">
                        @foreach($ticket->progress as $prog)
                            <div class="tk-timeline__item">
                                <div class="tk-timeline__dot tk-timeline__dot--{{ $prog->status }}"></div>
                                <div class="tk-timeline__content">
                                    <div class="tk-timeline__status" style="color: {{ match($prog->status) {
                                        'pending' => '#d97706',
                                        'assigned' => '#7c3aed',
                                        'in_progress' => '#2563eb',
                                        'completed' => '#16a34a',
                                        'cancelled' => '#64748b',
                                        default => '#0f172a'
                                    } }}">
                                        {{ $prog->statusLabel() }}
                                    </div>
                                    @if($prog->comment)
                                        <div class="tk-timeline__comment">{{ $prog->comment }}</div>
                                    @endif
                                    <div class="tk-timeline__meta">
                                        <span>{{ $prog->updatedBy->name ?? 'Hệ thống' }}</span>
                                        <span>{{ $prog->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($prog->image_proof)
                                        <div class="tk-timeline__proof">
                                            <img src="{{ asset('storage/' . $prog->image_proof) }}"
                                                 alt="Ảnh chứng minh"
                                                 onclick="openImgModal(this.src)">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- FEEDBACK FORM (khi completed và chưa đánh giá) --}}
            @if($ticket->canFeedback())
                <div class="tk-info-card" id="feedbackSection" style="border-color: #fde68a; box-shadow: 0 4px 20px rgba(245,158,11,0.15); scroll-margin-top: 20px;">
                    <div class="tk-rating-card">
                        <div class="tk-rating-card__title">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="#f59e0b" stroke="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            Đánh giá chất lượng xử lý
                        </div>

                        <form method="POST" action="{{ route('resident.tickets.feedback', $ticket->id) }}"
                              style="display: flex; flex-direction: column; gap: 1.25rem;">
                            @csrf

                            <div>
                                <label class="tk-label">Bạn hài lòng với kỹ thuật viên? <span style="color: #ef4444;">*</span></label>
                                <div class="tk-rating" id="starRating">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                                        <label for="star{{ $i }}" title="{{ $i }} sao">★</label>
                                    @endfor
                                </div>
                                <div class="tk-rating-hint" id="ratingHint">
                                    {{ old('rating') ? ['','😞 Rất tệ','😐 Chưa hài lòng','🙂 Bình thường','😊 Hài lòng','🤩 Xuất sắc!'][old('rating')] : 'Nhấn vào sao để đánh giá...' }}
                                </div>
                            </div>

                            <div>
                                <label class="tk-label">Nhận xét thêm <span style="color: #94a3b8; font-weight: 400;">(tùy chọn)</span></label>
                                <textarea name="feedback_comment"
                                          class="tk-textarea"
                                          placeholder="Ví dụ: Kỹ thuật viên xử lý nhanh, nhiệt tình, giải thích rõ ràng..."
                                          rows="3">{{ old('feedback_comment') }}</textarea>
                            </div>

                            <div style="display: flex; justify-content: flex-end;">
                                <button type="submit" class="tk-btn tk-btn--primary" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,0.3);">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Gửi đánh giá
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- DISPLAY EXISTING FEEDBACK --}}
            @if($ticket->rating && $ticket->feedback_comment)
                <div class="tk-info-card">
                    <h3 class="tk-info-card__title">Nhận xét của bạn</h3>
                    <p style="font-size: 0.9rem; color: #475569; line-height: 1.6; margin: 0; font-style: italic;">
                        "{{ $ticket->feedback_comment }}"
                    </p>
                </div>
            @endif

        </div>

        {{-- SIDEBAR --}}
        <div class="tk-detail__sidebar">

            {{-- INFO CARD --}}
            <div class="tk-info-card">
                <h3 class="tk-info-card__title">Thông tin phản ánh</h3>

                <div class="tk-info-row">
                    <span class="tk-info-row__label">Mã phản ánh</span>
                    <span class="tk-info-row__value">#{{ $ticket->id }}</span>
                </div>

                <div class="tk-info-row">
                    <span class="tk-info-row__label">Người gửi</span>
                    <span class="tk-info-row__value">{{ $ticket->sender->name ?? 'N/A' }}</span>
                </div>

                <div class="tk-info-row">
                    <span class="tk-info-row__label">Căn hộ</span>
                    <span class="tk-info-row__value">{{ $ticket->apartment->apartment_number ?? 'N/A' }}</span>
                </div>

                <div class="tk-info-row">
                    <span class="tk-info-row__label">Tòa nhà</span>
                    <span class="tk-info-row__value">{{ $ticket->apartment->floor->block->name ?? 'N/A' }}</span>
                </div>

                <div class="tk-info-row">
                    <span class="tk-info-row__label">Ngày gửi</span>
                    <span class="tk-info-row__value">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                </div>

                @if($ticket->handler)
                    <div class="tk-info-row">
                        <span class="tk-info-row__label">Kỹ thuật viên</span>
                        <span class="tk-info-row__value">{{ $ticket->handler->name }}</span>
                    </div>
                @endif
            </div>

            {{-- CHI PHÍ PHÁT SINH --}}
            @if($ticket->costs->count() > 0)
                <div class="tk-info-card">
                    <h3 class="tk-info-card__title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        Chi phí phát sinh
                    </h3>
                    <div class="tk-cost-list-resident">
                        @foreach($ticket->costs as $cost)
                            <div class="tk-cost-row">
                                <div class="tk-cost-row__info">
                                    <span class="tk-cost-row__desc">{{ $cost->description }}</span>
                                    <span class="tk-cost-row__amount">{{ number_format($cost->amount, 0, ',', '.') }}đ</span>
                                </div>
                                @if($cost->note)
                                    <p class="tk-cost-row__note">{{ $cost->note }}</p>
                                @endif
                            </div>
                        @endforeach
                        <div class="tk-cost-row tk-cost-row--total">
                            <span>Tổng chi phí</span>
                            <strong style="color: #dc2626;">{{ number_format($ticket->costs->sum('amount'), 0, ',', '.') }}đ</strong>
                        </div>
                    </div>
                </div>
            @endif

            {{-- CANCEL BUTTON - Chỉ người gửi phản ánh mới thấy --}}
            @if($ticket->canCancelBy(auth()->id()))
                <form method="POST"
                      action="{{ route('resident.tickets.cancel', $ticket->id) }}"
                      onsubmit="return confirm('Bạn có chắc chắn muốn hủy phản ánh này? Thao tác không thể hoàn tác.')">
                    @csrf
                    <button type="submit" class="tk-btn tk-btn--danger" style="width: 100%; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>
                        Hủy phản ánh
                    </button>
                </form>
            @endif

        </div>

    </div>
</div>

{{-- Image Lightbox Modal --}}
<div id="tkImgModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; cursor:pointer;" onclick="closeImgModal()">
    <span style="position:absolute; top:20px; right:30px; color:#fff; font-size:2rem; font-weight:bold; cursor:pointer;">&times;</span>
    <img id="tkModalImg" src="" style="max-width:90%; max-height:90%; border-radius:12px; object-fit:contain;">
</div>

<script>
function openImgModal(src) {
    const modal = document.getElementById('tkImgModal');
    document.getElementById('tkModalImg').src = src;
    modal.style.display = 'flex';
}
function closeImgModal() {
    document.getElementById('tkImgModal').style.display = 'none';
}

// Rating hint text
(function() {
    const hints = ['', '😞 Rất tệ', '😐 Chưa hài lòng', '🙂 Bình thường', '😊 Hài lòng', '🤩 Xuất sắc!'];
    const hintEl = document.getElementById('ratingHint');
    if (!hintEl) return;

    // Hover effects on labels
    document.querySelectorAll('#starRating label').forEach(function(label) {
        label.addEventListener('mouseenter', function() {
            const val = parseInt(this.getAttribute('for').replace('star', ''));
            hintEl.textContent = hints[val] || '';
            hintEl.style.color = '#d97706';
        });
        label.addEventListener('mouseleave', function() {
            // Show selected value or default
            const checked = document.querySelector('#starRating input:checked');
            if (checked) {
                hintEl.textContent = hints[parseInt(checked.value)] || '';
                hintEl.style.color = '#d97706';
            } else {
                hintEl.textContent = 'Nhấn vào sao để đánh giá...';
                hintEl.style.color = '#94a3b8';
            }
        });
    });

    // On change
    document.querySelectorAll('#starRating input').forEach(function(input) {
        input.addEventListener('change', function() {
            hintEl.textContent = hints[parseInt(this.value)] || '';
            hintEl.style.color = '#d97706';
        });
    });
})();

// Cuộn xuống form đánh giá
function scrollToFeedback() {
    const section = document.getElementById('feedbackSection');
    if (section) {
        setTimeout(function() {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            // Highlight nhấp nháy
            section.style.transition = 'box-shadow 0.3s';
            section.style.boxShadow = '0 0 0 4px rgba(245, 158, 11, 0.4)';
            setTimeout(function() {
                section.style.boxShadow = '0 4px 20px rgba(245,158,11,0.15)';
            }, 1000);
        }, 100);
    }
}

// Auto-scroll khi trang mới load (nếu cần đánh giá)
@if($ticket->canFeedback())
window.addEventListener('load', function() {
    // Chỉ auto-scroll nếu không có session success (tránh cuộn lại sau khi submit)
    @if(!session('success'))
    setTimeout(function() {
        const feedback = document.getElementById('feedbackSection');
        const banner = document.getElementById('completionBanner');
        if (feedback && banner) {
            // Chỉ scroll nếu user đang ở gần đầu trang
            if (window.scrollY < 100) {
                feedback.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }, 600);
    @endif
});
@endif
</script>

@endsection
