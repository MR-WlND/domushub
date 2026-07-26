<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Block;
use App\Models\Floor;
use App\Models\User;
use App\Models\UtilityMeter;
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
            'type'         => 'water',
            'record_month' => 6,
            'record_year'  => 2026,
            'old_value'    => 100,
            'new_value'    => 150,
            'recorded_by'  => $user->id,
            'status'       => 'pending',
        ]);

        // Assert: "recorded" log is created in DB
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'utility',
            'subject_type' => \App\Models\UtilityMeter::class,
            'subject_id' => $meter->id,
            'causer_id' => $user->id,
            'properties->action' => 'recorded',
            'properties->old_value' => 100,
            'properties->new_value' => 150,
        ]);

        // Act: Edit utility meter (simulating a technician updating the recorded value)
        $meter->update([
            'new_value' => 160,
        ]);

        // Assert: "updated" log is created in DB
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'utility',
            'subject_type' => \App\Models\UtilityMeter::class,
            'subject_id' => $meter->id,
            'properties->action' => 'updated',
            'properties->old_value' => 100,
            'properties->new_value' => 160,
        ]);

        // Act: Approve utility meter (simulating an accountant approving the index)
        $accountant = User::factory()->create(['role' => 'staff']);
        $this->actingAs($accountant);
        $meter->update([
            'status' => 'approved',
        ]);

        // Assert: "approved" log is created in DB
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'utility',
            'subject_type' => \App\Models\UtilityMeter::class,
            'subject_id' => $meter->id,
            'causer_id' => $accountant->id,
            'properties->action' => 'approved',
            'properties->new_value' => 160,
        ]);

        // Act: Reject utility meter (simulating an accountant rejecting the index)
        $meter->update([
            'status' => 'rejected',
            'rejected_by' => $accountant->id,
            'reject_reason' => 'Chỉ số không khớp với ảnh thực tế',
        ]);

        // Assert: "rejected" log is created in DB with rejection reason
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'utility',
            'subject_type' => \App\Models\UtilityMeter::class,
            'subject_id' => $meter->id,
            'causer_id' => $accountant->id,
            'properties->action' => 'rejected',
            'properties->reject_reason' => 'Chỉ số không khớp với ảnh thực tế',
            'properties->new_value' => 160,
        ]);
    }
}
