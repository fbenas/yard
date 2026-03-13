<?php

use App\Support\CurrentActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return [
        'status' => 'ok',
        'app' => config('app.name'),
    ];
});

Route::middleware('auth.auth')->group(function () {
    Route::get('/version', function () {
        return [
            'app' => config('app.name'),
            'environment' => config('app.env'),
        ];
    });

    Route::get('/modules', function () {
        return [
            'data' => config('api.modules', []),
        ];
    });

    Route::get('/me', function (Request $request) {
        /** @var CurrentActor $actor */
        return [
            'data' => current_actor()?->toArray(),
        ];
    });
});

Route::middleware(['auth.auth', 'auth.org'])->group(function () {
    Route::get('/context', function () {
        return [
            'data' => [
                'actor' => current_actor()?->toArray(),
                'organisation_id' => current_organisation_id(),
            ],
        ];
    });
});
