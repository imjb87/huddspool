<?php

namespace App\Livewire\Admin\Section;

use Livewire\Component;
use App\Models\Season;
use App\Models\Section;
use App\Models\Ruleset;
use App\Models\Team;
use App\Rules\UniqueTeamInSeason;
use App\Rules\UniqueTeamsInSection;

class Create extends Component
{
    public Season $season;
    public $rulesets;
    public $teams;
    public $section;

    protected function rules()
    {
        return [
            'section.name' => 'required|string',
            'section.season_id' => 'required|int',
            'section.ruleset_id' => 'required|int',
            'section.teams' => ['required', 'size:10', 'array', new UniqueTeamInSeason($this->season, $this->section['teams'] ?? []), new UniqueTeamsInSection($this->section['teams'] ?? [])],
        ];
    }

    protected $messages = [
        'section.name.required' => 'The section name is required.',
        'section.season_id.required' => 'The season is required.',
        'section.ruleset_id.required' => 'The ruleset is required.',
        'section.teams.required' => 'The teams are required.',
        'section.teams.size' => 'There must be 10 teams.',
    ];

    public function mount(Season $season)
    {
        $this->season = $season;
        $this->rulesets = Ruleset::all();
        $this->teams = Team::where('folded_at', null)->orderBy('name')->get();
        $this->section['season_id'] = $season->id;
    }

    public function save()
    {
        $this->validate();

        $section = Section::create([
            'name' => $this->section['name'],
            'season_id' => $this->section['season_id'],
            'ruleset_id' => $this->section['ruleset_id'],
        ]);

        $section->teams()->attach($this->section['teams']);

        return redirect()->route('admin.sections.show', $section);
    }

    public function render()
    {
        return view('livewire.admin.section.create')->layout('layouts.admin');
    }
}
