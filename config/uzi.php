<?php

use MinVWS\PUZI\UziConstants;

return [
    // True if the CA must be checked on the x509 certificate
    'strict_ca_check' => env('UZI_STRICT_CA_CHECK', true),
    // Set false for using test cards
    'allowed_types' = [
        UziConstants::UZI_TYPE_CARE_PROVIDER,
        UziConstants::UZI_TYPE_NAMED_EMPLOYEE
    ],
    'allowed_roles' = [
        UziConstants::UZI_ROLE_DOCTOR,
        UziConstants::UZI_ROLE_DOCTOR,
        UziConstants::UZI_ROLE_PHARMACIST,
        UziConstants::UZI_ROLE_NURSE,
        UziConstants::UZI_ROLE_PHYS_ASSISTANT,
    ],
];
