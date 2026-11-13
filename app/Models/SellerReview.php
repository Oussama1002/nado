<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SellerReview extends Model
{
    protected $fillable = [
        'reviewer_client_id', 'seller_client_id', 'annonce_id',
        'rating', 'title', 'body', 'verified', 'status',
    ];

    protected $casts = [
        'rating'   => 'integer',
        'verified' => 'boolean',
    ];

    public function reviewer()
    {
        return $this->belongsTo(Client::class, 'reviewer_client_id');
    }

    public function seller()
    {
        return $this->belongsTo(Client::class, 'seller_client_id');
    }

    public function scopeApproved($q)
    {
        return $q->where('status', 'approved');
    }

    /** Note agrégée d'un vendeur (moyenne + nombre), cachée 10 min. */
    public static function aggregateFor(int $sellerClientId): array
    {
        return Cache::remember("seller_rating_{$sellerClientId}", 600, function () use ($sellerClientId) {
            $rows = static::where('seller_client_id', $sellerClientId)->where('status', 'approved');
            $count = (clone $rows)->count();
            $avg   = $count ? round((clone $rows)->avg('rating'), 1) : 0;
            return ['count' => $count, 'avg' => (float) $avg];
        });
    }

    public static function flushAggregate(int $sellerClientId): void
    {
        Cache::forget("seller_rating_{$sellerClientId}");
    }
}
