<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasicAuthRelayMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->headers->has('Authorization')) {
            $authHeader = $request->header('Authorization');
            if (str_starts_with($authHeader, 'Basic ')) {
                $basicToken = substr($authHeader, 6);
                $decoded = base64_decode($basicToken);
                $parts = explode(';', $decoded);
                if (count($parts) === 3) {
                    [$clientId, $clientSecret, $userId] = $parts;

                    $h2hClientId = config('auth.providers.core-h2h-user-provider.client_id');
                    $h2hClientSecret = config('auth.providers.core-h2h-user-provider.client_secret');

                    $v2ClientId = config('sanf-api-v2.client_id');
                    $v2ClientSecret = config('sanf-api-v2.client_secret');

                    $isValid = ($clientId === $h2hClientId && $clientSecret === $h2hClientSecret)
                        || ($clientId === $v2ClientId && $clientSecret === $v2ClientSecret);

                    if ($isValid) {
                        // Remove the Authorization header immediately so JWTGuard does not try to parse Basic Auth during resolution
                        $request->headers->remove('Authorization');

                        try {
                            $profile = app(\Sanf\Core\Modules\User\Repositories\ProfileRepositoryInterface::class)->findById($userId);
                            if ($profile) {
                                $email = $profile->getEmail();
                                $provider = app('auth')->createUserProvider('mobile-user-provider');
                                $user = $provider->retrieveByCredentials(['username' => $email]);
                                if ($user) {
                                    app('auth')->guard('api')->login($user);
                                    $request->attributes->set('internal_sanf_user_id', $userId);
                                    $request->merge([
                                        'internal_sanf_user_id' => $userId,
                                        'cust_id' => $userId,
                                    ]);
                                }
                            }
                        } catch (\Throwable $e) {
                            // Fail silently, subsequent guards will handle auth check failures
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
