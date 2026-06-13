<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\Resident;
use App\Models\User;
use Carbon\Carbon;

class SampleTicketSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Lấy 1 cư dân ngẫu nhiên có căn hộ để làm người gửi
        $resident = Resident::with('user', 'apartment')->first();

        if (!$resident) {
            // Nếu chưa có cư dân nào trong bảng residents, thử tìm user role resident và gán với căn hộ đầu tiên
            $user = User::where('role', 'resident')->first();
            $apartment = \App\Models\Apartment::first();
            if ($user && $apartment) {
                $resident = Resident::create([
                    'user_id' => $user->id,
                    'apartment_id' => $apartment->id,
                    'relationship' => 'owner',
                    'temporary_status' => 'permanent',
                    'start_date' => $now->toDateString(),
                ]);
            }
        }

        if ($resident) {
            $senderId = $resident->user_id;
            $apartmentId = $resident->apartment_id;

            // Tạo các phản ánh mẫu ở trạng thái chờ xử lý (pending)
            $tickets = [
                [
                    'apartment_id' => $apartmentId,
                    'sender_id' => $senderId,
                    'title' => 'Hỏng bóng đèn hành lang block A',
                    'description' => 'Bóng đèn hành lang trước cửa căn hộ bị nhấp nháy liên tục rồi tắt hẳn từ tối qua. Nhờ ban quản lý cử kỹ thuật thay thế giúp để đảm bảo an ninh.',
                    'priority' => 'medium',
                    'status' => 'pending',
                    'images' => null,
                    'created_at' => $now->copy()->subHours(2),
                    'updated_at' => $now->copy()->subHours(2),
                ],
                [
                    'apartment_id' => $apartmentId,
                    'sender_id' => $senderId,
                    'title' => 'Rò rỉ nước từ đường ống nhà vệ sinh',
                    'description' => 'Đường ống dẫn nước vào bồn rửa mặt bị rò rỉ nước, nước chảy tràn ra sàn nhà vệ sinh gây trơn trượt. Cần kỹ thuật hỗ trợ gấp.',
                    'priority' => 'high',
                    'status' => 'pending',
                    'images' => null,
                    'created_at' => $now->copy()->subMinutes(30),
                    'updated_at' => $now->copy()->subMinutes(30),
                ],
                [
                    'apartment_id' => $apartmentId,
                    'sender_id' => $senderId,
                    'title' => 'Thang máy di chuyển phát ra âm thanh lạ',
                    'description' => 'Thang máy số 2 của tòa nhà khi di chuyển lên tầng cao phát ra tiếng kêu cọc cạch khá to và rung nhẹ. Rất mong ban quản lý kiểm tra độ an toàn.',
                    'priority' => 'urgent',
                    'status' => 'pending',
                    'images' => null,
                    'created_at' => $now->copy()->subHours(1),
                    'updated_at' => $now->copy()->subHours(1),
                ]
            ];

            foreach ($tickets as $ticketData) {
                Ticket::create($ticketData);
            }
        }
    }
}
