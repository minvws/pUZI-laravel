<?php

namespace Tests\Unit\Middleware;

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

class AuthenticateWithUziTest extends TestCase {

    /**
     * @var Factory|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected $mockFactory;
    /**
     * @var UziValidator|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected $mockValidator;
    /**
     * @var UziReader|Mockery\LegacyMockInterface|Mockery\MockInterface
     */
    protected $mockReader;

    function testOnHttp() {

        $request = new Request();
        $request->server->set('HTTPS', 'off');

        $middleware = $this->getMiddleware();

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage("requires a HTTPS connection");

        $middleware->handle($request, function () {});
    }

    function testNotValidated() {

        $request = new Request();
        $request->server->set('HTTPS', 'on');

        $middleware = $this->getMiddleware();

        $user = new UziUser();
        $this->mockReader->shouldReceive('getDataFromRequest')->andReturns($user);
        $this->mockValidator->shouldReceive('isValid')->andReturns(false);

        $this->expectException(AuthenticationException::class);

        $middleware->handle($request, function () {});
    }

    function testExceptionDuringValidation() {

        $request = new Request();
        $request->server->set('HTTPS', 'on');

        $middleware = $this->getMiddleware();

        $this->mockReader->shouldReceive('getDataFromRequest')->andThrow(new UziException());
        $this->expectException(AuthenticationException::class);

        $middleware->handle($request, function () {});
    }

    function testValidated() {

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

        $result = $middleware->handle($request, function () {});
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
