<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'DomusHub Resident')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/css/layouts/resident.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="resident-page">
    @include('layouts.resident.partials.header')

    <div class="resident-layout">
        <main class="resident-content">
            @yield('content')
        </main>

        @include('layouts.resident.partials.footer')
    </div>

    @include('layouts.resident.partials.chatbot')

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

    @stack('scripts')
</body>

</html>
