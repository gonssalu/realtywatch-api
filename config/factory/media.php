<?php

return [
    'user' => [
        'avatar' => [
            'api' => 'https://api.dicebear.com/5.x/avataaars/png?seed=',
        ],
    ],
    'property' => [
        'api' => [
            'images' => 'https://api.pexels.com/v1/search',
            'videos' => 'https://api.pexels.com/videos/search',
            'key' => env('PEXELS_API_KEY'),
        ],
    ],
];
