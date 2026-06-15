<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Gọi thư viện Hash để mã hóa mật khẩu
use Carbon\Carbon;                   // Gọi thư viện Carbon để xử lý thời gian
use App\Models\User;                // Sử dụng model User để tạo hoặc cập nhật tài khoản

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Quản Trị Viên',
                'phone' => '0900000001',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => $now,
            ]
        );
    }
}