<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FrameScoresAddUpToTen implements Rule
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
        $score = 0;

        foreach ($this->frames as $frame) {
            $score += $frame['home_score'];
            $score += $frame['away_score'];
        }

        return $score == 10;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The total score for all frames must add up to 10.';
    }
}
