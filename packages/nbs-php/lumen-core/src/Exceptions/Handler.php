<?php

namespace NbsPhp\Core\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use NbsPhp\Core\Response\ResponseMapperInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    protected $request;
    protected $mapper;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        ApiException::class,
    ];

    protected $ignoredInput = [
        'password',
        'token',
    ];

    protected $loggedHeaders = [
        'user-agent',
        'x-app-version-number',
        'x-app-version-string',
    ];

    public function __construct(ResponseMapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }

    public function report(Exception $e)
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        if (app()->bound('sentry') && $this->shouldReport($e)) {
            app('sentry')->captureException($e);
        }

        if (method_exists($e, 'report')) {
            return $e->report();
        }

        try {
            $logger = app(LoggerInterface::class);
        } catch (Exception $ex) {
            throw $e; // throw the original exception
        }

        $logger->error($e, array_merge($this->context(app('request')), ['exception' => $e]));
    }

    public function render($request, Exception $e)
    {
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        } elseif ($e instanceof AuthorizationException) {
            $e = new HttpException(403, $e->getMessage());
        } elseif ($e instanceof ValidationException && $e->getResponse()) {
            return $e->getResponse();
        }

        return $this->prepareJsonResponse($request, $e);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function prepareJsonResponse($request, Exception $e)
    {
        list($mappedException, $httpStatus) = $this->mapper->parseException($e);

        return new JsonResponse(
            $mappedException,
            $this->isHttpException($e) ? $e->getStatusCode() : $httpStatus,
            $this->isHttpException($e) ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    protected function headers(Request $request)
    {
        try {
            $headers = [];
            foreach ($this->loggedHeaders as $header) {
                $headers[$header] = $request->header($header);
            }

            return array_filter($headers);
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Get the default context variables for logging.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function context(Request $request)
    {
        try {
            return [
                'request_id' => $_SERVER['HTTP_X_REQUEST_ID'],
                'url' => $request->url(),
                'header' => $this->headers($request),
                'input' => $request->except($this->ignoredInput),
            ];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
