<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seed cư dân theo số phòng — mỗi căn hộ tối đa 3 cư dân.
 *
 * Đồng bộ dữ liệu:
 *   1. Tạo user với role = 'resident', apartment_id = căn hộ được gán
 *   2. Tạo bản ghi trong bảng residents (quan hệ user ↔ apartment)
 *   3. Cập nhật apartment.status = 'occupied'
 *
 * Quy tắc gán:
 *   - Cư dân đầu tiên (index 0): relationship = 'owner', permanent
 *   - Cư dân thứ 2 (index 1): relationship = 'family_member', permanent
 *   - Cư dân thứ 3 (index 2): relationship = 'tenant', temporary
 *
 * Tài khoản mẫu: Mật khẩu chung = password123
 */
class ResidentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('password123');

        // Lấy tất cả căn hộ (trừ trạng thái maintenance)
        $apartments = DB::table('apartments')
            ->where('status', '!=', 'maintenance')
            ->get();

        if ($apartments->isEmpty()) {
            $this->command->warn('Không tìm thấy căn hộ nào. Hãy chạy BlockFloorApartmentSeeder trước.');
            return;
        }

        // Danh sách tên cư dân mẫu (Việt Nam)
        $firstNames = ['An', 'Bình', 'Cường', 'Dung', 'Đức', 'Giang', 'Hà', 'Hùng', 'Khánh', 'Lan', 'Minh', 'Ngọc', 'Phương', 'Quân', 'Sơn', 'Thảo', 'Trung', 'Uyên', 'Vân', 'Xuân'];
        $lastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Vũ', 'Đặng', 'Bùi', 'Ngô', 'Đinh'];

        $relationships = ['owner', 'family_member', 'tenant'];
        $temporaryStatuses = ['permanent', 'permanent', 'temporary'];

        $residentIndex = 0;

        foreach ($apartments as $apartment) {
            // Mỗi căn hộ sẽ có 1–3 cư dân (phân bố: phần lớn 2–3 người)
            $numResidents = $this->getResidentCount($apartment->id);

            for ($i = 0; $i < $numResidents; $i++) {
                $residentIndex++;

                $lastName = $lastNames[($residentIndex + $i) % count($lastNames)];
                $firstName = $firstNames[($residentIndex * 3 + $i) % count($firstNames)];
                $fullName = $lastName . ' ' . $firstName;

                $email = 'resident' . str_pad($residentIndex, 3, '0', STR_PAD_LEFT) . '@domus.vn';
                $phone = '0912' . str_pad($residentIndex, 6, '0', STR_PAD_LEFT);
                $cccd = '079' . str_pad($residentIndex, 9, '0', STR_PAD_LEFT);

                // Bỏ qua nếu email hoặc phone đã tồn tại
                if (DB::table('users')->where('email', $email)->orWhere('phone', $phone)->exists()) {
                    continue;
                }

                // 1. Tạo user
                $userId = DB::table('users')->insertGetId([
                    'name'              => $fullName,
                    'email'             => $email,
                    'phone'             => $phone,
                    'cccd'              => $cccd,
                    'password'          => $password,
                    'role'              => 'resident',
                    'status'            => 'active',
                    'apartment_id'      => $apartment->id,
                    'email_verified_at' => $now,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);

                // 2. Tạo bản ghi residents (liên kết user ↔ apartment)
                $alreadyResident = DB::table('residents')
                    ->where('user_id', $userId)
                    ->where('apartment_id', $apartment->id)
                    ->exists();

                if (!$alreadyResident) {
                    DB::table('residents')->insert([
                        'user_id'          => $userId,
                        'apartment_id'     => $apartment->id,
                        'relationship'     => $relationships[$i] ?? 'family_member',
                        'temporary_status' => $temporaryStatuses[$i] ?? 'permanent',
                        'start_date'       => $now->copy()->subMonths(rand(1, 12))->toDateString(),
                        'end_date'         => ($i === 2) ? $now->copy()->addMonths(6)->toDateString() : null,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                }
            }

            // 3. Cập nhật trạng thái apartment → occupied
            DB::table('apartments')
                ->where('id', $apartment->id)
                ->update([
                    'status'     => 'occupied',
                    'updated_at' => $now,
                ]);
        }

        $this->command->info("Đã tạo {$residentIndex} cư dân cho {$apartments->count()} căn hộ.");
    }

    /**
     * Xác định số cư dân cho mỗi căn hộ (1–3 người).
     * Phân bố: ~20% có 1 người, ~40% có 2 người, ~40% có 3 người.
     */
    private function getResidentCount(int $apartmentId): int
    {
        $mod = $apartmentId % 5;

        return match (true) {
            $mod === 0    => 1,  // 20% — 1 cư dân
            $mod <= 2     => 2,  // 40% — 2 cư dân
            default       => 3,  // 40% — 3 cư dân
        };
    }
}
