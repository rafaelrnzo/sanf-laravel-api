<?php

namespace Sanf\Integration\Modules\SanfCore;

use GuzzleHttp\Exception\ServerException;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Exceptions\SanfInternalApiException;

class SanfCoreApiProcessor extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {

        $request->headers(['Client-id' => config('sanf-api.client_id')]);
        $request->headers(['X-Request-ID' => app('request')->header('X-Request-ID')]);
        try {
            $response = $next($request);
            $result = $response->json();
            if (is_null($result)) {
                throw new \Exception('API CORE ERROR');
            }
            if ($result['status'] === false) {
                if ($result['code'] === 'E_EmptyData') {
                    throw new SanfInternalApiDataNotFoundException($result['message']);
                }
                throw new SanfInternalApiException($result['message']);
            }
        } catch (ServerException $exception) {
            //TODO HANDLE EXCEPTION
            throw $exception;
        }

        return $response;
    }
}
