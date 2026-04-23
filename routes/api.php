<?php

use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Support\CurrentActor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return [
        'status' => 'ok',
        'app' => config('app.name'),
    ];
});

Route::middleware('auth.user')->group(function () {
    Route::get('/version', function () {
        return [
            'app' => config('app.name'),
            'environment' => config('app.env'),
        ];
    });

    Route::get('/me', function (Request $request) {
        /** @var CurrentActor $actor */
        return [
            'data' => current_actor()?->toArray(),
        ];
    });
});

Route::middleware(['auth.user', 'auth.org'])->group(function () {
    Route::get('/context', function () {
        return [
            'data' => [
                'actor' => current_actor()?->toArray(),
                'organisation_id' => current_organisation_id(),
            ],
        ];
    });
});

Route::middleware(['auth.user', 'auth.org'])->prefix('api')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}/roles', [UserController::class, 'syncRoles']);
    Route::put('/users/{user}/permissions', [UserController::class, 'syncPermissions']);

    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/roles/{role}', [RoleController::class, 'show']);
    Route::post('/roles', [RoleController::class, 'store']);
    Route::put('/roles/{role}', [RoleController::class, 'update']);
    Route::delete('/roles/{role}', [RoleController::class, 'destroy']);

    Route::get('/permissions', [PermissionController::class, 'index']);
    Route::get('/permissions/{permission}', [PermissionController::class, 'show']);
});

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
