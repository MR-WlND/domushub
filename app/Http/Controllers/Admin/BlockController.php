<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BlockController extends Controller
{
    /**
     * Danh sách tòa nhà
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $featuredId = $request->query('featured_block_id');

        // Overall stats
        $totalBlocks      = Block::count();
        $totalFloors      = \App\Models\Floor::count();
        $totalApartments  = \App\Models\Apartment::count();
        $occupiedApts     = \App\Models\Apartment::where('status', 'occupied')->count();
        $occupancyRate    = $totalApartments > 0 ? round(($occupiedApts / $totalApartments) * 100) : 0;

        // Base query for blocks
        $blockQuery = Block::query()
            ->withCount(['floors', 'apartments'])
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($status,  fn($q) => $q->where('status', $status))
            ->orderBy('name');

        // Identify the featured block
        $featuredBlock = null;
        if ($featuredId) {
            $featuredBlock = (clone $blockQuery)->where('id', $featuredId)->first();
        }
        if (!$featuredBlock) {
            $featuredBlock = (clone $blockQuery)->first();
        }

        // Fetch paginated floors for the featured block
        $featuredFloors = null;
        if ($featuredBlock) {
            $featuredFloors = $featuredBlock->floors()
                ->withCount('apartments')
                ->orderBy('floor_number', 'desc')
                ->get();
        }

        // Get other blocks
        $otherBlocks = (clone $blockQuery);
        if ($featuredBlock) {
            $otherBlocks = $otherBlocks->where('id', '!=', $featuredBlock->id);
        }
        $otherBlocks = $otherBlocks->paginate(10, ['*'], 'block_page')->appends($request->except('block_page'));

        return view('admin.blocks.index', compact(
            'search', 'status',
            'totalBlocks', 'totalFloors', 'totalApartments', 'occupancyRate',
            'featuredBlock', 'featuredFloors', 'otherBlocks'
        ));
    }

    /**
     * Form tạo tòa nhà
     */
    public function create(): View
    {
        return view('admin.blocks.create');
    }

    /**
     * Lưu tòa nhà mới
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:50|unique:blocks,name',
            'code'            => 'nullable|string|max:100|unique:blocks,code',
            'status'          => 'nullable|in:active,inactive,maintenance',
            'manager_name'    => 'nullable|string|max:100',
            'manager_contact' => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'total_floors'    => 'nullable|integer|min:0',
            'total_basements' => 'nullable|integer|min:0',
        ]);

        Block::create([
            'name'            => $validated['name'],
            'code'            => $validated['code'] ?? null,
            'status'          => $validated['status'] ?? 'active',
            'manager_name'    => $validated['manager_name'] ?? null,
            'manager_contact' => $validated['manager_contact'] ?? null,
            'description'     => $validated['description'] ?? null,
            'total_floors'    => $validated['total_floors'] ?? null,
            'total_basements' => $validated['total_basements'] ?? null,
        ]);

        return redirect()
            ->route('admin.blocks.index')
            ->with('success', 'Tòa nhà đã được tạo thành công.');
    }

    /**
     * Chi tiết tòa nhà — hiển thị các tầng bên trong
     */
    public function show(Block $block): View
    {
        $floors = $block->floors()
            ->withCount('apartments')
            ->get(); // orderBy đã được định nghĩa trong quan hệ

        $stats = [
            'floors'      => $floors->count(),
            'apartments'  => $block->apartments()->count(),
            'vacant'      => $block->apartments()->where('apartments.status', 'vacant')->count(),
            'occupied'    => $block->apartments()->where('apartments.status', 'occupied')->count(),
            'maintenance' => $block->apartments()->where('apartments.status', 'maintenance')->count(),
        ];

        return view('admin.blocks.show', compact('block', 'floors', 'stats'));
    }

    /**
     * Form sửa tòa nhà
     */
    public function edit(Block $block): View
    {
        return view('admin.blocks.edit', compact('block'));
    }

    /**
     * Cập nhật tòa nhà
     */
    public function update(Request $request, Block $block): RedirectResponse
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:50|unique:blocks,name,' . $block->id,
            'code'            => 'nullable|string|max:100|unique:blocks,code,' . $block->id,
            'status'          => 'nullable|in:active,inactive,maintenance',
            'manager_name'    => 'nullable|string|max:100',
            'manager_contact' => 'nullable|string|max:100',
            'description'     => 'nullable|string',
            'total_floors'    => 'nullable|integer|min:0',
            'total_basements' => 'nullable|integer|min:0',
        ]);

        $block->update([
            'name'            => $validated['name'],
            'code'            => $validated['code'] ?? null,
            'status'          => $validated['status'] ?? 'active',
            'manager_name'    => $validated['manager_name'] ?? null,
            'manager_contact' => $validated['manager_contact'] ?? null,
            'description'     => $validated['description'] ?? null,
            'total_floors'    => $validated['total_floors'] ?? null,
            'total_basements' => $validated['total_basements'] ?? null,
        ]);

        return redirect()
            ->route('admin.blocks.index')
            ->with('success', 'Tòa nhà đã được cập nhật thành công.');
    }

    /**
     * Xóa tòa nhà — chỉ cho phép khi không còn tầng
     */
    public function destroy(Block $block): RedirectResponse
    {
        if ($block->floors()->exists()) {
            return redirect()
                ->route('admin.blocks.index')
                ->with('error', 'Không thể xóa tòa nhà đang có tầng. Hãy xóa tất cả tầng trước.');
        }

        $block->delete();

        return redirect()
            ->route('admin.blocks.index')
            ->with('success', 'Tòa nhà đã được xóa thành công.');
    }
}