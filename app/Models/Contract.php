<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id', 'contract_number', 'type', 'base_salary', 'start_date', 'end_date', 'file_path'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'base_salary' => 'decimal:2',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->end_date) {
            return false; // Vô thời hạn
        }
        return \Carbon\Carbon::parse($this->end_date)->isPast();
    }
}
