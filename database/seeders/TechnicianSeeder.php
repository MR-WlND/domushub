<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TechnicianSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        User::updateOrCreate(
            ['email' => 'ktv@demo.com'],
            [
                'name' => 'Nguyen Van Ky Thuat',
                'phone' => '0900000006',
                'password' => Hash::make('password123'),
                'role' => 'technician',
                'status' => 'active',
                'email_verified_at' => $now,
            ]
        );

        User::updateOrCreate(
            ['email' => 'ktv2@demo.com'],
            [
                'name' => 'Tran Van Ky Thuat 2',
                'phone' => '0900000007',
                'password' => Hash::make('password123'),
                'role' => 'technician',
                'status' => 'active',
                'email_verified_at' => $now,
            ]
        );
    }
}
