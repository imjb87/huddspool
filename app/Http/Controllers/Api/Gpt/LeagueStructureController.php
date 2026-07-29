<?php

namespace App\Http\Controllers\Api\Gpt;

use App\Http\Controllers\Controller;
use App\Models\Season;
use App\Models\Section;
use App\Models\SectionTeam;
use App\Models\Team;
use App\Models\Venue;
use App\Services\Gpt\ManageLeagueStructure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class LeagueStructureController extends Controller
{
    public function storeSeason(Request $request, ManageLeagueStructure $service): JsonResponse
    {
        $data = $request->validate($this->seasonRules());
        [$season, $audit] = $service->createSeason($request->user(), $data, $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The season was created closed.', 'season_id' => $season->id, 'audit_id' => $audit->id], 201);
    }

    public function updateSeason(Request $request, Season $season, ManageLeagueStructure $service): JsonResponse
    {
        $data = $request->validate(['expected_updated_at' => ['required', 'date']] + $this->seasonRules(required: false));
        $expected = Carbon::parse($data['expected_updated_at']);
        unset($data['expected_updated_at']);
        [$season, $audit] = $service->updateSeason($request->user(), $season, $data, $expected, $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The season was updated.', 'season_id' => $season->id, 'audit_id' => $audit->id]);
    }

    public function openSeason(Request $request, Season $season, ManageLeagueStructure $service): JsonResponse
    {
        $audit = $service->openSeason($request->user(), $season, $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The season is now open and all other seasons are closed.', 'season_id' => $season->id, 'audit_id' => $audit->id]);
    }

    public function storeSection(Request $request, ManageLeagueStructure $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'season_id' => ['required', 'integer', Rule::exists('seasons', 'id')], 'ruleset_id' => ['required', 'integer', Rule::exists('rulesets', 'id')]]);
        [$section, $audit] = $service->createSection($request->user(), $data, $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The section was created.', 'section_id' => $section->id, 'audit_id' => $audit->id], 201);
    }

    public function updateSection(Request $request, Section $section, ManageLeagueStructure $service): JsonResponse
    {
        $data = $request->validate(['expected_updated_at' => ['required', 'date'], 'name' => ['sometimes', 'required', 'string', 'max:255'], 'ruleset_id' => ['sometimes', 'required', 'integer', Rule::exists('rulesets', 'id')]]);
        $expected = Carbon::parse($data['expected_updated_at']);
        unset($data['expected_updated_at']);
        [$section, $audit] = $service->updateSection($request->user(), $section, $data, $expected, $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The section was updated.', 'section_id' => $section->id, 'audit_id' => $audit->id]);
    }

    public function storeTeam(Request $request, ManageLeagueStructure $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', Rule::unique('teams', 'name')], 'shortname' => ['nullable', 'string', 'max:255'], 'venue_id' => ['nullable', 'integer', Rule::exists('venues', 'id')->whereNull('deleted_at')]]);
        [$team, $audit] = $service->createTeam($request->user(), $data, $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The team was created.', 'team' => $this->teamSummary($team), 'audit_id' => $audit->id], 201);
    }

    public function updateTeam(Request $request, Team $team, ManageLeagueStructure $service): JsonResponse
    {
        $data = $request->validate(['expected_updated_at' => ['required', 'date'], 'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('teams', 'name')->ignore($team)], 'shortname' => ['sometimes', 'nullable', 'string', 'max:255']]);
        $expected = Carbon::parse($data['expected_updated_at']);
        unset($data['expected_updated_at']);
        [$team, $audit] = $service->updateTeam($request->user(), $team, $data, $expected, $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The team was updated.', 'team' => $this->teamSummary($team), 'audit_id' => $audit->id]);
    }

    public function foldTeam(Request $request, Team $team, ManageLeagueStructure $service): JsonResponse
    {
        $audit = $service->foldTeam($request->user(), $team, $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The team was folded.', 'team_id' => $team->id, 'audit_id' => $audit->id]);
    }

    public function storeVenue(Request $request, ManageLeagueStructure $service): JsonResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', Rule::unique('venues', 'name')], 'address' => ['required', 'string'], 'telephone' => ['nullable', 'string', 'max:255']]);
        [$venue, $audit] = $service->createVenue($request->user(), $data, $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The venue was created.', 'venue' => $this->venueSummary($venue), 'audit_id' => $audit->id], 201);
    }

    public function updateVenue(Request $request, Venue $venue, ManageLeagueStructure $service): JsonResponse
    {
        $data = $request->validate(['expected_updated_at' => ['required', 'date'], 'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('venues', 'name')->ignore($venue)], 'address' => ['sometimes', 'required', 'string'], 'telephone' => ['sometimes', 'nullable', 'string', 'max:255']]);
        $expected = Carbon::parse($data['expected_updated_at']);
        unset($data['expected_updated_at']);
        [$venue, $audit] = $service->updateVenue($request->user(), $venue, $data, $expected, $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The venue was updated.', 'venue' => $this->venueSummary($venue), 'audit_id' => $audit->id]);
    }

    public function addTeamToSection(Request $request, Section $section, ManageLeagueStructure $service): JsonResponse
    {
        $data = $request->validate(['team_id' => ['required', 'integer', Rule::exists('teams', 'id')->whereNull('deleted_at')]]);
        [$membership, $audit] = $service->addTeam($request->user(), $section, Team::query()->findOrFail($data['team_id']), $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The team was added to the section.', 'section_team_id' => $membership->id, 'audit_id' => $audit->id], 201);
    }

    public function updateDeduction(Request $request, SectionTeam $sectionTeam, ManageLeagueStructure $service): JsonResponse
    {
        $data = $request->validate(['deducted' => ['required', 'integer', 'min:0'], 'expected_current_deduction' => ['required', 'integer', 'min:0']]);
        $audit = $service->deduct($request->user(), $sectionTeam, $data['deducted'], $data['expected_current_deduction'], $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The points deduction was updated.', 'section_team_id' => $sectionTeam->id, 'deducted' => $data['deducted'], 'audit_id' => $audit->id]);
    }

    public function withdrawTeam(Request $request, SectionTeam $sectionTeam, ManageLeagueStructure $service): JsonResponse
    {
        $audit = $service->withdraw($request->user(), $sectionTeam, $request->ip(), $request->userAgent());

        return response()->json(['message' => 'The team was withdrawn from the open-season section.', 'section_team_id' => $sectionTeam->id, 'audit_id' => $audit->id]);
    }

    private function teamSummary(Team $team): array
    {
        return $team->only(['id', 'name', 'shortname', 'venue_id', 'updated_at']);
    }

    private function venueSummary(Venue $venue): array
    {
        return $venue->only(['id', 'name', 'address', 'telephone', 'updated_at']);
    }

    private function seasonRules(bool $required = true): array
    {
        $prefix = $required ? 'required' : 'sometimes';

        return ['name' => [$prefix, 'string', 'max:255', Rule::unique('seasons', 'name')], 'dates' => [$prefix, 'array', 'size:18'], 'dates.*' => ['required', 'date'], 'team_entry_fee' => [$prefix, 'numeric', 'min:0'], 'signup_opens_at' => ['nullable', 'date'], 'signup_closes_at' => ['nullable', 'date', 'after_or_equal:signup_opens_at']];
    }
}
