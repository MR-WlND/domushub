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
        $thisMonthRevenue = \App\Models\Payment::where('status', 'success')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');
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
        $search = trim($request->search);
        $parsedSearchApt = null;

        // Trường hợp 1: Nhập đúng định dạng HD-202607-A502 hoặc tương tự
        if (preg_match('/^HD-(\d{4})(\d{2})-(.+)$/i', $search, $matches)) {
            $searchYear = (int) $matches[1];
            $searchMonth = (int) $matches[2];
            $parsedSearchApt = trim($matches[3]);
            
            // Override or set request month parameter to YYYY-MM
            $request->merge([
                'month' => sprintf('%04d-%02d', $searchYear, $searchMonth)
            ]);
        }
        // Trường hợp 2: Định dạng BILL-XXXXX
        elseif (preg_match('/^BILL-(\d+)$/i', $search, $matches)) {
            $parsedInvoiceId = (int) $matches[1];
            $inv = Invoice::find($parsedInvoiceId);
            if ($inv) {
                $parsedSearchApt = optional($inv->apartment)->apartment_number;
                $request->merge([
                    'month' => sprintf('%04d-%02d', $inv->billing_year, $inv->getRawOriginal('billing_month'))
                ]);
            }
        }

        $query = Apartment::with(['floor.block']);
        
        $invoiceFilter = function($q) use ($request) {
            if ($request->filled('month')) {
                [$year, $month] = explode('-', $request->month);
                $q->where('billing_month', (int) $month)
                  ->where('billing_year', (int) $year);
            }
            if ($request->filled('status')) {
                $q->where('status', $request->status);
            } else {
                $q->where('status', '!=', 'cancelled');
            }
        };

        $query->withCount(['invoices' => $invoiceFilter])
            ->withSum(['invoices' => $invoiceFilter], 'total_amount')
            ->withSum(['invoices' => $invoiceFilter], 'paid_amount');

        // Lọc theo tháng/năm (format: YYYY-MM)
        if ($request->filled('month')) {
            $query->whereHas('invoices', $invoiceFilter);
        }

        // Lọc theo căn hộ
        if ($request->filled('apartment_id')) {
            $query->where('id', $request->apartment_id);
        }

        // Lọc căn hộ nợ tiền quá 2 tháng (quá hạn 60 ngày)
        if ($request->boolean('debt_over_2_months')) {
            $query->whereHas('invoices', function($q) {
                $q->whereIn('status', ['unpaid', 'partial_paid', 'overdue'])
                  ->where('due_date', '<=', \Carbon\Carbon::now()->subDays(60));
            });
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->whereHas('invoices', $invoiceFilter);
        }

        // Tìm kiếm theo tên/mã căn hộ hoặc tòa
        if ($request->filled('search')) {
            if ($parsedSearchApt) {
                $query->where('apartment_number', 'like', '%' . $parsedSearchApt . '%');
            } else {
                $searchVal = $request->search;
                $query->where(function($sub) use ($searchVal) {
                    $sub->where('apartment_number', 'like', '%' . $searchVal . '%')
                        ->orWhereHas('floor.block', function ($b) use ($searchVal) {
                            $b->where('name', 'like', '%' . $searchVal . '%')
                                ->orWhere('code', 'like', '%' . $searchVal . '%');
                        });
                });
            }
        }

        // Lọc theo Tòa (Block)
        if ($request->filled('block_id')) {
            $query->whereHas('floor', function($q) use ($request) {
                $q->where('block_id', $request->block_id);
            });
        }

        // Lọc theo Tầng (Floor)
        if ($request->filled('floor_id')) {
            $query->where('floor_id', $request->floor_id);
        }

        $apartmentsPaginated = $query->paginate(20)->withQueryString();
        $apartments = Apartment::with('floor.block')->orderBy('apartment_number')->get();
        $blocks     = \App\Models\Block::orderBy('name')->get();
        $floors     = \App\Models\Floor::orderBy('name')->get();
        $statuses   = ['unpaid', 'partial_paid', 'paid', 'overdue', 'cancelled'];

        return view('admin.invoices.index', compact('apartmentsPaginated', 'apartments', 'blocks', 'floors', 'statuses'));
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

        // Tổng công nợ của căn hộ (bỏ qua hóa đơn đã hủy)
        $totalAmount  = Invoice::where('apartment_id', $apartment->id)->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalPaid    = Invoice::where('apartment_id', $apartment->id)->where('status', '!=', 'cancelled')->sum('paid_amount');
        $totalDebt    = max(0, $totalAmount - $totalPaid);

        return view('admin.invoices.apartment', compact('apartment', 'invoices', 'totalAmount', 'totalPaid', 'totalDebt'));
    }

    /**
     * Form tạo hóa đơn mới.
     */
    public function create()
    {
        $apartments = Apartment::with(['floor.block', 'residents.user', 'vehicles', 'apartmentType', 'utilityMeters'])->get();
        $servicePrices = \App\Models\ServicePrice::where('status', 'active')->get();
        return view('admin.invoices.create', compact('apartments', 'servicePrices'));
    }

    /**
     * Lưu hóa đơn mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id'   => 'required|exists:apartments,id',
            'title'          => 'required|string|max:150',
            'billing_month'  => 'required|string',
            'due_date'       => 'required|date',
            'custom_fees'    => 'required|array|min:1',
            'custom_fees.*.name' => 'required|string|max:255',
            'custom_fees.*.type' => 'required|in:other,service,parking_fee,compensation,penalty,card_reissue',
            'custom_fees.*.amount' => 'required|numeric|min:0',
            'custom_fees.*.note' => 'nullable|string|max:255',
        ], [
            'apartment_id.required'   => 'Vui lòng chọn căn hộ.',
            'title.required'          => 'Vui lòng nhập tiêu đề hóa đơn.',
            'billing_month.required'  => 'Vui lòng chọn kỳ hóa đơn.',
            'due_date.required'       => 'Vui lòng chọn hạn thanh toán.',
            'custom_fees.required'    => 'Vui lòng thêm ít nhất một khoản phí.',
            'custom_fees.min'         => 'Vui lòng thêm ít nhất một khoản phí.',
            'custom_fees.*.name.required' => 'Vui lòng nhập tên/mô tả khoản phí.',
            'custom_fees.*.amount.required' => 'Vui lòng nhập số tiền cho khoản phí.',
            'custom_fees.*.note.max' => 'Ghi chú không được dài quá 255 ký tự.',
        ]);

        // Parse month and year from YYYY-MM
        $monthYear = explode('-', $validated['billing_month']);
        $billingMonth = (int) $monthYear[1];
        $billingYear = (int) $monthYear[0];

        $previousDebt = \App\Models\Invoice::where('apartment_id', $validated['apartment_id'])
            ->where('status', '!=', 'cancelled')
            ->sum(\Illuminate\Support\Facades\DB::raw('total_amount - paid_amount'));

        $invoice = Invoice::create([
            'apartment_id'  => $validated['apartment_id'],
            'title'         => $validated['title'],
            'billing_month' => $billingMonth,
            'billing_year'  => $billingYear,
            'due_date'      => $validated['due_date'],
            'total_amount'  => 0, // Sẽ cập nhật sau
            'previous_debt' => $previousDebt,
            'current_amount' => 0,
            'total_due_at_issue' => $previousDebt,
            'status'        => 'unpaid',
        ]);

        $totalAddedAmount = 0;

        foreach ($validated['custom_fees'] as $fee) {
            // Find or create a ServicePrice placeholder for this type
            $servicePrice = \App\Models\ServicePrice::firstOrCreate(
                ['type' => $fee['type'], 'vehicle_type' => null],
                [
                    'name' => Invoice::typeLabel($fee['type']), 
                    'unit_price' => 0, 
                    'status' => 'active',
                    'description' => 'Phí ' . Invoice::typeLabel($fee['type'])
                ]
            );

            $fullNote = $fee['name'];
            if (!empty($fee['note'])) {
                $fullNote .= ' (' . $fee['note'] . ')';
            }

            \App\Models\InvoiceDetail::create([
                'bill_id'          => $invoice->id,
                'service_price_id' => $servicePrice->id,
                'quantity'         => 1,
                'amount'           => $fee['amount'],
                'note'             => $fullNote,
            ]);

            $totalAddedAmount += $fee['amount'];
        }

        if ($totalAddedAmount > 0) {
            $invoice->update([
                'total_amount' => $totalAddedAmount,
                'current_amount' => $totalAddedAmount,
                'total_due_at_issue' => $previousDebt + $totalAddedAmount
            ]);
        }

        return redirect()->route('admin.invoices.show', $invoice->id)
            ->with('success', 'Hóa đơn lẻ đã được tạo thành công.');
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
     * Hiển thị giao diện chỉnh sửa hóa đơn (chỉ cho phép nếu hóa đơn chưa paid/cancelled).
     */
    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return redirect()->route('admin.invoices.show', $invoice)
                ->with('error', 'Không thể chỉnh sửa hóa đơn đã được thanh toán đầy đủ.');
        }

        if ($invoice->status === 'cancelled') {
            return redirect()->route('admin.invoices.show', $invoice)
                ->with('error', 'Không thể chỉnh sửa hóa đơn đã bị hủy.');
        }

        $invoice->load(['apartment.floor.block', 'details.servicePrice']);
        return view('admin.invoices.edit', compact('invoice'));
    }

    /**
     * Cập nhật thông tin hóa đơn.
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Ràng buộc bảo mật tuyệt đối: chặn chỉnh sửa hóa đơn đã thanh toán/đã hủy
        if ($invoice->status === 'paid') {
            return redirect()->route('admin.invoices.show', $invoice)
                ->with('error', 'Chặn chỉnh sửa: Hóa đơn đã được thanh toán đầy đủ.');
        }

        if ($invoice->status === 'cancelled') {
            return redirect()->route('admin.invoices.show', $invoice)
                ->with('error', 'Chặn chỉnh sửa: Hóa đơn này đã bị hủy.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'required|date',
            'details' => 'required|array',
            'details.*.id' => 'required|exists:bill_details,id',
            'details.*.amount' => 'required|numeric|min:0',
            'details.*.quantity' => 'required|numeric|min:0',
            'details.*.note' => 'nullable|string|max:500',
        ]);

        DB::transaction(function() use ($invoice, $validated) {
            $totalAmount = 0;

            foreach ($validated['details'] as $detailData) {
                $detail = $invoice->details()->findOrFail($detailData['id']);
                $detail->update([
                    'amount' => $detailData['amount'],
                    'quantity' => $detailData['quantity'],
                    'note' => $detailData['note'] ?? null,
                ]);

                $totalAmount += $detailData['amount'];
            }

            // Tính toán lại dư nợ và tổng tiền
            $invoice->update([
                'title' => $validated['title'],
                'due_date' => $validated['due_date'],
                'total_amount' => $totalAmount,
                'current_amount' => $totalAmount,
                'total_due_at_issue' => (float)$invoice->previous_debt + $totalAmount,
            ]);

            // Cập nhật lại trạng thái thanh toán dựa trên paid_amount mới và total_amount mới
            $invoice->recalculateDetailsStatus();
            
            $paid = (float)$invoice->paid_amount;
            if ($paid >= $invoice->total_due_at_issue) {
                $newStatus = 'paid';
            } elseif ($paid > 0) {
                $newStatus = 'partial_paid';
            } else {
                $newStatus = $invoice->due_date->isPast() ? 'overdue' : 'unpaid';
            }
            
            $invoice->update([
                'status' => $newStatus
            ]);
        });

        // Ghi Activity Log
        SystemLogger::log(
            'finance',
            'Cập nhật thông tin hóa đơn #' . $invoice->id . ' (' . $invoice->invoice_code . ')',
            ['invoice_id' => $invoice->id]
        );

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Cập nhật hóa đơn thành công.');
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

        $activePrices = ServicePrice::where('status', 'active')
            ->whereNotIn('type', ['compensation', 'penalty', 'card_reissue'])
            ->get();

        $apartments = Apartment::with(['floor.block', 'invoices' => function ($query) use ($selectedYear, $selectedMonthNumber) {
            $query->where('billing_month', $selectedMonthNumber)
                ->where('billing_year', $selectedYear)
                ->where('status', '!=', 'cancelled');
        }])
            ->where('status', 'occupied')
            ->get();

        // Sắp xếp: đã xuất lên đầu
        $apartments = $apartments->sort(function ($a, $b) {
            $aHas = $a->invoices->isNotEmpty() ? 1 : 0;
            $bHas = $b->invoices->isNotEmpty() ? 1 : 0;
            if ($aHas !== $bHas) {
                return $bHas <=> $aHas;
            }
            return $a->id <=> $b->id;
        })->values();

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
            'types.*'       => 'in:electricity,water,management_fee,parking_fee_motorbike,parking_fee_car,parking_fee_electric_bike,parking_fee_bicycle,internet,service,other',
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
        $apartments = Apartment::with(['residents.user', 'vehicles', 'apartmentType'])
            ->whereIn('id', $selectedApartmentIds)
            ->where('status', 'occupied')
            ->get();

        // --- Lấy bảng đơn giá theo type ---
        $queryTypes = array_map(function($t) {
            return str_starts_with($t, 'parking_fee_') ? 'parking_fee' : $t;
        }, $request->types);

        $activePrices = ServicePrice::where('status', 'active')
            ->whereIn('type', $queryTypes)
            ->get()
            ->groupBy('type');

        $created = 0;
        $skipped = 0;
        $highlightAptIds = [];
        $errorAptIds = [];

        foreach ($apartments as $apartment) {
            // --- Tìm hoặc chuẩn bị tạo hóa đơn ---
            $invoice = Invoice::where('apartment_id', $apartment->id)
                ->where('billing_month', $month)
                ->where('billing_year', $year)
                ->where('status', '!=', 'cancelled')
                ->first();

            $apartmentPreviousDebt = \App\Models\Invoice::where('apartment_id', $apartment->id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($month, $year) {
                    $q->where('billing_year', '<', $year)
                      ->orWhere(function ($q2) use ($month, $year) {
                          $q2->where('billing_year', $year)->where('billing_month', '<', $month);
                      });
                })->sum(\Illuminate\Support\Facades\DB::raw('total_amount - paid_amount'));

            $invoiceExistedBefore = (bool) $invoice;
            $invoiceAmountAdded   = 0;
            $pendingUtilityAdded  = false; // Có dòng chờ chốt số hay không

            foreach ($request->types as $type) {

                // ============================================================
                // XỬ LÝ ĐIỆN / NƯỚC
                // ============================================================
                if (in_array($type, ['water', 'electricity'])) {
                    $servicePrice = $activePrices->get($type)?->first();
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
                            'previous_debt' => $apartmentPreviousDebt,
                            'current_amount' => 0,
                            'total_due_at_issue' => $apartmentPreviousDebt,
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
                // XỬ LÝ GỬI XE (PARKING FEE CỤ THỂ)
                // ============================================================
                if (str_starts_with($type, 'parking_fee_')) {
                    $vType = str_replace('parking_fee_', '', $type);
                    
                    $servicePrices = $activePrices->get('parking_fee');
                    if (!$servicePrices) {
                        $skipped++;
                        continue;
                    }
                    
                    $servicePrice = $servicePrices->where('vehicle_type', $vType)->first();
                    if (!$servicePrice) {
                        $skipped++;
                        continue;
                    }

                    $vehicleCount = Vehicle::where('apartment_id', $apartment->id)
                        ->where('vehicle_type', $vType)
                        ->where('status', 'active')
                        ->count();

                    // Không tạo dòng xe nếu căn hộ không có xe nào
                    if ($vehicleCount === 0) {
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

                        // Tạo hóa đơn nếu chưa có
                        if (!$invoice) {
                            $invoice = Invoice::create([
                                'apartment_id'  => $apartment->id,
                                'title'         => 'Hóa đơn tháng ' . $month . '/' . $year,
                                'billing_month' => $month,
                                'billing_year'  => $year,
                                'due_date'      => $request->due_date,
                                'total_amount'  => 0,
                                'previous_debt' => $apartmentPreviousDebt,
                                'current_amount' => 0,
                                'total_due_at_issue' => $apartmentPreviousDebt,
                                'status'        => 'unpaid',
                            ]);
                        }

                        $detailAmount = $vehicleCount * $servicePrice->unit_price;
                        $vLabels = [
                            'motorbike' => 'xe máy',
                            'electric_bike' => 'xe điện',
                            'car' => 'ô tô',
                            'bicycle' => 'xe đạp'
                        ];
                        $vName = $vLabels[$vType] ?? 'xe';
                        $vehicleNote = "Phí gửi {$vName}: " . number_format($servicePrice->unit_price, 0, ',', '.') . "đ/xe x {$vehicleCount} xe";

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
                    $servicePrice = $activePrices->get($type)?->first();
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
                            'previous_debt' => $apartmentPreviousDebt,
                            'current_amount' => 0,
                            'total_due_at_issue' => $apartmentPreviousDebt,
                            'status'        => 'unpaid',
                        ]);
                    }

                    $area = (float) ($apartment->area ?? 0);
                    $typeName = $apartment->apartmentType ? $apartment->apartmentType->name : 'Căn hộ';
                    $unitPrice = ($apartment->apartmentType && $apartment->apartmentType->base_service_fee > 0)
                        ? (float) $apartment->apartmentType->base_service_fee
                        : (float) $servicePrice->unit_price;
                    $detailAmount = $area * $unitPrice;

                    $note = "Phí quản lý [{$typeName}]: " . number_format($unitPrice, 0, ',', '.') . "đ/m² × {$area} m²";

                    InvoiceDetail::create([
                        'bill_id'          => $invoice->id,
                        'service_price_id' => $servicePrice->id,
                        'quantity'         => $area,
                        'amount'           => $detailAmount,
                        'note'             => $note,
                    ]);

                    $invoiceAmountAdded += $detailAmount;
                    continue;
                }

                // ============================================================
                // INTERNET, DỊCH VỤ KHÁC — ĐƠN GIÁ CỐ ĐỊNH × 1
                // ============================================================
                $servicePrice = $activePrices->get($type)?->first();
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
                        'previous_debt' => $apartmentPreviousDebt,
                        'current_amount' => 0,
                        'total_due_at_issue' => $apartmentPreviousDebt,
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
                $invoice->increment('current_amount', $invoiceAmountAdded);
                $invoice->increment('total_due_at_issue', $invoiceAmountAdded);
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

        if ($invoice->status === 'cancelled') {
            return back()->with('error', 'Hóa đơn này đã bị hủy, không thể ghi nhận thanh toán.');
        }

        $totalDueAtIssue = (float) ($invoice->total_due_at_issue > 0 ? $invoice->total_due_at_issue : $invoice->total_amount);
        $remainingDue = max(0, $totalDueAtIssue - (float) $invoice->paid_amount);

        if ($remainingDue <= 0) {
            return back()->with('error', 'Hóa đơn này đã được thanh toán đầy đủ.');
        }

        $maxAmount = $remainingDue;


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

        DB::transaction(function () use ($invoice, $validated, $proofPath, $totalDueAtIssue) {
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


            // Xác định status mới dựa trên total_due_at_issue (tính cả nợ cũ)
            $newStatus = $newPaidAmount >= $totalDueAtIssue ? 'paid' : 'partial_paid';


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

            // Kích hoạt xe nếu đây là hóa đơn phí gửi xe
            $this->activateVehicleIfParkingFee($freshInvoice);
        }

        $freshPaid = (float) $freshInvoice->paid_amount;
        $message = $freshPaid >= $totalDueAtIssue
            ? 'Hóa đơn đã được thanh toán đầy đủ.'
            : 'Ghi nhận thanh toán ' . number_format($validated['amount']) . 'đ thành công. Còn lại: ' . number_format(max(0, $totalDueAtIssue - $freshPaid)) . 'đ.';

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

            // Xác định lại status dựa trên tổng nợ (bao gồm previous_debt)
            $totalDue = (float) $invoice->total_due_at_issue;
            if ($newPaidAmount <= 0) {
                $newStatus = 'unpaid';
            } elseif ($newPaidAmount < $totalDue) {
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
                . (!empty($validated['cancel_note']) ? ' — Lý do: ' . $validated['cancel_note'] : ''),
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

    /**
     * Gửi nhắc nợ hàng loạt cho toàn bộ các hóa đơn chưa thanh toán.
     */
    public function batchResendNotification(Request $request)
    {
        // Lấy tất cả hóa đơn chưa thanh toán hoặc quá hạn
        $invoices = Invoice::whereIn('status', ['unpaid', 'partial_paid', 'overdue'])
            ->with(['apartment.residents.user'])
            ->get();

        if ($invoices->isEmpty()) {
            return back()->with('error', 'Không có hóa đơn nợ nào cần nhắc nhở.');
        }

        $sentCount = 0;
        $apartmentCount = 0;

        foreach ($invoices as $invoice) {
            $invoiceSent = 0;
            foreach ($invoice->apartment->residents as $resident) {
                $user = $resident->user;
                if (!$user) continue;
                try {
                    $user->notify(new \App\Notifications\NewInvoiceNotification($invoice));
                    $sentCount++;
                    $invoiceSent++;
                } catch (\Throwable $e) {
                    // Bỏ qua lỗi gửi thông báo để tránh gián đoạn
                }
            }
            if ($invoiceSent > 0) {
                $apartmentCount++;
            }
        }

        // Ghi Activity Log
        SystemLogger::log(
            'finance',
            "Gửi nhắc nợ hàng loạt cho {$apartmentCount} căn hộ ({$sentCount} cư dân).",
            ['sent_count' => $sentCount, 'apartment_count' => $apartmentCount]
        );

        return back()->with('success', "Đã gửi thông báo nhắc nợ thành công đến {$apartmentCount} căn hộ ({$sentCount} cư dân).");
    }

    /**
     * Kích hoạt xe khi hóa đơn phí gửi xe được thanh toán.
     */
    private function activateVehicleIfParkingFee(Invoice $invoice): void
    {
        $parkingDetail = $invoice->details()->whereHas('servicePrice', function ($q) {
            $q->where('type', 'parking_fee');
        })->first();

        if (!$parkingDetail) return;



        $vehicles = \App\Models\Vehicle::where('apartment_id', $invoice->apartment_id)
            ->where('status', 'awaiting_payment')
            ->get();

        if ($vehicles->isEmpty()) return;

        foreach ($vehicles as $vehicle) {
            $vehicle->update(['status' => 'active']);

            // Sinh QR
            try {
                $dir = storage_path('app/public/qr/vehicles');
                if (!is_dir($dir)) mkdir($dir, 0775, true);
                $content = strtoupper(str_replace([' ', '-'], '', $vehicle->license_plate));
                $filename = $content . '.svg';
                $filePath = $dir . '/' . $filename;
                if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                    \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->errorCorrection('H')->generate($content, $filePath);
                }
                $vehicle->update(['qr_code' => 'qr/vehicles/' . $filename]);
            } catch (\Throwable $e) {
                $vehicle->update(['qr_code' => strtoupper(str_replace([' ', '-'], '', $vehicle->license_plate))]);
            }


            SystemLogger::log(
                'vehicle',
                'Kích hoạt xe ' . $vehicle->license_plate . ' sau khi thanh toán phí gửi xe',
                ['vehicle_id' => $vehicle->id, 'apartment_id' => $invoice->apartment_id]

            );
        }
    }
}
