document.addEventListener('DOMContentLoaded', function() {
        const openBtn = document.getElementById('mobMenuOpenBtn');
        const closeBtn = document.getElementById('mobMenuClose');
        const drawer = document.getElementById('mobMenuDrawer');
        const overlay = document.getElementById('mobMenuOverlay');

        function openMenu() {
            drawer.style.left = '0';
            overlay.style.display = 'block';
        }

        function closeMenu() {
            drawer.style.left = '-300px';
            overlay.style.display = 'none';
        }

        if(openBtn) openBtn.addEventListener('click', openMenu);
        if(closeBtn) closeBtn.addEventListener('click', closeMenu);
        if(overlay) overlay.addEventListener('click', closeMenu);
    });