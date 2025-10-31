<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessageRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'user_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Get the message that was read
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'message_id');
    }

    /**
     * Get the user who read the message
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
