<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Models\GptActionAudit;
use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Services\KnockoutBracketBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KnockoutAdministrationController extends Controller
{
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

    private function audit(Request $request, string $action, $subject, ?array $before, array $after): GptActionAudit
    {
        return GptActionAudit::query()->create(['administrator_id' => $request->user()->id, 'action' => $action, 'subject_type' => $subject::class, 'subject_id' => $subject->id, 'before' => $before, 'after' => $after, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
    }
}
