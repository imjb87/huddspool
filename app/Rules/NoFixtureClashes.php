<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Fixture;

class NoFixtureClashes implements Rule
{
    public $clashes;
    public $schedule;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($schedule)
    {
        $this->schedule = $schedule;
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
        $this->clashes = Fixture::where('section_id', $value)
            ->where(function ($query) {
                foreach ($this->schedule as $week => $fixtures) {
                    foreach ($fixtures as $fixture) {
                        $query->orWhere(function ($query) use ($week, $fixture) {
                            $query->where('week', $week)
                                ->where(function ($query) use ($fixture) {
                                    $query->where('venue_id', $fixture['venue_id']);
                                });
                        });
                    }
                }
            })
            ->get();

        return $this->clashes->isEmpty();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        // List dates, venues and home team name for each clash
        return 'The following existing fixtures clash with this proposed schedule, you may ignore them if you wish:<ul class="list-disc space-y-1 pl-5 mt-2">' . $this->clashes->map(function ($clash) {
            return '<li>Week ' . $clash->week . ': ' . $clash->homeTeam->name . ' v ' . $clash->awayTeam->name . ' at ' . $clash->venue->name . '</li>';
        })->implode('') . '</ul>';
    }
}
