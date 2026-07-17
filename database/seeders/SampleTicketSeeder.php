<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\TicketProgress;
use App\Models\Resident;
use App\Models\User;
use Carbon\Carbon;

class SampleTicketSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Lấy danh sách cư dân có căn hộ
        $residents = Resident::with('user', 'apartment')->take(3)->get();

        if ($residents->isEmpty()) {
            $this->command->warn('Không có cư dân nào trong database. Hãy chạy ResidentSeeder trước.');
            return;
        }

        // Lấy handler (nhân viên kỹ thuật hoặc staff)
        $handler = User::where('role', 'technician')
            ->where('status', 'active')
            ->first();

        if (! $handler) {
            $this->command->warn('Khong co ky thuat vien active. Hay chay TechnicianSeeder truoc.');
            return;
        }

        $resident1 = $residents->get(0);
        $resident2 = $residents->get(1) ?? $resident1;
        $resident3 = $residents->get(2) ?? $resident1;

        $tickets = [
            // 1. Chờ xử lý - Ưu tiên cao
            [
                'data' => [
                    'apartment_id' => $resident1->apartment_id,
                    'sender_id'    => $resident1->user_id,
                    'title'        => 'Rò rỉ nước từ đường ống nhà vệ sinh',
                    'description'  => 'Đường ống dẫn nước vào bồn rửa mặt bị rò rỉ, nước chảy tràn ra sàn nhà vệ sinh gây trơn trượt rất nguy hiểm. Cần kỹ thuật hỗ trợ gấp để tránh hư hỏng sàn gỗ.',
                    'priority'     => 'high',
                    'status'       => 'pending',
                    'images'       => null,
                    'created_at'   => $now->copy()->subMinutes(45),
                    'updated_at'   => $now->copy()->subMinutes(45),
                ],
                'progress' => [],
            ],

            // 2. Khẩn cấp - Chờ xử lý
            [
                'data' => [
                    'apartment_id' => $resident2->apartment_id,
                    'sender_id'    => $resident2->user_id,
                    'title'        => 'Thang máy phát ra âm thanh lạ và rung mạnh',
                    'description'  => 'Thang máy số 2 khi di chuyển lên tầng cao phát ra tiếng cọc cạch khá to và rung nhẹ. Nhiều cư dân đã phản ánh tình trạng này từ sáng sớm. Rất mong ban quản lý kiểm tra độ an toàn ngay.',
                    'priority'     => 'urgent',
                    'status'       => 'pending',
                    'images'       => null,
                    'created_at'   => $now->copy()->subHours(2),
                    'updated_at'   => $now->copy()->subHours(2),
                ],
                'progress' => [],
            ],

            // 3. Đang xử lý - Đã phân công kỹ thuật
            [
                'data' => [
                    'apartment_id' => $resident1->apartment_id,
                    'sender_id'    => $resident1->user_id,
                    'handler_id'   => $handler?->id,
                    'title'        => 'Hỏng bóng đèn hành lang tầng 3',
                    'description'  => 'Bóng đèn hành lang trước cửa căn hộ bị nhấp nháy liên tục rồi tắt hẳn từ tối qua. Nhờ ban quản lý cử kỹ thuật thay thế giúp để đảm bảo an toàn.',
                    'priority'     => 'medium',
                    'status'       => 'in_progress',
                    'images'       => null,
                    'created_at'   => $now->copy()->subDays(2),
                    'updated_at'   => $now->copy()->subHours(5),
                ],
                'progress' => [
                    [
                        'status'     => 'assigned',
                        'comment'    => 'Đã tiếp nhận phản ánh và phân công kỹ thuật viên Nguyễn Văn Hùng xử lý.',
                        'created_at' => $now->copy()->subDays(2)->addHours(1),
                    ],
                    [
                        'status'     => 'in_progress',
                        'comment'    => 'Kỹ thuật viên đã kiểm tra, xác nhận cần thay bóng đèn LED 18W. Đang chờ linh kiện từ kho.',
                        'created_at' => $now->copy()->subHours(5),
                    ],
                ],
            ],

            // 4. Hoàn thành - Có đánh giá
            [
                'data' => [
                    'apartment_id'    => $resident3->apartment_id,
                    'sender_id'       => $resident3->user_id,
                    'handler_id'      => $handler?->id,
                    'title'           => 'Cửa sổ phòng ngủ bị kẹt không đóng được',
                    'description'     => 'Cửa sổ phòng ngủ chính bị cong khung, không thể đóng kín dẫn đến mưa hắt vào và tiếng ồn từ bên ngoài. Đã thử điều chỉnh nhưng không được.',
                    'priority'        => 'medium',
                    'status'          => 'completed',
                    'rating'          => 5,
                    'feedback_comment' => 'Kỹ thuật viên đến đúng hẹn, xử lý nhanh và gọn gàng. Cảm ơn ban quản lý!',
                    'images'          => null,
                    'created_at'      => $now->copy()->subDays(7),
                    'updated_at'      => $now->copy()->subDays(5),
                ],
                'progress' => [
                    [
                        'status'     => 'assigned',
                        'comment'    => 'Tiếp nhận phản ánh, lên lịch kiểm tra vào ngày hôm sau.',
                        'created_at' => $now->copy()->subDays(7)->addHours(2),
                    ],
                    [
                        'status'     => 'in_progress',
                        'comment'    => 'Kỹ thuật viên đã kiểm tra và điều chỉnh lại bản lề cửa sổ, bôi trơn thanh ray. Cửa đã đóng kín bình thường.',
                        'created_at' => $now->copy()->subDays(5)->addHours(10),
                    ],
                    [
                        'status'     => 'completed',
                        'comment'    => 'Xác nhận hoàn thành. Đã liên hệ cư dân kiểm tra và đồng ý nghiệm thu.',
                        'created_at' => $now->copy()->subDays(5)->addHours(11),
                    ],
                ],
            ],

            // 5. Trung bình - Chờ xử lý
            [
                'data' => [
                    'apartment_id' => $resident2->apartment_id,
                    'sender_id'    => $resident2->user_id,
                    'title'        => 'Hệ thống điều hòa không làm lạnh được',
                    'description'  => 'Điều hòa phòng khách bật lên có chạy nhưng không thổi hơi lạnh, chỉ thổi gió bình thường. Đã kiểm tra lại điều khiển và nguồn điện nhưng vẫn không cải thiện. Thời tiết nóng nực rất khó chịu.',
                    'priority'     => 'medium',
                    'status'       => 'pending',
                    'images'       => null,
                    'created_at'   => $now->copy()->subHours(6),
                    'updated_at'   => $now->copy()->subHours(6),
                ],
                'progress' => [],
            ],
        ];

        foreach ($tickets as $item) {
            $ticket = Ticket::create($item['data']);

            foreach ($item['progress'] as $prog) {
                TicketProgress::create([
                    'ticket_id'  => $ticket->id,
                    'updated_by' => $handler?->id ?? $ticket->sender_id,
                    'status'     => $prog['status'],
                    'comment'    => $prog['comment'],
                    'created_at' => $prog['created_at'],
                ]);
            }
        }

        $this->command->info('Đã tạo 5 phản ánh mẫu thành công!');
    }
}
