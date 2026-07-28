@extends('layouts.admin.master')

@section('page_title', 'Chi tiết ' . ($ticket->ticket_type === 'report' ? 'tố cáo' : 'phản ánh') . ' #' . $ticket->id)
@section('page_kicker', 'Dịch vụ cư dân')
@section('role_title', 'Admin Portal')
@section('home_route', portal_route('dashboard'))
@section('user_name', auth()->user()->name ?? 'Admin')
@section('user_role', auth()->user()->role)

@push('styles')
    @vite(['resources/css/pages/admin/tickets/show.css'])
@endpush

@section('content')
<div class="tk-detail-page">

    {{-- Header --}}
    <div class="tk-detail-header">
        <a href="{{ url()->previous() == url()->current() ? portal_route('tickets.index') : url()->previous() }}" class="tk-back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Quay lại
        </a>
        <div class="tk-detail-header-content">
            <div class="tk-detail-title-group">
                <h1 class="tk-detail-title">REQ-{{ $ticket->id }}</h1>
                <span class="tk-detail-status tk-status--{{ $ticket->status }}">{{ $ticket->statusLabel() }}</span>
            </div>
            <div class="tk-detail-meta">
                Ngày gửi: {{ $ticket->created_at->format('d/m/Y - h:i A') }}
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 24px; font-weight: 500;">{{ $errors->first() }}</div>
    @endif

    <div class="tk-detail-grid">
        {{-- LEFT COLUMN --}}
        <div class="tk-detail-left">
            
            {{-- Issue Card --}}
            <div class="tk-card">
                <div class="tk-card-header tk-card-header--icon" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="tk-card-icon-box">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2.25c-5.385 4.362-8.25 8.92-8.25 12.375 0 4.5 3.75 8.25 8.25 8.25s8.25-3.75 8.25-8.25c0-3.455-2.865-8.013-8.25-12.375z"/></svg>
                        </div>
                        <h2 class="tk-card-title" style="margin: 0;">{{ $ticket->title }}</h2>
                    </div>
                    
                    @if(auth()->user()->role === 'technician')
                    <div class="tk-action-list" style="margin: 0; display: flex; gap: 8px;">
                        @if($ticket->status === 'pending' || $ticket->status === 'assigned')
                            <form action="{{ portal_route('tickets.accept', $ticket->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="tk-btn-primary" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 6px;">Nhận việc</button>
                            </form>
                        @elseif($ticket->status === 'in_progress')
                            <button type="button" class="tk-btn-outline" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 6px;" onclick="openProgressModal()">Cập nhật tiến độ</button>
                            <button type="button" class="tk-btn-primary" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 6px; background: #16a34a; border-color: #16a34a;" onclick="openProgressModal(true)">Hoàn thành</button>
                        @elseif($ticket->status === 'completed')
                            <button class="tk-btn-disabled" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 6px;" disabled>Hoàn thành</button>
                        @endif
                    </div>
                    @endif
                </div>
                <div class="tk-card-body">
                    <div class="tk-section-title">NỘI DUNG PHẢN ÁNH</div>
                    <div class="tk-description-box">
                        {{ $ticket->description }}
                    </div>

                    @if($ticket->images && count($ticket->images) > 0)
                        <div class="tk-section-title">ẢNH ĐÍNH KÈM</div>
                        <div class="tk-photo-grid">
                            @foreach($ticket->images as $img)
                                <img src="{{ asset('storage/' . $img) }}" alt="Photo" class="tk-photo-item" onclick="openAdminImgModal(this.src)">
                            @endforeach
                        </div>
                    @elseif($ticket->image)
                        <div class="tk-section-title">ẢNH ĐÍNH KÈM</div>
                        <div class="tk-photo-grid">
                            <img src="{{ asset('storage/' . $ticket->image) }}" alt="Photo" class="tk-photo-item" onclick="openAdminImgModal(this.src)">
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resident Info Card --}}
            <div class="tk-card">
                <div class="tk-card-header">
                    <h2 class="tk-card-title" style="font-size: 1.1rem;">Thông tin cư dân</h2>
                </div>
                <div class="tk-card-body">
                    <div class="tk-resident-info">
                        <div class="tk-resident-item">
                            <div class="tk-resident-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="tk-resident-text">
                                <label>Họ và tên</label>
                                <span>{{ $ticket->sender->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="tk-resident-item">
                            <div class="tk-resident-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="tk-resident-text">
                                <label>Căn hộ</label>
                                <span>Phòng {{ $ticket->apartment->apartment_number ?? 'N/A' }}, Tòa {{ $ticket->apartment->floor->block->name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="tk-resident-item">
                            <div class="tk-resident-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div class="tk-resident-text">
                                <label>Liên hệ</label>
                                <span>{{ $ticket->sender->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Admin Only - Costs (if any) --}}
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <div class="tk-card">
                <div class="tk-card-header">
                    <h2 class="tk-card-title" style="font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        Chi phí phát sinh
                    </h2>
                </div>
                <div class="tk-card-body" style="padding: 16px;">
                    <form method="POST" action="{{ portal_route('tickets.add-cost', $ticket->id) }}">
                        @csrf
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                            <input type="text" name="description" placeholder="Mô tả chi phí..." required style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                            <input type="number" name="amount" placeholder="Số tiền (VNĐ)" min="1000" step="1000" required style="padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                        </div>
                        <input type="hidden" name="cost_type" value="repair">
                        <button type="submit" class="tk-btn-outline" style="width: auto; padding: 8px 16px; font-size: 0.85rem;">+ Thêm chi phí sửa chữa</button>
                    </form>

                    @if($ticket->costs->count() > 0)
                        <div style="margin-top: 24px;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #e2e8f0; color: #64748b; text-align: left;">
                                        <th style="padding: 8px;">Mô tả</th>
                                        <th style="padding: 8px;">Số tiền</th>
                                        <th style="padding: 8px;">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ticket->costs as $cost)
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 8px;">{{ $cost->description }}</td>
                                        <td style="padding: 8px; color: #0f172a; font-weight: 600;">{{ number_format($cost->amount, 0, ',', '.') }}đ</td>
                                        <td style="padding: 8px;">
                                            <form method="POST" action="{{ portal_route('tickets.delete-cost', [$ticket->id, $cost->id]) }}" onsubmit="return confirm('Xóa?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" style="color: #dc2626; background: none; border: none; cursor: pointer;">X</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div style="text-align: right; margin-top: 12px; font-weight: 800; font-size: 1.1rem; color: #dc2626;">
                                Tổng: {{ number_format($ticket->costs->sum('amount'), 0, ',', '.') }}đ
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Reported Person (if report) --}}
            @if($ticket->ticket_type === 'report')
            <div class="tk-card" style="border: 1px solid #fecaca;">
                <div class="tk-card-header" style="background: #fef2f2;">
                    <h2 class="tk-card-title" style="font-size: 1.1rem; color: #dc2626;">Người bị tố cáo</h2>
                </div>
                <div class="tk-card-body">
                    @if($ticket->accused_user_id)
                        <div style="font-weight: 700; color: #0f172a; margin-bottom: 8px;">{{ $ticket->accusedUser->name ?? 'N/A' }}</div>
                        <div style="font-size: 0.9rem; color: #64748b;">Phản hồi: {{ $ticket->accusedResponseLabel() }}</div>
                        @if($ticket->accused_response_comment)
                            <div style="font-size: 0.9rem; font-style: italic; margin-top: 8px;">"{{ $ticket->accused_response_comment }}"</div>
                        @endif
                    @else
                        @if($ticket->reported_person)
                            <div style="font-size: 0.9rem; color: #dc2626; margin-bottom: 12px;">Cư dân ghi: {{ $ticket->reported_person }}</div>
                        @endif
                        <form method="POST" action="{{ portal_route('tickets.assign-accused', $ticket->id) }}">
                            @csrf
                            <select name="accused_user_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 12px;">
                                <option value="" disabled selected>-- Chọn cư dân --</option>
                                @foreach($residents as $res)
                                    <option value="{{ $res->id }}">{{ $res->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="tk-btn-primary" style="background: #dc2626;">Gửi thông báo tố cáo</button>
                        </form>
                    @endif
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="tk-detail-right">


            {{-- Internal Notes (Timeline) --}}
            <div class="tk-card">
                <div class="tk-card-header">
                    <h2 class="tk-card-title" style="font-size: 1.1rem;">Ghi chú nội bộ</h2>
                </div>
                <div class="tk-card-body" style="padding: 16px;">
                    @if($ticket->progress->isEmpty())
                        <div class="tk-notes-empty">Chưa có ghi chú nào.</div>
                    @else
                        <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px; max-height: 400px; overflow-y: auto;">
                            @foreach($ticket->progress as $prog)
                                <div style="background: #f8fafc; border-radius: 8px; padding: 12px; font-size: 0.9rem;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                        <strong style="color: #0f172a;">{{ $prog->updatedBy->name ?? 'Hệ thống' }}</strong>
                                        <span style="color: #94a3b8; font-size: 0.8rem;">{{ $prog->created_at->format('M d, h:i A') }}</span>
                                    </div>
                                    <div style="color: #2563eb; font-weight: 600; font-size: 0.8rem; margin-bottom: 4px;">[{{ $prog->statusLabel() }}]</div>
                                    @if($prog->comment)
                                        <div style="color: #334155;">{{ $prog->comment }}</div>
                                    @endif
                                    @if($prog->image_proof)
                                        <img src="{{ asset('storage/' . $prog->image_proof) }}" style="max-width:100%; border-radius:6px; margin-top:8px; cursor:pointer;" onclick="openAdminImgModal(this.src)">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Admin Assign Form --}}
            @if(in_array(auth()->user()->role, ['admin', 'manager']))
            <div class="tk-card">
                <div class="tk-card-header">
                    <h2 class="tk-card-title" style="font-size: 1.1rem;">Phân công kỹ thuật viên</h2>
                </div>
                <div class="tk-card-body">
                    <form method="POST" action="{{ portal_route('tickets.assign', $ticket->id) }}">
                        @csrf
                        <div style="margin-bottom: 12px;">
                            <select name="handler_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;">
                                <option value="" disabled {{ $ticket->handler_id ? '' : 'selected' }}>-- Chọn kỹ thuật viên --</option>
                                @foreach($technicians as $tech)
                                    <option value="{{ $tech->id }}" {{ $ticket->handler_id == $tech->id ? 'selected' : '' }}>
                                        {{ $tech->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="tk-btn-primary" style="padding: 8px;">Phân công</button>
                    </form>
                </div>
            </div>
            @endif


        </div>
    </div>
</div>

{{-- Progress/Complete Modal --}}
<div id="progressModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; align-items:center; justify-content:center;">
    <div style="background:#fff; width:400px; border-radius:12px; padding:24px; position:relative;">
        <span style="position:absolute; top:16px; right:20px; font-size:1.5rem; cursor:pointer;" onclick="closeProgressModal()">&times;</span>
        <h2 style="margin-bottom: 16px; font-size:1.2rem; font-weight:700;" id="modalTitle">Cập nhật tiến độ</h2>
        
        <form id="progressForm" method="POST" action="{{ portal_route('tickets.update-progress', $ticket->id) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" id="modalStatus" value="in_progress">
            
            <div style="margin-bottom: 16px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; color:#475569; margin-bottom:6px;">Ghi chú tiến độ</label>
                <textarea name="comment" rows="3" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; outline:none; font-family:inherit;"></textarea>
            </div>
            
            <div style="margin-bottom: 24px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; color:#475569; margin-bottom:6px;">Ảnh chứng minh (Tùy chọn)</label>
                <input type="file" name="image_proof" accept="image/*" style="width:100%; padding:8px; border:1px solid #cbd5e1; border-radius:6px;">
            </div>

            <button type="submit" class="tk-btn-primary" id="modalSubmitBtn">Xác nhận</button>
        </form>
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

function openProgressModal(isComplete = false) {
    document.getElementById('progressModal').style.display = 'flex';
    if(isComplete) {
        document.getElementById('modalTitle').innerText = 'Hoàn thành nhiệm vụ';
        document.getElementById('modalStatus').value = 'completed';
        document.getElementById('modalSubmitBtn').innerText = 'Xác nhận hoàn thành';
        document.getElementById('modalSubmitBtn').style.background = '#16a34a';
    } else {
        document.getElementById('modalTitle').innerText = 'Cập nhật tiến độ';
        document.getElementById('modalStatus').value = 'in_progress';
        document.getElementById('modalSubmitBtn').innerText = 'Cập nhật';
        document.getElementById('modalSubmitBtn').style.background = '#00236f';
    }
}

function closeProgressModal() {
    document.getElementById('progressModal').style.display = 'none';
}
</script>
@endsection
