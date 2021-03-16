<?php

namespace MinVWS\PUZI\Laravel;

use Illuminate\Support\ServiceProvider;
use MinVWS\PUZI\UziValidator;

/**
 * Class UziServiceProvider
 * SPDX-License-Identifier: EUPL-1.2
 * @package MinVWS\PUZI\Laravel
 */
class UziServiceProvider extends ServiceProvider
{
    /**
     * Boots the Service provider.
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/uzi.php' => config_path('uzi.php')
        ]);
    }

    public function register()
    {
        $this->app->bind(UziValidator::class, function () {
            return new UziValidator(
                config("uzi.strict_ca_check", true),
                config("uzi.allowed_types", []),
                config("uzi.allowed_roles", [])
            );
        });
    }
}
