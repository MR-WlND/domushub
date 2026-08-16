<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\SystemLogger;
use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ApartmentController extends Controller
{
    /**
     * Danh sách căn hộ
     */
    public function index(Request $request): View
    {
        $floorId = $request->query('floor_id');
        $blockId = $request->query('block_id');
        $status = $request->query('status');
        $search = $request->query('search');
        $apartmentTypeId = $request->query('apartment_type_id');

        $floor = null;
        $block = null;

        $query = Apartment::query()
            ->with([
                'floor.block',
                'residents.user',
                'apartmentType',
            ])
            ->withCount('residents');

        /**
         * Filter theo loại căn hộ
         */
        if ($apartmentTypeId) {
            $query->where('apartment_type_id', $apartmentTypeId);
        }

        /**
         * Filter theo block
         */
        if ($blockId) {

            $block = Block::findOrFail($blockId);

            $query->whereHas('floor', function ($q) use ($blockId) {

                $q->where('block_id', $blockId);
            });
        }

        /**
         * Filter theo tầng
         */
        if ($floorId) {

            $floor = Floor::with('block')
                ->findOrFail($floorId);

            $query->where('floor_id', $floorId);
        }

        /**
         * Filter trạng thái
         */
        if ($status) {

            $query->where('status', $status);
        }

        /**
         * Search số căn
         */
        if ($search) {

            $query->where(
                'apartment_number',
                'like',
                "%{$search}%"
            );
        }

        $apartments = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        /**
         * Danh sách tầng
         */
        $floors = Floor::with('block')
            ->orderBy('floor_number')
            ->get();

        /**
         * Danh sách block
         */
        $blocks = Block::orderBy('name')
            ->get();

        /**
         * Danh sách loại căn hộ
         */
        $apartmentTypes = \App\Models\ApartmentType::orderBy('bedroom_count')
            ->orderBy('bathroom_count')
            ->orderBy('base_service_fee')
            ->get();

        /**
         * Stats
         */
        $stats = [

            'total' => Apartment::count(),

            'occupied' => Apartment::where(
                'status',
                'occupied'
            )->count(),

            'vacant' => Apartment::where(
                'status',
                'vacant'
            )->count(),

            'maintenance' => Apartment::where(
                'status',
                'maintenance'
            )->count(),

        ];

        return view(
            'admin.apartments.index',
            compact(
                'apartments',
                'floors',
                'blocks',
                'apartmentTypes',
                'floor',
                'block',
                'stats',
                'status',
                'search',
                'floorId',
                'blockId',
                'apartmentTypeId'
            )
        );
    }

    /**
     * Sơ đồ Ma trận Căn hộ
     */
    public function matrix(Request $request): View
    {
        $blockId = $request->query('block_id');
        $blocks = Block::orderBy('name')->get();

        if (!$blockId && $blocks->isNotEmpty()) {
            $blockId = $blocks->first()->id;
        }

        $block = null;
        $floors = collect();

        if ($blockId) {
            $block = Block::findOrFail($blockId);
            $floors = Floor::with(['apartments' => function($query) {
                $query->orderBy('apartment_number');
            }])->where('block_id', $blockId)->orderBy('floor_number', 'desc')->get();
        }

        /**
         * Thống kê
         */
        $stats = [
            'total' => Apartment::when($blockId, function($q) use ($blockId) {
                $q->whereHas('floor', fn($f) => $f->where('block_id', $blockId));
            })->count(),

            'occupied' => Apartment::when($blockId, function($q) use ($blockId) {
                $q->whereHas('floor', fn($f) => $f->where('block_id', $blockId));
            })->where('status', 'occupied')->count(),

            'vacant' => Apartment::when($blockId, function($q) use ($blockId) {
                $q->whereHas('floor', fn($f) => $f->where('block_id', $blockId));
            })->where('status', 'vacant')->count(),

            'maintenance' => Apartment::when($blockId, function($q) use ($blockId) {
                $q->whereHas('floor', fn($f) => $f->where('block_id', $blockId));
            })->where('status', 'maintenance')->count(),
        ];

        return view('admin.apartments.matrix', compact('blocks', 'block', 'floors', 'blockId', 'stats'));
    }

    /**
     * Form tạo
     */
    public function create(Request $request): View
    {
        $selectedFloorId = $request->query('floor_id');
        $blocks = Block::orderBy('name')->get();
        $floors = Floor::with('block')
            ->orderBy('floor_number')
            ->get();
        $apartmentTypes = \App\Models\ApartmentType::orderBy('bedroom_count')
            ->orderBy('bathroom_count')
            ->orderBy('base_service_fee')
            ->get();

        return view(
            'admin.apartments.create',
            compact('floors', 'blocks', 'selectedFloorId', 'apartmentTypes')
        );
    }

    /**
     * Lưu căn hộ
     */
    public function store(
        Request $request
    ): RedirectResponse {

        $validated = $request->validate([

            'floor_id' => [
                'required',
                'exists:floors,id',
            ],

            'apartment_type_id' => [
                'nullable',
                'exists:apartment_types,id',
            ],

            'apartment_number' => [
                'required',
                'string',
                'max:20',
            ],

            'area' => [
                'required',
                'numeric',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'images.*' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:5120'
            ],

        ]);

        $imagesPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('apartments', 'public');
                $imagesPaths[] = $path;
            }
        }
        $validated['images'] = $imagesPaths;

        /**
         * Check unique căn hộ trong tầng (bao gồm cả căn hộ đã xóa)
         */
        $existing = Apartment::withTrashed()
            ->where('floor_id', $validated['floor_id'])
            ->where('apartment_number', $validated['apartment_number'])
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Khôi phục và cập nhật
                $existing->restore();
                $existing->update($validated);
                
                return redirect()
                    ->route('admin.apartments.index', ['floor_id' => $validated['floor_id']])
                    ->with('success', 'Căn hộ (đã xóa trước đó) đã được khôi phục và cập nhật thành công.');
            }

            return back()
                ->withInput()
                ->withErrors([
                    'apartment_number' => 'Số căn hộ đã tồn tại trong tầng này.'
                ]);
        }

        $apartment = Apartment::create($validated);



        return redirect()
            ->route(
                'admin.apartments.index',
                [
                    'floor_id' =>
                    $validated['floor_id']
                ]
            )
            ->with(
                'success',
                'Căn hộ đã được tạo thành công.'
            );
    }

    /**
     * Chi tiết căn hộ
     */
    public function show(Apartment $apartment): View
    {
        $apartment->load(['floor.block', 'residents.user', 'invoices', 'vehicles']);

        $declaredMembers = \App\Models\ApartmentMember::where('apartment_id', $apartment->id)
            ->orderBy('created_at')
            ->get();

        $allResidents = \App\Models\User::where('role', 'resident')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $residentsHistory = \App\Models\Resident::withTrashed()
            ->with(['user'])
            ->where('apartment_id', $apartment->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $temporaryRegistrations = \App\Models\TemporaryRegistration::with(['user', 'approver'])
            ->where('apartment_id', $apartment->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.apartments.show', compact('apartment', 'declaredMembers', 'allResidents', 'residentsHistory', 'temporaryRegistrations'));
    }

    /**
     * Gán chủ hộ trực tiếp
     */
    public function assignOwner(Request $request, Apartment $apartment): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'Vui lòng chọn cư dân để gán.',
            'user_id.exists' => 'Cư dân được chọn không hợp lệ.',
        ]);

        $user = \App\Models\User::findOrFail($validated['user_id']);

        if ($user->role !== 'resident') {
            return back()->with('error', 'Người dùng được chọn không phải là cư dân.');
        }

        // Kiểm tra giới hạn số lượng cư dân tối đa (10 người)
        $currentCount = $apartment->residents()
            ->whereNull('deleted_at')
            ->whereIn('status', ['active', 'pending'])
            ->count();
        if ($currentCount >= 10) {
            return back()->with('error', 'Căn hộ đã đạt giới hạn cư dân tối đa (10 người).');
        }

        // 1. Kiểm tra căn hộ đã có chủ hộ hay chưa
        $hasOwner = $apartment->residents()
            ->where('relationship', 'owner')
            ->whereNull('deleted_at')
            ->exists();

        if ($hasOwner) {
            return back()->with('error', 'Căn hộ này đã có chủ hộ đăng ký trong hệ thống.');
        }

        // 2. Gán cư dân làm chủ hộ
        \Illuminate\Support\Facades\DB::transaction(function () use ($apartment, $user) {
            $resident = \App\Models\Resident::where('apartment_id', $apartment->id)
                ->where('user_id', $user->id)
                ->first();

            if ($resident) {
                $resident->update([
                    'relationship' => 'owner',
                    'end_date' => null,
                ]);
            } else {
                \App\Models\Resident::create([
                    'user_id' => $user->id,
                    'apartment_id' => $apartment->id,
                    'relationship' => 'owner',
                    'temporary_status' => 'permanent',
                    'start_date' => now()->toDateString(),
                ]);
            }
        });

        return redirect()
            ->route('admin.apartments.show', $apartment->id)
            ->with('success', 'Đã gán chủ hộ thành công cho cư dân ' . $user->name . '.');
    }

    /**
     * Gán khách thuê trực tiếp
     */
    public function assignTenant(Request $request, Apartment $apartment): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'user_id.required' => 'Vui lòng chọn cư dân để gán.',
            'user_id.exists' => 'Cư dân được chọn không hợp lệ.',
            'start_date.required' => 'Vui lòng chọn ngày bắt đầu thuê.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
        ]);

        $user = \App\Models\User::findOrFail($validated['user_id']);

        if ($user->role !== 'resident') {
            return back()->with('error', 'Người dùng được chọn không phải là cư dân.');
        }

        // Kiểm tra giới hạn số lượng cư dân tối đa (10 người)
        $currentCount = $apartment->residents()
            ->whereNull('deleted_at')
            ->whereIn('status', ['active', 'pending'])
            ->count();
        if ($currentCount >= 10) {
            return back()->with('error', 'Căn hộ đã đạt giới hạn cư dân tối đa (10 người).');
        }

        // Gán cư dân làm khách thuê
        \Illuminate\Support\Facades\DB::transaction(function () use ($apartment, $user, $validated) {
            $resident = \App\Models\Resident::where('apartment_id', $apartment->id)
                ->where('user_id', $user->id)
                ->first();

            $data = [
                'relationship' => 'tenant',
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'] ?? null,
            ];

            if ($resident) {
                $resident->update($data);
            } else {
                $data['user_id'] = $user->id;
                $data['apartment_id'] = $apartment->id;
                $data['temporary_status'] = 'permanent';
                \App\Models\Resident::create($data);
            }
        });

        return redirect()
            ->route('admin.apartments.show', $apartment->id)
            ->with('success', 'Đã gán khách thuê thành công cho cư dân ' . $user->name . '.');
    }

    /**
     * Cập nhật thông tin pháp lý
     */
    public function updateLegal(Request $request, Apartment $apartment): RedirectResponse
    {
        $validated = $request->validate([
            'handover_date' => 'nullable|date',
            'legal_status' => 'required|in:pending,processing,issued',
        ]);

        $apartment->update([
            'handover_date' => $validated['handover_date'] ?? null,
            'legal_status' => $validated['legal_status'],
        ]);

        return back()->with('success', 'Đã cập nhật thông tin pháp lý thành công.');
    }

    /**
     * Form sửa
     */
    public function edit(
        Apartment $apartment
    ): View {

        $blocks = Block::orderBy('name')->get();
        $floors = Floor::with('block')
            ->orderBy('floor_number')
            ->get();
        $apartmentTypes = \App\Models\ApartmentType::orderBy('bedroom_count')
            ->orderBy('bathroom_count')
            ->orderBy('base_service_fee')
            ->get();

        return view(
            'admin.apartments.edit',
            compact(
                'apartment',
                'floors',
                'blocks',
                'apartmentTypes'
            )
        );
    }

    /**
     * Cập nhật
     */
    public function update(
        Request $request,
        Apartment $apartment
    ): RedirectResponse {

        $validated = $request->validate([

            'floor_id' => [
                'required',
                'exists:floors,id',
            ],

            'apartment_type_id' => [
                'nullable',
                'exists:apartment_types,id',
            ],

            'apartment_number' => [
                'required',
                'string',
                'max:20',
            ],

            'area' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:vacant,occupied,maintenance',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'images.*' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:5120'
            ],
            
            'old_images' => [
                'nullable',
                'array'
            ],

        ]);

        $imagesPaths = $request->input('old_images', []);
        
        // Find deleted images to delete from storage
        $currentImages = $apartment->images ?? [];
        $deletedImages = array_diff($currentImages, $imagesPaths);
        foreach ($deletedImages as $deletedImage) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($deletedImage);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('apartments', 'public');
                $imagesPaths[] = $path;
            }
        }
        
        $validated['images'] = $imagesPaths;

        /**
         * Check duplicate (bao gồm cả căn hộ đã xóa)
         */
        $exists = Apartment::withTrashed()
            ->where('floor_id', $validated['floor_id'])
            ->where('apartment_number', $validated['apartment_number'])
            ->where('id', '!=', $apartment->id)
            ->exists();

        if ($exists) {

            return back()
                ->withInput()
                ->withErrors([
                    'apartment_number' => 'Số căn hộ đã tồn tại (hoặc đã bị xóa) trong tầng này.'
                ]);
        }

        $apartment->update($validated);



        return redirect()
            ->route(
                'admin.apartments.index',
                [
                    'floor_id' =>
                    $validated['floor_id']
                ]
            )
            ->with(
                'success',
                'Căn hộ đã được cập nhật thành công.'
            );
    }

    /**
     * Xóa căn hộ
     */
    public function destroy(
        Apartment $apartment
    ): RedirectResponse {

        $floorId = $apartment->floor_id;

        // 1. Kiểm tra trạng thái căn hộ
        if ($apartment->status === 'occupied') {
            return back()->with('error', 'Không thể xóa căn hộ đang có trạng thái "Đang ở". Vui lòng cập nhật trạng thái trước khi xóa.');
        }

        // 2. Kiểm tra xem căn hộ có cư dân nào không
        if ($apartment->residents()->exists()) {
            return back()->with('error', 'Không thể xóa căn hộ đang có cư dân sinh sống. Vui lòng chuyển cư dân đi trước khi xóa.');
        }

        $apartmentNumber = $apartment->apartment_number;
        $apartment->delete();



        return redirect()
            ->route(
                'admin.apartments.index',
                [
                    'floor_id' => $floorId
                ]
            )
            ->with(
                'success',
                'Căn hộ đã được xóa thành công.'
            );
    }

    /**
     * Tải file Excel mẫu để nhập căn hộ
     */
    public function downloadTemplate(): \Symfony\Component\HttpFoundation\Response
    {
        try {
            $filePath = \App\Helpers\SimpleXlsx::exportApartmentTemplate();
            return response()->download($filePath, 'template_danh_sach_can_ho.xlsx')->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi khi tạo file mẫu: ' . $e->getMessage());
        }
    }

    /**
     * Nhập căn hộ, tầng, tòa nhà từ Excel
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|max:4096', // Max 4MB
        ]);

        $file = $request->file('csv_file');
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls'])) {
            return back()->with('error', 'Chỉ chấp nhận tệp Excel có định dạng .xlsx hoặc .xls.');
        }

        try {
            $rows = \App\Helpers\SimpleXlsx::parse($file->getRealPath());
            
            if (count($rows) <= 1) {
                return back()->with('error', 'Tệp Excel trống hoặc không đúng cấu trúc mẫu.');
            }

            // Dòng đầu tiên là header
            $header = $rows[0];
            
            // Validate sơ bộ số cột (ít nhất phải có 15 cột cho cấu trúc mẫu đầy đủ)
            if (count($header) < 15) {
                return back()->with('error', 'Tệp Excel không đúng số cột quy định của file mẫu (yêu cầu 15 cột mới).');
            }

            $successCount = 0;
            $updatedCount = 0;

            \Illuminate\Support\Facades\DB::transaction(function () use ($rows, &$successCount, &$updatedCount) {
                // Duyệt từ dòng thứ 2 (chỉ mục 1)
                for ($i = 1; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    
                    // Bỏ qua dòng trống hoàn toàn
                    $isEmptyRow = true;
                    foreach ($row as $val) {
                        if ($val !== '') {
                            $isEmptyRow = false;
                            break;
                        }
                    }
                    if ($isEmptyRow) {
                        continue;
                    }

                    $blockName = trim($row[0] ?? '');
                    $blockCode = trim($row[1] ?? '');
                    $blockTotalFloors = trim($row[2] ?? '');
                    $blockTotalBasements = trim($row[3] ?? '');
                    $blockAptsPerFloor = trim($row[4] ?? '');
                    $blockAmenities = trim($row[5] ?? '');
                    $floorNumberStr = trim($row[6] ?? '');
                    $floorName = trim($row[7] ?? '');
                    $floorTypeStr = trim($row[8] ?? '');
                    $floorDescription = trim($row[9] ?? '');
                    $apartmentNumber = trim($row[10] ?? '');
                    $apartmentTypeName = trim($row[11] ?? '');
                    $areaStr = trim($row[12] ?? '');
                    $statusStr = trim($row[13] ?? '');
                    $apartmentDescription = trim($row[14] ?? '');

                    $rowNum = $i + 1; // Số hàng trong Excel (1-indexed)

                    // Validate các trường bắt buộc
                    if (empty($blockName)) {
                        throw new \Exception("Dòng {$rowNum}: Tên tòa nhà không được để trống.");
                    }
                    if ($floorNumberStr === '') {
                        throw new \Exception("Dòng {$rowNum}: Số tầng không được để trống.");
                    }
                    if (!is_numeric($floorNumberStr)) {
                        throw new \Exception("Dòng {$rowNum}: Số tầng phải là số nguyên (ví dụ: 1, 2, 3).");
                    }
                    $floorNumber = (int)$floorNumberStr;

                    if (empty($apartmentNumber)) {
                        throw new \Exception("Dòng {$rowNum}: Số căn hộ không được để trống.");
                    }
                    if ($areaStr === '') {
                        throw new \Exception("Dòng {$rowNum}: Diện tích không được để trống.");
                    }
                    if (!is_numeric($areaStr)) {
                        throw new \Exception("Dòng {$rowNum}: Diện tích phải là số thập phân (ví dụ: 75.50).");
                    }
                    $area = (float)$areaStr;

                    // 1. Tìm hoặc tạo Block
                    $block = Block::whereRaw('LOWER(name) = ?', [strtolower($blockName)])->first();
                    $amenitiesArray = $blockAmenities ? array_map('trim', explode(',', $blockAmenities)) : null;

                    if (!$block) {
                        $block = Block::create([
                            'name' => $blockName,
                            'code' => $blockCode ?: \Illuminate\Support\Str::slug($blockName),
                            'status' => 'active',
                            'total_floors' => $blockTotalFloors !== '' ? (int)$blockTotalFloors : null,
                            'total_basements' => $blockTotalBasements !== '' ? (int)$blockTotalBasements : null,
                            'apartments_per_floor' => $blockAptsPerFloor !== '' ? (int)$blockAptsPerFloor : null,
                            'amenities' => $amenitiesArray,
                        ]);
                    } else {
                        // Cập nhật thông tin block nếu có giá trị mới
                        $updateData = [];
                        if ($blockCode !== '') $updateData['code'] = $blockCode;
                        if ($blockTotalFloors !== '') $updateData['total_floors'] = (int)$blockTotalFloors;
                        if ($blockTotalBasements !== '') $updateData['total_basements'] = (int)$blockTotalBasements;
                        if ($blockAptsPerFloor !== '') $updateData['apartments_per_floor'] = (int)$blockAptsPerFloor;
                        if ($blockAmenities !== '') $updateData['amenities'] = $amenitiesArray;
                        
                        if (!empty($updateData)) {
                            $block->update($updateData);
                        }
                    }

                    // Phân loại tầng từ tiếng Việt sang enum
                    $floorType = 'above_ground';
                    $floorTypeLower = mb_strtolower($floorTypeStr, 'UTF-8');
                    if (str_contains($floorTypeLower, 'hầm') || str_contains($floorTypeLower, 'basement')) {
                        $floorType = 'basement';
                    }

                    // 2. Tìm hoặc tạo Floor
                    $floor = Floor::where('block_id', $block->id)
                        ->where('floor_number', $floorNumber)
                        ->first();
                    if (!$floor) {
                        $floor = Floor::create([
                            'block_id' => $block->id,
                            'floor_number' => $floorNumber,
                            'name' => $floorName ?: "Tầng {$floorNumber}",
                            'status' => 'active',
                            'description' => $floorDescription ?: null,
                            'floor_type' => $floorType,
                        ]);
                    } else {
                        // Cập nhật thông tin floor nếu có giá trị mới
                        $updateData = [];
                        if ($floorName !== '') $updateData['name'] = $floorName;
                        if ($floorDescription !== '') $updateData['description'] = $floorDescription;
                        if ($floorTypeStr !== '') $updateData['floor_type'] = $floorType;
                        if (!empty($updateData)) {
                            $floor->update($updateData);
                        }
                    }

                    // 3. Ánh xạ trạng thái căn hộ Lowercase
                    $status = 'vacant';
                    $statusLower = mb_strtolower($statusStr, 'UTF-8');
                    if (str_contains($statusLower, 'đang ở') || str_contains($statusLower, 'occupied')) {
                        $status = 'occupied';
                    } elseif (str_contains($statusLower, 'bảo trì') || str_contains($statusLower, 'maintenance') || str_contains($statusLower, 'sửa chữa')) {
                        $status = 'maintenance';
                    }

                    // 4. Tìm loại căn hộ (nếu có nhập Tên Loại Căn Hộ)
                    $apartmentTypeId = null;
                    if ($apartmentTypeName !== '') {
                        $aptType = \App\Models\ApartmentType::whereRaw('LOWER(name) = ?', [strtolower($apartmentTypeName)])->first();
                        if ($aptType) {
                            $apartmentTypeId = $aptType->id;
                        }
                    }

                    // 5. Tìm hoặc tạo/cập nhật Apartment (tìm cả căn hộ đã xóa mềm)
                    $apartment = Apartment::withTrashed()
                        ->where('floor_id', $floor->id)
                        ->where('apartment_number', $apartmentNumber)
                        ->first();

                    if ($apartment) {
                        if ($apartment->trashed()) {
                            $apartment->restore();
                        }
                        
                        $updateData = [
                            'area' => $area,
                            'status' => $status,
                        ];
                        if ($apartmentDescription !== '') {
                            $updateData['description'] = $apartmentDescription;
                        }
                        if ($apartmentTypeId !== null) {
                            $updateData['apartment_type_id'] = $apartmentTypeId;
                        }
                        
                        $apartment->update($updateData);
                        $updatedCount++;
                    } else {
                        Apartment::create([
                            'floor_id' => $floor->id,
                            'apartment_type_id' => $apartmentTypeId,
                            'apartment_number' => $apartmentNumber,
                            'area' => $area,
                            'status' => $status,
                            'description' => $apartmentDescription,
                        ]);
                        $successCount++;
                    }
                }
            });

            $msg = "Đã nhập dữ liệu thành công. Thêm mới {$successCount} căn hộ, cập nhật {$updatedCount} căn hộ.";



            return back()->with('success', $msg);

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi nhập dữ liệu: ' . $e->getMessage());
        }
    }
}
