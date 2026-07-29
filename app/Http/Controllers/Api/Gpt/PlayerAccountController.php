<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gpt\CreatePlayerRequest;
use App\Http\Requests\Gpt\UpdatePlayerRequest;
use App\Models\User;
use App\Services\Gpt\ManagePlayerAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class PlayerAccountController extends Controller
{
    public function store(CreatePlayerRequest $request, ManagePlayerAccount $accounts): JsonResponse
    {
        [$player, $audit] = $accounts->create($request->user(), $request->validated(), $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The account was created.', 'player' => $this->summary($player), 'audit_id' => $audit->id], 201);
    }

    public function update(UpdatePlayerRequest $request, User $player, ManagePlayerAccount $accounts): JsonResponse
    {
        $attributes = $request->safe()->except('expected_updated_at');
        [$player, $audit] = $accounts->update(
            $request->user(),
            $player,
            $attributes,
            Carbon::parse($request->string('expected_updated_at')),
            $request->ip(),
            $request->userAgent(),
        );

        return response()->json(['message' => 'The account was updated.', 'player' => $this->summary($player), 'audit_id' => $audit->id]);
    }

    private function summary(User $player): array
    {
        return ['id' => $player->id, 'name' => $player->name, 'team_id' => $player->team_id, 'role' => $player->roleLabel(), 'updated_at' => $player->updated_at?->toAtomString()];
    }
}
