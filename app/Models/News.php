<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'author_id',
    ];

    protected static function booted()
    {
        static::creating(function ($news) {
            $news->author_id = auth()->id();
        });
    }

    /**
     * Get the author that owns the news.
     */
    public function author()
    {
        return $this->belongsTo(User::class);
    }
}
