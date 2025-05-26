<?php

namespace Sanf\Integration\Modules\Fineksi;

use Exception;
use GuzzleHttp\Exception\ServerException;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;

class FineksiProcessor extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        
        $request->headers(['x-client-id' => config('fineksi-api.client_id')]);
        $request->headers(['x-client-secret' => config('fineksi-api.client_secret')]); 
        $request->headers(['Content-Type' => 'application/json']);

        try {
            $response = $next($request);
            $result = $response->json();
            if (is_null($result)) {
                throw new Exception('Something went wrong at Nanonets API');
            }
        } catch (ServerException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            throw $exception;
        }

        return $response;
    }
}
