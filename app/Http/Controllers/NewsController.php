<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('news.index', [
            'title' => 'News',
            'news' => News::query()
                ->published()
                ->with('author:id,name', 'media')
                ->latest('published_at')
                ->get(),
        ]);
    }

    public function show(News $news): View
    {
        if (! $news->isPublished()) {
            throw new NotFoundHttpException;
        }

        return view('news.show', [
            'title' => $news->title,
            'newsArticle' => $news->load('author:id,name', 'media'),
        ]);
    }
}
