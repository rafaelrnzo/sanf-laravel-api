<?php

namespace NbsPhp\Core\Controllers;

use NbsPhp\Core\Middleware\ResponseMiddleware;

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
}
