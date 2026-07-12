@extends('layouts.resident.master')
@section('title', 'Bảng tin cư dân – DomusHub')
@push('styles')
    @vite(['resources/css/pages/resident/home/index.css'])
@endpush

@section('content')
<div class="rh" style="max-width:720px;">

    {{-- Header --}}
    <div class="rh-section">
        <h3 class="rh-section__title" style="font-size:1.4rem;">Bài viết từ cư dân</h3>
        <div class="rh-section__actions">
            <a href="{{ route('resident.posts.index') }}" class="rh-section__link">Xem tất cả</a>
            <a href="{{ route('resident.posts.create') }}" class="rh-section__btn">+ Đăng bài</a>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div style="padding:12px 18px;background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;border-radius:10px;font-size:0.85rem;font-weight:600;margin-bottom:16px;">{{ session('success') }}</div>
    @endif

    {{-- Filter --}}
    <div style="display:flex;gap:16px;margin-bottom:16px;border-bottom:1px solid #f1f5f9;padding-bottom:12px;">
        <a href="{{ route('resident.posts.index') }}" style="font-size:0.85rem;font-weight:{{ !request()->filled('type') ? '700' : '500' }};color:{{ !request()->filled('type') ? '#0f172a' : '#64748b' }};text-decoration:none;">Tất cả</a>
        <a href="{{ route('resident.posts.index', ['type' => 'mine']) }}" style="font-size:0.85rem;font-weight:{{ request('type')==='mine' ? '700' : '500' }};color:{{ request('type')==='mine' ? '#0f172a' : '#64748b' }};text-decoration:none;">Của tôi</a>
    </div>

    {{-- Feed --}}
    <div class="rh-fb-feed" style="max-width:100%;">
        @forelse($posts as $post)
        <div class="rh-fb-card">
            <div class="rh-fb-card__header">
                <div class="rh-fb-card__avatar">
                    @if($post->user->avatar)
                        <img src="{{ asset('storage/' . $post->user->avatar) }}" alt="" loading="lazy">
                    @else
                        <span>{{ mb_substr($post->user->name ?? '?', 0, 1) }}</span>
                    @endif
                </div>
                <div>
                    <span class="rh-fb-card__name">{{ $post->user->name ?? 'Cư dân' }}</span>
                    <span class="rh-fb-card__time">{{ $post->created_at->diffForHumans() }}</span>
                </div>
            </div>

            @if($post->content)
            <div class="rh-fb-card__text">
                {{ Str::limit(strip_tags($post->content), 300) }}
                @if(strlen(strip_tags($post->content)) > 300)
                    <a href="{{ route('resident.posts.show', $post->id) }}" class="rh-fb-card__more-text">Xem thêm</a>
                @endif
            </div>
            @endif

            @if($post->images->isNotEmpty())
            <a href="{{ route('resident.posts.show', $post->id) }}" class="rh-fb-card__media">
                @php $imgCount = $post->images->count(); @endphp
                @if($imgCount === 1)
                    <img src="{{ asset('storage/' . $post->images[0]->image_path) }}" class="rh-fb-card__img-single" alt="" loading="lazy">
                @elseif($imgCount === 2)
                    <div class="rh-fb-card__img-grid rh-fb-card__img-grid--2">
                        @foreach($post->images->take(2) as $img)
                            <img src="{{ asset('storage/' . $img->image_path) }}" alt="" loading="lazy">
                        @endforeach
                    </div>
                @else
                    <div class="rh-fb-card__img-grid rh-fb-card__img-grid--3plus">
                        <img src="{{ asset('storage/' . $post->images[0]->image_path) }}" class="rh-fb-card__img-main" alt="" loading="lazy">
                        <div class="rh-fb-card__img-side">
                            @foreach($post->images->slice(1, 2) as $img)
                                <img src="{{ asset('storage/' . $img->image_path) }}" alt="" loading="lazy">
                            @endforeach
                            @if($imgCount > 3)
                                <div class="rh-fb-card__img-overlay">+{{ $imgCount - 3 }}</div>
                            @endif
                        </div>
                    </div>
                @endif
            </a>
            @endif

            <div class="rh-fb-card__stats">
                <span>{{ $post->likes_count ?? 0 }} lượt thích</span>
                <span>{{ $post->comments_count ?? 0 }} bình luận</span>
            </div>

            <div class="rh-fb-card__actions">
                <button type="button" class="rh-fb-card__action rh-fb-like-btn {{ $post->likedByCurrentUser->isNotEmpty() ? 'rh-fb-like-btn--active' : '' }}" data-post-id="{{ $post->id }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                    Thích
                </button>
                <a href="{{ route('resident.posts.show', $post->id) }}" class="rh-fb-card__action">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    Bình luận
                </a>
            </div>
        </div>
        @empty
        <div style="text-align:center;padding:48px;color:#94a3b8;">
            <p style="font-size:0.95rem;margin:0 0 12px;">Chưa có bài viết nào</p>
            <a href="{{ route('resident.posts.create') }}" class="rh-section__btn">Đăng bài đầu tiên</a>
        </div>
        @endforelse

        @if($posts->hasPages())
        <div style="margin-top:20px;">{{ $posts->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.rh-fb-like-btn').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        const postId = this.dataset.postId;
        const card = this.closest('.rh-fb-card');
        const statsEl = card.querySelector('.rh-fb-card__stats span:first-child');
        try {
            const res = await fetch('{{ route("resident.posts.like") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: JSON.stringify({ likeable_id: postId, likeable_type: 'post', type: 'like' })
            });
            const data = await res.json();
            if (data.likes_count !== undefined) statsEl.textContent = data.likes_count + ' lượt thích';
            if (data.liked) { this.classList.add('rh-fb-like-btn--active'); }
            else { this.classList.remove('rh-fb-like-btn--active'); }
        } catch(e) { console.error(e); }
    });
});
</script>
@endpush
@endsection
