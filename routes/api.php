<?php

use App\Support\Yard\CurrentActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return [
        'status' => 'ok',
        'app' => config('app.name'),
    ];
});

Route::middleware('shunt.auth')->group(function () {
    Route::get('/version', function () {
        return [
            'app' => config('app.name'),
            'environment' => config('app.env'),
        ];
    });

    Route::get('/modules', function () {
        return [
            'data' => config('yard.modules', []),
        ];
    });

    Route::get('/me', function (Request $request) {
        /** @var CurrentActor $actor */
        return [
            'data' => current_actor()?->toArray(),
        ];
    });
});
