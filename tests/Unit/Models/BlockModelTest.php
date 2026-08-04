<?php

namespace Tests\Unit\Models;

use App\Models\Block;
use App\Models\Floor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class BlockModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Block có nhiều Floor (quan hệ HasMany).
     */
    public function test_block_has_many_floors(): void
    {
        $block = Block::create(['name' => 'Tòa Unit Test']);
        Floor::create(['block_id' => $block->id, 'name' => 'Tầng 1', 'floor_number' => 1]);
        Floor::create(['block_id' => $block->id, 'name' => 'Tầng 2', 'floor_number' => 2]);

        $this->assertCount(2, $block->floors);
    }
}
