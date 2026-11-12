<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdsRequest;
use App\Models\Annonces;
use App\Models\Brand;
use App\Models\Modele;
use App\Models\NadoAutoEncheresBid;

use App\Models\Color;
use App\Models\Media;
use App\Models\Ville;
use Illuminate\Http\Request;
use App\Models\Accessoire;
use App\Models\Client;
class NadoAutoEnchere extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'phone','titre','prix','mark','modele','year','km','carburant','box','matricule','city','cv',
        'nb_Door','origine','premiere_main','video','reel','bodywork','images','rapport',
        'co2','certifie','particulier','showroom','finance','starts_at','ends_at',
        'starting_price','current_price','reserve_price','min_increment','status',
        'winner_client_id','winning_bid_id','extensions_count',
        'jantesA','airbags','climatisation','gps','toit_ouvrant','double_toit_ouvrant',
        'siege_cuir','radar_recul','camera_recul','abs','eps','esp','antipatinage',
        'aide_freinage_urgence','antidemarrage_electronique','aide_demarrage_cote','selecteur_mode_conduite',
        'detection_fatigue','maintien_dans_la_voie','detecteur_angle_mort','detecteur_sous_gonflage',
        'fermeture_portes_auto','preparation_isofix','phares_antibrouillard','systeme_alarme',
        'start_stop','regulateur_vitesse','detecteur_pluie','allumage_auto_feux','frein_main_electrique',
        'aide_stationnement','volant_reglable','retros_electriques','retros_rabat_electriques',
        'coffre_electrique','sieges_electriques','sieges_elec_memoire','banquette_rabattable',
        'ecran_tactile','cockpit_digital','commandes_volant','commandes_vocales','reconnaissance_panneaux',
        'affichage_tete_haute','systeme_audio','wifi_bord','bluetooth','compatibilite_smartphone',
        'apple_carplay_android_auto','chargeur_sans_fil','sellerie_similicuir_tissu','volant_cuir',
        'follow_me_home','lumiere_ambiance','feux_jour_led','phares_full_led','toit_panoramique',
        'barres_de_toit','vitres_surteintees','views','active','verified','colors',
        'carte_grise','vignette_annee','visite_technique',
        'nb_cles','pneu_secours','outillage',
        'latitude','longitude','adresse','maps_url',
        'reminder_sent_at'
    ];

    protected $casts = [
        'images' => 'array',
        'starts_at'        => 'datetime',
        'ends_at'          => 'datetime',
        'reminder_sent_at' => 'datetime',
        'starting_price'   => 'decimal:2',
        'current_price'    => 'decimal:2',
        'reserve_price'    => 'decimal:2',
        'min_increment'    => 'decimal:2',
        'extensions_count' => 'integer',
        'carte_grise'      => 'boolean',
        'visite_technique' => 'date',
        'pneu_secours'     => 'boolean',
        'outillage'        => 'boolean',

        // booleans
        'premiere_main' => 'boolean',
        'reel'          => 'boolean',
        'certifie'      => 'boolean',
        'particulier'   => 'boolean',
        'showroom'      => 'boolean',
        'finance'       => 'boolean',
        'active'        => 'boolean',
        'verified'      => 'boolean',

        // options booleans
        'jantesA'                   => 'boolean',
        'airbags'                   => 'boolean',
        'climatisation'             => 'boolean',
        'gps'                       => 'boolean',
        'toit_ouvrant'              => 'boolean',
        'double_toit_ouvrant'       => 'boolean',
        'siege_cuir'                => 'boolean',
        'radar_recul'               => 'boolean',
        'camera_recul'              => 'boolean',
        'abs'                       => 'boolean',
        'eps'                       => 'boolean',
        'esp'                       => 'boolean',
        'antipatinage'              => 'boolean',
        'aide_freinage_urgence'     => 'boolean',
        'antidemarrage_electronique'=> 'boolean',
        'aide_demarrage_cote'       => 'boolean',
        'selecteur_mode_conduite'   => 'boolean',
        'detection_fatigue'         => 'boolean',
        'maintien_dans_la_voie'     => 'boolean',
        'detecteur_angle_mort'      => 'boolean',
        'detecteur_sous_gonflage'   => 'boolean',
        'fermeture_portes_auto'     => 'boolean',
        'preparation_isofix'        => 'boolean',
        'phares_antibrouillard'     => 'boolean',
        'systeme_alarme'            => 'boolean',
        'start_stop'                => 'boolean',
        'regulateur_vitesse'        => 'boolean',
        'detecteur_pluie'           => 'boolean',
        'allumage_auto_feux'        => 'boolean',
        'frein_main_electrique'     => 'boolean',
        'aide_stationnement'        => 'boolean',
        'volant_reglable'           => 'boolean',
        'retros_electriques'        => 'boolean',
        'retros_rabat_electriques'  => 'boolean',
        'coffre_electrique'         => 'boolean',
        'sieges_electriques'        => 'boolean',
        'sieges_elec_memoire'       => 'boolean',
        'banquette_rabattable'      => 'boolean',
        'ecran_tactile'             => 'boolean',
        'cockpit_digital'           => 'boolean',
        'commandes_volant'          => 'boolean',
        'commandes_vocales'         => 'boolean',
        'reconnaissance_panneaux'   => 'boolean',
        'affichage_tete_haute'      => 'boolean',
        'systeme_audio'             => 'boolean',
        'wifi_bord'                 => 'boolean',
        'bluetooth'                 => 'boolean',
        'compatibilite_smartphone'  => 'boolean',
        'apple_carplay_android_auto'=> 'boolean',
        'chargeur_sans_fil'         => 'boolean',
        'sellerie_similicuir_tissu' => 'boolean',
        'volant_cuir'               => 'boolean',
        'follow_me_home'            => 'boolean',
        'lumiere_ambiance'          => 'boolean',
        'feux_jour_led'             => 'boolean',
        'phares_full_led'           => 'boolean',
        'toit_panoramique'          => 'boolean',
        'barres_de_toit'            => 'boolean',
        'vitres_surteintees'        => 'boolean',
    ];
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'mark'); // 'mark' stores brand id
    }

    public function modeleRelation()
    {
        return $this->belongsTo(Modele::class, 'modele'); // 'modele' stores model id
    }
public function client()
{
    return $this->belongsTo(Client::class, 'phone', 'phone');
}
 public function bids()
    {
        return $this->hasMany(NadoAutoEncheresBid::class, 'enchere_id');
    }

    /** Gagnant de l'enchère (renseigné à la clôture). */
    public function winner()
    {
        return $this->belongsTo(Client::class, 'winner_client_id');
    }

    /** Enchère gagnante (renseignée à la clôture). */
    public function winningBid()
    {
        return $this->belongsTo(NadoAutoEncheresBid::class, 'winning_bid_id');
    }

    /** Incrément minimum effectif (valeur de l'enchère, sinon réglage global). */
    public function effectiveMinIncrement(): float
    {
        $inc = (float) ($this->min_increment ?? 0);
        if ($inc <= 0) {
            $inc = (float) settings('enchere.default_min_increment', 1000);
        }
        return $inc > 0 ? $inc : 1000;
    }

    /** Mise à prix effective (fallback sur prix estimé). */
    public function effectiveStartingPrice(): float
    {
        return (float) (($this->starting_price ?? 0) ?: ($this->prix ?? 0));
    }

    /** Plus haute enchère courante (prix courant, sinon mise à prix). */
    public function currentHighBid(): float
    {
        $current = (float) ($this->current_price ?? 0);
        return $current > 0 ? $current : $this->effectiveStartingPrice();
    }

    /**
     * Montant minimum pour la prochaine enchère valable.
     * Première enchère : la mise à prix. Ensuite : courant + incrément.
     */
    public function minNextBid(): float
    {
        $hasBid = $this->current_price !== null && (float) $this->current_price > 0;
        if (! $hasBid) {
            return $this->effectiveStartingPrice();
        }
        return $this->currentHighBid() + $this->effectiveMinIncrement();
    }

    /** L'enchère est-elle ouverte aux offres maintenant ? */
    public function isLive(): bool
    {
        if ($this->status && $this->status !== 'live') {
            return false;
        }
        $now = now();
        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }
        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }
        return true;
    }
}
