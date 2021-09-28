<?php


namespace Sanf\Integration;


use GuzzleHttp\Exception\ServerException;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;
use Sanf\Integration\Exceptions\SanfInternalApiException;

class InternalApiProcessor extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        $request->headers(['Client-id' => config('sanf-internal.client_id')]);
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
