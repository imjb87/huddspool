<?php

namespace App\Observers;

use App\Models\KnockoutMatch;
use Illuminate\Support\Facades\Log;

class KnockoutMatchObserver
{
    public function creating(KnockoutMatch $knockoutMatch)
    {
        Log::info('KnockoutMatch creating event triggered.', ['match_id' => $knockoutMatch->id]);
        // Assign the next position when creating a new match
        $nextPosition = KnockoutMatch::whereHas('round', function($query) use ($knockoutMatch) {
            $query->where('knockout_id', $knockoutMatch->round->knockout->id);
        })->count() + 1;

        $knockoutMatch->position = $nextPosition;
    }

    public function updated(KnockoutMatch $knockoutMatch)
    {
        Log::info('KnockoutMatch updated event triggered.', ['match_id' => $knockoutMatch->id]);
        $this->reorderPositions($knockoutMatch->round->knockout->id);
    }

    public function deleted(KnockoutMatch $knockoutMatch)
    {
        Log::info('KnockoutMatch deleted event triggered.', ['match_id' => $knockoutMatch->id]);
        $this->reorderPositions($knockoutMatch->round->knockout->id);
    }

    protected function reorderPositions($knockoutId)
    {
        Log::info('Reordering positions for knockout.', ['knockout_id' => $knockoutId]);
        $matches = KnockoutMatch::whereHas('round', function($query) use ($knockoutId) {
            $query->where('knockout_id', $knockoutId);
        })->orderBy('created_at')->get();

        Log::info($matches);

        foreach ($matches as $index => $match) {
            $match->position = $index + 1;
            $match->save();
        }
    }
}
