<?php

namespace NbsPhp\Core\Middleware;

use Closure;

class RequestIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $requestId = $request->headers->get('X-Request-ID');

        if (is_null($requestId)) {
            $requestId = nano_id();

            $request->headers->set('X-Request-ID', $requestId);
        }

        $_SERVER['HTTP_X_REQUEST_ID'] = $requestId;

        $response = $next($request);

        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
