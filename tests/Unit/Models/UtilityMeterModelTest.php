<?php

namespace Tests\Unit\Models;

use App\Models\UtilityMeter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class UtilityMeterModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * UtilityMeter thuộc về Apartment.
     */
    public function test_utility_meter_belongs_to_apartment(): void
    {
        $apartment = $this->makeApartment();
        $admin     = $this->makeAdmin();

        $meter = UtilityMeter::create([
            'apartment_id' => $apartment->id,
            'type'         => 'water',
            'old_value'    => 100,
            'new_value'    => 120,
            'usage_amount' => 20,
            'record_month' => 7,
            'record_year'  => 2026,
            'recorded_by'  => $admin->id,
            'status'       => 'approved',
        ]);

        $this->assertEquals($apartment->id, $meter->apartment->id);
        $this->assertEquals(20, $meter->fresh()->usage_amount);
    }
}
