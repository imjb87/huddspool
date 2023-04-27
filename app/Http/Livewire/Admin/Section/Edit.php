<?php

namespace App\Http\Livewire\Admin\Section;

use Livewire\Component;
use App\Models\Section;
use App\Models\Ruleset;
use App\Models\Team;
use App\Rules\UniqueTeamInSeason;
use App\Rules\UniqueTeamsInSection;

class Edit extends Component
{
    public $rulesets;
    public $teams;
    public $section;
    public $section_teams;

    protected function rules()
    {
        return [
            'section.name' => 'required|string',
            'section.ruleset_id' => 'required|int',
            'section_teams' => ['required', 'size:10', 'array', new UniqueTeamInSeason($this->section->season, $this->section_teams, $this->section->id), new UniqueTeamsInSection($this->section_teams)],
        ];
    }

    protected $messages = [
        'section.name.required' => 'The section name is required.',
        'section.ruleset_id.required' => 'The ruleset is required.',
        'section_teams.required' => 'All teams are required.',
        'section_teams.size' => 'There must be 10 teams.',
    ];

    public function mount(Section $section)
    {
        $this->section = $section;
        $this->section_teams = $section->teams->pluck('id')->toArray();
        $this->rulesets = Ruleset::all();
        $this->teams = Team::all();
    }

    public function save()
    {
        $this->validate();

        $this->section->teams()->detach();

        $this->section->teams()->attach(array_map('intval', $this->section_teams));

        $this->section->save();

        return redirect()->route('admin.sections.show', $this->section);
    }

    public function render()
    {
        return view('livewire.admin.section.edit')->layout('layouts.admin');
    }
}
