<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CleaningSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        User::updateOrCreate(
            ['email' => 'cleaning.main@example.com'],
            [
                'name' => 'Nhan Vien Ve Sinh 1',
                'phone' => '0900000010',
                'password' => Hash::make('password123'),
                'role' => 'cleaning',
                'status' => 'active',
                'email_verified_at' => $now,
            ]
        );

        User::updateOrCreate(
            ['email' => 'cleaning.floor@example.com'],
            [
                'name' => 'Nhan Vien Ve Sinh 2',
                'phone' => '0900000011',
                'password' => Hash::make('password123'),
                'role' => 'cleaning',
                'status' => 'active',
                'email_verified_at' => $now,
            ]
        );
    }
}
