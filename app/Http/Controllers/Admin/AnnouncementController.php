<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\NewAnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of announcements.
     */
    public function index(Request $request)
    {
        $query = Announcement::with('user');

        // Search by title or content
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Ordered by pinned first, then created_at desc
        $announcements = $query->ordered()->paginate(10)->withQueryString();

        // Get some quick stats
        $totalCount = Announcement::count();
        $publishedCount = Announcement::published()->count();
        $pinnedCount = Announcement::where('pinned', true)->count();
        $maintenanceCount = Announcement::where('category', 'maintenance')->count();

        return view('admin.announcements.index', compact(
            'announcements',
            'totalCount',
            'publishedCount',
            'pinnedCount',
            'maintenanceCount'
        ));
    }

    /**
     * Show the form for creating a new announcement.
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Store a newly created announcement in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:maintenance,warning,general,event',
            'status' => 'required|in:draft,published,archived',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'pinned' => 'nullable|boolean',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề thông báo.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'content.required' => 'Vui lòng nhập nội dung chi tiết thông báo.',
            'category.required' => 'Vui lòng chọn phân loại thông báo.',
            'category.in' => 'Phân loại thông báo không hợp lệ.',
            'status.required' => 'Vui lòng chọn trạng thái thông báo.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Dung lượng hình ảnh không được vượt quá 10MB.',
        ]);

        $data = $request->only(['title', 'content', 'category', 'status']);
        $data['user_id'] = Auth::id();
        $data['pinned'] = $request->has('pinned') ? (bool) $request->pinned : false;
        $data['is_popup'] = $request->has('is_popup') ? (bool) $request->is_popup : false;

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('announcements', 'public');
        }

        $announcement = Announcement::create($data);

        // Gửi thông báo hệ thống cho cư dân nếu bản tin được công bố ngay lập tức
        if ($announcement->status === 'published') {
            $residents = User::where('role', 'resident')->get();
            Notification::send($residents, new NewAnnouncementNotification($announcement));
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Đăng thông báo mới thành công!');
    }

    /**
     * Show the form for editing the specified announcement.
     */
    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        return view('admin.announcements.edit', compact('announcement'));
    }

    /**
     * Update the specified announcement in storage.
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:maintenance,warning,general,event',
            'status' => 'required|in:draft,published,archived',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'pinned' => 'nullable|boolean',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề thông báo.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'content.required' => 'Vui lòng nhập nội dung chi tiết thông báo.',
            'category.required' => 'Vui lòng chọn phân loại thông báo.',
            'category.in' => 'Phân loại thông báo không hợp lệ.',
            'status.required' => 'Vui lòng chọn trạng thái thông báo.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'image.image' => 'File tải lên phải là hình ảnh.',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Dung lượng hình ảnh không được vượt quá 10MB.',
        ]);

        $data = $request->only(['title', 'content', 'category', 'status']);
        $data['pinned'] = $request->has('pinned') ? (bool) $request->pinned : false;
        $data['is_popup'] = $request->has('is_popup') ? (bool) $request->is_popup : false;

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }
            $data['image_path'] = $request->file('image')->store('announcements', 'public');
        } elseif ($request->has('remove_image') && $request->remove_image == 1) {
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }
            $data['image_path'] = null;
        }

        $oldStatus = $announcement->status;
        $announcement->update($data);

        // Gửi thông báo hệ thống cho cư dân nếu trạng thái chuyển sang công bố
        if ($announcement->status === 'published' && $oldStatus !== 'published') {
            $residents = User::where('role', 'resident')->get();
            Notification::send($residents, new NewAnnouncementNotification($announcement));
        }

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Cập nhật thông báo thành công!');
    }

    /**
     * Remove the specified announcement from storage.
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);

        // Delete associated image file
        if ($announcement->image_path) {
            Storage::disk('public')->delete($announcement->image_path);
        }

        $announcement->delete();

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Xóa thông báo thành công!');
    }

    /**
     * Toggle the pinned status of an announcement.
     */
    public function togglePin(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->pinned = !$announcement->pinned;
        $announcement->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'pinned' => $announcement->pinned,
                'message' => $announcement->pinned ? 'Đã ghim thông báo lên đầu!' : 'Đã bỏ ghim thông báo!'
            ]);
        }

        return redirect()->back()->with('success', $announcement->pinned ? 'Đã ghim thông báo!' : 'Đã bỏ ghim thông báo!');
    }
}
