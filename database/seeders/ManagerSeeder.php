<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        \App\Models\User::updateOrCreate(
            ['email' => 'manager@demo.com'],
            [
                'name' => 'Manager',
                'phone' => '0900000008',
                'password' => Hash::make('password123'),
                'role' => 'manager',
                'status' => 'active',
                'email_verified_at' => $now,
            ]
        );
    }
}

