<?php

namespace Sanf\Integration\Modules\SanfCore;

use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Exceptions\SanfInternalApiException;

class SanfCoreApiProcessorV2 extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        $request->auth([config('sanf-api-v2.client_id'), config('sanf-api-v2.client_secret')]);
        $request->headers(['X-Request-ID' => app('request')->header('X-Request-ID')]);
        try {
            $response = $next($request);
            $result = $response->json();
            if (is_null($result)) {
                Log::error($response->getContents());
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
            report($exception);
            throw $exception;
        }

        return $response;
    }
}
