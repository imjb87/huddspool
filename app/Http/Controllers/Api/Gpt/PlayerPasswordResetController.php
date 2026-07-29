<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Models\GptActionAudit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PlayerPasswordResetController extends Controller
{
    public function __invoke(Request $request, User $player): JsonResponse
    {
        abort_unless($request->user()?->isAdmin(), 403);

        if (blank($player->email)) {
            throw ValidationException::withMessages(['player' => 'This account does not have an email address.']);
        }

        $status = Password::sendResetLink(['email' => $player->email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages(['player' => __($status)]);
        }

        $audit = GptActionAudit::query()->create([
            'administrator_id' => $request->user()->id,
            'action' => 'send_player_password_reset',
            'subject_type' => User::class,
            'subject_id' => $player->id,
            'before' => null,
            'after' => ['reset_link_sent' => true],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['message' => 'The password-reset email was sent.', 'player_id' => $player->id, 'audit_id' => $audit->id]);
    }
}
