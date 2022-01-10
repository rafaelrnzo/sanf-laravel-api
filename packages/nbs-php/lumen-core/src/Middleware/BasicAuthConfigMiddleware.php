<?php

namespace NbsPhp\Core\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;
use NbsPhp\Core\Exceptions\InvalidCredentialException;

class BasicAuthConfigMiddleware
{
    /**
     * The authentication guard factory instance.
     *
     * @var Auth
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @return mixed
     * @throws InvalidCredentialException
     */
    public function handle($request, Closure $next, $provider = 'app-client')
    {
        if ($request->getUser() !== config("auth.providers.{$provider}.client_id")
            || $request->getPassword() !== config("auth.providers.{$provider}.client_secret")) {
            throw new InvalidCredentialException();
        }

        return $next($request);
    }
}
