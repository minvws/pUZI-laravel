<?php

declare(strict_types=1);

namespace MinVWS\PUZI\Laravel\Providers;

use MinVWS\PUZI\Laravel\Services\UziService;
use Illuminate\Support\ServiceProvider;

/**
 * Class UziProvider
 * SPDX-License-Identifier: EUPL-1.2
 * @package MinVWS\PUZI\Laravel\Providers
 */
class UziProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/uzi.php' => config_path('uzi.php'),
        ]);

        $this->app->singleton(UziService::class, function () {
            return new UziService(
                config("uzi.strict_ca_check", true),
                config('uzi.allowed_types', []),
                config('uzi.allowed_roles', [])
            );
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/../routes.php';
        $this->app->make('MinVWS\PUZI\Laravel\Controllers\UziController');

        $this->mergeConfigFrom(__DIR__ . '/../../config/uzi.php', 'uzi');
    }
}
