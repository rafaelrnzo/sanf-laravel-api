<?php

namespace NbsPhp\Core\Middleware;

use Closure;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Http\Request;

class ThrottleRequestsMiddleware
{
    private Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Usage: middleware => 'throttle:MAX,MINUTES'
     * Example: 'throttle:1,1' => 1 request per 1 minute.
     */
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1)
    {
        /** @var Request $request */
        $maxAttempts = max(1, (int) $maxAttempts);
        $decayMinutes = max(1, (int) $decayMinutes);

        $key = $this->resolveKey($request);

        $hitsKey = "throttle:hits:{$key}";
        $timerKey = "throttle:timer:{$key}";

        $now = time();

        // Start/reset window if needed
        if (!$this->cache->has($timerKey)) {
            $this->cache->put($timerKey, $now, $decayMinutes * 60);
            $this->cache->put($hitsKey, 0, $decayMinutes * 60);
        }

        $hits = (int) $this->cache->get($hitsKey, 0);

        if ($hits >= $maxAttempts) {
            $startedAt = (int) $this->cache->get($timerKey, $now);
            $window = $decayMinutes * 60;
            $retryAfter = max(1, ($startedAt + $window) - $now);

            return response('Too Many Requests', 429)
                ->header('Retry-After', (string) $retryAfter);
        }

        // Increment hits and keep same TTL window
        $this->cache->put($hitsKey, $hits + 1, $decayMinutes * 60);

        return $next($request);
    }

    protected function resolveKey(Request $request): string
    {
        $ip = $request->ip() ?? 'unknown';
        $method = $request->method();
        $path = '/' . ltrim($request->path(), '/');

        // Per-IP + per-endpoint key (like typical throttles)
        return sha1($ip . '|' . $method . '|' . $path);
    }
}
