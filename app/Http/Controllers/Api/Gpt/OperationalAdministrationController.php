<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Models\Expulsion;
use App\Models\GptActionAudit;
use App\Models\NotificationSetting;
use App\Models\SeasonEntry;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OperationalAdministrationController extends Controller
{
    public function storeExpulsion(Request $request): JsonResponse
    {
        $data = $request->validate(['season_id' => ['required', 'integer', Rule::exists('seasons', 'id')], 'subject_type' => ['required', Rule::in(['player', 'team'])], 'subject_id' => ['required', 'integer'], 'reason' => ['required', 'string', 'min:5', 'max:255'], 'date' => ['required', 'date']]);
        $model = $data['subject_type'] === 'player' ? User::class : Team::class;
        if (! $model::query()->whereKey($data['subject_id'])->exists()) {
            throw ValidationException::withMessages(['subject_id' => 'The selected player or team does not exist.']);
        }
        $expulsion = Expulsion::query()->create(['season_id' => $data['season_id'], 'expellable_type' => $model, 'expellable_id' => $data['subject_id'], 'reason' => $data['reason'], 'date' => $data['date']]);
        $audit = $this->audit($request, 'create_expulsion', $expulsion, ['season_id' => $expulsion->season_id, 'subject_type' => $data['subject_type'], 'subject_id' => $expulsion->expellable_id, 'reason' => $expulsion->reason, 'date' => $expulsion->date?->toDateString()]);

        return response()->json(['message' => 'The expulsion was recorded.', 'expulsion_id' => $expulsion->id, 'audit_id' => $audit->id], 201);
    }

    public function updateNotificationSetting(Request $request, NotificationSetting $setting): JsonResponse
    {
        $data = $request->validate(['enabled' => ['required', 'boolean'], 'expected_enabled' => ['required', 'boolean']]);
        if ((bool) $setting->enabled !== (bool) $data['expected_enabled']) {
            throw ValidationException::withMessages(['expected_enabled' => 'The notification setting changed after it was inspected. Inspect it again before retrying.']);
        }
        $before = ['enabled' => (bool) $setting->enabled];
        $setting->update(['enabled' => $data['enabled']]);
        $audit = $this->audit($request, 'update_notification_setting', $setting, ['before' => $before, 'after' => ['enabled' => (bool) $setting->enabled]]);

        return response()->json(['message' => 'The notification setting was updated.', 'setting_id' => $setting->id, 'enabled' => (bool) $setting->enabled, 'audit_id' => $audit->id]);
    }

    public function markEntryPaid(Request $request, SeasonEntry $entry): JsonResponse
    {
        if ($entry->isPaid()) {
            throw ValidationException::withMessages(['entry' => 'This season entry is already marked paid.']);
        }
        $entry->markPaid();
        $audit = $this->audit($request, 'mark_season_entry_paid', $entry, ['paid_at' => $entry->paid_at?->toAtomString()]);

        return response()->json(['message' => 'The season entry was marked paid.', 'entry_id' => $entry->id, 'audit_id' => $audit->id]);
    }

    private function audit(Request $request, string $action, $subject, array $after): GptActionAudit
    {
        return GptActionAudit::query()->create(['administrator_id' => $request->user()->id, 'action' => $action, 'subject_type' => $subject::class, 'subject_id' => $subject->id, 'before' => null, 'after' => $after, 'ip_address' => $request->ip(), 'user_agent' => $request->userAgent()]);
    }
}
