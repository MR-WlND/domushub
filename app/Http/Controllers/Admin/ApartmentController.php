<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

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
    public function create(): View
    {
        $blocks = Block::orderBy('name')->get();
        $floors = Floor::with('block')
            ->orderBy('floor_number')
            ->get();

        return view(
            'admin.apartments.create',
            compact('floors', 'blocks')
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
     * Tải file CSV mẫu để nhập căn hộ
     */
    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=mau_nhap_can_ho.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Đầy đủ tất cả các trường cho cả Tòa nhà, Tầng và Căn hộ
        $columns = [
            'Tên tòa nhà', 
            'Mã tòa nhà', 
            'Trạng thái tòa nhà (Hoạt động / Bảo trì / Tạm ngưng)', 
            'Người quản lý tòa nhà', 
            'Liên hệ quản lý tòa nhà', 
            'Mô tả tòa nhà',
            
            'Tên tầng', 
            'Loại tầng (Căn hộ / Tầng hầm / Thương mại / Dịch vụ)', 
            'Số căn hộ dự kiến', 
            'Trạng thái tầng (Hoạt động / Bảo trì / Ngưng)', 
            'Mô tả tầng',
            
            'Số căn hộ', 
            'Diện tích m2', 
            'Trạng thái căn hộ (Trống / Đang ở / Bảo trì)', 
            'Mô tả căn hộ'
        ];

        $callback = function() use($columns) {
            if (ob_get_level() > 0) {
                ob_clean();
            }
            $file = fopen('php://output', 'w');
            // Thêm UTF-8 BOM ở vị trí byte đầu tiên (bắt buộc để Excel nhận diện UTF-8)
            fwrite($file, "\xEF\xBB\xBF");
            fputcsv($file, $columns, ';');
            
            // Dòng mẫu 1
            fputcsv($file, [
                'Tòa D', 'BLOCK_D', 'Hoạt động', 'Nguyễn Văn A', '0901234567', 'Khu căn hộ cao cấp phía Tây',
                'Tầng 3', 'Căn hộ', '10', 'Hoạt động', 'Tầng căn hộ điển hình',
                'D3.01', '75.5', 'Trống', 'Căn hộ hướng Đông Nam'
            ], ';');
            
            // Dòng mẫu 2
            fputcsv($file, [
                'Tòa D', 'BLOCK_D', 'Hoạt động', 'Nguyễn Văn A', '0901234567', 'Khu căn hộ cao cấp phía Tây',
                'Tầng 3', 'Căn hộ', '10', 'Hoạt động', 'Tầng căn hộ điển hình',
                'D3.02', '80.0', 'Bảo trì', 'Căn góc gần thang máy'
            ], ';');
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Xử lý file CSV tải lên và nhập dữ liệu hàng loạt
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:4096',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        $handle = fopen($path, 'r');
        if (!$handle) {
            return back()->with('error', 'Không thể mở file CSV.');
        }

        $delimiter = ',';

        // Bỏ qua BOM nếu có
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // Đọc dòng đầu tiên xem có chỉ định dấu phân cách (sep=) không
        $firstLine = fgets($handle);
        if (str_starts_with(trim($firstLine), 'sep=')) {
            $parts = explode('=', trim($firstLine));
            $delimiter = isset($parts[1]) ? trim($parts[1]) : ',';
            // Đọc dòng tiêu đề tiếp theo
            $headers = fgetcsv($handle, 1000, $delimiter);
        } else {
            // Không có sep=, quay lại đầu tệp (sau BOM nếu có)
            rewind($handle);
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }
            
            $pos = ftell($handle);
            $headers = fgetcsv($handle, 1000, ',');
            // Nếu chỉ có 1 cột và chứa dấu chấm phẩy, thử chuyển sang dấu chấm phẩy
            if ($headers && count($headers) === 1 && str_contains($headers[0], ';')) {
                fseek($handle, $pos);
                $delimiter = ';';
                $headers = fgetcsv($handle, 1000, ';');
            }
        }

        if (!$headers) {
            fclose($handle);
            return back()->with('error', 'File CSV không có dữ liệu tiêu đề.');
        }

        $successCount = 0;
        $errors = [];
        $rowNumber = 1; // Dòng tiêu đề

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($handle, 2000, $delimiter)) !== false) {
                $rowNumber++;
                
                // Bỏ qua dòng trống
                if (empty(array_filter($row))) {
                    continue;
                }

                // Chuyển đổi mã hóa từng ô trong dòng sang UTF-8 nếu không phải UTF-8
                $row = array_map(function($val) {
                    if (empty($val)) return $val;
                    if (mb_check_encoding($val, 'UTF-8')) {
                        return $val;
                    }
                    // Thử chuyển từ Windows-1252 (mã hóa ANSI mặc định của Excel) sang UTF-8 bằng iconv
                    $converted = @iconv('Windows-1252', 'UTF-8//IGNORE', $val);
                    if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                        return $converted;
                    }
                    // Thử chuyển từ Windows-1258 (tiếng Việt ANSI Excel) sang UTF-8 bằng iconv
                    $converted = @iconv('Windows-1258', 'UTF-8//IGNORE', $val);
                    if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                        return $converted;
                    }
                    return $val;
                }, $row);

                // Ánh xạ cột (15 cột tương ứng mẫu)
                $blockName           = trim($row[0] ?? '');
                $blockCode           = trim($row[1] ?? '');
                $blockStatusLabel    = trim($row[2] ?? '');
                $blockManagerName    = trim($row[3] ?? '');
                $blockManagerContact = trim($row[4] ?? '');
                $blockDescription    = trim($row[5] ?? '');

                $floorName           = trim($row[6] ?? '');
                $floorTypeLabel      = trim($row[7] ?? '');
                $floorExpectedApts   = trim($row[8] ?? '');
                $floorStatusLabel    = trim($row[9] ?? '');
                $floorDescription    = trim($row[10] ?? '');

                $apartmentNumber     = trim($row[11] ?? '');
                $areaRaw             = trim($row[12] ?? '');
                $statusLabel         = trim($row[13] ?? 'Trống');
                $description         = trim($row[14] ?? '');

                // Bỏ qua dòng trống (kể cả khi dòng chỉ chứa dấu phân cách hoặc khoảng trắng)
                if ($blockName === '' && $floorName === '' && $apartmentNumber === '') {
                    continue;
                }

                // Xác thực các trường bắt buộc
                if (!$blockName || !$floorName || !$apartmentNumber) {
                    $errors[] = "Dòng {$rowNumber}: Tên Tòa nhà, Tên Tầng và Số căn hộ là các trường bắt buộc.";
                    continue;
                }

                // Xử lý diện tích (chuyển đổi dấu phẩy thập phân sang dấu chấm của lập trình)
                $area = 0;
                if ($areaRaw !== '') {
                    $areaRaw = str_replace(',', '.', $areaRaw);
                    if (!is_numeric($areaRaw) || floatval($areaRaw) < 0) {
                        $errors[] = "Dòng {$rowNumber}: Diện tích căn hộ phải là một số không âm.";
                        continue;
                    }
                    $area = floatval($areaRaw);
                }

                // Xử lý Số căn hộ dự kiến
                $expectedApts = null;
                if ($floorExpectedApts !== '') {
                    if (!is_numeric($floorExpectedApts) || intval($floorExpectedApts) < 0) {
                        $errors[] = "Dòng {$rowNumber}: Số căn hộ dự kiến phải là số nguyên không âm.";
                        continue;
                    }
                    $expectedApts = intval($floorExpectedApts);
                }

                // Ánh xạ trạng thái Tòa nhà
                $blockStatus = 'active';
                $bsLower = mb_strtolower($blockStatusLabel);
                if (str_contains($bsLower, 'trì') || str_contains($bsLower, 'maintenance')) {
                    $blockStatus = 'maintenance';
                } elseif (str_contains($bsLower, 'tạm ngưng') || str_contains($bsLower, 'inactive') || str_contains($bsLower, 'ngưng')) {
                    $blockStatus = 'inactive';
                }

                // Ánh xạ loại tầng
                $floorType = 'resident';
                $ftLower = mb_strtolower($floorTypeLabel);
                if (str_contains($ftLower, 'hầm') || str_contains($ftLower, 'basement')) {
                    $floorType = 'basement';
                } elseif (str_contains($ftLower, 'thương mại') || str_contains($ftLower, 'commercial')) {
                    $floorType = 'commercial';
                } elseif (str_contains($ftLower, 'dịch vụ') || str_contains($ftLower, 'service')) {
                    $floorType = 'service';
                }

                // Ánh xạ trạng thái Tầng
                $floorStatus = 'active';
                $fsLower = mb_strtolower($floorStatusLabel);
                if (str_contains($fsLower, 'trì') || str_contains($fsLower, 'maintenance')) {
                    $floorStatus = 'maintenance';
                } elseif (str_contains($fsLower, 'tạm ngưng') || str_contains($fsLower, 'inactive') || str_contains($fsLower, 'ngưng')) {
                    $floorStatus = 'inactive';
                }

                // Ánh xạ trạng thái Căn hộ
                $status = 'vacant';
                $statusLower = mb_strtolower($statusLabel);
                if (str_contains($statusLower, 'ở') || str_contains($statusLower, 'occupied') || str_contains($statusLower, 'đang ở')) {
                    $status = 'occupied';
                } elseif (str_contains($statusLower, 'trì') || str_contains($statusLower, 'sửa') || str_contains($statusLower, 'maintenance') || str_contains($statusLower, 'bảo trì')) {
                    $status = 'maintenance';
                }

                // 1. Tìm hoặc tự động tạo Tòa nhà
                if ($blockCode) {
                    $block = Block::firstOrCreate(
                        ['code' => $blockCode],
                        [
                            'name' => $blockName,
                            'status' => $blockStatus,
                            'manager_name' => $blockManagerName ?: null,
                            'manager_contact' => $blockManagerContact ?: null,
                            'description' => $blockDescription ?: null
                        ]
                    );
                } else {
                    $block = Block::firstOrCreate(
                        ['name' => $blockName],
                        [
                            'code' => strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $blockName)),
                            'status' => $blockStatus,
                            'manager_name' => $blockManagerName ?: null,
                            'manager_contact' => $blockManagerContact ?: null,
                            'description' => $blockDescription ?: null
                        ]
                    );
                }

                // Cập nhật thông tin tòa nhà nếu có thay đổi và chưa được điền trước đó
                if (!$block->wasRecentlyCreated) {
                    $updateBlockData = [];
                    if ($blockCode && !$block->code) $updateBlockData['code'] = $blockCode;
                    if ($blockName && !$block->name) $updateBlockData['name'] = $blockName;
                    if ($blockManagerName && !$block->manager_name) $updateBlockData['manager_name'] = $blockManagerName;
                    if ($blockManagerContact && !$block->manager_contact) $updateBlockData['manager_contact'] = $blockManagerContact;
                    if ($blockDescription && !$block->description) $updateBlockData['description'] = $blockDescription;
                    if (!empty($updateBlockData)) {
                        $block->update($updateBlockData);
                    }
                }

                // 2. Tự động nhận diện số thứ tự tầng từ tên tầng
                $floorNumber = 1;
                if (preg_match('/(-?\d+)/', $floorName, $matches)) {
                    $candidate = (int) $matches[1];
                    if ($candidate !== 0) {
                        $floorNumber = $candidate;
                    }
                } else {
                    $max = Floor::where('block_id', $block->id)->max('floor_number');
                    $floorNumber = is_numeric($max) ? $max + 1 : 1;
                }

                // 3. Tìm hoặc tự động tạo Tầng trực thuộc Tòa nhà (tra cứu theo block_id và floor_number để tránh lỗi trùng lặp khi tên tầng bị lỗi font hoặc viết khác nhau)
                $floor = Floor::firstOrCreate(
                    [
                        'block_id' => $block->id,
                        'floor_number' => $floorNumber
                    ],
                    [
                        'name' => $floorName,
                        'floor_type' => $floorType,
                        'expected_apartments' => $expectedApts,
                        'status' => $floorStatus,
                        'description' => $floorDescription ?: null
                    ]
                );

                // Cập nhật thông tin tầng nếu có thay đổi và chưa được điền trước đó
                if (!$floor->wasRecentlyCreated) {
                    $updateFloorData = [];
                    if ($expectedApts !== null && !$floor->expected_apartments) $updateFloorData['expected_apartments'] = $expectedApts;
                    if ($floorDescription && !$floor->description) $updateFloorData['description'] = $floorDescription;
                    if (!empty($updateFloorData)) {
                        $floor->update($updateFloorData);
                    }
                }

                // 4. Kiểm tra xem số căn hộ đã tồn tại trong tầng này chưa
                $exists = Apartment::where('floor_id', $floor->id)
                    ->where('apartment_number', $apartmentNumber)
                    ->exists();

                if ($exists) {
                    $errors[] = "Dòng {$rowNumber}: Căn hộ số '{$apartmentNumber}' đã tồn tại trong tầng này.";
                    continue;
                }

                // 5. Tạo mới căn hộ thực tế
                Apartment::create([
                    'floor_id' => $floor->id,
                    'apartment_number' => $apartmentNumber,
                    'area' => $area,
                    'status' => $status,
                    'description' => $description
                ]);

                $successCount++;
            }

            fclose($handle);

            if (!empty($errors)) {
                DB::rollBack();
                // Hiển thị tối đa 5 lỗi đầu tiên để tránh thông báo quá dài
                $displayedErrors = array_slice($errors, 0, 5);
                $errorMsg = "Nhập dữ liệu thất bại. Tiến trình đã bị hủy bỏ để đảm bảo tính nhất quán của dữ liệu.<br><strong>Chi tiết các lỗi phát hiện được:</strong><br>- " . implode('<br>- ', $displayedErrors);
                if (count($errors) > 5) {
                    $errorMsg .= "<br>... và " . (count($errors) - 5) . " lỗi khác. Vui lòng kiểm tra lại file CSV.";
                }
                return back()->with('error', $errorMsg);
            }

            DB::commit();
            return redirect()->route('admin.blocks.index')->with('success', "Đã nhập thành công {$successCount} căn hộ thực tế.");

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Import error exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            DB::rollBack();
            fclose($handle);
            return back()->with('error', 'Đã xảy ra lỗi hệ thống trong quá trình import: ' . $e->getMessage());
        }
    }
}