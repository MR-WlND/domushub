function openImgModal(src) {
    document.getElementById('tkModalImg').src = src;
    document.getElementById('tkImgModal').style.display = 'flex';
}
function closeImgModal() {
    document.getElementById('tkImgModal').style.display = 'none';
}

function scrollToFeedback() {
    const el = document.getElementById('feedbackSection');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Star rating logic
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('#starRating .star-item');
    const input = document.getElementById('ratingValue');
    const hint = document.getElementById('ratingHint');
    const hints = ['', 'Rất tệ', 'Chưa hài lòng', 'Bình thường', 'Hài lòng', 'Xuất sắc!'];

    if (stars.length > 0) {
        function updateStars(val) {
            stars.forEach((s, idx) => {
                if (idx < val) {
                    s.style.color = '#f59e0b';
                } else {
                    s.style.color = '#cbd5e1';
                }
            });
            if (hint) hint.textContent = hints[val] || '';
        }

        updateStars(parseInt(input.value || 5));

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const val = parseInt(this.getAttribute('data-val'));
                if (input) input.value = val;
                updateStars(val);
            });
        });
    }
});