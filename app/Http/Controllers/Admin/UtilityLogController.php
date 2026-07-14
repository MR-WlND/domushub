<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UtilityMeter;
use App\Models\Block;
use Illuminate\Http\Request;

class UtilityLogController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'all');

        $query = UtilityMeter::with(['apartment.floor.block', 'recorder', 'rejecter'])
            ->orderByDesc('record_year')
            ->orderByDesc('record_month')
            ->orderByDesc('created_at');

        if ($tab === 'rejected') {
            $query->where('status', 'rejected');
        }

        // Tìm kiếm theo số căn hộ
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('apartment', function ($q) use ($search) {
                $q->where('apartment_number', 'like', "%{$search}%");
            });
        }

        // Lọc theo loại điện/nước
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo tháng/năm
        if ($request->filled('month')) {
            $query->where('record_month', (int) $request->month);
        }
        if ($request->filled('year')) {
            $query->where('record_year', (int) $request->year);
        }

        // Lọc theo khoảng thời gian ghi nhận
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Thống kê tổng quan (không bị lọc)
        $stats = [
            'total'     => UtilityMeter::count(),
            'elec'      => UtilityMeter::where('type', 'electricity')->count(),
            'water'     => UtilityMeter::where('type', 'water')->count(),
            'approved'  => UtilityMeter::where('status', 'approved')->count(),
            'pending'   => UtilityMeter::where('status', 'pending')->count(),
            'rejected'  => UtilityMeter::where('status', 'rejected')->count(),
        ];

        return view('admin.utility-logs.index', compact('logs', 'stats', 'tab'));
    }
}
