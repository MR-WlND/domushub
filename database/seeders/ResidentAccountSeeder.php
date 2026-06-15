<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * Seed tài khoản cư dân mẫu để test.
 *
 * Mỗi cư dân sẽ được gán vào một căn hộ có status = 'occupied'.
 * Nếu chưa có căn hộ occupied nào, seeder vẫn chạy được (chỉ tạo user, không gán apartment).
 *
 * Tài khoản đăng nhập (tất cả dùng chung mật khẩu):
 *   Email : resident01@domus.vn ... resident10@domus.vn
 *   Pass  : password123
 */
class ResidentAccountSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Lấy danh sách các căn hộ đang có người ở (occupied)
        $occupiedApartments = DB::table('apartments')
            ->where('status', 'occupied')
            ->pluck('id')
            ->toArray();

        $residents = [
            ['name' => 'Nguyễn Văn An',    'email' => 'resident01@domus.vn', 'phone' => '0901000001', 'cccd' => '001234567801'],
            ['name' => 'Trần Thị Bích',     'email' => 'resident02@domus.vn', 'phone' => '0901000002', 'cccd' => '001234567802'],
            ['name' => 'Lê Hoàng Cường',    'email' => 'resident03@domus.vn', 'phone' => '0901000003', 'cccd' => '001234567803'],
            ['name' => 'Phạm Thị Dung',     'email' => 'resident04@domus.vn', 'phone' => '0901000004', 'cccd' => '001234567804'],
            ['name' => 'Hoàng Minh Đức',    'email' => 'resident05@domus.vn', 'phone' => '0901000005', 'cccd' => '001234567805'],
            ['name' => 'Vũ Thị Lan',        'email' => 'resident06@domus.vn', 'phone' => '0901000006', 'cccd' => '001234567806'],
            ['name' => 'Đặng Quốc Hùng',    'email' => 'resident07@domus.vn', 'phone' => '0901000007', 'cccd' => '001234567807'],
            ['name' => 'Bùi Thị Mai',       'email' => 'resident08@domus.vn', 'phone' => '0901000008', 'cccd' => '001234567808'],
            ['name' => 'Ngô Thanh Tùng',    'email' => 'resident09@domus.vn', 'phone' => '0901000009', 'cccd' => '001234567809'],
            ['name' => 'Đinh Thị Hoa',      'email' => 'resident10@domus.vn', 'phone' => '0901000010', 'cccd' => '001234567810'],
        ];

        foreach ($residents as $index => $data) {
            // Bỏ qua nếu email đã tồn tại
            if (DB::table('users')->where('email', $data['email'])->exists()) {
                continue;
            }

            // Gán apartment nếu có (theo thứ tự, mỗi cư dân 1 căn)
            $apartmentId = $occupiedApartments[$index] ?? null;

            $userId = DB::table('users')->insertGetId([
                'name'              => $data['name'],
                'email'             => $data['email'],
                'phone'             => $data['phone'],
                'cccd'              => $data['cccd'],
                'password'          => Hash::make('password123'),
                'role'              => 'resident',
                'status'            => 'active',
                'apartment_id'      => $apartmentId,
                'email_verified_at' => $now,
                'created_at'        => $now,
                'updated_at'        => $now,
            ]);

            // Tạo bản ghi trong bảng residents nếu được gán apartment
            if ($apartmentId) {
                $alreadyResident = DB::table('residents')
                    ->where('user_id', $userId)
                    ->where('apartment_id', $apartmentId)
                    ->exists();

                if (!$alreadyResident) {
                    DB::table('residents')->insert([
                        'user_id'          => $userId,
                        'apartment_id'     => $apartmentId,
                        'relationship'     => 'owner',
                        'temporary_status' => 'permanent',
                        'start_date'       => $now->toDateString(),
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                }
            }
        }
    }
}
