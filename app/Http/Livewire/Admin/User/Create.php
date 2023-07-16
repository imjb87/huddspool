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
        ];
    }

    protected $messages = [
        'user.name.required' => 'The user name is required',
    ];

    public function mount()
    {
        $this->teams = Team::orderBy('name')->get();
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
