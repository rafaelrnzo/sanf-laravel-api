<?php

namespace NbsPhp\Core\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class AppException extends Exception
{
    protected $data;

    protected $message = '';

    protected $status = Response::HTTP_BAD_REQUEST;

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
