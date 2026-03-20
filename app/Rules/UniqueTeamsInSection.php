<?php

namespace App\Rules;

use App\Models\Team;
use Illuminate\Contracts\Validation\Rule;

class UniqueTeamsInSection implements Rule
{
    protected array $teams;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $teams)
    {
        $this->teams = $teams;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        $teams = collect($this->teams)
            ->filter(function ($teamId) {
                $team = Team::query()->find($teamId);

                return $team?->isBye() !== true;
            });

        return $teams->count() === $teams->unique()->count();
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'Each team can only be in a section once.';
    }
}
