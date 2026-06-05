<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

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

        $floor = null;
        $block = null;

        $query = Apartment::query()
            ->with([
                'floor.block',
                'residents.user',
            ])
            ->withCount('residents');

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
                'floor',
                'block',
                'stats',
                'status',
                'search',
                'floorId',
                'blockId'
            )
        );
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

        return view(
            'admin.apartments.create',
            compact('floors', 'blocks', 'selectedFloorId')
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

            'apartment_number' => [
                'required',
                'string',
                'max:20',
            ],


            'status' => [
                'required',
                'in:vacant,occupied,maintenance',
            ],

            'description' => [
                'nullable',
                'string',
            ],

        ]);

        /**
         * Check unique căn hộ trong tầng
         */
        $exists = Apartment::where(
            'floor_id',
            $validated['floor_id']
        )
            ->where(
                'apartment_number',
                $validated['apartment_number']
            )
            ->exists();

        if ($exists) {

            return back()
                ->withInput()
                ->withErrors([

                    'apartment_number' =>
                    'Số căn hộ đã tồn tại trong tầng này.'

                ]);
        }

        $validated['area'] = 0;
        Apartment::create($validated);

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
        $apartment->load(['floor.block', 'residents']);

        return view('admin.apartments.show', compact('apartment'));
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

        return view(
            'admin.apartments.edit',
            compact(
                'apartment',
                'floors',
                'blocks'
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

            'apartment_number' => [
                'required',
                'string',
                'max:20',
            ],


            'status' => [
                'required',
                'in:vacant,occupied,maintenance',
            ],

            'description' => [
                'nullable',
                'string',
            ],

        ]);

        /**
         * Check duplicate
         */
        $exists = Apartment::where(
            'floor_id',
            $validated['floor_id']
        )
            ->where(
                'apartment_number',
                $validated['apartment_number']
            )
            ->where(
                'id',
                '!=',
                $apartment->id
            )
            ->exists();

        if ($exists) {

            return back()
                ->withInput()
                ->withErrors([

                    'apartment_number' =>
                    'Số căn hộ đã tồn tại trong tầng này.'

                ]);
        }

        $validated['area'] = 0;
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
            
            // Validate sơ bộ số cột (ít nhất phải có 12 cột cho cấu trúc mẫu đầy đủ)
            if (count($header) < 12) {
                return back()->with('error', 'Tệp Excel không đúng số cột quy định của file mẫu (yêu cầu 12 cột).');
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
                    $blockManagerName = trim($row[2] ?? '');
                    $blockManagerContact = trim($row[3] ?? '');
                    $blockDescription = trim($row[4] ?? '');
                    $floorNumberStr = trim($row[5] ?? '');
                    $floorName = trim($row[6] ?? '');
                    $floorDescription = trim($row[7] ?? '');
                    $apartmentNumber = trim($row[8] ?? '');
                    $areaStr = trim($row[9] ?? '');
                    $statusStr = trim($row[10] ?? '');
                    $apartmentDescription = trim($row[11] ?? '');

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
                    if (!$block) {
                        $block = Block::create([
                            'name' => $blockName,
                            'code' => $blockCode ?: \Illuminate\Support\Str::slug($blockName),
                            'status' => 'active',
                            'manager_name' => $blockManagerName ?: null,
                            'manager_contact' => $blockManagerContact ?: null,
                            'description' => $blockDescription ?: null,
                        ]);
                    } else {
                        // Cập nhật thông tin block nếu có giá trị mới
                        $updateData = [];
                        if ($blockCode !== '') $updateData['code'] = $blockCode;
                        if ($blockManagerName !== '') $updateData['manager_name'] = $blockManagerName;
                        if ($blockManagerContact !== '') $updateData['manager_contact'] = $blockManagerContact;
                        if ($blockDescription !== '') $updateData['description'] = $blockDescription;
                        if (!empty($updateData)) {
                            $block->update($updateData);
                        }
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
                        ]);
                    } else {
                        // Cập nhật thông tin floor nếu có giá trị mới
                        $updateData = [];
                        if ($floorName !== '') $updateData['name'] = $floorName;
                        if ($floorDescription !== '') $updateData['description'] = $floorDescription;
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

                    // 4. Tìm hoặc tạo/cập nhật Apartment (tìm cả căn hộ đã xóa mềm)
                    $apartment = Apartment::withTrashed()
                        ->where('floor_id', $floor->id)
                        ->where('apartment_number', $apartmentNumber)
                        ->first();

                    if ($apartment) {
                        if ($apartment->trashed()) {
                            $apartment->restore();
                        }
                        $apartment->update([
                            'area' => $area,
                            'status' => $status,
                            'description' => $apartmentDescription ?: $apartment->description,
                        ]);
                        $updatedCount++;
                    } else {
                        Apartment::create([
                            'floor_id' => $floor->id,
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