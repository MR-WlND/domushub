<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Block;
use App\Models\Floor;
use App\Models\Apartment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class FloorController extends Controller
{
    /**
     * Danh sách tầng (có thể lọc theo tòa nhà)
     */
    public function index(Request $request): View
    {
        $blockId = $request->query('block_id');
        $block   = null;

        if ($blockId) {
            $block  = Block::findOrFail($blockId);
            $floors = $block->floors()
                ->withCount('apartments')
                ->paginate(15);
        } else {
            $floors = Floor::with('block')
                ->withCount('apartments')
                ->orderBy('floor_number')
                ->paginate(15);
        }

        $blocks = Block::orderBy('name')->get();

        return view('admin.floors.index', compact('floors', 'blocks', 'block'));
    }

    /**
     * Form tạo tầng
     */
    public function create(Request $request): View
    {
        // Tự động chạy migration nếu cần thiết
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
            // Bỏ qua lỗi
        }

        $selectedBlockId = $request->query('block_id');
        $blocks = Block::orderBy('name')->get();
        return view('admin.floors.create', compact('blocks', 'selectedBlockId'));
    }

    /**
     * Lưu tầng mới
     */
    public function store(Request $request): RedirectResponse
    {
        // Tự động chạy migration nếu cần thiết
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
            // Bỏ qua lỗi
        }

        $validated = $request->validate([
            'block_id'             => 'required|exists:blocks,id',
            'name'                 => 'required|string|max:50',
            'floor_type'           => 'required|in:residential,commercial,technical,amenity',
            'status'               => 'required|in:active,maintenance,inactive',
            'description'          => 'nullable|string',
            'number_of_apartments' => 'nullable|integer|min:0|max:100',
        ]);

        // Phân tích số tầng từ tên tầng
        $floorNumber = $this->parseFloorNumber($validated['name'], (int)$validated['block_id']);

        // Lấy thông tin tòa nhà để check giới hạn tầng
        $block = Block::findOrFail($validated['block_id']);

        if ($floorNumber < 0) {
            if (!is_null($block->total_basements)) {
                $currentBasements = Floor::where('block_id', $block->id)
                    ->where('floor_number', '<', 0)
                    ->count();
                if ($currentBasements >= $block->total_basements) {
                    return back()->withInput()->withErrors([
                        'name' => 'Tòa nhà này đã đạt giới hạn tối đa ' . $block->total_basements . ' tầng hầm.',
                    ]);
                }
            }
        } else {
            if (!is_null($block->total_floors)) {
                $currentFloors = Floor::where('block_id', $block->id)
                    ->where('floor_number', '>=', 0)
                    ->count();
                if ($currentFloors >= $block->total_floors) {
                    return back()->withInput()->withErrors([
                        'name' => 'Tòa nhà này đã đạt giới hạn tối đa ' . $block->total_floors . ' tầng nổi.',
                    ]);
                }
            }
        }

        // Kiểm tra tầng trùng trong tòa
        $exists = Floor::where('block_id', $validated['block_id'])
            ->where('floor_number', $floorNumber)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'name' => 'Số tầng được phân tích từ tên này (' . $floorNumber . ') đã tồn tại trong tòa nhà đã chọn.',
            ]);
        }

        DB::transaction(function () use ($validated, $floorNumber) {
            $floor = Floor::create([
                'block_id'     => $validated['block_id'],
                'floor_number' => $floorNumber,
                'name'         => $validated['name'],
                'floor_type'   => $validated['floor_type'],
                'status'       => $validated['status'],
                'description'  => $validated['description'],
            ]);

            $numberOfApartments = isset($validated['number_of_apartments']) ? (int)$validated['number_of_apartments'] : 0;

            if ($numberOfApartments > 0) {
                for ($i = 1; $i <= $numberOfApartments; $i++) {
                    if ($floorNumber > 0) {
                        $apartmentNumber = $floorNumber . str_pad($i, 2, '0', STR_PAD_LEFT);
                    } elseif ($floorNumber === 0) {
                        $apartmentNumber = '0' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    } else {
                        $apartmentNumber = 'B' . abs($floorNumber) . str_pad($i, 2, '0', STR_PAD_LEFT);
                    }

                    Apartment::create([
                        'floor_id'         => $floor->id,
                        'apartment_number' => $apartmentNumber,
                        'area'             => 0,
                        'status'           => 'vacant',
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.blocks.index', ['featured_block_id' => $validated['block_id']])
            ->with('success', 'Tầng đã được tạo thành công.');
    }

    /**
     * Phân tích số tầng từ tên tầng
     */
    private function parseFloorNumber(string $name, int $blockId): int
    {
        $nameLower = mb_strtolower(trim($name), 'UTF-8');
        
        // 1. Kiểm tra tầng trệt
        if (str_contains($nameLower, 'trệt') || $nameLower === 'g' || $nameLower === 'tầng g') {
            return 0;
        }
        
        // 2. Kiểm tra tầng hầm
        if (str_contains($nameLower, 'hầm') || str_contains($nameLower, 'basement') || str_starts_with($nameLower, 'b')) {
            preg_match('/\d+/', $nameLower, $matches);
            if (!empty($matches)) {
                return -((int)$matches[0]);
            }
            return -1; // Mặc định hầm 1 nếu không có số cụ thể
        }
        
        // 3. Trường hợp số bình thường
        preg_match('/\d+/', $nameLower, $matches);
        if (!empty($matches)) {
            return (int)$matches[0];
        }
        
        // 4. Nếu không có số, tự động tăng từ tầng cao nhất hiện tại của block
        $maxFloor = Floor::where('block_id', $blockId)->max('floor_number');
        return is_null($maxFloor) ? 1 : $maxFloor + 1;
    }

    /**
     * Chi tiết tầng kèm danh sách căn hộ
     */
    public function show(Floor $floor): View
    {
        $floor->load(['block', 'apartments' => function ($query) {
            $query->with(['apartmentType'])->withCount(['residents', 'declaredMembers']);
        }]);

        $stats = [
            'total'       => $floor->apartments->count(),
            'occupied'    => $floor->apartments->where('status', 'occupied')->count(),
            'vacant'      => $floor->apartments->where('status', 'vacant')->count(),
            'maintenance' => $floor->apartments->where('status', 'maintenance')->count(),
        ];

        $stats['occupancy_rate'] = $stats['total'] > 0
            ? round(($stats['occupied'] / $stats['total']) * 100, 1)
            : 0;

        return view('admin.floors.show', compact('floor', 'stats'));
    }

    public function edit(Floor $floor): View
    {
        // Tự động chạy migration nếu cần thiết
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
            // Bỏ qua lỗi
        }

        $floor->loadCount('apartments');
        $blocks = Block::orderBy('name')->get();
        return view('admin.floors.edit', compact('floor', 'blocks'));
    }

    /**
     * Cập nhật tầng
     */
    public function update(Request $request, Floor $floor): RedirectResponse
    {
        // Tự động chạy migration nếu cần thiết
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        } catch (\Exception $e) {
            // Bỏ qua lỗi
        }

        $currentApartmentsCount = $floor->apartments()->count();

        $validated = $request->validate([
            'block_id'             => 'required|exists:blocks,id',
            'name'                 => 'required|string|max:50',
            'floor_type'           => 'required|in:residential,commercial,technical,amenity',
            'status'               => 'required|in:active,maintenance,inactive',
            'description'          => 'nullable|string',
            'number_of_apartments' => 'nullable|integer|min:' . $currentApartmentsCount . '|max:100',
        ], [
            'number_of_apartments.min' => 'Không thể giảm số lượng căn hộ thấp hơn số lượng hiện tại (' . $currentApartmentsCount . ') để tránh mất mát dữ liệu.',
        ]);

        // Phân tích số tầng từ tên tầng
        $floorNumber = $this->parseFloorNumber($validated['name'], (int)$validated['block_id']);

        // Lấy thông tin tòa nhà để check giới hạn tầng
        $block = Block::findOrFail($validated['block_id']);

        if ($floorNumber < 0) {
            if (!is_null($block->total_basements)) {
                $currentBasements = Floor::where('block_id', $block->id)
                    ->where('floor_number', '<', 0)
                    ->where('id', '!=', $floor->id)
                    ->count();
                if ($currentBasements >= $block->total_basements) {
                    return back()->withInput()->withErrors([
                        'name' => 'Tòa nhà này đã đạt giới hạn tối đa ' . $block->total_basements . ' tầng hầm.',
                    ]);
                }
            }
        } else {
            if (!is_null($block->total_floors)) {
                $currentFloors = Floor::where('block_id', $block->id)
                    ->where('floor_number', '>=', 0)
                    ->where('id', '!=', $floor->id)
                    ->count();
                if ($currentFloors >= $block->total_floors) {
                    return back()->withInput()->withErrors([
                        'name' => 'Tòa nhà này đã đạt giới hạn tối đa ' . $block->total_floors . ' tầng nổi.',
                    ]);
                }
            }
        }

        // Kiểm tra trùng (bỏ qua chính nó)
        $exists = Floor::where('block_id', $validated['block_id'])
            ->where('floor_number', $floorNumber)
            ->where('id', '!=', $floor->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'name' => 'Số tầng được phân tích từ tên này (' . $floorNumber . ') đã tồn tại trong tòa nhà đã chọn.',
            ]);
        }

        DB::transaction(function () use ($floor, $validated, $floorNumber, $currentApartmentsCount) {
            $floor->update([
                'block_id'     => $validated['block_id'],
                'floor_number' => $floorNumber,
                'name'         => $validated['name'],
                'floor_type'   => $validated['floor_type'],
                'status'       => $validated['status'],
                'description'  => $validated['description'],
            ]);

            $numberOfApartments = isset($validated['number_of_apartments']) ? (int)$validated['number_of_apartments'] : $currentApartmentsCount;

            if ($numberOfApartments > $currentApartmentsCount) {
                $diff = $numberOfApartments - $currentApartmentsCount;
                $added = 0;
                $i = 1;
                while ($added < $diff) {
                    if ($floorNumber > 0) {
                        $apartmentNumber = $floorNumber . str_pad($i, 2, '0', STR_PAD_LEFT);
                    } elseif ($floorNumber === 0) {
                        $apartmentNumber = '0' . str_pad($i, 2, '0', STR_PAD_LEFT);
                    } else {
                        $apartmentNumber = 'B' . abs($floorNumber) . str_pad($i, 2, '0', STR_PAD_LEFT);
                    }

                    $existsApartment = Apartment::where('floor_id', $floor->id)
                        ->where('apartment_number', $apartmentNumber)
                        ->exists();

                    if (!$existsApartment) {
                        Apartment::create([
                            'floor_id'         => $floor->id,
                            'apartment_number' => $apartmentNumber,
                            'area'             => 0,
                            'status'           => 'vacant',
                        ]);
                        $added++;
                    }
                    $i++;
                }
            }
        });

        return redirect()
            ->route('admin.blocks.index', ['featured_block_id' => $validated['block_id']])
            ->with('success', 'Tầng đã được cập nhật thành công.');
    }

    /**
     * Xóa tầng — chỉ cho phép khi không còn căn hộ
     */
    public function destroy(Floor $floor): RedirectResponse
    {
        $blockId = $floor->block_id;

        if ($floor->apartments()->exists()) {
            return redirect()
                ->route('admin.blocks.index', ['featured_block_id' => $blockId])
                ->with('error', 'Không thể xóa tầng đang có căn hộ. Hãy xóa hoặc chuyển tất cả căn hộ trước.');
        }

        $floor->delete();

        return redirect()
            ->route('admin.blocks.index', ['featured_block_id' => $blockId])
            ->with('success', 'Tầng đã được xóa thành công.');
    }
}