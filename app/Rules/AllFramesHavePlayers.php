<?php 

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AllFramesHavePlayers implements Rule
{
    private $frames;

    public function __construct($frames)
    {
        $this->frames = $frames;
    }

    public function passes($attribute, $value)
    {
        foreach ($this->frames as $frame) {
            if( $frame['home_player_id'] == null || $frame['away_player_id'] == null ) {
                return false;
            }
        }

        return true;
    }

    public function message()
    {
        return 'All frames must have a home and away player.';
    }
}
