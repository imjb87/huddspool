<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Result;
use Illuminate\Database\Eloquent\Builder;
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
            ->with([
                'fixture:id,fixture_date',
                'section.ruleset',
            ])
            ->inOpenSeason()
            ->where('is_confirmed', false)
            ->whereHas('fixture.homeTeam', fn (Builder $query) => $query->notBye())
            ->whereHas('fixture.awayTeam', fn (Builder $query) => $query->notBye())
            ->orderByDesc('updated_at')
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
