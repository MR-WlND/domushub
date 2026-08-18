document.addEventListener('DOMContentLoaded', function() {
    const editTA = document.getElementById('editor-post-content');
    if (editTA && typeof ClassicEditor !== 'undefined') {
        ClassicEditor.create(editTA, {
            toolbar: ['bold', 'italic', 'underline', 'bulletedList', 'numberedList', 'undo', 'redo'],
            placeholder: 'Bạn đang muốn chia sẻ điều gì với cư dân hôm nay...'
        }).catch(err => console.error(err));
    }
});

// Mark existing media for deletion
const deletedMediaIds = new Set();
window.markMediaForDeletion = function(mediaId) {
    deletedMediaIds.add(mediaId);
    
    // Hide visual card
    const card = document.getElementById(`existing-media-${mediaId}`);
    if (card) {
        card.style.display = 'none';
    }

    // Add hidden input to form
    const container = document.getElementById('deleted-media-inputs');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'delete_media[]';
    input.value = mediaId;
    input.id = `delete-media-input-${mediaId}`;
    container.appendChild(input);
}

// Preview new media uploads
window.previewMedia = function(input) {
    const container = document.getElementById('new-media-previews');
    container.innerHTML = '';
    if (!input.files) return;

    // Validate total count
    const existingCount = document.querySelectorAll('#existing-media-container .media-preview-card').length - deletedMediaIds.size;
    const newCount = input.files.length;
    if (existingCount + newCount > 5) {
        alert('Tổng số hình ảnh và video cho mỗi bài đăng không được vượt quá 5.');
        input.value = '';
        return;
    }

    Array.from(input.files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrap = document.createElement('div');
                wrap.className = 'media-preview-card';
                wrap.innerHTML = `<img src="${e.target.result}" alt="">`;
                container.appendChild(wrap);
            };
            reader.readAsDataURL(file);
        } else if (file.type.startsWith('video/')) {
            const wrap = document.createElement('div');
            wrap.className = 'media-preview-card';
            wrap.style.background = '#0f172a';
            wrap.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="white"><polygon points="5 3 19 12 5 21 5 3"/></svg><span style="position:absolute;bottom:2px;right:4px;font-size:0.6rem;color:#fff;background:rgba(0,0,0,0.6);padding:1px 4px;border-radius:3px;">Video</span>';
            container.appendChild(wrap);
        }
    });
}