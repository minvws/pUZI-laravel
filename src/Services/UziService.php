<?php

declare(strict_types=1);

namespace MinVWS\PUZI\Laravel\Services;

use Illuminate\Foundation\Auth\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Schema;
use MinVWS\PUZI\Exceptions\UziException;
use Illuminate\Support\Facades\Hash;
use MinVWS\PUZI\UziConstants;
use MinVWS\PUZI\UziReader;

/**
 * Class UziService
 * SPDX-License-Identifier: EUPL-1.2
 * For reference please read
 * https://www.zorgcsp.nl/documents/RK1%20CPS%20UZI-register%20V10.2%20ENG.pdf
 * @package MinVWS\PUZI\Laravel\Services
 */
class UziService
{
    /** @var UziReader $uzi */
    protected $uzi;

    /** @var bool */
    protected $strictCaCheck = true;
    /** @var array $allowedTypes */
    protected $allowedTypes = [];
    /** @var array $allowedRoles */
    protected $allowedRoles = [];

    /**
     * UziService constructor.
     *
     * @param bool $strictCaCheck
     * @param array $allowedTypes
     * @param array $allowedRoles
     */
    public function __construct(bool $strictCaCheck, array $allowedTypes, array $allowedRoles)
    {
        $this->strictCaCheck = $strictCaCheck;
        $this->allowedTypes = $allowedTypes;
        $this->allowedRoles = $allowedRoles;
        $this->uzi = new UziReader();
    }

    /**
     * @return Authenticatable
     * @throws UziException
     */
    public function getUserFromUzi(): Authenticatable
    {
        $data = $this->uzi->getData();
        if (
            $this->strictCaCheck == true &&
            $data['OidCa'] !== UziConstants::OID_CA_CARE_PROVIDER &&
            $data['OidCa'] !== UziConstants::OID_CA_NAMED_EMPLOYEE
        ) {
            throw new UziException('CA OID not UZI register Care Provider or named employee');
        }
        if ($data['UziVersion'] !== '1') {
            throw new UziException('UZI version not 1');
        }
        if (!in_array($data['CardType'], $this->allowedTypes)) {
            throw new UziException('UZI CardType not in ALLOWED_UZI_TYPES');
        }
        if (!in_array(substr($data['Role'], 0, 3), $this->allowedRoles)) {
            throw new UziException('UZI Role not in ALLOWED_UZI_ROLES');
        }

        $email = $data['UziNumber'] . '@uzi.pas';
        $user = User::whereEmail($email)->first();
        if ($user === null) {
            $user = User::create([
                'email' => $email,
                'name' => $data['givenName'] . $data['surName'],
                'password' => Hash::make(uniqid()),
            ]);
            if (Schema::hasColumn('users', 'password_updated_at')) {
                $user->password_updated_at = now();
            }
            $user->save();
        }

        return $user;
    }
}
