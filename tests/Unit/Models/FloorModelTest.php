<?php

namespace Tests\Unit\Models;

use App\Models\Block;
use App\Models\Floor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class FloorModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Floor thuộc về Block.
     */
    public function test_floor_belongs_to_block(): void
    {
        $block = Block::create(['name' => 'Tòa Unit Floor']);
        $floor = Floor::create(['block_id' => $block->id, 'name' => 'Tầng 5', 'floor_number' => 5]);

        $this->assertEquals($block->id, $floor->block->id);
    }
}
