<?php

namespace NbsPhp\Core\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use NbsPhp\Core\Models\AuditHttpLogModel;

class HttpLoggerMiddleware
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
        $response = $next($request);
        if (!(app()->runningInConsole() && app()->runningUnitTests()) && config('http-logger.enabled')) {
            //TODO DISPATCH JOB TO LOGGING
            if ($response instanceof JsonResponse) {
                $censoredKeys = config('http-logger.censor.bad-keys');
                //TODO REPOSITORY
                try{
                    $userId = Auth::id();
                } catch (\Exception $exception) {
                    $userId = null;
                }
                AuditHttpLogModel::create([
                    'user_id' => $userId,
                    'request_id' => $request->header('X-Request-ID'),
                    'method' => $request->method(),
                    'name' => optional(optional($request->route())[1])['as'],
                    'status_code' => $response->status(),
                    'path' => $request->getPathInfo(),
                    'header' => $this->censoringNestedArray($censoredKeys, $request->headers->all()),
                    'query' => $this->censoringNestedArray($censoredKeys, $request->query()),
                    'body' => $this->censoringNestedArray($censoredKeys, $request->post()),
                    'response' => $this->censoringNestedArray($censoredKeys, json_decode($response->content(), true)),
                    'ip_address' => $request->ip(),
                    'user_agent' => substr($request->userAgent(), 0, 1023),
                ]);
            }
        }

        return $response;
    }

    protected function censoringNestedArray($needles, $haystack)
    {
        $censor = config('http-logger.censor.replacement');
        $flattenArray = array_dot($haystack);
        foreach ($needles as $needle) {
            foreach ($flattenArray as $key => $value) {
                if (in_array($needle, explode('.', $key))) {
                    array_set($haystack, $key, $censor);
                }
            }
        }

        return $haystack;
    }
}
