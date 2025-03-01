<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\News;

class NewsListing extends Component
{
    public function render()
    {
        return view('livewire.news-listing', [
            'news' => News::orderBy('created_at', 'desc')->take(3)->get()
        ])->layout('layouts.app');
    }
}
