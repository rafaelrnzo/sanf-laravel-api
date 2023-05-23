<?php

namespace Sanf\Integration\Modules\Scanina;

use GuzzleHttp\Exception\ServerException;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;
use Sanf\Integration\Exceptions\TekenAjaExternalApiDataNotFoundException;
use Sanf\Integration\Exceptions\TekenAjaExternalApiException;

class ScaninaApiProcessor extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        $request->headers(['Client-id' => config('scanina.client_id')]);
        $request->headers(['X-Request-ID' => app('request')->header('X-Request-ID')]);
        try {
            $response = $next($request);
            $result = $response->json();
            if (is_null($result)) {
                throw new \Exception('Something went wrong at SCANINA API');
            }
            if ($result['status'] === false) {
                if ($result['code'] === 'E_EmptyData') {
                    throw new TekenAjaExternalApiDataNotFoundException($result['message']);
                }
                throw new TekenAjaExternalApiException($result['message']);
            }
        } catch (ServerException $exception) {
            //TODO HANDLE EXCEPTION
            throw $exception;
        }

        return $response;
    }
}
