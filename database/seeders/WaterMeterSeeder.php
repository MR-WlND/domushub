<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UtilityMeter;
use App\Models\Apartment;
use Carbon\Carbon;

class WaterMeterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all apartments to ensure the current user's apartment gets data
        $apartments = Apartment::all();

        foreach ($apartments as $apartment) {
            $baseValue = 100; // Starting meter reading
            
            // Generate data for the last 6 months
            for ($i = 6; $i >= 1; $i--) {
                $date = Carbon::now()->subMonths($i);
                $usage = rand(15, 30); // Random usage between 15m3 and 30m3
                $newValue = $baseValue + $usage;

                UtilityMeter::updateOrCreate(
                    [
                        'apartment_id' => $apartment->id,
                        'type' => 'water',
                        'record_month' => $date->month,
                        'record_year' => $date->year,
                    ],
                    [
                        'old_value' => $baseValue,
                        'new_value' => $newValue,
                        'usage_amount' => $usage,
                        'status' => 'approved',
                    ]
                );

                $baseValue = $newValue;
            }
        }
    }
}
