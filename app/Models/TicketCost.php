<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCost extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'description',
        'amount',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
