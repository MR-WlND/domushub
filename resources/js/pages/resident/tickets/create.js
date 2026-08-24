document.addEventListener('DOMContentLoaded', function() {
    // ----------------------------------------------------
    // Mobile Menu Logic
    // ----------------------------------------------------
    const mobMenuOpenBtn = document.getElementById('mobMenuOpenBtn');
    const mobMenuClose = document.getElementById('mobMenuClose');
    const mobMenuDrawer = document.getElementById('mobMenuDrawer');
    const mobMenuOverlay = document.getElementById('mobMenuOverlay');

    if (mobMenuOpenBtn) {
        mobMenuOpenBtn.addEventListener('click', function() {
            mobMenuOverlay.style.display = 'block';
            setTimeout(() => { mobMenuDrawer.style.left = '0'; }, 10);
        });
    }

    if (mobMenuClose) {
        mobMenuClose.addEventListener('click', function() {
            mobMenuDrawer.style.left = '-300px';
            setTimeout(() => { mobMenuOverlay.style.display = 'none'; }, 300);
        });
    }

    if (mobMenuOverlay) {
        mobMenuOverlay.addEventListener('click', function() {
            mobMenuDrawer.style.left = '-300px';
            setTimeout(() => { mobMenuOverlay.style.display = 'none'; }, 300);
        });
    }

    // ----------------------------------------------------
    // File Upload Logic
    // ----------------------------------------------------
    let uploadedFiles = new DataTransfer();
    const fileInput = document.getElementById('fileInput');
    const cameraInput = document.getElementById('cameraInput');
    const videoInput = document.getElementById('videoInput');
    const previewContainer = document.getElementById('imagePreview');
    const uploadZone = document.querySelector('.upload-zone');

    function handleFileSelect(event) {
        const files = event.target.files;
        
        for(let i=0; i<files.length; i++) {
            if(uploadedFiles.files.length >= 5) {
                alert('Tối đa 5 file!');
                break;
            }
            uploadedFiles.items.add(files[i]);
        }
        
        if (fileInput) fileInput.files = uploadedFiles.files;
        renderPreviews();
        
        // Clear camera input so it can be reused immediately for another shot
        if (event.target === cameraInput) cameraInput.value = '';
        if (event.target === videoInput) videoInput.value = '';
    }

    if (fileInput) fileInput.addEventListener('change', handleFileSelect);
    if (cameraInput) cameraInput.addEventListener('change', handleFileSelect);
    if (videoInput) videoInput.addEventListener('change', handleFileSelect);

    function renderPreviews() {
        if (!previewContainer || !uploadZone) return;
        
        previewContainer.innerHTML = '';
        
        if (uploadedFiles.files.length >= 5) {
            uploadZone.style.display = 'none';
        } else {
            uploadZone.style.display = 'block';
        }
        
        Array.from(uploadedFiles.files).forEach((file, index) => {
            const div = document.createElement('div');
            div.className = 'upload-preview-item';
            
            // Handle Video or Image preview
            let mediaHtml = '';
            if (file.type.startsWith('video/')) {
                mediaHtml = `<video src="${URL.createObjectURL(file)}" autoplay muted loop playsinline style="width:100%; height:100%; object-fit:cover;"></video>`;
            } else {
                mediaHtml = `<img src="${URL.createObjectURL(file)}" alt="Preview">`;
            }

            div.innerHTML = `
                ${mediaHtml}
                <button type="button" class="upload-preview-remove" data-index="${index}"><i class="fa-solid fa-xmark"></i></button>
            `;
            previewContainer.appendChild(div);
        });

        // Attach event listeners to remove buttons
        document.querySelectorAll('.upload-preview-remove').forEach(btn => {
            btn.addEventListener('click', function(event) {
                event.stopPropagation();
                event.preventDefault();
                const idx = parseInt(this.getAttribute('data-index'));
                removeImage(idx);
            });
        });
    }

    function removeImage(index) {
        const newFiles = new DataTransfer();
        Array.from(uploadedFiles.files).forEach((file, i) => {
            if(i !== index) newFiles.items.add(file);
        });
        uploadedFiles = newFiles;
        fileInput.files = uploadedFiles.files;
        renderPreviews();
    }
});