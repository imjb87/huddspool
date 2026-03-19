<?php

namespace App\Http\Controllers;

use App\Models\Knockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KnockoutController extends Controller
{
    public function index(): RedirectResponse
    {
        return to_route('page.show', 'knockout-dates');
    }

    public function show(Knockout $knockout): View
    {
        return view('knockouts.show', [
            'knockout' => $knockout,
        ]);
    }
}
