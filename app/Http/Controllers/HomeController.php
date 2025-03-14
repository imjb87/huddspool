<?php

namespace App\Http\Controllers;

use App\Models\News;

class HomeController extends Controller
{
    public function __invoke()
    {
        $news = News::latest()->take(3)->get();

        return view('home', compact('news'));
    }
}
