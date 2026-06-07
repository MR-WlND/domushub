<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            ServicePriceSeeder::class,       // Bảng giá dịch vụ (không phụ thuộc)
            AdminSeeder::class,              // Tài khoản admin
            SecuritySeeder::class,           // Tài khoản bảo vệ
            BlockFloorApartmentSeeder::class, // Tòa > Tầng > Căn hộ
            ResidentAccountSeeder::class,    // Tài khoản cư dân (cần apartments trước)
        ]);

    }
}
