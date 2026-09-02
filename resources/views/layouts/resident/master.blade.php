<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DomusHub Resident')</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/layouts/resident.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="resident-page">
    @include('layouts.resident.partials.header')

    <div class="resident-layout">
        <main class="resident-content @yield('content_class')">
            @yield('content')
        </main>
    </div>

    @include('layouts.resident.partials.chatbot')

    {{-- ===== VISITOR WALK-IN POPUP NOTIFICATION ===== --}}
    @php
        $unreadVisitorNotifs = [];
        if (auth()->check()) {
            $unreadVisitorNotifs = auth()->user()
                ->unreadNotifications()
                ->whereJsonContains('data->type', 'visitor_walk_in')
                ->latest()
                ->take(5)
                ->get();
        }
    @endphp

    @if($unreadVisitorNotifs->isNotEmpty())
    {{-- Backdrop --}}
    <div id="visitorPopupBackdrop" style="
        position:fixed; inset:0;
        background:rgba(15,23,42,0.55);
        backdrop-filter:blur(6px);
        -webkit-backdrop-filter:blur(6px);
        z-index:999990;
        display:flex; align-items:center; justify-content:center;
        opacity:0; transition:opacity .3s ease;
        padding:1rem;
    ">
        {{-- Popup card --}}
        <div id="visitorPopupCard" style="
            background:#fff;
            border-radius:20px;
            width:100%; max-width:420px;
            box-shadow:0 25px 60px rgba(0,0,100,.18);
            overflow:hidden;
            transform:scale(.92) translateY(24px);
            transition:transform .35s cubic-bezier(.34,1.56,.64,1), opacity .3s ease;
            opacity:0;
            position:relative;
        ">
            {{-- Header gradient --}}
            <div style="
                background:linear-gradient(135deg,#00236f 0%,#1e3a8a 100%);
                padding:1.25rem 1.5rem 1rem;
                position:relative;
            ">
                {{-- Badge --}}
                <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
                    <div style="
                        width:32px;height:32px;
                        background:rgba(255,255,255,.2);
                        border-radius:8px;
                        display:flex;align-items:center;justify-content:center;
                    ">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div>
                        <p style="margin:0;font-size:.7rem;color:rgba(255,255,255,.75);font-weight:600;letter-spacing:.06em;text-transform:uppercase;">Thông báo mới</p>
                        <h2 style="margin:0;font-size:1.05rem;font-weight:800;color:#fff;">Khách ghé thăm đang chờ</h2>
                    </div>
                </div>
                {{-- Count badge --}}
                @if($unreadVisitorNotifs->count() > 1)
                <div style="
                    position:absolute;top:1rem;right:1rem;
                    background:rgba(255,255,255,.25);
                    color:#fff;
                    font-size:.72rem;font-weight:700;
                    padding:.2rem .55rem;border-radius:999px;
                ">{{ $unreadVisitorNotifs->count() }} khách</div>
                @endif
            </div>

            {{-- Scrollable content area --}}
            <div style="max-height:55vh;overflow-y:auto;padding:.25rem 0;" id="visitorNotifList">
            @foreach($unreadVisitorNotifs as $i => $notif)
                @php
                    $d = $notif->data;
                    $visitorId  = $d['visitor_id']  ?? null;
                    $guestName  = $d['guest_name']   ?? 'Khách';
                    $guestPhone = $d['guest_phone']  ?? null;
                    $faceImg    = $d['face_image']   ?? null;
                    $detailUrl  = $d['url']          ?? '#';
                    $msgText    = $d['message']      ?? ($d['body'] ?? '');
                    $notifId    = $notif->id;
                    $initial    = mb_strtoupper(mb_substr($guestName, 0, 1));
                @endphp
                <div class="vp-item" data-notif-id="{{ $notifId }}" data-visitor-id="{{ $visitorId }}" style="
                    padding:1rem 1.5rem;
                    {{ !$loop->last ? 'border-bottom:1px solid #f1f5f9;' : '' }}
                ">
                    {{-- Clickable info row → mark read + navigate --}}
                    <a href="{{ $detailUrl }}" onclick="vpMarkRead('{{ $notifId }}')" style="display:block;text-decoration:none;color:inherit;">
                    <div style="display:flex;gap:.85rem;align-items:flex-start;">
                        {{-- Avatar --}}
                        <div style="
                            width:52px;height:52px;flex-shrink:0;
                            border-radius:12px;overflow:hidden;
                            background:linear-gradient(135deg,#dbeafe,#bfdbfe);
                            display:flex;align-items:center;justify-content:center;
                            font-weight:800;font-size:1.2rem;color:#00236f;
                            border:2px solid #e8ecf4;
                        ">
                            @if($faceImg)
                                <img src="{{ asset('storage/'.$faceImg) }}" alt="{{ $guestName }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                {{ $initial }}
                            @endif
                        </div>
                        {{-- Info --}}
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;margin-bottom:.2rem;">
                                <span style="font-size:.95rem;font-weight:800;color:#0f172a;">{{ $guestName }}</span>
                                @if($guestPhone)
                                    <span style="font-size:.75rem;color:#64748b;background:#f1f5f9;padding:.1rem .45rem;border-radius:6px;">{{ $guestPhone }}</span>
                                @endif
                            </div>
                            <p style="margin:0;font-size:.95rem;font-weight:400;color:#64748b;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $msgText }}</p>
                            <p style="margin:.4rem 0 0;font-size:.72rem;color:#94a3b8;">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    </a>

                    {{-- Actions --}}
                    @if($visitorId)
                    <div style="display:flex;gap:.5rem;margin-top:.85rem;" class="vp-actions">
                        <button
                            onclick="vpAction(this,'approve','{{ $visitorId }}','{{ $notifId }}')"
                            style="
                                flex:1;display:flex;align-items:center;justify-content:center;gap:.35rem;
                                background:linear-gradient(135deg,#059669,#10b981);
                                color:#fff;border:none;border-radius:10px;
                                padding:.55rem .75rem;font-size:.82rem;font-weight:700;
                                cursor:pointer;transition:opacity .15s,transform .1s;
                                font-family:inherit;
                            "
                            onmouseover="this.style.opacity='.9'"
                            onmouseout="this.style.opacity='1'"
                        >
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Chấp nhận
                        </button>
                        <button
                            onclick="vpAction(this,'reject','{{ $visitorId }}','{{ $notifId }}')"
                            style="
                                flex:1;display:flex;align-items:center;justify-content:center;gap:.35rem;
                                background:#fff;color:#ef4444;
                                border:1.5px solid #fecaca;border-radius:10px;
                                padding:.55rem .75rem;font-size:.82rem;font-weight:700;
                                cursor:pointer;transition:opacity .15s,background .15s;
                                font-family:inherit;
                            "
                            onmouseover="this.style.background='#fef2f2'"
                            onmouseout="this.style.background='#fff'"
                        >
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Từ chối
                        </button>
                        <a
                            href="{{ $detailUrl }}"
                            onclick="vpMarkRead('{{ $notifId }}')"
                            style="
                                display:flex;align-items:center;justify-content:center;
                                background:#f8fafc;color:#475569;
                                border:1.5px solid #e2e8f0;border-radius:10px;
                                padding:.55rem .75rem;font-size:.82rem;font-weight:700;
                                text-decoration:none;transition:background .15s;
                                font-family:inherit;
                            "
                            onmouseover="this.style.background='#f1f5f9'"
                            onmouseout="this.style.background='#f8fafc'"
                        >
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </a>
                    </div>
                    @endif
                </div>
            @endforeach
            </div>

            {{-- Footer --}}
            <div style="padding:.85rem 1.5rem;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
                <button onclick="vpDismissAll()" style="
                    background:none;border:none;color:#64748b;
                    font-size:.82rem;font-weight:600;cursor:pointer;padding:0;
                    font-family:inherit;
                    display:flex;align-items:center;gap:.3rem;
                ">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Đóng & xem sau
                </button>
                <a href="{{ route('resident.visitors.index') }}" onclick="vpMarkAllRead()" style="
                    display:flex;align-items:center;gap:.35rem;
                    background:linear-gradient(135deg,#00236f,#1e3a8a);
                    color:#fff;text-decoration:none;
                    padding:.5rem 1rem;border-radius:10px;
                    font-size:.82rem;font-weight:700;
                    font-family:inherit;
                ">
                    Xem tất cả khách
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        // ===== HIỆN POPUP 1 LẦN MỖI PHIÊN ĐĂNG NHẬP =====
        // Gắn key theo Laravel session ID → mỗi lần đăng nhập mới sẽ có key mới → popup hiện lại
        const storageKey = 'vp_popup_shown_{{ session()->getId() }}';

        document.addEventListener('DOMContentLoaded', function () {
            const bd = document.getElementById('visitorPopupBackdrop');
            const card = document.getElementById('visitorPopupCard');
            if (!bd || !card) return;

            // Nếu trong phiên này đã hiện popup rồi → ẩn ngay, không show lại
            if (sessionStorage.getItem(storageKey)) {
                bd.remove();
                return;
            }

            // Đánh dấu đã hiện popup trong phiên này
            sessionStorage.setItem(storageKey, '1');

            // Animate mở popup
            requestAnimationFrame(() => {
                bd.style.opacity = '1';
                requestAnimationFrame(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1) translateY(0)';
                });
            });

            // Close on backdrop click
            bd.addEventListener('click', function(e) {
                if (e.target === bd) vpDismissAll();
            });
        });

        function closePopup() {
            const bd = document.getElementById('visitorPopupBackdrop');
            const card = document.getElementById('visitorPopupCard');
            if (!bd || !card) return;
            card.style.opacity = '0';
            card.style.transform = 'scale(.92) translateY(24px)';
            bd.style.opacity = '0';
            setTimeout(() => { bd.remove(); }, 350);
        }

        function vpMarkRead(notifId) {
            fetch(`/resident/notifications/mark-read/${notifId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            }).catch(() => {});
        }

        function vpMarkAllRead() {
            fetch('/resident/notifications/mark-read/all', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            }).catch(() => {});
        }

        function vpRemoveItem(notifId) {
            const item = document.querySelector(`.vp-item[data-notif-id="${notifId}"]`);
            if (item) {
                item.style.transition = 'opacity .25s,transform .25s';
                item.style.opacity = '0';
                item.style.transform = 'translateX(30px)';
                setTimeout(() => {
                    item.remove();
                    if (!document.querySelector('.vp-item')) closePopup();
                }, 260);
            }
        }

        window.vpDismissAll = closePopup;
        window.vpMarkAllRead = vpMarkAllRead;
        window.vpMarkRead = vpMarkRead;

        window.vpAction = function(btn, action, visitorId, notifId) {
            const actionsDiv = btn.closest('.vp-actions');
            if (!actionsDiv) return;

            // Disable buttons during request
            actionsDiv.querySelectorAll('button').forEach(b => b.disabled = true);

            const url = action === 'approve'
                ? `/resident/visitors/${visitorId}/approve`
                : `/resident/visitors/${visitorId}/reject`;

            fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    vpMarkRead(notifId);
                    // Show inline feedback
                    const label = action === 'approve' ? '✓ Đã chấp nhận' : '✗ Đã từ chối';
                    const color = action === 'approve' ? '#059669' : '#ef4444';
                    const bg    = action === 'approve' ? '#dcfce7' : '#fee2e2';
                    actionsDiv.innerHTML = `<div style="
                        width:100%;text-align:center;padding:.45rem;
                        border-radius:10px;background:${bg};
                        color:${color};font-weight:700;font-size:.82rem;
                    ">${label}</div>`;
                    setTimeout(() => vpRemoveItem(notifId), 1400);
                } else {
                    actionsDiv.querySelectorAll('button').forEach(b => b.disabled = false);
                    alert(data.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                }
            })
            .catch(() => {
                actionsDiv.querySelectorAll('button').forEach(b => b.disabled = false);
            });
        };
    })();
    </script>
    @endif
    {{-- ===== END VISITOR WALK-IN POPUP ===== --}}

    {{-- Global Delete Post Confirm Modal --}}
    <div id="globalDeleteConfirmModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s ease;">
        <div style="background: #fff; padding: 28px; border-radius: 16px; max-width: 400px; width: 90%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); position: relative; transform: scale(0.95); transition: transform 0.2s ease; text-align: center; box-sizing: border-box; border: 1px solid #f1f5f9;">
            <div style="width: 56px; height: 56px; background: #fee2e2; color: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 1.5rem;">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <h3 style="margin: 0 0 8px; font-size: 1.2rem; font-weight: 700; color: #0f172a;">Xóa bài đăng này?</h3>
            <p style="margin: 0 0 24px; font-size: 0.9rem; color: #64748b; line-height: 1.5; padding: 0 8px;">Bạn có chắc chắn muốn xóa bài đăng này không? Hành động này không thể hoàn tác.</p>
            <div style="display: flex; justify-content: center; gap: 12px;">
                <button type="button" id="cancelDeleteBtn" style="background: #f1f5f9; color: #475569; border: none; padding: 10px 20px; font-size: 0.88rem; font-weight: 600; border-radius: 10px; cursor: pointer; transition: background 0.2s; width: 100px;">Hủy bỏ</button>
                <button type="button" id="confirmDeleteBtn" style="background: #ef4444; color: white; border: none; padding: 10px 20px; font-size: 0.88rem; font-weight: 600; border-radius: 10px; cursor: pointer; transition: background 0.2s; width: 100px;">Đồng ý</button>
            </div>
        </div>
    </div>

    <script>
    let activeDeleteForm = null;

    function confirmDeletePost(event, form) {
        event.preventDefault();
        activeDeleteForm = form;
        
        const modal = document.getElementById('globalDeleteConfirmModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.offsetHeight; // Force reflow
            modal.style.opacity = '1';
            modal.firstElementChild.style.transform = 'scale(1)';
        }
        return false;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const cancelBtn = document.getElementById('cancelDeleteBtn');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        const modal = document.getElementById('globalDeleteConfirmModal');
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeDeleteModal);
        }
        
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                if (activeDeleteForm) {
                    activeDeleteForm.submit();
                }
                closeDeleteModal();
            });
        }
        
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeDeleteModal();
                }
            });
        }
    });

    function closeDeleteModal() {
        const modal = document.getElementById('globalDeleteConfirmModal');
        if (modal) {
            modal.style.opacity = '0';
            modal.firstElementChild.style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 200);
        }
        activeDeleteForm = null;
    }
    </script>

    {{-- MOBILE BOTTOM NAVIGATION (MATCHING DESIGN) --}}
    <nav class="mobile-bottom-nav" aria-label="Mobile Navigation">
        <a href="{{ route('resident.dashboard') }}" class="mobile-nav-item {{ request()->routeIs('resident.dashboard') ? 'active' : '' }}" title="Trang chủ">
            <i class="fa-solid fa-house"></i>
        </a>
        <a href="{{ route('resident.invoices.index') }}" class="mobile-nav-item {{ request()->routeIs('resident.invoices.*') ? 'active' : '' }}" title="Hóa đơn">
            <i class="fa-solid fa-file-invoice-dollar"></i>
        </a>
        <a href="{{ route('resident.posts.index') }}" class="mobile-nav-item {{ request()->routeIs('resident.posts.*') ? 'active' : '' }}" title="Bản tin">
            <i class="fa-regular fa-newspaper"></i>
        </a>
        <a href="{{ route('resident.tickets.index') }}" class="mobile-nav-item {{ request()->routeIs('resident.tickets.*') ? 'active' : '' }}" title="Phản ánh">
            <i class="fa-solid fa-clipboard-question"></i>
        </a>
        <a href="{{ route('resident.contact') }}" class="mobile-nav-item {{ request()->routeIs('resident.contact') ? 'active' : '' }}" title="Liên hệ">
            <i class="fa-regular fa-comments"></i>
        </a>
    </nav>

    @stack('scripts')
</body>

</html>
