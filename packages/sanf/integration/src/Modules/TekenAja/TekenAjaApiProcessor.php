<?php

namespace Sanf\Integration\Modules\TekenAja;

use GuzzleHttp\Exception\ServerException;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;
use Sanf\Integration\Exceptions\TekenAjaExternalApiDataNotFoundException;
use Sanf\Integration\Exceptions\TekenAjaExternalApiException;

class TekenAjaApiProcessor extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        $request->headers(['Apikey' => config('tekenaja-api.client_id')]);
        $request->headers(['X-Request-ID' => app('request')->header('X-Request-ID')]);
        try {
            $response = $next($request);
            $result = $response->json();
            if (is_null($result)) {
                throw new \Exception('API TEKEN AJA ERROR');
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
