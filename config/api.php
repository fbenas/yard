<?php

return [
    'auth' => [
        'base_url' => env('AUTH_BASE_URL'),
        'timeout' => (int) env('AUTH_TIMEOUT', 5),
    ],

    'modules' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('API_ENABLED_MODULES', ''))
    ))),
];
