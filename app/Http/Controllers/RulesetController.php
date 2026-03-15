<?php

namespace App\Http\Controllers;

use App\Models\Ruleset;
use App\Models\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RulesetController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const SECTION_TABS = [
        'tables',
        'fixtures-results',
        'averages',
    ];

    public function index(): RedirectResponse
    {
        return redirect()->route('home');
    }

    public function show(Request $request, Ruleset $ruleset): RedirectResponse|View
    {
        if ($request->filled('section')) {
            $activeTab = $this->resolveActiveTab($request->string('tab')->toString());
            $section = $this->resolveRequestedOrDefaultSection($request, $ruleset);

            abort_unless($section, 404);

            return redirect()->route('ruleset.section.show', $this->sectionRouteParameters(
                $ruleset,
                $section,
                $activeTab,
                $request
            ));
        }

        return view('ruleset.show', [
            'title' => $ruleset->name,
            'ruleset' => $ruleset,
        ]);
    }

    public function section(Request $request, Ruleset $ruleset, Section $section): View
    {
        abort_unless($section->ruleset_id === $ruleset->id, 404);
        abort_unless($section->season()->where('is_open', true)->exists(), 404);

        $activeTab = $this->resolveActiveTab($request->string('tab')->toString());

        if ($activeTab === 'tables') {
            $section->loadMissing([
                'results' => fn ($query) => $query->where('is_confirmed', true),
                'season' => fn ($query) => $query->with('expulsions'),
                'teams' => fn ($query) => $query->withTrashed()->withPivot(['sort', 'section_id', 'team_id', 'deducted', 'withdrawn_at']),
            ]);
        } else {
            $section->loadMissing(['season', 'ruleset']);
        }

        $ruleset->loadMissing([
            'openSections' => fn ($query) => $query
                ->with('season'),
        ]);

        return view('ruleset.section', [
            'title' => sprintf('%s · %s', $ruleset->name, $section->name),
            'ruleset' => $ruleset,
            'sections' => $ruleset->openSections,
            'activeSection' => $section,
            'activeTab' => $activeTab,
        ]);
    }

    private function resolveActiveTab(string $tab): string
    {
        return in_array($tab, self::SECTION_TABS, true) ? $tab : 'tables';
    }

    private function resolveRequestedOrDefaultSection(Request $request, Ruleset $ruleset): ?Section
    {
        if ($request->filled('section')) {
            $requestedSection = $request->string('section')->toString();

            return $ruleset->openSections()
                ->with('season')
                ->where(function ($query) use ($requestedSection): void {
                    $query->where('slug', $requestedSection);

                    if (ctype_digit($requestedSection)) {
                        $query->orWhere('sections.id', (int) $requestedSection);
                    }
                })
                ->firstOrFail();
        }

        return $ruleset->defaultOpenSection($request->user());
    }

    /**
     * @return array<string, mixed>
     */
    private function sectionRouteParameters(Ruleset $ruleset, Section $section, string $activeTab, Request $request): array
    {
        $parameters = [
            'ruleset' => $ruleset,
            'section' => $section,
        ];

        if ($activeTab !== 'tables') {
            $parameters['tab'] = $activeTab;
        }

        if ($activeTab === 'fixtures-results' && $request->filled('week')) {
            $parameters['week'] = $request->integer('week');
        }

        if ($activeTab === 'averages' && $request->filled('page')) {
            $parameters['page'] = $request->integer('page');
        }

        return $parameters;
    }
}
