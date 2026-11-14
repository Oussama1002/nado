<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demande extends Model
{
    protected $fillable = [
        'client_id', 'nom', 'telephone', 'email',
        'brand_id', 'modele_id', 'ville_id', 'color_id',
        'year_min', 'year_max', 'price_min', 'price_max', 'km_max',
        'boite', 'carburant', 'raw_query', 'comment',
        'status', 'source', 'ip_hash', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function brand(): BelongsTo   { return $this->belongsTo(Brand::class); }
    public function modele(): BelongsTo  { return $this->belongsTo(Modele::class); }
    public function ville(): BelongsTo   { return $this->belongsTo(Ville::class); }
    public function color(): BelongsTo   { return $this->belongsTo(Color::class); }
    public function client(): BelongsTo  { return $this->belongsTo(Client::class); }
    public function replies(): HasMany   { return $this->hasMany(DemandeReply::class); }

    public function scopePublic($q)
    {
        return $q->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function matchesAnnonce(Annonces $a): bool
    {
        if ($this->brand_id  && (int) $a->mark   !== (int) $this->brand_id)  return false;
        if ($this->modele_id && (int) $a->modele !== (int) $this->modele_id) return false;
        if ($this->ville && $this->ville->name && $a->city && strcasecmp($a->city, $this->ville->name) !== 0) return false;
        if ($this->price_min && $a->prix < $this->price_min) return false;
        if ($this->price_max && $a->prix > $this->price_max) return false;
        if ($this->year_min  && $a->year < $this->year_min)  return false;
        if ($this->year_max  && $a->year > $this->year_max)  return false;
        if ($this->km_max    && $a->km   > $this->km_max)    return false;
        return true;
    }

    public function maskedTelephone(): string
    {
        $p = preg_replace('/\D+/', '', (string) $this->telephone);
        if (strlen($p) < 6) return $this->telephone;
        return substr($p, 0, 2) . ' ** ** ' . substr($p, -4, 2) . ' ' . substr($p, -2);
    }
}
