<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{


    /**
     * Display the specified announcement details.
     */
    public function show($id)
    {
        $announcement = Announcement::with('user')
            ->published()
            ->findOrFail($id);

        return view('resident.announcements.show', compact('announcement'));
    }
}
