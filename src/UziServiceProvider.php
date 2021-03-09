<?php

namespace MinVWS\Laravel\Puzi;

use Illuminate\Support\ServiceProvider;

class UziServiceProvider extends ServiceProvider
{
    /**
     * Boots the Service provider.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/uzi.php' => config_path('uzi.php')
        ]);
    }

    public function register()
    {
        $this->app->bind(UziValidator::class, function() {
            return new UziValidator(
                config("uzi.strict_ca_check", true),
                config("uzi.allowed_types", []),
                config("uzi.allowed_roles", [])
            );
        });
    }
}
