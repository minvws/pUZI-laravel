<?php

namespace MinVWS\PUZI\Laravel;

use Illuminate\Support\ServiceProvider;
use MinVWS\PUZI\UziReader;
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

            // Split certificates from file
            $caCerts = [];
            $path = config('uzi.ca_certs_path', null);
            if ($path) {
                $fileContent = @file_get_contents($path);
                if ($fileContent === false) {
                    throw new \RuntimeException("Could not read CA certificates from $path");
                }

                $caCerts = preg_split('/-----BEGIN CERTIFICATE-----/', $fileContent);
                if ($caCerts === false) {
                    $caCerts = [];
                } else {
                    // remove empty first element
                    array_shift($caCerts);
                }

                foreach ($caCerts as &$cert) {
                    $cert = trim($cert);
                    $cert = str_replace('-----END CERTIFICATE-----', '', $cert);
                    $cert = str_replace("\n", '', $cert);
                }
            }

            return new UziValidator(
                new UziReader(),
                config("uzi.strict_ca_check", true),
                config("uzi.allowed_types", []),
                config("uzi.allowed_roles", []),
                $caCerts
            );
        });
    }
}
