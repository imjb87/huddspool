<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'knockout_id',
        'date',
        'best_of',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function knockout()
    {
        return $this->belongsTo(Knockout::class);
    }

    public function matches()
    {
        return $this->hasMany(KnockoutMatch::class);
    }
}
