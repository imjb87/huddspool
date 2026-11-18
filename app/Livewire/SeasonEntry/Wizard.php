<?php

namespace App\Livewire\SeasonEntry;

use App\Domain\Registration\Actions\RegisterSeasonEntry;
use App\Mail\SeasonEntryInvoiceMail;
use App\Models\Knockout;
use App\Models\Ruleset;
use App\Models\Season;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;

class Wizard extends Component
{
    public Season $season;

    #[Url(as: 'step')]
    public string $step = 'details';

    /**
     * @var array{name: string, email: string, telephone: string, notes: string}
     */
    public array $contact = [];

    /**
     * @var array{venue_name: string, venue_address: string, venue_telephone: string}
     */
    public array $registrationVenue = [];

    /**
     * @var array{team_name: string, contact_name: string, contact_telephone: string, ruleset_id: ?int, second_ruleset_id: ?int}
     */
    public array $teamDraft = [];

    /**
     * @var array<int, array{team_name: string, contact_name: string, contact_telephone: string, ruleset_id: ?int, second_ruleset_id: ?int}>
     */
    public array $teamRegistrations = [];

    /**
     * @var array{knockout_id: ?int, entrant_name: string}
     */
    public array $knockoutDraft = [];

    /**
     * @var array<int, array{knockout_id: ?int, entrant_name: string}>
     */
    public array $knockoutRegistrations = [];

    private ?Collection $cachedSeasonRulesets = null;

    public function mount(Season $season): void
    {
        $this->season = $season->load([
            'knockouts' => fn ($query) => $query->orderBy('name'),
            'sections.ruleset' => fn ($query) => $query->orderBy('name'),
        ]);

        $state = session()->get($this->sessionKey());

        if (is_array($state)) {
            $this->step = $this->normaliseStep((string) ($state['step'] ?? 'details'));
            $this->contact = $state['contact'] ?? $this->defaultContact();
            $this->registrationVenue = $state['registrationVenue'] ?? $this->defaultRegistrationVenue();
            $this->teamDraft = $this->normaliseTeamDraft($state['teamDraft'] ?? []);
            $this->teamRegistrations = $this->normaliseTeamRegistrations($state['teamRegistrations'] ?? []);
            $this->knockoutDraft = $this->normaliseKnockoutDraft($state['knockoutDraft'] ?? []);
            $this->knockoutRegistrations = $this->normaliseKnockoutRegistrations($state['knockoutRegistrations'] ?? []);
        } else {
            $this->contact = $this->defaultContact();
            $this->registrationVenue = $this->defaultRegistrationVenue();
            $this->teamDraft = $this->defaultTeamDraft();
            $this->teamRegistrations = [];
            $this->knockoutDraft = $this->defaultKnockoutDraft();
            $this->knockoutRegistrations = [];
        }
    }

    public function updated(string $name): void
    {
        if ($name === 'step') {
            $this->step = $this->normaliseStep($this->step);
        }

        $this->persistState();
    }

    public function goToStep(string $step): void
    {
        if (! $this->canAccessStep($step)) {
            return;
        }

        $this->step = $this->normaliseStep($step);
        $this->persistState();
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();

        $steps = $this->steps();
        $currentIndex = array_search($this->step, $steps, true);
        $this->step = $steps[min(($currentIndex === false ? 0 : $currentIndex + 1), count($steps) - 1)];

        $this->persistState();
    }

    public function previousStep(): void
    {
        $steps = $this->steps();
        $currentIndex = array_search($this->step, $steps, true);
        $this->step = $steps[max(($currentIndex === false ? 0 : $currentIndex - 1), 0)];

        $this->persistState();
    }

    public function canAccessStep(string $step): bool
    {
        if ($step === 'details') {
            return true;
        }

        if ($step === 'review') {
            return $this->hasCompletedDetails()
                && (count($this->teamRegistrations) > 0 || count($this->knockoutRegistrations) > 0);
        }

        return $this->hasCompletedDetails();
    }

    public function addTeamRegistration(): void
    {
        $this->validateTeamDraft();

        $this->teamRegistrations[] = [
            'team_name' => trim((string) $this->teamDraft['team_name']),
            'contact_name' => trim((string) $this->teamDraft['contact_name']),
            'contact_telephone' => trim((string) $this->teamDraft['contact_telephone']),
            'ruleset_id' => $this->filledInt($this->teamDraft['ruleset_id'] ?? null),
            'second_ruleset_id' => $this->filledInt($this->teamDraft['second_ruleset_id'] ?? null),
        ];

        $this->teamDraft = $this->defaultTeamDraft();
        $this->persistState();
    }

    public function removeTeamRegistration(int $index): void
    {
        unset($this->teamRegistrations[$index]);
        $this->teamRegistrations = array_values($this->teamRegistrations);
        $this->persistState();
    }

    public function addKnockoutRegistration(): void
    {
        $this->validateKnockoutDraft();

        $this->knockoutRegistrations[] = [
            'knockout_id' => $this->filledInt($this->knockoutDraft['knockout_id'] ?? null),
            'entrant_name' => trim((string) ($this->knockoutDraft['entrant_name'] ?? '')),
        ];

        $this->knockoutDraft = $this->defaultKnockoutDraft();
        $this->persistState();
    }

    public function removeKnockoutRegistration(int $index): void
    {
        unset($this->knockoutRegistrations[$index]);
        $this->knockoutRegistrations = array_values($this->knockoutRegistrations);
        $this->persistState();
    }

    public function submit(): void
    {
        if (! $this->season->acceptingEntries()) {
            $this->addError('season', 'Season sign-ups are closed.');

            return;
        }

        $this->validateDetails();
        $this->validateVenue();
        $this->validateTeamStep();
        $this->validateKnockoutStep();

        if (count($this->teamRegistrations) === 0 && count($this->knockoutRegistrations) === 0) {
            $this->addError('cart', 'Add at least one team or knockout registration before you confirm.');

            return;
        }

        $entry = app(RegisterSeasonEntry::class)->execute(
            season: $this->season,
            contact: $this->contact,
            venue: $this->resolveVenuePayload(),
            teamRegistrations: $this->teamRegistrations,
            knockoutRegistrations: $this->knockoutRegistrations,
            knockouts: $this->seasonKnockouts(),
        );

        Mail::to($entry->contact_email)->queue(new SeasonEntryInvoiceMail($entry));

        session()->forget($this->sessionKey());

        $this->redirectRoute('season.entry.confirmation', [
            'season' => $this->season,
            'entry' => $entry->reference,
        ]);
    }

    public function render(): View
    {
        return view('livewire.season-entry.wizard', [
            'steps' => $this->steps(),
            'availableRulesets' => $this->seasonRulesets(),
            'availableKnockouts' => $this->seasonKnockouts(),
            'teamSubtotal' => $this->teamSubtotal(),
            'knockoutSubtotal' => $this->knockoutSubtotal(),
            'grandTotal' => $this->grandTotal(),
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function steps(): array
    {
        return ['details', 'venue', 'teams', 'knockouts', 'review'];
    }

    /**
     * @return array{name: string, email: string, telephone: string, notes: string}
     */
    private function defaultContact(): array
    {
        return [
            'name' => '',
            'email' => '',
            'telephone' => '',
            'notes' => '',
        ];
    }

    /**
     * @return array{venue_name: string, venue_address: string, venue_telephone: string}
     */
    private function defaultRegistrationVenue(): array
    {
        return [
            'venue_name' => '',
            'venue_address' => '',
            'venue_telephone' => '',
        ];
    }

    /**
     * @return array{team_name: string, contact_name: string, contact_telephone: string, ruleset_id: ?int, second_ruleset_id: ?int}
     */
    private function defaultTeamDraft(): array
    {
        return [
            'team_name' => '',
            'contact_name' => '',
            'contact_telephone' => '',
            'ruleset_id' => null,
            'second_ruleset_id' => null,
        ];
    }

    /**
     * @return array{knockout_id: ?int, entrant_name: string}
     */
    private function defaultKnockoutDraft(): array
    {
        return [
            'knockout_id' => null,
            'entrant_name' => '',
        ];
    }

    /**
     * @param  array<string, mixed>  $draft
     * @return array{team_name: string, contact_name: string, contact_telephone: string, ruleset_id: ?int, second_ruleset_id: ?int}
     */
    private function normaliseTeamDraft(array $draft): array
    {
        return array_replace($this->defaultTeamDraft(), $draft);
    }

    /**
     * @param  array<int, array<string, mixed>>  $registrations
     * @return array<int, array{team_name: string, contact_name: string, contact_telephone: string, ruleset_id: ?int, second_ruleset_id: ?int}>
     */
    private function normaliseTeamRegistrations(array $registrations): array
    {
        return array_values(array_map(
            fn (array $registration): array => $this->normaliseTeamDraft($registration),
            array_filter($registrations, 'is_array')
        ));
    }

    /**
     * @param  array<string, mixed>  $draft
     * @return array{knockout_id: ?int, entrant_name: string}
     */
    private function normaliseKnockoutDraft(array $draft): array
    {
        return array_replace($this->defaultKnockoutDraft(), $draft);
    }

    /**
     * @param  array<int, array<string, mixed>>  $registrations
     * @return array<int, array{knockout_id: ?int, entrant_name: string}>
     */
    private function normaliseKnockoutRegistrations(array $registrations): array
    {
        return array_values(array_map(
            fn (array $registration): array => $this->normaliseKnockoutDraft($registration),
            array_filter($registrations, 'is_array')
        ));
    }

    private function normaliseStep(string $step): string
    {
        return in_array($step, $this->steps(), true) ? $step : 'details';
    }

    private function hasCompletedDetails(): bool
    {
        return filled($this->contact['name'] ?? null)
            && filled($this->contact['email'] ?? null)
            && filter_var($this->contact['email'] ?? null, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function persistState(): void
    {
        session()->put($this->sessionKey(), [
            'step' => $this->step,
            'contact' => $this->contact,
            'registrationVenue' => $this->registrationVenue,
            'teamDraft' => $this->normaliseTeamDraft($this->teamDraft),
            'teamRegistrations' => $this->normaliseTeamRegistrations($this->teamRegistrations),
            'knockoutDraft' => $this->normaliseKnockoutDraft($this->knockoutDraft),
            'knockoutRegistrations' => $this->normaliseKnockoutRegistrations($this->knockoutRegistrations),
        ]);
    }

    private function sessionKey(): string
    {
        return sprintf('season-entry:%d', $this->season->id);
    }

    private function validateDetails(): void
    {
        $this->validate([
            'contact.name' => ['required', 'string', 'max:255'],
            'contact.email' => ['required', 'email', 'max:255'],
            'contact.telephone' => ['nullable', 'string', 'max:255'],
            'contact.notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    private function validateCurrentStep(): void
    {
        switch ($this->step) {
            case 'details':
                $this->validateDetails();
                break;
            case 'venue':
                $this->validateVenue();
                break;
            case 'teams':
                $this->validateTeamStep();
                break;
            case 'knockouts':
                $this->validateKnockoutStep();
                break;
        }
    }

    private function validateVenue(): void
    {
        $this->validate([
            'registrationVenue.venue_name' => ['required', 'string', 'max:255'],
            'registrationVenue.venue_address' => ['nullable', 'string', 'max:1000'],
            'registrationVenue.venue_telephone' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function validateTeamDraft(): void
    {
        $seasonRulesetIds = $this->seasonRulesets()->pluck('id')->all();

        $this->validate($this->teamValidationRules('teamDraft', $this->teamDraft, $seasonRulesetIds), [
            'teamDraft.second_ruleset_id.not_in' => 'The second ruleset option must be different from the first.',
        ]);
    }

    private function validateTeamStep(): void
    {
        if (count($this->teamRegistrations) === 0) {
            return;
        }

        $seasonRulesetIds = $this->seasonRulesets()->pluck('id')->all();

        foreach ($this->teamRegistrations as $index => $registration) {
            $this->validate($this->teamValidationRules("teamRegistrations.{$index}", $registration, $seasonRulesetIds));
        }
    }

    private function validateKnockoutDraft(): void
    {
        $seasonKnockoutIds = $this->seasonKnockouts()->pluck('id')->all();

        $this->validate($this->knockoutValidationRules('knockoutDraft', $seasonKnockoutIds));
    }

    private function validateKnockoutStep(): void
    {
        $seasonKnockoutIds = $this->seasonKnockouts()->pluck('id')->all();

        foreach ($this->knockoutRegistrations as $index => $registration) {
            $this->validate($this->knockoutValidationRules("knockoutRegistrations.{$index}", $seasonKnockoutIds));
        }
    }

    /**
     * @param  array<string, mixed>  $registration
     * @param  array<int, int>  $seasonRulesetIds
     * @return array<string, array<int, mixed>>
     */
    private function teamValidationRules(string $prefix, array $registration, array $seasonRulesetIds): array
    {
        $firstRulesetId = $this->filledInt($registration['ruleset_id'] ?? null);

        return [
            "{$prefix}.team_name" => ['required', 'string', 'max:255'],
            "{$prefix}.contact_name" => ['required', 'string', 'max:255'],
            "{$prefix}.contact_telephone" => ['required', 'string', 'max:255'],
            "{$prefix}.ruleset_id" => ['required', Rule::in($seasonRulesetIds)],
            "{$prefix}.second_ruleset_id" => [
                'required',
                Rule::in($seasonRulesetIds),
                Rule::notIn(array_filter([$firstRulesetId])),
            ],
        ];
    }

    /**
     * @param  array<int, int>  $seasonKnockoutIds
     * @return array<string, array<int, mixed>>
     */
    private function knockoutValidationRules(string $prefix, array $seasonKnockoutIds): array
    {
        return [
            "{$prefix}.knockout_id" => ['required', Rule::in($seasonKnockoutIds)],
            "{$prefix}.entrant_name" => ['required', 'string', 'max:255'],
        ];
    }

    private function teamSubtotal(): float
    {
        return count($this->teamRegistrations) * (float) $this->season->team_entry_fee;
    }

    private function knockoutSubtotal(): float
    {
        $knockouts = $this->seasonKnockouts()->keyBy('id');

        return collect($this->knockoutRegistrations)
            ->sum(fn (array $registration): float => (float) ($knockouts->get((int) ($registration['knockout_id'] ?? 0))?->entry_fee ?? 0));
    }

    private function grandTotal(): float
    {
        return $this->teamSubtotal() + $this->knockoutSubtotal();
    }

    /**
     * @return Collection<int, Ruleset>
     */
    private function seasonRulesets(): Collection
    {
        return $this->cachedSeasonRulesets ??= Ruleset::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->values();
    }

    /**
     * @return Collection<int, Knockout>
     */
    private function seasonKnockouts(): Collection
    {
        return $this->season->getRelationValue('knockouts') ?? collect();
    }

    private function filledInt(mixed $value): ?int
    {
        return filled($value) ? (int) $value : null;
    }

    /**
     * @return array{venue_name: ?string, venue_address: ?string, venue_telephone: ?string}
     */
    private function resolveVenuePayload(): array
    {
        return [
            'venue_name' => trim((string) $this->registrationVenue['venue_name']),
            'venue_address' => trim((string) $this->registrationVenue['venue_address']) ?: null,
            'venue_telephone' => trim((string) $this->registrationVenue['venue_telephone']) ?: null,
        ];
    }
}
