<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Result;
use App\Models\Season;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
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
            'entrySeason' => $this->entrySeason(),
            'entrySeasonCountdown' => $this->entrySeasonCountdown(),
            'featuredArticle' => $news->first(),
            'featuredParagraphs' => $this->featuredArticleParagraphs($news->first()),
            'secondaryArticles' => $news->slice(1),
        ]);
    }

    private function entrySeason(): ?Season
    {
        $now = now();

        return Season::query()
            ->whereNotNull('signup_opens_at')
            ->whereNotNull('signup_closes_at')
            ->where('signup_opens_at', '<=', $now)
            ->where('signup_closes_at', '>=', $now)
            ->orderBy('signup_opens_at')
            ->orderBy('signup_closes_at')
            ->first();
    }

    private function liveScores(): Collection
    {
        $user = auth()->user();

        return Result::query()
            ->with([
                'fixture:id,fixture_date,home_team_id,away_team_id',
                'fixture.homeTeam:id,name,shortname',
                'fixture.awayTeam:id,name,shortname',
                'section.ruleset',
            ])
            ->inOpenSeason()
            ->where('is_confirmed', false)
            ->whereHas('fixture.homeTeam', fn (Builder $query) => $query->notBye())
            ->whereHas('fixture.awayTeam', fn (Builder $query) => $query->notBye())
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (Result $result) use ($user) {
                $result->home_team_shortname = $result->fixture?->homeTeam?->shortname;
                $result->away_team_shortname = $result->fixture?->awayTeam?->shortname;
                $result->row_meta = collect([
                    $result->fixture?->fixture_date?->format('j M Y'),
                    $result->section?->name,
                ])->filter()->implode(' / ');
                $result->live_score_url = $this->liveScoreUrlFor($result, $user);

                return $result;
            });
    }

    private function liveScoreUrlFor(Result $result, ?User $user): string
    {
        if (
            ! $result->is_confirmed &&
            $user instanceof User &&
            Gate::forUser($user)->allows('resumeSubmission', $result)
        ) {
            return route('result.create', $result->fixture);
        }

        return route('result.show', $result);
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

    /**
     * @return array{mode: 'closes', target_iso: string, target_label: string, headline: string, body: string, cta_label: string, cta_url: string}|null
     */
    private function entrySeasonCountdown(): ?array
    {
        $season = $this->entrySeason();

        if (! $season) {
            return null;
        }

        return $this->makeEntrySeasonCountdown(
            season: $season,
            mode: 'closes',
            target: $season->signup_closes_at,
            headline: 'Season sign-up is open',
            body: 'Register teams and knockout entries for '.$season->name.' before the window closes.',
            ctaLabel: 'Register now',
            ctaUrl: route('season.entry.show', ['season' => $season]),
        );
    }

    /**
     * @return array{mode: 'closes', target_iso: string, target_label: string, headline: string, body: string, cta_label: string, cta_url: string}
     */
    private function makeEntrySeasonCountdown(
        Season $season,
        string $mode,
        CarbonInterface $target,
        string $headline,
        string $body,
        ?string $ctaLabel,
        ?string $ctaUrl,
    ): array {
        return [
            'mode' => $mode,
            'target_iso' => $target->toIso8601String(),
            'target_label' => $target->format('D j M \\a\\t H:i'),
            'headline' => $headline,
            'body' => $body,
            'cta_label' => $ctaLabel,
            'cta_url' => $ctaUrl,
        ];
    }
}
