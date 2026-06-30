<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SecuritySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        User::updateOrCreate(
            ['email' => 'security.main@example.com'],
            [
                'name' => 'Bao Ve Cong Chinh',
                'phone' => '0900000002',
                'password' => Hash::make('password123'),
                'role' => 'security',
                'status' => 'active',
                'email_verified_at' => $now,
            ]
        );

        User::updateOrCreate(
            ['email' => 'security.basement@example.com'],
            [
                'name' => 'Bao Ve Tang Ham',
                'phone' => '0900000003',
                'password' => Hash::make('password123'),
                'role' => 'security',
                'status' => 'active',
                'email_verified_at' => $now,
            ]
        );
    }
}
