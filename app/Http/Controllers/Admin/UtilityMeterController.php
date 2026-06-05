<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\UtilityMeter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UtilityMeterController extends Controller
{
    /**
     * Danh sách chỉ số điện nước + thống kê tổng quan
     */
    public function index(Request $request): View
    {
        $blockId = $request->query('block_id');
        $floorId = $request->query('floor_id');
        $type    = $request->query('type');
        $month   = $request->query('month');
        $year    = $request->query('year');

        if (!$month || !$year) {
            $latest = UtilityMeter::orderByDesc('record_year')
                ->orderByDesc('record_month')
                ->first();

            if ($latest) {
                $month = $month ?: $latest->record_month;
                $year  = $year ?: $latest->record_year;
            } else {
                $month = $month ?: now()->month;
                $year  = $year ?: now()->year;
            }
        }

        // Query chính
        $query = UtilityMeter::with(['apartment.floor.block', 'recorder'])
            ->where('record_month', $month)
            ->where('record_year', $year);

        if ($type) {
            $query->where('type', $type);
        }

        if ($floorId) {
            $query->whereHas('apartment', fn ($q) => $q->where('floor_id', $floorId));
        } elseif ($blockId) {
            $query->whereHas('apartment.floor', fn ($q) => $q->where('block_id', $blockId));
        }

        $readings = $query->orderBy('type')
            ->orderBy('apartment_id')
            ->paginate(20)
            ->withQueryString();

        // Thống kê tổng quan cho tháng/năm đã chọn
        $statsQuery = UtilityMeter::where('record_month', $month)->where('record_year', $year);

        $stats = [
            'total_records'       => (clone $statsQuery)->count(),
            'total_electricity'   => (clone $statsQuery)->where('type', 'electricity')->sum('usage_amount'),
            'total_water'         => (clone $statsQuery)->where('type', 'water')->sum('usage_amount'),
            'apartments_recorded' => (clone $statsQuery)->distinct('apartment_id')->count('apartment_id'),
            'apartments_total'    => Apartment::where('status', '!=', 'maintenance')->count(),
        ];

        // Dữ liệu filter
        $blocks = Block::orderBy('name')->get();
        $floors = Floor::with('block')->orderBy('floor_number')->get();

        return view('admin.utility-readings.index', compact(
            'readings', 'blocks', 'floors', 'blockId', 'floorId', 'type', 'month', 'year', 'stats'
        ));
    }

    /**
     * Form ghi chỉ số đơn lẻ
     */
    public function create(): View
    {
        $blocks     = Block::orderBy('name')->get();
        $apartments = Apartment::with('floor.block')
            ->where('status', '!=', 'maintenance')
            ->orderBy('apartment_number')
            ->get();

        return view('admin.utility-readings.create', compact('blocks', 'apartments'));
    }

    /**
     * Lưu chỉ số đơn lẻ
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'apartment_id'  => 'required|exists:apartments,id',
            'type'          => 'required|in:electricity,water',
            'record_month'  => 'required|integer|min:1|max:12',
            'record_year'   => 'required|integer|min:2020|max:2100',
            'new_value'     => 'required|integer|min:0',
        ], [
            'apartment_id.required' => 'Vui lòng chọn căn hộ.',
            'type.required'         => 'Vui lòng chọn loại (điện/nước).',
            'new_value.required'    => 'Vui lòng nhập chỉ số mới.',
            'new_value.min'         => 'Chỉ số mới phải >= 0.',
        ]);

        // Kiểm tra trùng
        $exists = UtilityMeter::where('apartment_id', $validated['apartment_id'])
            ->where('type', $validated['type'])
            ->where('record_month', $validated['record_month'])
            ->where('record_year', $validated['record_year'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'apartment_id' => 'Chỉ số loại này đã được ghi cho căn hộ trong tháng/năm đã chọn.',
            ]);
        }

        // Tự động lấy chỉ số cũ
        $oldValue = UtilityMeter::getPreviousNewValue(
            $validated['apartment_id'],
            $validated['type'],
            $validated['record_month'],
            $validated['record_year']
        ) ?? 0;

        UtilityMeter::create([
            'apartment_id'  => $validated['apartment_id'],
            'type'          => $validated['type'],
            'record_month'  => $validated['record_month'],
            'record_year'   => $validated['record_year'],
            'old_value'     => $oldValue,
            'new_value'     => $validated['new_value'],
            'recorded_by'   => Auth::id(),
        ]);

        $this->syncInvoice((int) $validated['apartment_id'], (int) $validated['record_month'], (int) $validated['record_year']);

        return redirect()->route('admin.utility-readings.index', [
            'month' => $validated['record_month'],
            'year'  => $validated['record_year'],
        ])->with('success', 'Đã ghi chỉ số thành công và đồng bộ hóa đơn.');
    }

    /**
     * Form ghi hàng loạt
     */
    public function batchCreate(Request $request): View
    {
        $blocks = Block::orderBy('name')->get();
        $floors = Floor::with('block')->orderBy('floor_number')->get();

        $selectedBlockId = $request->query('block_id');
        $selectedFloorId = $request->query('floor_id');
        $selectedMonth   = (int) $request->query('month', now()->month);
        $selectedYear    = (int) $request->query('year', now()->year);

        $apartments = collect();

        if ($selectedFloorId) {
            $apartments = Apartment::where('floor_id', $selectedFloorId)
                ->where('status', '!=', 'maintenance')
                ->orderBy('apartment_number')
                ->get();
        } elseif ($selectedBlockId) {
            $floorIds = Floor::where('block_id', $selectedBlockId)->pluck('id');
            $apartments = Apartment::whereIn('floor_id', $floorIds)
                ->where('status', '!=', 'maintenance')
                ->orderBy('apartment_number')
                ->get();
        }

        // Lấy chỉ số cũ + trạng thái cho CẢ điện và nước mỗi căn hộ
        $apartmentData = [];
        foreach ($apartments as $apt) {
            $elecRecorded = UtilityMeter::where('apartment_id', $apt->id)
                ->where('type', 'electricity')
                ->where('record_month', $selectedMonth)
                ->where('record_year', $selectedYear)
                ->exists();

            $waterRecorded = UtilityMeter::where('apartment_id', $apt->id)
                ->where('type', 'water')
                ->where('record_month', $selectedMonth)
                ->where('record_year', $selectedYear)
                ->exists();

            $elecOld = UtilityMeter::getPreviousNewValue(
                $apt->id, 'electricity', $selectedMonth, $selectedYear
            ) ?? 0;

            $waterOld = UtilityMeter::getPreviousNewValue(
                $apt->id, 'water', $selectedMonth, $selectedYear
            ) ?? 0;

            $apartmentData[] = [
                'apartment'      => $apt,
                'elec_old'       => $elecOld,
                'water_old'      => $waterOld,
                'elec_recorded'  => $elecRecorded,
                'water_recorded' => $waterRecorded,
                'both_recorded'  => $elecRecorded && $waterRecorded,
            ];
        }

        return view('admin.utility-readings.batch', compact(
            'blocks', 'floors', 'apartmentData',
            'selectedBlockId', 'selectedFloorId', 'selectedMonth', 'selectedYear'
        ));
    }

    /**
     * Lưu hàng loạt – cả điện lẫn nước trong một lần submit
     */
    public function batchStore(Request $request): RedirectResponse
    {
        $request->validate([
            'record_month' => 'required|integer|min:1|max:12',
            'record_year'  => 'required|integer|min:2020|max:2100',
            'readings'     => 'required|array|min:1',
            'readings.*.apartment_id' => 'required|exists:apartments,id',
        ], [
            'readings.required' => 'Vui lòng nhập ít nhất 1 chỉ số.',
        ]);

        $month   = (int) $request->record_month;
        $year    = (int) $request->record_year;
        $saved   = 0;
        $skipped = 0;
        $affectedApartments = [];

        foreach ($request->readings as $reading) {
            $aptId = $reading['apartment_id'];
            $elecSaved = false;
            $waterSaved = false;

            // ── Điện ────────────────────────────────────
            if (isset($reading['elec_new']) && $reading['elec_new'] !== '') {
                $elecExists = UtilityMeter::where('apartment_id', $aptId)
                    ->where('type', 'electricity')
                    ->where('record_month', $month)
                    ->where('record_year', $year)
                    ->exists();

                if ($elecExists) {
                    $skipped++;
                } else {
                    $elecOld = UtilityMeter::getPreviousNewValue($aptId, 'electricity', $month, $year) ?? 0;
                    UtilityMeter::create([
                        'apartment_id' => $aptId,
                        'type'         => 'electricity',
                        'record_month' => $month,
                        'record_year'  => $year,
                        'old_value'    => $elecOld,
                        'new_value'    => (int) $reading['elec_new'],
                        'recorded_by'  => Auth::id(),
                    ]);
                    $saved++;
                    $elecSaved = true;
                }
            }

            // ── Nước ────────────────────────────────────
            if (isset($reading['water_new']) && $reading['water_new'] !== '') {
                $waterExists = UtilityMeter::where('apartment_id', $aptId)
                    ->where('type', 'water')
                    ->where('record_month', $month)
                    ->where('record_year', $year)
                    ->exists();

                if ($waterExists) {
                    $skipped++;
                } else {
                    $waterOld = UtilityMeter::getPreviousNewValue($aptId, 'water', $month, $year) ?? 0;
                    UtilityMeter::create([
                        'apartment_id' => $aptId,
                        'type'         => 'water',
                        'record_month' => $month,
                        'record_year'  => $year,
                        'old_value'    => $waterOld,
                        'new_value'    => (int) $reading['water_new'],
                        'recorded_by'  => Auth::id(),
                    ]);
                    $saved++;
                    $waterSaved = true;
                }
            }

            if ($elecSaved || $waterSaved) {
                $affectedApartments[$aptId] = true;
            }
        }

        // Đồng bộ hóa hóa đơn cho tất cả căn hộ bị ảnh hưởng
        foreach (array_keys($affectedApartments) as $aptId) {
            $this->syncInvoice((int) $aptId, $month, $year);
        }

        $message = "Đã ghi thành công {$saved} chỉ số điện/nước và cập nhật hóa đơn tương ứng.";
        if ($skipped > 0) {
            $message .= " Bỏ qua {$skipped} mục đã chốt trước đó.";
        }

        return redirect()->route('admin.utility-readings.index', [
            'month' => $month,
            'year'  => $year,
        ])->with('success', $message);
    }

    /**
     * Form chỉnh sửa
     */
    public function edit(int $id): View
    {
        $reading = UtilityMeter::with('apartment.floor.block')->findOrFail($id);

        return view('admin.utility-readings.edit', compact('reading'));
    }

    /**
     * Cập nhật chỉ số
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $reading = UtilityMeter::findOrFail($id);

        $validated = $request->validate([
            'old_value' => 'required|integer|min:0',
            'new_value' => 'required|integer|min:0|gte:old_value',
        ], [
            'old_value.required' => 'Vui lòng nhập chỉ số cũ.',
            'new_value.required' => 'Vui lòng nhập chỉ số mới.',
            'new_value.gte'      => 'Chỉ số mới phải >= chỉ số cũ.',
        ]);

        $reading->update($validated);

        $this->syncInvoice($reading->apartment_id, (int) $reading->record_month, (int) $reading->record_year);

        return redirect()->route('admin.utility-readings.index', [
            'month' => $reading->record_month,
            'year'  => $reading->record_year,
        ])->with('success', 'Đã cập nhật chỉ số và hóa đơn thành công.');
    }

    /**
     * Xóa chỉ số
     */
    public function destroy(int $id): RedirectResponse
    {
        $reading = UtilityMeter::findOrFail($id);
        $month   = $reading->record_month;
        $year    = $reading->record_year;
        $apartmentId = $reading->apartment_id;

        $reading->delete();

        $this->syncInvoice($apartmentId, (int) $month, (int) $year);

        return redirect()->route('admin.utility-readings.index', [
            'month' => $month,
            'year'  => $year,
        ])->with('success', 'Đã xóa chỉ số và đồng bộ hóa đơn thành công.');
    }

    /**
     * AJAX – Lấy chỉ số cũ từ kỳ trước
     */
    public function getOldValue(Request $request): JsonResponse
    {
        $request->validate([
            'apartment_id' => 'required|integer',
            'type'         => 'required|in:electricity,water',
            'month'        => 'required|integer|min:1|max:12',
            'year'         => 'required|integer|min:2020|max:2100',
        ]);

        $oldValue = UtilityMeter::getPreviousNewValue(
            $request->apartment_id,
            $request->type,
            $request->month,
            $request->year
        );

        return response()->json(['old_value' => $oldValue ?? 0]);
    }

    /**
     * Tự động đồng bộ hóa hóa đơn tiền điện nước dựa trên số lượng tiêu thụ thực tế.
     */
    private function syncInvoice(int $apartmentId, int $month, int $year): void
    {
        // 1. Tìm tất cả chỉ số điện và nước của căn hộ này trong tháng/năm
        $elecReading = UtilityMeter::where('apartment_id', $apartmentId)
            ->where('type', 'electricity')
            ->where('record_month', $month)
            ->where('record_year', $year)
            ->first();

        $waterReading = UtilityMeter::where('apartment_id', $apartmentId)
            ->where('type', 'water')
            ->where('record_month', $month)
            ->where('record_year', $year)
            ->first();

        // 2. Đảm bảo ServicePrice tồn tại cho điện và nước
        $elecService = \App\Models\ServicePrice::firstOrCreate(
            ['type' => 'electricity', 'status' => 'active'],
            [
                'name' => 'Phí tiền điện',
                'unit_price' => 2500,
                'description' => 'Đơn giá điện mặc định hàng tháng (đ/kWh)'
            ]
        );

        $waterService = \App\Models\ServicePrice::firstOrCreate(
            ['type' => 'water', 'status' => 'active'],
            [
                'name' => 'Phí tiền nước',
                'unit_price' => 10000,
                'description' => 'Đơn giá nước mặc định hàng tháng (đ/m³)'
            ]
        );

        // Nếu không có bất kỳ chỉ số tiêu thụ nào, hãy tìm hóa đơn hiện tại và xóa nếu cần
        if (!$elecReading && !$waterReading) {
            $existingInvoice = \App\Models\Invoice::where('apartment_id', $apartmentId)
                ->where('billing_month', $month)
                ->where('billing_year', $year)
                ->where('title', 'like', '%Phí điện nước%')
                ->first();
            if ($existingInvoice) {
                $existingInvoice->details()->delete();
                $existingInvoice->delete();
            }
            return;
        }

        // 3. Tính toán tiền điện, nước
        $elecAmount = 0;
        $waterAmount = 0;

        if ($elecReading) {
            $elecAmount = $elecReading->usage_amount * $elecService->unit_price;
        }
        if ($waterReading) {
            $waterAmount = $waterReading->usage_amount * $waterService->unit_price;
        }

        $totalAmount = $elecAmount + $waterAmount;

        // 4. Tạo hoặc cập nhật hóa đơn (bills)
        $invoice = \App\Models\Invoice::updateOrCreate(
            [
                'apartment_id' => $apartmentId,
                'billing_month' => $month,
                'billing_year' => $year,
            ],
            [
                'title' => "Phí điện nước tháng {$month}/{$year}",
                'due_date' => now()->addDays(10), // Hạn nộp 10 ngày từ ngày chốt
                'total_amount' => $totalAmount,
                'status' => 'unpaid',
            ]
        );

        // 5. Đồng bộ chi tiết hóa đơn (bill_details)
        // Xóa các chi tiết cũ liên quan đến điện/nước
        $invoice->details()->whereIn('service_price_id', [$elecService->id, $waterService->id])->delete();

        if ($elecReading && $elecReading->usage_amount > 0) {
            \App\Models\InvoiceDetail::create([
                'bill_id' => $invoice->id,
                'service_price_id' => $elecService->id,
                'quantity' => $elecReading->usage_amount,
                'amount' => $elecAmount,
            ]);
        }

        if ($waterReading && $waterReading->usage_amount > 0) {
            \App\Models\InvoiceDetail::create([
                'bill_id' => $invoice->id,
                'service_price_id' => $waterService->id,
                'quantity' => $waterReading->usage_amount,
                'amount' => $waterAmount,
            ]);
        }
    }

    /**
     * Xuất file Excel mẫu chỉ số điện nước (Dạng Bảng Ngang) có Protect Sheet để BQL điền
     */
    public function downloadTemplate(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year  = (int) $request->query('year', now()->year);

        // Lấy tất cả căn hộ không ở trạng thái bảo trì
        $apartments = Apartment::with('floor.block')
            ->where('status', '!=', 'maintenance')
            ->get();

        // Sắp xếp tự nhiên trên PHP collection để bảo đảm thứ tự chính xác tuyệt đối (Tòa nhà -> Tầng -> Số phòng)
        $apartments = $apartments->sort(function ($a, $b) {
            // 1. So sánh Tòa nhà (Block)
            $blockA = $a->floor->block->name ?? '';
            $blockB = $b->floor->block->name ?? '';
            $cmpBlock = strcasecmp($blockA, $blockB);
            if ($cmpBlock !== 0) {
                return $cmpBlock;
            }

            // 2. So sánh Tầng (Floor)
            // Lấy floor_number, nếu null hoặc không hợp lệ thì cố gắng tách số từ tên tầng (ví dụ "Tầng 3" -> 3)
            $floorNumA = $a->floor->floor_number ?? null;
            if (is_null($floorNumA) && isset($a->floor->name)) {
                preg_match('/\d+/', $a->floor->name, $matches);
                $floorNumA = isset($matches[0]) ? (int)$matches[0] : 0;
            }
            $floorNumA = (int)$floorNumA;

            $floorNumB = $b->floor->floor_number ?? null;
            if (is_null($floorNumB) && isset($b->floor->name)) {
                preg_match('/\d+/', $b->floor->name, $matches);
                $floorNumB = isset($matches[0]) ? (int)$matches[0] : 0;
            }
            $floorNumB = (int)$floorNumB;

            if ($floorNumA !== $floorNumB) {
                return $floorNumA <=> $floorNumB;
            }

            // 3. So sánh Số phòng (Apartment Number) - Sắp xếp tự nhiên (Natural Sort)
            // Ví dụ: 101, 102, 102B, 1001...
            return strnatcasecmp($a->apartment_number, $b->apartment_number);
        });

        $filename = "Mau_Chot_So_Dien_Nuoc_Thang_{$month}_{$year}.xlsx";

        try {
            $tempFilePath = \App\Helpers\SimpleXlsx::exportUtilityTemplate($apartments, $month, $year);

            return response()->download($tempFilePath, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi xuất file Excel mẫu: ' . $e->getMessage());
        }
    }

    /**
     * Nhận file Excel hoặc CSV chỉ số điện nước dạng bảng ngang và import vào hệ thống
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'import_month' => 'required|integer|min:1|max:12',
            'import_year'  => 'required|integer|min:2020|max:2100',
            'csv_file'     => 'required|file|mimes:xlsx,xls,csv,txt|max:4096',
        ], [
            'import_month.required' => 'Vui lòng chọn tháng áp dụng.',
            'import_year.required'  => 'Vui lòng chọn năm áp dụng.',
            'csv_file.required'     => 'Vui lòng chọn file Excel/CSV để tải lên.',
            'csv_file.file'         => 'File tải lên không hợp lệ.',
            'csv_file.mimes'        => 'Hệ thống chỉ chấp nhận file Excel (.xlsx, .xls) hoặc CSV.',
            'csv_file.max'          => 'Kích thước file tối đa là 4MB.',
        ]);

        $month = (int)$request->import_month;
        $year  = (int)$request->import_year;

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        $rows = [];

        if (in_array($extension, ['xlsx', 'xls'])) {
            try {
                $rows = \App\Helpers\SimpleXlsx::parse($filePath);
            } catch (\Exception $e) {
                return back()->with('error', 'Lỗi khi đọc file Excel: ' . $e->getMessage());
            }
        } else {
            // Đọc file CSV
            $handle = fopen($filePath, 'r');
            if (!$handle) {
                return back()->with('error', 'Không thể mở file CSV vừa tải lên.');
            }

            // Đọc dòng đầu tiên để kiểm tra sep= hoặc lấy BOM
            $firstLine = fgets($handle);
            if ($firstLine === false) {
                fclose($handle);
                return back()->with('error', 'File CSV trống.');
            }

            $cleanFirstLine = $firstLine;
            $bom = chr(0xEF) . chr(0xBB) . chr(0xBF);
            if (str_starts_with($cleanFirstLine, $bom)) {
                $cleanFirstLine = substr($cleanFirstLine, 3);
            }

            // Tự động phát hiện dấu phân cách (dấu phẩy , hoặc dấu chấm phẩy ;)
            $commaCount = substr_count($cleanFirstLine, ',');
            $semicolonCount = substr_count($cleanFirstLine, ';');
            $delimiter = ($semicolonCount > $commaCount) ? ';' : ',';

            if (str_starts_with(trim($cleanFirstLine), 'sep=')) {
                $declaredSep = trim(str_replace('sep=', '', $cleanFirstLine));
                if (!empty($declaredSep)) {
                    $delimiter = $declaredSep;
                }
                // Đọc tiếp dòng thứ 2 làm headers
                $headersRow = fgetcsv($handle, 0, $delimiter);
                if ($headersRow) {
                    $rows[] = $headersRow;
                }
            } else {
                // Parse dòng đầu tiên làm headers
                $rows[] = str_getcsv($cleanFirstLine, $delimiter);
            }

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }

        if (count($rows) < 2) {
            return back()->with('error', 'File tải lên không có dữ liệu chốt số.');
        }

        $headers = $rows[0];
        $hasIdColumn = false;
        if ($headers && count($headers) >= 7 && str_contains(strtolower($headers[0]), 'id')) {
            $hasIdColumn = true;
        }

        $minColumns = $hasIdColumn ? 7 : 6;
        if (!$headers || count($headers) < $minColumns) {
            return back()->with('error', "Cấu trúc file không đúng mẫu. Yêu cầu tối thiểu {$minColumns} cột.");
        }

        $successCount = 0;
        $errors = [];
        $affectedApartments = [];

        // DB transaction để bảo đảm an toàn dữ liệu
        \DB::beginTransaction();

        try {
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $rowNum = $i + 1; // Số dòng thực tế (1-indexed)

                // Bỏ qua dòng trống
                if (count($row) < $minColumns || empty(array_filter($row))) {
                    continue;
                }

                if ($hasIdColumn) {
                    $aptId       = trim($row[0]);
                    $location    = trim($row[1]);
                    $aptNumber   = trim($row[2]);
                    $elecOld     = isset($row[3]) && $row[3] !== '' ? (int)$row[3] : 0;
                    $elecNewStr  = trim($row[4]);
                    $waterOld    = isset($row[5]) && $row[5] !== '' ? (int)$row[5] : 0;
                    $waterNewStr = trim($row[6]);
                } else {
                    $aptId       = null;
                    $location    = trim($row[0]);
                    $aptNumber   = trim($row[1]);
                    $elecOld     = isset($row[2]) && $row[2] !== '' ? (int)$row[2] : 0;
                    $elecNewStr  = trim($row[3]);
                    $waterOld    = isset($row[4]) && $row[4] !== '' ? (int)$row[4] : 0;
                    $waterNewStr = trim($row[5]);
                }

                // Nếu không có bất kỳ chỉ số mới nào được điền, bỏ qua căn hộ này
                if ($elecNewStr === '' && $waterNewStr === '') {
                    continue;
                }

                // Định danh Căn hộ (Ưu tiên ID, fallback tìm theo block/floor/apartment_number)
                $apartment = null;
                if (is_numeric($aptId)) {
                    $apartment = Apartment::find((int)$aptId);
                }

                if (!$apartment) {
                    $parts = explode('/', $location);
                    if (count($parts) === 2) {
                        $blockName = trim($parts[0]);
                        $floorName = trim($parts[1]);
                        
                        $block = Block::where('name', $blockName)->first();
                        if ($block) {
                            $floor = Floor::where('block_id', $block->id)->where('name', $floorName)->first();
                            if ($floor) {
                                $apartment = Apartment::where('floor_id', $floor->id)
                                    ->where('apartment_number', $aptNumber)
                                    ->first();
                            }
                        }
                    }
                }

                if (!$apartment) {
                    $errors[] = "Dòng {$rowNum}: Không thể xác định căn hộ '{$aptNumber}' thuộc vị trí '{$location}' (ID: {$aptId}).";
                    continue;
                }

                $updated = false;

                // ── Xử lý Điện ──────────────────────────────
                if ($elecNewStr !== '') {
                    $elecNew = (int)$elecNewStr;
                    if ($elecNew < $elecOld) {
                        $errors[] = "Dòng {$rowNum}: Căn hộ {$apartment->apartment_number} - Chỉ số ĐIỆN mới ({$elecNew}) phải lớn hơn hoặc bằng chỉ số cũ ({$elecOld}).";
                        continue;
                    }

                    UtilityMeter::updateOrCreate(
                        [
                            'apartment_id' => $apartment->id,
                            'type'         => 'electricity',
                            'record_month' => $month,
                            'record_year'  => $year,
                        ],
                        [
                            'old_value'   => $elecOld,
                            'new_value'   => $elecNew,
                            'recorded_by' => Auth::id(),
                        ]
                    );
                    $successCount++;
                    $updated = true;
                }

                // ── Xử lý Nước ──────────────────────────────
                if ($waterNewStr !== '') {
                    $waterNew = (int)$waterNewStr;
                    if ($waterNew < $waterOld) {
                        $errors[] = "Dòng {$rowNum}: Căn hộ {$apartment->apartment_number} - Chỉ số NƯỚC mới ({$waterNew}) phải lớn hơn hoặc bằng chỉ số cũ ({$waterOld}).";
                        continue;
                    }

                    UtilityMeter::updateOrCreate(
                        [
                            'apartment_id' => $apartment->id,
                            'type'         => 'water',
                            'record_month' => $month,
                            'record_year'  => $year,
                        ],
                        [
                            'old_value'   => $waterOld,
                            'new_value'   => $waterNew,
                            'recorded_by' => Auth::id(),
                        ]
                    );
                    $successCount++;
                    $updated = true;
                }

                if ($updated) {
                    $key = "{$apartment->id}-{$month}-{$year}";
                    $affectedApartments[$key] = [
                        'apartment_id' => $apartment->id,
                        'month'        => $month,
                        'year'         => $year,
                    ];
                }
            }

            if (count($errors) > 0) {
                \DB::rollBack();
                return back()->with('error', 'Import thất bại do có dữ liệu lỗi. Vui lòng kiểm tra lại file mẫu.')->withErrors($errors);
            }

            // Đồng bộ hóa hóa đơn cho tất cả các căn hộ bị ảnh hưởng
            foreach ($affectedApartments as $affected) {
                $this->syncInvoice($affected['apartment_id'], $affected['month'], $affected['year']);
            }

            \DB::commit();

            return redirect()->route('admin.utility-readings.index', [
                'month' => $month,
                'year'  => $year,
            ])->with('success', "Nhập thành công {$successCount} chỉ số điện/nước từ file Excel/CSV và tự động cập nhật hóa đơn tương ứng.");

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi hệ thống trong quá trình import: ' . $e->getMessage());
        }
    }
}
