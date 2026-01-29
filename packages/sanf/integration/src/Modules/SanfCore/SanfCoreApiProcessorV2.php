<?php

namespace Sanf\Integration\Modules\SanfCore;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;
use NbsPhp\ApiWrapper\Api\Processor;
use NbsPhp\ApiWrapper\Api\Request;
use NbsPhp\ApiWrapper\Api\Response;
use Sanf\Integration\Exceptions\SanfInternalApiDataNotFoundException;

class SanfCoreApiProcessorV2 extends Processor
{
    public static function handle(Request $request, callable $next): Response
    {
        $headers = [
            'X-Request-ID' => app('request')->header('X-Request-ID'),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . self::basicAuthKey(),
        ];

        foreach ($headers as $key => $value) {
            if ($header = data_get($request->getOptions(), "headers.{$key}")) {
                $headers[$key] = $header;
            }
        }

        $request->headers($headers);

        $verifyOnProduction = config('app.env') === 'production';
        $request->options([
            'verify' => $verifyOnProduction,
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
            $rawBody = (string) $response->getBody();

            $message = $bodyResponse['message']
                ?? $bodyResponse['error']['message']
                ?? trim(strip_tags($rawBody))  // fall back to text extracted from HTML
                ?? 'Sanf internal API error';

            if ($response->getStatusCode() === \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND) {
                throw new SanfInternalApiDataNotFoundException($message);
            }

            report($exception);
            throw $exception;
        } catch (ServerException $exception) {
            //TODO HANDLE EXCEPTION
            report($exception);
            throw $exception;
        }

        return $response;
    }

    private static function basicAuthKey(): string
    {
        $clientId = config('sanf-api-v2.client_id');
        $clientSecret = config('sanf-api-v2.client_secret');
        $userId = app('request')->attributes->get('internal_sanf_user_id') ?? app('request')->input('internal_sanf_user_id');

        $plainAuth = implode(';', [$clientId, $clientSecret, $userId]);

        return base64_encode($plainAuth);
    }
}
