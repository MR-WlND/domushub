<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{


    /**
     * Display a listing of announcements.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');
        $query = Announcement::with('user')->published();

        // Apply filters
        if ($tab === 'maintenance') {
            $query->where('category', 'maintenance');
        } elseif ($tab === 'warning') {
            $query->where('category', 'warning');
        } elseif ($tab === 'event') {
            $query->where('category', 'event');
        } elseif ($tab === 'general') {
            $query->where('category', 'general');
        }
        
        // Sort by pinned first, then latest
        $announcements = $query->orderBy('pinned', 'desc')->latest()->paginate(10)->withQueryString();

        $categoryMap = [
            'general' => 'Chung',
            'event' => 'Sự kiện',
            'maintenance' => 'Bảo trì',
            'warning' => 'Khẩn cấp',
            'community' => 'Cộng đồng',
        ];

        return view('resident.announcements.index', compact('announcements', 'tab', 'categoryMap'));
    }

    /**
     * Display the specified announcement details.
     */
    public function show($id)
    {
        $announcement = Announcement::with('user')
            ->published()
            ->findOrFail($id);

        $relatedAnnouncements = Announcement::published()
            ->where('id', '!=', $id)
            ->latest()
            ->take(2)
            ->get();

        $categoryMap = [
            'general' => 'Chung',
            'event' => 'Sự kiện',
            'maintenance' => 'Bảo trì',
            'warning' => 'Khẩn cấp',
            'community' => 'Cộng đồng',
        ];

        return view('resident.announcements.show', compact('announcement', 'relatedAnnouncements', 'categoryMap'));
    }
}
