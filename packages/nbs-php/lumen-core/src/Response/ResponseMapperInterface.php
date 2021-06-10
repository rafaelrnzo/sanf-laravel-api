<?php


namespace NbsPhp\Core\Response;


use Symfony\Component\HttpFoundation\Response;

interface ResponseMapperInterface
{
    public function successResponse(Response $response);

    public function errorResponse(Response $response);

    public function parseException(\Exception $exception);
}
