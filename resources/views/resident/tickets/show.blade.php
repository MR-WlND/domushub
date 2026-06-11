@extends('layouts.resident.master')

@section('title', 'Chi tiết phản ánh #' . $ticket->id . ' – DomusHub')

@push('styles')
    @vite(['resources/css/resident/tickets.css'])
@endpush

@section('content')
<div class="tk">

    {{-- HEADER --}}
    <div class="tk__header">
        <div>
            <p class="tk__eyebrow">Chi tiết phản ánh #{{ $ticket->id }}</p>
            <h1 class="tk__title">{{ $ticket->title }}</h1>
        </div>

        <a href="{{ route('resident.tickets.index') }}"
           class="tk-btn tk-btn--outline" style="display: inline-flex; align-items: center; gap: 6px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại
        </a>
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

    {{-- DETAIL GRID --}}
    <div class="tk-detail">

        {{-- MAIN COLUMN --}}
        <div class="tk-detail__main">

            {{-- TICKET CONTENT --}}
            <div class="tk-info-card">
                <h3 class="tk-info-card__title">Nội dung phản ánh</h3>

                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 1rem;">
                    <span class="tk-badge badge--{{ $ticket->status }}">{{ $ticket->statusLabel() }}</span>
                    <span class="tk-badge badge--{{ $ticket->priority }}">{{ $ticket->priorityLabel() }}</span>
                </div>

                <p style="font-size: 0.95rem; color: #334155; line-height: 1.7; margin: 0;">
                    {{ $ticket->description }}
                </p>

                @if($ticket->image)
                    <div style="margin-top: 1.25rem;">
                        <p style="font-size: 0.82rem; color: #94a3b8; font-weight: 600; margin-bottom: 8px;">ẢNH ĐÍNH KÈM</p>
                        <img src="{{ asset('storage/' . $ticket->image) }}"
                             alt="Ảnh phản ánh"
                             style="max-height: 300px; border-radius: 12px; object-fit: cover; cursor: pointer; border: 1px solid #e2e8f0;"
                             onclick="openImgModal(this.src)">
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
                <div class="tk-info-card">
                    <h3 class="tk-info-card__title">Đánh giá chất lượng xử lý</h3>

                    <form method="POST" action="{{ route('resident.tickets.feedback', $ticket->id) }}"
                          style="display: flex; flex-direction: column; gap: 1rem;">
                        @csrf

                        <div>
                            <label class="tk-label">Bạn hài lòng với cách xử lý? <span style="color: #ef4444;">*</span></label>
                            <div class="tk-rating">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                                    <label for="star{{ $i }}" title="{{ $i }} sao">★</label>
                                @endfor
                            </div>
                        </div>

                        <div>
                            <label class="tk-label">Nhận xét thêm (tùy chọn)</label>
                            <textarea name="feedback_comment"
                                      class="tk-textarea"
                                      placeholder="Chia sẻ thêm ý kiến của bạn..."
                                      rows="3">{{ old('feedback_comment') }}</textarea>
                        </div>

                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit" class="tk-btn tk-btn--primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Gửi đánh giá
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- DISPLAY EXISTING FEEDBACK --}}
            @if($ticket->rating)
                <div class="tk-info-card">
                    <h3 class="tk-info-card__title">Đánh giá của bạn</h3>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <div class="tk-rating-display">
                            @for($i = 1; $i <= 5; $i++)
                                <span class="tk-star {{ $i <= $ticket->rating ? 'tk-star--filled' : 'tk-star--empty' }}" style="font-size: 1.5rem;">★</span>
                            @endfor
                        </div>
                        <span style="font-size: 1.2rem; font-weight: 800; color: #f59e0b;">{{ $ticket->rating }}/5</span>
                    </div>
                    @if($ticket->feedback_comment)
                        <p style="font-size: 0.9rem; color: #475569; line-height: 1.6; margin: 0;">
                            "{{ $ticket->feedback_comment }}"
                        </p>
                    @endif
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

            {{-- CANCEL BUTTON --}}
            @if($ticket->canCancel())
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
</script>

@endsection
