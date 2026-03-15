<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Result;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'title' => 'Home',
            'liveScores' => $this->liveScores(),
            'news' => $this->news(),
        ]);
    }

    private function liveScores(): Collection
    {
        return Result::query()
            ->with(['section.ruleset'])
            ->inOpenSeason()
            ->where('is_confirmed', false)
            ->where('home_team_id', '!=', 1)
            ->where('away_team_id', '!=', 1)
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();
    }

    private function news(): Collection
    {
        return News::query()
            ->with('author:id,name')
            ->latest()
            ->limit(3)
            ->get();
    }
}
