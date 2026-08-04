<?php

namespace Tests\Unit\Models;

use App\Models\Facility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class FacilityModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Facility casting giá trị đúng dạng decimal.
     */
    public function test_facility_casts_price(): void
    {
        $facility = Facility::create([
            'name'          => 'Sân Hồ bơi',
            'capacity'      => 50,
            'status'        => 'available',
            'slot_duration' => 60,
            'booking_type'  => 'slot',
            'fee_type'      => 'per_hour',
            'price'         => 100000,
        ]);

        $this->assertEquals(100000, $facility->price);
    }
}
