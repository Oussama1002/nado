<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ModelGuide extends Model
{
    protected $fillable = [
        'brand_id', 'modele_id', 'slug', 'title', 'title_ar', 'intro', 'intro_ar',
        'body_fr', 'body_ar', 'image', 'specs', 'meta_title', 'meta_description', 'is_active', 'views',
    ];

    protected $casts = [
        'specs'     => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function ($guide) {
            if (empty($guide->slug)) {
                $guide->slug = Str::slug($guide->title);
            }
        });
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function modele()
    {
        return $this->belongsTo(Modele::class, 'modele_id');
    }

    /** Titre selon la locale (fallback FR). */
    public function localTitle(): string
    {
        return app()->getLocale() === 'ar' && $this->title_ar ? $this->title_ar : $this->title;
    }

    /** Intro selon la locale (fallback FR). */
    public function localIntro(): ?string
    {
        return app()->getLocale() === 'ar' && $this->intro_ar ? $this->intro_ar : $this->intro;
    }

    /** Corps selon la locale (fallback FR). */
    public function localBody(): ?string
    {
        return app()->getLocale() === 'ar' && $this->body_ar ? $this->body_ar : $this->body_fr;
    }
}
