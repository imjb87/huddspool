<?php

namespace App\Support;

use App\Models\Fixture;
use App\Models\Knockout;
use App\Models\Page;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapBuilder
{
    public function build(): Sitemap
    {
        $sitemap = Sitemap::create();

        $this->addStaticUrls($sitemap);
        $this->addRulesetUrls($sitemap);
        $this->addSectionUrls($sitemap);
        $this->addHistoryUrls($sitemap);
        $this->addPageUrls($sitemap);
        $this->addKnockoutUrls($sitemap);
        $this->addTeamUrls($sitemap);
        $this->addPlayerUrls($sitemap);
        $this->addVenueUrls($sitemap);
        $this->addFixtureUrls($sitemap);
        $this->addResultUrls($sitemap);

        return $sitemap;
    }

    public function writeTo(string $path): string
    {
        $this->build()->writeToFile($path);

        return $path;
    }

    private function addStaticUrls(Sitemap $sitemap): void
    {
        $sitemap->add($this->makeUrl(route('home', absolute: false)));
        $sitemap->add($this->makeUrl(route('history.index', absolute: false)));
    }

    private function addRulesetUrls(Sitemap $sitemap): void
    {
        Ruleset::query()
            ->orderBy('id')
            ->get()
            ->each(function (Ruleset $ruleset) use ($sitemap): void {
                $sitemap->add($this->makeModelUrl(route('ruleset.show', $ruleset, false), $ruleset));
            });
    }

    private function addSectionUrls(Sitemap $sitemap): void
    {
        Section::query()
            ->whereHas('season', fn ($query) => $query->where('is_open', true))
            ->with('ruleset')
            ->orderBy('id')
            ->get()
            ->each(function (Section $section) use ($sitemap): void {
                if (! $section->ruleset) {
                    return;
                }

                $sitemap->add($this->makeModelUrl(route('ruleset.section.show', [
                    'ruleset' => $section->ruleset,
                    'section' => $section,
                ], false), $section));
            });
    }

    private function addHistoryUrls(Sitemap $sitemap): void
    {
        Season::query()
            ->where('is_open', false)
            ->orWhere(function ($query): void {
                $query->whereHas('sections')
                    ->whereHas('fixtures');
            })
            ->with([
                'sections' => fn ($query) => $query->withTrashed()->with('ruleset'),
            ])
            ->orderByDesc('id')
            ->get()
            ->filter(fn (Season $season): bool => $season->hasConcluded())
            ->each(function (Season $season) use ($sitemap): void {
                $season->sections
                    ->filter(fn (Section $section): bool => $section->ruleset !== null)
                    ->each(function (Section $section) use ($season, $sitemap): void {
                        $sitemap->add($this->makeModelUrl(route('history.section.show', [
                            'season' => $season,
                            'ruleset' => $section->ruleset,
                            'section' => $section->slug,
                        ], false), $section));
                    });
            });
    }

    private function addPageUrls(Sitemap $sitemap): void
    {
        Page::query()
            ->orderBy('id')
            ->get()
            ->each(function (Page $page) use ($sitemap): void {
                $sitemap->add($this->makeModelUrl(route('page.show', $page, false), $page));
            });
    }

    private function addKnockoutUrls(Sitemap $sitemap): void
    {
        Knockout::query()
            ->whereNotNull('slug')
            ->orderBy('id')
            ->get()
            ->each(function (Knockout $knockout) use ($sitemap): void {
                $sitemap->add($this->makeModelUrl(route('knockout.show', $knockout, false), $knockout));
            });
    }

    private function addTeamUrls(Sitemap $sitemap): void
    {
        Team::query()
            ->notBye()
            ->whereHas('sections.season', fn ($query) => $query->where('is_open', true))
            ->orderBy('id')
            ->get()
            ->each(function (Team $team) use ($sitemap): void {
                $sitemap->add($this->makeModelUrl(route('team.show', $team, false), $team));
            });
    }

    private function addPlayerUrls(Sitemap $sitemap): void
    {
        User::query()
            ->whereHas('team')
            ->orderBy('id')
            ->get()
            ->each(function (User $player) use ($sitemap): void {
                $sitemap->add($this->makeModelUrl(route('player.show', ['player' => $player], false), $player));
            });
    }

    private function addVenueUrls(Sitemap $sitemap): void
    {
        Venue::query()
            ->orderBy('id')
            ->get()
            ->each(function (Venue $venue) use ($sitemap): void {
                $sitemap->add($this->makeModelUrl(route('venue.show', $venue, false), $venue));
            });
    }

    private function addFixtureUrls(Sitemap $sitemap): void
    {
        Fixture::query()
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('id')
            ->get()
            ->filter(function (Fixture $fixture): bool {
                return $fixture->homeTeam !== null
                    && $fixture->awayTeam !== null
                    && ! $fixture->isBye();
            })
            ->each(function (Fixture $fixture) use ($sitemap): void {
                $sitemap->add($this->makeModelUrl(route('fixture.show', $fixture, false), $fixture));
            });
    }

    private function addResultUrls(Sitemap $sitemap): void
    {
        Result::query()
            ->orderBy('id')
            ->get()
            ->each(function (Result $result) use ($sitemap): void {
                $sitemap->add($this->makeModelUrl(route('result.show', $result, false), $result, $result->submitted_at));
            });
    }

    private function makeModelUrl(string $path, Model $model, ?DateTimeInterface $lastModified = null): Url
    {
        return $this->makeUrl($path, $lastModified ?? $model->updated_at ?? $model->created_at);
    }

    private function makeUrl(string $path, ?DateTimeInterface $lastModified = null): Url
    {
        $url = Url::create($this->absoluteUrl($path));

        if ($lastModified) {
            $url->setLastModificationDate($lastModified);
        }

        return $url;
    }

    private function absoluteUrl(string $path): string
    {
        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');

        return $path === '/'
            ? "{$frontendUrl}/"
            : $frontendUrl.'/'.ltrim($path, '/');
    }
}
