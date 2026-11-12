<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Une vérification de situation légale (opposition NARSA) déclarée par un vendeur.
 *
 * Vie privée : on ne stocke QUE le statut + la capture d'écran (disque privé).
 * Jamais le nom/adresse du propriétaire (cf. règle OCR carte grise, AGENTS.md).
 */
class LegalCheck extends Model
{
    protected $fillable = [
        'annonce_id',
        'client_id',
        'matricule_number',
        'matricule_letter',
        'matricule_region',
        'matricule_full',
        'declared_status',
        'contact_channel',
        'contact_whatsapp',
        'screenshot_path',
        'checked_at',
        'admin_status',
        'admin_id',
        'reviewed_at',
        'notes',
    ];

    protected $casts = [
        'checked_at'  => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function annonce()
    {
        return $this->belongsTo(Annonces::class, 'annonce_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /** Résultat propre (aucune opposition) ? */
    public function isClear(): bool
    {
        return $this->declared_status === 'clear';
    }

    /** Confirmé par un admin ? */
    public function isConfirmed(): bool
    {
        return $this->admin_status === 'confirmed';
    }
}
