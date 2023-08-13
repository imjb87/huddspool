<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = DB::select('
            select count(*) as total_frames, count(distinct result_id) as total_results, count(distinct home_player_id) + count(distinct away_player_id) as total_players
            from frames as a
            join results as b
            on a.result_id = b.id
            join fixtures as c
            on b.fixture_id = c.id
            join seasons as d
            on c.season_id = d.id and d.is_open = 1            
        ');
        
        return view('admin.dashboard', [
            'stats' => $stats[0],
        ]);
    }
}