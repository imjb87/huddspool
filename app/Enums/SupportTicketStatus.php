<?php

namespace App\Enums;

enum SupportTicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in-progress';
    case Answered = 'answered';
    case Pending = 'pending';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::InProgress => 'In progress',
            self::Answered => 'Answered',
            self::Pending => 'Pending',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'info',
            self::InProgress => 'warning',
            self::Answered => 'success',
            self::Pending => 'primary',
            self::Resolved => 'success',
            self::Closed => 'gray',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Open => 'info',
            self::InProgress => 'warning',
            self::Answered, self::Resolved => 'success',
            self::Closed => 'outline',
            self::Pending => 'secondary',
        };
    }

    public function isClosing(): bool
    {
        return $this === self::Closed;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }

    /**
     * @return list<string>
     */
    public static function openValues(): array
    {
        return collect(self::cases())
            ->reject(fn (self $status): bool => $status->isClosing())
            ->map(fn (self $status): string => $status->value)
            ->values()
            ->all();
    }
}
