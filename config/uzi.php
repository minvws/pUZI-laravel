<?php

use MinVWS\PUZI\UziConstants;

return [
    // True if the CA must be checked on the x509 certificate, set to false for using test cards
    'strict_ca_check' => env('UZI_STRICT_CA_CHECK', true),

    // Which card types are allowed to log in
    'allowed_types' = [
        UziConstants::UZI_TYPE_CARE_PROVIDER,
        UziConstants::UZI_TYPE_NAMED_EMPLOYEE
    ],

    // Which roles are allowed to log in
    'allowed_roles' = [
        UziConstants::UZI_ROLE_DOCTOR,
        UziConstants::UZI_ROLE_DOCTOR,
        UziConstants::UZI_ROLE_PHARMACIST,
        UziConstants::UZI_ROLE_NURSE,
        UziConstants::UZI_ROLE_PHYS_ASSISTANT,
    ],
];
