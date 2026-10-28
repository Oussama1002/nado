<?php

/**
 * French UI strings. Mirror every key in lang/ar/messages.php.
 * Reference in Blade with __('messages.key').
 */
return [

    'lang' => [
        'fr'       => 'Français',
        'ar'       => 'العربية',
        'switch'   => 'Langue',
    ],

    'nav' => [
        'buy'            => 'Achat',
        'sell'           => 'Vente',
        'auction'        => 'Auto-enchère',
        'tradein'        => 'Reprise',
        'financing'      => 'Auto-financement',
        'about'          => 'À propos',
        'buy_ae'         => 'Acheter',
        'sell_ae'        => 'Vendre',
        'individual'     => 'Particulier',
        'professional'   => 'Professionnel',
        'my_space'       => 'Mon espace',
        'logout'         => 'Déconnexion',
        'publish_ad'     => 'Publier une annonce',
        'create_account' => 'Créez un compte',
        'publish_short'  => 'Publier',
        'account_short'  => 'Compte',
        'account'        => 'Mon compte',
        'support'        => 'Support NADO',
        'sell_individual'=> 'Vendre (Particulier)',
        'sell_pro'       => 'Vendre (Pro)',
    ],

    'hero' => [
        'badge'   => 'Expérience Premium NADO',
        'title_1' => "L'Auto",
        'title_2' => 'Redéfinie',
        'desc'    => 'Le plus grand catalogue de véhicules certifiés au Maroc. Achat, vente et enchères en un seul lieu.',
    ],

    'search' => [
        'brand'       => 'Marque',
        'model'       => 'Modèle',
        'all_brands'  => 'Toutes marques',
        'all_models'  => 'Tous modèles',
        'search'      => 'Rechercher',
    ],

    // Section headings keep their two-tone spans, so they are rendered with {!! !!}.
    'home' => [
        'vehicles_heading'     => 'Nos <br><span class="text-primary">DERNIERS</span> <span class="text-secondary">VÉHICULES</span>',
        'story_heading'        => '<span class="nado-b">NADO</span><span class="nado-y">.MA</span> sur les réseaux : Ne ratez rien !',
        'certified_heading'    => 'Dernières <br><span class="text-primary">ANNONCES</span> <span class="text-secondary">CERTIFIÉES</span>',
        'expertise_heading'    => 'Expertise <span class="text-primary">110 Points</span>',
        'expertise_desc'       => 'Chaque véhicule certifié passe une inspection chirurgicale avant sa mise en ligne.',
        'trust_label'          => 'Label Confiance',
        'particuliers_heading' => 'Dernières <br><span class="text-primary">ANNONCES</span> <span class="text-secondary">PARTICULIERS</span>',
        'finance_solutions'    => 'Solutions Finance',
        'financement_heading'  => 'Financement <br><span class="text-primary">Accordé en 24H</span>',
        'financement_desc'     => 'Réponse de principe immédiate. Taux préférentiels pour nos clients Premium.',
        'simulate_credit'      => 'Simuler mon crédit',
        'pros_heading'         => 'Dernières <br><span class="text-primary">ANNONCES</span> <span class="text-secondary">PROS</span>',
        'price_heading'        => 'Combien vaut <br><span class="text-secondary">VOTRE VOITURE ?</span>',
        'price_desc'           => "Obtenez une estimation précise en 2 minutes basée sur l'IA.",
        'estimate_my_car'      => 'Estimer ma voiture',
        'immersive_heading'    => 'Expériences <span class="text-primary">Immersives</span>',
        'live_heading'         => "L'Arène des <br><span class=\"text-primary\">Enchères Live</span>",
        'expert_heading'       => "BESOIN D'UN <br><span class=\"text-secondary\">EXPERT <br>NADO ?</span>",
        'clients_heading'      => 'Ce que disent <br><span class="text-primary">NOS</span> <span class="text-secondary">CLIENTS</span>',
        'clients_desc'         => 'La satisfaction de nos clients est notre priorité absolue.',
        'blog_heading'         => 'BLOG <br><span class="text-primary">NADO</span>',
        'explore_blog'         => 'Explorer nos blog',
        'view_all_catalog'     => 'Voir tout le catalogue',
    ],

    'expert' => [
        'form_btn' => 'FORMULAIRE DE CONTACT',
        'title'    => 'Besoin d\'un <span class="text-primary">expert NADO</span> ?',
        'subtitle' => 'Laissez vos coordonnées, un expert vous recontacte rapidement.',
        'name'     => 'Nom complet *',
        'phone'    => 'Téléphone *',
        'email'    => 'Email *',
        'ville'    => 'Choisir une ville *',
        'message'  => 'Votre message',
        'submit'   => 'Envoyer ma demande',
    ],

];
