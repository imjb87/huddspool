<?php

namespace App\Livewire\Admin\KnockoutMatch;

use Livewire\Component;
use App\Models\Round;
use App\Models\KnockoutMatch;
use App\Models\User;
use App\Models\Venue;

class Create extends Component
{
    public Round $round;
    public $match;
    public $users;
    public $venues;

    protected function rules()
    {
        return [
            'match.venue_id' => 'required|integer',
        ];
    }    

    public function mount(Round $round)
    {
        $this->round = $round;
            
        $this->users = User::orderBy('name')->get();
        $this->venues = Venue::orderBy('name')->get();
    }

    public function save()
    {
        $this->validate();

        $match = KnockoutMatch::create([
            'round_id' => $this->round->id,
            'venue_id' => $this->match['venue_id'],
        ]);

        if ( $this->round->knockout->type == 'singles' ) {
            if ( !empty($this->match['participant1_id']) ) {
                $match->participants()->create([
                    'participantable_id' => $this->match['participant1_id'],
                    'participantable_type' => 'App\Models\User',
                    'role' => 'player1',
                ]);
            }
            if ( !empty($this->match['participant2_id']) ) {
                $match->participants()->create([
                    'participantable_id' => $this->match['participant2_id'],
                    'participantable_type' => 'App\Models\User',
                    'role' => 'player2',
                ]);
            }
        }

        if ( !empty($this->match['depends_on1_id']) ) {
            $match->dependancies()->attach($this->match['depends_on1_id']);
        }

        if ( !empty($this->match['depends_on2_id']) ) {
            $match->dependancies()->attach($this->match['depends_on2_id']);
        }

        return redirect()->route('admin.matches.show', $match);
    }

    public function render()
    {
        return view('livewire.admin.knockoutmatch.create')->layout('layouts.admin');
    }
}
