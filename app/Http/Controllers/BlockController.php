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
            ->withCount('apartments')
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

            'code' => [
                'nullable',
                'string',
                'max:100',
                'unique:blocks,code',
            ],

            'status' => [
                'nullable',
                'in:active,inactive,maintenance',
            ],

            'number_of_floors' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'total_apartments' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'manager_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'manager_contact' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

        ]);

        Block::create([

            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'number_of_floors' => $validated['number_of_floors'] ?? null,
            'total_apartments' => $validated['total_apartments'] ?? null,
            'manager_name' => $validated['manager_name'] ?? null,
            'manager_contact' => $validated['manager_contact'] ?? null,
            'description' => $validated['description'] ?? null,

        ]);

        return redirect()
            ->route('admin.blocks.index')
            ->with(
                'success',
                'Toà nhà đã được tạo thành công.'
            );
    }

    /**
     * Chi tiết Toà nhà
     */
    public function show(Block $block): View
    {
        $floors = $block->floors()
            ->withCount('apartments')
            ->orderBy('floor_number')
            ->get();

        $stats = [
            'floors' => $floors->count(),
            'apartments' => $block->apartments()->count(),
            'vacant' => $block->apartments()->where('apartments.status', 'vacant')->count(),
            'occupied' => $block->apartments()->where('apartments.status', 'occupied')->count(),
            'maintenance' => $block->apartments()->where('apartments.status', 'maintenance')->count(),
        ];

        return view('admin.blocks.show', compact(
            'block',
            'floors',
            'stats'
        ));
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

            'code' => [
                'nullable',
                'string',
                'max:100',
                'unique:blocks,code,' . $block->id,
            ],

            'status' => [
                'nullable',
                'in:active,inactive,maintenance',
            ],

            'number_of_floors' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'total_apartments' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'manager_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            'manager_contact' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],

        ]);

        $block->update([

            'name' => $validated['name'],
            'code' => $validated['code'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'number_of_floors' => $validated['number_of_floors'] ?? null,
            'total_apartments' => $validated['total_apartments'] ?? null,
            'manager_name' => $validated['manager_name'] ?? null,
            'manager_contact' => $validated['manager_contact'] ?? null,
            'description' => $validated['description'] ?? null,

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