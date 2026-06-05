<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Block;
use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
        $selectedBlockId = $request->query('block_id');
        $blocks = Block::orderBy('name')->get();
        return view('admin.floors.create', compact('blocks', 'selectedBlockId'));
    }

    /**
     * Lưu tầng mới
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'block_id'     => 'required|exists:blocks,id',
            'floor_number' => 'required|integer',
            'name'         => 'nullable|string|max:50',
            'status'       => 'nullable|in:active,maintenance,inactive',
            'description'  => 'nullable|string',
        ]);

        // Kiểm tra tầng trùng trong tòa
        $exists = Floor::where('block_id', $validated['block_id'])
            ->where('floor_number', $validated['floor_number'])
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'floor_number' => 'Số tầng này đã tồn tại trong tòa nhà đã chọn.',
            ]);
        }

        $validated['status'] = $validated['status'] ?? 'active';

        Floor::create($validated);

        return redirect()
            ->route('admin.blocks.index', ['featured_block_id' => $validated['block_id']])
            ->with('success', 'Tầng đã được tạo thành công.');
    }

    /**
     * Chi tiết tầng kèm danh sách căn hộ
     */
    public function show(Floor $floor): View
    {
        $floor->load(['block', 'apartments']);

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

    /**
     * Form sửa tầng
     */
    public function edit(Floor $floor): View
    {
        $blocks = Block::orderBy('name')->get();
        return view('admin.floors.edit', compact('floor', 'blocks'));
    }

    /**
     * Cập nhật tầng
     */
    public function update(Request $request, Floor $floor): RedirectResponse
    {
        $validated = $request->validate([
            'block_id'     => 'required|exists:blocks,id',
            'floor_number' => 'required|integer',
            'name'         => 'nullable|string|max:50',
            'status'       => 'nullable|in:active,maintenance,inactive',
            'description'  => 'nullable|string',
        ]);

        // Kiểm tra trùng (bỏ qua chính nó)
        $exists = Floor::where('block_id', $validated['block_id'])
            ->where('floor_number', $validated['floor_number'])
            ->where('id', '!=', $floor->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'floor_number' => 'Số tầng này đã tồn tại trong tòa nhà đã chọn.',
            ]);
        }

        $floor->update($validated);

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