@extends('layouts.resident.master')

@section('title', 'Gửi phản ánh – DomusHub')

@push('styles')
    @vite(['resources/css/resident/tickets.css'])
    <style>
        .tk-page-wrapper {
            display: flex;
            gap: 24px;
            padding: 24px;
            background: #f8fafc;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            align-items: flex-start;
        }
        
        @media (max-width: 992px) {
            .resident-header { display: none !important; }
            .resident-content { padding: 0 !important; }
            .mob-header { display: flex !important; }
            .tk-page-wrapper {
                padding: 16px;
            }
            .tk-layout-row {
                flex-direction: column;
                gap: 16px !important;
            }
            .tk-sidebar {
                width: 100% !important;
                margin-top: 0 !important;
            }
        }

        .tk-main-col {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .tk-sidebar {
            width: 320px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-top: 0;
        }

        .tk-page-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }
        .btn-back-header {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            flex-shrink: 0;
            margin-top: 2px;
        }
        .btn-back-header:hover {
            background: #f8fafc;
            color: #1e3a8a;
            border-color: #94a3b8;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }
        .tk-page-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 4px 0;
        }
        .tk-page-header p {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }

        .tk-create-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            display: block;
        }

        .form-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 20px 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        @media (max-width: 640px) {
            .tk-page-wrapper {
                padding: 12px;
            }
            .tk-create-card {
                padding: 16px;
                border-radius: 8px;
            }
            .form-grid { 
                grid-template-columns: 1fr 1fr; /* Keep it side by side like template */
                gap: 16px;
                margin-bottom: 16px;
            }
            .form-group {
                margin-bottom: 16px;
            }
            .form-actions {
                flex-direction: column-reverse;
                margin-top: 24px;
                gap: 12px;
            }
            .btn-cancel, .btn-submit {
                width: 100%;
                text-align: center;
                justify-content: center;
                padding: 12px;
            }
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: #0f172a;
            transition: all 0.2s;
            background: #fff;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
        }
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
        }

        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 32px 20px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s;
        }
        .upload-zone:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }
        .upload-zone i {
            font-size: 28px;
            color: #94a3b8;
            margin-bottom: 12px;
        }
        .upload-zone p {
            margin: 0 0 4px 0;
            font-size: 14px;
            color: #475569;
        }
        .upload-zone p strong { color: #2563eb; }
        .upload-zone span {
            font-size: 12px;
            color: #94a3b8;
        }
        .upload-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 12px;
        }
        .upload-preview-item {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            position: relative;
        }
        .upload-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .upload-preview-remove {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(0,0,0,0.6);
            color: #fff;
            border: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 32px;
        }
        .btn-cancel {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            background: #fff;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-cancel:hover { background: #f8fafc; }
        .btn-submit {
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            background: #001e71;
            border: none;
            cursor: pointer;
        }
        .btn-submit:hover { background: #001552; }

        .history-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 16px 0;
        }
        .history-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
        }
        .history-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
            gap: 12px;
        }
        @media (max-width: 640px) {
            .history-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        .history-info h4 {
            margin: 0 0 4px 0;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }
        .history-info span {
            font-size: 12px;
            color: #64748b;
        }
        .history-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: #eff6ff;
            color: #2563eb;
            white-space: nowrap;
        }
        .history-status.completed {
            background: #ecfdf5;
            color: #059669;
        }

        .timeline {
            position: relative;
            padding-left: 20px;
            margin: 0;
            list-style: none;
        }
        .timeline::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 6px;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }
        .timeline li {
            position: relative;
            margin-bottom: 16px;
        }
        .timeline li:last-child { margin-bottom: 0; }
        .timeline li::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #cbd5e1;
            border: 2px solid #fff;
        }
        .timeline li.active::before { background: #2563eb; }
        .timeline-content p {
            margin: 0 0 2px 0;
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
        }
        .timeline-content span {
            font-size: 11px;
            color: #64748b;
        }

        .sidebar-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 20px;
        }
        .sidebar-box.danger {
            background: #fef2f2;
            border-color: #fecaca;
        }
        .sidebar-box-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 8px 0;
        }
        .sidebar-box-title i { font-size: 16px; }
        .sidebar-box.primary .sidebar-box-title { color: #1e3a8a; }
        .sidebar-box.danger .sidebar-box-title { color: #b91c1c; }
        .sidebar-box p {
            font-size: 13px;
            color: #475569;
            margin: 0 0 16px 0;
            line-height: 1.5;
        }
        .sidebar-box.danger p { color: #7f1d1d; }

        .tips-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .tip-item {
            display: flex;
            gap: 12px;
            background: #fff;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #bfdbfe;
        }
        .tip-img {
            width: 60px;
            height: 48px;
            border-radius: 4px;
            background: #e2e8f0;
            flex-shrink: 0;
            overflow: hidden;
        }
        .tip-img img { width: 100%; height: 100%; object-fit: cover; }
        .tip-text h5 { margin: 0 0 2px 0; font-size: 12px; font-weight: 700; color: #0f172a; }
        .tip-text span { font-size: 10px; color: #64748b; line-height: 1.3; display: block; }

        .hotline-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            background: #b91c1c;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.2s;
        }
        .hotline-btn:hover { background: #991b1b; }
    </style>
@endpush

@section('content')
    <!-- Mobile Menu Drawer (Matches Master Header 100%) -->
    <div id="mobMenuOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.45); backdrop-filter:blur(3px); -webkit-backdrop-filter:blur(3px); z-index:999;"></div>
    <div id="mobMenuDrawer" style="position:fixed; top:0; left:-300px; width:270px; height:100%; background:#ffffff; z-index:1000; transition:left 0.28s cubic-bezier(0.16, 1, 0.3, 1); box-shadow: 10px 0 30px rgba(0, 35, 111, 0.15); display:flex; flex-direction:column;">
        <div style="padding: 18px 20px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 16px;">
            <button id="mobMenuClose" style="background:none; border:none; font-size:20px; color:#1e293b; cursor:pointer; padding:0; display:flex;"><i class="fa-solid fa-bars"></i></button>
            <strong style="color:#00236f; font-size:1.15rem; font-weight:800; letter-spacing:-0.01em;">DomusHub</strong>
        </div>
        <div style="padding: 16px 12px; display: flex; flex-direction: column; gap: 6px; overflow-y: auto;">
            <a href="{{ route('resident.dashboard') }}" class="mob-drawer-item {{ request()->routeIs('resident.dashboard') ? 'mob-drawer-item--active' : '' }}">Trang chủ</a>
            <a href="{{ route('resident.posts.index') }}" class="mob-drawer-item {{ request()->routeIs('resident.posts.*') ? 'mob-drawer-item--active' : '' }}">Bản tin</a>
            <a href="{{ route('resident.members.index') }}" class="mob-drawer-item {{ request()->routeIs('resident.members.*') ? 'mob-drawer-item--active' : '' }}">Thành viên</a>
            
            <div class="mob-drawer-dropdown">
                <button type="button" class="mob-drawer-item mob-drawer-trigger {{ (request()->routeIs('resident.vehicles.*') || request()->routeIs('resident.invoices.*') || request()->routeIs('resident.visitors.*') || request()->routeIs('resident.facilities.*') || request()->routeIs('resident.temporary-registrations.*')) ? 'mob-drawer-item--active' : '' }}" onclick="this.nextElementSibling.classList.toggle('show'); this.querySelector('.chevron').classList.toggle('rotate');">
                    <span>Dịch vụ</span>
                    <i class="fa-solid fa-chevron-down chevron" style="font-size: 0.75rem; transition: transform 0.2s ease;"></i>
                </button>
                <div class="mob-drawer-sub" style="display: none; flex-direction: column; gap: 2px; padding-left: 12px; margin-top: 4px; border-left: 2px solid #e2e8f0;">
                    <a href="{{ route('resident.temporary-registrations.index') }}" class="mob-drawer-sub-item {{ request()->routeIs('resident.temporary-registrations.*') ? 'mob-drawer-item--active' : '' }}">Tạm trú / Tạm vắng</a>
                    <a href="{{ route('resident.vehicles.index') }}" class="mob-drawer-sub-item {{ request()->routeIs('resident.vehicles.*') ? 'mob-drawer-item--active' : '' }}">Phương tiện</a>
                    <a href="{{ route('resident.facilities.index') }}" class="mob-drawer-sub-item {{ request()->routeIs('resident.facilities.*') || request()->routeIs('resident.facility-bookings.*') ? 'mob-drawer-item--active' : '' }}">Tiện ích</a>
                    <a href="{{ route('resident.visitors.index') }}" class="mob-drawer-sub-item {{ request()->routeIs('resident.visitors.*') ? 'mob-drawer-item--active' : '' }}">Khách ghé thăm</a>
                    <a href="{{ route('resident.invoices.index') }}" class="mob-drawer-sub-item {{ request()->routeIs('resident.invoices.*') ? 'mob-drawer-item--active' : '' }}">Thanh toán hóa đơn</a>
                </div>
            </div>

            <a href="{{ route('resident.tickets.index') }}" class="mob-drawer-item {{ request()->routeIs('resident.tickets.*') ? 'mob-drawer-item--active' : '' }}">Phản Ánh</a>
            <a href="{{ route('resident.contact') }}" class="mob-drawer-item {{ request()->routeIs('resident.contact') ? 'mob-drawer-item--active' : '' }}">Liên hệ</a>
            
            <hr style="border:0; border-bottom:1px solid #f1f5f9; margin: 8px 4px;">
            <a href="#" onclick="event.preventDefault(); document.getElementById('resident-logout-form').submit();" class="mob-drawer-item mob-drawer-item--logout">Đăng xuất</a>
        </div>
    </div>
    
    <!-- Top Header -->
    <div class="mob-header" style="display: none; align-items: center; justify-content: space-between; padding: 16px 20px; background: #fff; border-bottom: 1px solid #f1f5f9; position: sticky; top: 0; z-index: 90;">
        <button class="mob-menu-btn" id="mobMenuOpenBtn" style="background:none; border:none; font-size:20px; color:#1e3a8a; padding:0; cursor:pointer;"><i class="fa-solid fa-bars"></i></button>
        <div class="mob-header-title" style="font-size:18px; font-weight:700; color:#1e3a8a; flex:1; margin-left:20px;">Gửi phản ánh</div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <button class="mob-bell-btn" style="background:none; border:none; font-size:20px; color:#1e3a8a; padding:0; cursor:pointer;"><i class="fa-regular fa-bell"></i></button>
            <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80' }}" alt="Avatar" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;">
        </div>
    </div>

<div class="tk-page-wrapper" style="flex-direction: column;">
    
    <div class="tk-page-header">
        <div>
            <h1>Phản ánh và Kiến nghị</h1>
            <p>Gửi yêu cầu bảo trì, phản ánh dịch vụ hoặc kiến nghị ban quản lý.</p>
        </div>
    </div>

    <div class="tk-layout-row" style="display: flex; gap: 24px; align-items: flex-start; width: 100%;">
        <!-- CỘT TRÁI (FORM + LỊCH SỬ) -->
        <div class="tk-main-col">
            
            <div class="tk-create-card">
                <h2 class="form-title">Gửi phản ánh mới</h2>

                <form method="POST" action="{{ route('resident.tickets.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    @if($errors->any())
                        <div style="background: #fef2f2; color: #b91c1c; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
                            <ul style="margin:0; padding-left:20px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-grid">
                        <div class="form-group" style="margin-bottom:0;">
                            <label>Loại vấn đề</label>
                            <select name="ticket_type" class="form-control" required>
                                <option value="complaint" {{ old('ticket_type') == 'complaint' ? 'selected' : '' }}>Phản ánh sự cố</option>
                                <option value="report" {{ old('ticket_type') == 'report' ? 'selected' : '' }}>Tố cáo</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label>Mức độ khẩn cấp</label>
                            <select name="priority" class="form-control" required>
                                <option value="" disabled {{ old('priority') ? '' : 'selected' }}>-- Chọn --</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Bình thường</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Trung bình</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Cao</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Khẩn cấp</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Tiêu đề phản ánh</label>
                        <input type="text" name="title" class="form-control" placeholder="Ví dụ: Ống nước rò rỉ tại phòng bếp" value="{{ old('title') }}" required>
                    </div>

                    <div class="form-group">
                        <label>Mô tả chi tiết</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Vui lòng mô tả rõ sự cố bạn đang gặp phải..." required>{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Hình ảnh đính kèm</label>
                        <input type="file" id="fileInput" name="images[]" multiple accept="image/*" style="display:none;" onchange="previewImages(event)">
                        <div class="upload-zone" onclick="document.getElementById('fileInput').click()">
                            <i class="fa-solid fa-camera"></i>
                            <p>Kéo thả hình ảnh hoặc <strong>Bấm để tải lên</strong></p>
                            <span>Tối đa 5 ảnh, dung lượng dưới 5MB/ảnh</span>
                        </div>
                        <div class="upload-preview" id="imagePreview"></div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('resident.tickets.index') }}" class="btn-cancel">Hủy</a>
                        <button type="submit" class="btn-submit">Gửi phản ánh</button>
                    </div>
                </form>
            </div>

            <h3 class="history-title">Lịch sử yêu cầu</h3>
            @php
                $recentTickets = \App\Models\Ticket::where('sender_id', auth()->id())->latest()->take(2)->get();
            @endphp
            
            @forelse($recentTickets as $ticket)
                <div class="history-item">
                    <div class="history-header">
                        <div class="history-info">
                            <h4>{{ $ticket->title }}</h4>
                            <span>Mã YC: #RQ-{{ $ticket->id }} • {{ $ticket->created_at->format('d/m/Y') }}</span>
                        </div>
                        @php
                            $statusText = 'Chờ duyệt';
                            $statusClass = '';
                            if(in_array($ticket->status, ['in_progress', 'assigned'])) { $statusText = 'Đang xử lý'; $statusClass = ''; }
                            elseif($ticket->status === 'completed') { $statusText = 'Hoàn thành'; $statusClass = 'completed'; }
                        @endphp
                        <div class="history-status {{ $statusClass }}">
                            {{ $statusText }}
                        </div>
                    </div>
                    
                    <ul class="timeline">
                        <li class="active">
                            <div class="timeline-content">
                                <p>Gửi yêu cầu thành công</p>
                                <span>{{ $ticket->created_at->format('H:i, d/m/Y') }}</span>
                            </div>
                        </li>
                        @if($ticket->status != 'pending')
                        <li class="active">
                            <div class="timeline-content">
                                <p>Đã tiếp nhận & phân công</p>
                                <span>{{ $ticket->updated_at->format('H:i, d/m/Y') }}</span>
                            </div>
                        </li>
                        @endif
                    </ul>
                </div>
            @empty
                <div style="text-align: center; padding: 20px; color: #64748b; font-size: 14px;">
                    Chưa có phản ánh nào gần đây.
                </div>
            @endforelse
            
        </div>

        <!-- CỘT PHẢI (SIDEBAR) -->
        <div class="tk-sidebar">
            <div class="sidebar-box primary">
                <div class="sidebar-box-title">
                    <i class="fa-solid fa-wrench"></i> Gợi ý tự khắc phục
                </div>
                <p>Các mẹo nhỏ giúp bạn xử lý nhanh các sự cố thông thường trước khi thợ đến.</p>
                <div class="tips-list">
                    <a href="{{ route('resident.tips.water-valve') }}" class="tip-item" style="text-decoration: none; color: inherit; cursor: pointer;">
                        <div class="tip-img"><img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?auto=format&fit=crop&q=80&w=200" alt="Tip 1"></div>
                        <div class="tip-text">
                            <h5>Cách khóa van nước tạm thời</h5>
                            <span>Hướng dẫn xử lý khi ống nước rò rỉ...</span>
                        </div>
                    </a>
                    <a href="{{ route('resident.tips.circuit-breaker') }}" class="tip-item" style="text-decoration: none; color: inherit; cursor: pointer;">
                        <div class="tip-img"><img src="https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&q=80&w=200" alt="Tip 2"></div>
                        <div class="tip-text">
                            <h5>Kiểm tra aptomat khi mất điện</h5>
                            <span>Các bước an toàn để kiểm tra nguồn...</span>
                        </div>
                    </a>
                </div>
            </div>

            <div class="sidebar-box danger">
                <div class="sidebar-box-title">
                    Hotline Khẩn cấp
                </div>
                <p>Chỉ dành cho các sự cố đe dọa tính mạng, hỏa hoạn, hoặc ngập lụt nghiêm trọng.</p>
                <a href="tel:19001234" class="hotline-btn">
                    <i class="fa-solid fa-phone"></i> 1900 1234 (Ext 9)
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    @vite(['resources/js/pages/resident/tickets/create.js'])
@endpush
