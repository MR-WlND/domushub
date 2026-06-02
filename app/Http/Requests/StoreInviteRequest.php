<?php

namespace App\Http\Requests;

use App\Models\Resident;
use Illuminate\Foundation\Http\FormRequest;

class StoreInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Kiểm tra user đang đăng nhập có phải owner của apartment_id hay không
        $apartmentId = $this->input('apartment_id');

        return Resident::where('user_id', $this->user()->id)
            ->where('apartment_id', $apartmentId)
            ->where('relationship', 'owner')
            ->whereNull('deleted_at')
            ->exists();
    }

    public function rules(): array
    {
        return [
            'apartment_id' => 'required|exists:apartments,id',
            'intended_relationship' => 'required|in:family_member,tenant',
            'expired_at' => 'nullable|date|after:now',
        ];
    }

    public function messages(): array
    {
        return [
            'apartment_id.required' => 'Vui lòng chọn căn hộ.',
            'apartment_id.exists' => 'Căn hộ không tồn tại.',
            'intended_relationship.required' => 'Vui lòng chọn vai trò cho người được mời.',
            'intended_relationship.in' => 'Vai trò không hợp lệ. Chỉ chấp nhận: Thành viên gia đình hoặc Người thuê.',
            'expired_at.date' => 'Ngày hết hạn không đúng định dạng.',
            'expired_at.after' => 'Ngày hết hạn phải sau thời điểm hiện tại.',
        ];
    }

    protected function failedAuthorization()
    {
        abort(403, 'Bạn không phải chủ hộ của căn hộ này. Không có quyền tạo mã mời.');
    }
}
