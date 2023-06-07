<?php

namespace Sanf\Integration\Modules\Scanina;

use Exception;
use GuzzleHttp\Exception\ClientException;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;
use NbsPhp\Core\Enum\HttpStatusCode;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductInvalidRequestException;
use Sanf\Core\Modules\Scanina\Exceptions\ScaninaProductNotFoundException;

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
                throw new Exception('Something went wrong at SCANINA API');
            }
        } catch (ClientException $exception) {
            $code = $exception->getCode();
            $exceptions = [
                HttpStatusCode::HTTP_NOT_FOUND => new ScaninaProductNotFoundException(),
                HttpStatusCode::HTTP_BAD_REQUEST => new ScaninaProductInvalidRequestException(),
            ];

            if (!array_key_exists($code, $exceptions)) {
                throw $exception;
            }

            throw $exceptions[$code];
        }

        return $response;
    }
}
