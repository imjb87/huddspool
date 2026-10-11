<?php

namespace App\Support\SiteSearch;

use App\Models\Section;
use App\Models\SectionTeam;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class TypesenseSiteSearch
{
    private const RESULT_LIMIT = 8;

    private const SEARCHES = [
        'players' => [
            'query_by' => 'name,team_name,team_shortname,section_names',
            'query_by_weights' => '12,5,3,1',
            'num_typos' => '2,1,1,1',
            'prefix' => 'true,true,false,false',
        ],
        'teams' => [
            'query_by' => 'name,shortname,venue_name,section_names',
            'query_by_weights' => '12,10,3,2',
            'num_typos' => '2,1,1,1',
            'prefix' => 'true,true,false,false',
        ],
        'venues' => [
            'query_by' => 'name,address',
            'query_by_weights' => '12,4',
            'num_typos' => '2,1',
            'prefix' => 'true,false',
        ],
        'sections' => [
            'query_by' => 'name,ruleset_name,season_name,team_names,search_terms',
            'query_by_weights' => '12,4,3,2,1',
            'num_typos' => '2,1,1,1,0',
            'prefix' => 'true,false,false,false,false',
        ],
    ];

    public function __construct(private readonly TypesenseClient $client)
    {
    }

    /**
     * Returns result IDs in Typesense relevance order, or null when Typesense is unavailable.
     *
     * @return array<string, array<int, string>>|null
     */
    public function search(string $query): ?array
    {
        if (! $this->client->searchConfigured()) {
            return null;
        }

        $searches = [];

        foreach (self::SEARCHES as $type => $options) {
            $searches[] = [
                'collection' => $this->client->collection($type),
                'q' => $query,
                ...$options,
                'filter_by' => 'is_active:=true',
                'sort_by' => '_text_match:desc',
                'per_page' => self::RESULT_LIMIT,
                'prioritize_exact_match' => true,
                'drop_tokens_threshold' => 1,
                'typo_tokens_threshold' => 1,
                'min_len_1typo' => 4,
                'min_len_2typo' => 8,
                'include_fields' => 'id',
            ];
        }

        try {
            $response = $this->client
                ->request(search: true)
                ->post($this->client->url('/multi_search'), ['searches' => $searches]);

            if (! $response->successful()) {
                Log::warning('Typesense site search failed; falling back to database search.', [
                    'status' => $response->status(),
                ]);

                return null;
            }

            $results = $response->json('results');

            if (! is_array($results) || count($results) !== count(self::SEARCHES)) {
                return null;
            }

            $idsByType = [];

            foreach (array_keys(self::SEARCHES) as $index => $type) {
                if (isset($results[$index]['error'])) {
                    return null;
                }

                $idsByType[$type] = collect($results[$index]['hits'] ?? [])
                    ->map(fn (array $hit): ?string => isset($hit['document']['id'])
                        ? (string) $hit['document']['id']
                        : null)
                    ->filter()
                    ->take(self::RESULT_LIMIT)
                    ->values()
                    ->all();
            }

            return $idsByType;
        } catch (Throwable $exception) {
            Log::warning('Typesense site search failed; falling back to database search.', [
                'exception' => $exception::class,
            ]);

            return null;
        }
    }

    public function reindex(bool $reset = false): void
    {
        if (! $this->client->configured()) {
            throw new RuntimeException('Typesense is not configured. Set TYPESENSE_HOST and TYPESENSE_API_KEY first.');
        }

        foreach (array_keys(self::SEARCHES) as $type) {
            $this->ensureCollection($type, $reset);
        }

        $this->importPlayers();
        $this->importTeams();
        $this->importVenues();
        $this->importSections();
    }

    public function sync(string $type, ?int $id = null): void
    {
        if (! $this->client->configured()) {
            return;
        }

        if ($type === 'rebuild') {
            $this->reindex(reset: true);

            return;
        }

        if ($id === null) {
            return;
        }

        match ($type) {
            'players' => $this->syncPlayer($id),
            'teams' => $this->syncTeamAndDependents($id),
            'venues' => $this->syncVenueAndDependents($id),
            'sections' => $this->syncSectionAndDependents($id),
            default => null,
        };
    }

    private function importPlayers(): void
    {
        User::query()
            ->whereHas('team.sections.season', fn ($query) => $query->where('is_open', true))
            ->with(['team.openSections:id,name'])
            ->chunkById(200, fn ($users) => $this->importDocuments(
                'players',
                $users->map(fn (User $user): array => $this->playerDocument($user))->all(),
            ));
    }

    private function importTeams(): void
    {
        Team::query()
            ->notBye()
            ->whereNull('folded_at')
            ->inOpenSeason()
            ->with(['venue:id,name', 'openSections:id,name'])
            ->chunkById(200, fn ($teams) => $this->importDocuments(
                'teams',
                $teams->map(fn (Team $team): array => $this->teamDocument($team))->all(),
            ));
    }

    private function importVenues(): void
    {
        Venue::query()
            ->chunkById(200, fn ($venues) => $this->importDocuments(
                'venues',
                $venues->map(fn (Venue $venue): array => $this->venueDocument($venue))->all(),
            ));
    }

    private function importSections(): void
    {
        Section::query()
            ->whereHas('season', fn ($query) => $query->where('is_open', true))
            ->with(['ruleset:id,name', 'season:id,name', 'teams:id,name,shortname'])
            ->chunkById(200, fn ($sections) => $this->importDocuments(
                'sections',
                $sections->map(fn (Section $section): array => $this->sectionDocument($section))->all(),
            ));
    }

    private function syncPlayer(int $id): void
    {
        $user = User::query()->with(['team.openSections:id,name'])->find($id);

        if (! $user || ! $user->team || $user->team->openSections->isEmpty()) {
            $this->deleteDocument('players', $id);

            return;
        }

        $this->upsertDocument('players', $this->playerDocument($user));
    }

    private function syncTeamAndDependents(int $id): void
    {
        $team = Team::query()
            ->with(['venue:id,name', 'openSections:id,name'])
            ->find($id);

        if (! $team || $team->folded_at || $team->isBye() || $team->openSections->isEmpty()) {
            $this->deleteDocument('teams', $id);
        } else {
            $this->upsertDocument('teams', $this->teamDocument($team));
        }

        User::query()
            ->where('team_id', $id)
            ->pluck('id')
            ->each(fn (int $userId) => $this->syncPlayer($userId));

        SectionTeam::query()
            ->where('team_id', $id)
            ->pluck('section_id')
            ->unique()
            ->each(fn (int $sectionId) => $this->syncSectionDocument($sectionId));
    }

    private function syncVenueAndDependents(int $id): void
    {
        $venue = Venue::query()->find($id);

        if (! $venue) {
            $this->deleteDocument('venues', $id);
        } else {
            $this->upsertDocument('venues', $this->venueDocument($venue));
        }

        Team::query()
            ->where('venue_id', $id)
            ->pluck('id')
            ->each(fn (int $teamId) => $this->syncTeamDocument($teamId));
    }

    private function syncSectionAndDependents(int $id): void
    {
        $this->syncSectionDocument($id);

        SectionTeam::query()
            ->where('section_id', $id)
            ->pluck('team_id')
            ->unique()
            ->each(function (int $teamId): void {
                $this->syncTeamDocument($teamId);

                User::query()
                    ->where('team_id', $teamId)
                    ->pluck('id')
                    ->each(fn (int $userId) => $this->syncPlayer($userId));
            });
    }

    private function syncSectionDocument(int $id): void
    {
        $section = Section::query()
            ->whereHas('season', fn ($query) => $query->where('is_open', true))
            ->with(['ruleset:id,name', 'season:id,name', 'teams:id,name,shortname'])
            ->find($id);

        if (! $section) {
            $this->deleteDocument('sections', $id);

            return;
        }

        $this->upsertDocument('sections', $this->sectionDocument($section));
    }

    private function syncTeamDocument(int $id): void
    {
        $team = Team::query()
            ->notBye()
            ->whereNull('folded_at')
            ->inOpenSeason()
            ->with(['venue:id,name', 'openSections:id,name'])
            ->find($id);

        if (! $team) {
            $this->deleteDocument('teams', $id);

            return;
        }

        $this->upsertDocument('teams', $this->teamDocument($team));
    }

    private function playerDocument(User $user): array
    {
        $team = $user->team;
        $sectionNames = $team?->openSections?->pluck('name')->values()->all();

        return $this->withoutEmptyValues([
            'id' => (string) $user->getKey(),
            'name' => $user->name,
            'team_name' => $team?->name,
            'team_shortname' => $team?->shortname,
            'section_names' => $sectionNames,
            'is_active' => true,
        ]);
    }

    private function teamDocument(Team $team): array
    {
        return $this->withoutEmptyValues([
            'id' => (string) $team->getKey(),
            'name' => $team->name,
            'shortname' => $team->shortname,
            'venue_name' => $team->venue?->name,
            'section_names' => $team->openSections->pluck('name')->values()->all(),
            'is_active' => true,
        ]);
    }

    private function venueDocument(Venue $venue): array
    {
        return $this->withoutEmptyValues([
            'id' => (string) $venue->getKey(),
            'name' => $venue->name,
            'address' => $venue->address,
            'is_active' => true,
        ]);
    }

    private function sectionDocument(Section $section): array
    {
        return $this->withoutEmptyValues([
            'id' => (string) $section->getKey(),
            'name' => $section->name,
            'ruleset_name' => $section->ruleset?->name,
            'season_name' => $section->season?->name,
            'season_id' => (int) $section->season_id,
            'team_names' => $section->teams->pluck('name')->values()->all(),
            'search_terms' => 'section division league',
            'is_active' => true,
        ]);
    }

    private function syncDocument(string $type, int $id): ?array
    {
        return match ($type) {
            'players' => $this->playerDocumentForId($id),
            'teams' => $this->teamDocumentForId($id),
            'venues' => $this->venueDocumentForId($id),
            'sections' => $this->sectionDocumentForId($id),
            default => null,
        };
    }

    private function playerDocumentForId(int $id): ?array
    {
        $user = User::query()->with(['team.openSections:id,name'])->find($id);

        return $user && $user->team && $user->team->openSections->isNotEmpty()
            ? $this->playerDocument($user)
            : null;
    }

    private function teamDocumentForId(int $id): ?array
    {
        $team = Team::query()
            ->notBye()
            ->whereNull('folded_at')
            ->inOpenSeason()
            ->with(['venue:id,name', 'openSections:id,name'])
            ->find($id);

        return $team ? $this->teamDocument($team) : null;
    }

    private function venueDocumentForId(int $id): ?array
    {
        $venue = Venue::query()->find($id);

        return $venue ? $this->venueDocument($venue) : null;
    }

    private function sectionDocumentForId(int $id): ?array
    {
        $section = Section::query()
            ->whereHas('season', fn ($query) => $query->where('is_open', true))
            ->with(['ruleset:id,name', 'season:id,name', 'teams:id,name,shortname'])
            ->find($id);

        return $section ? $this->sectionDocument($section) : null;
    }

    private function upsertDocument(string $type, array $document): void
    {
        $this->ensureCollection($type);

        $response = $this->client
            ->request()
            ->post($this->client->url(sprintf(
                '/collections/%s/documents?action=upsert',
                rawurlencode($this->client->collection($type)),
            )), $document);

        $response->throw();
    }

    private function deleteDocument(string $type, int $id): void
    {
        $response = $this->client
            ->request()
            ->delete($this->client->url(sprintf(
                '/collections/%s/documents/%s',
                rawurlencode($this->client->collection($type)),
                rawurlencode((string) $id),
            )));

        if ($response->status() !== 404) {
            $response->throw();
        }
    }

    private function importDocuments(string $type, array $documents): void
    {
        if ($documents === []) {
            return;
        }

        $body = collect($documents)
            ->map(fn (array $document): string => json_encode($document, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE))
            ->implode("\n");

        $response = $this->client
            ->request(timeout: (int) config('typesense.index_timeout', 15))
            ->withBody($body, 'text/plain')
            ->post($this->client->url(sprintf(
                '/collections/%s/documents/import?action=upsert',
                rawurlencode($this->client->collection($type)),
            )));

        $response->throw();

        foreach (preg_split('/\r?\n/', trim($response->body())) ?: [] as $line) {
            $result = json_decode($line, true);

            if (is_array($result) && ($result['success'] ?? true) === false) {
                throw new RuntimeException($result['error'] ?? 'Typesense could not import a search document.');
            }
        }
    }

    private function ensureCollection(string $type, bool $reset = false): void
    {
        $collection = $this->client->collection($type);

        if ($reset) {
            $deleted = $this->client->request()->delete($this->client->url('/collections/'.rawurlencode($collection)));

            if (! in_array($deleted->status(), [200, 404], true)) {
                $deleted->throw();
            }

            $this->createCollection($type);

            return;
        }

        $response = $this->client->request()->get($this->client->url('/collections/'.rawurlencode($collection)));

        if ($response->successful()) {
            return;
        }

        if ($response->status() !== 404) {
            $response->throw();
        }

        $this->createCollection($type);
    }

    private function createCollection(string $type): void
    {
        $response = $this->client
            ->request()
            ->post($this->client->url('/collections'), [
                'name' => $this->client->collection($type),
                'fields' => $this->collectionFields($type),
            ]);

        if ($response->status() === 409) {
            return;
        }

        $response->throw();
    }

    private function collectionFields(string $type): array
    {
        $base = [
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'is_active', 'type' => 'bool', 'facet' => true],
        ];

        return match ($type) {
            'players' => [
                ...$base,
                ['name' => 'team_name', 'type' => 'string', 'optional' => true],
                ['name' => 'team_shortname', 'type' => 'string', 'optional' => true],
                ['name' => 'section_names', 'type' => 'string[]', 'optional' => true],
            ],
            'teams' => [
                ...$base,
                ['name' => 'shortname', 'type' => 'string', 'optional' => true],
                ['name' => 'venue_name', 'type' => 'string', 'optional' => true],
                ['name' => 'section_names', 'type' => 'string[]', 'optional' => true],
            ],
            'venues' => [
                ...$base,
                ['name' => 'address', 'type' => 'string', 'optional' => true],
            ],
            'sections' => [
                ...$base,
                ['name' => 'ruleset_name', 'type' => 'string', 'optional' => true],
                ['name' => 'season_name', 'type' => 'string', 'optional' => true],
                ['name' => 'season_id', 'type' => 'int32', 'facet' => true],
                ['name' => 'team_names', 'type' => 'string[]', 'optional' => true],
                ['name' => 'search_terms', 'type' => 'string', 'optional' => true],
            ],
            default => throw new RuntimeException('Unknown Typesense search collection.'),
        };
    }

    private function bulkImport(string $type, callable $query, callable $transform): void
    {
        $query->chunkById(200, function ($records) use ($type, $transform): void {
            $this->importDocuments(
                $type,
                $records->map(fn (Model $record): array => $transform($record))->all(),
            );
        });
    }

    private function withoutEmptyValues(array $document): array
    {
        return array_filter(
            $document,
            static fn (mixed $value): bool => $value !== null && $value !== [],
        );
    }
}
