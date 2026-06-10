<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'DomusHub')</title>
    @vite(['resources/css/app.css', 'resources/css/layouts/admin.css'])
    @stack('styles')
</head>

<body class="dashboard-page">
    {{-- Mobile sidebar overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="dashboard-shell">
        @include('layouts.admin.partials.sidebar')

        <main class="dashboard-main">
            @include('layouts.admin.partials.header')

            <section class="dashboard-content">
                @yield('content')
            </section>
        </main>
    </div>

    @stack('scripts')
    <script>
        function openSidebar() {
            document.getElementById('dashboardSidebar').classList.add('sidebar--open');
            document.getElementById('sidebarOverlay').classList.add('sidebar-overlay--visible');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            document.getElementById('dashboardSidebar').classList.remove('sidebar--open');
            document.getElementById('sidebarOverlay').classList.remove('sidebar-overlay--visible');
            document.body.style.overflow = '';
        }
    </script>

    <script>
    (function () {
        const notifUrl    = '{{ route('admin.notifications.recent') }}';
        const markReadUrl = '{{ route('admin.notifications.mark-read') }}';
        const csrfToken   = '{{ csrf_token() }}';

        const wrapper  = document.getElementById('notif-wrapper');
        const badge    = document.getElementById('notif-badge');
        const dropdown = document.getElementById('notif-dropdown');
        const list     = document.getElementById('notif-list');

        if (!wrapper) return;

        // Toggle dropdown
        wrapper.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = dropdown.style.display === 'block';
            dropdown.style.display = isOpen ? 'none' : 'block';
            if (!isOpen) markRead();
        });
        document.addEventListener('click', () => dropdown.style.display = 'none');

        function markRead() {
            badge.style.display = 'none';
            fetch(markReadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
            });
        }

        async function fetchNotifications() {
            try {
                const res  = await fetch(notifUrl);
                const data = await res.json();

                // Cập nhật badge
                if (data.unread_count > 0) {
                    badge.textContent    = data.unread_count > 9 ? '9+' : data.unread_count;
                    badge.style.display  = 'flex';
                } else {
                    badge.style.display  = 'none';
                }

                // Cập nhật danh sách
                if (!data.payments || data.payments.length === 0) {
                    list.innerHTML = '<div style="padding:20px; text-align:center; color:#94a3b8; font-size:0.85rem;">Không có giao dịch mới trong 24h</div>';
                    return;
                }

                list.innerHTML = data.payments.map(p => `
                    <div style="padding:12px 16px; border-bottom:1px solid #f1f5f9; display:flex; gap:12px; align-items:flex-start;">
                        <div style="width:36px; height:36px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1rem;">✅</div>
                        <div style="flex:1; min-width:0;">
                            <div style="font-size:0.85rem; font-weight:600; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${p.invoice_title}</div>
                            <div style="font-size:0.78rem; color:#64748b; margin-top:2px;">
                                Căn hộ ${p.apartment} &bull; <span style="color:#${p.method==='MB Bank'?'1d4ed8':'7e22ce'}; font-weight:600;">${p.method}</span>
                            </div>
                            <div style="font-size:0.75rem; color:#94a3b8; margin-top:1px;">${p.paid_at_full}</div>
                        </div>
                        <div style="font-size:0.9rem; font-weight:700; color:#15803d; white-space:nowrap;">+${p.amount}</div>
                    </div>
                `).join('');
            } catch (e) { /* silent */ }
        }

        // Chạy ngay khi load và poll mỗi 15 giây
        fetchNotifications();
        setInterval(fetchNotifications, 15000);
    })();
    </script>
</body>

</html>
