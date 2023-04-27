<?php

namespace App\Http\Livewire\Admin\User;

use Livewire\Component;
use App\Models\User;
use App\Http\Controllers\Auth\InviteController;

class Show extends Component
{
    public User $user;
    
    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function delete()
    {
        $this->user->delete();

        return redirect()->route('admin.users.index');
    }

    public function invite()
    {
        $invite = new InviteController();
        $invite->send($this->user);

        session()->flash('message', 'Invitation sent to ' . $this->user->email);
    }

    public function render()
    {
        return view('livewire.admin.user.show')->layout('layouts.admin');
    }
}
