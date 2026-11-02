<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Song extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['title', 'artist', 'file_path', 'cover_path', 'duration', 'sort_order', 'active'];

    protected $casts = [
        'active'     => 'boolean',
        'sort_order' => 'integer',
    ];

    /** Titres actifs, dans l'ordre d'affichage voulu. */
    public function scopeActiveOrdered($query)
    {
        return $query->where('active', 1)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
