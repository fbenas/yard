<?php

return [
    'auth' => [
        'base_url' => env('AUTH_BASE_URL'),
        'timeout' => (int) env('AUTH_TIMEOUT', 5),
    ],

    'modules' => collect(explode(',', (string) env('API_ENABLED_MODULES', '')))
        ->map(fn (string $module) => trim($module))
        ->filter()
        ->mapWithKeys(fn (string $module) => [$module => []])
        ->all(),
];
