<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Resident;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResidentManageController extends Controller
{
    /**
     * Xóa mềm cư dân khỏi phòng (Admin thực hiện).
     * Kích hoạt SoftDeletes trên bảng residents.
     * Apartment model tự động cập nhật trạng thái thông qua Resident boot event.
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $resident = Resident::findOrFail($id);
        $apartmentId = $resident->apartment_id;

        $resident->delete(); // SoftDeletes → cột deleted_at được set

        return redirect()
            ->route('admin.apartments.show', $apartmentId)
            ->with('success', 'Đã gỡ cư dân khỏi phòng thành công. Lịch sử vẫn được giữ lại trong hệ thống.');
    }
}
