<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ResidentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        User::updateOrCreate(
            ['email' => 'resident.a@example.com'],
            [
                'name' => 'Cu Dan A',
                'phone' => '0900000004',
                'password' => Hash::make('password123'),
                'role' => 'resident',
                'status' => 'active',
                'email_verified_at' => $now,
            ]
        );

        User::updateOrCreate(
            ['email' => 'resident.b@example.com'],
            [
                'name' => 'Cu Dan B',
                'phone' => '0900000005',
                'password' => Hash::make('password123'),
                'role' => 'resident',
                'status' => 'active',
                'email_verified_at' => $now,
            ]
        );
    }
}
