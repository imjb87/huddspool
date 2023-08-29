<?php

namespace App\Http\Livewire\Admin\Page;

use Livewire\Component;
use App\Models\Page;
use Illuminate\Support\Str;

class Create extends Component
{
    public Page $page;

    public function mount()
    {
        $this->page = new Page();
    }

    protected $rules = [
        'page.title' => 'required',
        'page.slug' => 'required',
        'page.content' => 'required',
    ];

    public function save()
    {
        $this->validate();

        $page = Page::create(
            $this->page->toArray()
        );

        session()->flash('success', 'Page created successfully.');

        return redirect()->route('admin.pages.show', $page);
    }

    public function render()
    {
        return view('livewire.admin.page.create')
            ->layout('layouts.admin');
    }
}
