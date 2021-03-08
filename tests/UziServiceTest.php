<?php

namespace MinVWS\PUZI\Laravel\Tests;

use MinVWS\PUZI\Exceptions\UziException;
use MinVWS\PUZI\Laravel\Services\UziService;
use MinVWS\PUZI\UziConstants;
use Orchestra\Testbench\TestCase;

/**
 * Class UziServiceTest.
 */
final class UziServiceTest extends TestCase
{
    protected $allowedTypes = [
        UziConstants::UZI_TYPE_CARE_PROVIDER,
        UziConstants::UZI_TYPE_NAMED_EMPLOYEE
    ];

    protected $allowedRoles = [
        UziConstants::UZI_ROLE_DOCTOR,
        UziConstants::UZI_ROLE_DOCTOR,
        UziConstants::UZI_ROLE_PHARMACIST,
        UziConstants::UZI_ROLE_NURSE,
        UziConstants::UZI_ROLE_PHYS_ASSISTANT,
    ];

    public function testCheckRequestHasNoCert(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("Webserver client cert check not passed");

        $service->getUserFromUzi();
    }

    public function testCheckSSLClientFailed(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("Webserver client cert check not passed");

        $_SERVER['SSL_CLIENT_VERIFY'] = "failed";
        $service->getUserFromUzi();
    }

    public function testCheckNoClientCert(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("No client certificate presented");

        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";

        $service->getUserFromUzi();
    }

    public function testCheckCertWithoutValidData(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("No valid UZI data found");

        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-001-no-valid-uzi-data.cert');

        $service->getUserFromUzi();
    }

    public function testCheckCertWithInvalidSAN(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("No valid UZI data found");

        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-002-invalid-san.cert');

        $service->getUserFromUzi();
    }

    public function testCheckCertWithInvalidOthername(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("No valid UZI data found");

        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-003-invalid-othername.cert');

        $service->getUserFromUzi();
    }

    public function testCheckCertWithoutIa5string(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("No ia5String");

        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-004-othername-without-ia5string.cert');

        $service->getUserFromUzi();
    }

    public function testCheckCertIncorrectSanData(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("Incorrect SAN found");

        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-005-incorrect-san-data.cert');

        $service->getUserFromUzi();
    }

    public function testCheckCertIncorrectSanData2(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("Incorrect SAN found");


        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-006-incorrect-san-data.cert');

        $service->getUserFromUzi();
    }

    public function testCheckCheckStrictCA(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("CA OID not UZI register Care Provider or named employee");

        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-007-strict-ca-check.cert');

        $service->getUserFromUzi();
    }

    public function testCheckCheckStrictCA2(): void
    {
        $service = new UziService(false, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("UZI version not 1");

        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-008-invalid-version.cert');

        $service->getUserFromUzi();
    }

    public function testCheckNotAllowedTypes(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("UZI CardType not in ALLOWED_UZI_TYPES");

        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-009-invalid-types.cert');

        $service->getUserFromUzi();
    }

    public function testCheckNotAllowedRoles(): void
    {
        $service = new UziService(true, $this->allowedTypes, $this->allowedRoles);

        $this->expectException(UziException::class);
        $this->expectExceptionMessage("UZI Role not in ALLOWED_UZI_ROLES");


        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-010-invalid-roles.cert');

        $service->getUserFromUzi();
    }

//    public function testCheckValidCert(): void
//    {
//        $service = new UziService(true);
//
//        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
//        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-011-correct.cert');
//
//        $user = $service->getUserFromUzi();
//        $this->assertEquals("12345678@uzi.pas", $user->email);
//        $this->assertFalse($user->is_admin);
//    }
//
//    public function testCheckValidAdminCert(): void
//    {
//        $service = new UziService(true);
//
//
//        $_SERVER['SSL_CLIENT_VERIFY'] = "SUCCESS";
//        $_SERVER['SSL_CLIENT_CERT'] = file_get_contents(__DIR__ . '/certs/mock-012-correct-admin.cert');
//
//        $user = $service->getUserFromUzi();
//        $this->assertEquals("11111111@uzi.pas", $user->email);
//        $this->assertTrue($user->is_admin);
//    }
}
