let uploadedFiles = new DataTransfer();

function previewImages(event) {
    const files = event.target.files;
    
    for(let i=0; i<files.length; i++) {
        if(uploadedFiles.files.length >= 5) {
            alert('Tối đa 5 ảnh!');
            break;
        }
        uploadedFiles.items.add(files[i]);
    }
    
    document.getElementById('fileInput').files = uploadedFiles.files;
    renderPreviews();
}

function renderPreviews() {
    const previewContainer = document.getElementById('imagePreview');
    const uploadZone = document.querySelector('.upload-zone');
    previewContainer.innerHTML = '';
    
    if (uploadedFiles.files.length > 0) {
        uploadZone.style.display = 'none';
    } else {
        uploadZone.style.display = 'block';
    }
    
    Array.from(uploadedFiles.files).forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'upload-preview-item';
        div.innerHTML = `
            <img src="${URL.createObjectURL(file)}" alt="Preview">
            <button type="button" class="upload-preview-remove" onclick="removeImage(${index})"><i class="fa-solid fa-xmark"></i></button>
        `;
        previewContainer.appendChild(div);
    });

    if (uploadedFiles.files.length > 0 && uploadedFiles.files.length < 5) {
        const addBtn = document.createElement('div');
        addBtn.className = 'upload-preview-item add-more-btn';
        addBtn.style.display = 'flex';
        addBtn.style.alignItems = 'center';
        addBtn.style.justifyContent = 'center';
        addBtn.style.background = '#f8fafc';
        addBtn.style.border = '2px dashed #cbd5e1';
        addBtn.style.cursor = 'pointer';
        addBtn.innerHTML = '<i class="fa-solid fa-plus" style="font-size: 24px; color: #94a3b8;"></i>';
        addBtn.onclick = function() { document.getElementById('fileInput').click(); };
        previewContainer.appendChild(addBtn);
    }
}

function removeImage(index) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }
    const newFiles = new DataTransfer();
    Array.from(uploadedFiles.files).forEach((file, i) => {
        if(i !== index) newFiles.items.add(file);
    });
    uploadedFiles = newFiles;
    document.getElementById('fileInput').files = uploadedFiles.files;
    renderPreviews();
}

document.addEventListener('DOMContentLoaded', function() {
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
});