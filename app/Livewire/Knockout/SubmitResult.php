<?php

namespace App\Livewire\Knockout;

use App\Models\KnockoutMatch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

class SubmitResult extends Component
{
    public KnockoutMatch $match;

    public int $homeScore = 0;

    public int $awayScore = 0;

    public function mount(KnockoutMatch $match): void
    {
        $this->match = $match->load('homeParticipant', 'awayParticipant', 'round.knockout');

        Gate::authorize('submitResult', $this->match);

        $this->homeScore = $match->home_score ?? 0;
        $this->awayScore = $match->away_score ?? 0;
    }

    public function submit(): RedirectResponse|Redirector
    {
        Gate::authorize('submitResult', $this->match);

        $validated = $this->validate([
            'homeScore' => ['required', 'integer', 'min:0'],
            'awayScore' => ['required', 'integer', 'min:0'],
        ]);

        $this->match->recordResult($validated['homeScore'], $validated['awayScore'], Auth::user());

        session()->flash('status', 'Result submitted.');

        return redirect()->route('knockout.show', $this->match->round->knockout);
    }

    public function render()
    {
        return view('livewire.knockout.submit-result');
    }
}
