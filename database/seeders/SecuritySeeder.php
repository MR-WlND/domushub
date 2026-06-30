<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;   // Gọi thư viện DB (Query Builder) để chèn dữ liệu trực tiếp vào bảng
use Illuminate\Support\Facades\Hash; // Gọi thư viện Hash để mã hóa mật khẩu
use Carbon\Carbon;                   // Gọi thư viện Carbon để xử lý thời gian

class SecuritySeeder extends Seeder
{
    public function run(): void
    {
        // Lấy chính xác thời gian tại thời điểm chạy lệnh (VD: 2026-05-22 15:42:57)
        $now = Carbon::now();

        $users = [
            [
                'name' => 'Bảo Vệ Cổng Chính',
                'email' => 'security.main@example.com',
                'phone' => '0900000002',
                // Mật khẩu bắt buộc phải mã hóa bằng Hash::make() thì user mới đăng nhập được
                'password' => Hash::make('password123'),
                // Sử dụng đúng các giá trị Enum mà bạn đã thiết kế trong database
                'role' => 'security',
                'status' => 'active',
                // Cấp ngày giờ hiện tại cho các mốc thời gian để tài khoản hợp lệ ngay lập tức
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Bảo Vệ Tầng Hầm',
                'email' => 'security.basement@example.com',
                'phone' => '0900000003',
                'password' => Hash::make('password123'),
                'role' => 'security',
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