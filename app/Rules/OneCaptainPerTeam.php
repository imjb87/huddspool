<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Team;

class OneCaptainPerTeam implements Rule
{
    protected $role;
    protected $team_id;
    protected $user_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($role, $team_id, $user_id = null)
    {
        $this->role = $role;
        $this->team_id = $team_id;
        $this->user_id = $user_id;
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
        $team = Team::findOrFail($this->team_id);

        if ($this->user_id) {
            if ($this->role === 'captain') {
                if ($team->captain && $team->captain->id !== $this->user_id) {
                    return false;
                }
            }
        } else {
            if ($this->role === 'captain') {
                if ($team->captain) {
                    return false;
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
        return 'Only one captain is allowed per team.';
    }
}
