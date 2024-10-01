<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'knockout_match_id',
        'participantable_id',
        'participantable_type',
        'role',
    ];

    public function participantable()
    {
        return $this->morphTo();
    }

    public function match()
    {
        return $this->belongsTo(KnockoutMatch::class);
    }
}