<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TableController extends Controller
{
    public function index(Ruleset $ruleset)
    {
        $sections = $ruleset->sections()
            ->whereHas('season', function (Builder $query) {
                $query->whereIsOpen(true);
            })
            ->with([
                'results',
                'season.expulsions',
                'teams' => function (BelongsToMany $query) {
                    $query->withTrashed()->withPivot(['sort', 'section_id', 'team_id', 'deducted', 'withdrawn_at']);
                },
            ])
            ->get();

        return view('table.index', compact('ruleset', 'sections'));
    }
}
