<?php

namespace App\Livewire\Page;

use Livewire\Component;
use App\Models\Page;

class Show extends Component
{
    public $slug;
    public $page;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->page = Page::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.page.show')
            ->layout('layouts.app');
    }
}
