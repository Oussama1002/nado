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

    /**
     * Données structurées d'une proposition de véhicule, pour l'affichage en carte.
     * Utilise `meta` (nouvelles propositions) ou, à défaut, parse l'ancien message
     * texte (anciennes propositions, sans photos). Renvoie null si ce n'est pas
     * une proposition de véhicule.
     */
    public function proposalData(): ?array
    {
        if (is_array($this->meta) && !empty($this->meta)) {
            $meta = $this->meta;
            if (empty($meta['images'])) {
                $meta['images'] = $this->uploadedProposalImages();
            }
            return $meta;
        }

        $msg = (string) $this->message;
        if (mb_stripos($msg, 'Proposition de véhicule') === false) {
            return null;
        }

        $data = ['images' => $this->uploadedProposalImages()];
        if (preg_match('/-\s*(.+?)\s*\((\d{4})\)/u', $msg, $m)) {
            $data['title'] = trim($m[1]);
            $data['year']  = $m[2];
        }
        if (preg_match('/([\d\s\x{00A0}]+)\s*km/u', $msg, $m)) {
            $data['km'] = (int) preg_replace('/\D/', '', $m[1]);
        }
        if (preg_match('/·\s*([\d\s\x{00A0}]+)\s*DH/u', $msg, $m)) {
            $data['prix'] = (int) preg_replace('/\D/', '', $m[1]);
        }
        if (preg_match('/Ville\s*:\s*(.+)/u', $msg, $m))     { $data['city']       = trim($m[1]); }
        if (preg_match('/Carburant\s*:\s*(.+)/u', $msg, $m)) { $data['carburant']  = trim($m[1]); }
        if (preg_match('/Bo[iî]te\s*:\s*(.+)/u', $msg, $m))  { $data['box']        = trim($m[1]); }
        if (preg_match('/Contact\s*:\s*(.+?)(?:\s*—.*)?$/mu', $msg, $m)) { $data['proposer_name'] = trim($m[1]); }

        return $data;
    }

    /**
     * Récupère les photos déposées pour cette proposition depuis le disque
     * (storage/app/public/propositions/{demande_id}), pour les anciennes
     * propositions dont les chemins n'avaient pas été enregistrés en base.
     */
    protected function uploadedProposalImages(): array
    {
        try {
            $dir = 'propositions/' . $this->demande_id;
            $files = \Illuminate\Support\Facades\Storage::disk('public')->files($dir);
            return array_values(array_filter($files, fn ($f) => preg_match('/\.(jpe?g|png|webp|gif)$/i', $f)));
        } catch (\Throwable $e) {
            return [];
        }
    }
}
