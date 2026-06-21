<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\View\View;

class FacilityController extends Controller
{
    /**
     * Danh sách tiện ích chung cư (cư dân xem)
     */
    public function index(): View
    {
        $facilities = Facility::orderBy('name')->get();

        return view('resident.facilities.index', compact('facilities'));
    }

    /**
     * Chi tiết một tiện ích
     */
    public function show(Facility $facility): View
    {
        $slots = $facility->getTimeSlots();

        return view('resident.facilities.show', compact('facility', 'slots'));
    }
}
