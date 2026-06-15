<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TechnicianSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            [
                'name' => 'Nguyen Van Ky Thuat',
                'email' => 'ktv@demo.com',
                'phone' => '0900000006',
                'password' => Hash::make('password123'),
                'role' => 'technician',
                'status' => 'active',
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Tran Van Ky Thuat 2',
                'email' => 'ktv2@demo.com',
                'phone' => '0900000007',
                'password' => Hash::make('password123'),
                'role' => 'technician',
                'status' => 'active',
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
