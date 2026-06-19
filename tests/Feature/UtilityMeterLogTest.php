<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\User;
use App\Models\UtilityMeter;
use App\Models\UtilityMeterLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UtilityMeterLogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that utility meter operations automatically trigger audit logs correctly.
     */
    public function test_logging_utility_meter_creation_and_status_changes(): void
    {
        // 1. Setup entities
        $user = User::factory()->create(['role' => 'technician']);
        $block = Block::create(['name' => 'Tòa A']);
        $floor = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 1', 'floor_number' => 1]);
        $apartment = Apartment::create([
            'floor_id' => $floor->id,
            'apartment_number' => 'A101',
            'status' => 'active'
        ]);

        // Act: Create utility meter (simulating a technician recording a new value)
        $this->actingAs($user);
        $meter = UtilityMeter::create([
            'apartment_id' => $apartment->id,
            'type'         => 'electricity',
            'record_month' => 6,
            'record_year'  => 2026,
            'old_value'    => 100,
            'new_value'    => 150,
            'recorded_by'  => $user->id,
            'status'       => 'pending',
        ]);

        // Assert: "recorded" log is created in DB
        $this->assertDatabaseHas('utility_meter_logs', [
            'utility_meter_id' => $meter->id,
            'apartment_id'     => $apartment->id,
            'user_id'          => $user->id,
            'type'             => 'electricity',
            'action'           => 'recorded',
            'old_value'        => 100,
            'new_value'        => 150,
        ]);

        // Act: Edit utility meter (simulating a technician updating the recorded value)
        $meter->update([
            'new_value' => 160,
        ]);

        // Assert: "updated" log is created in DB
        $this->assertDatabaseHas('utility_meter_logs', [
            'utility_meter_id' => $meter->id,
            'action'           => 'updated',
            'old_value'        => 100,
            'new_value'        => 160,
        ]);

        // Act: Approve utility meter (simulating an accountant approving the index)
        $accountant = User::factory()->create(['role' => 'staff']);
        $this->actingAs($accountant);
        $meter->update([
            'status' => 'approved',
        ]);

        // Assert: "approved" log is created in DB
        $this->assertDatabaseHas('utility_meter_logs', [
            'utility_meter_id' => $meter->id,
            'user_id'          => $accountant->id,
            'action'           => 'approved',
            'new_value'        => 160,
        ]);

        // Act: Reject utility meter (simulating an accountant rejecting the index)
        $meter->update([
            'status' => 'rejected',
            'rejected_by' => $accountant->id,
            'reject_reason' => 'Chỉ số không khớp với ảnh thực tế',
        ]);

        // Assert: "rejected" log is created in DB with rejection reason
        $this->assertDatabaseHas('utility_meter_logs', [
            'utility_meter_id' => $meter->id,
            'user_id'          => $accountant->id,
            'action'           => 'rejected',
            'reject_reason'    => 'Chỉ số không khớp với ảnh thực tế',
            'new_value'        => 160,
        ]);
    }
}
