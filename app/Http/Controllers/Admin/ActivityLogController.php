<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Lịch sử Quản trị Hệ thống & Phân quyền (System & Security Logs)
     */
    public function index(Request $request)
    {
        try {
            $query = Activity::with('causer')
                ->where('log_name', 'system_security');

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('description', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('properties->target', 'LIKE', '%' . $request->search . '%');
                });
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59',
                ]);
            }

            $logs = $query->latest()->paginate(20)->withQueryString();

            return view('admin.system-logs.index', [
                'logs' => $logs,
                'pageTitle' => 'Lịch sử Quản trị Hệ thống & Phân quyền',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load log hệ thống: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu.');
        }
    }

    /**
     * Lịch sử Thông báo
     */
    public function notificationLogs(Request $request)
    {
        try {
            $query = Announcement::with('user')->withTrashed();

            if ($request->filled('search')) {
                $query->where('title', 'LIKE', '%' . $request->search . '%');
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59',
                ]);
            }

            $logs = $query->latest()->paginate(20)->withQueryString();

            return view('admin.notification-logs.index', [
                'logs' => $logs,
                'pageTitle' => 'Lịch sử Thông báo',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load lịch sử thông báo: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu.');
        }
    }

    /**
     * Lịch sử Tài chính
     */
    public function financeLogs(Request $request)
    {
        try {
            $query = Payment::with(['invoice.apartment', 'recorder'])->withTrashed();

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('receipt_code', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('transaction_code', 'LIKE', '%' . $request->search . '%')
                      ->orWhereHas('invoice', function ($qi) use ($request) {
                          $qi->whereRaw("CONCAT('BILL-', LPAD(id, 5, '0')) LIKE ?", ['%' . $request->search . '%']);
                      });
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('paid_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59',
                ]);
            }

            $logs = $query->latest('paid_at')->paginate(20)->withQueryString();

            return view('admin.finance-logs.index', [
                'logs' => $logs,
                'pageTitle' => 'Lịch sử Tài chính',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load lịch sử tài chính: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu.');
        }
    }

    /**
     * Chi tiết Lịch sử Quản trị Hệ thống
     */
    public function show($id)
    {
        try {
            $log = Activity::with('causer')->findOrFail($id);
            
            // Đảm bảo chỉ xem log của system_security
            if ($log->log_name !== 'system_security') {
                return redirect()->route('admin.system-logs.index')->with('error', 'Không tìm thấy nhật ký hợp lệ.');
            }

            return view('admin.system-logs.show', [
                'log' => $log,
                'pageTitle' => 'Chi tiết Lịch sử Quản trị Hệ thống',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load chi tiết log hệ thống: ' . $e->getMessage());
            return redirect()->route('admin.system-logs.index')->with('error', 'Không thể tải dữ liệu chi tiết.');
        }
    }
}
