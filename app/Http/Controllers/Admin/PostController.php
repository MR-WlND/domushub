<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SystemLogger;
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

        if ($activeTab === 'banned_users') {
            $query = \App\Models\User::with('apartment')
                ->where('role', 'resident')
                ->where(function($q) {
                    $q->where('banned_posting_until', '>', now())
                      ->orWhere('banned_commenting_until', '>', now());
                })
                ->orderBy('updated_at', 'desc');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $bannedUsers = $query->paginate(15)->withQueryString();

            return view('admin.posts.index', compact('bannedUsers', 'activeTab'));
        }

        if ($activeTab === 'comments') {
            $query = Comment::withTrashed()->with(['user.apartment', 'post.user', 'reports.user.apartment'])
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

        $query = Post::withTrashed()->with(['user.apartment'])
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
        $post = Post::withTrashed()->with([
            'user.apartment',
            'images',
            'reports' => function($q) {
                $q->with('user.apartment')->orderBy('created_at', 'desc');
            }
        ])->withCount('reports')->findOrFail($id);

        $isBannedPosting = $post->user ? $post->user->isBannedPosting() : false;
        $isBannedCommenting = $post->user ? $post->user->isBannedCommenting() : false;

        return response()->json([
            'success' => true,
            'post' => $post,
            'is_banned_posting' => $isBannedPosting,
            'is_banned_commenting' => $isBannedCommenting,
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
        $post = Post::findOrFail($id);

        // Chỉ soft-delete bài viết để ẩn ở phía cư dân, giữ lại hình ảnh và lịch sử báo cáo cho Admin
        $post->delete();



        return redirect()->back()->with('success', 'Đã xóa bài viết khỏi phía cư dân thành công và lưu lại lịch sử.');
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
        $comment = Comment::findOrFail($id);

        // Soft-delete tất cả câu trả lời con (replies) để ẩn ở phía cư dân
        Comment::where('parent_id', $comment->id)->delete();

        // Soft-delete chính bình luận (ẩn khỏi phía cư dân, giữ lại các báo cáo để xem lịch sử)
        $comment->delete();

        return redirect()->back()->with('success', 'Đã xóa bình luận khỏi phía cư dân thành công và lưu lại lịch sử.');
    }

    /**
     * Khóa hoặc mở khóa quyền đăng bài của cư dân
     */
    public function banPosting(Request $request, $userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        $request->validate([
            'duration' => 'required|string|in:unban,1,3,7,30,permanent',
        ]);

        $duration = $request->duration;
        
        if ($duration === 'unban') {
            $user->banned_posting_until = null;
            $message = "Đã mở khóa quyền đăng bài cho cư dân {$user->name} thành công.";
        } else {
            $days = match ($duration) {
                '1' => 1,
                '3' => 3,
                '7' => 7,
                '30' => 30,
                'permanent' => 36500, // ~100 năm
            };
            
            $user->banned_posting_until = now()->addDays($days);
            
            $durationLabel = match ($duration) {
                '1' => '1 ngày',
                '3' => '3 ngày',
                '7' => '7 ngày',
                '30' => '30 ngày',
                'permanent' => 'vĩnh viễn',
            };
            
            $message = "Đã khóa quyền đăng bài của cư dân {$user->name} trong {$durationLabel} thành công.";
        }

        $user->save();



        return redirect()->back()->with('success', $message);
    }

    /**
     * Khóa hoặc mở khóa quyền bình luận của cư dân
     */
    public function banCommenting(Request $request, $userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        
        $request->validate([
            'duration' => 'required|string|in:unban,1,3,7,30,permanent',
        ]);

        $duration = $request->duration;
        
        if ($duration === 'unban') {
            $user->banned_commenting_until = null;
            $message = "Đã mở khóa quyền bình luận cho cư dân {$user->name} thành công.";
        } else {
            $days = match ($duration) {
                '1' => 1,
                '3' => 3,
                '7' => 7,
                '30' => 30,
                'permanent' => 36500, // ~100 năm
            };
            
            $user->banned_commenting_until = now()->addDays($days);
            
            $durationLabel = match ($duration) {
                '1' => '1 ngày',
                '3' => '3 ngày',
                '7' => '7 ngày',
                '30' => '30 ngày',
                'permanent' => 'vĩnh viễn',
            };
            
            $message = "Đã khóa quyền bình luận của cư dân {$user->name} trong {$durationLabel} thành công.";
        }

        $user->save();



        return redirect()->back()->with('success', $message);
    }

    /**
     * Khôi phục bài viết đã bị soft delete
     */
    public function restore($id)
    {
        $post = Post::withTrashed()->findOrFail($id);
        $post->restore();

        return redirect()->back()->with('success', "Đã khôi phục bài viết \"{$post->title}\" thành công.");
    }

    /**
     * Khôi phục bình luận đã bị soft delete
     */
    public function restoreComment($id)
    {
        $comment = Comment::withTrashed()->findOrFail($id);
        
        // Khôi phục câu trả lời con nếu có
        Comment::onlyTrashed()->where('parent_id', $comment->id)->restore();

        $comment->restore();

        return redirect()->back()->with('success', 'Đã khôi phục bình luận và các phản hồi liên quan thành công.');
    }
}
