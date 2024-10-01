<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Knockout extends Model
{
    use HasFactory;
    
    const TYPE_SINGLES = 'singles';
    const TYPE_DOUBLES = 'doubles';
    const TYPE_TEAM = 'team';

    protected $fillable = [
        'name',
        'type',
        'season_id',
    ];

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
