<?php

return [
    // True if the CA must be checked on the x509 certificate
    'strict_ca_check' => env('UZI_STRICT_CA_CHECK', true),

    // Allowed types
    'allowed_types' => [],

    // Allowed roles
    'allowed_roles' => [],
];
