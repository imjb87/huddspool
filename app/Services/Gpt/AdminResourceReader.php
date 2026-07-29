<?php

namespace App\Services\Gpt;

use App\Models\Expulsion;
use App\Models\Fixture;
use App\Models\Frame;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\News;
use App\Models\NotificationSetting;
use App\Models\Page;
use App\Models\Result;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\SeasonEntry;
use App\Models\SeasonKnockoutEntry;
use App\Models\SeasonTeamEntry;
use App\Models\Section;
use App\Models\SectionTeam;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use daacreators\CreatorsTicketing\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminResourceReader
{
    /**
     * @return array<string, array{model: class-string<Model>, fields: list<string>, search: list<string>, relations?: list<string>, description: string}>
     */
    public function definitions(): array
    {
        return [
            'users' => $this->definition(User::class, ['id', 'name', 'email', 'telephone', 'team_id', 'role', 'is_admin', 'email_verified_at', 'updated_at'], ['name', 'email'], ['team'], 'Player and administrator accounts.'),
            'teams' => $this->definition(Team::class, ['id', 'name', 'shortname', 'venue_id', 'captain_id', 'folded_at', 'updated_at'], ['name', 'shortname'], ['venue', 'captain', 'openSections.ruleset'], 'Teams, venues, captains, and current sections.'),
            'venues' => $this->definition(Venue::class, ['id', 'name', 'telephone', 'address', 'latitude', 'longitude', 'updated_at'], ['name', 'address'], ['teams'], 'Venues and their teams.'),
            'seasons' => $this->definition(Season::class, ['id', 'name', 'dates', 'is_open', 'signup_opens_at', 'signup_closes_at', 'team_entry_fee', 'updated_at'], ['name'], [], 'League seasons, schedules, and signup windows.'),
            'sections' => $this->definition(Section::class, ['id', 'name', 'slug', 'season_id', 'ruleset_id', 'updated_at'], ['name', 'slug'], ['season', 'ruleset'], 'Competition sections.'),
            'section-teams' => $this->definition(SectionTeam::class, ['id', 'section_id', 'team_id', 'sort', 'deducted', 'withdrawn_at'], [], ['section', 'team'], 'Team membership, ordering, deductions, and withdrawals within sections.'),
            'fixtures' => $this->definition(Fixture::class, ['id', 'season_id', 'section_id', 'home_team_id', 'away_team_id', 'fixture_date', 'venue_id', 'ruleset_id', 'week'], [], ['season', 'section', 'homeTeam', 'awayTeam', 'venue'], 'League fixtures.'),
            'results' => $this->definition(Result::class, ['id', 'fixture_id', 'home_team_id', 'home_team_name', 'home_score', 'away_team_id', 'away_team_name', 'away_score', 'is_confirmed', 'is_overridden', 'submitted_at', 'section_id', 'ruleset_id'], ['home_team_name', 'away_team_name'], ['fixture', 'submittedBy'], 'Fixture results.'),
            'frames' => $this->definition(Frame::class, ['id', 'result_id', 'home_player_id', 'home_score', 'away_player_id', 'away_score'], [], ['homePlayer', 'awayPlayer'], 'Individual result frames.'),
            'rulesets' => $this->definition(Ruleset::class, ['id', 'name', 'slug', 'content', 'updated_at'], ['name', 'slug'], [], 'Competition rulesets.'),
            'knockouts' => $this->definition(Knockout::class, ['id', 'season_id', 'name', 'slug', 'type', 'best_of', 'entry_fee', 'published_at', 'updated_at'], ['name', 'slug'], ['season'], 'Knockout competitions.'),
            'knockout-rounds' => $this->definition(KnockoutRound::class, ['id', 'knockout_id', 'name', 'position', 'scheduled_for', 'best_of', 'is_visible', 'updated_at'], ['name'], ['knockout'], 'Knockout rounds and deadlines.'),
            'knockout-participants' => $this->definition(KnockoutParticipant::class, ['id', 'knockout_id', 'label', 'seed', 'team_id', 'player_one_id', 'player_two_id', 'updated_at'], ['label'], ['knockout', 'team', 'playerOne', 'playerTwo'], 'Knockout entrants.'),
            'knockout-matches' => $this->definition(KnockoutMatch::class, ['id', 'knockout_id', 'knockout_round_id', 'position', 'home_participant_id', 'away_participant_id', 'winner_participant_id', 'venue_id', 'forfeit_participant_id', 'forfeit_reason', 'referee', 'starts_at', 'home_score', 'away_score', 'best_of', 'completed_at', 'updated_at'], ['referee'], ['round', 'homeParticipant', 'awayParticipant', 'winner', 'venue'], 'Knockout matches and outcomes.'),
            'season-entries' => $this->definition(SeasonEntry::class, ['id', 'reference', 'season_id', 'contact_name', 'contact_email', 'contact_telephone', 'venue_name', 'total_amount', 'paid_at', 'notes', 'created_at'], ['reference', 'contact_name', 'contact_email'], ['season'], 'Season registration submissions.'),
            'season-team-entries' => $this->definition(SeasonTeamEntry::class, ['id', 'season_entry_id', 'existing_team_id', 'ruleset_id', 'second_ruleset_id', 'existing_venue_id', 'team_name', 'contact_name', 'contact_telephone', 'venue_name', 'venue_address', 'venue_telephone', 'price'], ['team_name', 'contact_name', 'venue_name'], ['entry', 'existingTeam', 'existingVenue', 'ruleset', 'secondRuleset'], 'Teams included in season registrations.'),
            'season-knockout-entries' => $this->definition(SeasonKnockoutEntry::class, ['id', 'season_entry_id', 'knockout_id', 'season_team_entry_id', 'existing_team_id', 'entrant_name', 'player_one_name', 'player_two_name', 'price'], ['entrant_name', 'player_one_name', 'player_two_name'], ['entry', 'knockout', 'existingTeam'], 'Knockout entrants included in season registrations.'),
            'expulsions' => $this->definition(Expulsion::class, ['id', 'season_id', 'expellable_type', 'expellable_id', 'reason', 'date', 'updated_at'], ['reason'], ['season', 'expellable'], 'Player and team expulsions.'),
            'news' => $this->definition(News::class, ['id', 'title', 'slug', 'content', 'published_at', 'author_id', 'created_at', 'updated_at'], ['title', 'slug'], ['author'], 'News posts and publication status.'),
            'pages' => $this->definition(Page::class, ['id', 'title', 'slug', 'content', 'created_at', 'updated_at'], ['title', 'slug'], [], 'Managed website pages.'),
            'notification-settings' => $this->definition(NotificationSetting::class, ['id', 'notification_type', 'name', 'description', 'enabled'], ['notification_type', 'name'], [], 'System notification controls.'),
            'support-tickets' => $this->definition(Ticket::class, ['id', 'ticket_uid', 'user_id', 'assignee_id', 'department_id', 'ticket_status_id', 'priority', 'custom_fields', 'last_activity_at', 'created_at', 'updated_at'], ['ticket_uid'], ['requester', 'assignee', 'department', 'status'], 'Support tickets and their current assignment and status.'),
            'media' => $this->definition(Media::class, ['id', 'model_type', 'model_id', 'collection_name', 'name', 'file_name', 'mime_type', 'disk', 'size', 'created_at', 'updated_at'], ['name', 'file_name'], [], 'Uploaded media records.'),
        ];
    }

    /**
     * @return array{resource: string, description: string, count: int, records: list<array<string, mixed>>}
     */
    public function list(string $resource, ?string $search, int $limit): array
    {
        $definition = $this->definitionFor($resource);
        $query = $this->query($definition);
        $search = trim((string) $search);

        if ($search !== '' && $definition['search'] !== []) {
            $query->where(function (Builder $builder) use ($definition, $search): void {
                foreach ($definition['search'] as $index => $column) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $builder->{$method}($column, 'like', '%'.$search.'%');
                }
            });
        }

        $modelClass = $definition['model'];
        $qualifiedKeyName = (new $modelClass)->getQualifiedKeyName();

        $records = $query
            ->orderByDesc($qualifiedKeyName)
            ->limit(max(1, min($limit, 50)))
            ->get()
            ->map(fn (Model $model): array => $this->serialize($model, $definition))
            ->values()
            ->all();

        return [
            'resource' => $resource,
            'description' => $definition['description'],
            'count' => count($records),
            'records' => $records,
        ];
    }

    /** @return array<string, mixed> */
    public function find(string $resource, int $record): array
    {
        $definition = $this->definitionFor($resource);
        $model = $this->query($definition)->findOrFail($record);

        return $this->serialize($model, $definition);
    }

    /** @return list<array{resource: string, description: string, searchable: bool}> */
    public function capabilities(): array
    {
        return collect($this->definitions())
            ->map(fn (array $definition, string $resource): array => [
                'resource' => $resource,
                'description' => $definition['description'],
                'searchable' => $definition['search'] !== [],
            ])
            ->values()
            ->all();
    }

    /**
     * @param  class-string<Model>  $model
     * @param  list<string>  $fields
     * @param  list<string>  $search
     * @param  list<string>  $relations
     * @return array{model: class-string<Model>, fields: list<string>, search: list<string>, relations: list<string>, description: string}
     */
    private function definition(string $model, array $fields, array $search, array $relations, string $description): array
    {
        return compact('model', 'fields', 'search', 'relations', 'description');
    }

    /** @return array{model: class-string<Model>, fields: list<string>, search: list<string>, relations: list<string>, description: string} */
    private function definitionFor(string $resource): array
    {
        return $this->definitions()[$resource] ?? throw new NotFoundHttpException('Unknown administration resource. Call the capabilities action for valid resource names.');
    }

    /** @param array{model: class-string<Model>, relations: list<string>} $definition */
    private function query(array $definition): Builder
    {
        return $definition['model']::query()->with($definition['relations']);
    }

    /**
     * @param  array{fields: list<string>, relations: list<string>}  $definition
     * @return array<string, mixed>
     */
    private function serialize(Model $model, array $definition): array
    {
        $data = Arr::only($model->attributesToArray(), $definition['fields']);

        foreach ($definition['relations'] as $relationPath) {
            $rootRelation = str($relationPath)->before('.')->toString();
            $related = $model->getRelation($rootRelation);

            if ($related instanceof Model) {
                $data[$rootRelation] = $this->relationIdentity($related);
            } elseif ($related instanceof Collection) {
                $data[$rootRelation] = collect($related)
                    ->map(fn (Model $relation): array => $this->relationIdentity($relation))
                    ->values()
                    ->all();
            }
        }

        return $data;
    }

    /** @return array<string, mixed> */
    private function relationIdentity(Model $model): array
    {
        return array_filter([
            'id' => $model->getKey(),
            'name' => $model->getAttribute('name'),
            'title' => $model->getAttribute('title'),
            'reference' => $model->getAttribute('reference'),
            'ticket_uid' => $model->getAttribute('ticket_uid'),
            'display_name' => $model->getAttribute('display_name'),
        ], fn (mixed $value): bool => $value !== null);
    }
}
