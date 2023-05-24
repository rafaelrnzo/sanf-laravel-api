<?php

namespace Sanf\Integration\Modules\Scanina;

use GuzzleHttp\Exception\ServerException;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;

class ScaninaApiProcessor extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        $base64 = base64_encode(config('scanina-api.client_id') . ":" . config('scanina-api.client_secret'));
        $request->headers(['Authorization' => "Basic {$base64}"]);
        $request->headers(['X-Request-ID' => app('request')->header('X-Request-ID')]);
        try {
            $response = $next($request);
            $result = $response->json();
            if (is_null($result)) {
                throw new \Exception('Something went wrong at SCANINA API');
            }
        } catch (ServerException $exception) {
            //TODO HANDLE EXCEPTION
            throw $exception;
        }

        return $response;
    }
}
