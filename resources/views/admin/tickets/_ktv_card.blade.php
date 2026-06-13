{{-- Partial: _ktv_card.blade.php --}}
{{-- Variables: $ticket (Ticket), $mode (new | active | done) --}}

@php
    $ageHours = $ticket->created_at->diffInHours(now());
    $slaLimit = match($ticket->priority) {
        'urgent' => 2, 'high' => 8, 'medium' => 24, default => 72
    };
    $overdue = $ageHours >= $slaLimit && $mode !== 'done';
    $lastProgress = $ticket->progress?->last();
@endphp

<div class="ktv-card ktv-card--{{ $mode }} {{ $overdue ? 'ktv-card--overdue' : '' }}" id="card-{{ $ticket->id }}">

    {{-- Priority bar --}}
    <div class="ktv-card__bar ktv-card__bar--{{ $ticket->priority }}"></div>

    <div class="ktv-card__inner">

        {{-- Top row --}}
        <div class="ktv-card__top">
            <span class="ktv-card__id">#{{ $ticket->id }}</span>
            <span class="tk-priority tk-priority--{{ $ticket->priority }}">{{ $ticket->priorityLabel() }}</span>
            @if($overdue)
                <span class="ktv-card__sla">⚠ Trễ SLA</span>
            @endif
        </div>

        {{-- Title --}}
        <h3 class="ktv-card__title">{{ $ticket->title }}</h3>
        <p class="ktv-card__desc">{{ Str::limit($ticket->description, 80) }}</p>

        {{-- Meta --}}
        <div class="ktv-card__meta">
            <div class="ktv-card__meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>
                    {{ $ticket->apartment->apartment_number ?? 'N/A' }}
                    @if($ticket->apartment?->floor?->block)
                        · {{ $ticket->apartment->floor->block->name }}
                    @endif
                </span>
            </div>
            <div class="ktv-card__meta-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>{{ $ticket->created_at->diffForHumans() }}</span>
            </div>
            @if($mode !== 'done' && $ticket->sender)
                <div class="ktv-card__meta-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>{{ $ticket->sender->name }}</span>
                </div>
            @endif
        </div>

        {{-- Last progress (for active tasks) --}}
        @if($mode === 'active' && $lastProgress?->comment)
            <div class="ktv-card__last-update">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span>{{ Str::limit($lastProgress->comment, 60) }}</span>
            </div>
        @endif

        {{-- Done: show completion date --}}
        @if($mode === 'done')
            <div class="ktv-card__done-tag">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Hoàn thành {{ $ticket->updated_at->format('d/m/Y') }}
            </div>
        @endif

        {{-- Actions --}}
        <div class="ktv-card__actions">
            <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="ktv-btn ktv-btn--sm ktv-btn--ghost">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                Xem chi tiết
            </a>

            @if($mode === 'new')
                {{-- Nút Nhận nhiệm vụ --}}
                <button class="ktv-btn ktv-btn--sm ktv-btn--accept ktv-accept-btn"
                        data-url="{{ route('admin.tickets.accept', $ticket->id) }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Nhận nhiệm vụ
                </button>
            @endif

            @if($mode === 'active')
                {{-- Nút Cập nhật tiến độ --}}
                <button class="ktv-btn ktv-btn--sm ktv-btn--progress"
                        onclick="openProgressModal(this)"
                        data-url="{{ route('admin.tickets.update-progress', $ticket->id) }}"
                        data-title="{{ $ticket->title }}"
                        data-apt="{{ $ticket->apartment->apartment_number ?? 'N/A' }}"
                        data-block="Tòa {{ $ticket->apartment?->floor?->block?->name ?? 'N/A' }}"
                        data-priority="{{ $ticket->priority }}"
                        data-status="{{ $ticket->status }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Cập nhật tiến độ
                </button>
            @endif
        </div>

    </div>
</div>
