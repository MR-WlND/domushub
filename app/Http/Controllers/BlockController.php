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
    $search = $request->get('search');
    $status = $request->get('status');

    $query = Block::query()
        ->withCount('floors');

    // Search
    if (!empty($search)) {
        $query->where('name', 'like', "%{$search}%");
    }

    // Filter status
    if (!empty($status)) {
        $query->where('status', $status);
    }

    $blocks = $query
        ->latest()
        ->paginate(12)
        ->withQueryString();

    // Stats
    $totalBlocks = Block::count();

    $activeBlocks = Block::where(
        'status',
        'active'
    )->count();

    $maintenanceBlocks = Block::where(
        'status',
        'maintenance'
    )->count();

    $inactiveBlocks = Block::where(
        'status',
        'inactive'
    )->count();

    return view('admin.blocks.index', compact(
        'blocks',
        'search',
        'status',
        'totalBlocks',
        'activeBlocks',
        'maintenanceBlocks',
        'inactiveBlocks'
    ));
}

    /**
     * Form tạo
     */
    public function create(): View
    {
        return view(
            'admin.blocks.create'
        );
    }

    /**
     * Lưu Toà nhà
     */
    public function store(
        Request $request
    ): RedirectResponse {

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

        ]);

        Block::create([

            'name' => trim(
                $validated['name']
            ),

            'description' =>
            $validated['description'] ?? null,

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
    public function edit(
        Block $block
    ): View {

        return view(
            'admin.blocks.edit',
            compact('block')
        );
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

        ]);

        $block->update([

            'name' => trim(
                $validated['name']
            ),

            'description' =>
            $validated['description'] ?? null,

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
    public function destroy(
        Block $block
    ): RedirectResponse {

        /**
         * Không cho xóa nếu còn tầng
         */
        if ($block->floors()->exists()) {

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