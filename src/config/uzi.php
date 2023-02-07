<?php

return [
    // True if the CA must be checked on the x509 certificate, set to false for using test cards
    'strict_ca_check' => env('UZI_STRICT_CA_CHECK', true),

    // Which card types are allowed to log in
    'allowed_types' => [],

    // Which roles are allowed to log in
    'allowed_roles' => [],

    // The CA certificates to use for validating the UZI certificate. Must be concatenated in a single file.
    'ca_certs_path' => env('UZI_CA_CERTS_PATH', null),
];
