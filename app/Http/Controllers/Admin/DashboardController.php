<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Queries\GetOpenSeasonStats;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = (new GetOpenSeasonStats())();

        return view('admin.dashboard', [
            'stats' => $stats,
        ]);
    }
}
