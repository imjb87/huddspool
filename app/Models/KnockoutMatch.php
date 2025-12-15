<?php

namespace App\Models;

use App\KnockoutType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class KnockoutMatch extends Model
{
    use HasFactory;

    public const TEAM_TARGET_FRAMES = 6;

    protected $fillable = [
        'knockout_id',
        'knockout_round_id',
        'position',
        'home_participant_id',
        'away_participant_id',
        'winner_participant_id',
        'venue_id',
        'starts_at',
        'home_score',
        'away_score',
        'best_of',
        'next_match_id',
        'next_slot',
        'completed_at',
        'reported_by_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected ?int $previousWinnerId = null;
    protected bool $suppressAutoBye = false;

    protected static function booted(): void
    {
        static::saving(function (KnockoutMatch $match) {
            $match->previousWinnerId = $match->getOriginal('winner_participant_id');

            if ($match->venue_id && $match->venueConflictsWithParticipants()) {
                throw ValidationException::withMessages([
                    'venue_id' => 'A match cannot be assigned to a venue that belongs to one of the teams involved.',
                ]);
            }

            if ($match->hasSingleParticipantBye()) {
                $match->winner_participant_id = $match->home_participant_id ?: $match->away_participant_id;
                $match->completed_at = now();

                return;
            }

            if ($match->home_score === null || $match->away_score === null) {
                $match->winner_participant_id = null;
                $match->completed_at = null;

                return;
            }

            $match->ensureScoresAreValid($match->home_score, $match->away_score);

            $winner = $match->decideWinner();
            $match->winner_participant_id = $winner;
            $match->completed_at = $winner ? now() : null;
        });

        static::saved(function (KnockoutMatch $match) {
            $match->syncNextMatchSlot();
        });
    }

    public function suppressAutoBye(): void
    {
        $this->suppressAutoBye = true;
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(KnockoutRound::class, 'knockout_round_id');
    }

    public function knockout(): BelongsTo
    {
        return $this->belongsTo(Knockout::class);
    }

    public function homeParticipant(): BelongsTo
    {
        return $this->belongsTo(KnockoutParticipant::class, 'home_participant_id');
    }

    public function awayParticipant(): BelongsTo
    {
        return $this->belongsTo(KnockoutParticipant::class, 'away_participant_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(KnockoutParticipant::class, 'winner_participant_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function nextMatch(): BelongsTo
    {
        return $this->belongsTo(self::class, 'next_match_id');
    }

    public function previousMatches(): HasMany
    {
        return $this->hasMany(self::class, 'next_match_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    public function type(): ?KnockoutType
    {
        return $this->knockout?->type;
    }

    public function recordResult(int $homeScore, int $awayScore, User $user): void
    {
        $this->ensureScoresAreValid($homeScore, $awayScore);

        $this->fill([
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'reported_by_id' => $user->id,
        ]);

        $this->save();
    }

    public function clearResult(): void
    {
        $this->fill([
            'home_score' => null,
            'away_score' => null,
            'reported_by_id' => null,
            'completed_at' => null,
            'winner_participant_id' => null,
        ]);

        $this->save();
    }

    public function ensureScoresAreValid(int $homeScore, int $awayScore): void
    {
        if ($homeScore < 0 || $awayScore < 0) {
            $this->scoreValidationError('Scores must be zero or a positive number.');
        }

        if ($homeScore === $awayScore) {
            $this->scoreValidationError('Knockout matches cannot end in a draw.');
        }

        $target = $this->targetScoreToWin();
        $maxFrames = $this->maxFramesAllowed();
        $total = $homeScore + $awayScore;
        $winnerScore = max($homeScore, $awayScore);

        if ($winnerScore < $target) {
            $this->scoreValidationError("Winning participant must reach {$target} points.");
        }

        if ($maxFrames && $total > $maxFrames) {
            $this->scoreValidationError("Total frames cannot exceed {$maxFrames}.");
        }
    }

    public function userCanSubmit(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $type = $this->type();

        return match ($type) {
            KnockoutType::Singles => $this->homeParticipant?->includesPlayer($user)
                || $this->awayParticipant?->includesPlayer($user),
            KnockoutType::Doubles => $this->homeParticipant?->includesPlayer($user)
                || $this->awayParticipant?->includesPlayer($user),
            KnockoutType::Team => $this->userIsTeamRepresentative($user),
            default => false,
        };
    }

    public function title(): string
    {
        return trim("{$this->homeParticipant?->display_name} vs {$this->awayParticipant?->display_name}") ?: 'TBC';
    }

    public function bestOfValue(): int
    {
        return $this->best_of
            ?? $this->round?->bestOfValue()
            ?? $this->knockout?->bestOfValue()
            ?? $this->type()?->defaultBestOf()
            ?? 5;
    }

    public function maxFramesAllowed(): int
    {
        $type = $this->type();

        return $type === KnockoutType::Team ? 10 : $this->bestOfValue();
    }

    public function targetScoreToWin(): int
    {
        $type = $this->type();

        return $type === KnockoutType::Team
            ? self::TEAM_TARGET_FRAMES
            : (int) ceil($this->bestOfValue() / 2);
    }

    private function hasSingleParticipantBye(): bool
    {
        if ($this->suppressAutoBye) {
            return false;
        }

        $participants = collect([$this->home_participant_id, $this->away_participant_id])->filter();

        return $participants->count() === 1 && $this->home_score === null && $this->away_score === null;
    }

    private function scoreValidationError(string $message): void
    {
        throw ValidationException::withMessages([
            'home_score' => $message,
            'away_score' => $message,
        ]);
    }

    private function venueConflictsWithParticipants(): bool
    {
        if (! $this->venue_id) {
            return false;
        }

        $venueId = $this->venue_id;

        return collect([$this->homeParticipant, $this->awayParticipant])
            ->filter()
            ->contains(function (KnockoutParticipant $participant) use ($venueId) {
                $participant->loadMissing('team', 'playerOne.team', 'playerTwo.team');

                $teamVenueIds = collect([
                    $participant->team?->venue_id,
                    $participant->playerOne?->team?->venue_id,
                    $participant->playerTwo?->team?->venue_id,
                ])->filter();

                if ($teamVenueIds->isEmpty()) {
                    return false;
                }

                return $teamVenueIds->contains(fn ($id) => (int) $id === (int) $venueId);
            });
    }

    private function decideWinner(): ?int
    {
        if ($this->home_score === null || $this->away_score === null) {
            return null;
        }

        if ($this->home_score === $this->away_score) {
            return null;
        }

        return $this->home_score > $this->away_score
            ? $this->home_participant_id
            : $this->away_participant_id;
    }

    private function syncNextMatchSlot(): void
    {
        if (! $this->next_match_id || ! $this->next_slot) {
            return;
        }

        $next = $this->nextMatch;

        if (! $next) {
            return;
        }

        $column = $this->next_slot === 'home' ? 'home_participant_id' : 'away_participant_id';

        if ($this->winner_participant_id) {
            $next->update([$column => $this->winner_participant_id]);
        } elseif ($this->previousWinnerId && $next->{$column} === $this->previousWinnerId) {
            $next->update([$column => null]);
        }
    }

    private function userIsTeamRepresentative(User $user): bool
    {
        $teamIds = collect([$this->homeParticipant?->team_id, $this->awayParticipant?->team_id])->filter();

        if ($teamIds->isEmpty()) {
            return false;
        }

        return $teamIds->contains($user->team_id) && ($user->isCaptain() || $user->isTeamAdmin());
    }
}
