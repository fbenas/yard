<?php

return [
    'permissions' => [
        'products.read',
        'products.write',
    ],

    'roles' => [
        'products-manager' => [
            'products.read',
            'products.write',
        ],

        'products-viewer' => [
            'products.read',
        ],
    ],

];
