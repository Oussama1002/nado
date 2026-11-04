<?php

/**
 * Messages de validation en français.
 * Sans ce fichier, Laravel affichait la clé brute (ex. « validation.unique »).
 * Les libellés des champs sont dans 'attributes' et quelques messages ciblés
 * (compte client) dans 'custom'.
 */
return [
    'accepted'             => 'Le champ :attribute doit être accepté.',
    'active_url'           => "Le champ :attribute n'est pas une URL valide.",
    'after'                => 'Le champ :attribute doit être une date postérieure au :date.',
    'alpha'                => 'Le champ :attribute ne peut contenir que des lettres.',
    'alpha_dash'           => 'Le champ :attribute ne peut contenir que des lettres, des chiffres et des tirets.',
    'alpha_num'            => 'Le champ :attribute ne peut contenir que des lettres et des chiffres.',
    'array'                => 'Le champ :attribute doit être un tableau.',
    'before'               => 'Le champ :attribute doit être une date antérieure au :date.',
    'between'              => [
        'numeric' => 'Le champ :attribute doit être compris entre :min et :max.',
        'file'    => 'Le fichier :attribute doit peser entre :min et :max kilo-octets.',
        'string'  => 'Le champ :attribute doit contenir entre :min et :max caractères.',
        'array'   => 'Le champ :attribute doit contenir entre :min et :max éléments.',
    ],
    'boolean'              => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed'            => 'La confirmation du champ :attribute ne correspond pas.',
    'date'                 => "Le champ :attribute n'est pas une date valide.",
    'date_format'          => 'Le champ :attribute ne correspond pas au format :format.',
    'different'            => 'Les champs :attribute et :other doivent être différents.',
    'digits'               => 'Le champ :attribute doit contenir :digits chiffres.',
    'digits_between'       => 'Le champ :attribute doit contenir entre :min et :max chiffres.',
    'dimensions'           => "L'image :attribute a des dimensions non valides.",
    'email'                => "Le champ :attribute doit être une adresse e-mail valide.",
    'exists'               => 'Le champ :attribute sélectionné est invalide.',
    'file'                 => 'Le champ :attribute doit être un fichier.',
    'filled'               => 'Le champ :attribute doit avoir une valeur.',
    'image'                => 'Le champ :attribute doit être une image.',
    'in'                   => 'Le champ :attribute sélectionné est invalide.',
    'integer'              => 'Le champ :attribute doit être un entier.',
    'ip'                   => 'Le champ :attribute doit être une adresse IP valide.',
    'json'                 => 'Le champ :attribute doit être un document JSON valide.',
    'max'                  => [
        'numeric' => 'Le champ :attribute ne peut pas être supérieur à :max.',
        'file'    => 'Le fichier :attribute ne peut pas dépasser :max kilo-octets.',
        'string'  => 'Le champ :attribute ne peut pas dépasser :max caractères.',
        'array'   => 'Le champ :attribute ne peut pas contenir plus de :max éléments.',
    ],
    'mimes'                => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes'            => 'Le champ :attribute doit être un fichier de type : :values.',
    'min'                  => [
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'file'    => 'Le fichier :attribute doit peser au moins :min kilo-octets.',
        'string'  => 'Le champ :attribute doit contenir au moins :min caractères.',
        'array'   => 'Le champ :attribute doit contenir au moins :min éléments.',
    ],
    'not_in'               => "Le champ :attribute sélectionné n'est pas valide.",
    'numeric'              => 'Le champ :attribute doit être un nombre.',
    'present'              => 'Le champ :attribute doit être présent.',
    'regex'                => 'Le format du champ :attribute est invalide.',
    'required'             => 'Le champ :attribute est obligatoire.',
    'required_if'          => 'Le champ :attribute est obligatoire quand :other vaut :value.',
    'required_with'        => 'Le champ :attribute est obligatoire quand :values est présent.',
    'required_without'     => "Le champ :attribute est obligatoire quand :values n'est pas présent.",
    'same'                 => 'Les champs :attribute et :other doivent être identiques.',
    'size'                 => [
        'numeric' => 'Le champ :attribute doit valoir :size.',
        'file'    => 'Le fichier :attribute doit peser :size kilo-octets.',
        'string'  => 'Le champ :attribute doit contenir :size caractères.',
        'array'   => 'Le champ :attribute doit contenir :size éléments.',
    ],
    'string'               => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone'             => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique'               => 'Cette valeur de :attribute est déjà utilisée.',
    'uploaded'             => "Le téléchargement du champ :attribute a échoué.",
    'url'                  => "Le format de l'URL :attribute est invalide.",

    /*
     | Messages ciblés par champ.rule (prioritaires sur les messages génériques).
     */
    'custom' => [
        'email' => [
            'unique' => 'Cette adresse e-mail est déjà utilisée. Connectez-vous ou utilisez-en une autre.',
            'email'  => "L'adresse e-mail saisie n'est pas valide.",
        ],
        'phone' => [
            'unique' => 'Ce numéro de téléphone est déjà utilisé. Connectez-vous ou utilisez-en un autre.',
        ],
        'password' => [
            'confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'min'       => 'Le mot de passe doit contenir au moins :min caractères.',
        ],
    ],

    /*
     | Libellés français des champs (remplacent :attribute).
     */
    'attributes' => [
        'name'                  => 'nom complet',
        'nom'                   => 'nom',
        'email'                 => 'adresse e-mail',
        'password'              => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
        'phone'                 => 'téléphone',
        'telephone'             => 'téléphone',
        'company'               => "nom de l'entreprise",
        'type'                  => 'type de compte',
        'ae_role'               => 'rôle',
        'city'                  => 'ville',
        'ville'                 => 'ville',
        'mark'                  => 'marque',
        'modele'                => 'modèle',
        'year'                  => 'année',
        'prix'                  => 'prix',
        'km'                    => 'kilométrage',
        'carburant'             => 'carburant',
        'boite'                 => 'boîte',
        'box'                   => 'boîte',
        'etat'                  => 'état',
        'origine'               => 'origine',
        'titre'                 => 'titre',
        'description'           => 'description',
        'message'               => 'message',
        'ice'                   => 'ICE',
        'images'                => 'photos',
        'image'                 => 'image',
        'video'                 => 'vidéo',
    ],
];
