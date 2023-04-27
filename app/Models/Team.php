<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'venue_id',
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function players()
    {
        return $this->hasMany(User::class);
    }

}
