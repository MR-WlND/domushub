<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BlockController extends Controller
{
    /**
     * Danh sách Toà nhà
     */
    public function index(Request $request): View
    {
        $search = $request->search;

        $blocks = Block::query()
            ->withCount('floors')
            ->when($search, function ($query) use ($search) {

                $query->where('name', 'like', "%{$search}%");

            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.blocks.index', compact(
            'blocks',
            'search'
        ));
    }

    /**
     * Form tạo
     */
    public function create(): View
    {
        return view('admin.blocks.create');
    }

    /**
     * Lưu Toà nhà
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:50',
                'unique:blocks,name',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'nullable',
                'in:active,inactive,maintenance',
            ],

        ]);

        Block::create([

            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'active',

        ]);

        return redirect()
            ->route('admin.blocks.index')
            ->with(
                'success',
                'Toà nhà đã được tạo thành công.'
            );
    }

    /**
     * Form sửa
     */
    public function edit(Block $block): View
    {
        return view('admin.blocks.edit', compact('block'));
    }

    /**
     * Cập nhật
     */
    public function update(
        Request $request,
        Block $block
    ): RedirectResponse {

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:50',
                'unique:blocks,name,' . $block->id,
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'nullable',
                'in:active,inactive,maintenance',
            ],

        ]);

        $block->update([

            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'active',

        ]);

        return redirect()
            ->route('admin.blocks.index')
            ->with(
                'success',
                'Toà nhà đã được cập nhật thành công.'
            );
    }

    /**
     * Xóa Toà nhà
     */
    public function destroy(Block $block): RedirectResponse
    {
        if ($block->floors()->count() > 0) {

            return redirect()
                ->route('admin.blocks.index')
                ->with(
                    'error',
                    'Không thể xóa toà nhà đang có tầng.'
                );
        }

        DB::transaction(function () use ($block) {

            $block->delete();

        });

        return redirect()
            ->route('admin.blocks.index')
            ->with(
                'success',
                'Toà nhà đã được xóa thành công.'
            );
    }
}