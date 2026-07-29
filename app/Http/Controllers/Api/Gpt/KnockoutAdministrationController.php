<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\KnockoutType;
use App\Models\GptActionAudit;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutParticipant;
use App\Models\KnockoutRound;
use App\Services\KnockoutBracketBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KnockoutAdministrationController extends Controller
{
    public function storeKnockout(Request $request): JsonResponse
    {
        $data = $request->validate(['season_id' => ['required', 'integer', Rule::exists('seasons', 'id')], 'name' => ['required', 'string', 'max:255'], 'type' => ['required', Rule::enum(KnockoutType::class)], 'best_of' => ['nullable', 'integer', 'min:1'], 'entry_fee' => ['nullable', 'numeric', 'min:0']]);
        if ($data['type'] !== KnockoutType::Team->value && isset($data['best_of']) && $data['best_of'] % 2 === 0) {
            throw ValidationException::withMessages(['best_of' => 'Singles and doubles best-of values must be odd.']);
        }
        $knockout = Knockout::query()->create($data);
        $audit = $this->audit($request, 'create_knockout', $knockout, null, $knockout->toArray());

        return response()->json(['message' => 'The knockout was created.', 'knockout_id' => $knockout->id, 'audit_id' => $audit->id], 201);
    }

    public function storeParticipant(Request $request, Knockout $knockout): JsonResponse
    {
        $data = $request->validate(['label' => ['nullable', 'string', 'max:255'], 'seed' => ['nullable', 'integer', 'min:1'], 'team_id' => ['nullable', 'integer', Rule::exists('teams', 'id')], 'player_one_id' => ['nullable', 'integer', Rule::exists('users', 'id')], 'player_two_id' => ['nullable', 'integer', Rule::exists('users', 'id')]]);
        match ($knockout->type) {
            KnockoutType::Team => $data['team_id'] ?? throw ValidationException::withMessages(['team_id' => 'A team knockout participant requires a team.']),
            KnockoutType::Singles => $data['player_one_id'] ?? throw ValidationException::withMessages(['player_one_id' => 'A singles participant requires a player.']),
            KnockoutType::Doubles => $data['player_one_id'] ?? throw ValidationException::withMessages(['player_one_id' => 'A doubles participant requires at least one player.']),
        };
        if (($data['player_one_id'] ?? null) !== null && ($data['player_one_id'] ?? null) === ($data['player_two_id'] ?? null)) {
            throw ValidationException::withMessages(['player_two_id' => 'A doubles participant cannot contain the same player twice.']);
        }
        $duplicate = $knockout->participants()->where(function ($query) use ($data): void {
            foreach (['team_id', 'player_one_id', 'player_two_id'] as $field) {
                if (! empty($data[$field])) {
                    $query->orWhere($field, $data[$field]);
                }
            }
        })->exists();
        if ($duplicate) {
            throw ValidationException::withMessages(['participant' => 'This team or player is already entered in the knockout.']);
        }
        $participant = $knockout->participants()->create($data);
        $audit = $this->audit($request, 'create_knockout_participant', $participant, null, $participant->toArray());

        return response()->json(['message' => 'The participant was added.', 'participant_id' => $participant->id, 'audit_id' => $audit->id], 201);
    }

    public function storeRound(Request $request, Knockout $knockout): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'position' => ['required', 'integer', 'min:1'], 'scheduled_for' => ['nullable', 'date'], 'best_of' => ['nullable', 'integer', 'min:1'], 'is_visible' => ['required', 'boolean']]);
        if ($knockout->rounds()->where('position', $data['position'])->exists()) {
            throw ValidationException::withMessages(['position' => 'A round already uses this position.']);
        }
        $round = $knockout->rounds()->create($data);
        $audit = $this->audit($request, 'create_knockout_round', $round, null, $round->toArray());

        return response()->json(['message' => 'The knockout round was created.', 'round_id' => $round->id, 'audit_id' => $audit->id], 201);
    }

    public function updateKnockout(Request $request, Knockout $knockout): JsonResponse
    {
        $data = $request->validate(['expected_updated_at' => ['required', 'date'], 'name' => ['sometimes', 'string', 'max:255'], 'best_of' => ['sometimes', 'nullable', 'integer', 'min:1'], 'entry_fee' => ['sometimes', 'nullable', 'numeric', 'min:0'], 'published_at' => ['sometimes', 'nullable', 'date']]);
        $this->guardUpdatedAt($knockout, $data['expected_updated_at']);
        unset($data['expected_updated_at']);
        if ($knockout->type !== KnockoutType::Team && isset($data['best_of']) && $data['best_of'] % 2 === 0) {
            throw ValidationException::withMessages(['best_of' => 'Singles and doubles best-of values must be odd.']);
        }
        $before = $knockout->only(['id', 'name', 'best_of', 'entry_fee', 'published_at', 'updated_at']);
        $knockout->update($data);
        $audit = $this->audit($request, 'update_knockout', $knockout, $before, $knockout->fresh()->only(array_keys($before)));

        return response()->json(['message' => 'The knockout was updated.', 'knockout' => $knockout->fresh()->only(array_keys($before)), 'audit_id' => $audit->id]);
    }

    public function updateParticipant(Request $request, KnockoutParticipant $participant): JsonResponse
    {
        $data = $request->validate(['expected_updated_at' => ['required', 'date'], 'label' => ['sometimes', 'nullable', 'string', 'max:255'], 'seed' => ['sometimes', 'nullable', 'integer', 'min:1'], 'team_id' => ['sometimes', 'nullable', 'integer', Rule::exists('teams', 'id')], 'player_one_id' => ['sometimes', 'nullable', 'integer', Rule::exists('users', 'id')], 'player_two_id' => ['sometimes', 'nullable', 'integer', Rule::exists('users', 'id')]]);
        $this->guardUpdatedAt($participant, $data['expected_updated_at']);
        unset($data['expected_updated_at']);
        $candidate = array_merge($participant->only(['team_id', 'player_one_id', 'player_two_id']), $data);
        $this->validateParticipant($participant->knockout, $candidate, $participant);
        $before = $participant->only(['id', 'knockout_id', 'label', 'seed', 'team_id', 'player_one_id', 'player_two_id', 'updated_at']);
        $participant->update($data);
        $audit = $this->audit($request, 'update_knockout_participant', $participant, $before, $participant->fresh()->only(array_keys($before)));

        return response()->json(['message' => 'The knockout participant was updated.', 'participant' => $participant->fresh()->only(array_keys($before)), 'audit_id' => $audit->id]);
    }

    public function updateRound(Request $request, KnockoutRound $round): JsonResponse
    {
        $data = $request->validate(['expected_updated_at' => ['required', 'date'], 'name' => ['sometimes', 'string', 'max:255'], 'position' => ['sometimes', 'integer', 'min:1'], 'scheduled_for' => ['sometimes', 'nullable', 'date'], 'best_of' => ['sometimes', 'nullable', 'integer', 'min:1'], 'is_visible' => ['sometimes', 'boolean']]);
        $this->guardUpdatedAt($round, $data['expected_updated_at']);
        unset($data['expected_updated_at']);
        if (isset($data['position']) && $round->knockout->rounds()->whereKeyNot($round->id)->where('position', $data['position'])->exists()) {
            throw ValidationException::withMessages(['position' => 'A round already uses this position.']);
        }
        $before = $round->only(['id', 'knockout_id', 'name', 'position', 'scheduled_for', 'best_of', 'is_visible', 'updated_at']);
        $round->update($data);
        $audit = $this->audit($request, 'update_knockout_round', $round, $before, $round->fresh()->only(array_keys($before)));

        return response()->json(['message' => 'The knockout round was updated.', 'round' => $round->fresh()->only(array_keys($before)), 'audit_id' => $audit->id]);
    }

    public function recordResult(Request $request, KnockoutMatch $match): JsonResponse
    {
        $data = $request->validate(['home_score' => ['required', 'integer', 'min:0'], 'away_score' => ['required', 'integer', 'min:0'], 'reason' => ['required', 'string', 'min:5', 'max:500'], 'expected_completed_at' => ['present', 'nullable', 'date']]);

        return $this->mutateMatch($request, $match, 'record_knockout_result', $data['expected_completed_at'], fn () => $match->recordResult($data['home_score'], $data['away_score'], $request->user(), $data['reason']));
    }

    public function recordForfeit(Request $request, KnockoutMatch $match): JsonResponse
    {
        $data = $request->validate(['forfeit_participant_id' => ['required', 'integer', Rule::in([$match->home_participant_id, $match->away_participant_id])], 'reason' => ['required', 'string', 'min:5', 'max:500'], 'expected_completed_at' => ['present', 'nullable', 'date']]);

        return $this->mutateMatch($request, $match, 'record_knockout_forfeit', $data['expected_completed_at'], fn () => $match->update(['forfeit_participant_id' => $data['forfeit_participant_id'], 'forfeit_reason' => $data['reason'], 'reported_by_id' => $request->user()->id, 'reported_at' => now(), 'report_reason' => $data['reason']]));
    }

    public function clearResult(Request $request, KnockoutMatch $match): JsonResponse
    {
        $data = $request->validate(['expected_completed_at' => ['required', 'date'], 'reason' => ['required', 'string', 'min:5', 'max:500']]);

        return $this->mutateMatch($request, $match, 'clear_knockout_result', $data['expected_completed_at'], fn () => $match->clearResult(), $data['reason']);
    }

    public function generateBracket(Request $request, Knockout $knockout): JsonResponse
    {
        $data = $request->validate(['shuffle' => ['sometimes', 'boolean']]);
        (new KnockoutBracketBuilder($knockout))->generate((bool) ($data['shuffle'] ?? false));
        $audit = $this->audit($request, 'generate_knockout_bracket', $knockout, null, ['shuffle' => (bool) ($data['shuffle'] ?? false), 'round_count' => $knockout->rounds()->count(), 'match_count' => $knockout->matches()->count()]);

        return response()->json(['message' => 'The knockout bracket was generated.', 'audit_id' => $audit->id]);
    }

    public function randomizeNextRound(Request $request, Knockout $knockout): JsonResponse
    {
        $round = (new KnockoutBracketBuilder($knockout))->randomizeNextRound();
        $audit = $this->audit($request, 'randomize_knockout_round', $knockout, null, ['round_id' => $round->id]);

        return response()->json(['message' => 'The next knockout round was randomised.', 'round_id' => $round->id, 'audit_id' => $audit->id]);
    }

    private function mutateMatch(Request $request, KnockoutMatch $match, string $action, mixed $expectedCompletedAt, callable $callback, ?string $reason = null): JsonResponse
    {
        return DB::transaction(function () use ($request, $match, $action, $expectedCompletedAt, $callback, $reason): JsonResponse {
            $match->refresh();
            if (($match->completed_at?->toAtomString()) !== ($expectedCompletedAt ? Carbon::parse($expectedCompletedAt)->toAtomString() : null)) {
                throw ValidationException::withMessages(['expected_completed_at' => 'The match result changed after it was inspected. Inspect it again before retrying.']);
            }
            $before = $this->matchData($match);
            $callback();
            $match->refresh();
            $after = $this->matchData($match);
            if ($reason) {
                $after['reason'] = $reason;
            }
            $audit = $this->audit($request, $action, $match, $before, $after);

            return response()->json(['message' => 'The knockout match was updated.', 'match' => $after, 'audit_id' => $audit->id]);
        });
    }

    private function matchData(KnockoutMatch $match): array
    {
        return $match->only(['id', 'home_score', 'away_score', 'winner_participant_id', 'forfeit_participant_id', 'completed_at']);
    }

    private function validateParticipant(Knockout $knockout, array $data, ?KnockoutParticipant $ignore = null): void
    {
        match ($knockout->type) {
            KnockoutType::Team => $data['team_id'] ?? throw ValidationException::withMessages(['team_id' => 'A team knockout participant requires a team.']),
            KnockoutType::Singles => $data['player_one_id'] ?? throw ValidationException::withMessages(['player_one_id' => 'A singles participant requires a player.']),
            KnockoutType::Doubles => $data['player_one_id'] ?? throw ValidationException::withMessages(['player_one_id' => 'A doubles participant requires at least one player.']),
        };
        if (($data['player_one_id'] ?? null) !== null && ($data['player_one_id'] ?? null) === ($data['player_two_id'] ?? null)) {
            throw ValidationException::withMessages(['player_two_id' => 'A doubles participant cannot contain the same player twice.']);
        }
        $duplicate = $knockout->participants()->when($ignore, fn ($query) => $query->whereKeyNot($ignore->id))->where(function ($query) use ($data): void {
            foreach (['team_id', 'player_one_id', 'player_two_id'] as $field) {
                if (! empty($data[$field])) {
                    $query->orWhere($field, $data[$field]);
                }
            }
        })->exists();
        if ($duplicate) {
            throw ValidationException::withMessages(['participant' => 'This team or player is already entered in the knockout.']);
        }
    }

    private function guardUpdatedAt($model, string $expected): void
    {
        if (! $model->updated_at?->equalTo(Carbon::parse($expected))) {
            throw ValidationException::withMessages(['expected_updated_at' => 'The record changed after it was inspected. Inspect it again before retrying.']);
        }
    }

    private function audit(Request $request, string $action, $subject, ?array $before, array $after): GptActionAudit
    {
        return GptActionAudit::query()->create(['administrator_id' => $request->user()->id, 'action' => $action, 'subject_type' => $subject::class, 'subject_id' => $subject->id, 'before' => $before, 'after' => $after, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
    }
}
