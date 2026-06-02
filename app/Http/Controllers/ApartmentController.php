<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
}