@php
    $grouped = $tickets->getCollection()->groupBy(fn($t) => $t->apartment?->floor?->block?->name ?? 'Không xác định');
    $blockOrder = $blocks->pluck('name')->toArray();
    $grouped = $grouped->sortBy(fn($items, $key) => array_search($key, $blockOrder) !== false ? array_search($key, $blockOrder) : 999);
@endphp

@if($tickets->isEmpty())
    <div class="rpt-empty">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#cbd5e1" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p>Không có phản ánh nào</p>
    </div>
@else
    @foreach($grouped as $blockName => $blockTickets)
    <div class="tickets-block-group mb-5">
        <div class="tickets-block-group__header">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="1"/><path d="M9 3v18"/><path d="M15 3v18"/><path d="M2 9h20"/><path d="M2 15h20"/></svg>
            <span>Tòa {{ $blockName }}</span>
            <span class="tickets-block-group__count">{{ $blockTickets->count() }} phản ánh</span>
        </div>
        <div class="tickets-table-card">
            <div class="tickets-table-wrap">
                <table class="tickets-table">
                    <thead>
                        <tr>
                            <th class="w-9"></th>
                            <th>Phản ánh</th>
                            <th>Căn hộ</th>
                            <th>Trạng thái</th>
                            <th>KTV</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blockTickets as $ticket)
                        @php
                            $ageHours = $ticket->created_at->diffInHours(now());
                            $slaOver = match($ticket->priority) { 'urgent' => $ageHours >= 2, 'high' => $ageHours >= 8, 'medium' => $ageHours >= 24, 'low' => $ageHours >= 72, default => false };
                            $isActive = !in_array($ticket->status, ['completed','cancelled']);
                            $overdue = $slaOver && $isActive;
                        @endphp
                        <tr class="tk-row {{ $overdue ? 'tk-row--overdue' : '' }}"
                            data-id="{{ $ticket->id }}" data-title="{{ $ticket->title }}" data-desc="{{ $ticket->description }}"
                            data-status="{{ $ticket->status }}" data-status-label="{{ $ticket->statusLabel() }}"
                            data-priority="{{ $ticket->priority }}" data-priority-label="{{ $ticket->priorityLabel() }}"
                            data-apartment="{{ $ticket->apartment->apartment_number ?? 'N/A' }}" data-block="{{ $blockName }}"
                            data-floor="{{ $ticket->apartment?->floor?->floor_number ?? '' }}"
                            data-sender="{{ $ticket->sender->name ?? 'N/A' }}" data-handler="{{ $ticket->handler->name ?? '' }}"
                            data-handler-id="{{ $ticket->handler_id ?? '' }}"
                            data-created="{{ $ticket->created_at->diffForHumans() }}" data-created-full="{{ $ticket->created_at->format('d/m/Y H:i') }}"
                            data-assign-url="{{ route('admin.tickets.assign', $ticket->id) }}"
                            data-detail-url="{{ route('admin.tickets.show', $ticket->id) }}"
                            data-can-assign="{{ in_array($ticket->status, ['pending','assigned']) && in_array(auth()->user()->role, ['admin','manager']) ? '1' : '0' }}"
                            data-overdue="{{ $overdue ? '1' : '0' }}">
                            <td><span class="tk-priority-dot tk-priority-dot--{{ $ticket->priority }}" title="{{ $ticket->priorityLabel() }}"></span></td>
                            <td>
                                <div class="tk-title-cell">
                                    <span class="tk-title-cell__title">{{ $ticket->title }}</span>
                                    <span class="tk-title-cell__desc">{{ Str::limit($ticket->description, 55) }}</span>
                                </div>
                            </td>
                            <td><strong>{{ $ticket->apartment->apartment_number ?? 'N/A' }}</strong></td>
                            <td><span class="tk-status tk-status--{{ $ticket->status }}">{{ $ticket->statusLabel() }}</span></td>
                            <td>
                                @if($ticket->handler)
                                    <span class="font-semibold text-sm">{{ $ticket->handler->name }}</span>
                                @else
                                    <span class="tk-unassigned">Chưa phân công</span>
                                @endif
                            </td>
                            <td class="tk-time" title="{{ $ticket->created_at->format('d/m/Y H:i') }}">
                                {{ $ticket->created_at->diffForHumans() }}
                                @if($overdue)<div class="tk-overdue-badge">⚠ Trễ SLA</div>@endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
@endif

@if($tickets->hasPages())
    <div class="tickets-pagination">{{ $tickets->links() }}</div>
@endif
