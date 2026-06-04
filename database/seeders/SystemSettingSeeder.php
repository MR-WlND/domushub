<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        SystemSetting::updateOrCreate(
            ['setting_key' => 'max_motorbike_per_apartment'],
            ['setting_value' => '3']
        );
        
        SystemSetting::updateOrCreate(
            ['setting_key' => 'max_car_per_apartment'],
            ['setting_value' => '1']
        );
    }
}
