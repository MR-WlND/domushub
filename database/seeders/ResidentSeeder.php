<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;   // Gọi thư viện DB (Query Builder) để chèn dữ liệu trực tiếp vào bảng
use Illuminate\Support\Facades\Hash; // Gọi thư viện Hash để mã hóa mật khẩu
use Carbon\Carbon;                   // Gọi thư viện Carbon để xử lý thời gian

class ResidentSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy chính xác thời gian tại thời điểm chạy lệnh (VD: 2026-05-22 15:42:57)
        $now = Carbon::now();

        $users = [
            [
                'name' => 'Cư Dân A',
                'email' => 'resident.a@example.com',
                'phone' => '0900000004',
                // Mật khẩu bắt buộc phải mã hóa bằng Hash::make() thì user mới đăng nhập được
                'password' => Hash::make('password123'),
                // Sử dụng đúng các giá trị Enum mà bạn đã thiết kế trong database
                'role' => 'resident',
                'status' => 'active',
                // Cấp ngày giờ hiện tại cho các mốc thời gian để tài khoản hợp lệ ngay lập tức
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Cư Dân B',
                'email' => 'resident.b@example.com',
                'phone' => '0900000005',
                'password' => Hash::make('password123'),
                'role' => 'resident',
                'status' => 'active',
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        foreach ($users as $user) {
            if (!DB::table('users')->where('email', $user['email'])->exists()) {
                DB::table('users')->insert($user);
            }
        }
    }
}
