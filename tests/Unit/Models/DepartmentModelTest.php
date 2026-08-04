<?php

namespace Tests\Unit\Models;

use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class DepartmentModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Department có thể khởi tạo thuộc tính đúng.
     */
    public function test_department_attributes(): void
    {
        $dept = Department::create([
            'code'        => 'KT01',
            'name'        => 'Phòng Kế toán',
            'status'      => 'active',
            'description' => 'Phòng kế toán tài chính',
        ]);

        $this->assertEquals('KT01', $dept->code);
        $this->assertEquals('Phòng Kế toán', $dept->name);
    }
}
