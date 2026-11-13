<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeReply extends Model
{
    protected $fillable = [
        'demande_id', 'client_id', 'annonce_id',
        'message', 'contact_phone', 'status', 'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function demande(): BelongsTo { return $this->belongsTo(Demande::class); }
    public function annonce(): BelongsTo { return $this->belongsTo(Annonces::class); }
    public function client(): BelongsTo  { return $this->belongsTo(Client::class); }
}
