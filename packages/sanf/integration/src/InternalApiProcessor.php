<?php


namespace Sanf\Integration;


use GuzzleHttp\Exception\ClientException;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;

class InternalApiProcessor extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        $request->headers(['Client-id' => config('sanf-internal.client_id')]);
        try {
            $response = $next($request);
            $result = $response->json();
            if ($result['status'] === false) {
                throw new SanfInternalApiException($result['message']);
            }

        } catch (ClientException $exception) {
            //TODO HANDLE EXCEPTION
            throw $exception;
        }

        return $response;
    }
}
