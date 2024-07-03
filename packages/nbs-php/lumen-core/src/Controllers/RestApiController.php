<?php

namespace NbsPhp\Core\Controllers;

use Illuminate\Support\Str;
use NbsPhp\Core\Middleware\ResponseMiddleware;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RestApiController extends AbstractController
{
    protected $middlewareOptions = [];

    public function __construct()
    {
        $this->middleware(ResponseMiddleware::class, $this->middlewareOptions);
    }

    public function responseOk($message = 'Success', $data = null)
    {
        return response()->json(['message' => $message, 'data' => $data]);
    }

    public function streamDownload($callback, $name = null, array $headers = [], $disposition = 'attachment')
    {

        $response = new StreamedResponse($callback, 200, $headers);

        if (!is_null($name)) {
            $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
                $disposition,
                $name,
                $this->fallbackName($name)
            ));
        }
        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    protected function fallbackName($name)
    {
        return str_replace('%', '', Str::ascii($name));
    }
}
