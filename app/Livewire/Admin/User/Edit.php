<?php

namespace App\Livewire\Admin\User;

use Livewire\Component;
use App\Models\User;
use App\Models\Team;

class Edit extends Component
{
    public User $user;
    public $teams;

    protected function rules()
    {
        return [
            'user.name' => 'required|string',
            'user.email' => 'nullable|email',
            'user.telephone' => 'nullable|string',
            'user.is_admin' => 'nullable|boolean',
            'user.team_id' => ['nullable', 'integer'],
            'user.role' => 'nullable|integer',
        ];
    }

    protected $messages = [
        'user.name.required' => 'The user name is required',
    ];

    public function mount()
    {
        $this->teams = Team::all();
    }

    public function save()
    {
        $this->validate();

        $this->user->save();

        return redirect()->route('admin.users.show', $this->user);
    }

    public function render()
    {
        return view('livewire.admin.user.edit')->layout('layouts.admin');
    }
}
