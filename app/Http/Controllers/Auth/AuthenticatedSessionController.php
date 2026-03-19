<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var User $user */
        $user = auth()->user();

        return redirect($user->getRedirectRoute())
            ->with($this->resultSubmissionPrompt($user));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * @return array<string, array{message: string, url: string}|null>
     */
    private function resultSubmissionPrompt(User $user): array
    {
        if (! $user->isTeamAdmin() && ! $user->isCaptain()) {
            return ['result_submission_prompt' => null];
        }

        if (! $user->team) {
            return ['result_submission_prompt' => null];
        }

        $fixture = Fixture::query()
            ->with(['result', 'homeTeam', 'awayTeam'])
            ->inOpenSeason()
            ->forTeam($user->team)
            ->whereHas('homeTeam', fn ($query) => $query->where('name', '!=', 'Bye'))
            ->whereHas('awayTeam', fn ($query) => $query->where('name', '!=', 'Bye'))
            ->whereDate('fixture_date', '<=', now()->toDateString())
            ->orderBy('fixture_date')
            ->orderBy('id')
            ->get()
            ->first(function (Fixture $fixture) use ($user) {
                if ($fixture->result instanceof Result) {
                    return ! $fixture->result->is_confirmed && Gate::forUser($user)->allows('resumeSubmission', $fixture->result);
                }

                return Gate::forUser($user)->allows('createResult', $fixture);
            });

        if (! $fixture) {
            return ['result_submission_prompt' => null];
        }

        return [
            'result_submission_prompt' => [
                'message' => 'A team result is ready to submit.',
                'url' => route('result.create', $fixture),
            ],
        ];
    }
}
