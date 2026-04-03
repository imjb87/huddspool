<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('news.index', [
            'title' => 'News',
            'news' => $this->news(),
        ]);
    }

    public function show(News $news): View
    {
        return view('news.show', [
            'title' => $news->title,
            'newsArticle' => $news->load('author:id,name'),
        ]);
    }

    private function news(): Collection
    {
        return News::query()
            ->with('author:id,name')
            ->latest()
            ->get();
    }
}
