<?php

namespace App\Services;

use App\Models\Annonces;
use App\Models\Brand;
use App\Models\Demande;
use App\Models\DemandeReply;
use App\Models\Modele;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Rapproche une nouvelle annonce des demandes (recherches) qui lui correspondent
 * et ajoute l'annonce en proposition sur chacune, puis notifie le demandeur.
 */
class DemandeMatcher
{
    /** Ajoute l'annonce aux demandes correspondantes. Renvoie le nb de demandes touchées. */
    public static function matchAnnonce(Annonces $annonce): int
    {
        // Résout marque/modèle en IDs (les nouvelles lignes stockent déjà des IDs,
        // les migrées des noms) pour comparer aux demandes (brand_id / modele_id).
        $brandId  = self::resolveBrandId($annonce->mark);
        $modeleId = self::resolveModeleId($annonce->modele);
        if (! $brandId || ! $modeleId) {
            return 0;
        }

        $year = (int) ($annonce->year ?? 0);
        $prix = (float) ($annonce->prix ?? 0);
        $km   = (int) ($annonce->km ?? 0);

        $demandes = Demande::query()
            ->whereIn('status', ['approved', 'pending'])
            ->where(fn ($x) => $x->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where('brand_id', $brandId)
            ->where('modele_id', $modeleId)
            ->when($year > 0, fn ($q) => $q
                ->where(fn ($w) => $w->whereNull('year_min')->orWhere('year_min', '<=', $year))
                ->where(fn ($w) => $w->whereNull('year_max')->orWhere('year_max', '>=', $year)))
            ->when($prix > 0, fn ($q) => $q
                ->where(fn ($w) => $w->whereNull('price_max')->orWhere('price_max', '>=', $prix))
                ->where(fn ($w) => $w->whereNull('price_min')->orWhere('price_min', '<=', $prix)))
            ->when($km > 0, fn ($q) => $q
                ->where(fn ($w) => $w->whereNull('km_max')->orWhere('km_max', '>=', $km)))
            ->get();

        $touched = 0;
        foreach ($demandes as $demande) {
            // Évite les doublons : une seule proposition de cette annonce par demande.
            $exists = DemandeReply::where('demande_id', $demande->id)
                ->where('annonce_id', $annonce->id)->exists();
            if ($exists) {
                continue;
            }

            DemandeReply::create([
                'demande_id'    => $demande->id,
                'client_id'     => null,
                'annonce_id'    => $annonce->id,
                'message'       => 'Annonce NADO correspondant à votre recherche.',
                'contact_phone' => $annonce->phone ?? null,
                'status'        => 'visible',
            ]);
            $demande->increment('replies_count');
            $touched++;

            self::notifyOwner($demande, $annonce);
        }

        return $touched;
    }

    private static function notifyOwner(Demande $demande, Annonces $annonce): void
    {
        $email = $demande->email ?: $demande->client?->email;
        if (empty($email)) {
            return;
        }

        try {
            $brand = Brand::find(self::resolveBrandId($annonce->mark))?->name ?? '';
            $model = Modele::find(self::resolveModeleId($annonce->modele))?->name ?? '';
            $label = trim($brand . ' ' . $model) ?: 'un véhicule';
            $url   = url('/achat/voiture/' . str_replace(' ', '', (string) $annonce->mark) . '_'
                   . str_replace(' ', '', (string) $annonce->modele) . '_' . $annonce->id . '.html');

            $body = "Bonjour {$demande->nom},\n\n"
                . "Une nouvelle annonce correspond à votre recherche : {$label} ("
                . ($annonce->year ?? '—') . ") — "
                . number_format((float) ($annonce->prix ?? 0), 0, ',', ' ') . " DH.\n\n"
                . "Voir l'annonce :\n{$url}\n\n"
                . "L'équipe NADO.";

            Mail::raw($body, function ($m) use ($email) {
                $m->to($email)->subject('Une annonce correspond à votre recherche — NADO');
            });
        } catch (\Throwable $e) {
            Log::warning('DemandeMatcher notify failed: ' . $e->getMessage());
        }
    }

    private static function resolveBrandId($mark): ?int
    {
        if (is_numeric($mark)) {
            return (int) $mark;
        }
        return $mark ? Brand::whereRaw('UPPER(name) = ?', [mb_strtoupper((string) $mark)])->value('id') : null;
    }

    private static function resolveModeleId($modele): ?int
    {
        if (is_numeric($modele)) {
            return (int) $modele;
        }
        return $modele ? Modele::whereRaw('UPPER(name) = ?', [mb_strtoupper((string) $modele)])->value('id') : null;
    }
}
