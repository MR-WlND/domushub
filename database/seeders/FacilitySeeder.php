<?php
 
 namespace Database\Seeders;
 
 use App\Models\Facility;
 use Illuminate\Database\Seeder;
 
 class FacilitySeeder extends Seeder
 {
     public function run(): void
     {
         $facilities = [
             [
                 'name'           => 'Hồ bơi',
                 'capacity'       => 30,
                 'description'    => 'Hồ bơi ngoài trời tầng 5, view đẹp, thoáng mát.',
                 'status'         => 'available',
                 'open_time'      => '06:00',
                 'close_time'     => '20:00',
                 'slot_duration'  => 60,
                 'price_per_slot' => 20000,
                 'rules'          => "Cư dân bắt buộc mặc trang phục bơi.\nTrẻ em dưới 12 tuổi phải có người lớn đi kèm.",
             ],
             [
                 'name'           => 'Phòng Gym',
                 'capacity'       => 20,
                 'description'    => 'Phòng tập thể dục đầy đủ thiết bị hiện đại, máy chạy bộ, tạ.',
                 'status'         => 'available',
                 'open_time'      => '05:30',
                 'close_time'     => '22:00',
                 'slot_duration'  => 90,
                 'price_per_slot' => 0,
                 'rules'          => "Yêu cầu mang giày thể thao và dùng khăn lau cá nhân.\nThu dọn tạ sau khi tập xong.",
             ],
             [
                 'name'           => 'Sân BBQ',
                 'capacity'       => 50,
                 'description'    => 'Khu vực nướng BBQ ngoài trời tầng thượng, không gian ấm cúng.',
                 'status'         => 'available',
                 'open_time'      => '10:00',
                 'close_time'     => '22:00',
                 'slot_duration'  => 120,
                 'price_per_slot' => 100000,
                 'rules'          => "Không gây mất trật tự sau 22h.\nDọn dẹp rác sạch sẽ sau khi kết thúc buổi tiệc.",
             ],
             [
                 'name'           => 'Phòng sinh hoạt cộng đồng',
                 'capacity'       => 100,
                 'description'    => 'Phòng hội họp rộng rãi, đầy đủ bàn ghế, tổ chức sự kiện cư dân.',
                 'status'         => 'available',
                 'open_time'      => '08:00',
                 'close_time'     => '21:00',
                 'slot_duration'  => 120,
                 'price_per_slot' => 0,
                 'rules'          => "Đăng ký trước tối thiểu 3 ngày đối với các sự kiện lớn trên 30 người.\nGiữ gìn vệ sinh chung.",
             ],
             [
                 'name'           => 'Sân tennis',
                 'capacity'       => 4,
                 'description'    => 'Sân tennis tiêu chuẩn quốc tế, đèn chiếu sáng ban đêm.',
                 'status'         => 'maintenance',
                 'open_time'      => '06:00',
                 'close_time'     => '21:00',
                 'slot_duration'  => 60,
                 'price_per_slot' => 50000,
                 'rules'          => "Mặc trang phục thể thao và đi giày chuyên dụng sân tennis.\nKhông hút thuốc trong sân.",
             ],
             [
                 'name'           => 'Phòng đọc sách',
                 'capacity'       => 15,
                 'description'    => 'Thư viện mini yên tĩnh dành cho cư dân học tập và làm việc.',
                 'status'         => 'available',
                 'open_time'      => '08:00',
                 'close_time'     => '21:30',
                 'slot_duration'  => 60,
                 'price_per_slot' => 0,
                 'rules'          => "Giữ trật tự tuyệt đối.\nKhông mang đồ ăn uống có mùi vào thư viện.",
             ],
         ];
 
         foreach ($facilities as $data) {
             Facility::updateOrCreate(
                 ['name' => $data['name']],
                 $data
             );
         }
     }
 }

