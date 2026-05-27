<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FloorController extends Controller
{
    public function index(Request $request): View
    {
        $blockId = $request->query('block_id');
        $block = null;

        if ($blockId) {
            $block = Block::findOrFail($blockId);

            $floors = $block->floors()
                ->withCount('apartments')
                ->paginate(15);
        } else {
            $floors = Floor::with('block')
                ->withCount('apartments')
                ->paginate(15);
        }

        $blocks = Block::all();

        return view('admin.floors.index', compact(
            'floors',
            'blocks',
            'block'
        ));
    }

    public function create(): View
    {
        $blocks = Block::all();

        return view('admin.floors.create', compact('blocks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'block_id'            => 'required|exists:blocks,id',
            'floor_number'        => 'nullable|integer|min:0',
            'expected_apartments' => 'nullable|integer|min:0',
            'floor_type'          => 'required|in:resident,basement,commercial,service',
            'name'                => 'nullable|string|max:100',
            'status'              => 'nullable|in:active,maintenance,inactive',
            'description'         => 'nullable|string',
        ]);

        $validated['status'] = $validated['status'] ?? 'active';
        $validated['floor_number'] = $validated['floor_number'] ?? $this->resolveFloorNumber($validated['name'] ?? null, $validated['block_id']);

        Floor::create($validated);

        return redirect()
            ->route('admin.floors.index', [
                'block_id' => $validated['block_id']
            ])
            ->with('success', 'Tầng đã được tạo thành công.');
    }

    public function edit(Floor $floor): View
    {
        $blocks = Block::all();

        return view('admin.floors.edit', compact(
            'floor',
            'blocks'
        ));
    }

    public function update(
        Request $request,
        Floor $floor
    ): RedirectResponse {

        $validated = $request->validate([
            'block_id'            => 'required|exists:blocks,id',
            'floor_number'        => 'nullable|integer|min:0',
            'expected_apartments' => 'nullable|integer|min:0',
            'floor_type'          => 'required|in:resident,basement,commercial,service',
            'name'                => 'nullable|string|max:100',
            'status'              => 'nullable|in:active,maintenance,inactive',
            'description'         => 'nullable|string',
        ]);

        $validated['floor_number'] = $validated['floor_number'] ?? $floor->floor_number;
        $validated['status'] = $validated['status'] ?? $floor->status;

        $floor->update($validated);

        return redirect()
            ->route('admin.floors.index', [
                'block_id' => $validated['block_id']
            ])
            ->with('success', 'Tầng đã được cập nhật thành công.');
    }

    private function resolveFloorNumber(?string $name, int $blockId): int
    {
        if ($name) {
            if (preg_match('/(-?\d+)/', $name, $matches)) {
                $candidate = (int) $matches[1];
                if ($candidate !== 0) {
                    return $candidate;
                }
            }
        }

        $max = Floor::where('block_id', $blockId)->max('floor_number');

        return is_numeric($max) ? $max + 1 : 1;
    }

    public function destroy(Floor $floor): RedirectResponse
    {
        $blockId = $floor->block_id;

        $floor->delete();

        return redirect()
            ->route('admin.floors.index', [
                'block_id' => $blockId
            ])
            ->with('success', 'Tầng đã được xóa thành công.');
    }
}