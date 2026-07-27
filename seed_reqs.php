<?php

use App\Models\Shift;
use App\Models\Department;
use App\Models\ShiftRequirement;

$shifts = Shift::all();
$depts = Department::whereIn('code', ['SEC', 'TECH', 'REC', 'CLEAN'])->get();

foreach ($shifts as $shift) {
    foreach ($depts as $dept) {
        $required = 1;
        if ($dept->code == 'SEC') $required = 4;
        if ($dept->code == 'TECH') $required = 2;
        if ($dept->code == 'CLEAN') $required = 3;

        ShiftRequirement::firstOrCreate(
            ['shift_id' => $shift->id, 'department_id' => $dept->id],
            ['required_staff' => $required]
        );
    }
}

echo "Seeded shift requirements.";
