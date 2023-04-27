<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PlayerLimit implements Rule
{
    protected $frames;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($frames)
    {
        $this->frames = $frames;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $players = [];

        foreach ($this->frames as $frame) {
            if( $frame['home_player_id'] ) {
                $players[] = $frame['home_player_id'];
            }
            if( $frame['away_player_id'] ) {
                $players[] = $frame['away_player_id'];
            }
        }

        $playerCounts = array_count_values($players);

        foreach ($playerCounts as $playerCount) {
            if ($playerCount > 2) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'A player on either the home or away team cannot play more than twice.';
    }
}
