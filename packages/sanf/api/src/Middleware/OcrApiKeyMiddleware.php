<?php

namespace Sanf\Api\Middleware;

use Closure;
use Illuminate\Http\Request;

class OcrApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $configuredKey = (string) config('llm-ocr.api_key');
        $providedKey = (string) ($request->header('X-API-Key') ?: $request->bearerToken());

        if ($configuredKey === '' || $providedKey === '' || ! hash_equals($configuredKey, $providedKey)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        return $next($request);
    }
}
