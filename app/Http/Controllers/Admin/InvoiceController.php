<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SystemLogger;
use App\Models\Invoice;
use App\Models\Apartment;
use App\Models\InvoiceDetail;
use App\Models\ServicePrice;
use App\Models\UtilityMeter;
use App\Models\Vehicle;
use App\Models\Payment;
use App\Notifications\NewInvoiceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Thống kê hóa đơn.
     */
    public function stats(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        // KPI tổng quát
        $totalRevenue     = Invoice::where('status', 'paid')->sum('total_amount');
        $thisMonthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->sum('total_amount');
        $totalInvoices    = Invoice::count();
        $paidCount        = Invoice::where('status', 'paid')->count();
        $unpaidCount      = Invoice::where('status', 'unpaid')->count();
        $overdueCount     = Invoice::where('status', 'unpaid')->where('due_date', '<', now())->count();
        $totalUnpaid      = Invoice::where('status', 'unpaid')->sum('total_amount');
        $totalOverdue     = Invoice::where('status', 'unpaid')->where('due_date', '<', now())->sum('total_amount');

        // Doanh thu 12 tháng của năm được chọn
        $revenueByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $base = Invoice::where('billing_year', $year)->where('billing_month', $m);
            $revenueByMonth[] = [
                'label'   => 'T' . $m,
                'paid'    => (clone $base)->where('status', 'paid')->sum('total_amount'),
                'unpaid'  => (clone $base)->where('status', 'unpaid')->where('due_date', '>=', now())->sum('total_amount'),
                'overdue' => (clone $base)->where('status', 'unpaid')->where('due_date', '<', now())->sum('total_amount'),
            ];
        }

        // Phân tích theo loại phí (join qua bill_details -> service_prices)
        $typeLabels = [
            'electricity'    => 'Tiền điện',
            'water'          => 'Tiền nước',
            'management_fee' => 'Phí quản lý',
            'motorbike'      => 'Phí gửi xe máy',
            'car'            => 'Phí gửi ô tô',
            'bicycle'        => 'Phí gửi xe đạp',
            'electric_bike'  => 'Phí gửi xe điện',
            'parking'        => 'Phí đỗ xe',
            'internet'       => 'Internet',
            'service'        => 'Dịch vụ',
            'other'          => 'Khác',
        ];
        $byType = DB::table('bills')
            ->join('bill_details', 'bills.id', '=', 'bill_details.bill_id')
            ->join('service_prices', 'bill_details.service_price_id', '=', 'service_prices.id')
            ->whereNull('bills.deleted_at')
            ->select('service_prices.type', DB::raw('SUM(bills.total_amount) as total'))
            ->groupBy('service_prices.type')
            ->get()
            ->map(fn($r) => [
                'type'  => $r->type,
                'label' => $typeLabels[$r->type] ?? $r->type,
                'total' => (float) $r->total,
            ]);

        // Hóa đơn gần đây
        $recentInvoices = Invoice::with(['apartment.floor.block'])
            ->orderByDesc('created_at')->limit(8)->get();

        // Top nợ đọng theo căn hộ
        $topDebt = Invoice::with(['apartment.floor.block'])
            ->where('status', 'unpaid')
            ->select('apartment_id', DB::raw('SUM(total_amount) as debt'), DB::raw('COUNT(*) as count'))
            ->groupBy('apartment_id')
            ->orderByDesc('debt')
            ->limit(7)
            ->get();

        // Tỉ lệ thu tiền theo tháng
        $collectionRate = collect(array_map(function ($m) use ($year) {
            $total = Invoice::where('billing_year', $year)->where('billing_month', $m)->count();
            $paid  = Invoice::where('billing_year', $year)->where('billing_month', $m)->where('status', 'paid')->count();
            return ['month' => $m, 'rate' => $total > 0 ? round($paid / $total * 100) : 0];
        }, range(1, 12)));

        return view('admin.invoices.stats', compact(
            'year',
            'totalRevenue',
            'thisMonthRevenue',
            'totalInvoices',
            'paidCount',
            'unpaidCount',
            'overdueCount',
            'totalUnpaid',
            'totalOverdue',
            'revenueByMonth',
            'byType',
            'recentInvoices',
            'topDebt',
            'collectionRate'
        ));
    }

    /**
     * Danh sách hóa đơn gộp theo căn hộ.
     */
    public function index(Request $request)
    {
        $query = Apartment::with(['floor.block'])
            ->withCount('invoices')
            ->withSum('invoices', 'total_amount')
            ->withSum('invoices', 'paid_amount');

        // Lọc theo tháng/năm (format: YYYY-MM)
        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereHas('invoices', function($q) use ($year, $month) {
                $q->where('billing_month', (int) $month)
                  ->where('billing_year', (int) $year);
            });
        }

        // Lọc theo căn hộ
        if ($request->filled('apartment_id')) {
            $query->where('id', $request->apartment_id);
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('invoices', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        // Tìm kiếm theo tên/mã căn hộ hoặc tòa
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('apartment_number', 'like', '%' . $search . '%')
                ->orWhereHas('floor.block', function ($b) use ($search) {
                    $b->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
                });
        }

        $apartmentsPaginated = $query->paginate(20)->withQueryString();
        $apartments = Apartment::with('floor.block')->orderBy('apartment_number')->get();
        $statuses   = ['unpaid', 'partial_paid', 'paid', 'overdue', 'cancelled'];

        return view('admin.invoices.index', compact('apartmentsPaginated', 'apartments', 'statuses'));
    }

    /**
     * Danh sách tất cả hóa đơn của một căn hộ cụ thể.
     */
    public function apartmentInvoices(Request $request, Apartment $apartment)
    {
        $apartment->load(['floor.block']);

        $query = Invoice::with(['details.servicePrice', 'payments'])
            ->where('apartment_id', $apartment->id)
            ->orderBy('billing_year', 'desc')
            ->orderBy('billing_month', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->where('billing_month', (int) $month)
                ->where('billing_year', (int) $year);
        }

        $invoices = $query->paginate(20)->withQueryString();

        // Tổng công nợ của căn hộ
        $totalAmount  = Invoice::where('apartment_id', $apartment->id)->sum('total_amount');
        $totalPaid    = Invoice::where('apartment_id', $apartment->id)->sum('paid_amount');
        $totalDebt    = max(0, $totalAmount - $totalPaid);

        return view('admin.invoices.apartment', compact('apartment', 'invoices', 'totalAmount', 'totalPaid', 'totalDebt'));
    }

    /**
     * Form tạo hóa đơn mới.
     */
    public function create()
    {
        $apartments = Apartment::with('floor.block')->get();
        return view('admin.invoices.create', compact('apartments'));
    }

    /**
     * Lưu hóa đơn mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id'   => 'required|exists:apartments,id',
            'title'          => 'required|string|max:150',
            'type'           => 'required|in:electricity,water,management_fee,parking,other',
            'amount'         => 'required|numeric|min:0',
            'billing_month'  => 'required|string',
            'due_date'       => 'required|date',
            'note'           => 'nullable|string|max:500',
        ], [
            'apartment_id.required'   => 'Vui lòng chọn căn hộ.',
            'apartment_id.exists'     => 'Căn hộ không hợp lệ.',
            'title.required'          => 'Vui lòng nhập tiêu đề hóa đơn.',
            'title.max'               => 'Tiêu đề không được vượt quá 150 ký tự.',
            'type.required'           => 'Vui lòng chọn loại phí.',
            'type.in'                 => 'Loại phí không hợp lệ.',
            'amount.required'         => 'Vui lòng nhập số tiền.',
            'amount.numeric'          => 'Số tiền phải là số.',
            'amount.min'              => 'Số tiền không được âm.',
            'billing_month.required'  => 'Vui lòng chọn kỳ hóa đơn.',
            'due_date.required'       => 'Vui lòng chọn hạn thanh toán.',
            'due_date.date'           => 'Hạn thanh toán không đúng định dạng.',
            'note.max'                => 'Ghi chú không được vượt quá 500 ký tự.',
        ]);

        // Parse month and year from YYYY-MM
        $monthYear = explode('-', $validated['billing_month']);
        $billingMonth = (int) $monthYear[1];
        $billingYear = (int) $monthYear[0];

        // Ensure ServicePrice exists
        $servicePrice = \App\Models\ServicePrice::firstOrCreate(
            ['type' => $validated['type']],
            [
                'name' => 'Phí ' . Invoice::typeLabel($validated['type']),
                'unit_price' => $validated['amount'],
                'status' => 'active',
                'description' => 'Tự động tạo từ màn hình phát hành hóa đơn'
            ]
        );

        // Find if an invoice already exists for the apartment in this month/year
        $invoice = Invoice::where('apartment_id', $validated['apartment_id'])
            ->where('billing_month', $billingMonth)
            ->where('billing_year', $billingYear)
            ->first();

        if ($invoice) {
            // Check if this service type already exists
            $exists = \App\Models\InvoiceDetail::where('bill_id', $invoice->id)
                ->where('service_price_id', $servicePrice->id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Hóa đơn tháng này của căn hộ đã có loại phí này.');
            }

            $invoice->increment('total_amount', $validated['amount']);
        } else {
            $invoice = Invoice::create([
                'apartment_id'  => $validated['apartment_id'],
                'title'         => 'Hóa đơn tháng ' . $billingMonth . '/' . $billingYear,
                'billing_month' => $billingMonth,
                'billing_year'  => $billingYear,
                'due_date'      => $validated['due_date'],
                'total_amount'  => $validated['amount'],
                'status'        => 'unpaid',
            ]);
        }

        // Create InvoiceDetail
        \App\Models\InvoiceDetail::create([
            'bill_id'          => $invoice->id,
            'service_price_id' => $servicePrice->id,
            'quantity'         => 1,
            'amount'           => $validated['amount'],
        ]);



        return redirect()->route('admin.invoices.index')
            ->with('success', 'Hóa đơn đã được tạo thành công.')
            ->with('highlightAptIds', [$validated['apartment_id']]);
    }

    /**
     * Chi tiết hóa đơn.
     */
    public function show(Invoice $invoice)
    {
        $invoice->recalculateDetailsStatus();
        $invoice->load(['apartment.floor.block', 'details.servicePrice', 'payments']);
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Form xuất hóa đơn hàng loạt.
     */
    public function batchCreate(Request $request)
    {
        $selectedMonth = $request->input('billing_month', now()->format('Y-m'));
        [$selectedYear, $selectedMonthNumber] = array_pad(explode('-', $selectedMonth), 2, now()->format('Y-m'));

        $selectedYear = (int) $selectedYear;
        $selectedMonthNumber = (int) $selectedMonthNumber;

        $activePrices = ServicePrice::where('status', 'active')->get();

        $apartments = Apartment::with('floor.block')
            ->where('status', 'occupied')
            ->whereDoesntHave('invoices', function ($query) use ($selectedYear, $selectedMonthNumber) {
                $query->where('billing_month', $selectedMonthNumber)
                    ->where('billing_year', $selectedYear);
            })
            ->orderBy('id')
            ->get();

        $occupiedCount = $apartments->count();
        $totalCount = $apartments->count();
        $totalPriceSum = $activePrices->sum('unit_price');

        return view('admin.invoices.batch', compact(
            'apartments',
            'activePrices',
            'occupiedCount',
            'totalCount',
            'totalPriceSum',
            'selectedMonth'
        ));
    }

    /**
     * Xử lý xuất hóa đơn hàng loạt.
     *
     * Điện/Nước: Đọc chỉ số từ utility_meters (status=approved).
     *   - Nếu đã có: tính tiền theo usage_amount × unit_price.
     *   - Nếu chưa chốt: tạo dòng amount=0, note='Đang chờ chốt chỉ số'.
     * Gửi xe: Tính motorbike_count × motorbike_price + car_count × car_price.
     * Phí khác: Áp đơn giá cố định × 1.
     * Sau khi tạo: Gửi thông báo cho cư dân + ghi Activity Log.
     */
    public function batchStore(Request $request)
    {
        $request->validate([
            'billing_month' => 'required|string',
            'due_date'      => 'required|date',
            'types'         => 'required|array|min:1',
            'types.*'       => 'in:electricity,water,management_fee,motorbike,electric_bike,car,bicycle,internet,service,other',
            'apartment_ids' => 'required|array|min:1',
            'apartment_ids.*' => 'integer|exists:apartments,id',
        ], [
            'billing_month.required' => 'Vui lòng chọn kỳ hóa đơn.',
            'due_date.required'      => 'Vui lòng chọn hạn thanh toán.',
            'due_date.date'          => 'Hạn thanh toán không đúng định dạng ngày.',
            'types.required'         => 'Vui lòng chọn ít nhất một loại phí.',
            'types.min'              => 'Vui lòng chọn ít nhất một loại phí.',
            'types.*.in'             => 'Loại phí không hợp lệ.',
            'apartment_ids.required' => 'Vui lòng chọn ít nhất một căn hộ mục tiêu.',
            'apartment_ids.min'      => 'Vui lòng chọn ít nhất một căn hộ mục tiêu.',
            'apartment_ids.*.exists' => 'Căn hộ đã chọn không hợp lệ.',
        ]);

        [$year, $month] = explode('-', $request->billing_month);
        $month = (int) $month;
        $year  = (int) $year;

        $skipExisting = $request->boolean('skip_existing', true);
        $selectedApartmentIds = array_values(array_unique(array_filter($request->input('apartment_ids', []), fn($id) => is_numeric($id))));

        // --- Lấy danh sách căn hộ đã chọn ---
        $apartments = Apartment::with(['residents.user', 'vehicles'])
            ->whereIn('id', $selectedApartmentIds)
            ->where('status', 'occupied')
            ->get();

        // --- Lấy bảng đơn giá theo type ---
        $activePrices = ServicePrice::where('status', 'active')
            ->whereIn('type', $request->types)
            ->get()
            ->keyBy('type');

        $created = 0;
        $skipped = 0;
        $highlightAptIds = [];
        $errorAptIds = [];

        foreach ($apartments as $apartment) {
            // --- Tìm hoặc chuẩn bị tạo hóa đơn ---
            $invoice = Invoice::where('apartment_id', $apartment->id)
                ->where('billing_month', $month)
                ->where('billing_year', $year)
                ->first();

            $invoiceExistedBefore = (bool) $invoice;
            $invoiceAmountAdded   = 0;
            $pendingUtilityAdded  = false; // Có dòng chờ chốt số hay không

            foreach ($request->types as $type) {

                // ============================================================
                // XỬ LÝ ĐIỆN / NƯỚC
                // ============================================================
                if (in_array($type, ['electricity', 'water'])) {
                    $servicePrice = $activePrices->get($type);
                    if (!$servicePrice) {
                        $skipped++;
                        continue;
                    }

                    // Kiểm tra đã tồn tại trong hóa đơn chưa
                    if ($invoice && $skipExisting) {
                        $exists = InvoiceDetail::where('bill_id', $invoice->id)
                            ->where('service_price_id', $servicePrice->id)
                            ->exists();
                        if ($exists) {
                            $skipped++;
                            continue;
                        }
                    }

                    // Tạo hóa đơn nếu chưa có
                    if (!$invoice) {
                        $invoice = Invoice::create([
                            'apartment_id'  => $apartment->id,
                            'title'         => 'Hóa đơn tháng ' . $month . '/' . $year,
                            'billing_month' => $month,
                            'billing_year'  => $year,
                            'due_date'      => $request->due_date,
                            'total_amount'  => 0,
                            'status'        => 'unpaid',
                        ]);
                    }

                    // Tìm chỉ số trong kỳ này (không bắt buộc status = approved)
                    $meter = UtilityMeter::where('apartment_id', $apartment->id)
                        ->where('type', $type)
                        ->where('record_month', $month)
                        ->where('record_year', $year)
                        ->first();

                    if ($meter && $meter->usage_amount > 0) {
                        // Đã có số liệu: tính tiền theo usage_amount
                        $detailAmount = $meter->usage_amount * $servicePrice->unit_price;
                        InvoiceDetail::create([
                            'bill_id'          => $invoice->id,
                            'service_price_id' => $servicePrice->id,
                            'quantity'         => $meter->usage_amount,
                            'amount'           => $detailAmount,
                        ]);
                        $invoiceAmountAdded += $detailAmount;
                    } else {
                        // Chưa có số liệu: tạo dòng placeholder chờ kế toán bổ sung
                        InvoiceDetail::create([
                            'bill_id'          => $invoice->id,
                            'service_price_id' => $servicePrice->id,
                            'quantity'         => 0,
                            'amount'           => 0,
                            'note'             => 'Đang chờ chốt chỉ số — kế toán bổ sung sau',
                        ]);
                        $pendingUtilityAdded = true;
                        // Không cộng thêm total_amount vì chưa có số tiền
                    }
                    continue;
                }

                // ============================================================
                // XỬ LÝ GỬI XE (MOTORBIKE / ELECTRIC_BIKE / CAR / BICYCLE)
                // ============================================================
                if (in_array($type, ['motorbike', 'electric_bike', 'car', 'bicycle'])) {
                    $servicePrice = $activePrices->get($type);
                    if (!$servicePrice) {
                        $skipped++;
                        continue;
                    }

                    // Kiểm tra đã tồn tại
                    if ($invoice && $skipExisting) {
                        $exists = InvoiceDetail::where('bill_id', $invoice->id)
                            ->where('service_price_id', $servicePrice->id)
                            ->exists();
                        if ($exists) {
                            $skipped++;
                            continue;
                        }
                    }

                    // Đếm xe theo từng loại (chỉ xe active)
                    if ($type === 'motorbike') {
                        $vehicleCount = Vehicle::where('apartment_id', $apartment->id)
                            ->where('vehicle_type', 'motorbike')
                            ->where('status', 'active')
                            ->count();
                    } elseif ($type === 'electric_bike') {
                        $vehicleCount = Vehicle::where('apartment_id', $apartment->id)
                            ->where('vehicle_type', 'electric_bike')
                            ->where('status', 'active')
                            ->count();
                    } elseif ($type === 'car') {
                        $vehicleCount = Vehicle::where('apartment_id', $apartment->id)
                            ->where('vehicle_type', 'car')
                            ->where('status', 'active')
                            ->count();
                    } elseif ($type === 'bicycle') {
                        $vehicleCount = Vehicle::where('apartment_id', $apartment->id)
                            ->where('vehicle_type', 'bicycle')
                            ->where('status', 'active')
                            ->count();
                    }

                    // Không tạo dòng xe nếu căn hộ không có xe nào
                    if ($vehicleCount === 0) {
                        continue;
                    }

                    // Tạo hóa đơn nếu chưa có
                    if (!$invoice) {
                        $invoice = Invoice::create([
                            'apartment_id'  => $apartment->id,
                            'title'         => 'Hóa đơn tháng ' . $month . '/' . $year,
                            'billing_month' => $month,
                            'billing_year'  => $year,
                            'due_date'      => $request->due_date,
                            'total_amount'  => 0,
                            'status'        => 'unpaid',
                        ]);
                    }

                    $detailAmount = $vehicleCount * $servicePrice->unit_price;
                    $vehicleNote = match ($type) {
                        'motorbike'    => "Phí gửi xe máy: " . number_format($servicePrice->unit_price, 0, ',', '.') . "đ/xe x {$vehicleCount} xe",
                        'electric_bike'=> "Phí gửi xe điện: " . number_format($servicePrice->unit_price, 0, ',', '.') . "đ/xe x {$vehicleCount} xe",
                        'car'          => "Phí gửi ô tô: " . number_format($servicePrice->unit_price, 0, ',', '.') . "đ/xe x {$vehicleCount} xe",
                        'bicycle'      => "Phí gửi xe đạp: " . number_format($servicePrice->unit_price, 0, ',', '.') . "đ/xe x {$vehicleCount} xe",
                        default        => "Gửi xe: {$vehicleCount} xe",
                    };

                    InvoiceDetail::create([
                        'bill_id'          => $invoice->id,
                        'service_price_id' => $servicePrice->id,
                        'quantity'         => $vehicleCount,
                        'amount'           => $detailAmount,
                        'note'             => $vehicleNote,
                    ]);

                    $invoiceAmountAdded += $detailAmount;
                    continue;
                }

                // ============================================================
                // PHÍ QUẢN LÝ
                // ============================================================
                if ($type === 'management_fee') {
                    $servicePrice = $activePrices->get($type);
                    if (!$servicePrice) {
                        $skipped++;
                        continue;
                    }

                    if ($invoice && $skipExisting) {
                        $exists = InvoiceDetail::where('bill_id', $invoice->id)
                            ->where('service_price_id', $servicePrice->id)
                            ->exists();
                        if ($exists) {
                            $skipped++;
                            continue;
                        }
                    }

                    if (!$invoice) {
                        $invoice = Invoice::create([
                            'apartment_id'  => $apartment->id,
                            'title'         => 'Hóa đơn tháng ' . $month . '/' . $year,
                            'billing_month' => $month,
                            'billing_year'  => $year,
                            'due_date'      => $request->due_date,
                            'total_amount'  => 0,
                            'status'        => 'unpaid',
                        ]);
                    }

                    $area = $apartment->area ?? 0;
                    $detailAmount = $area * $servicePrice->unit_price;

                    InvoiceDetail::create([
                        'bill_id'          => $invoice->id,
                        'service_price_id' => $servicePrice->id,
                        'quantity'         => $area,
                        'amount'           => $detailAmount,
                        'note'             => "Phí quản lý: " . number_format($servicePrice->unit_price, 0, ',', '.') . "đ/m2 x {$area} m2",
                    ]);

                    $invoiceAmountAdded += $detailAmount;
                    continue;
                }

                // ============================================================
                // INTERNET, DỊCH VỤ KHÁC — ĐƠN GIÁ CỐ ĐỊNH × 1
                // ============================================================
                $servicePrice = $activePrices->get($type);
                if (!$servicePrice) {
                    $skipped++;
                    continue;
                }

                if ($invoice && $skipExisting) {
                    $exists = InvoiceDetail::where('bill_id', $invoice->id)
                        ->where('service_price_id', $servicePrice->id)
                        ->exists();
                    if ($exists) {
                        $skipped++;
                        continue;
                    }
                }

                if (!$invoice) {
                    $invoice = Invoice::create([
                        'apartment_id'  => $apartment->id,
                        'title'         => 'Hóa đơn tháng ' . $month . '/' . $year,
                        'billing_month' => $month,
                        'billing_year'  => $year,
                        'due_date'      => $request->due_date,
                        'total_amount'  => 0,
                        'status'        => 'unpaid',
                    ]);
                }

                InvoiceDetail::create([
                    'bill_id'          => $invoice->id,
                    'service_price_id' => $servicePrice->id,
                    'quantity'         => 1,
                    'amount'           => $servicePrice->unit_price,
                    'note'             => "Phí dịch vụ: " . number_format($servicePrice->unit_price, 0, ',', '.') . "đ x 1",
                ]);

                $invoiceAmountAdded += $servicePrice->unit_price;
            } // end foreach types

            // --- Cập nhật tổng tiền hóa đơn ---
            if ($invoice && $invoiceAmountAdded > 0) {
                $invoice->increment('total_amount', $invoiceAmountAdded);
            }

            // Đếm số hóa đơn mới tạo trong kỳ này
            if ($invoice && !$invoiceExistedBefore) {
                $created++;
                $highlightAptIds[] = $apartment->id;

                // --- Gửi thông báo cho cư dân ---
                try {
                    $freshInvoice = $invoice->fresh();
                    foreach ($apartment->residents as $resident) {
                        if ($resident->user) {
                            $resident->user->notify(new NewInvoiceNotification($freshInvoice));
                        }
                    }
                } catch (\Throwable $e) {
                    // Không để lỗi notification ảnh hưởng đến tiến trình
                }
            } elseif ($invoice && ($invoiceAmountAdded > 0 || $pendingUtilityAdded)) {
                $highlightAptIds[] = $apartment->id;
            } else {
                $errorAptIds[] = $apartment->id;
            }
        } // end foreach apartments

        // --- Ghi Activity Log ---
        SystemLogger::log(
            'finance',
            'Xuất hóa đơn hàng loạt tháng ' . $month . '/' . $year
                . " — Tạo mới: {$created} hóa đơn"
                . ($skipped ? ", bỏ qua: {$skipped} mục." : '.'),
            [
                'billing_month' => $month,
                'billing_year'  => $year,
                'types'         => $request->types,
                'created'       => $created,
                'skipped'       => $skipped,
            ]
        );

        $msg = "Đã tạo {$created} hóa đơn";
        if ($skipped) {
            $msg .= ", bỏ qua {$skipped} mục (đã tồn tại hoặc thiếu đơn giá).";
        } else {
            $msg .= '.';
        }

        return redirect()->route('admin.invoices.index')
            ->with('success', $msg)
            ->with('highlightAptIds', array_unique($highlightAptIds))
            ->with('errorAptIds', array_unique($errorAptIds));
    }

    /**
     * Đánh dấu đã thanh toán (hỗ trợ thanh toán một phần, ghi chú, lưu người ghi nhận).
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $maxAmount = $invoice->remaining_amount > 0 ? $invoice->remaining_amount : $invoice->total_amount;

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer,momo,vnpay,other',
            'amount'         => 'required|numeric|min:1|max:' . $maxAmount,
            'note'           => 'nullable|string|max:500',
            'proof_image'    => 'nullable|image|max:4096', // Max 4MB
            'payer_name'     => 'nullable|string|max:255',
            'transaction_code' => 'nullable|string|max:100',
        ], [
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in'       => 'Phương thức thanh toán không hợp lệ.',
            'amount.required'         => 'Vui lòng nhập số tiền.',
            'amount.numeric'          => 'Số tiền phải là số.',
            'amount.min'              => 'Số tiền phải lớn hơn 0.',
            'amount.max'              => 'Số tiền không được lớn hơn ' . number_format($maxAmount) . 'đ.',
            'note.max'                => 'Ghi chú không được vượt quá 500 ký tự.',
            'proof_image.image'       => 'Minh chứng phải là hình ảnh.',
            'proof_image.max'         => 'Dung lượng ảnh tối đa 4MB.',
            'payer_name.max'          => 'Tên người nộp không được vượt quá 255 ký tự.',
            'transaction_code.max'    => 'Mã giao dịch không được vượt quá 100 ký tự.',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('proofs', 'public');
        }

        DB::transaction(function () use ($invoice, $validated, $proofPath) {
            // Tạo bản ghi payment
            Payment::create([
                'bill_id'        => $invoice->id,
                'amount'         => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'note'           => $validated['note'] ?? null,
                'proof_image'    => $proofPath,
                'payer_name'     => $validated['payer_name'] ?? null,
                'transaction_code' => $validated['transaction_code'] ?? null,
                'recorded_by'    => auth()->id(),
                'status'         => 'success',
                'paid_at'        => now(),
            ]);

            // Cộng vào paid_amount của bill
            $newPaidAmount = (float) $invoice->paid_amount + (float) $validated['amount'];

            // Xác định status mới
            $newStatus = $newPaidAmount >= (float) $invoice->total_amount ? 'paid' : 'partial_paid';

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'status'      => $newStatus,
            ]);

            $invoice->recalculateDetailsStatus();
        });

        // Sync trạng thái thanh toán cho lịch đặt tiện ích liên kết (nếu có)
        $freshInvoice = $invoice->fresh();
        if ($freshInvoice->status === 'paid') {
            $facilityBooking = $freshInvoice->facilityBooking;
            if ($facilityBooking && $facilityBooking->payment_status !== 'paid') {
                $facilityBooking->update([
                    'payment_status' => 'paid',
                    'payment_method' => $validated['payment_method'],
                ]);
            }
        }

        $message = (float)($invoice->fresh()->paid_amount) >= (float)$invoice->total_amount
            ? 'Hóa đơn đã được thanh toán đầy đủ.'
            : 'Ghi nhận thanh toán ' . number_format($validated['amount']) . 'đ thành công. Còn lại: ' . number_format($invoice->fresh()->remaining_amount) . 'đ.';



        return back()->with('success', $message);
    }

    /**
     * Hủy (refund) một lần thanh toán.
     */
    public function refundPayment(Request $request, Payment $payment)
    {
        if ($payment->is_refunded) {
            return back()->with('error', 'Thanh toán này đã được hủy trước đó.');
        }

        $validated = $request->validate([
            'refund_note' => 'nullable|string|max:500',
        ], [
            'refund_note.max' => 'Ghi chú hủy không được vượt quá 500 ký tự.',
        ]);

        DB::transaction(function () use ($payment, $validated) {
            $invoice = $payment->invoice;

            // Cập nhật payment thành refunded
            $payment->update([
                'status'      => 'refunded',
                'refunded_at' => now(),
                'refund_note' => $validated['refund_note'] ?? null,
                'refunded_by' => auth()->id(),
            ]);

            // Trừ paid_amount của bill
            $newPaidAmount = max(0, (float) $invoice->paid_amount - (float) $payment->amount);

            // Xác định lại status
            if ($newPaidAmount <= 0) {
                $newStatus = 'unpaid';
            } elseif ($newPaidAmount < (float) $invoice->total_amount) {
                $newStatus = 'partial_paid';
            } else {
                $newStatus = 'paid';
            }

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'status'      => $newStatus,
            ]);

            $invoice->recalculateDetailsStatus();
        });

        // Sync trạng thái booking tiện ích nếu hóa đơn bị hoàn tiền
        $freshInvoice = $payment->invoice->fresh();
        if ($freshInvoice->status !== 'paid') {
            $facilityBooking = $freshInvoice->facilityBooking;
            if ($facilityBooking && $facilityBooking->payment_status === 'paid') {
                $facilityBooking->update(['payment_status' => 'unpaid']);
            }
        }

        SystemLogger::log('system', 'Hủy thanh toán #' . $payment->id . ' trị giá ' . number_format($payment->amount) . 'đ của hóa đơn #' . $payment->invoice->id, $payment);

        return back()->with('success', 'Hủy thanh toán thành công. Trạng thái hóa đơn đã được cập nhật.');
    }

    /**
     * Ghi nhận thanh toán cho một chi tiết dịch vụ đơn lẻ.
     */


    /**
     * In biên lai thu tiền cho một giao dịch cụ thể.
     */
    public function printReceipt(Payment $payment)
    {
        $payment->load(['invoice.apartment.floor.block', 'recorder']);
        return view('admin.invoices.receipt', compact('payment'));
    }

    /**
     * In chi tiết toàn bộ hóa đơn (PDF).
     */
    public function printInvoice(Invoice $invoice)
    {
        $invoice->load(['apartment.floor.block', 'details.servicePrice', 'payments']);
        return view('admin.invoices.print', compact('invoice'));
    }

    /**
     * Hủy hóa đơn (status → cancelled).
     * Chỉ cho phép hủy khi chưa thanh toán đủ (tránh hủy hóa đơn đã paid).
     */
    public function cancelInvoice(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Không thể hủy hóa đơn đã được thanh toán đầy đủ.');
        }

        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'Hóa đơn này đã bị hủy trước đó.');
        }

        $validated = $request->validate([
            'cancel_note' => 'nullable|string|max:500',
        ], [
            'cancel_note.max' => 'Lý do hủy không được vượt quá 500 ký tự.',
        ]);

        $invoice->update(['status' => 'cancelled']);

        SystemLogger::log(
            'finance',
            'Hủy hóa đơn #' . $invoice->id . ' (' . $invoice->title . ')'
                . ($validated['cancel_note'] ? ' — Lý do: ' . $validated['cancel_note'] : ''),
            ['invoice_id' => $invoice->id, 'note' => $validated['cancel_note'] ?? null]
        );

        return back()->with('success', 'Đã hủy hóa đơn thành công.');
    }

    /**
     * Gửi lại thông báo nhắc thanh toán cho cư dân.
     */
    public function resendNotification(Invoice $invoice)
    {
        if ($invoice->status === 'paid' || $invoice->status === 'cancelled') {
            return back()->with('error', 'Không cần gửi thông báo cho hóa đơn đã thanh toán hoặc đã hủy.');
        }

        $invoice->load(['apartment.residents.user']);

        $sent = 0;
        foreach ($invoice->apartment->residents as $resident) {
            $user = $resident->user;
            if (!$user) continue;
            try {
                $user->notify(new NewInvoiceNotification($invoice));
                $sent++;
            } catch (\Throwable $e) {
                // Bỏ qua lỗi notification, không làm gián đoạn
            }
        }

        SystemLogger::log(
            'finance',
            'Gửi lại thông báo hóa đơn #' . $invoice->id . " cho {$sent} cư dân.",
            ['invoice_id' => $invoice->id, 'sent_count' => $sent]
        );

        if ($sent === 0) {
            return back()->with('error', 'Không tìm thấy cư dân nào trong căn hộ này để gửi thông báo.');
        }

        return back()->with('success', "Đã gửi lại thông báo cho {$sent} cư dân.");
    }
}
