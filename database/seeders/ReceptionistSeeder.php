<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReceptionistSeeder extends Seeder
{
    /**
     * Tạo tài khoản lễ tân mẫu để test.
     * Chạy: php artisan db:seed --class=ReceptionistSeeder
     */
    public function run(): void
    {
        $receptionists = [
            [
                'name'   => 'Nguyễn Thị Lan',
                'email'  => 'letan01@domushub.vn',
                'phone'  => '0901111091',
                'role'   => 'receptionist',
                'status' => 'active',
            ],
            [
                'name'   => 'Trần Văn Minh',
                'email'  => 'letan02@domushub.vn',
                'phone'  => '0901111092',
                'role'   => 'receptionist',
                'status' => 'active',
            ],
        ];

        foreach ($receptionists as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'password' => Hash::make('Chungcu@2026'),
                ])
            );
        }

        $this->command->info('✅ Đã tạo ' . count($receptionists) . ' tài khoản lễ tân.');
        $this->command->info('   Mật khẩu mặc định: Chungcu@2026');
        $this->command->info('   Đăng nhập tại: /receptionist/login');
    }
}
