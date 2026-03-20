<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth.user'])
    ->prefix('api/{organisation}')
    ->group(function () {
        Route::get('/products', function (string $organisation) {
            abort_unless(current_actor()->hasOrganisation($organisation), 403);
            abort_unless(actor_can('products.read'), 403);

            return [
                'data' => [
                    'module' => 'products',
                    'organisation_id' => $organisation,
                ]
            ];
        });
    });
