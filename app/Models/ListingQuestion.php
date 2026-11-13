<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingQuestion extends Model
{
    protected $fillable = [
        'annonce_id', 'asker_client_id', 'question', 'answer', 'answered_by', 'status', 'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonces::class, 'annonce_id');
    }

    public function asker()
    {
        return $this->belongsTo(Client::class, 'asker_client_id');
    }

    public function scopePublicApproved($q)
    {
        return $q->where('status', 'approved')->where('is_public', true);
    }
}
