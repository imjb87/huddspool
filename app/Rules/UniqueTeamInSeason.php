<?php

namespace App\Rules;

use App\Models\Season;
use App\Models\Team;
use Illuminate\Contracts\Validation\Rule;

class UniqueTeamInSeason implements Rule
{
    protected Season $season;

    protected array $teams;

    protected Team $failing_team;

    protected ?int $section_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Season $season, array $teams, ?int $section_id = null)
    {
        $this->season = $season;
        $this->teams = $teams;
        $this->section_id = $section_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        foreach ($this->teams as $teamId) {
            $team = Team::query()->find($teamId);

            if (! $team || $team->isBye()) {
                continue;
            }

            foreach ($this->season->sections as $section) {
                if ($section->id !== $this->section_id && $section->teams->contains($team)) {
                    $this->failing_team = $team;

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return "The team {$this->failing_team->name} is already in another section in this season.";
    }
}
