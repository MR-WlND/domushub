<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $table = 'bills';

    protected $fillable = [
        'apartment_id',
        'title',
        'billing_month',
        'billing_year',
        'due_date',
        'total_amount',
        'status',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }

    public function details()
    {
        return $this->hasMany(InvoiceDetail::class, 'bill_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'bill_id');
    }
}
