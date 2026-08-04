<?php

namespace Tests\Unit\Models;

use App\Models\ServicePrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class ServicePriceModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * ServicePrice khởi tạo thuộc tính đúng.
     */
    public function test_service_price_attributes(): void
    {
        $price = ServicePrice::create([
            'type'       => 'water',
            'name'       => 'Giá nước sinh hoạt',
            'unit_price' => 15000,
            'unit'       => 'm3',
            'status'     => 'active',
        ]);

        $this->assertEquals('water', $price->type);
        $this->assertEquals(15000, $price->unit_price);
    }
}
