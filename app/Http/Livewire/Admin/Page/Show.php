<?php

namespace App\Http\Livewire\Admin\Page;

use Livewire\Component;
use App\Models\Page;

class Show extends Component
{
    public $page;

    public function mount(Page $page)
    {
        $this->page = $page;
    }

    public function render()
    {
        return view('livewire.admin.page.show')
            ->layout('layouts.admin');
    }
}
