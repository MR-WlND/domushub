<?php

namespace Tests\Unit\Models;

use App\Models\Resident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ResidentModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Resident thuộc về User và Apartment.
     */
    public function test_resident_relationships(): void
    {
        $apartment = $this->makeApartment();
        $user      = $this->makeResident($apartment);

        $resident = Resident::create([
            'user_id'      => $user->id,
            'apartment_id' => $apartment->id,
            'relationship' => 'owner',
            'status'       => 'active',
            'start_date'   => now()->toDateString(),
        ]);

        $this->assertEquals($user->id, $resident->user->id);
        $this->assertEquals($apartment->id, $resident->apartment->id);
    }
}
