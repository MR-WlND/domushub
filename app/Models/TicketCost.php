<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCost extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'cost_type',
        'description',
        'amount',
        'note',
        'responsible_user_id',
        'created_by',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
    ];

    // ── Relationships ───────────────────────────────────────────

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responsibleUser()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    // ── Label Helpers ───────────────────────────────────────────

    public function costTypeLabel(): string
    {
        return match ($this->cost_type) {
            'repair'       => 'Sửa chữa',
            'compensation' => 'Đền bù',
            default        => $this->cost_type,
        };
    }

    public function isRepair(): bool
    {
        return $this->cost_type === 'repair';
    }

    public function isCompensation(): bool
    {
        return $this->cost_type === 'compensation';
    }
}
