<?php

namespace App\Http\Livewire\Admin\User;

use Livewire\Component;
use App\Models\User;
use App\Models\Team;

class Create extends Component
{
    public $roles;
    public $teams;
    public $user;

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

    public function mount($team_id = null)
    {
        $this->teams = Team::orderBy('name')->get();

        $this->user['team_id'] = $team_id;
    }

    public function save()
    {
        $this->validate();

        $user = User::create($this->user);

        if ($user->team_id) {
            return redirect()->route('admin.teams.show', $user->team_id);
        } else {
            return redirect()->route('admin.users.show', $user);
        }
    }

    public function render()
    {
        return view('livewire.admin.user.create')->layout('layouts.admin');
    }
}
