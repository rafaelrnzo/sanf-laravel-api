<?php

namespace NbsPhp\Core\Middleware;

use Illuminate\Http\Request;

class StatusMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        if (auth()->check()) {
            $middlewareConditions = config('auth.middleware_conditions');
            $conditions = is_array($middlewareConditions)
                ? $middlewareConditions
                : explode('|', $middlewareConditions);

            if (in_array(auth()->user()->status_id, $conditions)) {
                return $this->logout();
            }
        }

        return $next($request);
    }

    protected function logout()
    {
        auth()->logout();
        return abort(401);
    }
}
