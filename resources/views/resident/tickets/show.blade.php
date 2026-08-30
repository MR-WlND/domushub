@extends('layouts.resident.master')

@section('title', 'Chi tiết phản ánh #RQ-' . $ticket->id . ' – DomusHub')

@push('styles')
    @vite(['resources/css/resident/tickets.css'])
    <style>
        .tk-detail-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            font-family: 'Inter', system-ui, 'Segoe UI', -apple-system, sans-serif;
            color: #0f172a;
        }

        /* Top Header */
        .tk-top-nav {
            margin-bottom: 12px;
        }
        .tk-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #475569;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s;
        }
        .tk-back-btn:hover {
            color: #001e71;
        }

        .tk-main-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .tk-main-title {
            font-size: 26px;
            font-weight: 800;
            color: #00236f;
            margin: 0;
            letter-spacing: -0.02em;
        }
        .tk-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #dbeafe;
        }
        .tk-status-badge.completed {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }
        .tk-status-badge.cancelled {
            background: #f1f5f9;
            color: #475569;
            border-color: #cbd5e1;
        }
        .tk-status-badge.pending {
            background: #fffbeb;
            color: #b45309;
            border-color: #fde68a;
        }

        /* 2-Column Layout */
        .tk-layout-grid {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 24px;
            align-items: start;
        }
        @media (max-width: 992px) {
            .tk-layout-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Card Container */
        .tk-box-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            margin-bottom: 24px;
        }
        .tk-box-card:last-child {
            margin-bottom: 0;
        }
        .tk-box-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 20px 0;
        }

        /* Thông tin chung */
        .tk-info-row-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        @media (max-width: 640px) {
            .tk-info-row-grid {
                grid-template-columns: 1fr;
            }
        }
        .tk-info-col {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .tk-info-col-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        .tk-info-col-val {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
        }
        .tk-info-col-val i {
            color: #1e3a8a;
            font-size: 15px;
        }

        /* Callout Box - Nội dung chi tiết & Tin nhắn BQL */
        .tk-text-callout {
            background: #f0f7ff;
            border-radius: 12px;
            padding: 16px 20px;
            font-size: 14px;
            line-height: 1.6;
            color: #1e293b;
            border: 1px solid #dbeafe;
        }

        /* Hình ảnh đính kèm */
        .tk-media-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }
        .tk-media-item {
            width: 100%;
            height: 180px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .tk-media-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        /* Timeline Tiến trình */
        .tk-timeline-list {
            position: relative;
            padding-left: 24px;
            margin: 0;
            list-style: none;
        }
        .tk-timeline-list::before {
            content: '';
            position: absolute;
            left: 5px;
            top: 8px;
            bottom: 8px;
            width: 2px;
            background: #e2e8f0;
        }
        .tk-tl-step {
            position: relative;
            margin-bottom: 24px;
        }
        .tk-tl-step:last-child {
            margin-bottom: 0;
        }
        .tk-tl-dot {
            position: absolute;
            left: -24px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #cbd5e1;
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 2px #e2e8f0;
        }
        .tk-tl-step.active .tk-tl-dot {
            background: #001e71;
            box-shadow: 0 0 0 2px #bfdbfe;
        }
        .tk-tl-time {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 4px;
        }
        .tk-tl-desc {
            font-size: 13.5px;
            color: #1e293b;
            line-height: 1.5;
            font-weight: 500;
        }

        /* Tin nhắn BQL Box */
        .tk-bql-box {
            background: #eff6ff;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid #dbeafe;
            margin-bottom: 16px;
        }
        .tk-bql-head {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        .tk-bql-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #001e71;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .tk-bql-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
        }
        .tk-bql-body {
            font-size: 13.5px;
            color: #1e293b;
            line-height: 1.5;
            margin: 0 0 8px 0;
            font-style: italic;
        }
        .tk-bql-time-text {
            font-size: 11px;
            color: #64748b;
            text-align: right;
        }

        /* Right Column Buttons */
        .btn-side-primary {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-side-primary:hover {
            background: #dbeafe;
            color: #1e40af;
        }
        .btn-side-disabled {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 14px;
            font-weight: 600;
            cursor: not-allowed;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }
        .side-note-text {
            font-size: 11px;
            color: #94a3b8;
            text-align: center;
            margin-top: 8px;
            display: block;
        }
        .btn-side-active-star {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: #ffffff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            margin-top: 10px;
            box-shadow: 0 4px 12px rgba(245,158,11,0.25);
            transition: opacity 0.2s;
        }
        .btn-side-active-star:hover {
            opacity: 0.95;
            color: #fff;
        }
        .btn-side-cancel {
            width: 100%;
            padding: 11px;
            border-radius: 10px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            margin-top: 12px;
            transition: background 0.2s;
        }
        .btn-side-cancel:hover {
            background: #fee2e2;
        }
    </style>
@endpush

@section('content')
<div class="tk-detail-wrapper">

    {{-- NAV BACK --}}
    <div class="tk-top-nav">
        <a href="{{ route('resident.tickets.index') }}" class="tk-back-btn">
            <i class="fa-solid fa-arrow-left"></i> Trở lại danh sách
        </a>
    </div>

    {{-- MAIN HEADER --}}
    <div class="tk-main-header">
        <h1 class="tk-main-title">Chi tiết phản ánh #RQ-{{ $ticket->id }}</h1>

        @php
            $statusClass = match($ticket->status) {
                'completed' => 'completed',
                'cancelled' => 'cancelled',
                'pending' => 'pending',
                default => ''
            };
        @endphp
        <div class="tk-status-badge {{ $statusClass }}">
            @if($ticket->status === 'pending')
                <i class="fa-solid fa-clock"></i> Chờ tiếp nhận
            @elseif($ticket->status === 'in_progress' || $ticket->status === 'assigned')
                <i class="fa-solid fa-rotate"></i> Đang xử lý
            @elseif($ticket->status === 'completed')
                <i class="fa-solid fa-circle-check"></i> Đã hoàn thành
            @elseif($ticket->status === 'cancelled')
                <i class="fa-solid fa-circle-xmark"></i> Đã hủy
            @else
                {{ $ticket->statusLabel() }}
            @endif
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="tk-alert tk-alert--success" style="margin-bottom: 20px;">
            <i class="fa-solid fa-circle-check"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="tk-alert tk-alert--error" style="margin-bottom: 20px;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>{{ $errors->first() }}</div>
        </div>
    @endif

    {{-- BANNER BỊ TỐ CÁO (NẾU CÓ) --}}
    @if(isset($isAccused) && $isAccused)
        @if(!$ticket->hasAccusedResponse())
            <div style="background: linear-gradient(135deg, #fef2f2, #fff1f2); border: 1.5px solid #fca5a5; border-radius: 16px; padding: 20px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                    <i class="fa-solid fa-triangle-exclamation" style="color: #dc2626; font-size: 20px;"></i>
                    <strong style="color: #dc2626; font-size: 1.05rem;">Bạn đã bị tố cáo trong vụ việc này</strong>
                </div>
                <p style="color: #991b1b; font-size: 0.88rem; margin: 0 0 16px 0; line-height: 1.6;">
                    Vui lòng xem nội dung tố cáo và bằng chứng bên dưới, sau đó phản hồi bằng cách chọn <strong>"Tôi xác nhận"</strong> hoặc <strong>"Tôi phản đối"</strong>.
                </p>

                <form method="POST" action="{{ route('resident.tickets.respond-accusation', $ticket->id) }}" style="display: flex; flex-direction: column; gap: 12px;">
                    @csrf
                    <div>
                        <label style="font-weight: 600; font-size: 0.85rem; color: #374151; margin-bottom: 4px; display: block;">Lý do (không bắt buộc)</label>
                        <textarea name="accused_response_comment" style="width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 0.88rem; resize: vertical; min-height: 60px; box-sizing: border-box;" placeholder="Giải thích lý do xác nhận hoặc phản đối...">{{ old('accused_response_comment') }}</textarea>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" name="accused_response" value="confirmed" style="flex: 1; padding: 12px; border: none; border-radius: 10px; background: #16a34a; color: #fff; font-weight: 700; font-size: 0.88rem; cursor: pointer;" onclick="return confirm('Bạn xác nhận sự việc trong tố cáo là đúng?')">
                            Tôi xác nhận
                        </button>
                        <button type="submit" name="accused_response" value="denied" style="flex: 1; padding: 12px; border: none; border-radius: 10px; background: #dc2626; color: #fff; font-weight: 700; font-size: 0.88rem; cursor: pointer;" onclick="return confirm('Bạn phản đối tố cáo này?')">
                            Tôi phản đối
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div style="display:flex;align-items:center;gap:12px;padding:14px 20px;border-radius:16px;margin-bottom:24px;
                @if($ticket->accused_response === 'confirmed')
                    background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1.5px solid #86efac;
                @else
                    background:linear-gradient(135deg,#fef2f2,#fecaca);border:1.5px solid #fca5a5;
                @endif
            ">
                <div>
                    <div style="font-weight:700;font-size:0.95rem; @if($ticket->accused_response === 'confirmed') color:#166534; @else color:#991b1b; @endif">
                        Bạn đã {{ $ticket->accused_response === 'confirmed' ? 'xác nhận' : 'phản đối' }} tố cáo này
                    </div>
                    @if($ticket->accused_response_comment)
                        <div style="font-size:0.82rem;color:#475569;margin-top:4px;font-style:italic;">
                            "{{ $ticket->accused_response_comment }}"
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endif

    {{-- GRID LAYOUT 2 CỘT --}}
    <div class="tk-layout-grid">

        {{-- CỘT TRÁI (WIDE) --}}
        <div>
            {{-- CARD 1: THÔNG TIN CHUNG --}}
            <div class="tk-box-card">
                <h3 class="tk-box-title">Thông tin chung</h3>
                <div class="tk-info-row-grid">
                    <div class="tk-info-col">
                        <span class="tk-info-col-label">Loại sự cố</span>
                        <div class="tk-info-col-val">
                            <i class="fa-solid fa-wrench"></i>
                            <span>{{ $ticket->ticket_type === 'report' ? 'Tố cáo' : 'Kỹ thuật - ' . ($ticket->title ?? 'Điện/Nước') }}</span>
                        </div>
                    </div>
                    <div class="tk-info-col">
                        <span class="tk-info-col-label">Ngày gửi</span>
                        <div class="tk-info-col-val">
                            <i class="fa-regular fa-calendar"></i>
                            <span>{{ $ticket->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="tk-info-col">
                        <span class="tk-info-col-label">Vị trí</span>
                        <div class="tk-info-col-val">
                            <i class="fa-solid fa-building"></i>
                            <span>Phòng {{ $ticket->apartment->apartment_number ?? '1205' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: NỘI DUNG CHI TIẾT --}}
            <div class="tk-box-card">
                <h3 class="tk-box-title">Nội dung chi tiết</h3>
                <div class="tk-text-callout">
                    “{{ $ticket->description }}”
                </div>
            </div>

            {{-- CARD 3: HÌNH ẢNH ĐÍNH KÈM (NẾU CÓ) --}}
            @if($ticket->images && count($ticket->images) > 0)
                <div class="tk-box-card">
                    <h3 class="tk-box-title">Hình ảnh đính kèm</h3>
                    <div class="tk-media-grid">
                        @foreach($ticket->images as $file)
                            @php
                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
                            @endphp
                            @if($isVideo)
                                <video controls src="{{ asset('storage/' . $file) }}" class="tk-media-item" style="background:#000;"></video>
                            @else
                                <img src="{{ asset('storage/' . $file) }}" alt="Ảnh đính kèm" class="tk-media-item" onclick="openImgModal(this.src)">
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif



            {{-- FORM ĐÁNH GIÁ (KHI XONG & CHƯA ĐÁNH GIÁ) --}}
            @if($ticket->canFeedback())
                <div class="tk-box-card" id="feedbackSection" style="border-color: #fde68a; box-shadow: 0 4px 20px rgba(245,158,11,0.15);">
                    <h3 class="tk-box-title" style="color: #d97706; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-star"></i> Đánh giá chất lượng xử lý
                    </h3>
                    <form method="POST" action="{{ route('resident.tickets.feedback', $ticket->id) }}" style="display: flex; flex-direction: column; gap: 16px;">
                        @csrf
                        <div>
                            <label style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 8px; display: block;">Mức độ hài lòng <span style="color:#ef4444;">*</span></label>
                            <div class="tk-rating" id="starRating" style="display: flex; gap: 8px; font-size: 28px; cursor: pointer; color: #cbd5e1;">
                                @for($i = 1; $i <= 5; $i++)
                                    <span class="star-item" data-val="{{ $i }}" style="transition: color 0.2s;">★</span>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="ratingValue" value="{{ old('rating', 5) }}">
                            <div id="ratingHint" style="font-size: 12px; color: #d97706; font-weight: 600; margin-top: 4px;">Xuất sắc!</div>
                        </div>

                        <div>
                            <label style="font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px; display: block;">Nhận xét thêm (tùy chọn)</label>
                            <textarea name="feedback_comment" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13.5px; box-sizing: border-box;" rows="3" placeholder="Nhập nhận xét của bạn...">{{ old('feedback_comment') }}</textarea>
                        </div>

                        <button type="submit" class="btn-side-active-star" style="margin-top: 0; width: auto; align-self: flex-start; padding: 10px 24px;">
                            Gửi đánh giá
                        </button>
                    </form>
                </div>
            @elseif($ticket->rating)
                <div class="tk-box-card">
                    <h3 class="tk-box-title" style="color: #059669; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-star"></i> Đánh giá của bạn
                    </h3>
                    <div style="font-size: 14px; color: #1e293b; font-weight: 600; margin-bottom: 6px;">
                        {{ $ticket->rating }}/5 sao — {{ ['','Rất tệ','Chưa hài lòng','Bình thường','Hài lòng','Xuất sắc!'][$ticket->rating] ?? '' }}
                    </div>
                    @if($ticket->feedback_comment)
                        <p style="font-size: 13.5px; color: #475569; font-style: italic; margin: 0;">“{{ $ticket->feedback_comment }}”</p>
                    @endif
                </div>
            @endif

        </div>

        {{-- CỘT PHẢI (SIDEBAR) --}}
        <div>
            {{-- CARD 1: TIẾN TRÌNH XỬ LÝ --}}
            <div class="tk-box-card">
                <h3 class="tk-box-title">Tiến trình xử lý</h3>
                <ul class="tk-timeline-list">
                    @if($ticket->progress && $ticket->progress->count() > 0)
                        @foreach($ticket->progress as $prog)
                            <li class="tk-tl-step {{ $loop->first ? 'active' : '' }}">
                                <div class="tk-tl-dot"></div>
                                <div class="tk-tl-time">{{ $prog->created_at->format('d/m/Y H:i') }}</div>
                                <div class="tk-tl-desc">
                                    {{ $prog->comment ?? $prog->statusLabel() }}
                                </div>
                                @if($prog->image_proof)
                                    <img src="{{ asset('storage/' . $prog->image_proof) }}" alt="Bằng chứng" style="width: 60px; height: 60px; border-radius: 6px; object-fit: cover; margin-top: 6px; cursor: pointer;" onclick="openImgModal(this.src)">
                                @endif
                            </li>
                        @endforeach
                    @else
                        {{-- Mẫu tiến trình theo trạng thái nếu chưa có bản ghi progress --}}
                        @if(in_array($ticket->status, ['in_progress', 'assigned', 'completed']))
                            <li class="tk-tl-step active">
                                <div class="tk-tl-dot"></div>
                                <div class="tk-tl-time">{{ $ticket->updated_at->format('d/m/Y H:i') }}</div>
                                <div class="tk-tl-desc">
                                    @if($ticket->handler)
                                        Kỹ thuật viên {{ $ticket->handler->name }} đang thực hiện sửa chữa
                                    @else
                                        Kỹ thuật viên đang thực hiện sửa chữa
                                    @endif
                                </div>
                            </li>
                            <li class="tk-tl-step">
                                <div class="tk-tl-dot"></div>
                                <div class="tk-tl-time">{{ $ticket->updated_at->subMinutes(30)->format('d/m/Y H:i') }}</div>
                                <div class="tk-tl-desc">Ban quản lý đã tiếp nhận và phân công nhân viên kỹ thuật</div>
                            </li>
                        @endif
                        <li class="tk-tl-step {{ $ticket->status === 'pending' ? 'active' : '' }}">
                            <div class="tk-tl-dot"></div>
                            <div class="tk-tl-time">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                            <div class="tk-tl-desc">Gửi yêu cầu thành công</div>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- CARD 2: TIN NHẮN TỪ BQL --}}
            <div class="tk-box-card">
                <h3 class="tk-box-title">Tin nhắn từ BQL</h3>

                @php
                    $bqlMsg = $ticket->progress->where('comment', '!=', null)->first()?->comment
                        ?? "Chào cư dân, thợ kỹ thuật đã tiếp nhận thông tin và sẽ kiểm tra xử lý căn hộ của bạn.";
                    $bqlTime = $ticket->progress->first()?->created_at ?? $ticket->updated_at;
                @endphp

                <div class="tk-bql-box">
                    <div class="tk-bql-head">
                        <div class="tk-bql-icon">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        <span class="tk-bql-title">Ban Quản Lý Toà Nhà</span>
                    </div>
                    <p class="tk-bql-body">“{{ $bqlMsg }}”</p>
                    <div class="tk-bql-time-text">{{ $bqlTime->format('H:i · d/m/Y') }}</div>
                </div>

                {{-- NÚT HÀNH ĐỘNG --}}
                <div style="display: flex; flex-direction: column;">
                    <button type="button" class="btn-side-primary" onclick="alert('Vui lòng phản hồi trực tiếp qua chat hotline hoặc liên hệ BQL.')">
                        <i class="fa-regular fa-comment-dots"></i> Gửi thêm phản hồi
                    </button>

                    @if($ticket->canFeedback())
                        <a href="#feedbackSection" class="btn-side-active-star" onclick="scrollToFeedback()">
                            <i class="fa-solid fa-star"></i> Đánh giá dịch vụ ngay
                        </a>
                    @else
                        <button type="button" class="btn-side-disabled" disabled>
                            <i class="fa-regular fa-star"></i> Đánh giá dịch vụ
                        </button>
                        <span class="side-note-text">Tính năng đánh giá sẽ mở khi sự cố được giải quyết</span>
                    @endif

                    {{-- NÚT HỦY --}}
                    @if(!isset($isAccused) || !$isAccused)
                        @if($ticket->canCancelBy(auth()->id()))
                            <form method="POST" action="{{ route('resident.tickets.cancel', $ticket->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn hủy phản ánh này?')">
                                @csrf
                                <button type="submit" class="btn-side-cancel">
                                    <i class="fa-solid fa-xmark"></i> Hủy phản ánh
                                </button>
                            </form>
                        @endif
                    @endif
                </div>

            </div>
        </div>

    </div>

</div>

{{-- Image Lightbox Modal --}}
<div id="tkImgModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:9999; align-items:center; justify-content:center; cursor:pointer;" onclick="closeImgModal()">
    <span style="position:absolute; top:20px; right:30px; color:#fff; font-size:2rem; font-weight:bold; cursor:pointer;">&times;</span>
    <img id="tkModalImg" src="" style="max-width:90%; max-height:90%; border-radius:12px; object-fit:contain;">
</div>

@endsection

@push('scripts')
    @vite(['resources/js/pages/resident/tickets/show.js'])
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.Echo) {
            window.Echo.private('ticket.{{ $ticket->id }}')
                .listen('TicketProgressUpdated', (e) => {
                    // Hiển thị thông báo và tải lại trang để xem tiến độ mới
                    const toast = document.createElement('div');
                    toast.className = 'tk-alert tk-alert--success';
                    toast.style.position = 'fixed';
                    toast.style.bottom = '20px';
                    toast.style.right = '20px';
                    toast.style.zIndex = '9999';
                    toast.innerHTML = '<i class="fa-solid fa-circle-info"></i><div>Tiến độ phản ánh vừa được cập nhật. Đang tải lại...</div>';
                    document.body.appendChild(toast);
                    
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                });
        }
    });
    </script>
@endpush
