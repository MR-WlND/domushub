@extends('layouts.admin.master')

@section('page_title', 'Quản lý lịch đặt tiện ích')

@section('content')
<div class="amb-page">

    {{-- Header --}}
    <div class="amb-header">
        <div>
            <p class="amb-eyebrow">Tiện ích chung cư</p>
            <h1 class="amb-title">Quản lý lịch đặt</h1>
        </div>
        <a href="{{ portal_route('amenities.index') }}" class="amb-btn amb-btn--outline">
            Danh sách tiện ích
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="amb-alert amb-alert--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="amb-alert amb-alert--error">{{ session('error') }}</div>
    @endif

    {{-- Stats --}}
    <div class="amb-stats">
        <a href="{{ portal_route('amenities.bookings', ['status' => 'pending']) }}" class="amb-stat amb-stat--pending {{ request('status') === 'pending' ? 'amb-stat--active' : '' }}">
            <div>
                <div class="amb-stat-num">{{ $stats['pending'] }}</div>
                <div class="amb-stat-label">Chờ duyệt</div>
            </div>
        </a>
        <a href="{{ portal_route('amenities.bookings', ['status' => 'approved']) }}" class="amb-stat amb-stat--approved {{ request('status') === 'approved' ? 'amb-stat--active' : '' }}">
            <div>
                <div class="amb-stat-num">{{ $stats['approved'] }}</div>
                <div class="amb-stat-label">Đã duyệt</div>
            </div>
        </a>
        <a href="{{ portal_route('amenities.bookings', ['status' => 'used']) }}" class="amb-stat amb-stat--used {{ request('status') === 'used' ? 'amb-stat--active' : '' }}">
            <div>
                <div class="amb-stat-num">{{ $stats['used'] }}</div>
                <div class="amb-stat-label">Đã sử dụng</div>
            </div>
        </a>
        <a href="{{ portal_route('amenities.bookings', ['status' => 'cancelled']) }}" class="amb-stat amb-stat--cancelled {{ request('status') === 'cancelled' ? 'amb-stat--active' : '' }}">
            <div>
                <div class="amb-stat-num">{{ $stats['cancelled'] }}</div>
                <div class="amb-stat-label">Đã hủy</div>
            </div>
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="amb-filter">
        <select name="facility_id" onchange="this.form.submit()">
            <option value="">Tất cả tiện ích</option>
            @foreach($facilities as $f)
                <option value="{{ $f->id }}" {{ request('facility_id') == $f->id ? 'selected' : '' }}>
                    {{ $f->name }}
                </option>
            @endforeach
        </select>
        <select name="status" onchange="this.form.submit()">
            <option value="">Tất cả trạng thái</option>
            <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Chờ duyệt</option>
            <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Đã duyệt</option>
            <option value="used"      {{ request('status') === 'used'      ? 'selected' : '' }}>Đã sử dụng</option>
            <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Từ chối</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
        </select>
        <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" class="amb-filter-date" placeholder="Lọc ngày">
        @if(request('facility_id') || request('status') || request('date'))
            <a href="{{ portal_route('amenities.bookings') }}" class="amb-btn amb-btn--ghost amb-btn--sm">Xóa bộ lọc</a>
        @endif
    </form>

    {{-- Bảng --}}
    <div class="amb-card">
        <div class="amb-card-header">
            <span>{{ $bookings->total() }} lịch đặt</span>
            @if(request('status') === 'pending' && $stats['pending'] > 0)
            <span class="amb-pending-badge">{{ $stats['pending'] }} chờ xử lý</span>
            @endif
        </div>

        @if($bookings->isEmpty())
        <div class="amb-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="none" stroke="#cbd5e1" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <p>Không có lịch đặt nào phù hợp</p>
        </div>
        @else
        <table class="amb-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cư dân</th>
                    <th>Tiện ích</th>
                    <th>Ngày & Giờ</th>
                    <th>Số người</th>
                    <th>Trạng thái</th>
                    <th>Thanh toán</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td class="amb-id">{{ $booking->id }}</td>
                    <td>
                        <div class="amb-user">
                            <div class="amb-avatar">{{ mb_substr($booking->user->name ?? 'N', 0, 1) }}</div>
                            <div>
                                <p class="amb-user-name">{{ $booking->user->name ?? '—' }}</p>
                                <p class="amb-user-email">{{ $booking->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <a href="{{ portal_route('amenities.show', $booking->facility) }}" class="amb-facility-link">
                            {{ $booking->facility->name ?? '—' }}
                        </a>
                    </td>
                    <td>
                        <div class="amb-date-time">
                            <span class="amb-date">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</span>
                            <span class="amb-time">{{ substr($booking->start_time, 0, 5) }} – {{ substr($booking->end_time, 0, 5) }}</span>
                        </div>
                    </td>
                    <td class="amb-people">{{ $booking->number_of_people ?? 1 }} người</td>
                    <td>
                        <span class="amb-badge amb-badge--{{ $booking->status_class }}">
                            {{ $booking->status_label }}
                        </span>
                        @if($booking->checked_in_at)
                        <div class="amb-checkin-time">{{ $booking->checked_in_at->format('H:i d/m') }}</div>
                        @endif
                    </td>
                    <td>
                        @php $amount = $booking->amount; @endphp
                        @if($amount > 0)
                        <div class="amb-payment">
                            <span class="amb-amount">{{ number_format($amount) }}đ</span>
                            <span class="amb-payment-status amb-ps--{{ $booking->payment_status }}">
                                {{ $booking->payment_label }}
                            </span>
                        </div>
                        @endif
                    </td>
                    <td>
                        <div class="amb-actions">
                            @if($booking->status === 'pending')
                                <form method="POST" action="{{ portal_route('amenities.bookings.approve', $booking) }}">
                                    @csrf
                                    <button type="submit" class="amb-btn amb-btn--xs amb-btn--approve" title="Duyệt lịch">✓ Duyệt</button>
                                </form>
                                <form method="POST" action="{{ portal_route('amenities.bookings.reject', $booking) }}">
                                    @csrf
                                    <button type="submit" class="amb-btn amb-btn--xs amb-btn--reject" onclick="return confirm('Từ chối lịch đặt #{{ $booking->id }}?')" title="Từ chối">✕ Từ chối</button>
                                </form>

                            @elseif($booking->status === 'approved')
                                {{-- Cập nhật trạng thái --}}
                                <button class="amb-btn amb-btn--xs amb-btn--status" onclick="openStatusModal({{ $booking->id }}, '{{ $booking->status }}', '{{ $booking->status_label }}')" title="Cập nhật trạng thái">
                                    Trạng thái
                                </button>
                            @endif

                            @if(!in_array($booking->status, ['used', 'cancelled', 'rejected']))
                                <form method="POST" action="{{ portal_route('amenities.bookings.cancel', $booking) }}" onsubmit="return confirm('Hủy lịch đặt #{{ $booking->id }}?')">
                                    @csrf
                                    <button type="submit" class="amb-btn amb-btn--xs amb-btn--cancel" title="Hủy lịch">Hủy</button>
                                </form>
                            @endif

                            {{-- Xem chi tiết --}}
                            <button class="amb-btn amb-btn--xs amb-btn--detail" onclick="openDetailModal({{ $booking->id }})" title="Xem chi tiết">Chi tiết</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($bookings->hasPages())
        <div class="amb-pagination">
            {{ $bookings->links() }}
        </div>
        @endif
        @endif
    </div>
</div>

{{-- Modal: Cập nhật trạng thái --}}
<div id="statusModal" class="amb-modal" style="display:none">
    <div class="amb-modal-backdrop" onclick="closeStatusModal()"></div>
    <div class="amb-modal-content">
        <div class="amb-modal-header">
            <h3>Cập nhật trạng thái lịch đặt</h3>
            <button onclick="closeStatusModal()" class="amb-modal-close">✕</button>
        </div>
        <p class="amb-modal-sub" id="statusModalSub"></p>
        <form id="statusForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="amb-status-options">
                <label class="amb-status-opt">
                    <input type="radio" name="status" value="used">
                    <div class="amb-status-opt-content amb-soc--used">
                        <span>Đã sử dụng</span>
                        <small>Check-in thành công</small>
                    </div>
                </label>
                <label class="amb-status-opt">
                    <input type="radio" name="status" value="cancelled">
                    <div class="amb-status-opt-content amb-soc--cancelled">
                        <span>Hủy lịch</span>
                        <small>Chủ động hủy</small>
                    </div>
                </label>
            </div>
            <button type="submit" class="amb-modal-submit">Xác nhận cập nhật</button>
        </form>
    </div>
</div>

{{-- Modal: Chi tiết --}}
<div id="detailModal" class="amb-modal" style="display:none">
    <div class="amb-modal-backdrop" onclick="closeDetailModal()"></div>
    <div class="amb-modal-content amb-modal-content--wide">
        <div class="amb-modal-header">
            <h3>Chi tiết lịch đặt</h3>
            <button onclick="closeDetailModal()" class="amb-modal-close">✕</button>
        </div>
        <div id="detailContent" class="amb-detail-content">
            <div class="amb-loading">Đang tải...</div>
        </div>
    </div>
</div>

{{-- Booking data for JS --}}
<script>
const bookingsData = {
    @foreach($bookings as $booking)
    {{ $booking->id }}: {
        id: {{ $booking->id }},
        facility: "{{ $booking->facility->name ?? '—' }}",
        user: "{{ $booking->user->name ?? '—' }}",
        email: "{{ $booking->user->email ?? '' }}",
        date: "{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}",
        start: "{{ substr($booking->start_time, 0, 5) }}",
        end: "{{ substr($booking->end_time, 0, 5) }}",
        people: {{ $booking->number_of_people ?? 1 }},
        status: "{{ $booking->status_label }}",
        statusClass: "{{ $booking->status_class }}",
        qr: "{{ $booking->qr_code }}",
        checkin: "{{ $booking->checked_in_at ? $booking->checked_in_at->format('H:i d/m/Y') : '' }}",
        amount: {{ $booking->amount }},
        payStatus: "{{ $booking->payment_label }}",
    },
    @endforeach
};

function openStatusModal(id, currentStatus, currentLabel) {
    document.getElementById('statusForm').action = '/admin/facility-bookings/' + id + '/status';
    document.getElementById('statusModalSub').textContent = 'Lịch đặt #' + id + ' – Hiện tại: ' + currentLabel;
    document.getElementById('statusModal').style.display = 'flex';
    // Reset radio
    document.querySelectorAll('#statusModal input[type=radio]').forEach(r => r.checked = false);
}
function closeStatusModal() {
    document.getElementById('statusModal').style.display = 'none';
}

function openDetailModal(id) {
    const b = bookingsData[id];
    if (!b) return;
    let html = `
        <div class="amb-detail-grid">
            <div class="amb-detail-row"><span class="amb-dl">Tiện ích</span><span class="amb-dv">${b.facility}</span></div>
            <div class="amb-detail-row"><span class="amb-dl">Cư dân</span><span class="amb-dv">${b.user} <span style="color:#94a3b8;font-size:0.78rem">${b.email}</span></span></div>
            <div class="amb-detail-row"><span class="amb-dl">Ngày sử dụng</span><span class="amb-dv">${b.date}</span></div>
            <div class="amb-detail-row"><span class="amb-dl">Giờ</span><span class="amb-dv amb-dv--mono">${b.start} – ${b.end}</span></div>
            <div class="amb-detail-row"><span class="amb-dl">Số người</span><span class="amb-dv">${b.people} người</span></div>
            <div class="amb-detail-row"><span class="amb-dl">Trạng thái</span><span class="amb-dv"><span class="amb-badge amb-badge--${b.statusClass}">${b.status}</span></span></div>
            ${b.amount > 0 ? `<div class="amb-detail-row"><span class="amb-dl">Phí</span><span class="amb-dv">${b.amount.toLocaleString('vi-VN')}đ – ${b.payStatus}</span></div>` : '<div class="amb-detail-row"><span class="amb-dl">Phí</span><span class="amb-dv" style="color:#16a34a">Miễn phí</span></div>'}
            ${b.checkin ? `<div class="amb-detail-row"><span class="amb-dl">Check-in</span><span class="amb-dv" style="color:#16a34a">✓ ${b.checkin}</span></div>` : ''}
            ${b.qr ? `<div class="amb-detail-row"><span class="amb-dl">QR Code</span><span class="amb-dv amb-dv--mono" style="font-size:0.75rem">${b.qr}</span></div>` : ''}
        </div>
        ${b.qr ? `<div style="text-align:center;margin-top:16px"><img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=${encodeURIComponent(b.qr)}" style="border-radius:10px;border:1px solid #e2e8f0" alt="QR"></div>` : ''}
    `;
    document.getElementById('detailContent').innerHTML = html;
    document.getElementById('detailModal').style.display = 'flex';
}
function closeDetailModal() {
    document.getElementById('detailModal').style.display = 'none';
}
</script>

<style>
.amb-page { max-width: 1300px; margin: 0 auto; padding: 24px 20px; }

.amb-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
.amb-eyebrow { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; margin: 0 0 4px; font-weight: 600; font-family:'Inter', system-ui, -apple-system, sans-serif; }
.amb-title { font-size: 26px; font-weight: 700; color: #00236f; margin: 0; font-family:'Inter', system-ui, -apple-system, sans-serif; letter-spacing:-0.02em; }

/* Alert */
.amb-alert { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 8px; font-size: 0.875rem; font-weight: 500; margin-bottom: 16px; }
.amb-alert--success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
.amb-alert--error   { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }

/* Stats */
.amb-stats { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 20px; }
.amb-stat { display: flex; align-items: center; gap: 12px; padding: 14px 20px; background: #fff; border: 1.5px solid #e2e8f0; border-radius: 12px; text-decoration: none; color: inherit; transition: all 0.15s; cursor: pointer; }
.amb-stat:hover { border-color: #3b82f6; }
.amb-stat--active { border-color: #3b82f6; background: #eff6ff; }
.amb-stat-icon { font-size: 1.4rem; }
.amb-stat-num { font-size: 1.4rem; font-weight: 800; color: #0f172a; line-height: 1; }
.amb-stat-label { font-size: 0.72rem; font-weight: 600; color: #64748b; margin-top: 2px; }
.amb-stat--pending .amb-stat-num { color: #d97706; }
.amb-stat--approved .amb-stat-num { color: #16a34a; }
.amb-stat--used .amb-stat-num { color: #0369a1; }
.amb-stat--cancelled .amb-stat-num { color: #94a3b8; }

/* Filter */
.amb-filter { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; align-items: center; }
.amb-filter select { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; color: #374151; background: #fff; outline: none; cursor: pointer; }
.amb-filter select:focus { border-color: #3b82f6; }
.amb-filter-date { padding: 8px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; color: #374151; background: #fff; outline: none; }
.amb-filter-date:focus { border-color: #3b82f6; }

/* Card */
.amb-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
.amb-card-header { padding: 12px 18px; border-bottom: 1px solid #e2e8f0; font-size: 0.85rem; font-weight: 600; color: #334155; background: #f8fafc; display: flex; justify-content: space-between; align-items: center; }
.amb-pending-badge { font-size: 0.72rem; font-weight: 700; background: #fef9c3; color: #a16207; padding: 3px 10px; border-radius: 20px; animation: pulse 2s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.6} }

/* Table */
.amb-table { width: 100%; border-collapse: collapse; }
.amb-table th { text-align: left; padding: 10px 12px; font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid #e2e8f0; background: #f8fafc; white-space: nowrap; }
.amb-table td { padding: 11px 12px; font-size: 0.875rem; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.amb-table tr:last-child td { border-bottom: none; }
.amb-table tr:hover td { background: #fafbff; }

.amb-id { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }
.amb-user { display: flex; align-items: center; gap: 9px; }
.amb-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; font-weight: 700; flex-shrink: 0; }
.amb-user-name { font-weight: 600; color: #0f172a; margin: 0; font-size: 0.875rem; }
.amb-user-email { font-size: 0.72rem; color: #94a3b8; margin: 1px 0 0; }
.amb-facility-link { font-weight: 600; color: #2563eb; text-decoration: none; font-size: 0.875rem; }
.amb-facility-link:hover { text-decoration: underline; }
.amb-date-time { display: flex; flex-direction: column; gap: 3px; }
.amb-date { font-weight: 600; color: #0f172a; font-size: 0.875rem; }
.amb-time { font-size: 0.78rem; color: #475569; background: #f1f5f9; padding: 2px 7px; border-radius: 4px; font-family: monospace; display: inline-block; width: fit-content; }
.amb-people { font-size: 0.82rem; color: #475569; white-space: nowrap; }
.amb-checkin-time { font-size: 0.7rem; color: #16a34a; margin-top: 3px; }
.amb-payment { display: flex; flex-direction: column; gap: 3px; }
.amb-amount { font-size: 0.85rem; font-weight: 700; color: #0f172a; }
.amb-payment-status { font-size: 0.7rem; font-weight: 600; }
.amb-ps--paid { color: #16a34a; }
.amb-ps--unpaid { color: #dc2626; }
.amb-free { font-size: 0.78rem; color: #16a34a; font-weight: 600; }

/* Badges */
.amb-badge { font-size: 0.7rem; font-weight: 700; padding: 3px 9px; border-radius: 20px; white-space: nowrap; }
.amb-badge--warning   { background: #fef9c3; color: #a16207; }
.amb-badge--success   { background: #dcfce7; color: #15803d; }
.amb-badge--info      { background: #e0f2fe; color: #0369a1; }
.amb-badge--secondary { background: #f1f5f9; color: #64748b; }
.amb-badge--danger    { background: #fee2e2; color: #b91c1c; }
.amb-badge--primary   { background: #ede9fe; color: #7c3aed; }

/* Actions */
.amb-actions { display: flex; gap: 4px; flex-wrap: wrap; align-items: center; }
.amb-btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 13px; border-radius: 7px; font-size: 0.82rem; font-weight: 600; cursor: pointer; border: none; text-decoration: none; transition: all 0.15s; white-space: nowrap; }
.amb-btn--outline { background: #fff; color: #475569; border: 1px solid #e2e8f0; }
.amb-btn--outline:hover { background: #f8fafc; }
.amb-btn--ghost { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
.amb-btn--ghost:hover { background: #f1f5f9; }
.amb-btn--sm { padding: 6px 12px; font-size: 0.8rem; }
.amb-btn--xs { padding: 4px 8px; font-size: 0.72rem; }
.amb-btn--approve { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.amb-btn--approve:hover { background: #bbf7d0; }
.amb-btn--reject  { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
.amb-btn--reject:hover  { background: #fecaca; }
.amb-btn--cancel  { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
.amb-btn--cancel:hover  { background: #f1f5f9; color: #374151; }
.amb-btn--status  { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
.amb-btn--status:hover  { background: #dbeafe; }
.amb-btn--detail  { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }
.amb-btn--detail:hover  { background: #f1f5f9; }

/* Pagination */
.amb-pagination { padding: 14px 18px; border-top: 1px solid #f1f5f9; }
.amb-empty { text-align: center; padding: 48px; color: #94a3b8; }
.amb-empty p { margin-top: 12px; font-size: 0.9rem; }

/* Modal */
.amb-modal { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; }
.amb-modal-backdrop { position: absolute; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(3px); }
.amb-modal-content { position: relative; background: #fff; border-radius: 18px; padding: 28px; width: 100%; max-width: 400px; margin: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
.amb-modal-content--wide { max-width: 520px; }
.amb-modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.amb-modal-header h3 { font-size: 1.05rem; font-weight: 700; color: #0f172a; margin: 0; }
.amb-modal-close { background: none; border: none; font-size: 1.1rem; cursor: pointer; color: #64748b; padding: 4px 8px; border-radius: 6px; }
.amb-modal-close:hover { background: #f1f5f9; }
.amb-modal-sub { font-size: 0.82rem; color: #64748b; margin: 0 0 20px; }

.amb-status-options { display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px; }
.amb-status-opt { display: block; cursor: pointer; }
.amb-status-opt input { display: none; }
.amb-status-opt-content { display: flex; flex-direction: column; padding: 12px 16px; border: 1.5px solid #e2e8f0; border-radius: 10px; transition: all 0.15s; }
.amb-status-opt-content span { font-size: 0.9rem; font-weight: 600; }
.amb-status-opt-content small { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }
.amb-status-opt input:checked + .amb-soc--used { border-color: #0ea5e9; background: #e0f2fe; color: #0369a1; }
.amb-status-opt input:checked + .amb-soc--cancelled { border-color: #ef4444; background: #fee2e2; color: #b91c1c; }
.amb-status-opt-content:hover { border-color: #94a3b8; }

.amb-modal-submit { width: 100%; padding: 12px; background: linear-gradient(135deg, #3b82f6, #6366f1); color: #fff; border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.amb-modal-submit:hover { opacity: 0.9; transform: translateY(-1px); }

/* Detail modal */
.amb-detail-grid { display: flex; flex-direction: column; gap: 10px; }
.amb-detail-row { display: flex; gap: 12px; align-items: flex-start; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
.amb-detail-row:last-child { border-bottom: none; }
.amb-dl { font-size: 0.78rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; width: 90px; flex-shrink: 0; padding-top: 2px; }
.amb-dv { font-size: 0.875rem; font-weight: 600; color: #0f172a; flex: 1; }
.amb-dv--mono { font-family: monospace; }
.amb-loading { text-align: center; padding: 20px; color: #94a3b8; }
</style>
@endsection
