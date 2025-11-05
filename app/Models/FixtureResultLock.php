<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class FixtureResultLock extends Model
{
    use HasFactory;

    protected $fillable = [
        'fixture_id',
        'locked_by',
        'locked_until',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
    ];

    public function fixture()
    {
        return $this->belongsTo(Fixture::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'locked_by')->withTrashed();
    }

    public function isActive(): bool
    {
        return $this->locked_until instanceof Carbon && $this->locked_until->isFuture();
    }

    public function heldBy(int $userId): bool
    {
        return $this->locked_by === $userId && $this->isActive();
    }
}

