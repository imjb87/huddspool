<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expulsion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'season_id',
        'expellable_id',
        'expellable_type',
        'reason',
        'date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    /**
     * Get the owning expellable model (team or player).
     */
    public function expellable()
    {
        return $this->morphTo();
    }

    /**
     * Get the season associated with the expulsion.
     */
    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
