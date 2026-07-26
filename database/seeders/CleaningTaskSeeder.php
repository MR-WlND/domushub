<?php

namespace Database\Seeders;

use App\Models\CleaningTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CleaningTaskSeeder extends Seeder
{
    public function run(): void
    {
        $cleaner = User::where('role', 'cleaning')->first();
        if (!$cleaner) return;

        $today = Carbon::today();

        $tasks = [
            [
                'title' => 'Vệ sinh thang máy A1, A2',
                'description' => 'Lau sàn, gương và khử trùng nút bấm thang máy block A.',
                'area' => 'Block A - Thang máy',
                'area_group' => 'Tầng trệt & Thang máy',
                'start_time' => '06:30', 'end_time' => '07:30',
                'priority' => 'high', 'status' => 'done',
                'completed_at' => $today->copy()->setTime(7, 25),
                'checklist' => [
                    ['text' => 'Lau sàn thang máy', 'done' => true],
                    ['text' => 'Lau gương', 'done' => true],
                    ['text' => 'Khử trùng nút bấm', 'done' => true],
                    ['text' => 'Kiểm tra mùi', 'done' => true],
                ],
            ],
            [
                'title' => 'Vệ sinh sâu sảnh chính',
                'description' => 'Làm sạch sàn, cửa kính mặt tiền và khử trùng bề mặt tiếp xúc cao.',
                'area' => 'Tầng trệt - Cửa chính',
                'area_group' => 'Tầng trệt & Thang máy',
                'start_time' => '08:00', 'end_time' => '09:30',
                'priority' => 'high', 'status' => 'progress',
                'checklist' => [
                    ['text' => 'Quét sàn', 'done' => true],
                    ['text' => 'Lau sàn', 'done' => true],
                    ['text' => 'Đổ rác', 'done' => false],
                    ['text' => 'Lau cửa kính', 'done' => false],
                    ['text' => 'Khử trùng tay vịn', 'done' => false],
                ],
                'manager_note' => 'Hôm nay có đoàn khách VIP tham quan lúc 10:00. Ưu tiên vệ sinh sảnh chính và thang máy trước 09:30. Đảm bảo khu vực hồ bơi sạch sẽ trước 14:00.',
            ],
            [
                'title' => 'Tưới cây cảnh hành lang',
                'description' => 'Tưới nước và kiểm tra cây xanh hành lang tầng 1-3.',
                'area' => 'Tầng 1-3 - Hành lang',
                'area_group' => 'Tầng 1 – 3',
                'start_time' => '06:30', 'end_time' => '07:00',
                'priority' => 'low', 'status' => 'done',
                'completed_at' => $today->copy()->setTime(6, 55),
                'checklist' => [
                    ['text' => 'Tưới cây', 'done' => true],
                    ['text' => 'Kiểm tra lá vàng', 'done' => true],
                ],
            ],
            [
                'title' => 'Dọn dẹp khu vực hồ bơi',
                'description' => 'Thu dọn ghế, lau sàn ướt xung quanh hồ bơi và kiểm tra thùng rác.',
                'area' => 'Tầng thượng - Hồ bơi',
                'area_group' => 'Tầng thượng',
                'start_time' => '07:00', 'end_time' => '08:00',
                'priority' => 'medium', 'status' => 'done',
                'completed_at' => $today->copy()->setTime(7, 50),
                'checklist' => [
                    ['text' => 'Thu dọn ghế', 'done' => true],
                    ['text' => 'Lau sàn ướt', 'done' => true],
                    ['text' => 'Kiểm tra thùng rác', 'done' => true],
                ],
            ],
            [
                'title' => 'Lau kính cánh đông',
                'description' => 'Vệ sinh bề mặt kính bên ngoài tầng 4 khu vực cánh đông.',
                'area' => 'Tầng 4 - Cánh đông',
                'area_group' => 'Tầng 4 – 5',
                'start_time' => '09:30', 'end_time' => '11:00',
                'priority' => 'medium', 'status' => 'progress',
                'checklist' => [
                    ['text' => 'Chuẩn bị dung dịch', 'done' => true],
                    ['text' => 'Lau kính ngoài', 'done' => false],
                    ['text' => 'Kiểm tra vết ố', 'done' => false],
                ],
            ],
            [
                'title' => 'Thu gom rác văn phòng',
                'description' => 'Thu gom rác tại khu vực văn phòng điều hành tầng 5.',
                'area' => 'Tầng 5 - Văn phòng',
                'area_group' => 'Tầng 4 – 5',
                'start_time' => '11:00', 'end_time' => '12:00',
                'priority' => 'medium', 'status' => 'pending',
                'checklist' => [
                    ['text' => 'Thu gom rác', 'done' => false],
                    ['text' => 'Thay túi rác', 'done' => false],
                    ['text' => 'Vệ sinh thùng', 'done' => false],
                ],
            ],
            [
                'title' => 'Kiểm tra hệ thống đèn',
                'description' => 'Kiểm tra bóng đèn hỏng hành lang khu vực C tầng 2.',
                'area' => 'Tầng 2 - Hành lang C',
                'area_group' => 'Tầng 1 – 3',
                'start_time' => '13:00', 'end_time' => '14:00',
                'priority' => 'low', 'status' => 'pending',
                'checklist' => [
                    ['text' => 'Kiểm tra đèn hành lang', 'done' => false],
                    ['text' => 'Kiểm tra đèn cầu thang', 'done' => false],
                    ['text' => 'Ghi nhận bóng hỏng', 'done' => false],
                    ['text' => 'Báo cáo kỹ thuật', 'done' => false],
                ],
            ],
        ];

        foreach ($tasks as $task) {
            CleaningTask::create(array_merge($task, [
                'assigned_to' => $cleaner->id,
                'assigned_by' => User::where('role', 'admin')->first()?->id,
                'task_date' => $today,
            ]));
        }
    }
}
