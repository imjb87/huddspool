<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Result;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $news = $this->news();

        return view('home', [
            'title' => 'Home',
            'liveScores' => $this->liveScores(),
            'featuredArticle' => $news->first(),
            'featuredParagraphs' => $this->featuredArticleParagraphs($news->first()),
            'secondaryArticles' => $news->slice(1),
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
            ->get()
            ->map(function (Result $result) {
                $result->row_meta = collect([
                    $result->fixture?->fixture_date?->format('j M Y'),
                    $result->section?->name,
                    $result->section?->ruleset?->name,
                ])->filter()->implode(' / ');

                return $result;
            });
    }

    private function news(): Collection
    {
        return News::query()
            ->with('author:id,name')
            ->latest()
            ->limit(3)
            ->get();
    }

    private function featuredArticleParagraphs(?News $article): Collection
    {
        if (! $article) {
            return collect();
        }

        return collect(preg_split('/\r\n|\r|\n/', trim((string) $article->content)))
            ->filter(fn (?string $paragraph): bool => filled(Str::of((string) $paragraph)->trim()))
            ->take(3)
            ->values();
    }
}
