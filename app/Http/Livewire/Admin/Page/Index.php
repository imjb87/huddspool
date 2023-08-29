<?php

namespace App\Http\Livewire\Admin\Page;

use Livewire\Component;
use App\Models\Page;

class Index extends Component
{
    public $pages;

    public function mount()
    {
        $this->pages = Page::all();
    }

    public function render()
    {
        return view('livewire.admin.page.index')
            ->layout('layouts.admin');
    }
}
