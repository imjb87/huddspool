<?php

use App\Models\Fixture;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Gate;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('fixture-results.{fixtureId}', function (User $user, int $fixtureId): array|false {
    $fixture = Fixture::query()
        ->with(['homeTeam', 'awayTeam'])
        ->find($fixtureId);

    if (! $fixture || ! Gate::forUser($user)->allows('submitResult', $fixture)) {
        return false;
    }

    return [
        'id' => (int) $user->getKey(),
        'name' => $user->name,
        'avatar_url' => $user->avatar_url,
    ];
});
