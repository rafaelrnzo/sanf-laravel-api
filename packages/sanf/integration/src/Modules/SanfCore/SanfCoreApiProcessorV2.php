<?php

namespace Sanf\Integration\Modules\SanfCore;

use GuzzleHttp\Exception\ClientException;
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
        $clientId = config('sanf-api-v2.client_id');
        $clientSecret = config('sanf-api-v2.client_secret');
        $userId = app('request')->attributes->get('internal_sanf_user_id') ?? app('request')->input('internal_sanf_user_id');

        $plainAuth = implode(';', [$clientId, $clientSecret, $userId]);
        $encodedAuth = base64_encode($plainAuth);

        $request->headers([
            'X-Request-ID' => app('request')->header('X-Request-ID'),
            'Authorization' => 'Basic ' . $encodedAuth,
        ]);

        try {
            $response = $next($request);
            $result = $response->json();

            if (is_null($result)) {
                Log::error($response->getContents());
                throw new \Exception('API CORE ERROR');
            }
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            $bodyResponse = json_decode((string) $response->getBody(), true);

            if ($response->getStatusCode() === \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND) {
                throw new SanfInternalApiDataNotFoundException($bodyResponse['message']);
            }

            throw new SanfInternalApiException($bodyResponse['message']);
        } catch (ServerException $exception) {
            //TODO HANDLE EXCEPTION
            report($exception);
            throw $exception;
        }

        return $response;
    }
}
