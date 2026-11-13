<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'views',
        'author',
        'image',
        'excerpt',
        'content',
        'is_premium',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    /** Temps de lecture estimé (≈ 200 mots/min), minimum 1 min. */
    public function readingTime(): int
    {
        $words = str_word_count(strip_tags((string) $this->content));
        return max(1, (int) ceil($words / 200));
    }

    protected $casts = [
        'is_premium' => 'boolean',
        'is_active'  => 'boolean',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }
}
