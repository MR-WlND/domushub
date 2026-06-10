<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\PostReport;
use App\Models\CommentReport;
use App\Models\PostImage;
use App\Models\Like;
use App\Events\PostUpdated;
use App\Events\CommentUpdated;
use App\Events\LikeToggled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Hiển thị bảng tin cộng đồng cư dân
     */
    public function index(Request $request)
    {
        $query = Post::with(['user.apartment', 'comments.user.apartment', 'images'])
            ->withCount('likes')
            ->with(['likedByCurrentUser'])
            ->where('status', 'published')
            ->whereDoesntHave('reports', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc');
 
        // Lọc bài viết theo loại (Ví dụ: Tìm kiếm từ khóa)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }
 
        // Lọc bài viết thanh lý (có giá)
        if ($request->get('type') === 'marketplace') {
            $query->whereNotNull('price');
        } elseif ($request->get('type') === 'general') {
            $query->whereNull('price');
        }
 
        $posts = $query->paginate(10)->withQueryString();
 
        return view('resident.posts.index', compact('posts'));
    }

    /**
     * Đăng bài viết mới
     */
    public function store(Request $request)
    {
        // 1. Xử lý định dạng tiền tệ trước khi validate
        if ($request->filled('price')) {
            // Loại bỏ dấu phân cách hàng nghìn (chấm/phẩy) và khoảng trắng để lấy số thuần
            $cleanPrice = preg_replace('/[^0-9]/', '', $request->price);
            $request->merge(['price' => $cleanPrice]);
        }

        // 2. Validate dữ liệu đầu vào (hỗ trợ images dạng mảng)
        $request->validate([
            'title' => 'nullable|string|max:200',
            'content' => 'required|string',
            'price' => 'nullable|numeric|min:0|max:999999999',
            'images' => 'nullable|array|max:5', // Tối đa 5 ảnh
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Mỗi ảnh tối đa 2MB
        ], [
            'title.max' => 'Tiêu đề không được vượt quá 200 ký tự.',
            'content.required' => 'Vui lòng nhập nội dung bài đăng.',
            'price.numeric' => 'Giá bán phải là định dạng số.',
            'price.min' => 'Giá bán không được nhỏ hơn 0đ.',
            'images.array' => 'Hình ảnh đính kèm phải ở dạng danh sách.',
            'images.max' => 'Bạn chỉ được đính kèm tối đa 5 hình ảnh cho mỗi bài viết.',
            'images.*.image' => 'File tải lên phải là hình ảnh.',
            'images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'images.*.max' => 'Dung lượng mỗi hình ảnh không được vượt quá 2MB.',
        ]);

        $data = $request->only(['title', 'content', 'price']);
        
        // Tạo tiêu đề tự động nếu người dùng để trống
        if (!$request->filled('title')) {
            $data['title'] = Str::limit(strip_tags($request->content), 40, '...');
        }
        
        $data['user_id'] = Auth::id();
        $data['status'] = 'published'; // Mặc định là published
        $data['ai_flagged'] = 'clean';   // Mặc định là clean

        $post = Post::create($data);

        // Xử lý upload danh sách ảnh nếu có
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('posts', 'public');
                $post->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        // Phát sự kiện real-time sau khi đã upload xong hình ảnh
        broadcast(new \App\Events\PostCreated($post));

        return redirect()->route('resident.posts.index')->with('success', 'Đăng bài viết lên bảng tin thành công!');
    }

    /**
     * Xem chi tiết bài viết kèm bình luận
     */
    public function show($id)
    {
        $post = Post::with(['user.apartment', 'images'])
            ->withCount('likes')
            ->with(['likedByCurrentUser'])
            ->findOrFail($id);

        // Chặn xem nếu cư dân này đã báo cáo bài viết này
        $hasReported = $post->reports()->where('user_id', Auth::id())->exists();
        if ($hasReported) {
            abort(403, 'Bạn đã báo cáo bài viết này và không thể xem lại nội dung.');
        }

        // Đếm tổng số bình luận cha hợp lệ (parent_id = null)
        $totalComments = Comment::where('post_id', $post->id)
            ->whereNull('parent_id')
            ->whereDoesntHave('reports', function($rq) {
                $rq->where('user_id', Auth::id());
            })
            ->count();

        // Tải 10 bình luận cha mới nhất kèm các phản hồi con
        $comments = Comment::with(['user.apartment', 'replies' => function($q) {
                $q->with(['user.apartment', 'parent.user'])
                  ->withCount('likes')
                  ->with(['likedByCurrentUser'])
                  ->orderBy('created_at', 'asc');
            }])
            ->withCount('likes')
            ->with(['likedByCurrentUser'])
            ->where('post_id', $post->id)
            ->whereNull('parent_id')
            ->whereDoesntHave('reports', function($rq) {
                $rq->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->reverse();

        return view('resident.posts.show', compact('post', 'comments', 'totalComments'));
    }

    /**
     * Xóa bài viết (Chống IDOR bằng cách kiểm tra quyền sở hữu hoặc admin)
     */
    public function destroy($id)
    {
        $post = Post::with('images')->findOrFail($id);

        // Bảo mật tuyệt đối: chỉ tác giả hoặc admin portal mới được quyền xóa bài viết
        if ($post->user_id !== Auth::id() && !Auth::user()->isAdminPortalUser()) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        // Xóa các file ảnh vật lý đính kèm trên ổ cứng
        foreach ($post->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $post->delete();

        return redirect()->route('resident.posts.index')->with('success', 'Xóa bài viết thành công!');
    }

    /**
     * Viết bình luận mới
     */
    public function storeComment(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
        ], [
            'content.required' => 'Vui lòng nhập nội dung bình luận.',
        ]);

        $post = Post::findOrFail($postId);

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
        ]);

        // Phát sự kiện real-time cho bình luận mới
        broadcast(new \App\Events\CommentCreated($comment));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng bình luận thành công!',
                'comment' => $comment->load(['user.apartment', 'parent.user']),
            ]);
        }

        return redirect()->back()->with('success', 'Đăng bình luận thành công!');
    }

    /**
     * Xóa bình luận (Chống IDOR bằng cách kiểm tra quyền sở hữu, chủ bài đăng hoặc admin)
     */
    public function destroyComment($id)
    {
        $comment = Comment::findOrFail($id);

        // Bảo mật tuyệt đối: chỉ người viết bình luận, chủ bài đăng đó hoặc admin mới được quyền xóa
        if ($comment->user_id !== Auth::id() && 
            $comment->post->user_id !== Auth::id() && 
            !Auth::user()->isAdminPortalUser()) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        $comment->delete();

        return redirect()->back()->with('success', 'Xóa bình luận thành công!');
    }

    /**
     * Báo cáo bài viết vi phạm
     */
    public function report(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ], [
            'reason.required' => 'Vui lòng chọn hoặc nhập lý do báo cáo.',
            'reason.max' => 'Lý do báo cáo không quá 255 ký tự.',
        ]);

        $post = Post::findOrFail($id);

        // Cư dân không thể báo cáo bài viết của chính mình
        if ($post->user_id === Auth::id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể báo cáo bài đăng của chính mình.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Bạn không thể báo cáo bài đăng của chính mình.');
        }

        // Tạo hoặc tìm bản ghi báo cáo (đảm bảo tính Unique)
        PostReport::firstOrCreate(
            [
                'post_id' => $post->id,
                'user_id' => Auth::id(),
            ],
            [
                'reason' => $request->reason,
            ]
        );

        // Đếm tổng số cư dân khác nhau đã báo cáo bài này
        $reportsCount = $post->reports()->count();

        // Nếu nhận đủ 5 báo cáo trở lên, tự động ẩn bài viết đối với mọi người
        $hiddenGlobally = false;
        if ($reportsCount >= 5) {
            $post->status = 'hidden';
            $post->save();
            $hiddenGlobally = true;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $hiddenGlobally
                    ? 'Báo cáo thành công. Bài viết này đã bị ẩn tạm thời do nhận quá nhiều lượt báo cáo vi phạm.'
                    : 'Báo cáo bài viết thành công. Ban quản lý sẽ sớm xem xét bài đăng này!',
                'hidden_globally' => $hiddenGlobally
            ]);
        }

        return redirect()->route('resident.posts.index')->with('success', 'Báo cáo bài đăng thành công!');
    }

    /**
     * Báo cáo bình luận vi phạm
     */
    public function reportComment(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ], [
            'reason.required' => 'Vui lòng chọn hoặc nhập lý do báo cáo.',
            'reason.max' => 'Lý do báo cáo không quá 255 ký tự.',
        ]);

        $comment = Comment::findOrFail($id);

        // Cư dân không thể báo cáo bình luận của chính mình
        if ($comment->user_id === Auth::id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không thể báo cáo bình luận của chính mình.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Bạn không thể báo cáo bình luận của chính mình.');
        }

        // Tạo hoặc tìm bản ghi báo cáo (đảm bảo tính Unique)
        CommentReport::firstOrCreate(
            [
                'comment_id' => $comment->id,
                'user_id' => Auth::id(),
            ],
            [
                'reason' => $request->reason,
            ]
        );

        // Đếm tổng số cư dân khác nhau đã báo cáo bình luận này
        $reportsCount = $comment->reports()->count();

        $hiddenGlobally = false;
        if ($reportsCount >= 5) {
            $comment->content = '[Bình luận này đã bị ẩn tạm thời do nhận nhiều báo cáo vi phạm]';
            $comment->save();
            $hiddenGlobally = true;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $hiddenGlobally
                    ? 'Báo cáo thành công. Bình luận này đã bị ẩn tạm thời do nhận nhiều báo cáo vi phạm.'
                    : 'Báo cáo bình luận thành công. Ban quản lý sẽ sớm xem xét!',
                'hidden_globally' => $hiddenGlobally
            ]);
        }

        return redirect()->back()->with('success', 'Báo cáo bình luận thành công!');
    }

    /**
     * Chỉnh sửa bài viết
     */
    public function update(Request $request, $id)
    {
        if ($request->filled('price')) {
            $cleanPrice = preg_replace('/[^0-9]/', '', $request->price);
            $request->merge(['price' => $cleanPrice]);
        }

        $request->validate([
            'title' => 'nullable|string|max:200',
            'content' => 'required|string',
            'price' => 'nullable|numeric|min:0|max:999999999',
        ]);

        $post = Post::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        $post->update([
            'title' => $request->title ?: Str::limit(strip_tags($request->content), 40, '...'),
            'content' => $request->content,
            'price' => $request->price,
        ]);

        broadcast(new PostUpdated($post))->toOthers();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bài viết thành công!',
                'post' => $post,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật bài viết thành công!');
    }

    /**
     * Chỉnh sửa bình luận
     */
    public function updateComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bình luận này.');
        }

        $comment->update([
            'content' => $request->content,
        ]);

        broadcast(new CommentUpdated($comment))->toOthers();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bình luận thành công!',
                'comment' => $comment,
            ]);
        }

        return redirect()->back()->with('success', 'Cập nhật bình luận thành công!');
    }

    /**
     * Thích / Bỏ thích Bài viết hoặc Bình luận (Polymorphic Like)
     */
    public function toggleLike(Request $request)
    {
        $request->validate([
            'likeable_id' => 'required|integer',
            'likeable_type' => 'required|string|in:post,comment',
        ]);

        $userId = Auth::id();
        $likeableId = $request->likeable_id;
        $type = $request->likeable_type;
        $likeableClass = $type === 'post' ? Post::class : Comment::class;

        $likeable = $likeableClass::findOrFail($likeableId);

        $existingLike = Like::where([
            'user_id' => $userId,
            'likeable_id' => $likeableId,
            'likeable_type' => $likeableClass,
        ])->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            Like::create([
                'user_id' => $userId,
                'likeable_id' => $likeableId,
                'likeable_type' => $likeableClass,
            ]);
            $liked = true;
        }

        $likesCount = $likeable->likes()->count();
        $postId = $type === 'post' ? $likeable->id : $likeable->post_id;

        broadcast(new LikeToggled($type, $likeableId, $likesCount, $postId))->toOthers();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $likesCount,
        ]);
    }

    /**
     * Tải thêm bình luận cũ hơn qua AJAX
     */
    public function loadComments(Request $request, $postId)
    {
        $offset = (int) $request->get('offset', 10);
        $limit = 10;

        $comments = Comment::with(['user.apartment', 'replies' => function($q) {
                $q->with(['user.apartment', 'parent.user'])
                  ->withCount('likes')
                  ->with(['likedByCurrentUser'])
                  ->orderBy('created_at', 'asc');
            }])
            ->withCount('likes')
            ->with(['likedByCurrentUser'])
            ->where('post_id', $postId)
            ->whereNull('parent_id')
            ->whereDoesntHave('reports', function($rq) {
                $rq->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($limit)
            ->get()
            ->reverse()
            ->values();

        $formatted = $comments->map(function($comment) {
            return [
                'id' => $comment->id,
                'post_id' => $comment->post_id,
                'parent_id' => $comment->parent_id,
                'content' => $comment->content,
                'created_at_human' => $comment->created_at->diffForHumans(),
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'avatar' => $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : null,
                    'apartment' => $comment->user->apartment ? [
                        'apartment_number' => $comment->user->apartment->apartment_number,
                    ] : null,
                ],
                'likes_count' => $comment->likes_count,
                'liked' => $comment->likedByCurrentUser->isNotEmpty(),
                'is_owner' => $comment->user_id === Auth::id(),
                'can_delete' => $comment->user_id === Auth::id() || $comment->post->user_id === Auth::id() || Auth::user()->isAdminPortalUser(),
                'replies' => $comment->replies->map(function($reply) {
                    return [
                        'id' => $reply->id,
                        'post_id' => $reply->post_id,
                        'parent_id' => $reply->parent_id,
                        'content' => $reply->content,
                        'created_at_human' => $reply->created_at->diffForHumans(),
                        'user' => [
                            'id' => $reply->user->id,
                            'name' => $reply->user->name,
                            'avatar' => $reply->user->avatar ? asset('storage/' . $reply->user->avatar) : null,
                            'apartment' => $reply->user->apartment ? [
                                'apartment_number' => $reply->user->apartment->apartment_number,
                            ] : null,
                        ],
                        'likes_count' => $reply->likes_count,
                        'liked' => $reply->likedByCurrentUser->isNotEmpty(),
                        'is_owner' => $reply->user_id === Auth::id(),
                        'can_delete' => $reply->user_id === Auth::id() || $reply->post->user_id === Auth::id() || Auth::user()->isAdminPortalUser(),
                    ];
                })->toArray(),
            ];
        });

        return response()->json([
            'success' => true,
            'comments' => $formatted,
        ]);
    }
}
