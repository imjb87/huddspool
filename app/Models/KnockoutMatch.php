<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class KnockoutMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'round_id',
        'venue_id',
        'score1',
        'score2',
        'player1_id',
        'player2_id',
        'pair1',
        'pair2',
        'team1_id',
        'team2_id',
    ];

    protected $appends = [
        'title',
        'pair1Player1',
        'pair1Player2',
        'pair2Player1',
        'pair2Player2',
    ];

    protected $casts = [
        'pair1' => 'array',
        'pair2' => 'array',
    ];

    protected static function booted()
    {
        static::saved(function ($match) {
            // Check if a winner is decided
            if ($match->score1 !== null && $match->score2 !== null) {
                $match->updateNextMatch();
            }
        });
    }    

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function dependancies()
    {
        return $this->hasMany(MatchDependancy::class, 'knockout_match_id');
    }

    public function dependant()
    {
        return $this->hasOne(MatchDependancy::class, 'depends_on_id');
    }

    public function player1()
    {
        return $this->belongsTo(User::class, 'player1_id');
    }

    public function player2()
    {
        return $this->belongsTo(User::class, 'player2_id');
    }

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function getTitleAttribute()
    {
        // singles, doubles or team?
        $type = $this->round->knockout->type->value;

        switch ($type) {
            case 'singles':
                if ( ! $this->player1 && ! $this->player2) {
                    return 'Winner of above match';
                }
                if ($this->player1 && ! $this->player2) {
                    return "{$this->player1->name} vs Winner of above match";
                }
                if ( ! $this->player1 && $this->player2) {
                    return "Winner of above match vs {$this->player2->name}";
                }
                return "{$this->player1->name} vs {$this->player2->name}";
            case 'doubles':
                if ( ! $this->pair1Player1 || ! $this->pair1Player2 || ! $this->pair2Player1 || ! $this->pair2Player2) {
                    return 'Winner of above match';
                }
                if ($this->pair1Player1 && $this->pair1Player2 && ! $this->pair2Player1 && ! $this->pair2Player2) {
                    return "{$this->pair1Player1->name} & {$this->pair1Player2->name} vs Winner of above match";
                }
                if ( ! $this->pair1Player1 && ! $this->pair1Player2 && $this->pair2Player1 && $this->pair2Player2) {
                    return "Winner of above match vs {$this->pair2Player1->name} & {$this->pair2Player2->name}";
                }
                return "{$this->pair1Player1->name} & {$this->pair1Player2->name} vs {$this->pair2Player1->name} & {$this->pair2Player2->name}";
            case 'team':
                return "{$this->team1->name} vs {$this->team2->name}";
        }

        return 'Unknown';
    }

    public function getPair1Player1Attribute()
    {
        return User::find($this->pair1[0]);
    }

    public function getPair1Player2Attribute()
    {
        return User::find($this->pair1[1]);
    }

    public function getPair2Player1Attribute()
    {
        return User::find($this->pair2[0]);
    }

    public function getPair2Player2Attribute()
    {
        return User::find($this->pair2[1]);
    }

    /**
     * Update the next match with the winner's ID.
     */
    public function updateNextMatch()
    {
        $match = $this;
        $dependant = $this->dependant;

        // get the winner of current match
        $winner = $match->score1 > $match->score2 ? $match->player1_id : $match->player2_id;

        // use search to find which position the winner should be in
        $position = array_search($match->id, $dependant->dependancies->pluck('id')->toArray()) + 1;

        // update the dependant match with the winner
        $dependant->update([
            "player{$position}_id" => $winner,
        ]);
    }
}
