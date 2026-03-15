<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TableController extends Controller
{
    public function index(Request $request, Ruleset $ruleset): RedirectResponse
    {
        $section = $request->filled('section')
            ? $ruleset->openSections()->whereKey($request->integer('section'))->firstOrFail()
            : $ruleset->defaultOpenSection($request->user());

        abort_unless($section, 404);

        $parameters = [
            'ruleset' => $ruleset,
            'section' => $section,
        ];

        return redirect()->route('ruleset.section.show', $parameters);
    }
}
