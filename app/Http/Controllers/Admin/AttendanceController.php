<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SystemLogger;
use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * AttendanceController
 *
 * Phân quyền: chỉ Admin và Manager (kiểm soát qua middleware group trong routes/web.php).
 * Không có method destroy — dữ liệu chấm công không được phép xoá, kể cả Admin.
 */
class AttendanceController extends Controller
{
    /** Danh sách roles được phép chấm công */
    private const STAFF_ROLES = ['admin', 'manager', 'staff', 'technician', 'security', 'cleaning'];

    // ── Helper dùng chung ──────────────────────────────────────────

    /**
     * Chuyển chuỗi H:i thành Carbon datetime đầy đủ gắn với work_date.
     */
    private function parseTime(string $date, ?string $time): ?Carbon
    {
        return $time ? Carbon::parse($date . ' ' . $time) : null;
    }

    /**
     * Trả về query base chỉ lấy nhân viên nội bộ.
     */
    private function staffQuery()
    {
        return User::whereIn('role', self::STAFF_ROLES)->where('status', 'active');
    }

    // ── Danh sách & tổng quan ──────────────────────────────────────

    public function index(Request $request)
    {
        $query = AttendanceRecord::with(['user', 'recorder'])
            ->whereHas('user', fn($q) => $q->whereIn('role', self::STAFF_ROLES));

        // Lọc ngày (mặc định hôm nay)
        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : Carbon::today()->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : Carbon::today()->endOfDay();

        $query->whereBetween('work_date', [$dateFrom->toDateString(), $dateTo->toDateString()]);

        if ($request->filled('user_id'))  $query->where('user_id', $request->user_id);
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('shift'))    $query->where('shift', $request->shift);

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q
                ->where('name', 'LIKE', '%' . $request->search . '%')
                ->orWhere('email', 'LIKE', '%' . $request->search . '%')
            );
        }

        $records = $query->orderByDesc('work_date')->orderByDesc('check_in_at')->paginate(20);

        // Thống kê trong khoảng ngày
        $statsBase = AttendanceRecord::whereHas('user', fn($q) => $q->whereIn('role', self::STAFF_ROLES))
            ->whereBetween('work_date', [$dateFrom->toDateString(), $dateTo->toDateString()]);

        $stats = [
            'total_staff' => $this->staffQuery()->count(),
            'present'     => (clone $statsBase)->whereIn('status', ['present', 'working'])->count(),
            'late'        => (clone $statsBase)->where('status', 'late')->count(),
            'absent'      => (clone $statsBase)->where('status', 'absent')->count(),
            'half_day'    => (clone $statsBase)->where('status', 'half_day')->count(),
            'total_hours' => (clone $statsBase)->sum('working_hours'),
        ];

        $staffList = $this->staffQuery()->orderBy('name')->get(['id', 'name', 'role']);

        return view('admin.attendance.index', compact('records', 'stats', 'staffList', 'dateFrom', 'dateTo'));
    }

    // ── Chấm công nhanh (bulk) ─────────────────────────────────────

    /**
     * Hiển thị trang chấm công nhanh — toàn bộ nhân viên theo ngày.
     */
    public function bulk(Request $request)
    {
        $date = $request->filled('date') ? $request->date : today()->toDateString();

        $staffList = $this->staffQuery()->orderBy('name')->get(['id', 'name', 'role', 'avatar']);

        // Load các bản ghi đã có cho ngày đó
        $existing = AttendanceRecord::where('work_date', $date)
            ->whereHas('user', fn($q) => $q->whereIn('role', self::STAFF_ROLES))
            ->get()
            ->keyBy('user_id');

        $workLocations = config('attendance.work_locations', []);
        $shifts        = config('attendance.shifts', []);

        return view('admin.attendance.bulk', compact('staffList', 'existing', 'date', 'workLocations', 'shifts'));
    }

    /**
     * Xử lý chấm công nhanh hàng loạt.
     * Mỗi dòng trong form là 1 nhân viên; tạo hoặc cập nhật bản ghi tương ứng.
     */
    public function bulkStore(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $request->validate([
            'date'              => 'required|date',
            'records'           => 'required|array',
            'records.*.status'  => ['required', Rule::in(['working', 'present', 'late', 'absent', 'half_day'])],
        ], [
            'records.*.status.required' => 'Trạng thái là bắt buộc cho mỗi nhân viên.',
        ]);

        $count = 0;

        foreach ($request->input('records', []) as $userId => $row) {
            // Bỏ qua nếu không chọn trạng thái hoặc ô trống
            if (empty($row['status'])) continue;

            $checkIn  = $this->parseTime($date, $row['check_in_at']  ?? null);
            $checkOut = $this->parseTime($date, $row['check_out_at'] ?? null);

            // Tìm bản ghi đã có hoặc tạo mới
            $record = AttendanceRecord::firstOrNew([
                'user_id'   => $userId,
                'work_date' => $date,
            ]);

            $record->fill([
                'check_in_at'   => $checkIn,
                'check_out_at'  => $checkOut,
                'status'        => $row['status'],
                'shift'         => $row['shift'] ?? 'full_day',
                'work_location' => $row['work_location'] ?? null,
                'note'          => $row['note'] ?? null,
                'recorded_by'   => auth()->id(),
            ]);

            // Tính late_minutes ngay khi có check_in
            if ($checkIn) {
                $record->computeLateMinutes();
            }

            // Chốt status nếu có đủ check_in + check_out
            if ($checkIn && $checkOut) {
                $record->computeFinalStatus();
            } elseif (! $checkIn && ! $checkOut && $record->status !== 'absent') {
                // Chỉ có status thủ công (absent / half_day), reset giờ
                $record->working_hours = null;
                $record->late_minutes  = null;
            }

            $record->save();
            $count++;
        }

        SystemLogger::log('Chấm công nhanh', 'Ngày: ' . Carbon::parse($date)->format('d/m/Y') . ' — ' . $count . ' bản ghi');

        return redirect()->to(portal_route('attendance.bulk') . '?date=' . $date)
            ->with('success', "Đã lưu {$count} bản ghi chấm công cho ngày " . Carbon::parse($date)->format('d/m/Y') . '.');
    }

    // ── Tạo đơn lẻ ────────────────────────────────────────────────

    public function create()
    {
        $staffList     = $this->staffQuery()->orderBy('name')->get(['id', 'name', 'role']);
        $workLocations = config('attendance.work_locations', []);
        $shifts        = config('attendance.shifts', []);

        return view('admin.attendance.create', compact('staffList', 'workLocations', 'shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'work_date'     => 'required|date',
            'check_in_at'   => 'nullable|date_format:H:i',
            'check_out_at'  => 'nullable|date_format:H:i|after:check_in_at',
            'status'        => ['required', Rule::in(['working', 'present', 'late', 'absent', 'half_day'])],
            'shift'         => ['required', Rule::in(['full_day', 'morning', 'afternoon'])],
            'work_location' => 'nullable|string|max:100',
            'note'          => 'nullable|string|max:500',
        ], [
            'user_id.required'   => 'Vui lòng chọn nhân viên.',
            'work_date.required' => 'Vui lòng chọn ngày.',
            'check_out_at.after' => 'Giờ ra phải sau giờ vào.',
            'status.required'    => 'Vui lòng chọn trạng thái.',
        ]);

        // Unique check (tầng ứng dụng — DB cũng có constraint)
        if (AttendanceRecord::where('user_id', $validated['user_id'])
                            ->where('work_date', $validated['work_date'])
                            ->exists()) {
            return back()->withErrors(['work_date' =>
                'Nhân viên này đã có bản ghi chấm công cho ngày '
                . Carbon::parse($validated['work_date'])->format('d/m/Y') . '.'
            ])->withInput();
        }

        $workDate = $validated['work_date'];
        $checkIn  = $this->parseTime($workDate, $request->check_in_at);
        $checkOut = $this->parseTime($workDate, $request->check_out_at);

        $record = new AttendanceRecord([
            'user_id'       => $validated['user_id'],
            'work_date'     => $workDate,
            'check_in_at'   => $checkIn,
            'check_out_at'  => $checkOut,
            'status'        => $validated['status'],
            'shift'         => $validated['shift'],
            'work_location' => $validated['work_location'] ?? null,
            'note'          => $validated['note'] ?? null,
            'recorded_by'   => auth()->id(),
        ]);

        // BƯỚC 1: tính late_minutes ngay khi có check_in (dù chưa checkout)
        if ($checkIn) {
            $record->computeLateMinutes();
        }

        // BƯỚC 2: chốt status cuối nếu đã checkout
        if ($checkIn && $checkOut) {
            $record->computeFinalStatus();
        }

        $record->save();

        $staff = User::find($validated['user_id']);
        SystemLogger::log('Thêm chấm công', 'Nhân viên: ' . $staff->name . ' — ngày ' . Carbon::parse($workDate)->format('d/m/Y'));

        return redirect()->to(portal_route('attendance.index'))
            ->with('success', 'Đã thêm bản ghi chấm công cho ' . $staff->name . '.');
    }

    // ── Chỉnh sửa ─────────────────────────────────────────────────

    public function edit($id)
    {
        $record        = AttendanceRecord::with('user')->findOrFail($id);
        $workLocations = config('attendance.work_locations', []);
        $shifts        = config('attendance.shifts', []);

        return view('admin.attendance.edit', compact('record', 'workLocations', 'shifts'));
    }

    /**
     * update() luôn recalculate late_minutes và working_hours.
     * Không bao giờ giữ lại giá trị cũ khi giờ check-in/out thay đổi.
     */
    public function update(Request $request, $id)
    {
        $record = AttendanceRecord::findOrFail($id);

        $validated = $request->validate([
            'check_in_at'   => 'nullable|date_format:H:i',
            'check_out_at'  => 'nullable|date_format:H:i|after:check_in_at',
            'status'        => ['required', Rule::in(['working', 'present', 'late', 'absent', 'half_day'])],
            'shift'         => ['required', Rule::in(['full_day', 'morning', 'afternoon'])],
            'work_location' => 'nullable|string|max:100',
            'note'          => 'nullable|string|max:500',
        ], [
            'check_out_at.after' => 'Giờ ra phải sau giờ vào.',
            'status.required'    => 'Vui lòng chọn trạng thái.',
        ]);

        $workDate = $record->work_date->format('Y-m-d');
        $checkIn  = $this->parseTime($workDate, $request->check_in_at);
        $checkOut = $this->parseTime($workDate, $request->check_out_at);

        $record->fill([
            'check_in_at'   => $checkIn,
            'check_out_at'  => $checkOut,
            'status'        => $validated['status'],
            'shift'         => $validated['shift'],
            'work_location' => $validated['work_location'] ?? null,
            'note'          => $validated['note'] ?? null,
            'recorded_by'   => auth()->id(),
        ]);

        // Luôn reset trước để tránh giữ giá trị cũ
        $record->working_hours = null;
        $record->late_minutes  = null;

        // BƯỚC 1: recalculate late_minutes theo check_in mới
        if ($checkIn) {
            $record->computeLateMinutes();
        }

        // BƯỚC 2: chốt status nếu đủ dữ liệu
        if ($checkIn && $checkOut) {
            $record->computeFinalStatus();
        }
        // Nếu chỉ có check_in → status giữ 'working' (Admin đã set ở form)

        $record->save();

        SystemLogger::log('Sửa chấm công', 'Nhân viên: ' . $record->user->name . ' — ngày ' . $record->work_date->format('d/m/Y'));

        return redirect()->to(portal_route('attendance.index'))
            ->with('success', 'Đã cập nhật bản ghi chấm công cho ' . $record->user->name . '.');
    }

    // ── Xem chi tiết ──────────────────────────────────────────────

    public function show($id)
    {
        $record = AttendanceRecord::with(['user', 'recorder'])->findOrFail($id);
        return view('admin.attendance.show', compact('record'));
    }

    // ── Xuất báo cáo tháng ────────────────────────────────────────

    public function export(Request $request)
    {
        $month  = $request->get('month', now()->format('Y-m'));
        $userId = $request->get('user_id');

        [$year, $mon] = explode('-', $month);

        $query = AttendanceRecord::with('user')
            ->whereYear('work_date', $year)
            ->whereMonth('work_date', $mon)
            ->whereHas('user', fn($q) => $q->whereIn('role', self::STAFF_ROLES));

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $grouped   = $query->orderBy('user_id')->orderBy('work_date')->get()->groupBy('user_id');
        $staffList = $this->staffQuery()->orderBy('name')->get(['id', 'name', 'role']);

        return view('admin.attendance.export', compact('grouped', 'month', 'year', 'mon', 'staffList', 'userId'));
    }
}
