<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerReview extends Model
{
    protected $fillable = [
        'modele_id', 'reviewer_client_id', 'rating', 'title', 'body', 'status',
    ];

    protected $casts = ['rating' => 'integer'];

    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }

    public function reviewer()
    {
        return $this->belongsTo(Client::class, 'reviewer_client_id');
    }

    /** Note agrégée pour un modèle. */
    public static function aggregateFor(int $modeleId): array
    {
        $rows = static::where('modele_id', $modeleId)->where('status', 'approved');
        $count = (clone $rows)->count();
        $avg   = $count ? round((clone $rows)->avg('rating'), 1) : 0;
        return ['count' => $count, 'avg' => (float) $avg];
    }
}
