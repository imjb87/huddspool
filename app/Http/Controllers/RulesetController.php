<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;

class RulesetController extends Controller
{
    public function show(Ruleset $ruleset)
    {
        return view('ruleset.show', compact('ruleset'));
    }
}
