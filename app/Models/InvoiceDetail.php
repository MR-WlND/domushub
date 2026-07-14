<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetail extends Model
{
    protected $table = 'bill_details';

    public $timestamps = false; // Bảng bill_details chỉ có created_at mặc định CSDL

    protected $fillable = [
        'bill_id',
        'service_price_id',
        'quantity',
        'amount',
        'status',
        'note',
        'payment_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'bill_id');
    }

    public function servicePrice()
    {
        return $this->belongsTo(ServicePrice::class, 'service_price_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
