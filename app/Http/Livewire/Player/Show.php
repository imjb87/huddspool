<?php

namespace App\Http\Livewire\Player;

use Livewire\Component;
use App\Models\User;
use App\Models\Frame;

class Show extends Component
{
    public User $player;
    public $frames = [];
    public $played;
    public $won;
    public $lost;

    public function mount(User $player)
    {
        $this->player = $player;
        
        $this->frames = Frame::where('home_player_id', $this->player->id)
            ->orWhere('away_player_id', $this->player->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->played = $this->frames->count();

        $this->frames->each(function ($frame) {
            if ($frame->home_player_id == $this->player->id) {
                if ($frame->home_score > $frame->away_score) {
                    $this->won++;
                }
            } else {
                if ($frame->away_score > $frame->home_score) {
                    $this->won++;
                }
            }
        });

        $this->lost = $this->played - $this->won;

    }

    public function render()
    {
        return view('livewire.player.show');
    }
}
