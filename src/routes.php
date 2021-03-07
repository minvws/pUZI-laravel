<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use MinVWS\PUZI\Laravel\Controllers\UziController;

Route::get('/uzi-login', [UziController::class, 'index'])
    ->middleware(['guest'])
    ->name('uzi.login');

Route::group(['domain' => config('routes.uzi.domain')], function () {
    Route::get('/uzi/login', [UziController::class, 'login'])
        ->middleware(['guest'])
        ->name('uzi.auth');
});
