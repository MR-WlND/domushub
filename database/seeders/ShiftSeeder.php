<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'name' => 'Ca Sáng',
                'start_time' => '07:00:00',
                'end_time' => '15:00:00',
                'grace_period_minutes' => 15,
                'shift_rate' => 1.0,
                'status' => 'active',
                'description' => 'Ca trực buổi sáng (7h - 15h)',
            ],
            [
                'name' => 'Ca Chiều',
                'start_time' => '15:00:00',
                'end_time' => '23:00:00',
                'grace_period_minutes' => 15,
                'shift_rate' => 1.0,
                'status' => 'active',
                'description' => 'Ca trực buổi chiều (15h - 23h)',
            ],
            [
                'name' => 'Ca Đêm',
                'start_time' => '23:00:00',
                'end_time' => '07:00:00',
                'grace_period_minutes' => 15,
                'shift_rate' => 1.3,
                'status' => 'active',
                'description' => 'Ca trực đêm qua ngày hôm sau (23h - 7h)',
            ],
        ];

        foreach ($shifts as $data) {
            Shift::updateOrCreate(['name' => $data['name']], $data);
        }
    }
}
