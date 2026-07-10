<?php

namespace Sanf\Api\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyCoreWebhookSignature
{
    public function handle(Request $request, Closure $next)
    {
        $secret = config('core-webhook.secret');

        if (empty($secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook secret is not configured',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!$this->hasValidSignature($request, $secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!$this->hasValidTimestamp($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Stale or invalid timestamp',
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }

    private function hasValidSignature(Request $request, string $secret): bool
    {
        $provided = (string) $request->header(config('core-webhook.signature_header', 'X-Signature'));

        if ($provided === '') {
            return false;
        }

        if (str_starts_with($provided, 'sha256=')) {
            $provided = substr($provided, 7);
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $provided);
    }

    private function hasValidTimestamp(Request $request): bool
    {
        $tolerance = (int) config('core-webhook.signature_tolerance', 0);

        if ($tolerance <= 0) {
            return true;
        }

        $timestamp = $request->header(config('core-webhook.timestamp_header', 'X-Timestamp'));

        if ($timestamp === null || !is_numeric($timestamp)) {
            return false;
        }

        return abs(time() - (int) $timestamp) <= $tolerance;
    }
}
