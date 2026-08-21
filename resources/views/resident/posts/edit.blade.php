@extends('layouts.resident.master')
@section('title', 'Chỉnh sửa bài viết – DomusHub')
@push('styles')
    <style>
        body {
            background-color: #f8fafc;
        }
        .edit-post-container {
            max-width: 680px;
            margin: 40px auto;
            padding: 0 16px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 12px;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #0f172a;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #000;
            margin: 0 0 24px 0;
        }
        .post-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .author-info {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .author-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .author-avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #3730a3;
            font-size: 1.1rem;
        }
        .author-details {
            display: flex;
            flex-direction: column;
        }
        .author-name {
            font-weight: 600;
            color: #000;
            font-size: 0.95rem;
        }
        .author-meta {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.8rem;
            color: #64748b;
        }

        /* Tùy chỉnh giao diện CKEditor cho gọn gàng và liền mạch */
        .ck-editor__editable_inline {
            border: none !important;
            box-shadow: none !important;
            padding: 0 4px !important;
            min-height: 100px;
            font-family: inherit !important;
            font-size: 1.05rem !important;
        }
        .ck-editor__editable.ck-focused:not(.ck-editor__nested-editable) {
            border: none !important;
            box-shadow: none !important;
        }
        .ck.ck-toolbar {
            background: transparent !important;
            border: none !important;
            padding: 0 0 8px 0 !important;
            margin-bottom: 12px !important;
        }
        
        .content-textarea {
            width: 100%;
            border: none;
            outline: none;
            resize: none;
            font-size: 0.95rem;
            color: #000;
            font-family: inherit;
            padding: 0;
            margin-bottom: 20px;
            min-height: 80px;
            box-sizing: border-box;
        }
        .media-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }
        .media-preview-card {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            width: 80px;
            height: 80px;
            background: #f8fafc;
            flex-shrink: 0;
        }
        .media-preview-card img, .media-preview-card video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .delete-btn {
            position: absolute;
            top: 6px;
            right: 6px;
            background: rgba(255, 255, 255, 0.9);
            color: #000;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            padding: 0;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            z-index: 10;
        }
        .delete-btn:hover {
            background: #fff;
        }
        .add-media-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #000;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background 0.2s;
            margin-bottom: 24px;
        }
        .add-media-btn:hover {
            background: #f8fafc;
        }
        .action-buttons {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid #f1f5f9;
        }
        .cancel-btn {
            padding: 8px 16px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #64748b;
            text-decoration: none;
            border-radius: 6px;
            background: transparent;
        }
        .cancel-btn:hover {
            color: #0f172a;
        }
        .save-btn {
            padding: 8px 16px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .save-btn:hover {
            opacity: 0.9;
        }
    </style>
    @vite(['resources/css/pages/resident/posts/edit.css'])
@endpush

@section('content')
<div class="edit-post-container">
    <a href="{{ route('resident.posts.index') }}" class="back-link">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Quay lại
    </a>
    
    <h1 class="page-title">Chỉnh sửa bài viết</h1>

    @if($errors->any())
    <div style="padding:12px 18px;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:10px;font-size:0.85rem;margin-bottom:16px;">
        @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
    </div>
    @endif

    <div class="post-card">
        <form id="edit-post-form" action="{{ route('resident.posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-post-id" value="{{ $post->id }}">

            <div class="author-info">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="author-avatar" alt="Avatar">
                @else
                    <div class="author-avatar-placeholder">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
                @endif
                <div class="author-details">
                    <span class="author-name">{{ auth()->user()->name }}</span>
                    <span class="author-meta">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        Công khai
                    </span>
                </div>
            </div>

            <textarea name="content" id="editor-post-content" class="content-textarea" placeholder="Bạn đang muốn chia sẻ điều gì với cư dân hôm nay...">{{ old('content', $post->content) }}</textarea>

            <div class="media-grid" id="existing-media-container">
                @foreach($post->images as $img)
                    <div class="media-preview-card" id="existing-media-{{ $img->id }}">
                        @if($img->type === 'video')
                            <video src="{{ asset('storage/' . $img->image_path) }}"></video>
                        @else
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="" onerror="this.parentElement.style.display='none';">
                        @endif
                        <button type="button" class="delete-btn" onclick="markMediaForDeletion({{ $img->id }})" title="Xóa file này">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                        </button>
                    </div>
                @endforeach
            </div>
            <div id="deleted-media-inputs"></div>

            <div class="media-grid" id="new-media-previews"></div>

            <label class="add-media-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                Thêm ảnh/video
                <input type="file" name="media[]" id="new-media-input" multiple accept="image/*,video/*" style="display:none;" onchange="previewMedia(this)">
            </label>

            <div class="action-buttons">
                <a href="{{ route('resident.posts.index') }}" class="cancel-btn">Hủy</a>
                <button type="submit" class="save-btn">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    @vite(['resources/js/pages/resident/posts/edit.js'])
@endpush
