<?php

namespace App\Livewire\Admin\KnockoutMatch;

use Livewire\Component;
use App\Models\KnockoutMatch;
use App\Models\User;
use App\Models\Venue;

class Edit extends Component
{
    public $match;
    public $users;
    public $venues;
    public $participant1_id;
    public $participant2_id;
    public $depends_on1_id;
    public $depends_on2_id;

    protected function rules()
    {
        return [
            'match.venue_id' => 'required|integer',
        ];
    }    

    public function mount(KnockoutMatch $match)
    {
        $this->match = $match;
        $this->participant1_id = $match->participants->where('role', 'player1')->first()->participantable_id ?? null;
        $this->participant2_id = $match->participants->where('role', 'player2')->first()->participantable_id ?? null;
        $this->depends_on1_id = $match->dependancies->first()->id ?? null;
        $this->depends_on2_id = $match->dependancies->last()->id ?? null;
    
        // Load options for select inputs
        $this->users = User::orderBy('name')->get();
        $this->venues = Venue::orderBy('name')->get();
    }    

    public function save()
    {
        $this->validate();

        $this->match->save();
        $this->match->touch();

        $this->match->participants()->delete();

        if ( $this->match->round->knockout->type == 'singles' ) {
            if ( !empty($this->participant1_id) ) {
                $this->match->participants()->create([
                    'participantable_id' => $this->participant1_id,
                    'participantable_type' => 'App\Models\User',
                    'role' => 'player1',
                ]);
            }
            if ( !empty($this->participant2_id) ) {
                $this->match->participants()->create([
                    'participantable_id' => $this->participant2_id,
                    'participantable_type' => 'App\Models\User',
                    'role' => 'player2',
                ]);
            }
        }

        $this->match->dependancies()->detach();

        if ( !empty($this->depends_on1_id) ) {
            $this->match->dependancies()->attach($this->depends_on1_id);
        }

        if ( !empty($this->depends_on2_id) ) {
            $this->match->dependancies()->attach($this->depends_on2_id);
        }

        return redirect()->route('admin.matches.show', $this->match);
    }

    public function render()
    {
        return view('livewire.admin.knockoutmatch.edit')->layout('layouts.admin');
    }
}
