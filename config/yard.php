<?php

return [
    'shunt' => [
        'base_url' => env('SHUNT_BASE_URL'),
        'timeout' => (int) env('SHUNT_TIMEOUT', 5),
    ],

    'modules' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('YARD_ENABLED_MODULES', ''))
    ))),
];
