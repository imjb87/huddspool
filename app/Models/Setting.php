<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'stripe_enabled',
    ];

    protected $casts = [
        'stripe_enabled' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([
            'id' => 1,
        ], [
            'stripe_enabled' => false,
        ]);
    }

    public static function stripeEnabled(): bool
    {
        return (bool) static::current()->stripe_enabled;
    }

    public static function stripePaymentsAvailable(): bool
    {
        return static::stripeEnabled()
            && filled(config('services.stripe.secret_key'))
            && filled(config('services.stripe.webhook_secret'));
    }
}
