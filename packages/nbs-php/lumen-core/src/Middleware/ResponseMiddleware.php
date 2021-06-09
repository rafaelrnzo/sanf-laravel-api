<?php

namespace NbsPhp\Core\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use NbsPhp\Core\Response\ResponseMapperInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResponseMiddleware
{
    protected $responseMapper;

    public function __construct(ResponseMapperInterface $responseMapper)
    {
        $this->responseMapper = $responseMapper;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof StreamedResponse) {
            return $response;
        }
        if ($response->original instanceof View) {
            return $response;
        }
        if (isset($response->exception) || ($response instanceof JsonResponse && $response->getStatusCode() == Response::HTTP_UNPROCESSABLE_ENTITY)) {
            return $this->responseMapper->errorResponse($response);
        }

        return $this->responseMapper->successResponse($response);
    }
}
