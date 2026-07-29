<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class AdministrationCommandController extends Controller
{
    private const COMMANDS = [
        'create_player' => ['POST', '/api/gpt/players'], 'update_player' => ['PATCH', '/api/gpt/players/{player}'], 'send_password_reset' => ['POST', '/api/gpt/players/{player}/password-reset'], 'move_player' => ['POST', '/api/gpt/players/{player}/team'],
        'create_team' => ['POST', '/api/gpt/teams'], 'update_team' => ['PATCH', '/api/gpt/teams/{team}'], 'fold_team' => ['POST', '/api/gpt/teams/{team}/fold'], 'set_team_captain' => ['POST', '/api/gpt/teams/{team}/captain'], 'set_team_venue' => ['POST', '/api/gpt/teams/{team}/venue'],
        'create_venue' => ['POST', '/api/gpt/venues'], 'update_venue' => ['PATCH', '/api/gpt/venues/{venue}'], 'add_team_to_section' => ['POST', '/api/gpt/sections/{section}/teams'], 'set_points_deduction' => ['PATCH', '/api/gpt/section-teams/{sectionTeam}/deduction'], 'withdraw_team' => ['POST', '/api/gpt/section-teams/{sectionTeam}/withdraw'],
        'create_season' => ['POST', '/api/gpt/seasons'], 'update_season' => ['PATCH', '/api/gpt/seasons/{season}'], 'open_season' => ['POST', '/api/gpt/seasons/{season}/open'], 'create_section' => ['POST', '/api/gpt/sections'], 'update_section' => ['PATCH', '/api/gpt/sections/{section}'],
        'reschedule_fixture' => ['POST', '/api/gpt/fixtures/{fixture}/date'], 'correct_result' => ['POST', '/api/gpt/results/{result}/correction'],
        'create_knockout' => ['POST', '/api/gpt/knockouts'], 'update_knockout' => ['PATCH', '/api/gpt/knockouts/{knockout}'], 'add_knockout_participant' => ['POST', '/api/gpt/knockouts/{knockout}/participants'], 'update_knockout_participant' => ['PATCH', '/api/gpt/knockout-participants/{participant}'], 'create_knockout_round' => ['POST', '/api/gpt/knockouts/{knockout}/rounds'], 'update_knockout_round' => ['PATCH', '/api/gpt/knockout-rounds/{round}'],
        'record_knockout_result' => ['POST', '/api/gpt/knockout-matches/{match}/result'], 'record_knockout_forfeit' => ['POST', '/api/gpt/knockout-matches/{match}/forfeit'], 'clear_knockout_result' => ['POST', '/api/gpt/knockout-matches/{match}/clear-result'], 'generate_knockout_bracket' => ['POST', '/api/gpt/knockouts/{knockout}/generate-bracket'], 'randomize_knockout_round' => ['POST', '/api/gpt/knockouts/{knockout}/randomize-next-round'],
        'create_expulsion' => ['POST', '/api/gpt/expulsions'], 'update_notification_setting' => ['PATCH', '/api/gpt/notification-settings/{setting}'], 'mark_season_entry_paid' => ['POST', '/api/gpt/season-entries/{entry}/mark-paid'], 'update_support_ticket' => ['PATCH', '/api/gpt/support-tickets/{ticket}'],
        'create_content' => ['POST', '/api/gpt/content/{resource}'], 'update_content' => ['PATCH', '/api/gpt/content/{resource}/{record}'], 'delete_record' => ['DELETE', '/api/gpt/resources/{resource}/{record}'],
    ];

    public function __invoke(Request $request, Kernel $kernel): Response
    {
        $data = $request->validate(['command' => ['required', Rule::in(array_keys(self::COMMANDS))], 'arguments' => ['required', 'array']]);
        [$method, $uri] = self::COMMANDS[$data['command']];
        $arguments = $data['arguments'];
        $uri = preg_replace_callback('/\{([^}]+)\}/', function (array $match) use (&$arguments): string {
            $key = $match[1];
            abort_unless(array_key_exists($key, $arguments), 422, "The {$key} argument is required for this command.");
            $value = (string) $arguments[$key];
            unset($arguments[$key]);

            return rawurlencode($value);
        }, $uri);
        $subRequest = Request::create($uri, $method, $arguments);
        $subRequest->headers->set('Authorization', (string) $request->header('Authorization'));
        $subRequest->headers->set('Accept', 'application/json');

        return $kernel->handle($subRequest);
    }
}
