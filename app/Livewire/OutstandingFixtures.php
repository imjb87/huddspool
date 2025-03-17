<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Fixture;
use App\Models\Season;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;

class OutstandingFixtures extends Component
{
    use WithPagination, WithoutUrlPagination;

    public function render()
    {
        return view('livewire.outstanding-fixtures', [
            'fixtures' => Fixture::doesnthave('result')->where('fixture_date', '<=', now())->whereHas('section', function ($query) {
                $query->where('season_id', Season::current()->id);
            })->where('home_team_id', '!=', 1)->where('away_team_id', '!=', 1)->orderBy('fixture_date', 'asc')->orderBy('id', 'asc')->simplePaginate(5),
        ]);
    }
}
