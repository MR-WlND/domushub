<?php

namespace Tests\Unit\Models;

use App\Models\Department;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestDataHelper;
use Tests\TestCase;

class StaffModelTest extends TestCase
{
    use RefreshDatabase, TestDataHelper;

    /**
     * Staff thuộc về Department.
     */
    public function test_staff_belongs_to_department(): void
    {
        $dept  = Department::create(['code' => 'KT02', 'name' => 'Phòng Kỹ thuật', 'status' => 'active']);
        $staff = Staff::create([
            'full_name'     => 'Nguyễn Văn Kỹ Thuật',
            'phone'         => '0911000111',
            'department_id' => $dept->id,
            'status'        => 'active',
        ]);

        $this->assertEquals($dept->id, $staff->department->id);
    }
}
