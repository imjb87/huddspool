<?php

namespace App\Http\Livewire\Admin\User;

use Livewire\Component;
use App\Models\User;
use App\Models\Team;
use App\Rules\OneCaptainPerTeam;

class Create extends Component
{
    public $roles;
    public $teams;
    public $user = [
        'name' => '',
        'email' => '',
        'role' => 'player',
        'telephone' => '',
        'team_id' => null,
        'is_admin' => false,
    ];

    protected function rules()
    {
        return [
            'user.name' => 'required|string',
            'user.email' => 'nullable|email',
            'user.role' => 'required|string',
            'user.telephone' => 'nullable|string',
            'user.is_admin' => 'nullable|boolean',
            'user.team_id' => ['nullable', 'integer', new OneCaptainPerTeam($this->user['role'], $this->user['team_id'])],
        ];
    }

    protected $messages = [
        'user.name.required' => 'The user name is required',
        'user.role.required' => 'The user role is required',
    ];

    public function mount()
    {
        $this->teams = Team::all();
    }

    public function save()
    {
        $this->validate();

        $user = User::create($this->user);

        return redirect()->route('admin.users.show', $user);
    }

    public function render()
    {
        return view('livewire.admin.user.create')->layout('layouts.admin');
    }
}
