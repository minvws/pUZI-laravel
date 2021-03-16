<?php

namespace Tests\Unit;

use MinVWS\PUZI\Laravel\AuthenticatableUziUser;
use MinVWS\PUZI\UziConstants;
use MinVWS\PUZI\UziUser;
use PHPUnit\Framework\TestCase;

class AuthenticatableUziUserTest extends TestCase
{
    public function testAuthenticableUser(): void
    {
        $user = new UziUser();
        $user->setAgbCode("agb123");
        $user->setCardType(UziConstants::UZI_TYPE_CARE_PROVIDER);
        $user->setGivenName("john");
        $user->setOidCa(UziConstants::OID_CA_CARE_PROVIDER);
        $user->setRole(UziConstants::UZI_ROLE_DENTIST);
        $user->setSubscriberNumber("abc123");
        $user->setSurName("doe");
        $user->setUziNumber("123");
        $user->setUziVersion("1");

        $expected = [
            'agb_code' => 'agb123',
            'card_type' => 'Z',
            'given_name' => 'john',
            'oid_ca' => '2.16.528.1.1003.1.3.5.5.2',
            'role' => '02.',
            'subscriber_number' => 'abc123',
            'sur_name' => 'doe',
            'uzi_number' => '123',
            'uzi_version' => '1',
        ];

        $authUser = AuthenticatableUziUser::fromUziUser($user);
        $this->assertEquals($expected, $authUser->jsonSerialize());

        $this->assertEquals("uzi_number", $authUser->getAuthIdentifierName());
        $this->assertEquals("123", $authUser->getAuthIdentifier());
        $this->assertEquals("", $authUser->getAuthPassword());
    }
}
