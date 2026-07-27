<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'notification_type',
        'name',
        'description',
        'enabled',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    public static function isEnabledFor(string $notificationType): bool
    {
        $enabled = static::query()
            ->where('notification_type', $notificationType)
            ->value('enabled');

        return $enabled === null || (bool) $enabled;
    }
}
