<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Season;

class OneSeasonOpen implements Rule
{
    private $season;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($season)
    {
        $this->season = $season;
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
        if ($this->season['is_open']) {
            $seasons = Season::where('is_open', true)->where('id', '!=', $this->season['id'])->get();
            if ($seasons->count() > 0) {
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
        return 'Only one season can be open at a time.';
    }
}
