<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\Result;

class ResultController extends Controller
{
    public function show(Result $result)
    {
        return view('result.show', compact('result'));
    }

    public function create(Fixture $fixture)
    {
        $user = auth()->user();

        $fixture->load('result');

        if ($fixture->result && $fixture->result->is_confirmed) {
            return redirect()->route('result.show', $fixture->result);
        }

        if (!$user) {
            abort(403);
        }

        if (!$user->isTeamAdmin()) {
            abort(403);
        }

        if (!$fixture->homeTeam->is($user->team) && !$fixture->awayTeam->is($user->team)) {
            abort(403);
        }

        return view('result.create', compact('fixture'));
    }
}
