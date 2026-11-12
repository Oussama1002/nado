<?php

/**
 * Alias marques (FR/AR/EN) pour la recherche tolérante du catalogue
 * (App\Support\AnnonceFilter, gated par discovery.smart_search).
 *
 * Clé = nom canonique tel qu'il existe (ou devrait exister) dans la table brands.
 * Valeurs = variantes de saisie équivalentes. La recherche est insensible à la
 * casse. Ajoutez librement — toute saisie correspondant à une variante est
 * étendue à l'ensemble du groupe.
 */
return [
    'brands' => [
        'Mercedes-Benz' => ['Mercedes', 'Mercedes Benz', 'مرسيدس', 'مرسيدس بنز', 'Benz'],
        'Volkswagen'    => ['VW', 'فولكس فاغن', 'فولكسفاغن', 'Volks'],
        'Peugeot'       => ['بيجو', 'بيجو'],
        'Renault'       => ['رينو', 'Reno'],
        'Citroën'       => ['Citroen', 'سيتروين'],
        'Toyota'        => ['تويوتا'],
        'Hyundai'       => ['هيونداي', 'هونداي'],
        'Kia'           => ['كيا'],
        'Nissan'        => ['نيسان'],
        'Ford'          => ['فورد'],
        'Audi'          => ['أودي', 'اودي'],
        'BMW'           => ['بي إم دبليو', 'بيإمدبليو'],
        'Dacia'         => ['داسيا', 'داشيا'],
        'Fiat'          => ['فيات'],
        'Opel'          => ['أوبل', 'اوبل'],
        'Seat'          => ['سيات'],
        'Škoda'         => ['Skoda', 'سكودا'],
        'Volvo'         => ['فولفو'],
        'Honda'         => ['هوندا'],
        'Mazda'         => ['مازدا'],
        'Suzuki'        => ['سوزوكي'],
        'Jeep'          => ['جيب'],
        'Land Rover'    => ['Landrover', 'لاند روفر', 'لاندروفر'],
        'Mini'          => ['ميني'],
        'Mitsubishi'    => ['ميتسوبيشي', 'متسوبيشي'],
        'Chevrolet'     => ['شيفروليه', 'Chevy'],
    ],
];
