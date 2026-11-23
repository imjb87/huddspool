<?php

namespace App\Http\Controllers;

use App\Domain\Results\ResultFrameState;
use App\Models\News;
use App\Models\Result;
use App\Models\Season;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $news = $this->news();
        $entrySeason = $this->entrySeason();

        return view('home', [
            'title' => 'Home',
            'liveScores' => $this->liveScores(),
            'entrySeason' => $entrySeason,
            'entrySeasonCountdown' => $this->entrySeasonCountdown($entrySeason),
            'news' => $news,
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
                'frames' => fn ($query) => $query->orderBy('id'),
            ])
            ->inOpenSeason()
            ->where('is_confirmed', false)
            ->whereHas('fixture.homeTeam', fn (Builder $query) => $query->notBye())
            ->whereHas('fixture.awayTeam', fn (Builder $query) => $query->notBye())
            ->orderByDesc('updated_at')
            ->get()
            ->filter(fn (Result $result): bool => $this->hasLiveActivity($result))
            ->map(function (Result $result) use ($user) {
                $result->home_team_shortname = $result->fixture?->homeTeam?->shortname;
                $result->away_team_shortname = $result->fixture?->awayTeam?->shortname;
                $result->row_meta = $result->section?->name ?? '';
                $result->live_score_url = $this->liveScoreUrlFor($result, $user);

                return $result;
            });
    }

    private function hasLiveActivity(Result $result): bool
    {
        if ($result->home_score > 0 || $result->away_score > 0) {
            return true;
        }

        $frames = ResultFrameState::merge(
            ResultFrameState::fromPersistedFrames($result->frames),
            is_array($result->draft_state) ? $result->draft_state : [],
        );

        return ! ResultFrameState::isEmpty($frames);
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
            ->published()
            ->with('author:id,name', 'media')
            ->latest('published_at')
            ->limit(3)
            ->get();
    }

    /**
     * @return array{mode: 'closes', target_iso: string, target_label: string, headline: string, body: string, cta_label: string, cta_url: string}|null
     */
    private function entrySeasonCountdown(?Season $season): ?array
    {
        if (! $season) {
            return null;
        }

        return [
            'mode' => 'closes',
            'target_iso' => $season->signup_closes_at->toIso8601String(),
            'target_label' => $season->signup_closes_at->format('D j M \\a\\t H:i'),
            'headline' => 'Season sign-up is open',
            'body' => 'Register teams and knockout entries for '.$season->name.' before the window closes.',
            'cta_label' => 'Register now',
            'cta_url' => route('season.entry.show', ['season' => $season]),
        ];
    }
}
