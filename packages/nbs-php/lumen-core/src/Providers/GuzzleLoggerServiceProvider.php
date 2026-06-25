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
                                    'headers' => $this->censor($req->getHeaders()),
                                    'body' => $this->censorBody($req->getBody()->getContents()),
                                ],
                                'response' => [
                                    'status' => $res->getStatusCode(),
                                    'headers' => $this->censor($res->getHeaders()),
                                    'body' => $this->censorBody($res->getBody()->getContents()),
                                ],
                            ]);

                            // Move pointer to the beginning of the stream
                            $res->getBody()->rewind();
                        },
                        function (RequestException $e) use ($req, $logger) {
                            $logger->error('GuzzleHttp', [
                                'request' => [
                                    'uri' => "{$req->getMethod()} {$req->getUri()}",
                                    'headers' => $this->censor($req->getHeaders()),
                                    'body' => $this->censorBody($req->getBody()->getContents()),
                                ],
                                'error' => [
                                    'status' => $e->hasResponse() ? $e->getResponse()->getStatusCode() : $e->getCode(),
                                    'message' => $e->getMessage(),
                                    'body' => $e->hasResponse() ? $this->censorBody($e->getResponse()->getBody()->getContents()) : null,
                                ],
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

    /**
     * Mask sensitive keys (e.g. authorization, password) before they reach the log,
     * using the same config as the database driver.
     *
     * @param mixed $data
     * @return mixed
     */
    protected function censor($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $needles = config('guzzle-logger.censor.bad-keys', []);
        $replacement = config('guzzle-logger.censor.replacement', '**censor**');
        $flattenArray = array_dot($data);

        foreach ($needles as $needle) {
            foreach ($flattenArray as $key => $value) {
                if (in_array($needle, explode('.', strtolower($key)), true)) {
                    array_set($data, $key, $replacement);
                }
            }
        }

        return $data;
    }

    /**
     * Censor a (possibly JSON) body string. Non-JSON bodies are returned untouched.
     *
     * @param string|null $body
     * @return mixed
     */
    protected function censorBody($body)
    {
        $decoded = json_decode((string) $body, true);

        if (!is_array($decoded)) {
            return $body;
        }

        return $this->censor($decoded);
    }
}
