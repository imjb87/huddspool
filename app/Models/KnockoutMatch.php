<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnockoutMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'round_id',
        'venue_id',
        'score1',
        'score2',
    ];

    protected $appends = [
        'title',
    ];

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function participants()
    {
        return $this->hasMany(MatchParticipant::class);
    }

    public function dependancies()
    {
        return $this->belongsToMany(KnockoutMatch::class, 'match_dependancies', 'knockout_match_id', 'depends_on_id');
    }

    public function dependants()
    {
        return $this->hasMany(KnockoutMatch::class, 'match_dependancies', 'id', 'depends_on_id');
    }

    public function getTitleAttribute()
    {
        if ( $this->round->knockout->type == 'singles' ) {
            if ( $this->participants->count() > 0 ) {
                return $this->position . '. ' . $this->participants->where('role', 'player1')->first()->participantable->name . ' vs ' . $this->participants->where('role', 'player2')->first()->participantable->name;
            }

            if ( $this->dependancies->count() == 1 ) {
                return $this->position . '. Winner of ' . $this->dependancies->first()->title;
            }

            if ( $this->dependancies->count() == 2 ) {
                return $this->position . '. Winner of Match ' . $this->dependancies->first()->position . ' and ' . $this->dependancies->last()->position;
            }
        }
    }
}
