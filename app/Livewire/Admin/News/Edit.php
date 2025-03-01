<?php

namespace App\Livewire\Admin\News;

use Livewire\Component;
use App\Models\News;

class Edit extends Component
{
    public News $news;

    protected $rules = [
        'news.title' => 'required|string|min:3|max:255',
        'news.content' => 'required|string'
    ];

    public function mount(News $news)
    {
        $this->news = $news;
    }

    public function save()
    {
        $this->validate();

        $this->news->save();

        session()->flash('success', 'News updated successfully!');

        return redirect()->route('admin.news.index');
    }

    public function render()
    {
        return view('livewire.admin.news.edit');
    }
}
