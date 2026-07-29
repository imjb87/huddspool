<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Models\Expulsion;
use App\Models\GptActionAudit;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Models\News;
use App\Models\Page;
use App\Models\Ruleset;
use App\Models\Season;
use App\Models\Section;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use daacreators\CreatorsTicketing\Models\Ticket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DeleteAdministrationRecordController extends Controller
{
    public function __invoke(Request $request, string $resource, int $record): JsonResponse
    {
        $data = $request->validate([
            'expected_updated_at' => ['required', 'date'],
            'reason' => ['required', 'string', 'min:5', 'max:500'],
            'confirmation' => ['required', Rule::in(['DELETE'])],
        ]);

        return DB::transaction(function () use ($request, $resource, $record, $data): JsonResponse {
            $model = $this->find($resource, $record);
            if (! $model->updated_at?->equalTo(Carbon::parse($data['expected_updated_at']))) {
                throw ValidationException::withMessages(['expected_updated_at' => 'The record changed after it was inspected. Inspect it again before retrying.']);
            }
            $this->guard($request, $resource, $model);
            $before = $this->summary($resource, $model);
            if ($model->delete() === false) {
                throw ValidationException::withMessages(['record' => 'The application rejected deletion of this record.']);
            }
            $audit = GptActionAudit::query()->create([
                'administrator_id' => $request->user()->id,
                'action' => 'delete_'.$resource,
                'subject_type' => $model::class,
                'subject_id' => $model->getKey(),
                'before' => $before,
                'after' => ['deleted' => true, 'reason' => $data['reason']],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json(['message' => 'The administration record was deleted.', 'resource' => $resource, 'record_id' => $record, 'audit_id' => $audit->id]);
        });
    }

    private function find(string $resource, int $record): Model
    {
        $model = match ($resource) {
            'users' => User::class,
            'venues' => Venue::class,
            'seasons' => Season::class,
            'sections' => Section::class,
            'rulesets' => Ruleset::class,
            'knockouts' => Knockout::class,
            'knockout-rounds' => KnockoutRound::class,
            'knockout-participants' => KnockoutParticipant::class,
            'knockout-matches' => KnockoutMatch::class,
            'expulsions' => Expulsion::class,
            'news' => News::class,
            'pages' => Page::class,
            'support-tickets' => Ticket::class,
            'media' => Media::class,
            default => throw ValidationException::withMessages(['resource' => 'This resource does not support deletion through GPT administration.']),
        };

        return $model::query()->lockForUpdate()->findOrFail($record);
    }

    private function guard(Request $request, string $resource, Model $model): void
    {
        $message = match ($resource) {
            'users' => $model->is_admin || $model->is($request->user()) || Team::query()->where('captain_id', $model->id)->exists()
                ? 'Administrator accounts, the connected account, and current captains cannot be archived.' : null,
            'venues' => $model->teams()->exists() ? 'A venue with teams cannot be archived. Move its teams first.' : null,
            'seasons' => $model->is_open || $model->sections()->withTrashed()->exists() || $model->fixtures()->exists() || $model->knockouts()->exists() || $model->entries()->exists() || $model->expulsions()->exists()
                ? 'Only an empty, closed season can be deleted.' : null,
            'sections' => $model->season->is_open || $model->fixtures()->exists() || $model->hasRecordedResults()
                ? 'Only a fixture-free section in a closed season can be archived.' : null,
            'rulesets' => $model->sections()->withTrashed()->exists() || DB::table('fixtures')->where('ruleset_id', $model->id)->exists() || DB::table('results')->where('ruleset_id', $model->id)->exists()
                ? 'A ruleset referenced by competition records cannot be deleted.' : null,
            'knockouts' => $model->matches()->whereNotNull('completed_at')->exists()
                ? 'A knockout with completed matches cannot be deleted.' : null,
            'knockout-rounds' => $model->matches()->whereNotNull('completed_at')->exists()
                ? 'A round with completed matches cannot be deleted.' : null,
            'knockout-participants' => KnockoutMatch::query()->where('home_participant_id', $model->id)->orWhere('away_participant_id', $model->id)->orWhere('winner_participant_id', $model->id)->exists()
                ? 'A participant already used in a knockout match cannot be deleted.' : null,
            'knockout-matches' => $model->completed_at || $model->previousMatches()->exists()
                ? 'A completed match or a match fed by earlier matches cannot be deleted.' : null,
            default => null,
        };

        if ($message) {
            throw ValidationException::withMessages(['record' => $message]);
        }
    }

    private function summary(string $resource, Model $model): array
    {
        $summary = ['id' => $model->getKey(), 'updated_at' => $model->updated_at?->toAtomString()];
        foreach (['name', 'title', 'slug', 'ticket_uid', 'season_id', 'knockout_id', 'knockout_round_id'] as $field) {
            if ($model->getAttribute($field) !== null) {
                $summary[$field] = $model->getAttribute($field);
            }
        }
        if (in_array($resource, ['news', 'pages', 'rulesets'], true)) {
            $body = (string) $model->getAttribute('content');
            $summary['content_length'] = strlen($body);
            $summary['content_sha256'] = hash('sha256', $body);
        }

        return $summary;
    }
}
