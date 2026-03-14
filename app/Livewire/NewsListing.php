<?php

namespace App\Livewire;

use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NewsListing extends Component
{
    #[Computed]
    public function news(): Collection
    {
        return News::query()
            ->orderByDesc('created_at')
            ->take(3)
            ->get();
    }

    public function render(): View
    {
        return view('livewire.news-listing', [
            'news' => $this->news,
        ]);
    }
}
