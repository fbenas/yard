<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth.auth', 'auth.org'])
    ->prefix('api/products')
    ->group(function () {
        Route::get('/list', function () {
            abort_unless(actor_can('products.read'), 403);

            return [
                'data' => [
                    'module' => 'products',
                    'organisation_id' => current_organisation_id(),
                ],
            ];
        });
    });
