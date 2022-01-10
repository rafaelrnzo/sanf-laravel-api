<?php

namespace NbsPhp\Core\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use NbsPhp\Core\Exceptions\ForbiddenException;

class AuthorizationMiddleware extends Authorize
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param string|array $permission
     * @param mixed ...$models
     *
     * @return RedirectResponse|Response|mixed|void
     */
    public function handle($request, Closure $next, $permission, ...$models)
    {
        if (is_string($permission) && substr($permission, -8) === 'resource') {
            $permission = $request->route()->getName();
            $permission = preg_replace('/\bupdate\b/u', 'edit', $permission);
            $permission = preg_replace('/\bstore\b/u', 'create', $permission);
        }

        $permissions = is_array($permission) ? $permission : explode('|', $permission);

        if (!$this->gate->any($permissions) && !in_array('all.manage', $permissions)) {
            return $this->handleUnauthorizedRequest($request);
        }

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response|void
     */
    private function handleUnauthorizedRequest(Request $request)
    {
        throw new ForbiddenException();
    }
}
