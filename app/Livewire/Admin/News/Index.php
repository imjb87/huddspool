<?php

namespace App\Livewire\Admin\News;

use Livewire\Component;
use App\Models\News;

class Index extends Component
{
    public function render()
    {
        return view('livewire.admin.news.index', [
            'news' => News::orderBy('created_at', 'desc')->simplePaginate(10)
        ])->layout('layouts.admin');
    }
}
