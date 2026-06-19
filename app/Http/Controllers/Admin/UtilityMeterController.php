<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\UtilityMeter;
use App\Models\UtilityMeterLog;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UtilityMeterController extends Controller
{
    /**
     * Danh sách chỉ số điện nước + thống kê tổng quan
     */
    public function index(Request $request): View|RedirectResponse
    {
        if (!file_exists(public_path('storage'))) {
            try {
                \Illuminate\Support\Facades\Artisan::call('storage:link');
            } catch (\Exception $e) {
                // Ignore if symlink fails or permission is denied on local Windows development
            }
        }

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

        $highlightId = $request->query('highlight');
        if ($highlightId && !$request->has('page')) {
            $target = UtilityMeter::find($highlightId);
            if ($target) {
                $month = $target->record_month;
                $year = $target->record_year;

                $countQuery = UtilityMeter::where('record_month', $month)
                    ->where('record_year', $year);

                if ($type) {
                    $countQuery->where('type', $type);
                }

                if ($floorId) {
                    $countQuery->whereHas('apartment', fn ($q) => $q->where('floor_id', $floorId));
                } elseif ($blockId) {
                    $countQuery->whereHas('apartment.floor', fn ($q) => $q->where('block_id', $blockId));
                }

                $allIds = $countQuery->orderBy('type')
                    ->orderBy('apartment_id')
                    ->pluck('id')
                    ->toArray();

                $index = array_search($highlightId, $allIds);
                if ($index !== false) {
                    $page = (int) floor($index / 20) + 1;
                    if ($page > 1) {
                        return redirect()->route('admin.utility-readings.index', array_merge($request->query(), [
                            'month' => $month,
                            'year' => $year,
                            'page' => $page,
                            'highlight' => $highlightId
                        ]));
                    }
                }
            }
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
        $floors     = Floor::orderBy('floor_number')->get();
        $apartments = Apartment::with('floor.block')
            ->where('status', '!=', 'maintenance')
            ->orderBy('apartment_number')
            ->get();

        return view('admin.utility-readings.create', compact('blocks', 'floors', 'apartments'));
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
            'images'        => 'nullable|array|max:5',
            'images.*'      => 'image|max:4096',
            'is_reset'      => 'nullable|boolean',
        ], [
            'apartment_id.required' => 'Vui lòng chọn căn hộ.',
            'type.required'         => 'Vui lòng chọn loại (điện/nước).',
            'new_value.required'    => 'Vui lòng nhập chỉ số mới.',
            'new_value.min'         => 'Chỉ số mới phải >= 0.',
            'images.max'            => 'Tối đa 5 ảnh minh chứng.',
            'images.*.image'        => 'Tệp minh chứng phải là hình ảnh.',
            'images.*.max'          => 'Dung lượng mỗi ảnh tối đa là 4MB.',
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

        // Tự động lấy chỉ số cũ (nếu thay công tơ mới thì đặt bằng 0)
        $isReset = $request->boolean('is_reset');
        $oldValue = $isReset ? 0 : (UtilityMeter::getPreviousNewValue(
            $validated['apartment_id'],
            $validated['type'],
            $validated['record_month'],
            $validated['record_year']
        ) ?? 0);

        $imageProofPath = null;
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('proofs', 'public');
                $imagePaths[] = $path;
            }
            // Backward compat: giữ image_proof là ảnh đầu tiên
            $imageProofPath = $imagePaths[0] ?? null;
        }

        $meter = UtilityMeter::create([
            'apartment_id'  => $validated['apartment_id'],
            'type'          => $validated['type'],
            'record_month'  => $validated['record_month'],
            'record_year'   => $validated['record_year'],
            'old_value'     => $oldValue,
            'new_value'     => $validated['new_value'],
            'recorded_by'   => Auth::id(),
            'status'        => 'pending',
            'image_proof'   => $imageProofPath,
            'images'        => !empty($imagePaths) ? $imagePaths : null,
            'is_reset'      => $isReset,
        ]);

        // Gửi thông báo cho nhân viên kế toán (staff) khi kỹ thuật viên ghi số
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && $user->role === 'technician') {
            $apartment = Apartment::find($validated['apartment_id']);
            $typeName = $validated['type'] === 'electricity' ? 'Điện' : 'Nước';
            $recorderName = $user->name;
            $apartmentNumber = $apartment->apartment_number ?? 'N/A';
            
            $accountants = \App\Models\User::whereIn('role', ['staff', 'manager', 'admin'])->get();
            $notificationData = [
                'title' => '⏳ Chỉ số mới chờ phê duyệt',
                'message' => "Kỹ thuật viên <strong>{$recorderName}</strong> đã gửi số <strong>{$typeName}</strong> mới cho căn hộ <strong>{$apartmentNumber}</strong> (Kỳ {$validated['record_month']}/{$validated['record_year']}): {$validated['new_value']}. Vui lòng kiểm tra và duyệt.",
                'url' => route('admin.utility-readings.index', [
                    'month' => $validated['record_month'],
                    'year' => $validated['record_year'],
                    'highlight' => $meter->id
                ]),
                'type' => 'single',
            ];
            
            foreach ($accountants as $acc) {
                $acc->notify(new \App\Notifications\UtilityIndexRecordedNotification($notificationData));
            }
        }

        return redirect()->route('admin.utility-readings.index', [
            'month' => $validated['record_month'],
            'year'  => $validated['record_year'],
        ])->with('success', 'Đã ghi nhận chỉ số mới và chuyển trạng thái Chờ chốt.');
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

        foreach ($request->readings as $i => $reading) {
            $aptId = $reading['apartment_id'];
            $elecSaved = false;
            $waterSaved = false;

            // Tải nhiều ảnh công tơ nếu có
            $imageProofPath = null;
            $imagePaths = [];
            if ($request->hasFile("readings.{$i}.images")) {
                foreach ($request->file("readings.{$i}.images") as $img) {
                    $p = $img->store('proofs', 'public');
                    $imagePaths[] = $p;
                }
                $imageProofPath = $imagePaths[0] ?? null;
            }

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
                    $elecIsReset = isset($reading['elec_is_reset']) && (bool)$reading['elec_is_reset'];
                    $elecOld = $elecIsReset ? 0 : (UtilityMeter::getPreviousNewValue($aptId, 'electricity', $month, $year) ?? 0);
                    UtilityMeter::create([
                        'apartment_id' => $aptId,
                        'type'         => 'electricity',
                        'record_month' => $month,
                        'record_year'  => $year,
                        'old_value'    => $elecOld,
                        'new_value'    => (int) $reading['elec_new'],
                        'recorded_by'  => Auth::id(),
                        'status'       => 'pending',
                        'image_proof'  => $imageProofPath,
                        'images'       => !empty($imagePaths) ? $imagePaths : null,
                        'is_reset'     => $elecIsReset,
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
                    $waterIsReset = isset($reading['water_is_reset']) && (bool)$reading['water_is_reset'];
                    $waterOld = $waterIsReset ? 0 : (UtilityMeter::getPreviousNewValue($aptId, 'water', $month, $year) ?? 0);
                    UtilityMeter::create([
                        'apartment_id' => $aptId,
                        'type'         => 'water',
                        'record_month' => $month,
                        'record_year'  => $year,
                        'old_value'    => $waterOld,
                        'new_value'    => (int) $reading['water_new'],
                        'recorded_by'  => Auth::id(),
                        'status'       => 'pending',
                        'image_proof'  => $imageProofPath,
                        'images'       => !empty($imagePaths) ? $imagePaths : null,
                        'is_reset'     => $waterIsReset,
                    ]);
                    $saved++;
                    $waterSaved = true;
                }
            }

            if ($elecSaved || $waterSaved) {
                $affectedApartments[$aptId] = true;
            }
        }

        // Gửi thông báo cho nhân viên kế toán (staff) khi kỹ thuật viên ghi số hàng loạt
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($saved > 0 && $user && $user->role === 'technician') {
            $recorderName = $user->name;
            $countApts = count($affectedApartments);
            
            $accountants = \App\Models\User::whereIn('role', ['staff', 'manager', 'admin'])->get();
            $notificationData = [
                'title' => '⏳ Chốt số hàng loạt chờ phê duyệt',
                'message' => "Kỹ thuật viên <strong>{$recorderName}</strong> đã chốt hàng loạt chỉ số mới cho <strong>{$countApts}</strong> căn hộ (Kỳ {$month}/{$year}) và đang chờ duyệt.",
                'url' => route('admin.utility-readings.index', [
                    'month' => $month,
                    'year' => $year
                ]),
                'type' => 'batch',
            ];
            
            foreach ($accountants as $acc) {
                $acc->notify(new \App\Notifications\UtilityIndexRecordedNotification($notificationData));
            }
        }

        $message = "Đã gửi thành công {$saved} chỉ số điện/nước và đang chờ kế toán phê duyệt.";
        if ($skipped > 0) {
            $message .= " Bỏ qua {$skipped} mục đã chốt trước đó.";
        }

        return redirect()->route('admin.utility-readings.index', [
            'month' => $month,
            'year'  => $year,
        ])->with('success', $message);
    }

    /**
     * Xem chi tiết chỉ số
     */
    public function show(int $id): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        $reading = UtilityMeter::with(['apartment.floor.block', 'recorder', 'rejecter'])->findOrFail($id);

        if (request()->ajax() || request()->wantsJson()) {
            // Tạo danh sách URL ảnh
            $imagesUrls = [];
            if (!empty($reading->images)) {
                foreach ($reading->images as $imgPath) {
                    $imagesUrls[] = asset('storage/' . $imgPath);
                }
            } elseif ($reading->image_proof) {
                // Backward compat với bản ghi cũ chỉ có 1 ảnh
                $imagesUrls[] = asset('storage/' . $reading->image_proof);
            }

            return response()->json([
                'success' => true,
                'reading' => [
                    'id' => $reading->id,
                    'apartment_number' => $reading->apartment->apartment_number ?? 'N/A',
                    'location' => ($reading->apartment->floor->block->name ?? '') . ' / ' . ($reading->apartment->floor->name ?? ''),
                    'type' => $reading->type,
                    'type_label' => $reading->type_label,
                    'record_month' => $reading->record_month,
                    'record_year' => $reading->record_year,
                    'old_value' => $reading->old_value,
                    'new_value' => $reading->new_value,
                    'usage_amount' => $reading->usage_amount,
                    'status' => $reading->status,
                    'is_reset' => $reading->is_reset,
                    'images_urls' => $imagesUrls,
                    'image_proof_url' => $reading->image_proof ? asset('storage/' . $reading->image_proof) : null,
                    'recorder_name' => $reading->recorder->name ?? 'Hệ thống',
                    'reject_reason' => $reading->reject_reason,
                    'rejecter_name' => $reading->rejecter ? $reading->rejecter->name : null,
                    'created_at' => $reading->created_at->format('d/m/Y H:i'),
                    'updated_at' => $reading->updated_at->format('d/m/Y H:i'),
                ]
            ]);
        }

        return view('admin.utility-readings.show', compact('reading'));
    }

    /**
     * Form chỉnh sửa
     */
    public function edit(int $id): View
    {
        $reading = UtilityMeter::with('apartment.floor.block')->findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role === 'technician') {
            if (!in_array($reading->status, ['pending', 'rejected'])) {
                abort(403, 'Bạn không có quyền chỉnh sửa chỉ số đã được duyệt.');
            }
            if ($reading->recorded_by !== Auth::id()) {
                abort(403, 'Bạn không có quyền chỉnh sửa chỉ số của kỹ thuật viên khác.');
            }
        }

        return view('admin.utility-readings.edit', compact('reading'));
    }

    /**
     * Cập nhật chỉ số
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $reading = UtilityMeter::findOrFail($id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->role === 'technician') {
            if (!in_array($reading->status, ['pending', 'rejected'])) {
                abort(403, 'Bạn không có quyền chỉnh sửa chỉ số đã được duyệt.');
            }
            if ($reading->recorded_by !== Auth::id()) {
                abort(403, 'Bạn không có quyền chỉnh sửa chỉ số của kỹ thuật viên khác.');
            }
        }

        $isAdmin = $user->role === 'admin';

        $rules = [
            'new_value' => 'required|integer|min:0',
        ];

        if ($isAdmin) {
            $rules['old_value'] = 'required|integer|min:0';
            $rules['new_value'] .= '|gte:old_value';
        } else {
            $rules['new_value'] .= '|gte:' . $reading->old_value;
        }

        $validated = $request->validate($rules, [
            'old_value.required' => 'Vui lòng nhập chỉ số cũ.',
            'new_value.required' => 'Vui lòng nhập chỉ số mới.',
            'new_value.gte'      => 'Chỉ số mới phải >= chỉ số cũ.',
        ]);

        $updateData = [
            'new_value' => $validated['new_value'],
        ];

        if ($isAdmin && $request->has('old_value')) {
            $updateData['old_value'] = $validated['old_value'];
        }

        // Xử lý ảnh upload thêm
        if ($request->hasFile('images')) {
            $request->validate([
                'images'   => 'array|max:5',
                'images.*' => 'image|max:4096',
            ]);
            $existingImages = $reading->images ?? [];
            if (empty($existingImages) && $reading->image_proof) {
                $existingImages = [$reading->image_proof];
            }
            foreach ($request->file('images') as $img) {
                $existingImages[] = $img->store('proofs', 'public');
            }
            $updateData['images'] = $existingImages;
            // Cập nhật image_proof (backward compat)
            if (empty($reading->image_proof) && !empty($existingImages)) {
                $updateData['image_proof'] = $existingImages[0];
            }
        }

        // Nếu kỹ thuật viên sửa đổi bản ghi bị từ chối/chờ duyệt, trả trạng thái về pending để kế toán duyệt lại
        if (auth()->user()->role === 'technician') {
            $updateData['status'] = 'pending';
        }

        $reading->update($updateData);

        // Chỉ đồng bộ hóa đơn nếu bản ghi đã được duyệt
        if ($reading->status === 'approved') {
            $this->syncInvoice($reading->apartment_id, (int) $reading->record_month, (int) $reading->record_year);
        }

        return redirect()->route('admin.utility-readings.index', [
            'month' => $reading->record_month,
            'year'  => $reading->record_year,
        ])->with('success', 'Đã cập nhật chỉ số thành công.');
    }

    /**
     * Xóa 1 ảnh khỏi danh sách ảnh của bản ghi
     */
    public function removeImage(Request $request, int $id): RedirectResponse
    {
        $reading = UtilityMeter::findOrFail($id);

        // Chỉ cho phép người ghi hoặc admin sửa ảnh
        if (auth()->user()->role === 'technician' && $reading->recorded_by !== auth()->id()) {
            abort(403, 'Bạn không có quyền xóa ảnh của bản ghi này.');
        }

        $index = (int) $request->input('index', -1);
        $images = $reading->images ?? [];
        if (empty($images) && $reading->image_proof) {
            $images = [$reading->image_proof];
        }

        if ($index >= 0 && isset($images[$index])) {
            // Xóa file khỏi storage
            \Illuminate\Support\Facades\Storage::disk('public')->delete($images[$index]);
            array_splice($images, $index, 1);

            $updateData = ['images' => !empty($images) ? array_values($images) : null];

            // Cập nhật image_proof nếu ảnh đầu tiên bị xóa
            if ($index === 0) {
                $updateData['image_proof'] = !empty($images) ? $images[0] : null;
            }

            $reading->update($updateData);
        }

        return back()->with('success', 'Đã xóa ảnh minh chứng.');
    }

    /**
     * Xóa chỉ số
     */
    public function destroy(int $id): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && $user->role === 'technician') {
            abort(403, 'Bạn không có quyền xóa chỉ số.');
        }

        $reading = UtilityMeter::findOrFail($id);
        $month   = $reading->record_month;
        $year    = $reading->record_year;
        $apartmentId = $reading->apartment_id;
        $status = $reading->status;

        $reading->delete();

        if ($status === 'approved') {
            $this->syncInvoice($apartmentId, (int) $month, (int) $year);
        }

        return redirect()->route('admin.utility-readings.index', [
            'month' => $month,
            'year'  => $year,
        ])->with('success', 'Đã xóa chỉ số thành công.');
    }

    /**
     * Phê duyệt chỉ số đơn lẻ
     */
    public function approve(int $id): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && in_array($user->role, ['technician'])) {
            abort(403, 'Bạn không có quyền phê duyệt chỉ số.');
        }

        $reading = UtilityMeter::findOrFail($id);
        $reading->update(['status' => 'approved']);

        // Đồng bộ hóa đơn ngay lập tức
        $this->syncInvoice($reading->apartment_id, (int) $reading->record_month, (int) $reading->record_year);

        return redirect()->route('admin.utility-readings.index', [
            'month' => $reading->record_month,
            'year'  => $reading->record_year,
        ])->with('success', 'Đã phê duyệt chỉ số và đồng bộ hóa đơn.');
    }

    /**
     * Từ chối chỉ số đơn lẻ
     */
    public function reject(Request $request, int $id): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && in_array($user->role, ['technician'])) {
            abort(403, 'Bạn không có quyền từ chối chỉ số.');
        }

        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ], [
            'reject_reason.required' => 'Vui lòng cung cấp lý do từ chối.',
        ]);

        $reading = UtilityMeter::with('apartment')->findOrFail($id);
        $reading->update([
            'status' => 'rejected',
            'rejected_by' => Auth::id(),
            'reject_reason' => $request->input('reject_reason'),
        ]);

        // Gửi thông báo cho kỹ thuật viên ghi số nếu có
        $recorder = $reading->recorder;
        if ($recorder) {
            $typeName = $reading->type === 'electricity' ? 'Điện' : 'Nước';
            $apartmentNumber = $reading->apartment->apartment_number ?? 'N/A';
            $rejecterName = $user->name ?? 'Hệ thống';
            $reason = $request->input('reject_reason');
            
            $notificationData = [
                'title' => '❌ Chỉ số điện nước bị từ chối',
                'message' => "Chỉ số <strong>{$typeName}</strong> mới cho căn hộ <strong>{$apartmentNumber}</strong> (Kỳ {$reading->record_month}/{$reading->record_year}) đã bị từ chối bởi kế toán <strong>{$rejecterName}</strong>. Lý do: <em>{$reason}</em>. Vui lòng kiểm tra và ghi lại.",
                'url' => route('admin.utility-readings.index', [
                    'month' => $reading->record_month,
                    'year' => $reading->record_year,
                    'highlight' => $reading->id
                ]),
                'type' => 'rejected',
            ];
            
            $recorder->notify(new \App\Notifications\UtilityIndexRecordedNotification($notificationData));
        }

        return redirect()->route('admin.utility-readings.index', [
            'month' => $reading->record_month,
            'year'  => $reading->record_year,
        ])->with('success', 'Đã từ chối chỉ số và gửi thông báo cho kỹ thuật viên.');
    }

    /**
     * Phê duyệt hàng loạt chỉ số
     */
    public function batchApprove(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user && in_array($user->role, ['technician'])) {
            abort(403, 'Bạn không có quyền phê duyệt chỉ số.');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|exists:utility_meters,id',
            'month' => 'required|integer',
            'year' => 'required|integer',
        ]);

        $ids = $request->ids;
        $readings = UtilityMeter::whereIn('id', $ids)->where('status', 'pending')->get();
        $affectedApartments = [];

        foreach ($readings as $reading) {
            $reading->update(['status' => 'approved']);
            $affectedApartments[$reading->apartment_id] = true;
        }

        // Đồng bộ hóa đơn cho toàn bộ căn hộ bị ảnh hưởng
        foreach (array_keys($affectedApartments) as $aptId) {
            $this->syncInvoice((int) $aptId, (int) $request->month, (int) $request->year);
        }

        return redirect()->route('admin.utility-readings.index', [
            'month' => $request->month,
            'year'  => $request->year,
        ])->with('success', 'Đã phê duyệt thành công ' . count($readings) . ' chỉ số và đồng bộ hóa đơn.');
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
        // 1. Tìm tất cả chỉ số điện và nước của căn hộ này trong tháng/năm có trạng thái APPROVED
        $elecReading = UtilityMeter::where('apartment_id', $apartmentId)
            ->where('type', 'electricity')
            ->where('record_month', $month)
            ->where('record_year', $year)
            ->where('status', 'approved')
            ->first();

        $waterReading = UtilityMeter::where('apartment_id', $apartmentId)
            ->where('type', 'water')
            ->where('record_month', $month)
            ->where('record_year', $year)
            ->where('status', 'approved')
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

        // 4. Tìm hóa đơn hiện có hoặc tạo mới
        $invoice = \App\Models\Invoice::where('apartment_id', $apartmentId)
            ->where('billing_month', $month)
            ->where('billing_year', $year)
            ->first();

        if ($invoice) {
            $oldDetails = $invoice->details()->whereIn('service_price_id', [$elecService->id, $waterService->id])->get();
            $oldAmount = $oldDetails->sum('amount');
            
            // Xóa các chi tiết cũ liên quan đến điện/nước
            $invoice->details()->whereIn('service_price_id', [$elecService->id, $waterService->id])->delete();

            $newTotalAmount = max(0, $invoice->total_amount - $oldAmount + $totalAmount);

            $invoice->update([
                'title' => "Hóa đơn tháng {$month}/{$year}",
                'total_amount' => $newTotalAmount,
                // Giữ nguyên trạng thái nếu đã thanh toán, tránh chuyển ngược về unpaid trái phép
                'status' => $invoice->status === 'paid' ? 'paid' : 'unpaid',
            ]);
        } else {
            $invoice = \App\Models\Invoice::create([
                'apartment_id' => $apartmentId,
                'billing_month' => $month,
                'billing_year' => $year,
                'title' => "Hóa đơn tháng {$month}/{$year}",
                'due_date' => now()->addDays(10), // Hạn nộp 10 ngày từ ngày chốt
                'total_amount' => $totalAmount,
                'status' => 'unpaid',
            ]);
        }

        // 5. Đồng bộ chi tiết hóa đơn (bill_details)
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
        DB::beginTransaction();

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
                            'status'      => 'pending',
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
                            'status'      => 'pending',
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
                DB::rollBack();
                return back()->with('error', 'Import thất bại do có dữ liệu lỗi. Vui lòng kiểm tra lại file mẫu.')->withErrors($errors);
            }

            DB::commit();

            return redirect()->route('admin.utility-readings.index', [
                'month' => $month,
                'year'  => $year,
            ])->with('success', "Nhập thành công {$successCount} chỉ số điện/nước từ file Excel/CSV ở trạng thái Chờ chốt.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Đã xảy ra lỗi hệ thống trong quá trình import: ' . $e->getMessage());
        }
    }

    /**
     * Danh sách lịch sử ghi nhận & phê duyệt số điện nước
     */
    public function logs(Request $request): \Illuminate\View\View
    {
        $blockId = $request->query('block_id');
        $floorId = $request->query('floor_id');
        $type    = $request->query('type');
        $action  = $request->query('action');
        $month   = $request->query('month');
        $year    = $request->query('year');
        $search  = $request->query('search');

        // Query logs
        $query = UtilityMeterLog::with(['apartment.floor.block', 'user', 'utilityMeter']);

        // Lọc theo block hoặc floor
        if ($floorId) {
            $query->whereHas('apartment', fn ($q) => $q->where('floor_id', $floorId));
        } elseif ($blockId) {
            $query->whereHas('apartment.floor', fn ($q) => $q->where('block_id', $blockId));
        }

        // Lọc theo loại
        if ($type) {
            $query->where('type', $type);
        }

        // Lọc theo hành động
        if ($action) {
            $query->where('action', $action);
        }

        // Lọc theo tháng / năm của kỳ ghi nhận
        if ($month) {
            $query->where('record_month', $month);
        }
        if ($year) {
            $query->where('record_year', $year);
        }

        // Tìm kiếm theo số phòng hoặc tên người thực hiện
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('apartment', function ($q2) use ($search) {
                    $q2->where('apartment_number', 'like', "%{$search}%");
                })->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Sắp xếp mặc định log mới nhất lên trước
        $logs = $query->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // Lấy dữ liệu filter
        $blocks = Block::orderBy('name')->get();
        $floors = Floor::with('block')->orderBy('floor_number')->get();

        return view('admin.utility-readings.logs', compact(
            'logs', 'blocks', 'floors', 'blockId', 'floorId', 'type', 'action', 'month', 'year', 'search'
        ));
    }
}
