<?php

namespace Tests\Unit\Models;

use App\Models\Apartment;
use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ApartmentModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Apartment thuộc về 1 Floor và Block.
     */
    public function test_apartment_belongs_to_floor_and_block(): void
    {
        $apartment = $this->makeApartment(['apartment_number' => 'A101']);

        $this->assertNotNull($apartment->floor);
        $this->assertNotNull($apartment->floor->block);
    }

    /**
     * Scope vacant / occupied của Apartment tự động cập nhật khi có cư dân.
     */
    public function test_apartment_status_helpers(): void
    {
        $vacantApt = $this->makeApartment(['status' => 'vacant']);
        $this->assertEquals('vacant', $vacantApt->status);

        $user = $this->makeResident($vacantApt);
        Resident::create([
            'user_id'       => $user->id,
            'apartment_id'  => $vacantApt->id,
            'relationship'  => 'owner',
            'status'        => 'active',
            'start_date'    => now()->toDateString(),
        ]);

        $vacantApt->save();
        $this->assertEquals('occupied', $vacantApt->fresh()->status);
    }
}
