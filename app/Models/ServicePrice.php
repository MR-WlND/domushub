<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePrice extends Model
{
    protected $table = 'service_prices';

    protected $fillable = [
        'name',
        'type',
        'vehicle_type',
        'unit_price',
        'status',
        'description',
    ];

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class, 'service_price_id');
    }
}
