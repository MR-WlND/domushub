<?php

namespace App\Http\Controllers;

use App\Models\ServicePrice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServicePriceController extends Controller
{
    /**
     * Danh sách cấu hình giá dịch vụ
     */
    public function index(): View
    {
        $servicePrices = ServicePrice::orderBy('type')->orderByDesc('status')->get();

        return view('admin.service-prices.index', compact('servicePrices'));
    }

    /**
     * Thêm đơn giá mới
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'type'        => 'required|in:electricity,water,parking,management_fee,internet,service,other',
            'unit_price'  => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required'       => 'Vui lòng nhập tên dịch vụ.',
            'type.required'       => 'Vui lòng chọn loại dịch vụ.',
            'unit_price.required' => 'Vui lòng nhập đơn giá.',
            'unit_price.min'      => 'Đơn giá phải lớn hơn hoặc bằng 0.',
        ]);

        $validated['status'] = 'active';

        ServicePrice::create($validated);

        return redirect()->route('admin.service-prices.index')
            ->with('success', 'Đã thêm đơn giá dịch vụ thành công.');
    }

    /**
     * Cập nhật đơn giá
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $servicePrice = ServicePrice::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'type'        => 'required|in:electricity,water,parking,management_fee,internet,service,other',
            'unit_price'  => 'required|numeric|min:0',
            'status'      => 'required|in:active,pending,banned',
            'description' => 'nullable|string|max:500',
        ]);

        $servicePrice->update($validated);

        return redirect()->route('admin.service-prices.index')
            ->with('success', 'Đã cập nhật đơn giá thành công.');
    }

    /**
     * Xoá đơn giá
     */
    public function destroy($id): RedirectResponse
    {
        $servicePrice = ServicePrice::findOrFail($id);

        // Kiểm tra nếu đã có hoá đơn sử dụng đơn giá này
        if ($servicePrice->invoiceDetails()->exists()) {
            return redirect()->route('admin.service-prices.index')
                ->with('error', 'Không thể xoá đơn giá đang được sử dụng trong hoá đơn.');
        }

        $servicePrice->delete();

        return redirect()->route('admin.service-prices.index')
            ->with('success', 'Đã xoá đơn giá thành công.');
    }
}
