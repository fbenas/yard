<?php

use App\Http\Controllers\Auth\ShuntAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [ShuntAuthController::class, 'redirect'])->name('login');
Route::get('/auth/callback', [ShuntAuthController::class, 'callback'])->name('auth.callback');
Route::post('/logout', [ShuntAuthController::class, 'logout'])->name('logout');
