<?php

namespace NbsPhp\Core\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use NbsPhp\Core\Exceptions\UnauthorizedException;

class BasicClientAuthMiddleware
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param \Illuminate\Contracts\Auth\Factory $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     * @throws UnauthorizedException
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($request->getUser() != config('auth.providers.nbs-basic-config.client_id')
            || $request->getPassword() != config('auth.providers.nbs-basic-config.client_secret')) {
            throw new UnauthorizedException();
        }

        return $next($request);
    }
}
