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
        $fixture->load('result');

        if ($fixture->result && $fixture->result->is_confirmed) {
            return redirect()->route('result.show', $fixture->result);
        }

        $this->authorize('createResult', $fixture);

        return view('result.create', compact('fixture'));
    }
}
