<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (News $news): void {
            if ($news->isDirty('title') || blank($news->slug)) {
                $news->slug = $news->generateSlug($news->title, $news->id);
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'published_at',
        'author_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the author that owns the news.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): void
    {
        $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->published_at instanceof Carbon
            && $this->published_at->lte(now());
    }

    public function excerpt(int $limit = 180): string
    {
        return Str::limit(preg_replace('/\s+/', ' ', trim($this->content)) ?? '', $limit);
    }

    private function generateSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title) ?: 'news';
        $original = $slug;
        $index = 1;

        while (self::query()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$original}-{$index}";
            $index++;
        }

        return $slug;
    }
}
