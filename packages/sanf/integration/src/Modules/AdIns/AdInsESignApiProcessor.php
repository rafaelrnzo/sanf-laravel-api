<?php

namespace Sanf\Integration\Modules\AdIns;

use GuzzleHttp\Exception\ServerException;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;

class AdInsESignApiProcessor extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        $key = config('adins.e-sign-hub.key');
        $tenantCode = config('adins.e-sign-hub.tenant_code');
        $apiKey = "{$key}@{$tenantCode}";

        $request->headers(['X-API-KEY' => $apiKey]);
        $request->headers(['Accept' => 'application/json']);

        try {
            $response = $next($request);
        } catch (ServerException $exception) {
            throw $exception;
        }

        return $response;
    }
}
