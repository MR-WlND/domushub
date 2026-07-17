<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role',
        'message',
    ];

    /**
     * Get the user that owns the chatbot message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
