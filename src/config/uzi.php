<?php

return [
    // True if the CA must be checked on the x509 certificate, set to false for using test cards
    'strict_ca_check' => env('UZI_STRICT_CA_CHECK', true),

    // Which card types are allowed to log in
    'allowed_types' => [],

    // Which roles are allowed to log in
    'allowed_roles' => [],
];
