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
            'block_id'      => 'required|exists:blocks,id',
            'floor_number'  => 'required|integer|min:0',

            'name'          => 'nullable|string|max:100',
            'status'        => 'required|in:active,maintenance,inactive',
            'description'   => 'nullable|string',
        ]);

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
            'block_id'      => 'required|exists:blocks,id',
            'floor_number'  => 'required|integer|min:0',

            'name'          => 'nullable|string|max:100',
            'status'        => 'required|in:active,maintenance,inactive',
            'description'   => 'nullable|string',
        ]);

        $floor->update($validated);

        return redirect()
            ->route('admin.floors.index', [
                'block_id' => $validated['block_id']
            ])
            ->with('success', 'Tầng đã được cập nhật thành công.');
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