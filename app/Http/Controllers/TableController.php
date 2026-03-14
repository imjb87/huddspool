<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use Illuminate\Database\Eloquent\Builder;

class TableController extends Controller
{
    public function index(Ruleset $ruleset)
    {
        $sections = $ruleset->sections()
            ->whereHas('season', function (Builder $query) {
                $query->whereIsOpen(true);
            })
            ->withStandingsRelations()
            ->get();

        return view('table.index', compact('ruleset', 'sections'));
    }
}
