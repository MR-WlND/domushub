<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Comment;
use App\Models\CommentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách tất cả các bài đăng của cư dân phía Admin
     */
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'posts');

        if ($activeTab === 'comments') {
            $query = Comment::with(['user.apartment', 'post.user', 'reports.user.apartment'])
                ->has('reports')
                ->withCount('reports')
                ->orderBy('created_at', 'desc');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('content', 'like', "%{$search}%")
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $reportedComments = $query->paginate(15)->withQueryString();

            return view('admin.posts.index', compact('reportedComments', 'activeTab'));
        }

        $query = Post::with(['user.apartment', 'comments'])
            ->withCount('reports')
            ->orderBy('created_at', 'desc');

        // Tìm kiếm theo từ khóa (tiêu đề, nội dung, hoặc tên người đăng)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Lọc theo trạng thái bài đăng (published, hidden, v.v.)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc bài đăng rao vặt thanh lý
        if ($request->filled('type')) {
            if ($request->type === 'marketplace') {
                $query->whereNotNull('price');
            } elseif ($request->type === 'general') {
                $query->whereNull('price');
            }
        }

        // Lọc bài đăng bị báo cáo
        if ($request->filled('reported')) {
            if ($request->reported === 'yes') {
                $query->has('reports');
            } elseif ($request->reported === 'high') {
                $query->has('reports', '>=', 3);
            }
        }

        $posts = $query->paginate(15)->withQueryString();

        return view('admin.posts.index', compact('posts', 'activeTab'));
    }

    /**
     * Ẩn hoặc hiện bài viết (chuyển đổi trạng thái giữa published và hidden)
     */
    public function toggleStatus($id)
    {
        $post = Post::findOrFail($id);
        
        if ($post->status === 'published') {
            $post->status = 'hidden';
            $message = 'Đã ẩn bài viết thành công.';
        } else {
            $post->status = 'published';
            $message = 'Đã khôi phục hiển thị bài viết thành công.';
        }

        $post->save();

        return redirect()->back()->with('success', $message);
    }

    /**
     * Lấy dữ liệu chi tiết bài viết và báo cáo dưới dạng JSON
     */
    public function getPostJson($id)
    {
        $post = Post::with([
            'user.apartment',
            'images',
            'reports' => function($q) {
                $q->with('user.apartment')->orderBy('created_at', 'desc');
            }
        ])->withCount('reports')->findOrFail($id);

        return response()->json([
            'success' => true,
            'post' => $post
        ]);
    }

    /**
     * Bỏ qua báo cáo bài viết (xóa các bản ghi liên quan trong bảng post_reports)
     */
    public function dismissReports($id)
    {
        $post = Post::findOrFail($id);

        // Xóa toàn bộ báo cáo liên quan đến bài viết này
        $post->reports()->delete();

        return redirect()->back()->with('success', 'Đã bỏ qua và gỡ bỏ toàn bộ lượt báo cáo của bài viết này.');
    }

    /**
     * Admin xóa bài viết
     */
    public function destroy($id)
    {
        $post = Post::with('images')->findOrFail($id);

        // Xóa các file hình ảnh đính kèm vật lý khỏi ổ cứng
        foreach ($post->images as $img) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($img->image_path);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Đã xóa bài viết vĩnh viễn khỏi hệ thống.');
    }

    /**
     * Bỏ qua báo cáo bình luận (xóa các bản ghi liên quan trong bảng comment_reports)
     */
    public function dismissCommentReports($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->reports()->delete();

        return redirect()->back()->with('success', 'Đã bỏ qua và gỡ bỏ toàn bộ lượt báo cáo của bình luận này.');
    }

    /**
     * Xóa bình luận do vi phạm (ghi đè nội dung vi phạm để giữ nguyên cấu trúc nhánh)
     */
    public function destroyComment($id)
    {
        $comment = Comment::withTrashed()->findOrFail($id);

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
            // Xóa vĩnh viễn câu trả lời con khỏi database
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

        return redirect()->back()->with('success', 'Đã xóa vĩnh viễn bình luận và toàn bộ phản hồi con liên quan thành công.');
    }
}
