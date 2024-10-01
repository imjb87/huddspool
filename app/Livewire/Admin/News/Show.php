<?php

namespace App\Livewire\Admin\News;

use Livewire\Component;
use App\Models\News;

class Show extends Component
{
    public News $news;

    public function mount(News $news)
    {
        $this->news = $news;
    }

    public function render()
    {
        return view('livewire.admin.news.show')->layout('layouts.admin');
    }
}
