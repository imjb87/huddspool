<?php

namespace App\Models;

use App\KnockoutType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Knockout extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'season_id',
    ];

    protected $casts = [
        'type' => KnockoutType::class,
    ];

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function matches()
    {
        return $this->hasManyThrough(KnockoutMatch::class, Round::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
