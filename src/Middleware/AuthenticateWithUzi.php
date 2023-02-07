<?php

namespace MinVWS\PUZI\Laravel\Middleware;

use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Auth\AuthenticationException;
use Closure;
use MinVWS\PUZI\Exceptions\UziException;
use MinVWS\PUZI\Laravel\AuthenticatableUziUser;
use MinVWS\PUZI\UziReader;
use MinVWS\PUZI\UziValidator;

/**
 * Class AuthenticateWithUzi
 * SPDX-License-Identifier: EUPL-1.2
 * @package MinVWS\PUZI\Laravel\Middleware
 */
class AuthenticateWithUzi
{
    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var UziReader
     */
    protected $uziReader;
    /**
     * @var UziValidator
     */
    protected $uziValidator;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     * @param UziReader $reader
     * @param UziValidator $validator
     */
    public function __construct(Auth $auth, UziReader $reader, UziValidator $validator)
    {
        $this->auth = $auth;
        $this->uziReader = $reader;
        $this->uziValidator = $validator;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param null|string $guard
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (! $request->secure()) {
            abort(400, 'The UZI auth requires a HTTPS connection.');
        }

        try {
            if (!$this->uziValidator->isValid($request)) {
                throw new AuthenticationException('Unauthenticated.');
            }

            $uziUser = $this->uziReader->getDataFromRequest($request);
            if (! $uziUser) {
                throw new AuthenticationException('Unauthenticated.');
            }
            $this->auth->guard($guard)->login(AuthenticatableUziUser::fromUziUser($uziUser), false);

            return $next($request);
        } catch (UziException $e) {
            // Exception
        }

        throw new AuthenticationException('Unauthenticated.');
    }
}
