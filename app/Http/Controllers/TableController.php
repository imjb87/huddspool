<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;

class TableController extends Controller
{
    public function index(Ruleset $ruleset)
    {
        $sections = $ruleset->sections()
            ->whereHas('season', function ($query) {
                $query->whereIsOpen(true);
            })->get();

        return view('table.index', compact('ruleset', 'sections'));
    }
}
