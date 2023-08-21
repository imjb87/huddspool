<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Fixture;

class OutstandingFixtures extends Component
{
    public function render()
    {
        return view(
            'livewire.admin.outstanding-fixtures',
            [
                'fixtures' => Fixture::doesntHave('result')
                    ->where('fixture_date', '<', now())
                    ->orderBy('fixture_date', 'asc')
                    ->simplePaginate(10)
            ]
        );
    }
}
