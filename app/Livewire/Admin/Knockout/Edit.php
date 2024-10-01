<?php

namespace App\Livewire\Admin\Knockout;

use Livewire\Component;
use App\Models\Knockout;

class Edit extends Component
{
    protected function rules()
    {
        return [
            'knockout.name' => 'required|string',
            'knockout.type' => 'required|string|in:singles,doubles,team',
        ];
    }

    protected $messages = [
        'knockout.name.required' => 'The knockout name is required.',
        'knockout.type.required' => 'The knockout type is required.',
    ];

    public function mount(Knockout $knockout)
    {
        $this->knockout = $knockout;
    }

    public function save()
    {
        $this->validate();

        $this->knockout->save();

        return redirect()->route('admin.knockouts.show', $this->knockout);
    }

    public function render()
    {
        return view('livewire.admin.knockout.edit')->layout('layouts.admin');
    }
}
