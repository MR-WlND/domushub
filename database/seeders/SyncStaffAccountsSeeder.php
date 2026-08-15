<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\User;

class SyncStaffAccountsSeeder extends Seeder
{
    public function run(): void
    {
        // Map department code → user role
        $deptRoleMap = [
            'BV' => 'security',
            'VS' => 'cleaning',
            'KT' => 'technician',
            'LT' => 'receptionist',
            'QL' => 'manager',
            'HC' => 'staff',
        ];

        $staffs = Staff::with('department')->whereDoesntHave('user')->get();
        $created = 0;

        foreach ($staffs as $staff) {
            // Bỏ qua nếu không có phone
            if (!$staff->phone) continue;

            // Kiểm tra phone đã tồn tại trong users chưa
            if (User::where('phone', $staff->phone)->exists()) continue;

            // Xác định role từ department
            $role = 'staff'; // default
            if ($staff->department && isset($deptRoleMap[$staff->department->code])) {
                $role = $deptRoleMap[$staff->department->code];
            }

            User::create([
                'name' => $staff->full_name,
                'phone' => $staff->phone,
                'email' => $staff->phone . '@domushub.local',
                'password' => '123',
                'role' => $role,
                'status' => 'active',
                'staff_id' => $staff->id,
            ]);

            $created++;
        }

        $this->command->info("Đã tạo {$created} tài khoản user cho nhân viên.");
    }
}
