<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;
use App\Models\Visitor;
use App\Models\VehicleLog;
use App\Models\FacilityBooking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Block;
use App\Models\Floor;
use App\Models\Apartment;

class ActivityLogController extends Controller
{
    /**
     * Tab 1: Lịch sử Ra vào (Khách & Cư dân)
     */
    public function entryExit(Request $request)
    {
        try {
            $query = Visitor::with(['apartment', 'registeredBy', 'checkedInBy', 'checkedOutBy']);

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('guest_name', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('guest_phone', 'LIKE', '%' . $request->search . '%');
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59',
                ]);
            }

            $logs = $query->latest()->paginate(20)->withQueryString();

            return view('admin.activity-logs.index', [
                'tab'      => 'entry_exit',
                'logs'     => $logs,
                'pageTitle' => 'Lịch sử Quản lý Ra vào',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load log ra vào: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu.');
        }
    }

    /**
     * Tab 2: Lịch sử Bãi xe & Thu phí
     */
    public function parking(Request $request)
    {
        try {
            $query = VehicleLog::with([
                'vehicle.apartment',
                'checkedInBy',
                'checkedOutBy',
            ]);

            if ($request->filled('search')) {
                $query->whereHas('vehicle', function ($q) use ($request) {
                    $q->where('license_plate', 'LIKE', '%' . $request->search . '%');
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('check_in_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59',
                ]);
            }

            $logs = $query->latest('check_in_at')->paginate(20)->withQueryString();

            return view('admin.activity-logs.index', [
                'tab'      => 'parking',
                'logs'     => $logs,
                'pageTitle' => 'Lịch sử Bãi xe & Thu phí',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load log bãi xe: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu.');
        }
    }

    /**
     * Tab 3: Lịch sử Đặt lịch Tiện ích
     */
    public function facilityBooking(Request $request)
    {
        try {
            $query = FacilityBooking::with(['facility', 'user']);

            if ($request->filled('search')) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->search . '%');
                })->orWhereHas('facility', function ($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->search . '%');
                });
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('booking_date', [
                    $request->date_from,
                    $request->date_to,
                ]);
            }

            $logs = $query->latest()->paginate(20)->withQueryString();

            return view('admin.activity-logs.index', [
                'tab'      => 'facility',
                'logs'     => $logs,
                'pageTitle' => 'Lịch sử Đặt lịch Tiện ích',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load log tiện ích: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu.');
        }
    }

    /**
     * Tab 4: Lịch sử Quản trị Hệ thống & Phân quyền
     */
    public function system(Request $request)
    {
        try {
            $query = Activity::with('causer')
                ->where('log_name', 'system');

            if ($request->filled('search')) {
                $query->where('description', 'LIKE', '%' . $request->search . '%');
            }
            if ($request->filled('causer_id')) {
                $query->where('causer_id', $request->causer_id);
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59',
                ]);
            }

            $logs  = $query->latest()->paginate(20)->withQueryString();
            $users = User::where('role', 'admin')->orderBy('name')->get();

            return view('admin.activity-logs.index', [
                'tab'      => 'system',
                'logs'     => $logs,
                'users'    => $users,
                'pageTitle' => 'Lịch sử Quản trị Hệ thống & Phân quyền',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load log hệ thống: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu.');
        }
    }

    /**
     * Tab 5: Lịch sử Tài chính & Hóa đơn
     */
    public function finance(Request $request)
    {
        try {
            $query = Payment::with(['invoice.apartment', 'recorder']);

            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('receipt_code', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('payer_name', 'LIKE', '%' . $request->search . '%')
                      ->orWhere('transaction_code', 'LIKE', '%' . $request->search . '%');
                });
            }
            if ($request->filled('method')) {
                $query->where('payment_method', $request->method);
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

            return view('admin.activity-logs.index', [
                'tab'      => 'finance',
                'logs'     => $logs,
                'pageTitle' => 'Lịch sử Tài chính & Hóa đơn',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load log tài chính: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu.');
        }
    }

    /**
     * Tab 6: Lịch sử Giao tiếp Phần cứng
     */
    public function hardware(Request $request)
    {
        try {
            $query = Activity::with('causer')
                ->whereIn('log_name', ['hardware', 'qr', 'scanner']);

            if ($request->filled('search')) {
                $query->where('description', 'LIKE', '%' . $request->search . '%');
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59',
                ]);
            }

            $logs = $query->latest()->paginate(20)->withQueryString();

            return view('admin.activity-logs.index', [
                'tab'      => 'hardware',
                'logs'     => $logs,
                'pageTitle' => 'Lịch sử Giao tiếp Phần cứng',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load log phần cứng: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu.');
        }
    }

    /**
     * Tab 7: Lịch sử Giao tiếp Cư dân (Thông báo)
     */
    public function communication(Request $request)
    {
        try {
            $query = Activity::with('causer')
                ->whereIn('log_name', ['notification', 'announcement', 'resident']);

            if ($request->filled('search')) {
                $query->where('description', 'LIKE', '%' . $request->search . '%');
            }
            if ($request->filled('causer_id')) {
                $query->where('causer_id', $request->causer_id);
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59',
                ]);
            }

            $logs  = $query->latest()->paginate(20)->withQueryString();
            $users = User::where('role', 'admin')->orderBy('name')->get();

            return view('admin.activity-logs.index', [
                'tab'      => 'communication',
                'logs'     => $logs,
                'users'    => $users,
                'pageTitle' => 'Lịch sử Giao tiếp Cư dân',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load log thông báo: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu.');
        }
    }

    /**
     * Tab 8: Lịch sử Ghi số Điện nước
     */
    public function utility(Request $request)
    {
        try {
            $query = Activity::with('causer')
                ->where('log_name', 'utility');

            if ($request->filled('search')) {
                // Tìm kiếm theo người thực hiện
                $query->where(function ($q) use ($request) {
                    $q->where('description', 'LIKE', '%' . $request->search . '%')
                      ->orWhereHas('causer', function($qc) use ($request) {
                          $qc->where('name', 'LIKE', '%' . $request->search . '%');
                      });
                });
            }

            if ($request->filled('type')) {
                $query->where('properties->type', $request->type);
            }

            if ($request->filled('action')) {
                $query->where('properties->action', $request->action);
            }

            if ($request->filled('month')) {
                $query->where('properties->record_month', (int)$request->month);
            }

            if ($request->filled('year')) {
                $query->where('properties->record_year', (int)$request->year);
            }

            $blockId = $request->query('block_id');
            $floorId = $request->query('floor_id');

            if ($floorId) {
                $apartmentIds = Apartment::where('floor_id', $floorId)->pluck('id')->toArray();
                $query->whereIn('properties->apartment_id', $apartmentIds);
            } elseif ($blockId) {
                $floorIds = Floor::where('block_id', $blockId)->pluck('id')->toArray();
                $apartmentIds = Apartment::whereIn('floor_id', $floorIds)->pluck('id')->toArray();
                $query->whereIn('properties->apartment_id', $apartmentIds);
            }

            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59',
                ]);
            }

            $logs = $query->latest()->paginate(20)->withQueryString();

            // Lấy dữ liệu cho bộ lọc
            $blocks = Block::orderBy('name')->get();
            $floors = Floor::with('block')->orderBy('floor_number')->get();

            // Load thông tin căn hộ cho mỗi log để hiển thị
            $apartmentMap = [];
            $allAptIds = $logs->pluck('properties.apartment_id')->filter()->unique();
            if ($allAptIds->isNotEmpty()) {
                $apartmentMap = Apartment::with('floor.block')->whereIn('id', $allAptIds)->get()->keyBy('id');
            }

            // Gắn thông tin căn hộ vào mỗi log
            foreach ($logs as $log) {
                $aptId = $log->properties['apartment_id'] ?? null;
                if ($aptId && isset($apartmentMap[$aptId])) {
                    $log->apartment = $apartmentMap[$aptId];
                }
            }

            return view('admin.activity-logs.index', [
                'tab'      => 'utility',
                'logs'     => $logs,
                'blocks'   => $blocks,
                'floors'   => $floors,
                'blockId'  => $blockId,
                'floorId'  => $floorId,
                'pageTitle' => 'Lịch sử Ghi số Điện Nước',
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi load log điện nước: ' . $e->getMessage());
            return back()->with('error', 'Không thể tải dữ liệu.');
        }
    }

    /**
     * Xuất Excel cho Log
     */
    public function export(Request $request)
    {
        $tab = $request->input('tab', 'entry_exit');
        $filters = $request->except(['tab', 'page']);
        
        $fileName = 'activity_logs_' . $tab . '_' . date('Ymd_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ActivityLogsExport($tab, $filters), $fileName);
    }
}
