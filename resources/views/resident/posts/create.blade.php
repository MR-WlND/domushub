@extends('layouts.resident.master')
@section('title', 'Đăng bài viết – DomusHub')
@push('styles')
    @vite(['resources/css/pages/resident/home/index.css'])
    <style>
        .ck-editor__editable_inline {
            min-height: 180px;
            color: #0f172a;
        }
    </style>
@endpush

@section('content')
<div class="rh" style="max-width:680px;">

    <div style="margin-bottom:24px;">
        <a href="{{ route('resident.dashboard') }}" style="font-size:0.82rem;color:#2563eb;text-decoration:none;font-weight:600;">← Quay lại bảng tin</a>
        <h1 style="margin:8px 0 0;font-size:1.4rem;font-weight:800;color:#0f172a;">Đăng bài viết mới</h1>
    </div>

    @if($errors->any())
    <div style="padding:12px 18px;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;border-radius:10px;font-size:0.85rem;margin-bottom:16px;">
        @foreach($errors->all() as $error)<div>• {{ $error }}</div>@endforeach
    </div>
    @endif

    <div class="rh-fb-card" style="padding:24px;">
        <form action="{{ route('resident.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Author --}}
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


            {{-- Content --}}
            <div style="margin-bottom:20px;">
                <textarea name="content" id="editor-post-content" rows="5" placeholder="Bạn đang muốn chia sẻ điều gì với cư dân hôm nay..." style="width:100%;border:1px solid #e2e8f0;border-radius:10px;padding:14px;font-size:0.9rem;color:#0f172a;outline:none;resize:vertical;font-family:inherit;box-sizing:border-box;">{{ old('content') }}</textarea>
            </div>

            {{-- Media upload --}}
            <div style="margin-bottom:20px;">
                <label style="display:flex;align-items:center;gap:8px;padding:14px 18px;border:2px dashed #e2e8f0;border-radius:10px;cursor:pointer;transition:border-color 0.2s;" onmouseover="this.style.borderColor='#2563eb'" onmouseout="this.style.borderColor='#e2e8f0'">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <span style="font-size:0.85rem;font-weight:600;color:#334155;">Đính kèm ảnh/video (có thể chọn nhiều)</span>
                    <input type="file" name="media[]" multiple accept="image/*,video/*" style="display:none;" onchange="previewMedia(this)">
                </label>
                <div id="media-previews" style="display:flex;gap:8px;flex-wrap:wrap;margin-top:10px;"></div>
            </div>

            {{-- Actions --}}
            <div style="display:flex;align-items:center;justify-content:flex-end;gap:12px;padding-top:16px;border-top:1px solid #f1f5f9;">
                <a href="{{ route('resident.dashboard') }}" style="padding:10px 20px;font-size:0.85rem;font-weight:600;color:#475569;text-decoration:none;">Hủy</a>
                <button type="submit" style="padding:10px 24px;background:#00236F;color:#fff;border:none;border-radius:10px;font-size:0.88rem;font-weight:700;cursor:pointer;">Đăng bài</button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const createTA = document.getElementById('editor-post-content');
    if (createTA) {
        ClassicEditor.create(createTA, {
            toolbar: ['bold', 'italic', 'underline', 'bulletedList', 'numberedList', 'undo', 'redo'],
            placeholder: 'Bạn đang muốn chia sẻ điều gì với cư dân hôm nay...'
        }).catch(err => console.error(err));
    }
});
</script>
<script>
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
</script>
@endsection
