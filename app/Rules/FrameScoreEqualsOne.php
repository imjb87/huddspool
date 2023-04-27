<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FrameScoreEqualsOne implements Rule
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
            if ( $frame['home_score'] + $frame['away_score'] != 1 ) {
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
        return 'The total score for each frame must equal 1.';
    }
}
