<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        $rulesetOrder = [
            'international-rules' => 0,
            'blackball-rules' => 1,
            'epa-rules' => 2,
        ];

        $seasons = Cache::remember('history:index', now()->addMinutes(10), function () use ($rulesetOrder) {
            $today = now()->toDateString();

            return Season::query()
                ->select(['id', 'name', 'slug', 'dates', 'is_open'])
                ->where(function ($query) use ($today) {
                    $query
                        ->where('is_open', false)
                        ->orWhereRaw(
                            "JSON_LENGTH(dates) > 0 and JSON_UNQUOTE(JSON_EXTRACT(dates, CONCAT('$[', JSON_LENGTH(dates) - 1, ']'))) <= ?",
                            [$today],
                        );
                })
                ->with([
                    'sections' => fn ($query) => $query
                        ->withTrashed()
                        ->select(['id', 'season_id', 'ruleset_id', 'name', 'slug', 'deleted_at'])
                        ->whereNotNull('ruleset_id')
                        ->with('ruleset:id,name,slug')
                        ->orderBy('name'),
                    'knockouts' => fn ($query) => $query
                        ->select(['id', 'season_id', 'name', 'slug', 'type'])
                        ->whereNotNull('slug')
                        ->orderBy('name'),
                ])
                ->orderByDesc('id')
                ->get()
                ->filter(fn (Season $season) => $season->hasConcluded())
                ->values()
                ->map(function (Season $season) use ($rulesetOrder) {
                    $rulesets = $season->sections
                        ->groupBy('ruleset_id')
                        ->map(function ($sections) use ($rulesetOrder) {
                            /** @var Section $firstSection */
                            $firstSection = $sections->first();

                            return [
                                'ruleset' => $firstSection->ruleset,
                                'sort_order' => $rulesetOrder[$firstSection->ruleset->slug ?? ''] ?? PHP_INT_MAX,
                                'sections' => $sections->values(),
                            ];
                        })
                        ->sortBy(fn (array $group) => sprintf('%03d-%s', $group['sort_order'], $group['ruleset']->name))
                        ->values();

                    return [
                        'season' => $season,
                        'rulesets' => $rulesets,
                        'knockouts' => $season->knockouts->values(),
                    ];
                });
        });

        return view('history.index', [
            'seasonGroups' => $seasons,
        ]);
    }

    public function season(Season $season): RedirectResponse
    {
        return redirect()->route('history.index');
    }

    public function show(Season $season, Ruleset $ruleset): RedirectResponse
    {
        abort_if(! $season->hasConcluded(), 404);

        $sections = Cache::remember(
            sprintf('history:sections:%d:%d', $season->id, $ruleset->id),
            now()->addMinutes(10),
            fn () => Section::withTrashed()
                ->where('season_id', $season->id)
                ->where('ruleset_id', $ruleset->id)
                ->orderBy('name')
                ->get()
        );

        $section = $sections->first();

        if (! $section) {
            return redirect()->route('history.index');
        }

        return redirect()->route('history.section.show', [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $section,
        ]);
    }

    public function section(Season $season, Ruleset $ruleset, string $section): View
    {
        abort_if(! $season->hasConcluded(), 404);

        $historySection = Section::withTrashed()
            ->where('season_id', $season->id)
            ->where('ruleset_id', $ruleset->id)
            ->where('slug', $section)
            ->firstOrFail();

        return view('history.section', [
            'season' => $season,
            'ruleset' => $ruleset,
            'section' => $historySection,
        ]);
    }
}
