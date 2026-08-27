<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemporaryRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'apartment_id',
        'approved_by',
        'type',
        'start_date',
        'end_date',
        'reason',
        'attachment_path',
        'attachments',
        'rejection_reason',
        'status',
        'card_status',
        'guest_name',
        'guest_phone',
        'guest_cccd',
        'guest_email',
        'guest_dob',
        'guest_gender',
        'guest_hometown',
        'relationship',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'guest_dob' => 'date',
        'attachments' => 'array',
    ];

    /**
     * Get the user that owns the temporary registration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the apartment associated with the temporary registration.
     */
    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }

    /**
     * Get the admin who approved/rejected the registration.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
