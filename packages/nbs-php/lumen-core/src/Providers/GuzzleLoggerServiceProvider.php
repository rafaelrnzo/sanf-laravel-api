<?php

namespace NbsPhp\Core\Providers;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use NbsPhp\Core\Jobs\LogApiRequestToDatabaseJob;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class GuzzleLoggerServiceProvider extends ServiceProvider
{
    protected const DRIVER_DATABASE = 'database';
    protected const DRIVER_LOG = 'log';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\GuzzleHttp\Client::class, function (Application $app) {
            $isEnabled = config('guzzle-logger.logger');
            $logDriver = config('guzzle-logger.driver');
            $handlerStack = HandlerStack::create();
            $logger = $app->make(LoggerInterface::class);
            if ($isEnabled && $logDriver == self::DRIVER_LOG) {
                $handlerStack->push(Middleware::tap(null, function (RequestInterface $req, array $options, PromiseInterface $promise) use ($logger) {
                    $promise->then(
                        function (ResponseInterface $res) use ($req, $logger) {
                            $logger->info('GuzzleHttp', [
                                'request' => [
                                    'uri' => "{$req->getMethod()} {$req->getUri()}",
                                    'headers' => $req->getHeaders(),
                                    'body' => $req->getBody()->getContents(),
                                ],
                                'response' => [
                                    'status' => $res->getStatusCode(),
                                    'headers' => $res->getHeaders(),
                                    'body' => $res->getBody()->getContents(),
                                ]
                            ]);

                            // Move pointer to the beginning of the stream
                            $res->getBody()->rewind();
                        },
                        function (RequestException $e) use ($req, $logger) {
                            $logger->error('GuzzleHttp', [
                                'request' => [
                                    'uri' => "{$req->getMethod()} {$req->getUri()}",
                                    'headers' => $req->getHeaders(),
                                    'body' => $req->getBody()->getContents(),
                                ],
                                'error' => [
                                    'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : $e->getCode(),
                                    'message' => $e->getMessage(),
                                    'body' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
                                ]
                            ]);
                        }
                    );
                }));
            }
            if ($isEnabled && $logDriver == self::DRIVER_DATABASE) {

                //TODO SERVICE AND REPO
                try{
                    $userId = Auth::id();
                } catch (\Exception $exception) {
                    $userId = null;
                }
                $handlerStack->push(
                    Middleware::tap(null, function (RequestInterface $request, array $options, PromiseInterface $promise) use ($userId) {
                        $requestBody = $request->getBody();
                        $requestBody->rewind();  // need to rewind stream to be able read request content again
                        $requestContent = json_decode($requestBody, true);
                        $promise->then(function (ResponseInterface $response) use ($request, $requestContent, $userId) {
                            $responseContent = json_decode($response->getBody(), true);
                            return dispatch(new LogApiRequestToDatabaseJob(
                                $request,
                                $requestContent,
                                $response,
                                $responseContent,
                                $userId,
                            ));
                        });
                    })
                );
            }
            return new \GuzzleHttp\Client([
                'handler' => $handlerStack,
            ]);
        });
    }
}
