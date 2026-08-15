document.addEventListener('DOMContentLoaded', function() {
    const createTA = document.getElementById('editor-post-content');
    if (createTA) {
        ClassicEditor.create(createTA, {
            toolbar: ['bold', 'italic', 'underline', 'bulletedList', 'numberedList', 'undo', 'redo'],
            placeholder: 'Bạn đang muốn chia sẻ điều gì với cư dân hôm nay...'
        }).catch(err => console.error(err));
    }
});

function previewMedia(input) {
    const container = document.getElementById('media-previews');
    container.innerHTML = '';
    if (!input.files) return;
    Array.from(input.files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.cssText = 'width:64px;height:64px;object-fit:cover;border-radius:8px;border:1px solid #e2e8f0;';
                container.appendChild(img);
            };
            reader.readAsDataURL(file);
        } else if (file.type.startsWith('video/')) {
            const wrap = document.createElement('div');
            wrap.style.cssText = 'width:64px;height:64px;border-radius:8px;border:1px solid #e2e8f0;background:#0f172a;display:flex;align-items:center;justify-content:center;position:relative;';
            wrap.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="white"><polygon points="5 3 19 12 5 21 5 3"/></svg><span style="position:absolute;bottom:2px;right:4px;font-size:0.6rem;color:#fff;background:rgba(0,0,0,0.6);padding:1px 4px;border-radius:3px;">Video</span>';
            container.appendChild(wrap);
        }
    });
}