<?php

namespace Sanf\Api\Middleware;

use Closure;
use Illuminate\Http\Request;

class InjectUserIdFromPathMiddleware
{
    /**
     * Path parameters that should be treated as a user identifier.
     *
     * @var array<int, string>
     */
    protected array $pathParameters = [
        'xid',
    ];

    public function handle(Request $request, Closure $next)
    {
        foreach ($this->pathParameters as $parameter) {
            $value = $request->route($parameter);
            if (!empty($value)) {
                $this->attachUserId($request, $value);
                break;
            }
        }

        return $next($request);
    }

    protected function attachUserId(Request $request, $userId): void
    {
        $request->merge(['internal_sanf_user_id' => $userId]);
        $request->attributes->set('internal_sanf_user_id', $userId);
    }
}