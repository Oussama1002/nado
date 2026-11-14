<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Annonces extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'annonces';
    
protected $fillable = [
    'phone',
    'pack_id',
    'titre',
    'prix',
    'mark',
    'images', // ✅ add this line

    'modele',
    'matricule',
    'vin',
    'year',
    'km',
    'carburant',
    'box',
    'etat',
    'type', // Occasion / Neuf (vente modal)
    'city',
    'cv',
    'nb_Door',
    'origine',
    'premiere_main',
    'video',
    'reel',
    'bodywork',
    'status',
    'image',
    'imgrleft',
    'imgrtop',
    'imgrmed',
    'imgrbottom',
    'imgrright',
    'imginterior',
    'imgrapport',
    'rapport',
    'co2',
    'certifie',
    'particulie',
    'pro',
    'showroom',
    'finance',
    'jantes_aluminium',
    'airbags',
    'climatisation',
    'gps',
    'toit_ouvrant',
    'double_toit_ouvrant',
    'siege_cuir',
    'radar_recul',
    'camera_recul',
    'vitreselec',
    'abs',
    'eps',
    'rv',
    'lv',
    'cd',
    'ordinateur',
    'verrouillage',
    'garantie',
    'views',
    'active',
    'verified',
    'pack',
    'featured_position',
    'show_in_homepage',
    'homepage_position',
    'is_homepage',
    'colors',

    // nouvelles colonnes sécurité / confort
    'esp',
    'antipatinage',
    'aide_freinage_urgence',
    'antidemarrage_electronique',
    'aide_demarrage_cote',
    'selecteur_mode_conduite',
    'detection_fatigue',
    'maintien_dans_la_voie',
    'detecteur_angle_mort',
    'detecteur_sous_gonflage',
    'fermeture_portes_auto',
    'preparation_isofix',
    'phares_antibrouillard',
    'systeme_alarme',
    'start_stop',
    'regulateur_vitesse',
    'detecteur_pluie',
    'allumage_auto_feux',
    'frein_main_electrique',
    'aide_stationnement',
    'volant_reglable',
    'retros_electriques',
    'retros_rabat_electriques',
    'coffre_electrique',
    'sieges_electriques',
    'sieges_elec_memoire',
    'banquette_rabattable',
    'ecran_tactile',
    'cockpit_digital',
    'commandes_volant',
    'commandes_vocales',
    'reconnaissance_panneaux',
    'affichage_tete_haute',
    'systeme_audio',
    'wifi_bord',
    'bluetooth',
    'compatibilite_smartphone',
    'apple_carplay_android_auto',
    'chargeur_sans_fil',
    'sellerie_similicuir_tissu',
    'sellerie_tissu',
    'volant_cuir',
    'follow_me_home',
    'lumiere_ambiance',
    'feux_jour_led',
    'phares_full_led',
    'toit_panoramique',
    'barres_de_toit',
    'vitres_surteintees',
];


    protected $casts = [
        'verified' => 'boolean',
        // si tu veux forcer les options à int (0/1)
        'jantesA' => 'integer',
        'airbags' => 'integer',
        'climatisation' => 'integer',
        'pro' => 'boolean',
    'images' => 'array', // now Laravel will automatically decode JSON into a PHP array
];
        // ... tu peux ajouter les autres si besoin

    // Relations
    public function autoencheres()
    {
        return $this->hasMany(Autoenchere::class);
    }

    public function favoredBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function clientsfavorites()
    {
        return $this->belongsToMany(
            Client::class,
            'favorites',
            'annonce_id',
            'client_id'
        )->withTimestamps();
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'phone', 'phone');
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'annonces_id');
    }

    public function entretiens()
    {
        return $this->hasMany(Entretien::class, 'annonce_id');
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }

   public function brand()
{
    return $this->belongsTo(Brand::class, 'mark', 'id');
}


    // Model relationship
    public function modeleRelation()
    {
        return $this->belongsTo(Modele::class, 'modele', 'id'); // 'modele' = foreign key in annonces
    }
public function videoAd()
{
    return $this->hasOne(\App\Models\Videosads::class, 'annonce_id');
}

    // Vérifications de situation légale (opposition NARSA, flux guidé).
    public function legalChecks()
    {
        return $this->hasMany(\App\Models\LegalCheck::class, 'annonce_id');
    }

    /** Dernière vérification, quel que soit son statut. */
    public function latestLegalCheck()
    {
        return $this->hasOne(\App\Models\LegalCheck::class, 'annonce_id')->latestOfMany();
    }

    /** Dernière vérification « aucune opposition » confirmée par un admin. */
    public function confirmedLegalCheck()
    {
        return $this->hasOne(\App\Models\LegalCheck::class, 'annonce_id')
            ->where('declared_status', 'clear')
            ->where('admin_status', 'confirmed')
            ->latestOfMany();
    }

    /**
     * La vérification à afficher en badge public (ou null).
     * - require_admin_review ON  → uniquement une vérif "clear" + confirmée.
     * - require_admin_review OFF → une vérif "clear" (déclarée vendeur) suffit,
     *   tant qu'elle n'a pas été rejetée. Libellé honnête côté vue.
     * On ne badge JAMAIS une opposition/unknown (donnée sensible + périssable).
     */
    public function badgeLegalCheck()
    {
        if (! feature_on('legalcheck.enabled')) {
            return null;
        }

        if (feature_on('legalcheck.require_admin_review')) {
            return $this->confirmedLegalCheck;
        }

        return $this->legalChecks()
            ->where('declared_status', 'clear')
            ->where('admin_status', '!=', 'rejected')
            ->latest()
            ->first();
    }


    // Corrigé : utilise la relation media au lieu de décoder $this->media qui n'est pas un JSON
    public function pathImageAds()
    {
        $first = $this->media()->first();
        if ($first && !empty($first->name)) {
            return $first->name;
        }

        return 'phonedef.jpg';
    }

    /**
     * Historique de prix (discovery.price_alerts). Le hook « updating » enregistre
     * chaque changement de prix, quelle que soit la voie de modification
     * (client, admin, vente), donc une seule source.
     */
    protected static function booted(): void
    {
        // Nouvelle annonce → la proposer aux demandes (recherches) correspondantes.
        static::created(function ($ad) {
            dispatch(function () use ($ad) {
                try {
                    \App\Services\DemandeMatcher::matchAnnonce($ad);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('DemandeMatcher failed: ' . $e->getMessage());
                }
            })->afterResponse();
        });

        static::updating(function ($ad) {
            if (! $ad->isDirty('prix')) {
                return;
            }
            $old = $ad->getOriginal('prix');
            $new = $ad->prix;
            if ($old === null || (float) $old === (float) $new) {
                return;
            }
            try {
                \App\Models\PriceHistory::create([
                    'annonce_id' => $ad->id,
                    'old_price'  => $old,
                    'new_price'  => $new,
                    'changed_at' => now(),
                ]);
            } catch (\Throwable $e) {
                // table absente (migration non jouée) → on n'empêche jamais la mise à jour
                \Illuminate\Support\Facades\Log::warning('price_history non enregistré: ' . $e->getMessage());
            }
        });
    }

    public function priceHistories()
    {
        return $this->hasMany(\App\Models\PriceHistory::class, 'annonce_id');
    }

    /** Dernier changement de prix (pour le badge « prix en baisse »). */
    public function latestPriceChange()
    {
        return $this->hasOne(\App\Models\PriceHistory::class, 'annonce_id')->latestOfMany('changed_at');
    }

    /**
     * Montant de la baisse courante en MAD (0 si pas de baisse). Gated par le flag.
     * On n'affiche que si le dernier changement est une baisse ET que le nouveau
     * prix correspond toujours au prix actuel.
     */
    public function priceDropAmount(): int
    {
        if (! feature_on('discovery.price_alerts')) {
            return 0;
        }
        $last = $this->relationLoaded('latestPriceChange')
            ? $this->getRelation('latestPriceChange')
            : $this->latestPriceChange()->first();

        if (! $last) {
            return 0;
        }
        if ((float) $last->new_price < (float) $last->old_price
            && (float) $last->new_price === (float) $this->prix) {
            return (int) round((float) $last->old_price - (float) $last->new_price);
        }
        return 0;
    }
 

}
