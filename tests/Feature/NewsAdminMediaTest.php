<?php

namespace Tests\Feature;

use App\Filament\Resources\NewsResource\Pages\EditNews;
use App\Models\News;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class NewsAdminMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_a_featured_image_from_the_filament_news_edit_page(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $article = News::withoutEvents(fn (): News => News::query()->create([
            'title' => 'Fixture dates updated',
            'slug' => 'fixture-dates-updated',
            'content' => 'Several fixture dates have changed.',
            'published_at' => now(),
            'author_id' => $admin->id,
        ]));

        Filament::setCurrentPanel('admin');

        Livewire::actingAs($admin)
            ->test(EditNews::class, [
                'record' => $article->getRouteKey(),
            ])
            ->set('data.featured_image', UploadedFile::fake()->image('fixture-cover.jpg'))
            ->fillForm([
                'title' => $article->title,
                'publication_status' => 'published',
                'content' => $article->content,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $media = $article->fresh()->getFirstMedia('featured-images');

        $this->assertNotNull($media);
        Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
    }
}
