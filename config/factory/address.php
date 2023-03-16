<?php

return [
    'api' => [
        'url' => 'https://nominatim.openstreetmap.org/reverse?format=json',
    ],
    'locations' => [
        'caldas' => [
            'coords' => [
                'lat' => 39.4071,
                'lon' => -9.13458,
            ],
            'quantity' => 35,
            'radius' => 2000
        ],
        'leiria' => [
            'coords' => [
                'lat' => 39.7443,
                'lon' => -8.80725,
            ],
            'quantity' => 55,
            'radius' => 3000
        ],
        'nazare' => [
            'coords' => [
                'lat' => 39.600423,
                'lon' => -9.064975,
            ],
            'quantity' => 6,
            'radius' => 500
        ],
        'near_batalha_pm' => [
            'coords' => [
                'lat' => 39.636585,
                'lon' => -8.822160,
            ],
            'quantity' => 10,
            'radius' => 2500
        ],
        'near_alcobaca' => [
            'coords' => [
                'lat' => 39.533924,
                'lon' => -9.013047,
            ],
            'quantity' => 15,
            'radius' => 3250
        ],
        'torres_vedras' => [
            'coords' => [
                'lat' => 39.091580,
                'lon' => -9.259140,
            ],
            'quantity' => 4,
            'radius' => 400
        ],
    ],
];
