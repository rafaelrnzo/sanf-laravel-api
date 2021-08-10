<?php

namespace NbsPhp\ApiWrapper\Api\Exceptions;

class EndpointNotDefinedException extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Endpoint '$name' is not defined.");
    }
}
