<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApartmentType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ApartmentTypeController extends Controller
{
    /**
     * Danh sách loại căn hộ
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $apartmentTypes = ApartmentType::query()
            ->withCount('apartments')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('bedroom_count')
            ->orderBy('bathroom_count')
            ->orderBy('base_service_fee')
            ->paginate(10)
            ->withQueryString();

        return view('admin.apartment-types.index', compact('apartmentTypes', 'search'));
    }

    /**
     * Form tạo loại căn hộ
     */
    public function create(): View
    {
        return view('admin.apartment-types.create');
    }

    /**
     * Lưu loại căn hộ mới
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:50|unique:apartment_types,name',
            'description'      => 'nullable|string',
            'base_service_fee' => 'required|numeric|min:0',
            'bedroom_count'    => 'required|integer|min:0|max:10',
            'bathroom_count'   => 'required|integer|min:0|max:10',
        ]);

        ApartmentType::create($validated);

        return redirect()
            ->route('admin.apartment-types.index')
            ->with('success', 'Loại căn hộ đã được tạo thành công.');
    }

    /**
     * Form sửa loại căn hộ
     */
    public function edit(ApartmentType $apartmentType): View
    {
        return view('admin.apartment-types.edit', compact('apartmentType'));
    }

    /**
     * Cập nhật loại căn hộ
     */
    public function update(Request $request, ApartmentType $apartmentType): RedirectResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:50|unique:apartment_types,name,' . $apartmentType->id,
            'description'      => 'nullable|string',
            'base_service_fee' => 'required|numeric|min:0',
            'bedroom_count'    => 'required|integer|min:0|max:10',
            'bathroom_count'   => 'required|integer|min:0|max:10',
        ]);

        $apartmentType->update($validated);

        return redirect()
            ->route('admin.apartment-types.index')
            ->with('success', 'Loại căn hộ đã được cập nhật thành công.');
    }

    /**
     * Xóa loại căn hộ
     */
    public function destroy(ApartmentType $apartmentType): RedirectResponse
    {
        if ($apartmentType->apartments()->exists()) {
            return redirect()
                ->route('admin.apartment-types.index')
                ->with('error', 'Không thể xóa loại căn hộ này vì đang có căn hộ thuộc loại này.');
        }

        $apartmentType->delete();

        return redirect()
            ->route('admin.apartment-types.index')
            ->with('success', 'Loại căn hộ đã được xóa thành công.');
    }
}
