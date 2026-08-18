<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Announcement;
use App\Models\User;
use Carbon\Carbon;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Try to find an admin user to be the author
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin ? $admin->id : 1;

        $announcements = [
            [
                'user_id' => $adminId,
                'title' => 'Lễ hội Trung Thu: Gắn kết yêu thương',
                'content' => 'Kính mời toàn thể Quý cư dân tham gia đêm hội trăng rằm tại sảnh chính tòa nhà. Chương trình có các tiết mục múa lân, rước đèn ông sao và phá cỗ dành cho các bé. Sự kiện hứa hẹn mang lại không khí đầm ấm và gắn kết cộng đồng.',
                'category' => 'event',
                'status' => 'published',
                'pinned' => true,
                'is_popup' => false,
                'image_path' => null,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => $adminId,
                'title' => 'Bảo trì thang máy Tòa B - Đợt định kỳ tháng',
                'content' => 'Ban quản lý xin thông báo lịch bảo trì định kỳ toàn bộ hệ thống thang máy tại Tòa B. Trong thời gian này, thang máy số 1 và số 2 sẽ tạm ngừng hoạt động từ 08:00 đến 12:00. Quý cư dân vui lòng sử dụng thang số 3 và số 4. Xin lỗi vì sự bất tiện này.',
                'category' => 'maintenance',
                'status' => 'published',
                'pinned' => false,
                'is_popup' => false,
                'image_path' => null,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $adminId,
                'title' => 'Họp mặt thảo luận ý tưởng cải tạo khu vui chơi trẻ em',
                'content' => 'Nhằm mục đích nâng cấp không gian sinh hoạt chung, Ban quản lý mong muốn nhận được ý kiến đóng góp từ Quý cư dân về việc cải tạo khu vui chơi trẻ em ở tầng 1. Hãy cùng xây dựng môi trường tốt nhất cho con em chúng ta.',
                'category' => 'event',
                'status' => 'published',
                'pinned' => false,
                'is_popup' => false,
                'image_path' => null,
                'created_at' => Carbon::now()->subHours(5),
                'updated_at' => Carbon::now()->subHours(5),
            ],
            [
                'user_id' => $adminId,
                'title' => 'Quy định mới về phân loại rác thải sinh hoạt',
                'content' => 'Để đảm bảo vệ sinh môi trường và tuân thủ quy định của thành phố, Ban quản lý đề nghị Quý cư dân thực hiện phân loại rác thải tại nguồn (rác hữu cơ, rác vô cơ và rác tái chế) trước khi đem bỏ vào thùng rác tập trung.',
                'category' => 'general',
                'status' => 'published',
                'pinned' => false,
                'is_popup' => false,
                'image_path' => null,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'user_id' => $adminId,
                'title' => 'Thông báo diễn tập phòng cháy chữa cháy thường niên',
                'content' => 'Ban quản lý phối hợp cùng cơ quan PCCC khu vực sẽ tổ chức buổi diễn tập phòng cháy chữa cháy vào cuối tuần này. Chuông báo cháy sẽ reo thử nghiệm. Kính mong Quý cư dân không hoảng loạn và tham gia diễn tập theo sự hướng dẫn của nhân viên tòa nhà.',
                'category' => 'warning',
                'status' => 'published',
                'pinned' => true,
                'is_popup' => true,
                'image_path' => null,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }
    }
}
