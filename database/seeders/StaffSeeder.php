<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        \App\Models\User::updateOrCreate(
            ['email' => 'ketoan@demo.com'],
            [
                'name' => 'Nhân Viên Kế Toán',
                'phone' => '0900000009',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'status' => 'active',
                'email_verified_at' => $now,
            ]
        );
    }
}

