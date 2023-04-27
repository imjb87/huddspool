<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Season;
use App\Models\Team;

class UniqueTeamInSeason implements Rule
{
    protected Season $season;
    protected Array $teams;
    protected Team $failing_team;
    protected $section_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Season $season, Array $teams, $section_id = null)
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
     * @return bool
     */
    public function passes($attribute, $value)
    {
        foreach( $this->teams as $team_id ) {
            $team = Team::find($team_id);
            if( $team->id != 1 ) {
                foreach( $this->season->sections as $section ) {
                    if( $section->id != $this->section_id && $section->teams->contains($team) ) {
                        $this->failing_team = $team;
                        return false;
                    }
                }
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
        return "The team {$this->failing_team->name} is already in another section in this season.";
    }
}
