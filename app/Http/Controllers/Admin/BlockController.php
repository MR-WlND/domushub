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
            ->withCount(['apartments as occupied_apartments_count' => function ($query) {
                $query->where('apartments.status', 'occupied');
            }])
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
            'name'                 => 'required|string|max:50|unique:blocks,name',
            'code'                 => 'required|string|max:100|unique:blocks,code',
            'status'               => 'nullable|in:active,inactive,maintenance',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'total_floors'         => 'required|integer|min:0',
            'total_basements'      => 'required|integer|min:0',
            'apartments_per_floor' => 'nullable|integer|min:0',
            'amenities'            => 'nullable|array',
            'amenities.*'          => 'string',
        ], [
            'name.required' => 'Vui lòng nhập tên tòa nhà.',
            'name.unique'   => 'Tên tòa nhà đã tồn tại.',
            'code.required' => 'Vui lòng nhập mã tòa nhà.',
            'code.unique'   => 'Mã tòa nhà đã tồn tại.',
            'total_floors.required' => 'Vui lòng nhập số tầng nổi.',
            'total_basements.required' => 'Vui lòng nhập số tầng hầm.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blocks', 'public');
        }

        Block::create([
            'name'                 => $validated['name'],
            'code'                 => $validated['code'] ?? null,
            'status'               => $validated['status'] ?? 'active',
            'image'                => $imagePath,
            'total_floors'         => $validated['total_floors'] ?? null,
            'total_basements'      => $validated['total_basements'] ?? null,
            'apartments_per_floor' => $validated['apartments_per_floor'] ?? null,
            'amenities'            => $validated['amenities'] ?? null,
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
            'name'                 => 'required|string|max:50|unique:blocks,name,' . $block->id,
            'code'                 => 'required|string|max:100|unique:blocks,code,' . $block->id,
            'status'               => 'nullable|in:active,inactive,maintenance',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'total_floors'         => 'required|integer|min:0',
            'total_basements'      => 'required|integer|min:0',
            'apartments_per_floor' => 'nullable|integer|min:0',
            'amenities'            => 'nullable|array',
            'amenities.*'          => 'string',
        ], [
            'name.required' => 'Vui lòng nhập tên tòa nhà.',
            'name.unique'   => 'Tên tòa nhà đã tồn tại.',
            'code.required' => 'Vui lòng nhập mã tòa nhà.',
            'code.unique'   => 'Mã tòa nhà đã tồn tại.',
            'total_floors.required' => 'Vui lòng nhập số tầng nổi.',
            'total_basements.required' => 'Vui lòng nhập số tầng hầm.',
        ]);

        $imagePath = $block->image;
        if ($request->hasFile('image')) {
            if ($block->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($block->image);
            }
            $imagePath = $request->file('image')->store('blocks', 'public');
        }

        $block->update([
            'name'                 => $validated['name'],
            'code'                 => $validated['code'] ?? null,
            'status'               => $validated['status'] ?? 'active',
            'image'                => $imagePath,
            'total_floors'         => $validated['total_floors'] ?? null,
            'total_basements'      => $validated['total_basements'] ?? null,
            'apartments_per_floor' => $validated['apartments_per_floor'] ?? null,
            'amenities'            => $validated['amenities'] ?? null,
        ]);

        return redirect()
            ->route('admin.blocks.index')
            ->with('success', 'Tòa nhà đã được cập nhật thành công.');
    }

    /**
     * Xóa tòa nhà — hệ thống sẽ tự động xóa mềm các tầng và căn hộ bên trong
     */
    public function destroy(Block $block): RedirectResponse
    {
        $block->delete();

        return redirect()
            ->route('admin.blocks.index')
            ->with('success', 'Tòa nhà đã được xóa thành công.');
    }

    /**
     * Ma trận hiển thị trạng thái căn hộ toàn tòa nhà
     */
    public function matrix(Block $block): View
    {
        $floors = $block->floors()
            ->with(['apartments' => function($q) {
                $q->orderBy('apartment_number');
            }])
            ->orderBy('floor_number', 'desc')
            ->get();

        $stats = [
            'floors'      => $floors->count(),
            'apartments'  => $block->apartments()->count(),
            'vacant'      => $block->apartments()->where('apartments.status', 'vacant')->count(),
            'occupied'    => $block->apartments()->where('apartments.status', 'occupied')->count(),
            'maintenance' => $block->apartments()->where('apartments.status', 'maintenance')->count(),
        ];

        return view('admin.blocks.matrix', compact('block', 'floors', 'stats'));
    }
}