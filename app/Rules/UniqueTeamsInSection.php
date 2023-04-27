<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Collection;

class UniqueTeamsInSection implements Rule
{
    protected Array $teams;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($teams)
    {
        $this->teams = $teams;
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
        $teams = collect($this->teams);
        $teams = $teams->filter(function($team_id) {
            return $team_id != 1;
        });
        return $teams->count() == $teams->unique()->count();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Each team can only be in a section once.';
    }
}
