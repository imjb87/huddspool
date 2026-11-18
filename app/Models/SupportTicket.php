<?php

namespace App\Models;

use App\Enums\SupportTicketStatus;
use Database\Factories\SupportTicketFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    /** @use HasFactory<SupportTicketFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'requester_name',
        'requester_email',
        'message',
        'status',
        'is_seen',
        'seen_by',
        'seen_at',
        'last_activity_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ticket): void {
            if (blank($ticket->ticket_number)) {
                do {
                    $ticketNumber = 'TKT-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
                } while (self::query()->where('ticket_number', $ticketNumber)->exists());

                $ticket->ticket_number = $ticketNumber;
            }

            $ticket->last_activity_at ??= now();
        });
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seen_by');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(SupportTicketActivity::class);
    }

    protected function title(): Attribute
    {
        return Attribute::get(fn (): string => Str::limit(trim((string) preg_replace('/\s+/', ' ', $this->message)), 100));
    }

    public function isClosing(): bool
    {
        return $this->status?->isClosing() ?? false;
    }

    public function markSeenBy(User $user): void
    {
        if ($this->is_seen || $this->user_id === $user->getKey()) {
            return;
        }

        $this->forceFill([
            'is_seen' => true,
            'seen_by' => $user->getKey(),
            'seen_at' => now(),
        ])->saveQuietly();
    }

    protected function casts(): array
    {
        return [
            'status' => SupportTicketStatus::class,
            'is_seen' => 'boolean',
            'seen_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }
}
