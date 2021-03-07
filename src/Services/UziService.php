<?php

namespace MinVWS\PUZI\Laravel\Services;

use Illuminate\Foundation\Auth\User;
use Illuminate\Contracts\Auth\Authenticatable;
use MinVWS\PUZI\Exceptions\UziException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use MinVWS\PUZI\UziReader;

/**
 * Class UziService.
 *
 * For reference please read
 * https://www.zorgcsp.nl/documents/RK1%20CPS%20UZI-register%20V10.2%20ENG.pdf
 *
 * @package MinVWS\PUZI\Laravel\Services
 */
class UziService
{
    protected const OID_CA_CARE_PROFESSIONAL = '2.16.528.1.1003.1.3.5.5.2'; // Reference page 59
    protected const OID_CA_NAMED_EMPLOYEE = '2.16.528.1.1003.1.3.5.5.3';    // Reference page 59
    protected const UZI_ROLE_DOCTOR = '01.';                                // Reference page 89
    protected const UZI_ROLE_PHARMACIST = '17.';                            // Reference page 89
    protected const UZI_ROLE_NURSE = '30.';                                 // Reference page 89
    protected const UZI_ROLE_PHYS_ASSISTANT = '81.';                        // Reference page 89
    protected const UZI_TYPE_CARE_PROVIDER = 'Z';                           // Reference page 60
    protected const UZI_TYPE_NAMED_EMPLOYEE = 'N';                          // Reference page 60

    protected const ALLOWED_UZI_TYPES = [
        self::UZI_TYPE_CARE_PROVIDER,
        self::UZI_TYPE_NAMED_EMPLOYEE
    ];

    protected const ALLOWED_UZI_ROLES = [
        self::UZI_ROLE_DOCTOR,
        self::UZI_ROLE_PHARMACIST,
        self::UZI_ROLE_NURSE,
        self::UZI_ROLE_PHYS_ASSISTANT,
    ];

    /** @var bool */
    protected $strictCaCheck = true;
    /** @var UziReader $uzi */
    protected $uzi;

    /**
     * UziService constructor.
     *
     * @param bool $strictCaCheck
     */
    public function __construct(bool $strictCaCheck)
    {
        $this->strictCaCheck = $strictCaCheck;
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
            $data['OidCa'] !== self::OID_CA_CARE_PROFESSIONAL &&
            $data['OidCa'] !== self::OID_CA_NAMED_EMPLOYEE
        ) {
            throw new UziException('CA OID not UZI register Care Provider or named employee');
        }
        if ($data['UziVersion'] !== '1') {
            throw new UziException('UZI version not 1');
        }
        if (!in_array($data['CardType'], self::ALLOWED_UZI_TYPES)) {
            throw new UziException('UZI CardType not in ALLOWED_UZI_TYPES');
        }
        if (!in_array(substr($data['Role'], 0, 3), self::ALLOWED_UZI_ROLES)) {
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
            $user->password_updated_at = now();
            $user->save();
        }
        return $user;
    }
}
