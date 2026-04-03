<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_index_lists_published_articles_with_excerpt_links(): void
    {
        $author = User::factory()->create(['name' => 'John Bell']);

        $article = News::withoutEvents(function () use ($author): News {
            return News::query()->create([
                'title' => 'Fixture dates updated',
                'slug' => 'fixture-dates-updated',
                'content' => "Several fixture dates have changed.\nPlease check the full list before travelling.",
                'author_id' => $author->id,
            ]);
        });

        $response = $this->get(route('news.index'));

        $response->assertOk()
            ->assertSee('data-news-index-list', false)
            ->assertSeeText('Fixture dates updated')
            ->assertSeeText('Several fixture dates have changed. Please check the full list before travelling.')
            ->assertSee(route('news.show', $article), false)
            ->assertSeeText('See more');
    }

    public function test_news_show_uses_slug_route_and_renders_share_button(): void
    {
        $author = User::factory()->create(['name' => 'John Bell']);

        $article = News::withoutEvents(function () use ($author): News {
            return News::query()->create([
                'title' => 'Captains meeting',
                'slug' => 'captains-meeting',
                'content' => "Captains should arrive for 7:15pm.\nImportant league notices will be covered before the break.",
                'author_id' => $author->id,
            ]);
        });

        $response = $this->get(route('news.show', $article));

        $response->assertOk()
            ->assertSee('data-news-show', false)
            ->assertSee('data-news-share-button', false)
            ->assertSee('navigator.share', false)
            ->assertSeeText('Captains meeting')
            ->assertSeeText('John Bell')
            ->assertSeeText('Captains should arrive for 7:15pm.')
            ->assertSeeText('Important league notices will be covered before the break.');

        $this->assertSame('/news/captains-meeting', route('news.show', $article, false));
    }
}
