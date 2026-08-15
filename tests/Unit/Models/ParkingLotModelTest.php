<?php

namespace Tests\Unit\Models;

use App\Models\ParkingLot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ParkingLotModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Helper isAvailable() và isOccupied().
     */
    public function test_parking_lot_status_helpers(): void
    {
        $availableLot = ParkingLot::create([
            'lot_number' => 'P-01',
            'lot_type'   => 'car',
            'status'     => 'available',
        ]);

        $occupiedLot = ParkingLot::create([
            'lot_number' => 'P-02',
            'lot_type'   => 'car',
            'status'     => 'occupied',
        ]);

        $this->assertTrue($availableLot->isAvailable());
        $this->assertTrue($occupiedLot->isOccupied());
    }
}
