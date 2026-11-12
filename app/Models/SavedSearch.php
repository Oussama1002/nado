<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    protected $fillable = [
        'client_id', 'label', 'params', 'notify_new', 'channel', 'last_notified_at',
    ];

    protected $casts = [
        'params'           => 'array',
        'notify_new'       => 'boolean',
        'last_notified_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /** Libellé lisible : le label saisi, sinon un résumé des filtres. */
    public function displayLabel(): string
    {
        if ($this->label) {
            return $this->label;
        }
        $p = (array) $this->params;
        $bits = array_filter([
            $p['marque'] ?? null,
            $p['modele'] ?? null,
            isset($p['prix']) ? '≤ ' . number_format((int) $p['prix'], 0, ',', ' ') . ' MAD' : null,
            $p['ville'] ?? null,
        ]);
        return $bits ? implode(' · ', $bits) : __('messages.catalog.search');
    }
}
