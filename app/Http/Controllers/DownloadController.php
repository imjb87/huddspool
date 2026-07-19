<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class DownloadController extends Controller
{
    public function index(): View
    {
        return view('downloads.index');
    }
}
