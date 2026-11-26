<?php

namespace App\Http\Controllers;

use App\Domain\Results\ResultFrameState;
use App\Models\News;
use App\Models\Result;
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

        return view('home', [
            'title' => 'Home',
            'liveScores' => $this->liveScores(),
            'news' => $news,
        ]);
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
        if ($this->canResumeSubmission($result, $user)) {
            return route('result.create', $result->fixture);
        }

        return route('result.show', $result);
    }

    private function canResumeSubmission(Result $result, ?User $user): bool
    {
        return ! $result->is_confirmed
            && $user instanceof User
            && Gate::forUser($user)->allows('resumeSubmission', $result);
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
}
