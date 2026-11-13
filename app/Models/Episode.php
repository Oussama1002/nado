<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = [
        'title', 'description', 'audio_path', 'cover_path', 'duration',
        'published_at', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active'    => 'boolean',
    ];

    public function scopePublished($q)
    {
        return $q->where('is_active', true)
            ->where(function ($x) {
                $x->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }
}
