<?php

namespace MinVWS\PUZI\Laravel\Tests\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use MinVWS\PUZI\Exceptions\UziException;
use MinVWS\PUZI\Laravel\Middleware\AuthenticateWithUzi;
use MinVWS\PUZI\UziReader;
use MinVWS\PUZI\UziUser;
use MinVWS\PUZI\UziValidator;
use Mockery;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AuthenticateWithUziTest
 * SPDX-License-Identifier: EUPL-1.2
 * @package Tests\Unit\Middleware
 */
class AuthenticateWithUziTest extends TestCase
{

    /**
     * @var Factory|Mockery\MockInterface|Mockery\LegacyMockInterface
     */
    protected $mockFactory;
    /**
     * @var UziValidator|Mockery\MockInterface|Mockery\LegacyMockInterface
     */
    protected $mockValidator;
    /**
     * @var UziReader|Mockery\MockInterface|Mockery\LegacyMockInterface
     */
    protected $mockReader;

    /**
     * @throws AuthenticationException
     */
    public function testOnHttp(): void
    {
        $request = new Request();
        $request->server->set('HTTPS', 'off');

        $middleware = $this->getMiddleware();

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("requires a HTTPS connection");

        $middleware->handle($request, function () {
        });
    }

    public function testNotValidated(): void
    {
        $request = new Request();
        $request->server->set('HTTPS', 'on');

        $middleware = $this->getMiddleware();

        $user = new UziUser();
        $this->mockReader->shouldReceive('getDataFromRequest')->andReturn($user);
        $this->mockValidator->shouldReceive('isValid')->andReturns(false);

        $this->expectException(AuthenticationException::class);

        $middleware->handle($request, function () {
        });
    }

    public function testExceptionDuringValidation(): void
    {
        $request = new Request();
        $request->server->set('HTTPS', 'on');

        $middleware = $this->getMiddleware();

        $this->mockReader->shouldReceive('getDataFromRequest')->andThrow(new UziException());
        $this->expectException(AuthenticationException::class);

        $middleware->handle($request, function () {
        });
    }

    public function testValidated(): void
    {
        $request = new Request();
        $request->server->set('HTTPS', 'on');

        $middleware = $this->getMiddleware();

        $user = new UziUser();
        $user->setUziNumber("12345");
        $this->mockReader->shouldReceive('getDataFromRequest')->andReturns($user);
        $this->mockValidator->shouldReceive('isValid')->andReturns(true);

        $mockGuard = Mockery::mock(Guard::class);
        $mockGuard->shouldReceive('login');
        $this->mockFactory->shouldReceive('guard')->andReturns($mockGuard);

        $result = $middleware->handle($request, function () {
        });
        $this->assertNull($result);
    }


    /**
     * @return AuthenticateWithUzi
     */
    protected function getMiddleware(): AuthenticateWithUzi
    {
        $this->mockFactory = Mockery::mock(Factory::class);
        $this->mockReader = Mockery::mock(UziReader::class);
        $this->mockValidator = Mockery::mock(UziValidator::class);

        return new AuthenticateWithUzi(
            $this->mockFactory,
            $this->mockReader,
            $this->mockValidator
        );
    }
}
