<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class DatesAreOrdered implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        for ($i = 0; $i < count($value) - 1; $i++) {
            if ($value[$i] > $value[$i+1]) {
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
        return 'The dates must be ordered correctly.';
    }
}
