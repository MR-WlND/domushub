<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\PostReport;
use App\Models\CommentReport;
use App\Models\PostImage;
use App\Models\Like;
use App\Models\User;
use App\Models\Apartment;
use App\Events\PostUpdated;
use App\Events\CommentUpdated;
use App\Events\LikeToggled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Rules\CleanContent;

class PostController extends Controller
{
    /**
     * Hiển thị bảng tin cộng đồng cư dân
     */
    public function index(Request $request)
    {
        $query = Post::with(['user.apartment', 'images'])
            ->withCount(['likes', 'comments'])
            ->with(['likedByCurrentUser'])
            ->where('status', 'published')
            ->whereDoesntHave('reports', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereDoesntHave('hides', function($q) {
                $q->where('user_id', Auth::id());
            });

        $query->orderBy('created_at', 'desc');
 
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
        } elseif ($request->get('type') === 'mine') {
            $query->where('user_id', Auth::id());
        }
 
        $posts = $query->paginate(10)->withQueryString();
 
        return view('resident.posts.index', compact('posts'));
    }

    /**
     * Đăng bài viết mới
     */
    public function store(Request $request)
    {
        // Kiểm tra xem cư dân có bị khóa quyền đăng bài hay không
        if (Auth::user()->isBannedPosting()) {
            $bannedTime = \Carbon\Carbon::parse(Auth::user()->banned_posting_until)->format('H:i d/m/Y');
            $msg = "Tài khoản của bạn đang bị khóa quyền đăng bài viết đến {$bannedTime} do vi phạm nội quy.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect()->back()->withErrors(['content' => $msg])->withInput();
        }

        // 1. Xử lý định dạng tiền tệ trước khi validate
        if ($request->filled('price')) {
            // Loại bỏ dấu phân cách hàng nghìn (chấm/phẩy) và khoảng trắng để lấy số thuần
            $cleanPrice = preg_replace('/[^0-9]/', '', $request->price);
            $request->merge(['price' => $cleanPrice]);
        }

        // 2. Validate dữ liệu đầu vào (hỗ trợ images/video dạng mảng)
        $request->validate([
            'title' => ['nullable', 'string', 'max:200', new CleanContent()],
            'content' => ['required', 'string', new CleanContent()],
            'price' => 'nullable|numeric|min:0|max:999999999',
            'media' => 'nullable|array|max:5', // Tối đa 5 file ảnh/video
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,webm|max:20480', // Mỗi file tối đa 20MB
        ], [
            'title.max' => 'Tiêu đề không được vượt quá 200 ký tự.',
            'content.required' => 'Vui lòng nhập nội dung bài đăng.',
            'price.numeric' => 'Giá bán phải là định dạng số.',
            'price.min' => 'Giá bán không được nhỏ hơn 0đ.',
            'media.array' => 'File đính kèm phải ở dạng danh sách.',
            'media.max' => 'Bạn chỉ được đính kèm tối đa 5 file cho mỗi bài viết.',
            'media.*.file' => 'File tải lên không hợp lệ.',
            'media.*.mimes' => 'File phải có định dạng: jpeg, png, jpg, gif, webp, mp4, mov, avi, webm.',
            'media.*.max' => 'Dung lượng mỗi file không được vượt quá 20MB.',
        ]);

        $data = $request->only(['content', 'price']);
        
        // Loại bỏ việc tự động tạo tiêu đề
        $data['title'] = null;
        
        $data['user_id'] = Auth::id();
        $data['status'] = 'published'; // Mặc định là published
        $data['ai_flagged'] = 'clean';   // Mặc định là clean

        $post = Post::create($data);

        // Xử lý upload danh sách ảnh/video nếu có
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $mime = $file->getMimeType();
                $isVideo = str_starts_with($mime, 'video/');
                $folder = $isVideo ? 'posts/videos' : 'posts';
                $path = $file->store($folder, 'public');
                $post->images()->create([
                    'image_path' => $path,
                    'type' => $isVideo ? 'video' : 'image',
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

        // Chặn xem nếu cư dân này đã báo cáo hoặc ẩn bài viết này
        $hasReported = $post->reports()->where('user_id', Auth::id())->exists();
        $hasHidden = false;
        if (\Illuminate\Support\Facades\Schema::hasTable('post_hides')) {
            $hasHidden = $post->hides()->where('user_id', Auth::id())->exists();
        }
        if ($hasReported || $hasHidden) {
            abort(403, 'Bạn đã báo cáo hoặc ẩn bài viết này và không thể xem lại nội dung.');
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

        // Ghim bình luận lên đầu bằng cách sắp xếp lại collection
        $comments = $comments->sortByDesc('is_pinned')->values();

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
        // Kiểm tra xem cư dân có bị khóa quyền bình luận hay không
        if (Auth::user()->isBannedCommenting()) {
            $bannedTime = \Carbon\Carbon::parse(Auth::user()->banned_commenting_until)->format('H:i d/m/Y');
            $msg = "Tài khoản của bạn đang bị khóa quyền bình luận đến {$bannedTime} do vi phạm nội quy.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect()->back()->with('error', $msg);
        }

        $request->validate([
            'content' => ['required', 'string', new CleanContent()],
            'parent_id' => 'nullable|exists:comments,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'content.required' => 'Vui lòng nhập nội dung bình luận.',
            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Dung lượng hình ảnh không được vượt quá 2MB.',
        ]);

        $post = Post::findOrFail($postId);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comments', 'public');
        }

        $comment = Comment::create([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'image_path' => $imagePath,
            'is_pinned' => false,
        ]);

        // Phát sự kiện real-time cho bình luận mới
        try {
            broadcast(new \App\Events\CommentCreated($comment));
        } catch (\Exception $e) {
            \Log::warning('Broadcast CommentCreated failed: ' . $e->getMessage());
        }

        // Quét cư dân được tag nhắc tên bằng kí tự phân tách zero-width space (\x{200B})
        $mentionedUserIds = [];
        preg_match_all('/@([^@\x{200B}]+)\x{200B}/u', $comment->content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $fullName = trim($match[1]);
            
            // Kiểm tra định dạng đầy đủ: Tên Cư Dân (Căn hộ XXX)
            if (preg_match('/^([^\(\n\r]+?)\s*\(Căn hộ\s*([^\)]+?)\)$/u', $fullName, $subMatches)) {
                $name = trim($subMatches[1]);
                $room = trim($subMatches[2]);
                $user = User::where('name', $name)
                    ->where('role', 'resident')
                    ->whereHas('apartment', function($q) use ($room) {
                        $q->where('apartment_number', $room);
                    })
                    ->first();
                if ($user) {
                    $mentionedUserIds[] = $user->id;
                }
            } else {
                // Định dạng rút gọn: Tên Cư Dân
                $user = User::where('name', $fullName)
                    ->where('role', 'resident')
                    ->first();
                if ($user) {
                    $mentionedUserIds[] = $user->id;
                }
            }
        }

        $mentionedUserIds = array_unique($mentionedUserIds);

        // Gửi thông báo nhắc tên và tương tác (không block response nếu lỗi)
        try {
            foreach ($mentionedUserIds as $uId) {
                if ($uId !== Auth::id()) {
                    $userToNotify = User::find($uId);
                    if ($userToNotify) {
                        $userToNotify->notify(new \App\Notifications\CommentMentionNotification($comment));
                    }
                }
            }

            if ($comment->parent_id) {
                $parentComment = Comment::find($comment->parent_id);
                if ($parentComment && $parentComment->user_id !== Auth::id() && !in_array($parentComment->user_id, $mentionedUserIds)) {
                    $parentComment->user->notify(new \App\Notifications\CommentRepliedNotification($comment));
                }
                if ($post->user_id !== Auth::id() && (!$parentComment || $post->user_id !== $parentComment->user_id) && !in_array($post->user_id, $mentionedUserIds)) {
                    $post->user->notify(new \App\Notifications\PostCommentedNotification($comment));
                }
            } else {
                if ($post->user_id !== Auth::id() && !in_array($post->user_id, $mentionedUserIds)) {
                    $post->user->notify(new \App\Notifications\PostCommentedNotification($comment));
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Notification dispatch failed: ' . $e->getMessage());
        }

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
        $comment = Comment::withTrashed()->findOrFail($id);

        // Bảo mật tuyệt đối: chỉ người viết bình luận, chủ bài đăng đó hoặc admin mới được quyền xóa
        if ($comment->user_id !== Auth::id() && 
            $comment->post->user_id !== Auth::id() && 
            !Auth::user()->isAdminPortalUser()) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        // 1. Tìm và xóa vĩnh viễn tất cả câu trả lời con (replies) và dữ liệu đính kèm của chúng
        $replies = Comment::withTrashed()->where('parent_id', $comment->id)->get();
        foreach ($replies as $reply) {
            // Xóa file ảnh vật lý của câu trả lời con
            if ($reply->image_path) {
                Storage::disk('public')->delete($reply->image_path);
            }
            // Xóa các lượt thích (likes) của câu trả lời con
            $reply->likes()->delete();
            // Xóa các báo cáo (reports) của câu trả lời con
            $reply->reports()->delete();
            // Xóa vĩnh viễn khỏi database
            $reply->forceDelete();
        }

        // 2. Xóa dữ liệu đính kèm của chính bình luận cha
        // Xóa file ảnh vật lý của bình luận cha
        if ($comment->image_path) {
            Storage::disk('public')->delete($comment->image_path);
        }
        // Xóa các lượt thích (likes) của bình luận cha
        $comment->likes()->delete();
        // Xóa các báo cáo (reports) của bình luận cha
        $comment->reports()->delete();

        // 3. Xóa vĩnh viễn bình luận cha khỏi database
        $comment->forceDelete();

        return redirect()->back()->with('success', 'Xóa bình luận và toàn bộ phản hồi con liên quan thành công!');
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
            if ($comment->image_path) {
                Storage::disk('public')->delete($comment->image_path);
                $comment->image_path = null;
            }
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
     * Hiển thị trang chỉnh sửa bài viết
     */
    public function edit($id)
    {
        $post = Post::with('images')->findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        return view('resident.posts.edit', compact('post'));
    }

    /**
     * Chỉnh sửa bài viết
     */
    public function update(Request $request, $id)
    {
        // Kiểm tra xem cư dân có bị khóa quyền đăng bài hay không
        if (Auth::user()->isBannedPosting()) {
            $bannedTime = \Carbon\Carbon::parse(Auth::user()->banned_posting_until)->format('H:i d/m/Y');
            $msg = "Tài khoản của bạn đang bị khóa quyền sửa/đăng bài viết đến {$bannedTime} do vi phạm nội quy.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($request->filled('price')) {
            $cleanPrice = preg_replace('/[^0-9]/', '', $request->price);
            $request->merge(['price' => $cleanPrice]);
        }

        $request->validate([
            'title' => ['nullable', 'string', 'max:200', new CleanContent()],
            'content' => ['required', 'string', new CleanContent()],
            'price' => 'nullable|numeric|min:0|max:999999999',
            'media' => 'nullable|array|max:5',
            'media.*' => 'file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,webm|max:20480',
            'delete_media' => 'nullable|array',
            'delete_media.*' => 'integer|exists:post_images,id',
        ], [
            'title.max' => 'Tiêu đề không được vượt quá 200 ký tự.',
            'content.required' => 'Vui lòng nhập nội dung bài đăng.',
            'price.numeric' => 'Giá bán phải là định dạng số.',
            'price.min' => 'Giá bán không được nhỏ hơn 0đ.',
            'media.array' => 'File đính kèm phải ở dạng danh sách.',
            'media.max' => 'Bạn chỉ được đính kèm tối đa 5 file cho mỗi bài viết.',
            'media.*.file' => 'File tải lên không hợp lệ.',
            'media.*.mimes' => 'File phải có định dạng: jpeg, png, jpg, gif, webp, mp4, mov, avi, webm.',
            'media.*.max' => 'Dung lượng mỗi file không được vượt quá 20MB.',
        ]);

        $post = Post::findOrFail($id);

        if ($post->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền chỉnh sửa bài viết này.');
        }

        // Xử lý xóa media cũ
        $deleteMediaIds = $request->input('delete_media', []);
        if (!empty($deleteMediaIds)) {
            $imagesToDelete = $post->images()->whereIn('id', $deleteMediaIds)->get();
            foreach ($imagesToDelete as $img) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }
        }

        // Xử lý upload danh sách ảnh/video mới nếu có
        if ($request->hasFile('media')) {
            $currentMediaCount = $post->images()->count();
            $newMediaCount = count($request->file('media'));
            if ($currentMediaCount + $newMediaCount > 5) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Tổng số hình ảnh/video của bài viết không được vượt quá 5.'], 422);
                }
                return redirect()->back()->withErrors(['media' => 'Tổng số hình ảnh/video của bài viết không được vượt quá 5.'])->withInput();
            }

            foreach ($request->file('media') as $file) {
                $mime = $file->getMimeType();
                $isVideo = str_starts_with($mime, 'video/');
                $folder = $isVideo ? 'posts/videos' : 'posts';
                $path = $file->store($folder, 'public');
                $post->images()->create([
                    'image_path' => $path,
                    'type' => $isVideo ? 'video' : 'image',
                ]);
            }
        }

        $post->update([
            'title' => $request->title ?: Str::limit(strip_tags($request->content), 40, '...'),
            'content' => $request->content,
            'price' => $request->price,
        ]);

        try {
            broadcast(new PostUpdated($post))->toOthers();
        } catch (\Exception $e) {
            \Log::warning('Broadcast PostUpdated failed: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bài viết thành công!',
                'post' => $post->load('images'),
            ]);
        }

        return redirect()->route('resident.posts.show', $post->id)->with('success', 'Cập nhật bài viết thành công!');
    }

    /**
     * Chỉnh sửa bình luận
     */
    public function updateComment(Request $request, $id)
    {
        // Kiểm tra xem cư dân có bị khóa quyền bình luận hay không
        if (Auth::user()->isBannedCommenting()) {
            $bannedTime = \Carbon\Carbon::parse(Auth::user()->banned_commenting_until)->format('H:i d/m/Y');
            $msg = "Tài khoản của bạn đang bị khóa quyền chỉnh sửa bình luận đến {$bannedTime} do vi phạm nội quy.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect()->back()->with('error', $msg);
        }

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

        try {
            broadcast(new CommentUpdated($comment))->toOthers();
        } catch (\Exception $e) {
            \Log::warning('Broadcast CommentUpdated failed: ' . $e->getMessage());
        }

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
     * Thích / Bỏ thích Bài viết hoặc Bình luận (Polymorphic Like / Reaction)
     */
    public function toggleLike(Request $request)
    {
        $request->validate([
            'likeable_id' => 'required|integer',
            'likeable_type' => 'required|string|in:post,comment',
            'type' => 'nullable|string|in:like,love,haha,wow,sad,angry',
        ]);

        $userId = Auth::id();
        $likeableId = $request->likeable_id;
        $type = $request->likeable_type;
        $reactionType = $request->type ?? 'like';
        $likeableClass = $type === 'post' ? Post::class : Comment::class;

        $likeable = $likeableClass::findOrFail($likeableId);

        $existingLike = Like::where([
            'user_id' => $userId,
            'likeable_id' => $likeableId,
            'likeable_type' => $likeableClass,
        ])->first();

        $liked = false;
        $activeReactionType = null;

        if ($existingLike) {
            if ($existingLike->type === $reactionType) {
                // Cùng loại cảm xúc -> xóa cảm xúc (Bỏ thích)
                $existingLike->delete();
            } else {
                // Khác loại cảm xúc -> cập nhật sang cảm xúc mới
                $existingLike->update(['type' => $reactionType]);
                $liked = true;
                $activeReactionType = $reactionType;

                // Xử lý thông báo (Lưu ý A - tránh spam thông báo)
                $recipient = $likeable->user;
                if ($recipient && $recipient->id !== $userId) {
                    $oldNotif = $recipient->notifications()
                        ->where('type', \App\Notifications\ReactionNotification::class)
                        ->where('data->sender_id', $userId)
                        ->where('data->likeable_id', $likeableId)
                        ->where('data->likeable_type', $likeableClass)
                        ->first();

                    if ($oldNotif) {
                        $data = $oldNotif->data;
                        $reactionLabel = match($reactionType) {
                            'love' => 'yêu thích',
                            'haha' => 'haha',
                            'wow' => 'ngạc nhiên',
                            'sad' => 'buồn',
                            'angry' => 'phẫn nộ',
                            default => 'thích',
                        };
                        $targetLabel = $type === 'post' ? 'bài viết' : 'bình luận';
                        $senderName = Auth::user()->apartment ? 'Căn hộ ' . Auth::user()->apartment->apartment_number : Auth::user()->name;
                        
                        $data['message'] = "{$senderName} đã bày tỏ cảm xúc {$reactionLabel} về {$targetLabel} của bạn.";
                        $data['reaction_type'] = $reactionType;
                        
                        $oldNotif->data = $data;
                        $oldNotif->read_at = null;
                        $oldNotif->save();
                    } else {
                        // Nếu không có thông báo cũ, gửi thông báo mới
                        $newLike = Like::where([
                            'user_id' => $userId,
                            'likeable_id' => $likeableId,
                            'likeable_type' => $likeableClass,
                        ])->first();
                        if ($newLike) {
                            $recipient->notify(new \App\Notifications\ReactionNotification($newLike, $likeable, $type));
                        }
                    }
                }
            }
        } else {
            // Chưa có -> Tạo cảm xúc mới
            $like = Like::create([
                'user_id' => $userId,
                'likeable_id' => $likeableId,
                'likeable_type' => $likeableClass,
                'type' => $reactionType,
            ]);
            $liked = true;
            $activeReactionType = $reactionType;

            // Bắn thông báo mới cho chủ sở hữu
            $recipient = $likeable->user;
            if ($recipient && $recipient->id !== $userId) {
                $recipient->notify(new \App\Notifications\ReactionNotification($like, $likeable, $type));
            }
        }

        $likesCount = $likeable->likes()->count();
        
        // Thống kê các loại reaction khác nhau đang có
        $reactionsSummary = Like::where([
                'likeable_id' => $likeableId,
                'likeable_type' => $likeableClass,
            ])
            ->select('type', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $postId = $type === 'post' ? $likeable->id : $likeable->post_id;

        try {
            broadcast(new LikeToggled($type, $likeableId, $likesCount, $postId, $activeReactionType, $reactionsSummary))->toOthers();
        } catch (\Exception $e) {
            \Log::warning('Broadcast LikeToggled failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'reaction_type' => $activeReactionType,
            'likes_count' => $likesCount,
            'reactions_summary' => $reactionsSummary,
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

        // Sắp xếp bình luận được ghim lên đầu
        $comments = $comments->sortByDesc('is_pinned')->values();

        $formatted = $comments->map(function($comment) {
            return [
                'id' => $comment->id,
                'post_id' => $comment->post_id,
                'parent_id' => $comment->parent_id,
                'content' => $comment->content,
                'image_path' => $comment->image_path ? asset('storage/' . $comment->image_path) : null,
                'is_pinned' => $comment->is_pinned,
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
                        'image_path' => $reply->image_path ? asset('storage/' . $reply->image_path) : null,
                        'is_pinned' => $reply->is_pinned,
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

    /**
     * Lấy danh sách thành viên cư dân để gợi ý tag tên (@mentions)
     */
    public function searchMembersForMention(Request $request)
    {
        $users = User::with('apartment')
            ->where('status', 'active')
            ->where('role', 'resident')
            ->where('id', '!=', Auth::id())
            ->get();

        $formatted = $users->map(function($u) {
            $apartmentNo = $u->apartment ? ' (Căn hộ ' . $u->apartment->apartment_number . ')' : '';
            return [
                'key' => $u->name . $apartmentNo,
                'value' => '@' . $u->name . $apartmentNo,
                'id' => $u->id,
                'avatar' => $u->avatar ? asset('storage/' . $u->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=00236f&color=fff',
            ];
        });

        return response()->json($formatted);
    }

    /**
     * Ghim hoặc bỏ ghim bình luận lên đầu bài viết
     */
    public function togglePinComment($id)
    {
        $comment = Comment::findOrFail($id);
        $post = $comment->post;

        // Chỉ chủ bài viết hoặc admin portal mới được ghim bình luận
        if ($post->user_id !== Auth::id() && !Auth::user()->isAdminPortalUser()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này.'
            ], 403);
        }

        // Chỉ ghim được bình luận cấp 1 (bình luận cha)
        if ($comment->parent_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể ghim bình luận chính, không thể ghim phản hồi con.'
            ], 400);
        }

        $isPinned = !$comment->is_pinned;

        if ($isPinned) {
            // Hủy ghim toàn bộ các bình luận khác trong cùng bài viết này
            Comment::where('post_id', $post->id)
                ->where('id', '!=', $comment->id)
                ->update(['is_pinned' => false]);
        }

        $comment->update(['is_pinned' => $isPinned]);

        // Phát sự kiện cập nhật bình luận real-time
        try {
            broadcast(new \App\Events\CommentUpdated($comment))->toOthers();
        } catch (\Exception $e) {
            \Log::warning('Broadcast CommentUpdated (pin) failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'is_pinned' => $isPinned,
            'message' => $isPinned ? 'Đã ghim bình luận thành công!' : 'Đã bỏ ghim bình luận thành công!'
        ]);
    }

    /**
     * Lấy danh sách cư dân đã thả cảm xúc (Reactions Modal)
     */
    public function getReactions($likeableType, $likeableId)
    {
        if (!in_array($likeableType, ['post', 'comment'])) {
            return response()->json([
                'success' => false,
                'message' => 'Loại tương tác không hợp lệ.'
            ], 400);
        }

        $likeableClass = $likeableType === 'post' ? Post::class : Comment::class;
        $likeable = $likeableClass::findOrFail($likeableId);

        $reactions = Like::with(['user.apartment'])
            ->where('likeable_id', $likeableId)
            ->where('likeable_type', $likeableClass)
            ->get();

        $formatted = $reactions->map(function ($like) {
            return [
                'user_id' => $like->user_id,
                'name' => $like->user->name,
                'avatar' => $like->user->avatar ? asset('storage/' . $like->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($like->user->name) . '&background=00236f&color=fff',
                'apartment' => $like->user->apartment ? [
                    'apartment_number' => $like->user->apartment->apartment_number,
                ] : null,
                'type' => $like->type ?? 'like',
            ];
        });

        return response()->json([
            'success' => true,
            'reactions' => $formatted,
        ]);
    }

    /**
     * Chia sẻ bài đăng trực tiếp cho cư dân khác trong hệ thống
     */
    public function shareToUser(Request $request, $id)
    {
        $request->validate([
            'target_user_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:255',
        ], [
            'target_user_id.required' => 'Vui lòng chọn cư dân để chia sẻ.',
            'target_user_id.exists' => 'Cư dân được chia sẻ không tồn tại.',
            'message.max' => 'Lời nhắn không được vượt quá 255 ký tự.',
        ]);

        $post = Post::findOrFail($id);
        $targetUser = User::findOrFail($request->target_user_id);

        if ($targetUser->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể tự chia sẻ bài viết cho chính mình.'
            ], 400);
        }

        // Gửi thông báo chia sẻ bài đăng tới cư dân đích
        $targetUser->notify(new \App\Notifications\PostSharedNotification($post, Auth::user(), $request->message));

        return response()->json([
            'success' => true,
            'message' => 'Đã chia sẻ bài viết thành công đến cư dân ' . $targetUser->name . '!'
        ]);
    }

    /**
     * Ẩn bài đăng khỏi bảng tin của cư dân hiện tại
     */
    public function hide(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        \App\Models\PostHide::firstOrCreate([
            'post_id' => $post->id,
            'user_id' => Auth::id(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã ẩn bài viết thành công khỏi bảng tin của bạn.'
            ]);
        }

        return redirect()->route('resident.posts.index')->with('success', 'Đã ẩn bài viết thành công!');
    }
}

