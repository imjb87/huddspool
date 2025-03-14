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

        if ($fixture->result) {
            return redirect()->route('results.show', $fixture->result);
        }

        if (!$user->isTeamAdmin() || $fixture->team->id !== $user->team->id) {
            abort(403);
        }

        return view('result.create', compact('fixture'));
    }
}
