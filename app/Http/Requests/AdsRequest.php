<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function isAdmin(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        $isCreate = $this->method() === 'POST';
        $isAdmin  = $this->isAdmin();

        return [
            'phone'      => 'nullable|numeric',
            'titre'      => [$isCreate ? 'required' : 'nullable', 'string'],
            'prix'       => [$isCreate ? 'required' : 'nullable', 'numeric'],
            'mark'       => [$isCreate ? 'required' : 'nullable', 'string'],
            'modele'     => [$isCreate ? 'required' : 'nullable', 'string'],
            'matricule'  => [$isCreate ? 'required' : 'nullable', 'string'],
            'city'       => [$isCreate ? 'required' : 'nullable', 'string'],
            'year'       => [$isCreate ? 'required' : 'nullable', 'numeric'],
            'km'         => [$isCreate ? 'required' : 'nullable', 'numeric'],
            'cv'         => [$isCreate ? 'required' : 'nullable', 'numeric'],
            'carburant'  => [$isCreate ? 'required' : 'nullable', 'string'],
            'box'        => [$isCreate ? 'required' : 'nullable', 'string'],
            'origine'    => [$isCreate ? 'required' : 'nullable', 'string'],
            'nb_Door'    => [$isCreate ? 'required' : 'nullable', 'numeric'],
            // Admin submits a YouTube URL; regular users upload a video file
            'video' => $isAdmin
                ? ['nullable', 'string', 'max:255']
                : ['nullable', 'file', 'mimes:mp4,mov,avi,wmv', 'max:51200'],
            'reel'       => ['nullable', 'string'],
            'bodywork'   => [$isCreate ? 'required' : 'nullable', 'string'],
            'status'     => 'nullable|string',
            'co2'        => ['nullable', 'string'],
            'certifie'   => ['nullable'],
            'particulie' => ['nullable'],
            'pro'        => ['nullable'],
            'showroom'   => ['nullable'],
            'garantie'   => ['nullable'],
            'carnet_entretien' => ['nullable'],
            'finance'    => ['nullable'],

            // Options (booleans)
            'jantesA'                       => 'sometimes|boolean',
            'airbags'                       => 'sometimes|boolean',
            'climatisation'                 => 'sometimes|boolean',
            'gps'                           => 'sometimes|boolean',
            'toit_ouvrant'                  => 'sometimes|boolean',
            'double_toit_ouvrant'           => 'sometimes|boolean',
            'siege_cuir'                    => 'sometimes|boolean',
            'radar_recul'                   => 'sometimes|boolean',
            'camera_recul'                  => 'sometimes|boolean',
            'abs'                           => 'sometimes|boolean',
            'eps'                           => 'sometimes|boolean',
            'esp'                           => 'sometimes|boolean',
            'antipatinage'                  => 'sometimes|boolean',
            'aide_freinage_urgence'         => 'sometimes|boolean',
            'antidemarrage_electronique'    => 'sometimes|boolean',
            'aide_demarrage_cote'           => 'sometimes|boolean',
            'selecteur_mode_conduite'       => 'sometimes|boolean',
            'detection_fatigue'             => 'sometimes|boolean',
            'maintien_dans_la_voie'         => 'sometimes|boolean',
            'detecteur_angle_mort'          => 'sometimes|boolean',
            'detecteur_sous_gonflage'       => 'sometimes|boolean',
            'fermeture_portes_auto'         => 'sometimes|boolean',
            'preparation_isofix'            => 'sometimes|boolean',
            'phares_antibrouillard'         => 'sometimes|boolean',
            'systeme_alarme'                => 'sometimes|boolean',
            'start_stop'                    => 'sometimes|boolean',
            'regulateur_vitesse'            => 'sometimes|boolean',
            'detecteur_pluie'               => 'sometimes|boolean',
            'allumage_auto_feux'            => 'sometimes|boolean',
            'frein_main_electrique'         => 'sometimes|boolean',
            'aide_stationnement'            => 'sometimes|boolean',
            'volant_reglable'               => 'sometimes|boolean',
            'retros_electriques'            => 'sometimes|boolean',
            'retros_rabat_electriques'      => 'sometimes|boolean',
            'coffre_electrique'             => 'sometimes|boolean',
            'sieges_electriques'            => 'sometimes|boolean',
            'sieges_elec_memoire'           => 'sometimes|boolean',
            'banquette_rabattable'          => 'sometimes|boolean',
            'ecran_tactile'                 => 'sometimes|boolean',
            'cockpit_digital'               => 'sometimes|boolean',
            'commandes_volant'              => 'sometimes|boolean',
            'commandes_vocales'             => 'sometimes|boolean',
            'reconnaissance_panneaux'       => 'sometimes|boolean',
            'affichage_tete_haute'          => 'sometimes|boolean',
            'systeme_audio'                 => 'sometimes|boolean',
            'wifi_bord'                     => 'sometimes|boolean',
            'bluetooth'                     => 'sometimes|boolean',
            'compatibilite_smartphone'      => 'sometimes|boolean',
            'apple_carplay_android_auto'    => 'sometimes|boolean',
            'chargeur_sans_fil'             => 'sometimes|boolean',
            'sellerie_similicuir_tissu'     => 'sometimes|boolean',
            'volant_cuir'                   => 'sometimes|boolean',
            'follow_me_home'                => 'sometimes|boolean',
            'lumiere_ambiance'              => 'sometimes|boolean',
            'feux_jour_led'                 => 'sometimes|boolean',
            'phares_full_led'               => 'sometimes|boolean',
            'toit_panoramique'              => 'sometimes|boolean',
            'barres_de_toit'                => 'sometimes|boolean',
            'vitres_surteintees'            => 'sometimes|boolean',

            'rapport'        => 'nullable|file|mimes:pdf,jpg,png,doc,docx',
            'premiere_main'  => [$isCreate ? 'required' : 'nullable', 'in:Oui,Non'],
            'etat'           => [$isCreate ? 'required' : 'nullable', 'string', 'max:255'],
            'colors'         => 'nullable|string|max:255',

            'uploaded_images'   => [$isCreate && !$isAdmin ? 'required' : 'nullable', 'array'],
            'uploaded_images.*' => ['nullable', 'image', 'mimes:jpeg,jfif,jpg,png,gif,webp', 'max:5120'],
        ];
    }
}
