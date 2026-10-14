<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paramètres Généraux BNPL Cypres Pay
    |--------------------------------------------------------------------------
    */
    'currency' => 'FCFA',
    'currency_symbol' => 'FCFA',
    'cash_discount_percent' => 7, // 7% de remise sur paiement comptant
    'free_shipping_threshold' => 100000, // Livraison offerte dès 100 000 FCFA

    // Délais de livraison
    'delivery_time' => '72h Chrono',
    'delivery_zones' => [
        'Abidjan (Toutes communes)',
        'Intérieur (Bouaké, Yamoussoukro, San-Pedro, Daloa, Lakota, Korhogo)',
    ],

    // Fourchettes du simulateur
    'simulator' => [
        'min_amount' => 50000,
        'max_amount' => 3000000,
        'step' => 5000,
        'default_amount' => 150000,
        'default_duration_months' => 12,
    ],

    // Canaux de paiement Mobile Money agréés en Côte d'Ivoire
    'payment_channels' => [
        'wave' => [
            'name' => 'Wave Côte d\'Ivoire',
            'color' => '#1DA1F2',
            'instant' => true,
        ],
        'orange_money' => [
            'name' => 'Orange Money',
            'color' => '#FF7900',
            'instant' => true,
        ],
        'mtn_momo' => [
            'name' => 'MTN Mobile Money',
            'color' => '#FFCC00',
            'instant' => true,
        ],
        'moov_money' => [
            'name' => 'Moov Africa Money',
            'color' => '#005CA9',
            'instant' => true,
        ],
        'cash' => [
            'name' => 'Espèces (En caisse ou agent commercial)',
            'color' => '#00825A',
            'instant' => false,
        ],
    ],
];
