<?php

namespace App\Models;

use Database\Factories\SupportTicketReplyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketReply extends Model
{
    /** @use HasFactory<SupportTicketReplyFactory> */
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'content',
        'is_internal_note',
        'is_seen',
        'seen_by',
        'seen_at',
    ];

    protected $casts = [
        'is_internal_note' => 'boolean',
        'is_seen' => 'boolean',
        'seen_at' => 'datetime',
    ];

    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function seenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seen_by');
    }
}
