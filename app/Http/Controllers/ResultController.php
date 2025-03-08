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
        return view('result.create', compact('fixture'));
    }
}
