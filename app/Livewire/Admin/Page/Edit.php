<?php

namespace App\Livewire\Admin\Page;

use Livewire\Component;
use App\Models\Page;
use Illuminate\Support\Str;

class Edit extends Component
{
    public $page;

    protected $rules = [
        'page.title' => 'required',
        'page.slug' => 'required',
        'page.content' => 'required',
    ];

    public function mount(Page $page)
    {
        $this->page = $page;
    }

    public function save()
    {
        $this->validate();

        $this->page->save();

        session()->flash('success', 'Page updated successfully.');

        return redirect()->route('admin.pages.show', $this->page);
    }

    public function render()
    {
        return view('livewire.admin.page.edit')
            ->layout('layouts.admin');
    }
}
