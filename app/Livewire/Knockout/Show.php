<?php

namespace App\Livewire\Knockout;

use App\Models\Knockout;
use App\Models\KnockoutMatch;
use App\Models\KnockoutRound;
use App\Support\KnockoutRoundMatchViewDataBuilder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Knockout $knockout;

    public ?int $currentRoundId = null;

    /**
     * @var array<int, int>
     */
    public array $matchNumbers = [];

    public function mount(Knockout $knockout): void
    {
        $this->knockout = $knockout->load([
            'season',
            'rounds.matches.homeParticipant.playerOne',
            'rounds.matches.homeParticipant.playerTwo',
            'rounds.matches.awayParticipant.playerOne',
            'rounds.matches.awayParticipant.playerTwo',
            'rounds.matches.homeParticipant.team',
            'rounds.matches.awayParticipant.team',
            'rounds.matches.forfeitParticipant.playerOne',
            'rounds.matches.forfeitParticipant.playerTwo',
            'rounds.matches.forfeitParticipant.team',
            'rounds.matches.winner',
            'rounds.matches.venue',
            'rounds.matches.previousMatches',
        ]);

        $this->matchNumbers = $this->buildMatchNumbers();
        $this->currentRoundId = $this->resolveDefaultRoundId();
    }

    public function previousRound(): void
    {
        $currentIndex = $this->currentRoundIndex();

        if ($currentIndex === null || $currentIndex === 0) {
            return;
        }

        $previousRound = $this->availableRounds()->get($currentIndex - 1);

        $this->currentRoundId = $previousRound?->id;
        $this->dispatch('knockout-round-changed');
    }

    public function nextRound(): void
    {
        $currentIndex = $this->currentRoundIndex();

        if ($currentIndex === null) {
            return;
        }

        $nextRound = $this->availableRounds()->get($currentIndex + 1);

        if (! $nextRound) {
            return;
        }

        $this->currentRoundId = $nextRound->id;
        $this->dispatch('knockout-round-changed');
    }

    public function render(): View
    {
        return view('livewire.knockout.show');
    }

    #[Computed]
    public function currentRound(): ?KnockoutRound
    {
        return $this->availableRounds()
            ->firstWhere('id', $this->currentRoundId);
    }

    #[Computed]
    public function hasPreviousRound(): bool
    {
        $currentIndex = $this->currentRoundIndex();

        return $currentIndex !== null && $currentIndex > 0;
    }

    #[Computed]
    public function hasNextRound(): bool
    {
        $currentIndex = $this->currentRoundIndex();

        return $currentIndex !== null
            && $currentIndex < ($this->availableRounds()->count() - 1);
    }

    /**
     * @return Collection<int, KnockoutRound>
     */
    #[Computed]
    public function availableRounds(): Collection
    {
        $latestPublishedRound = $this->latestPublishedRound();

        if (! $latestPublishedRound) {
            return collect();
        }

        return $this->knockout->rounds
            ->filter(fn (KnockoutRound $round): bool => $round->position <= $latestPublishedRound->position)
            ->values();
    }

    /**
     * @return Collection<int, object>
     */
    #[Computed]
    public function currentRoundRows(): Collection
    {
        if (! $this->currentRound) {
            return collect();
        }

        return (new KnockoutRoundMatchViewDataBuilder)->build(
            $this->currentRound->matches,
            $this->knockout,
            $this->matchNumbers,
            fn (KnockoutMatch $match, string $slot): string => $this->slotLabel($match, $slot),
        );
    }

    public function slotLabel(KnockoutMatch $match, string $slot): string
    {
        $participant = $slot === 'home' ? $match->homeParticipant : $match->awayParticipant;

        if ($participant) {
            return $participant->display_name;
        }

        $previousMatch = $match->previousMatches->firstWhere('next_slot', $slot);

        if ($previousMatch && isset($this->matchNumbers[$previousMatch->id])) {
            return 'Winner of Match '.$this->matchNumbers[$previousMatch->id];
        }

        $pairedParticipantId = $slot === 'home'
            ? $match->away_participant_id
            : $match->home_participant_id;

        if ($pairedParticipantId) {
            return 'Bye';
        }

        return 'TBC';
    }

    /**
     * @return array<int, int>
     */
    private function buildMatchNumbers(): array
    {
        $matchNumbers = [];
        $counter = 1;

        foreach ($this->knockout->rounds as $round) {
            foreach ($round->matches as $match) {
                $matchNumbers[$match->id] = $counter++;
            }
        }

        return $matchNumbers;
    }

    private function resolveDefaultRoundId(): ?int
    {
        return $this->latestPublishedRound()?->id;
    }

    private function currentRoundIndex(): ?int
    {
        $currentRound = $this->currentRound();

        if (! $currentRound) {
            return null;
        }

        $index = $this->availableRounds()
            ->search(fn (KnockoutRound $round): bool => $round->is($currentRound));

        return $index === false ? null : $index;
    }

    private function latestPublishedRound(): ?KnockoutRound
    {
        return $this->knockout->rounds
            ->filter(function (KnockoutRound $round): bool {
                return $round->is_visible;
            })
            ->sortByDesc('position')
            ->first();
    }
}
