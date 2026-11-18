<?php

namespace App\Models;

use Database\Factories\SupportTicketActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketActivity extends Model
{
    /** @use HasFactory<SupportTicketActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'support_ticket_id',
        'user_id',
        'description',
        'old_value',
        'new_value',
    ];

    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
