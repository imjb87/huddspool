<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class BothPlayersAwardedIfOneIs implements Rule
{
    private $frames;

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
        foreach ($this->frames as $frame) {
            if ($frame['home_player_id'] == 0 xor $frame['away_player_id'] == 0) {
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
        return 'If a player is set to awarded in either home or away, both home and away must be set to awarded.';
    }
}
