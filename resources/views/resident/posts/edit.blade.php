@extends('layouts.resident.master')
@section('title', 'Chỉnh sửa bài viết – DomusHub')
@push('styles')
    @vite(['resources/css/pages/resident/home/index.css'])
    <style>
        .ck-editor__editable_inline {
            min-height: 180px;
            color: #0f172a;
        }
        .media-preview-card {
            position: relative;
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .media-preview-card img, .media-preview-card video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .media-preview-card .delete-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(220, 38, 38, 0.9);
            color: #fff;
            border: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            font-size: 0.75rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            padding: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
            transition: background 0.15s;
        }
        .media-preview-card .delete-btn:hover {
            background: #b91c1c;
        }
    </style>
@endpush

@section('content')
<div class="rh" style="max-width:680px;">

    <div style="margin-bottom:24px;">
        <a href="{{ route('resident.posts.show', $post->id) }}" style="font-size:0.82rem;color:#2563eb;text-decoration:none;font-weight:600;display:inline-flex;align-items:center;gap:4px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            Quay lại chi tiết bài viết
        </a>
        <h1 style="margin:8px 0 0;font-size:1.4rem;font-weight:800;color:#0f172a;">Chỉnh sửa bài viết</h1>
    </div>

    @if($errors->any())
    <div style="padding:12px 18px;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:10px;font-size:0.85rem;margin-bottom:16px;">
        @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
    </div>
    @endif

    <div class="rh-fb-card" style="padding:24px;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.05);border:1px solid #f1f5f9;">
        <form id="edit-post-form" action="{{ route('resident.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Author information --}}
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
                <div style="width:44px;height:44px;border-radius:50%;overflow:hidden;background:#e0e7ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <span style="font-weight:700;color:#3730a3;font-size:1.1rem;">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    @endif
                </div>
                <div>
                    <span style="display:block;font-weight:700;color:#0f172a;font-size:0.92rem;">{{ auth()->user()->name }}</span>
                    <span style="display:block;font-size:0.75rem;color:#64748b;">Cư dân</span>
                </div>
            </div>

            <input type="hidden" name="title" id="edit-post-title" value="{{ $post->title }}">

            {{-- Content --}}
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:0.85rem;font-weight:600;color:#334155;margin-bottom:6px;">Nội dung bài viết</label>
                <textarea name="content" id="editor-post-content" rows="5" placeholder="Bạn đang muốn chia sẻ điều gì với cư dân hôm nay..." style="width:100%;border:1px solid #e2e8f0;border-radius:10px;padding:14px;font-size:0.9rem;color:#0f172a;outline:none;resize:vertical;font-family:inherit;box-sizing:border-box;">{{ old('content', $post->content) }}</textarea>
            </div>

            {{-- Existing media --}}
            @if($post->images->isNotEmpty())
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:0.85rem;font-weight:600;color:#334155;margin-bottom:8px;">Ảnh/video hiện tại (nhấp nút đỏ để xóa)</label>
                <div id="existing-media-container" style="display:flex;gap:12px;flex-wrap:wrap;">
                    @foreach($post->images as $img)
                        <div class="media-preview-card" id="existing-media-{{ $img->id }}">
                            @if($img->type === 'video')
                                <video src="{{ asset('storage/' . $img->image_path) }}"></video>
                            @else
                                <img src="{{ asset('storage/' . $img->image_path) }}" alt="">
                            @endif
                            <button type="button" class="delete-btn" onclick="markMediaForDeletion({{ $img->id }})" title="Xóa file này">&times;</button>
                        </div>
                    @endforeach
                </div>
                <div id="deleted-media-inputs"></div>
            </div>
            @endif

            {{-- New media upload --}}
            <div style="margin-bottom:24px;">
                <label style="display:block;font-size:0.85rem;font-weight:600;color:#334155;margin-bottom:8px;">Đính kèm thêm ảnh/video</label>
                <label style="display:flex;align-items:center;gap:8px;padding:14px 18px;border:2px dashed #e2e8f0;border-radius:10px;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.borderColor='#2563eb'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <span style="font-size:0.85rem;font-weight:600;color:#334155;">Chọn ảnh/video (tối đa 5 file tổng cộng)</span>
                    <input type="file" name="media[]" id="new-media-input" multiple accept="image/*,video/*" style="display:none;" onchange="previewMedia(this)">
                </label>
                <div id="new-media-previews" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;"></div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:12px;padding-top:16px;border-top:1px solid #f1f5f9;">
                <a href="{{ route('resident.posts.show', $post->id) }}" style="padding:10px 20px;font-size:0.85rem;font-weight:600;color:#475569;text-decoration:none;">Hủy</a>
                <button type="submit" style="padding:10px 24px;background:#00236F;color:#fff;border:none;border-radius:10px;font-size:0.88rem;font-weight:700;cursor:pointer;transition:background 0.2s;" onmouseover="this.style.background='#001a54'" onmouseout="this.style.background='#00236F'">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
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
function markMediaForDeletion(mediaId) {
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
function previewMedia(input) {
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
</script>
@endsection
