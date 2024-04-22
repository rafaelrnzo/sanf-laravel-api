<?php

namespace Sanf\Integration\Modules\Nanonets;

use Exception;
use GuzzleHttp\Exception\ServerException;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;

class NanonetsProcessor extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        $base64 = base64_encode(config('nanonets-api.client_id') . ':' . config('nanonets-api.client_secret'));

        $request->headers(['Authorization' => "Basic {$base64}"]);
        $request->headers(['X-Request-ID' => app('request')->header('X-Request-ID')]);

        try {
            $response = $next($request);
            $result = $response->json();
            if (is_null($result)) {
                throw new Exception('Something went wrong at Nanonets API');
            }
        } catch (ServerException $exception) {
        } catch (Exception $exception) {
            //TODO HANDLE EXCEPTION
            dd($exception);
            throw $exception;
        }

        return $response;
    }
}
