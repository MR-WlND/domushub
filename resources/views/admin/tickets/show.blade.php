@extends('layouts.admin.master')

@section('page_title', 'Chi tiết phản ánh #' . $ticket->id)
@section('page_kicker', 'Dịch vụ cư dân')
@section('role_title', 'Admin Portal')
@section('home_route', route('admin.dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', auth()->user()->role)

@push('styles')
    @vite(['resources/css/pages/admin/tickets/index.css'])
@endpush

@section('content')
<div class="tickets-page">

    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0;">
                Phản ánh #{{ $ticket->id }}
            </h1>
            <p class="tickets-page__subtitle">{{ $ticket->title }}</p>
        </div>
        <a href="{{ route('admin.tickets.index') }}"
           style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: 10px; background: #f1f5f9; color: #334155; font-weight: 600; font-size: 0.88rem; text-decoration: none; transition: all 0.2s;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Quay lại
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="tickets-alert tickets-alert--success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="tickets-alert tickets-alert--danger">{{ $errors->first() }}</div>
    @endif

    {{-- Two Column Layout --}}
    <div class="tickets-show">

        {{-- Main Column --}}
        <div class="tickets-show__main">

            {{-- Ticket Info --}}
            <div class="tk-show-card">
                <div class="tk-show-card__header">
                    <span class="tk-show-card__title">Nội dung phản ánh</span>
                    <div style="display: flex; gap: 8px;">
                        <span class="tk-status tk-status--{{ $ticket->status }}">{{ $ticket->statusLabel() }}</span>
                        <span class="tk-priority tk-priority--{{ $ticket->priority }}">{{ $ticket->priorityLabel() }}</span>
                    </div>
                </div>
                <div class="tk-show-card__body">
                    <div class="tk-info-grid" style="margin-bottom: 1.25rem;">
                        <div class="tk-info-item">
                            <span class="tk-info-item__label">Căn hộ</span>
                            <span class="tk-info-item__value">{{ $ticket->apartment->apartment_number ?? 'N/A' }}</span>
                        </div>
                        <div class="tk-info-item">
                            <span class="tk-info-item__label">Tòa nhà</span>
                            <span class="tk-info-item__value">{{ $ticket->apartment->floor->block->name ?? 'N/A' }}</span>
                        </div>
                        <div class="tk-info-item">
                            <span class="tk-info-item__label">Người gửi</span>
                            <span class="tk-info-item__value">{{ $ticket->sender->name ?? 'N/A' }}</span>
                        </div>
                        <div class="tk-info-item">
                            <span class="tk-info-item__label">Ngày gửi</span>
                            <span class="tk-info-item__value">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                        <p style="font-size: 0.95rem; color: #334155; line-height: 1.7; margin: 0;">
                            {{ $ticket->description }}
                        </p>
                    </div>

                    @if($ticket->image)
                        <div style="margin-top: 1rem;">
                            <p style="font-size: 0.78rem; color: #94a3b8; font-weight: 600; margin-bottom: 6px;">ẢNH ĐÍNH KÈM</p>
                            <img src="{{ asset('storage/' . $ticket->image) }}"
                                 alt="Ảnh phản ánh"
                                 style="max-height: 280px; border-radius: 10px; object-fit: cover; border: 1px solid #e2e8f0; cursor: pointer;"
                                 onclick="openAdminImgModal(this.src)">
                        </div>
                    @endif

                    {{-- Rating Display --}}
                    @if($ticket->rating)
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                            <p style="font-size: 0.78rem; color: #94a3b8; font-weight: 600; margin-bottom: 6px;">ĐÁNH GIÁ CỦA CƯ DÂN</p>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="tk-rating-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="{{ $i <= $ticket->rating ? 'filled' : 'empty' }}">★</span>
                                    @endfor
                                </div>
                                <span style="font-weight: 800; color: #f59e0b;">{{ $ticket->rating }}/5</span>
                            </div>
                            @if($ticket->feedback_comment)
                                <p style="font-size: 0.88rem; color: #475569; margin-top: 6px; font-style: italic;">
                                    "{{ $ticket->feedback_comment }}"
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="tk-show-card">
                <div class="tk-show-card__header">
                    <span class="tk-show-card__title">Tiến trình xử lý</span>
                </div>
                <div class="tk-show-card__body">
                    @if($ticket->progress->isEmpty())
                        <p style="color: #94a3b8; text-align: center; padding: 1.5rem 0;">Chưa có cập nhật.</p>
                    @else
                        <div class="tk-admin-timeline">
                            @foreach($ticket->progress as $prog)
                                <div class="tk-admin-timeline__item">
                                    <div class="tk-admin-timeline__dot tk-admin-timeline__dot--{{ $prog->status }}"></div>
                                    <div class="tk-admin-timeline__content">
                                        <div class="tk-admin-timeline__status" style="color: {{ match($prog->status) {
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
                                            <div class="tk-admin-timeline__comment">{{ $prog->comment }}</div>
                                        @endif
                                        <div class="tk-admin-timeline__meta">
                                            <span>{{ $prog->updatedBy->name ?? 'Hệ thống' }}</span>
                                            <span>{{ $prog->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                        @if($prog->image_proof)
                                            <div class="tk-admin-timeline__proof">
                                                <img src="{{ asset('storage/' . $prog->image_proof) }}" alt="Ảnh nghiệm thu"
                                                     onclick="openAdminImgModal(this.src)" style="cursor: pointer;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="tickets-show__sidebar">

            {{-- Assign Form --}}
            @if(in_array($ticket->status, ['pending', 'assigned']) && in_array(auth()->user()->role, ['admin', 'manager']))
                <div class="tk-show-card">
                    <div class="tk-show-card__header">
                        <span class="tk-show-card__title">Phân công xử lý</span>
                    </div>
                    <div class="tk-show-card__body">
                        <form method="POST" action="{{ route('admin.tickets.assign', $ticket->id) }}">
                            @csrf
                            <div class="tk-form-group">
                                <label>Kỹ thuật viên</label>
                                <select name="handler_id" required>
                                    <option value="" disabled {{ $ticket->handler_id ? '' : 'selected' }}>-- Chọn KTV --</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}" {{ $ticket->handler_id == $tech->id ? 'selected' : '' }}>
                                            {{ $tech->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="tk-form-submit tk-form-submit--primary" style="width: 100%; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" x2="20" y1="8" y2="14"/><line x1="23" x2="17" y1="11" y2="11"/></svg>
                                {{ $ticket->handler_id ? 'Đổi KTV' : 'Phân công' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Update Progress Form --}}
            @if(in_array($ticket->status, ['assigned', 'in_progress']))
                <div class="tk-show-card">
                    <div class="tk-show-card__header">
                        <span class="tk-show-card__title">Cập nhật tiến trình</span>
                    </div>
                    <div class="tk-show-card__body">
                        <form method="POST"
                              id="progressFormDetail"
                              action="{{ route('admin.tickets.update-progress', $ticket->id) }}"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="tk-form-group">
                                <label>Trạng thái mới</label>
                                <select name="status" id="progressStatusDetail" required>
                                    @if($ticket->status === 'in_progress')
                                        <option value="in_progress" selected>Đang xử lý</option>
                                        <option value="completed">Hoàn thành</option>
                                    @else
                                        <option value="" disabled selected>-- Chọn --</option>
                                        <option value="in_progress">Đang xử lý</option>
                                        <option value="completed">Hoàn thành</option>
                                    @endif
                                </select>
                            </div>
                            <div class="tk-form-group">
                                <label>Báo cáo hoàn thành <span style="font-size: 0.8rem; color: #64748b;">(bắt buộc khi hoàn thành)</span></label>
                                <textarea name="comment" placeholder="Mô tả công việc đã thực hiện, vật tư sử dụng, kết quả..."></textarea>
                            </div>
                            <div class="tk-form-group">
                                <label>Ảnh nghiệm thu <span style="font-size: 0.8rem; color: #64748b;">(bắt buộc khi hoàn thành)</span></label>
                                <input type="file" name="image_proof" accept="image/*">
                            </div>
                            <button type="submit" class="tk-form-submit tk-form-submit--primary" style="width: 100%; justify-content: center;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Cập nhật
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Quick Info --}}
            <div class="tk-show-card">
                <div class="tk-show-card__header">
                    <span class="tk-show-card__title">Thông tin nhanh</span>
                </div>
                <div class="tk-show-card__body" style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.88rem;">
                        <span style="color: #64748b;">Mã phản ánh</span>
                        <strong>#{{ $ticket->id }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.88rem;">
                        <span style="color: #64748b;">Ngày tạo</span>
                        <strong>{{ $ticket->created_at->format('d/m/Y') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.88rem;">
                        <span style="color: #64748b;">Cập nhật lần cuối</span>
                        <strong>{{ $ticket->updated_at->format('d/m/Y H:i') }}</strong>
                    </div>
                    @if($ticket->handler)
                        <div style="display: flex; justify-content: space-between; font-size: 0.88rem;">
                            <span style="color: #64748b;">KTV phụ trách</span>
                            <strong>{{ $ticket->handler->name }}</strong>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>

{{-- Image Modal --}}
<div id="adminImgModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; cursor:pointer;" onclick="closeAdminImgModal()">
    <span style="position:absolute; top:20px; right:30px; color:#fff; font-size:2rem; font-weight:bold; cursor:pointer;">&times;</span>
    <img id="adminModalImg" src="" style="max-width:90%; max-height:90%; border-radius:12px; object-fit:contain;">
</div>

<script>
function openAdminImgModal(src) {
    const modal = document.getElementById('adminImgModal');
    document.getElementById('adminModalImg').src = src;
    modal.style.display = 'flex';
}
function closeAdminImgModal() {
    document.getElementById('adminImgModal').style.display = 'none';
}

// ── Xác nhận cập nhật tiến độ ở trang chi tiết ─────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    const progressFormDetail = document.getElementById('progressFormDetail');
    if (progressFormDetail) {
        progressFormDetail.addEventListener('submit', function (e) {
            const statusSelect = document.getElementById('progressStatusDetail');
            const statusText = statusSelect.value === 'completed' ? 'Hoàn thành' : 'Đang xử lý';
            if (!confirm(`Bạn có chắc chắn muốn cập nhật trạng thái phản ánh này thành "${statusText}" không?`)) {
                e.preventDefault();
            }
        });
    }
});
</script>

@endsection
